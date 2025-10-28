#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
EGI Commit Statistics Exporter v6.0.0 - HYBRID EDITION
Author: Fabio Cherici & Padmin D. Curtis (AI Partner OS3.0)
License: MIT
Version: 6.0.0 (FlorenceEGI - PA/Enterprise Analytics)
Date: 2025-10-28

Overview
--------
HYBRID combining best of v3.py (weekly analytics) + v5.py (modern code).

Features from v3.py:
- Weekly aggregation from 2025-08-19 to today
- Multi-dimensional productivity scoring
- Day type classification (REFACTORING, BUG_FIXING, FEATURE_DEV, etc.)
- Cognitive load estimation with components
- Tag-weighted commits with advanced weights
- Comprehensive exclude patterns (deps + testing data)
- 3-sheet Excel: Weekly, Daily, Testing Summary

Features from v5.py:
- Modern Python 3.10+ with dataclasses
- Clean timezone parsing (fixes +0100 issue)
- Flexible date handling
- Optional pandas/openpyxl (graceful degradation)
- CLI arguments support

Improvements v6:
- Parametrizable: --since/--until override default (2025-08-19 to today)
- Better error handling
- Unified exclude patterns
- Combined cognitive load + productivity index

Usage
-----
# Default: Process ALL from 2025-08-19 to today
python egi_productivity_v6.py

# Override date range
python egi_productivity_v6.py --since 2025-10-01 --until 2025-10-28

# Custom output
python egi_productivity_v6.py --xlsx /custom/path.xlsx
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
        cmd = f'git log --oneline --since="{since} 00:00:00" --until="{until} 23:59:59"'
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


# ═══════════════════════════════════════════════════════════════
# STATISTICS ENGINE
# ═══════════════════════════════════════════════════════════════

class ProductivityAnalyzer:
    """Main analytics engine."""

    def __init__(self, repo_path: Path):
        self.repo = GitRepo(repo_path)

    def analyze_day(self, date: dt.date) -> DayStats:
        """Analyze single day statistics."""
        commits = self.repo.get_commits_for_day(date)

        if not commits:
            return DayStats(
                date=date,
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

        # File statistics
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

        # Aggregate weekly stats
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
        current_monday = start_date

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

    # Prepare weekly data
    weekly_data = []
    for week in weekly_stats:
        weekly_data.append({
            'Settimana': f'Settimana {week.week_number}',
            'Periodo': week.period,
            'Descrizione': week.description,
            'Commits': week.commits,
            'Commits Pesati': round(week.commits_weighted, 1),
            'Files Modificati': week.files_modified,
            'Righe Toccate': week.lines_touched,
            'Righe Nette': week.lines_net,
            'TAG Coverage %': round(week.tag_coverage_pct, 1),
            'Cognitive Load': round(week.avg_cognitive_load, 2),
            'Productivity Index': round(week.avg_productivity, 2),
            'Testing Time (h)': round(week.testing_minutes / 60, 1),
            'Coding Time (h)': round(week.coding_minutes / 60, 1)
        })

    # Prepare daily data
    daily_data = []
    for day in daily_stats:
        if day.commits == 0:
            continue  # Skip days without commits

        tags_str = ', '.join(f"{tag}:{count}" for tag, count in day.tags.items())

        daily_data.append({
            'Data': day.date.isoformat(),
            'Giorno': day.date.strftime('%A'),
            'Commits': day.commits,
            'Commits Pesati': round(day.commits_weighted, 1),
            'Files': day.files_modified,
            'Righe +': day.lines_added,
            'Righe -': day.lines_removed,
            'Righe Toccate': day.lines_touched,
            'Righe Nette': day.lines_net,
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

def print_terminal_summary(weekly_stats: List[WeekStats], daily_stats: List[DayStats]) -> None:
    """Print summary to terminal."""
    today_stats = [d for d in daily_stats if d.date == dt.date.today()]

    if today_stats:
        today = today_stats[0]
        print("\n" + "="*70)
        print("📊 STATISTICHE OGGI")
        print("="*70)
        print(f"📅 Data: {today.date.isoformat()}")
        print(f"✨ Commits: {today.commits} (pesati: {today.commits_weighted:.1f})")
        print(f"📁 Files modificati: {today.files_modified}")
        print(f"📈 Righe aggiunte: +{today.lines_added:,}")
        print(f"📉 Righe rimosse: -{today.lines_removed:,}")
        print(f"🔢 Righe toccate: {today.lines_touched:,}")
        print(f"💯 Righe nette: {today.lines_net:+,}")
        print(f"{today.day_type_icon} Tipo giornata: {today.day_type}")
        print(f"🧠 Cognitive Load: {today.cognitive_load:.2f}x")
        print(f"🚀 Productivity Index: {today.productivity_index:.2f}")

        if today.tags:
            print(f"\n🏷️ TAG Distribution:")
            for tag, count in sorted(today.tags.items(), key=lambda x: -x[1]):
                weight = TAG_WEIGHTS.get(tag, 0.5)
                print(f"   [{tag}]: {count} commits (weight: {weight}x)")

    # Weekly summary
    print("\n" + "="*70)
    print("📊 RIEPILOGO SETTIMANALE")
    print("="*70)

    total_commits = sum(w.commits for w in weekly_stats)
    total_weighted = sum(w.commits_weighted for w in weekly_stats)
    total_lines_net = sum(w.lines_net for w in weekly_stats)

    print(f"🗓️ Periodo analizzato: {weekly_stats[0].start_date.isoformat()} → {weekly_stats[-1].end_date.isoformat()}")
    print(f"📊 Settimane totali: {len(weekly_stats)}")
    print(f"✨ Commit totali: {total_commits} (pesati: {total_weighted:.1f})")
    print(f"💯 Righe nette totali: {total_lines_net:+,}")

    print("\n📈 Top 3 settimane per produttività:")
    top_weeks = sorted(weekly_stats, key=lambda w: w.avg_productivity, reverse=True)[:3]
    for i, week in enumerate(top_weeks, 1):
        print(f"   {i}. {week.period} - Productivity: {week.avg_productivity:.2f}")


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
    print("🚀 EGI Commit Statistics Excel Exporter v6.0.0")
    print("   HYBRID EDITION - Best of v3 + v5")
    print("   Multi-Dimensional Productivity Analytics")
    print("   🔧 Accurate line counting (excludes deps + testing data)")
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

    # Run analysis
    try:
        analyzer = ProductivityAnalyzer(args.repo)

        print("🔍 Generazione report completo...")
        weekly_stats, daily_stats = analyzer.generate_full_report(start_date, end_date)

        print(f"✅ Analizzate {len(weekly_stats)} settimane, {len([d for d in daily_stats if d.commits > 0])} giorni con commit")

        # Terminal output
        print_terminal_summary(weekly_stats, daily_stats)

        # Excel export
        if HAVE_PANDAS:
            print("\n📊 Creazione file Excel...")
            create_excel_report(weekly_stats, daily_stats, args.xlsx)

            if args.xlsx.exists():
                file_size = args.xlsx.stat().st_size
                print(f"📁 Dimensione file: {file_size:,} bytes")
        else:
            print("\n⚠️ pandas/openpyxl non disponibili. Excel export skipped.")
            print("   Installa con: pip install pandas openpyxl")

        print("\n🎉 Analisi completata con successo!")
        return 0

    except Exception as e:
        print(f"\n❌ Errore durante l'analisi: {e}", file=sys.stderr)
        import traceback
        traceback.print_exc()
        return 1


if __name__ == "__main__":
    sys.exit(main())
