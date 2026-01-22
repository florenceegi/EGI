#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
EGI Productivity Analytics v7.1.0 - Complete Edition
Author: Fabio Cherici & Padmin D. Curtis (AI Partner OS3.0)
License: MIT
Version: 7.1.0
Date: 2026-01-20

COMPLETE rewrite with v6-compatible comprehensive metrics.
"""

from __future__ import annotations

import argparse
import datetime as dt
import math
import os
import sys
from collections import Counter, defaultdict
from dataclasses import dataclass
from pathlib import Path
from typing import Dict, List, Tuple
import yaml

# Import v7 components
try:
    from tag_system_v2 import TagSystem, TAG_WEIGHTS, DAY_TYPES
    from github_client import GitHubMultiRepoClient, CommitData
    from auto_categorizer import CommitCategorizer
except ImportError as e:
    print(f"❌ Missing v7 component: {e}")
    sys.exit(1)

# Excel support
HAVE_PANDAS = False
try:
    import pandas as pd
    import openpyxl
    HAVE_PANDAS = True
except ImportError:
    pass


# ═══════════════════════════════════════════════════════════════
# DATACLASSES (from v6)
# ═══════════════════════════════════════════════════════════════

@dataclass
class DayStats:
    date: dt.date
    # Per-repository breakdown
    repos_commits: Dict[str, int]  # repo_name -> commit_count
    repos_lines_net: Dict[str, int]  # repo_name -> net_lines
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
    # Per-repository breakdown
    repos_commits: Dict[str, int]
    repos_lines_net: Dict[str, int]
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

def classify_day_type(tag_percentages: Dict[str, float]) -> Tuple[str, str]:
    """Classify day based on tag distribution."""
    for day_type, config in DAY_TYPES.items():
        if config['criteria'](tag_percentages):
            return day_type, config['icon']
    return 'MIXED', '📦'


def calculate_cognitive_load(commits: int, files: int, lines_touched: int) -> float:
    """Calculate cognitive load using log-scaled formula."""
    if commits == 0:
        return 1.0
    
    li = math.log(lines_touched + 1)
    fm = math.log(files + 1)
    dp = math.log(commits + 1)
    
    cl = (li + fm + dp) / 3.0
    cl_normalized = 1.0 + (cl / 2.0)
    return max(1.0, min(3.5, cl_normalized))


def calculate_productivity_index(
    commits_weighted: float,
    lines_net: int,
    cognitive_load: float,
    day_type_multiplier: float
) -> float:
    """Calculate productivity index."""
    if cognitive_load == 0:
        cognitive_load = 1.0
    
    base_score = (commits_weighted * 10.0) + (lines_net / 10.0)
    return (base_score * day_type_multiplier) / cognitive_load


# ═══════════════════════════════════════════════════════════════
# ANALYSIS ENGINE (v6-compatible)
# ═══════════════════════════════════════════════════════════════

class ProductivityAnalyzer:
    """Main analytics engine with v6-compatible metrics."""
    
    def __init__(self, github_client: GitHubMultiRepoClient, categorizer: CommitCategorizer):
        self.github = github_client
        self.categorizer = categorizer
    
    def analyze_day(self, date: dt.date, all_commits: List[CommitData]) -> DayStats:
        """Analyze single day statistics."""
        # Filter commits for this day
        day_commits = [c for c in all_commits if c.date.date() == date]
        
        if not day_commits:
            return DayStats(
                date=date,
                repos_commits={},
                repos_lines_net={},
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
        
        # Count tags and calculate weights
        tag_counter = Counter()
        commits_weighted = 0.0
        
        for commit in day_commits:
            tag, confidence = TagSystem.parse_tag(commit.message)
            if not tag:
                result = self.categorizer.categorize(commit.message, commit.files_changed)
                tag = result.tag
            
            tag_counter[tag] += 1
            weight = TagSystem.get_weight(tag)
            commits_weighted += weight
        
        # Calculate tag percentages
        total_commits = len(day_commits)
        tag_percentages = {tag: (count / total_commits) * 100 for tag, count in tag_counter.items()}
        
        # Repository breakdown
        repos_commits = Counter()
        repos_lines_net = Counter()
        
        for commit in day_commits:
            repos_commits[commit.repository] += 1
            repos_lines_net[commit.repository] += (commit.additions - commit.deletions)
        
        # File statistics
        all_files = set()
        lines_added = sum(c.additions for c in day_commits)
        lines_removed = sum(c.deletions for c in day_commits)
        
        for commit in day_commits:
            all_files.update(commit.files_changed)
        
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
            repos_commits=dict(repos_commits),
            repos_lines_net=dict(repos_lines_net),
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
        description: str,
        all_commits: List[CommitData]
    ) -> Tuple[WeekStats, List[DayStats]]:
        """Analyze weekly statistics with daily breakdown."""
        daily_stats = []
        current_date = start_date
        
        while current_date <= end_date:
            day_stat = self.analyze_day(current_date, all_commits)
            daily_stats.append(day_stat)
            current_date += dt.timedelta(days=1)
        
        # Aggregate weekly stats
        repos_commits = Counter()
        repos_lines_net = Counter()
        
        for day in daily_stats:
            for repo, count in day.repos_commits.items():
                repos_commits[repo] += count
            for repo, lines in day.repos_lines_net.items():
                repos_lines_net[repo] += lines
        
        total_commits = sum(d.commits for d in daily_stats)
        total_commits_weighted = sum(d.commits_weighted for d in daily_stats)
        total_files = sum(d.files_modified for d in daily_stats)
        total_lines_touched = sum(d.lines_touched for d in daily_stats)
        total_lines_net = sum(d.lines_net for d in daily_stats)
        
        # TAG coverage
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
        
        # Testing time estimate
        testing_minutes = total_commits * 22.0
        coding_minutes = total_commits * 22.0
        
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
            repos_commits=dict(repos_commits),
            repos_lines_net=dict(repos_lines_net),
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
        # Fetch all commits at once
        start_dt = dt.datetime.combine(start_date, dt.time.min)
        end_dt = dt.datetime.combine(end_date, dt.time.max)
        
        print("🔍 Fetching commits from GitHub...")
        all_commits = self.github.get_commits(start_dt, end_dt)
        print(f"✅ Fetched {len(all_commits)} commits")
        
        all_weekly_stats = []
        all_daily_stats = []
        
        week_number = 1
        
        # Calculate MONDAY of the week containing start_date
        days_since_monday = start_date.weekday()
        current_monday = start_date - dt.timedelta(days=days_since_monday)
        
        # Week descriptions
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
            12: 'Final Polish',
            13: 'Quality Assurance',
            14: 'Deployment Preparation',
            15: 'User Testing',
            16: 'Performance Tuning',
            17: 'Security Hardening',
            18: 'Documentation',
            19: 'Launch Preparation',
            20: 'Post-Launch Support',
            21: 'Iteration & Improvement',
            22: 'Feature Expansion',
            23: 'Platform Optimization',
            24: 'Enterprise Integration',
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
                description,
                all_commits
            )
            
            all_weekly_stats.append(week_stats)
            all_daily_stats.extend(daily_stats)
            
            current_monday += dt.timedelta(days=7)
            week_number += 1
        
        return all_weekly_stats, all_daily_stats


# Continua nel prossimo file...
