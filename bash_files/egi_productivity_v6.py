#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
EGI Commit Statistics Exporter v6.1.0 - DUAL REPO EDITION
Author: Fabio Cherici & Padmin D. Curtis (AI Partner OS3.0)
License: MIT
Version: 6.1.0 (FlorenceEGI - Dual Repository Analytics)
Date: 2025-11-02

Overview
--------
Analizza commit da 2 repository separati (EGI + NATAN_LOC) e fornisce stats unificate.

DUAL REPOSITORY SUPPORT:
- EGI repo: /home/fabio/EGI
- NATAN_LOC repo: /home/fabio/NATAN_LOC (auto-detect)
- Query Git separate per ogni repository
- Somma automatica per totali combined
- Excel con colonne separate per EGI e NATAN_LOC

TERMINAL OUTPUT:
1. GIORNALIERO (sempre):
   - TOT GIORNALIERO EGI (commits, righe nette)
   - TOT GIORNALIERO NATAN_LOC (commits, righe nette)
   - SOMMA GIORNALIERA (commits totali, righe totali)

2. SETTIMANALE (solo se range > 1 giorno):
   - TOTALE SETTIMANALE EGI
   - TOTALE SETTIMANALE NATAN_LOC
   - SOMMA SETTIMANALE (EGI + NATAN_LOC)

EXCEL OUTPUT (solo se range > 1 giorno):
- UNA RIGA per periodo (weekly/daily)
- Colonne separate: Commits EGI | Righe EGI | Commits NATAN | Righe NATAN | Totali
- 3 sheets: Summary, Weekly, Daily

MODES:
- Single day (--since X --until X): Solo echo giornaliero, NO Excel, NO settimanale
- Range/Default: Echo giornaliero + settimanale + Excel completo

Features from v3.py + v5.py:
- Multi-dimensional productivity scoring
- Day type classification (REFACTORING, BUG_FIXING, FEATURE_DEV, etc.)
- Cognitive load estimation
- Tag-weighted commits (REFACTOR=2x, FIX=1.5x, FEAT=1.0x)
- Comprehensive exclude patterns (deps + testing data)
- Modern Python 3.10+ with dataclasses
- Clean timezone parsing

Usage
-----
# Single day analysis (solo echo, no Excel)
python egi_productivity_v6.py --since 2025-11-01 --until 2025-11-01

# Full report (default: 2025-08-19 to today, genera Excel)
python egi_productivity_v6.py

# Custom range
python egi_productivity_v6.py --since 2025-10-01 --until 2025-10-28
"""

from __future__ import annotations

import argparse
import datetime as dt
import math
import os
import re
import subprocess
import sys
from collections import Counter, defaultdict
from dataclasses import dataclass
from pathlib import Path
from typing import Dict, List, Tuple

# ---- Optional deps for XLSX ----
HAVE_PANDAS = False
try:
    import pandas as pd
    import openpyxl
    from openpyxl.styles import Font, PatternFill, Alignment
    HAVE_PANDAS = True
except ImportError:
    HAVE_PANDAS = False


# ═══════════════════════════════════════════════════════════════
# CONFIGURATION
# ═══════════════════════════════════════════════════════════════

# TAG weights from v3 (most comprehensive)
TAG_WEIGHTS = {
    'FEAT': 1.0,      # Standard - nuove features
    'FIX': 1.5,       # Fix vale di più (previene emergencies)
    'REFACTOR': 2.0,  # Refactoring è preziosissimo (debt repayment)
    'DOC': 0.8,       # Importante ma meno impattante
    'TEST': 1.2,      # Testing ha valore alto (quality assurance)
    'CHORE': 0.6,     # Maintenance routine
    'DEBUG': 1.3,     # Debugging è complesso
    'UNTAGGED': 0.5   # Penalità per commit non categorizzati
}

# Day type classification (from v3)
DAY_TYPES = {
    'REFACTORING': {
        'description': 'Debt Repayment Day',
        'criteria': lambda stats: stats.get('REFACTOR', 0) > 20 or stats.get('FIX', 0) > 50,
        'multiplier': 1.5,
        'icon': '🔧'
    },
    'BUG_FIXING': {
        'description': 'Bug Extermination Day',
        'criteria': lambda stats: stats.get('FIX', 0) > 40 and stats.get('FIX', 0) <= 50,
        'multiplier': 1.3,
        'icon': '🐛'
    },
    'FEATURE_DEV': {
        'description': 'Feature Building Day',
        'criteria': lambda stats: stats.get('FEAT', 0) > 50,
        'multiplier': 1.0,
        'icon': '✨'
    },
    'TESTING': {
        'description': 'Quality Assurance Day',
        'criteria': lambda stats: stats.get('TEST', 0) > 40,
        'multiplier': 1.1,
        'icon': '🧪'
    },
    'MAINTENANCE': {
        'description': 'Maintenance Day',
        'criteria': lambda stats: stats.get('CHORE', 0) > 40,
        'multiplier': 0.8,
        'icon': '🔨'
    },
    'MIXED': {
        'description': 'Mixed Activities',
        'criteria': lambda stats: True,  # Default fallback
        'multiplier': 1.0,
        'icon': '📦'
    }
}

# Comprehensive exclude patterns (from v3 + additions)
EXCLUDED_PATTERNS = [
    # Dependency directories
    'node_modules/',
    'vendor/',
    'bower_components/',

    # History and IDE
    '.history/',
    '.vscode/',
    '.idea/',

    # Build outputs
    'build/',
    'dist/',
    'out/',
    'coverage/',
    '.next/',

    # Lock files (huge and auto-generated)
    'package-lock.json',
    'composer.lock',
    'yarn.lock',
    'pnpm-lock.yaml',

    # Minified files
    '.min.js',
    '.min.css',
    '.bundle.js',

    # Other auto-generated
    '.map',
    '.cache/',
    'tmp/',
    'temp/',

    # Testing data files (JSON datasets, debug HTML)
    'storage/testing/',
    'deliberazioni_',
    'albo_debug_',
    'albo_structure_debug',
    'albo_real_page',

    # Data directories
    'storage/data/',
    'storage/logs/',
    'storage/dumps/',

    # Git
    '.git/',
]

TAG_REGEX = re.compile(r'\[(FEAT|FIX|DOC|CHORE|REFACTOR|TEST|DEBUG)\]')


# ═══════════════════════════════════════════════════════════════
# DATACLASSES
# ═══════════════════════════════════════════════════════════════

@dataclass
class CommitEntry:
    sha: str
    author: str
    date: dt.datetime
    message: str
    files: List[Tuple[str, int, int]]  # (path, added, removed)


@dataclass
class DayStats:
    date: dt.date
    # EGI stats (repo root - excluding docs/NATAN_LOC)
    commits_egi: int
    lines_net_egi: int
    # NATAN_LOC stats (docs/NATAN_LOC only)
    commits_natan: int
    lines_net_natan: int
    # Combined stats
    commits: int
    commits_weighted: float
    files_modified: int
    lines_added: int
    lines_removed: int
    lines_touched: int
    lines_net: int
    tags: Dict[str, int]
    day_type: str
    day_type_icon: str
    cognitive_load: float
    productivity_index: float


@dataclass
class WeekStats:
    week_number: int
    period: str
    description: str
    start_date: dt.date
    end_date: dt.date
    # EGI stats (repo root - excluding docs/NATAN_LOC)
    commits_egi: int
    lines_net_egi: int
    # NATAN_LOC stats (docs/NATAN_LOC only)
    commits_natan: int
    lines_net_natan: int
    # Combined stats
    commits: int
    commits_weighted: float
    files_modified: int
    lines_touched: int
    lines_net: int
    tag_coverage_pct: float
    avg_cognitive_load: float
    avg_productivity: float
    testing_minutes: float
    coding_minutes: float


# ═══════════════════════════════════════════════════════════════
# HELPER FUNCTIONS
# ═══════════════════════════════════════════════════════════════

def should_exclude_file(filepath: str) -> bool:
    """Check if file should be excluded from line counting."""
    if not filepath:
        return True

    for pattern in EXCLUDED_PATTERNS:
        if pattern in filepath:
            return True

    return False


def parse_git_date(date_str: str) -> dt.datetime:
    """
    Parse git date with timezone handling.
    Fix from v5: strip timezone before fromisoformat.

    Input: '2025-10-28 02:23:04 +0100'
    Output: datetime(2025, 10, 28, 2, 23, 4)
    """
    # Strip timezone suffix (e.g., ' +0100', ' -0500')
    date_clean = re.sub(r'\s[+-]\d{4}$', '', date_str.strip())
    return dt.datetime.fromisoformat(date_clean)


def classify_day_type(tag_percentages: Dict[str, float]) -> Tuple[str, str]:
    """
    Classify day based on tag distribution.
    Returns: (type_name, icon)
    """
    for day_type, config in DAY_TYPES.items():
        if config['criteria'](tag_percentages):
            return day_type, config['icon']

    return 'MIXED', '📦'


def calculate_cognitive_load(commits: int, files: int, lines_touched: int) -> float:
    """
    Calculate cognitive load using log-scaled formula.

    Components:
    - LI (Lines Impact): log(lines_touched + 1)
    - FM (File Modifications): log(files + 1)
    - DP (Daily Pressure): log(commits + 1)

    Formula: CL = (LI + FM + DP) / 3, clamped to [1.0, 3.5]
    """
    if commits == 0:
        return 1.0

    li = math.log(lines_touched + 1)
    fm = math.log(files + 1)
    dp = math.log(commits + 1)

    cl = (li + fm + dp) / 3.0

    # Normalize and clamp
    cl_normalized = 1.0 + (cl / 2.0)
    return max(1.0, min(3.5, cl_normalized))


def calculate_productivity_index(
    commits_weighted: float,
    lines_net: int,
    cognitive_load: float,
    day_type_multiplier: float
) -> float:
    """
    Calculate productivity index.

    Formula: PI = (weighted_commits * 10 + net_lines / 10) * day_type_multiplier / cognitive_load
    """
    if cognitive_load == 0:
        cognitive_load = 1.0

    base_score = (commits_weighted * 10.0) + (lines_net / 10.0)
    return (base_score * day_type_multiplier) / cognitive_load


# ═══════════════════════════════════════════════════════════════
# GIT OPERATIONS
# ═══════════════════════════════════════════════════════════════

class GitRepo:
    """Git repository operations."""

    def __init__(self, repo_path: Path):
        self.repo_path = repo_path
        self.natan_repo_path = repo_path.parent / 'NATAN_LOC'

    def run_command(self, cmd: str) -> str:
        """Execute git command and return output."""
        try:
            result = subprocess.run(
                cmd,
                shell=True,
                cwd=self.repo_path,
                capture_output=True,
                text=True,
                check=True
            )
            return result.stdout.strip()
        except subprocess.CalledProcessError as e:
            print(f"❌ Git command failed: {e}", file=sys.stderr)
            return ""

    def get_commits(self, since: str, until: str) -> List[CommitEntry]:
        """Get all commits in date range with file stats."""
        commits = []

        # Get commit list
        cmd = f'git log --all --oneline --since="{since} 00:00:00" --until="{until} 23:59:59"'
        output = self.run_command(cmd)

        if not output:
            return commits

        for line in output.strip().split('\n'):
            if not line:
                continue

            sha = line.split()[0]

            # Get commit details
            cmd_show = f'git show --no-patch --format="%an|%ai|%s" {sha}'
            details = self.run_command(cmd_show)

            if not details:
                continue

            parts = details.split('|', 2)
            if len(parts) < 3:
                continue

            author, date_str, message = parts

            try:
                commit_date = parse_git_date(date_str)
            except Exception as e:
                print(f"⚠️ Failed to parse date '{date_str}': {e}", file=sys.stderr)
                continue

            # Get file stats (with exclusions)
            cmd_numstat = f'git show --numstat --format="" {sha}'
            numstat_output = self.run_command(cmd_numstat)

            files = []
            for stat_line in numstat_output.strip().split('\n'):
                if not stat_line:
                    continue

                parts = stat_line.split('\t')
                if len(parts) < 3:
                    continue

                added_str, removed_str, filepath = parts

                # Skip excluded files
                if should_exclude_file(filepath):
                    continue

                try:
                    added = int(added_str) if added_str != '-' else 0
                    removed = int(removed_str) if removed_str != '-' else 0
                    files.append((filepath, added, removed))
                except ValueError:
                    continue

            commits.append(CommitEntry(
                sha=sha,
                author=author,
                date=commit_date,
                message=message,
                files=files
            ))

        return commits

    def get_commits_for_day(self, date: dt.date) -> List[CommitEntry]:
        """Get commits for a specific day."""
        date_str = date.isoformat()
        next_day = (date + dt.timedelta(days=1)).isoformat()

        return self.get_commits(date_str, date_str)

    def get_commits_from_repo(self, repo_path: Path, since: str, until: str) -> List[CommitEntry]:
        """Get commits from specific repository."""
        commits = []
        cmd = f'git log --all --oneline --since="{since} 00:00:00" --until="{until} 23:59:59"'

        try:
            result = subprocess.run(
                cmd,
                shell=True,
                cwd=repo_path,
                capture_output=True,
                text=True,
                check=True
            )
            output = result.stdout.strip()
        except subprocess.CalledProcessError:
            return commits

        if not output:
            return commits

        for line in output.strip().split('\n'):
            if not line:
                continue
            sha = line.split()[0]

            # Get commit details
            cmd_show = f'git show --no-patch --format="%an|%ai|%s" {sha}'
            try:
                result = subprocess.run(cmd_show, shell=True, cwd=repo_path, capture_output=True, text=True, check=True)
                details = result.stdout.strip()
            except:
                continue

            parts = details.split('|', 2)
            if len(parts) < 3:
                continue

            author, date_str, message = parts

            try:
                commit_date = parse_git_date(date_str)
            except:
                continue

            # Get file stats
            cmd_numstat = f'git show --numstat --format="" {sha}'
            try:
                result = subprocess.run(cmd_numstat, shell=True, cwd=repo_path, capture_output=True, text=True, check=True)
                numstat_output = result.stdout.strip()
            except:
                numstat_output = ""

            files = []
            for stat_line in numstat_output.strip().split('\n'):
                if not stat_line:
                    continue
                parts = stat_line.split('\t')
                if len(parts) < 3:
                    continue
                added_str, removed_str, filepath = parts

                # Skip excluded files
                if should_exclude_file(filepath):
                    continue

                try:
                    added = int(added_str) if added_str != '-' else 0
                    removed = int(removed_str) if removed_str != '-' else 0
                    files.append((filepath, added, removed))
                except ValueError:
                    continue

            commits.append(CommitEntry(
                sha=sha,
                author=author,
                date=commit_date,
                message=message,
                files=files
            ))

        return commits


# ═══════════════════════════════════════════════════════════════
# STATISTICS ENGINE
# ═══════════════════════════════════════════════════════════════

class ProductivityAnalyzer:
    """Main analytics engine."""

    def __init__(self, repo_path: Path):
        self.repo = GitRepo(repo_path)

    def analyze_day(self, date: dt.date) -> DayStats:
        """Analyze single day statistics with EGI/NATAN_LOC separation."""
        date_str = date.isoformat()

        # Query EGI repo (/home/fabio/EGI)
        commits_egi = self.repo.get_commits_from_repo(self.repo.repo_path, date_str, date_str)

        # Query NATAN_LOC repo (/home/fabio/NATAN_LOC) se esiste
        commits_natan = []
        if self.repo.natan_repo_path.exists():
            commits_natan = self.repo.get_commits_from_repo(self.repo.natan_repo_path, date_str, date_str)

        # Calculate EGI stats
        lines_net_egi = 0
        for commit in commits_egi:
            for filepath, added, removed in commit.files:
                lines_net_egi += (added - removed)

        # Calculate NATAN stats
        lines_net_natan = 0
        for commit in commits_natan:
            for filepath, added, removed in commit.files:
                lines_net_natan += (added - removed)

        # Combine commits
        commits = commits_egi + commits_natan

        if not commits:
            return DayStats(
                date=date,
                commits_egi=0,
                lines_net_egi=0,
                commits_natan=0,
                lines_net_natan=0,
                commits=0,
                commits_weighted=0.0,
                files_modified=0,
                lines_added=0,
                lines_removed=0,
                lines_touched=0,
                lines_net=0,
                tags={},
                day_type='MIXED',
                day_type_icon='📦',
                cognitive_load=1.0,
                productivity_index=0.0
            )

        # Count tags
        tag_counter = Counter()
        for commit in commits:
            match = TAG_REGEX.search(commit.message)
            if match:
                tag_counter[match.group(1)] += 1
            else:
                tag_counter['UNTAGGED'] += 1

        # Calculate tag percentages
        total_commits = len(commits)
        tag_percentages = {tag: (count / total_commits) * 100 for tag, count in tag_counter.items()}

        # Calculate weighted commits
        commits_weighted = sum(
            TAG_WEIGHTS.get(tag, 0.5) * count
            for tag, count in tag_counter.items()
        )

        # File statistics (COMBINED)
        all_files = set()
        lines_added = 0
        lines_removed = 0

        for commit in commits:
            for filepath, added, removed in commit.files:
                all_files.add(filepath)
                lines_added += added
                lines_removed += removed

        lines_touched = lines_added + lines_removed
        lines_net = lines_added - lines_removed

        # Day type classification
        day_type, day_icon = classify_day_type(tag_percentages)
        day_type_multiplier = DAY_TYPES[day_type]['multiplier']

        # Cognitive load
        cognitive_load = calculate_cognitive_load(total_commits, len(all_files), lines_touched)

        # Productivity index
        productivity = calculate_productivity_index(
            commits_weighted,
            lines_net,
            cognitive_load,
            day_type_multiplier
        )

        return DayStats(
            date=date,
            commits_egi=len(commits_egi),
            lines_net_egi=lines_net_egi,
            commits_natan=len(commits_natan),
            lines_net_natan=lines_net_natan,
            commits=total_commits,
            commits_weighted=commits_weighted,
            files_modified=len(all_files),
            lines_added=lines_added,
            lines_removed=lines_removed,
            lines_touched=lines_touched,
            lines_net=lines_net,
            tags=dict(tag_counter),
            day_type=day_type,
            day_type_icon=day_icon,
            cognitive_load=cognitive_load,
            productivity_index=productivity
        )

    def analyze_week(
        self,
        week_number: int,
        start_date: dt.date,
        end_date: dt.date,
        description: str
    ) -> Tuple[WeekStats, List[DayStats]]:
        """Analyze weekly statistics with daily breakdown."""
        daily_stats = []
        current_date = start_date

        while current_date <= end_date:
            day_stat = self.analyze_day(current_date)
            daily_stats.append(day_stat)
            current_date += dt.timedelta(days=1)

        # Aggregate weekly stats (SEPARATED + COMBINED)
        total_commits_egi = sum(d.commits_egi for d in daily_stats)
        total_lines_net_egi = sum(d.lines_net_egi for d in daily_stats)
        total_commits_natan = sum(d.commits_natan for d in daily_stats)
        total_lines_net_natan = sum(d.lines_net_natan for d in daily_stats)

        total_commits = sum(d.commits for d in daily_stats)
        total_commits_weighted = sum(d.commits_weighted for d in daily_stats)
        total_files = sum(d.files_modified for d in daily_stats)
        total_lines_touched = sum(d.lines_touched for d in daily_stats)
        total_lines_net = sum(d.lines_net for d in daily_stats)

        # Tag coverage (% of commits with tags)
        all_tags = []
        for day in daily_stats:
            all_tags.extend(day.tags.keys())

        tagged_commits = sum(
            count for day in daily_stats
            for tag, count in day.tags.items()
            if tag != 'UNTAGGED'
        )

        tag_coverage_pct = (tagged_commits / total_commits * 100) if total_commits > 0 else 0.0

        # Average cognitive load and productivity
        days_with_commits = [d for d in daily_stats if d.commits > 0]
        avg_cognitive = (
            sum(d.cognitive_load for d in days_with_commits) / len(days_with_commits)
            if days_with_commits else 1.0
        )
        avg_productivity = (
            sum(d.productivity_index for d in days_with_commits) / len(days_with_commits)
            if days_with_commits else 0.0
        )

        # Testing time estimate (22 min per commit)
        testing_minutes = total_commits * 22.0
        coding_minutes = total_commits * 22.0  # Same estimate

        # Format period
        month_names = {
            1: 'Gennaio', 2: 'Febbraio', 3: 'Marzo', 4: 'Aprile', 5: 'Maggio', 6: 'Giugno',
            7: 'Luglio', 8: 'Agosto', 9: 'Settembre', 10: 'Ottobre', 11: 'Novembre', 12: 'Dicembre'
        }

        start_month = month_names.get(start_date.month, start_date.strftime('%B'))
        end_month = month_names.get(end_date.month, end_date.strftime('%B'))

        if start_date.month == end_date.month:
            period = f"{start_date.day}-{end_date.day} {start_month} 2025"
        else:
            period = f"{start_date.day} {start_month} - {end_date.day} {end_month} 2025"

        week_stats = WeekStats(
            week_number=week_number,
            period=period,
            description=description,
            start_date=start_date,
            end_date=end_date,
            commits_egi=total_commits_egi,
            lines_net_egi=total_lines_net_egi,
            commits_natan=total_commits_natan,
            lines_net_natan=total_lines_net_natan,
            commits=total_commits,
            commits_weighted=total_commits_weighted,
            files_modified=total_files,
            lines_touched=total_lines_touched,
            lines_net=total_lines_net,
            tag_coverage_pct=tag_coverage_pct,
            avg_cognitive_load=avg_cognitive,
            avg_productivity=avg_productivity,
            testing_minutes=testing_minutes,
            coding_minutes=coding_minutes
        )

        return week_stats, daily_stats

    def generate_full_report(
        self,
        start_date: dt.date,
        end_date: dt.date
    ) -> Tuple[List[WeekStats], List[DayStats]]:
        """Generate complete weekly + daily report."""
        all_weekly_stats = []
        all_daily_stats = []

        week_number = 1

        # Calculate MONDAY of the week containing start_date
        # weekday(): 0 = Monday, 6 = Sunday
        days_since_monday = start_date.weekday()
        current_monday = start_date - dt.timedelta(days=days_since_monday)

        # Week descriptions (from v3)
        week_descriptions = {
            1: 'Introduzione TAG system',
            2: 'Stabilizzazione',
            3: 'Consolidamento',
            4: 'Sviluppo avanzato',
            5: 'Ottimizzazione',
            6: 'Completamento features',
            7: 'Testing e refinement',
            8: 'Advanced development',
            9: 'Production readiness',
            10: 'PA/Enterprise Development',
            11: 'Scalability & Performance',
            12: 'Final Polish'
        }

        while current_monday <= end_date:
            current_sunday = current_monday + dt.timedelta(days=6)
            week_end = min(current_sunday, end_date)

            description = week_descriptions.get(week_number, f'Sviluppo settimana {week_number}')
            if week_end == end_date and end_date == dt.date.today():
                description += ' (in corso)'

            week_stats, daily_stats = self.analyze_week(
                week_number,
                current_monday,
                week_end,
                description
            )

            all_weekly_stats.append(week_stats)
            all_daily_stats.extend(daily_stats)

            current_monday += dt.timedelta(days=7)
            week_number += 1

        return all_weekly_stats, all_daily_stats


# ═══════════════════════════════════════════════════════════════
# EXCEL EXPORT
# ═══════════════════════════════════════════════════════════════

def create_excel_report(
    weekly_stats: List[WeekStats],
    daily_stats: List[DayStats],
    output_path: Path
) -> None:
    """Create Excel file with 3 sheets: Weekly, Daily, Summary."""

    if not HAVE_PANDAS:
        print("⚠️ pandas/openpyxl not available. Skipping Excel export.", file=sys.stderr)
        return

    # Prepare weekly data (UNA SOLA RIGA per settimana con colonne separate)
    weekly_data = []
    for week in weekly_stats:
        weekly_data.append({
            'Settimana': f'Settimana {week.week_number}',
            'Periodo': week.period,
            'Descrizione': week.description,
            # EGI columns
            'Commits EGI': week.commits_egi,
            'Righe Nette EGI': week.lines_net_egi,
            # NATAN_LOC columns
            'Commits NATAN_LOC': week.commits_natan,
            'Righe Nette NATAN_LOC': week.lines_net_natan,
            # Combined columns
            'Commits TOTALI': week.commits,
            'Commits Pesati': round(week.commits_weighted, 1),
            'Files Modificati': week.files_modified,
            'Righe Toccate': week.lines_touched,
            'Righe Nette TOTALI': week.lines_net,
            'TAG Coverage %': round(week.tag_coverage_pct, 1),
            'Cognitive Load': round(week.avg_cognitive_load, 2),
            'Productivity Index': round(week.avg_productivity, 2),
            'Testing Time (h)': round(week.testing_minutes / 60, 1),
            'Coding Time (h)': round(week.coding_minutes / 60, 1)
        })

    # Prepare daily data (UNA SOLA RIGA per giorno con colonne separate)
    daily_data = []
    for day in daily_stats:
        if day.commits == 0:
            continue  # Skip days without commits

        tags_str = ', '.join(f"{tag}:{count}" for tag, count in day.tags.items())

        daily_data.append({
            'Data': day.date.isoformat(),
            'Giorno': day.date.strftime('%A'),
            # EGI columns
            'Commits EGI': day.commits_egi,
            'Righe Nette EGI': day.lines_net_egi,
            # NATAN_LOC columns
            'Commits NATAN_LOC': day.commits_natan,
            'Righe Nette NATAN_LOC': day.lines_net_natan,
            # Combined columns
            'Commits TOTALI': day.commits,
            'Commits Pesati': round(day.commits_weighted, 1),
            'Files': day.files_modified,
            'Righe +': day.lines_added,
            'Righe -': day.lines_removed,
            'Righe Toccate': day.lines_touched,
            'Righe Nette TOTALI': day.lines_net,
            'TAG Distribution': tags_str,
            'Day Type': f"{day.day_type_icon} {day.day_type}",
            'Cognitive Load': round(day.cognitive_load, 2),
            'Productivity Index': round(day.productivity_index, 2)
        })

    # Summary data
    total_commits = sum(w.commits for w in weekly_stats)
    total_weighted = sum(w.commits_weighted for w in weekly_stats)
    total_lines_touched = sum(w.lines_touched for w in weekly_stats)
    total_lines_net = sum(w.lines_net for w in weekly_stats)
    total_testing_hours = sum(w.testing_minutes for w in weekly_stats) / 60
    avg_tag_coverage = sum(w.tag_coverage_pct for w in weekly_stats) / len(weekly_stats) if weekly_stats else 0
    avg_productivity = sum(d.productivity_index for d in daily_stats if d.commits > 0) / len([d for d in daily_stats if d.commits > 0]) if any(d.commits > 0 for d in daily_stats) else 0

    summary_data = [
        {'Metrica': 'Commit Totali', 'Valore': total_commits, 'Note': 'Tutti i commit nel periodo'},
        {'Metrica': 'Commit Pesati Totali', 'Valore': round(total_weighted, 1), 'Note': 'Basato su TAG weights (REFACTOR=2x, FIX=1.5x)'},
        {'Metrica': 'Copertura TAG Media', 'Valore': f'{round(avg_tag_coverage, 1)}%', 'Note': 'Media delle settimane'},
        {'Metrica': 'Righe Toccate Totali', 'Valore': f'{total_lines_touched:,}', 'Note': 'Added + Removed (real work, NO deps/testing data)'},
        {'Metrica': 'Righe Nette Totali', 'Valore': f'{total_lines_net:,}', 'Note': 'Added - Removed'},
        {'Metrica': 'Testing Time Totale', 'Valore': f'{round(total_testing_hours, 1)}h', 'Note': '22 min per commit estimate'},
        {'Metrica': 'Indice Produttività Medio', 'Valore': round(avg_productivity, 2), 'Note': 'Multi-dimensional scoring'},
    ]

    # Create DataFrames
    df_weekly = pd.DataFrame(weekly_data)
    df_daily = pd.DataFrame(daily_data)
    df_summary = pd.DataFrame(summary_data)

    # Write Excel
    with pd.ExcelWriter(output_path, engine='openpyxl') as writer:
        df_summary.to_excel(writer, sheet_name='Summary', index=False)
        df_weekly.to_excel(writer, sheet_name='Weekly', index=False)
        df_daily.to_excel(writer, sheet_name='Daily', index=False)

        # Auto-adjust column widths
        for sheet_name in ['Summary', 'Weekly', 'Daily']:
            worksheet = writer.sheets[sheet_name]
            for column in worksheet.columns:
                max_length = 0
                column_letter = column[0].column_letter
                for cell in column:
                    try:
                        if len(str(cell.value)) > max_length:
                            max_length = len(str(cell.value))
                    except:
                        pass
                adjusted_width = min(max_length + 2, 50)
                worksheet.column_dimensions[column_letter].width = adjusted_width

    print(f"✅ Excel file created: {output_path}")


# ═══════════════════════════════════════════════════════════════
# TERMINAL OUTPUT
# ═══════════════════════════════════════════════════════════════

def print_terminal_summary(weekly_stats: List[WeekStats], daily_stats: List[DayStats], is_single_day: bool = False, requested_date: dt.date = None) -> None:
    """Print summary to terminal with EGI/NATAN_LOC separation."""
    # Se single day, prendi quel giorno. Altrimenti cerca oggi, o ultimo giorno con commit.
    if is_single_day and daily_stats and requested_date:
        # Cerca il giorno richiesto, non il primo della lista
        target_day = next((d for d in daily_stats if d.date == requested_date), daily_stats[0])
    else:
        today_list = [d for d in daily_stats if d.date == dt.date.today()]
        if today_list:
            target_day = today_list[0]
        else:
            # Prendi ultimo giorno con commit
            days_with_commits = [d for d in daily_stats if d.commits > 0]
            target_day = days_with_commits[-1] if days_with_commits else None

    if target_day:
        print("\n" + "="*70)
        if is_single_day:
            print("📊 STATISTICHE GIORNO ANALIZZATO")
        else:
            print("📊 STATISTICHE OGGI")
        print("="*70)
        print(f"📅 Data: {target_day.date.isoformat()}")
        print()
        print("📊 TOT GIORNALIERO EGI:")
        print(f"   ✨ Commits EGI: {target_day.commits_egi}")
        print(f"   💯 Righe nette EGI: {target_day.lines_net_egi:+,}")
        print()
        print("📊 TOT GIORNALIERO NATAN_LOC:")
        print(f"   ✨ Commits NATAN_LOC: {target_day.commits_natan}")
        print(f"   💯 Righe nette NATAN_LOC: {target_day.lines_net_natan:+,}")
        print()
        print("📊 SOMMA GIORNALIERA (EGI + NATAN_LOC):")
        print(f"   ✨ Commits totali: {target_day.commits} (pesati: {target_day.commits_weighted:.1f})")
        print(f"   📁 Files modificati: {target_day.files_modified}")
        print(f"   📈 Righe aggiunte: +{target_day.lines_added:,}")
        print(f"   📉 Righe rimosse: -{target_day.lines_removed:,}")
        print(f"   🔢 Righe toccate: {target_day.lines_touched:,}")
        print(f"   💯 Righe nette: {target_day.lines_net:+,}")
        print(f"   {target_day.day_type_icon} Tipo giornata: {target_day.day_type}")
        print(f"   🧠 Cognitive Load: {target_day.cognitive_load:.2f}x")
        print(f"   🚀 Productivity Index: {target_day.productivity_index:.2f}")

        if target_day.tags:
            print(f"\n🏷️ TAG Distribution:")
            for tag, count in sorted(target_day.tags.items(), key=lambda x: -x[1]):
                weight = TAG_WEIGHTS.get(tag, 0.5)
                print(f"   [{tag}]: {count} commits (weight: {weight}x)")

    # Weekly summary (SOLO se range > 1 giorno) - MOSTRA ULTIMA SETTIMANA
    if not is_single_day and weekly_stats:
        last_week = weekly_stats[-1]

        print("\n" + "="*70)
        print("📊 RIEPILOGO ULTIMA SETTIMANA")
        print("="*70)
        print(f"🗓️ Periodo: {last_week.period}")
        print()
        print("📊 TOTALE SETTIMANALE EGI:")
        print(f"   ✨ Commits EGI: {last_week.commits_egi}")
        print(f"   💯 Righe nette EGI: {last_week.lines_net_egi:+,}")
        print()
        print("📊 TOTALE SETTIMANALE NATAN_LOC:")
        print(f"   ✨ Commits NATAN_LOC: {last_week.commits_natan}")
        print(f"   💯 Righe nette NATAN_LOC: {last_week.lines_net_natan:+,}")
        print()
        print("📊 SOMMA SETTIMANALE (EGI + NATAN_LOC):")
        print(f"   ✨ Commit totali: {last_week.commits} (pesati: {last_week.commits_weighted:.1f})")
        print(f"   💯 Righe nette totali: {last_week.lines_net:+,}")
        print(f"   📊 Settimane analizzate: {len(weekly_stats)} (Excel completo)")
        print(f"   🗓️ Periodo completo: {weekly_stats[0].start_date.isoformat()} → {weekly_stats[-1].end_date.isoformat()}")


# ═══════════════════════════════════════════════════════════════
# CLI & MAIN
# ═══════════════════════════════════════════════════════════════

def parse_args() -> argparse.Namespace:
    """Parse command-line arguments."""
    parser = argparse.ArgumentParser(
        description='EGI Productivity Analytics v6.0 - HYBRID EDITION',
        formatter_class=argparse.RawDescriptionHelpFormatter
    )

    parser.add_argument(
        '--repo',
        type=Path,
        default=Path(__file__).parent.parent,
        help='Git repository path (default: parent of script)'
    )

    parser.add_argument(
        '--since',
        type=str,
        default='2025-08-19',
        help='Start date YYYY-MM-DD (default: 2025-08-19)'
    )

    parser.add_argument(
        '--until',
        type=str,
        default=None,
        help='End date YYYY-MM-DD (default: today)'
    )

    parser.add_argument(
        '--xlsx',
        type=Path,
        default=None,
        help='Output Excel path (default: productivity_YYYYMMDD.xlsx in repo)'
    )

    return parser.parse_args()


def main() -> int:
    """Main execution."""
    print("🚀 EGI Commit Statistics Excel Exporter v6.1.0")
    print("   DUAL REPO EDITION - EGI + NATAN_LOC")
    print("   Multi-Dimensional Productivity Analytics")
    print("   🔧 Dual repository support with unified stats")
    print("="*70)

    args = parse_args()

    # Parse dates
    try:
        start_date = dt.date.fromisoformat(args.since)
    except ValueError as e:
        print(f"❌ Invalid --since date: {e}", file=sys.stderr)
        return 1

    if args.until:
        try:
            end_date = dt.date.fromisoformat(args.until)
        except ValueError as e:
            print(f"❌ Invalid --until date: {e}", file=sys.stderr)
            return 1
    else:
        end_date = dt.date.today()

    # Validate repo
    if not args.repo.exists():
        print(f"❌ Repository not found: {args.repo}", file=sys.stderr)
        return 1

    git_dir = args.repo / '.git'
    if not git_dir.exists():
        print(f"❌ Not a git repository: {args.repo}", file=sys.stderr)
        return 1

    # Default output path
    if args.xlsx is None:
        output_filename = f"productivity_{dt.date.today().strftime('%Y%m%d')}.xlsx"
        args.xlsx = args.repo / output_filename

    print(f"\n📊 Analisi periodo: {start_date.isoformat()} → {end_date.isoformat()}")
    print(f"📁 Repository: {args.repo}")
    print(f"💾 Output: {args.xlsx}")
    print()

    # Detect single day analysis
    is_single_day = (start_date == end_date)

    # Run analysis
    try:
        analyzer = ProductivityAnalyzer(args.repo)

        if is_single_day:
            print("🔍 Analisi singolo giorno...")
        else:
            print("🔍 Generazione report completo...")

        weekly_stats, daily_stats = analyzer.generate_full_report(start_date, end_date)

        if not is_single_day:
            print(f"✅ Analizzate {len(weekly_stats)} settimane, {len([d for d in daily_stats if d.commits > 0])} giorni con commit")

        # Terminal output (giornaliero sempre, settimanale solo se NOT single day)
        print_terminal_summary(weekly_stats, daily_stats, is_single_day, start_date if is_single_day else None)

        # Excel export (SOLO se NOT single day)
        if not is_single_day:
            if HAVE_PANDAS:
                print("\n📊 Creazione file Excel...")
                create_excel_report(weekly_stats, daily_stats, args.xlsx)

                if args.xlsx.exists():
                    file_size = args.xlsx.stat().st_size
                    print(f"✅ Excel file created: {args.xlsx}")
                    print(f"📁 Dimensione file: {file_size:,} bytes")
            else:
                print("\n⚠️ pandas/openpyxl non disponibili. Excel export skipped.")
                print("   Installa con: pip install pandas openpyxl")
        else:
            print("\n💡 Analisi singolo giorno - Excel export skipped")

        print("\n🎉 Analisi completata con successo!")
        return 0

    except Exception as e:
        print(f"\n❌ Errore durante l'analisi: {e}", file=sys.stderr)
        import traceback
        traceback.print_exc()
        return 1


if __name__ == "__main__":
    sys.exit(main())
