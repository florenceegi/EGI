#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
EGI Productivity Analytics v7.0.0 - GitHub API Edition
Author: Fabio Cherici & Padmin D. Curtis (AI Partner OS3.0)
License: MIT
Version: 7.0.0
Date: 2026-01-20

Features:
- Multi-repository GitHub API support (5 repos)
- Expanded TAG system (16 tags with 86 aliases)
- Auto-categorization with ML/rule-based hybrid
- Automatic time tracking (WakaTime + git inference)
- SQLite caching for API efficiency
- Excel export with multi-repo breakdown

Usage:
    # Default: full report from all 5 repos
    python egi_productivity_v7.py
    
    # Custom date range
    python egi_productivity_v7.py --since 2026-01-01 --until 2026-01-20
    
    # Test GitHub connection
    python egi_productivity_v7.py --test-github
"""

from __future__ import annotations

import argparse
import datetime as dt
import os
import sys
from pathlib import Path
from typing import Dict, List, Tuple
import yaml

# Import v7 components
try:
    from tag_system_v2 import TagSystem, TAG_WEIGHTS
    from github_client import GitHubMultiRepoClient, CommitData
    from auto_categorizer import CommitCategorizer
except ImportError as e:
    print(f"❌ Missing v7 component: {e}")
    print("   Make sure tag_system_v2.py, github_client.py, and auto_categorizer.py are in bash_files/")
    sys.exit(1)

# Excel support (optional)
HAVE_PANDAS = False
try:
    import pandas as pd
    import openpyxl
    HAVE_PANDAS = True
except ImportError:
    pass


# ═══════════════════════════════════════════════════════════════
# CONFIGURATION
# ═══════════════════════════════════════════════════════════════

DEFAULT_CONFIG = {
    'github': {
        'repositories': [
            'AutobookNft/EGI',
            'AutobookNft/EGI-HUB',
            'AutobookNft/EGI-HUB-HOME-REACT',
            'AutobookNft/EGI-INFO',
            'AutobookNft/NATAN_LOC',
        ]
    },
    'analysis': {
        'default_start_date': '2025-08-19',
    }
}


def load_config(config_path: Path = None) -> Dict:
    """Load configuration from YAML or use defaults."""
    if config_path and config_path.exists():
        with open(config_path) as f:
            return yaml.safe_load(f)
    return DEFAULT_CONFIG


# ═══════════════════════════════════════════════════════════════
# ANALYSIS ENGINE
# ═══════════════════════════════════════════════════════════════

class ProductivityAnalyzerV7:
    """Main analytics engine for v7."""
    
    def __init__(self, github_client: GitHubMultiRepoClient, categorizer: CommitCategorizer):
        self.github = github_client
        self.categorizer = categorizer
    
    def analyze_period(
        self,
        start_date: dt.date,
        end_date: dt.date
    ) -> Tuple[Dict[str, List], Dict]:
        """
        Analyze commits across all repositories.
        
        Returns:
            (repo_stats, summary_stats)
        """
        # Fetch all commits from GitHub
        start_dt = dt.datetime.combine(start_date, dt.time.min)
        end_dt = dt.datetime.combine(end_date, dt.time.max)
        
        commits = self.github.get_commits(start_dt, end_dt)
        
        # Group by repository and date
        repo_stats = {}
        daily_stats = {}
        
        for commit in commits:
            # Parse/infer tag
            tag, confidence = TagSystem.parse_tag(commit.message)
            if not tag:
                # Auto-categorize untagged commits
                result = self.categorizer.categorize(
                    message=commit.message,
                    files=commit.files_changed
                )
                tag = result.tag
                confidence = result.confidence
            
            # Get tag weight
            weight = TagSystem.get_weight(tag)
            
            # Initialize repo stats
            if commit.repository not in repo_stats:
                repo_stats[commit.repository] = []
            
            # Store commit info
            commit_info = {
                'date': commit.date.date(),
                'sha': commit.sha,
                'message': commit.message,
                'author': commit.author,
                'tag': tag,
                'confidence': confidence,
                'weight': weight,
                'additions': commit.additions,
                'deletions': commit.deletions,
                'net_lines': commit.additions - commit.deletions,
                'files_count': len(commit.files_changed),
            }
            
            repo_stats[commit.repository].append(commit_info)
            
            # Daily aggregation
            day_key = commit.date.date().isoformat()
            if day_key not in daily_stats:
                daily_stats[day_key] = {
                    'commits': 0,
                    'additions': 0,
                    'deletions': 0,
                    'net_lines': 0,
                    'tags': {},
                    'repos': set()
                }
            
            daily = daily_stats[day_key]
            daily['commits'] += 1
            daily['additions'] += commit.additions
            daily['deletions'] += commit.deletions
            daily['net_lines'] += commit.additions - commit.deletions
            daily['tags'][tag] = daily['tags'].get(tag, 0) + 1
            daily['repos'].add(commit.repository)
        
        # Calculate summary stats
        summary = {
            'total_commits': len(commits),
            'total_repos': len(repo_stats),
            'date_range': f"{start_date.isoformat()} → {end_date.isoformat()}",
            'repo_breakdown': {
                repo: len(commits_list)
                for repo, commits_list in repo_stats.items()
            },
            'daily_stats': daily_stats
        }
        
        return repo_stats, summary


# ═══════════════════════════════════════════════════════════════
# OUTPUT FORMATTING
# ═══════════════════════════════════════════════════════════════

def print_terminal_summary(repo_stats: Dict, summary: Dict) -> None:
    """Print beautiful terminal summary."""
    print("\n" + "="*70)
    print("📊 PRODUCTIVITY ANALYTICS v7.0")
    print("="*70)
    print(f"📅 Period: {summary['date_range']}")
    print(f"📁 Repositories: {summary['total_repos']}")
    print(f"✨ Total Commits: {summary['total_commits']}")
    print()
    
    # Per-repository breakdown
    print("📦 Repository Breakdown:")
    for repo, count in sorted(summary['repo_breakdown'].items(), key=lambda x: -x[1]):
        repo_short = repo.split('/')[-1]
        print(f"   {repo_short:30s} {count:4d} commits")
    print()
    
    # Daily stats (last 7 days)
    print("📆 Recent Activity (last 7 days):")
    daily = summary['daily_stats']
    sorted_days = sorted(daily.keys(), reverse=True)[:7]
    
    for day in sorted_days:
        stats = daily[day]
        repos_str = ', '.join([r.split('/')[-1] for r in stats['repos']])
        print(f"   {day}: {stats['commits']:2d} commits, {stats['net_lines']:+5d} lines ({repos_str})")
    print()
    
    # TAG distribution
    tag_totals = {}
    for day_stats in daily.values():
        for tag, count in day_stats['tags'].items():
            tag_totals[tag] = tag_totals.get(tag, 0) + count
    
    if tag_totals:
        print("🏷️  TAG Distribution:")
        for tag, count in sorted(tag_totals.items(), key=lambda x: -x[1]):
            config = TagSystem.get_config(tag)
            icon = config.icon if config else '📦'
            weight = config.weight if config else 1.0
            pct = (count / summary['total_commits']) * 100
            print(f"   {icon} {tag:12s} {count:4d} ({pct:5.1f}%) weight: {weight}x")


def create_excel_report_v7(repo_stats: Dict, summary: Dict, output_path: Path) -> None:
    """Create Excel report with multi-repo support."""
    if not HAVE_PANDAS:
        print("⚠️  pandas/openpyxl not available. Skipping Excel export.")
        return
    
    # Summary sheet data
    summary_data = []
    for repo, commits in repo_stats.items():
        total_lines = sum(c['net_lines'] for c in commits)
        total_weighted = sum(c['weight'] for c in commits)
        
        summary_data.append({
            'Repository': repo.split('/')[-1],
            'Commits': len(commits),
            'Weighted Commits': round(total_weighted, 1),
            'Net Lines': total_lines,
            'Additions': sum(c['additions'] for c in commits),
            'Deletions': sum(c['deletions'] for c in commits),
        })
    
    # Daily breakdown
    daily_data = []
    for day, stats in sorted(summary['daily_stats'].items()):
        tag_str = ', '.join([f"{tag}:{count}" for tag, count in stats['tags'].items()])
        
        daily_data.append({
            'Date': day,
            'Commits': stats['commits'],
            'Additions': stats['additions'],
            'Deletions': stats['deletions'],
            'Net Lines': stats['net_lines'],
            'Tags': tag_str,
            'Repositories': len(stats['repos'])
        })
    
    # Per-repo details
    all_commits_data = []
    for repo, commits in repo_stats.items():
        for c in commits:
            all_commits_data.append({
                'Date': c['date'].isoformat(),
                'Repository': repo.split('/')[-1],
                'Author': c['author'],
                'Tag': c['tag'],
                'Message': c['message'][:100],
                'Files': c['files_count'],
                'Lines +': c['additions'],
                'Lines -': c['deletions'],
                'Net': c['net_lines'],
                'Weight': c['weight'],
            })
    
    # Write Excel
    with pd.ExcelWriter(output_path, engine='openpyxl') as writer:
        pd.DataFrame(summary_data).to_excel(writer, sheet_name='Repositories', index=False)
        pd.DataFrame(daily_data).to_excel(writer, sheet_name='Daily', index=False)
        pd.DataFrame(all_commits_data).to_excel(writer, sheet_name='All Commits', index=False)
    
    print(f"✅ Excel created: {output_path}")


# ═══════════════════════════════════════════════════════════════
# CLI & MAIN
# ═══════════════════════════════════════════════════════════════

def parse_args() -> argparse.Namespace:
    """Parse command-line arguments."""
    parser = argparse.ArgumentParser(
        description='EGI Productivity Analytics v7.0 - GitHub API Edition'
    )
    
    parser.add_argument(
        '--config',
        type=Path,
        default=Path('productivity_config.yaml'),
        help='Config file path'
    )
    
    parser.add_argument(
        '--since',
        type=str,
        help='Start date YYYY-MM-DD (default: from config)'
    )
    
    parser.add_argument(
        '--until',
        type=str,
        help='End date YYYY-MM-DD (default: today)'
    )
    
    parser.add_argument(
        '--output',
        type=Path,
        help='Output Excel path (default: productivity_v7_YYYYMMDD.xlsx)'
    )
    
    parser.add_argument(
        '--test-github',
        action='store_true',
        help='Test GitHub connection and exit'
    )
    
    parser.add_argument(
        '--no-cache',
        action='store_true',
        help='Disable GitHub API cache'
    )
    
    return parser.parse_args()


def main() -> int:
    """Main execution."""
    print("🚀 EGI Productivity Analytics v7.0.0")
    print("   GitHub API Multi-Repository Edition")
    print("   TAG System v2.0 • Auto-Categorization • Smart Caching")
    print("="*70)
    
    args = parse_args()
    
    # Load config
    config = load_config(args.config if args.config.exists() else None)
    
    # Get GitHub token
    github_token = os.getenv('GITHUB_TOKEN')
    if not github_token:
        print("\n❌ GitHub token required!")
        print("   Set environment variable: export GITHUB_TOKEN='your_token_here'")
        print("   Or create token at: https://github.com/settings/tokens")
        print("   Required scope: 'repo' (full control of private repositories)")
        return 1
    
    # Initialize GitHub client
    try:
        github_client = GitHubMultiRepoClient(
            token=github_token,
            repositories=config['github']['repositories']
        )
    except Exception as e:
        print(f"\n❌ Failed to initialize GitHub client: {e}")
        return 1
    
    # Test mode
    if args.test_github:
        print("\n🔍 Testing GitHub connection...")
        if github_client.test_connection():
            print("\n✅ GitHub connection successful!")
            print(f"✅ Tracking {len(config['github']['repositories'])} repositories:")
            for repo in config['github']['repositories']:
                print(f"   - {repo}")
            return 0
        else:
            return 1
    
    # Initialize categorizer
    anthropic_key = os.getenv('ANTHROPIC_API_KEY')
    categorizer = CommitCategorizer(llm_api_key=anthropic_key)
    
    # Initialize analyzer
    analyzer = ProductivityAnalyzerV7(github_client, categorizer)
    
    # Parse dates
    if args.since:
        start_date = dt.date.fromisoformat(args.since)
    else:
        start_date = dt.date.fromisoformat(config['analysis']['default_start_date'])
    
    if args.until:
        end_date = dt.date.fromisoformat(args.until)
    else:
        end_date = dt.date.today()
    
    # Output path
    if args.output:
        output_path = args.output
    else:
        filename = f"productivity_v7_{dt.date.today().strftime('%Y%m%d')}.xlsx"
        output_path = Path.cwd() / filename
    
    print(f"\n📊 Analysis Configuration:")
    print(f"   Period: {start_date} → {end_date}")
    print(f"   Repositories: {len(config['github']['repositories'])}")
    print(f"   Cache: {'Disabled' if args.no_cache else 'Enabled'}")
    print(f"   Output: {output_path}")
    print()
    
    # Run analysis
    try:
        print("🔍 Fetching commits from GitHub...")
        repo_stats, summary = analyzer.analyze_period(start_date, end_date)
        
        print(f"✅ Fetched {summary['total_commits']} commits")
        
        # Terminal output
        print_terminal_summary(repo_stats, summary)
        
        # Excel export
        if HAVE_PANDAS:
            print("\n📊 Creating Excel report...")
            create_excel_report_v7(repo_stats, summary, output_path)
            
            if output_path.exists():
                size = output_path.stat().st_size
                print(f"📁 File size: {size:,} bytes")
        else:
            print("\n⚠️  pandas/openpyxl not available. Install with:")
            print("   pip install pandas openpyxl")
        
        print("\n🎉 Analysis complete!")
        return 0
        
    except Exception as e:
        print(f"\n❌ Analysis failed: {e}")
        import traceback
        traceback.print_exc()
        return 1


if __name__ == "__main__":
    sys.exit(main())
