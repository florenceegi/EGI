#!/usr/bin/env python3
"""
EGI Productivity Tracker - Filtered Version
Excludes dependency files and package installations from metrics
"""

import subprocess
import re
import sys
from datetime import datetime, timedelta
import pandas as pd

class FilteredProductivityTracker:
    def __init__(self):
        # File patterns to exclude from line counting
        self.excluded_patterns = [
            r'node_modules/.*',
            r'vendor/.*',
            r'\.git/.*',
            r'build/.*',
            r'dist/.*',
            r'public/build/.*',
            r'storage/logs/.*',
            r'.*\.lock$',
            r'.*\.min\.js$',
            r'.*\.min\.css$',
            r'package-lock\.json$',
            r'composer\.lock$',
            r'yarn\.lock$',
            r'.*\.map$',
            r'coverage/.*',
            r'\.nyc_output/.*'
        ]
        
        # Commit message patterns that indicate dependency updates
        self.dependency_commit_patterns = [
            r'npm install',
            r'composer (install|update)',
            r'yarn (install|add)',
            r'update dependencies',
            r'bump.*version',
            r'add.*dependency',
            r'install.*package',
            r'package.*update',
            r'dependency.*update',
            r'node_modules',
            r'vendor.*update',
            r'lock.*update'
        ]
    
    def is_excluded_file(self, filepath):
        """Check if file should be excluded from metrics"""
        for pattern in self.excluded_patterns:
            if re.match(pattern, filepath):
                return True
        return False
    
    def is_dependency_commit(self, commit_message, files_changed):
        """Check if commit is primarily about dependencies"""
        # Check commit message
        message_lower = commit_message.lower()
        for pattern in self.dependency_commit_patterns:
            if re.search(pattern, message_lower):
                return True
        
        # Check if only dependency files were changed
        non_dep_files = [f for f in files_changed if not self.is_excluded_file(f)]
        if len(non_dep_files) == 0 and len(files_changed) > 0:
            return True
            
        # Check if >90% of changes are in dependency files
        if len(files_changed) > 0:
            dep_ratio = (len(files_changed) - len(non_dep_files)) / len(files_changed)
            if dep_ratio > 0.9:
                return True
        
        return False
    
    def get_filtered_git_stats(self, date_from, author):
        """Get git statistics excluding dependency-related changes"""
        # Get all commits for the date range
        date_until = (datetime.strptime(date_from, '%Y-%m-%d') + timedelta(days=1)).strftime('%Y-%m-%d')
        
        cmd = [
            'git', 'log',
            f'--since={date_from} 00:00:00',
            f'--until={date_until} 00:00:00',
            f'--author={author}',
            '--pretty=format:%H|%s',
            '--name-only'
        ]
        
        try:
            result = subprocess.run(cmd, capture_output=True, text=True, check=True)
            if not result.stdout.strip():
                return self.get_empty_stats()
            
            commits_data = self.parse_commit_data(result.stdout)
            return self.calculate_filtered_stats(commits_data, date_from, author)
        
        except subprocess.CalledProcessError:
            return self.get_empty_stats()
    
    def parse_commit_data(self, git_output):
        """Parse git log output into structured data"""
        commits = []
        current_commit = None
        
        for line in git_output.strip().split('\n'):
            if '|' in line:  # Commit hash and message
                if current_commit:
                    commits.append(current_commit)
                hash_msg = line.split('|', 1)
                current_commit = {
                    'hash': hash_msg[0],
                    'message': hash_msg[1] if len(hash_msg) > 1 else '',
                    'files': []
                }
            elif line.strip() and current_commit:  # File name
                current_commit['files'].append(line.strip())
        
        if current_commit:
            commits.append(current_commit)
        
        return commits
    
    def calculate_filtered_stats(self, commits_data, date_from, author):
        """Calculate statistics excluding dependency commits"""
        filtered_commits = []
        total_added = 0
        total_removed = 0
        total_files = set()
        
        for commit in commits_data:
            if not self.is_dependency_commit(commit['message'], commit['files']):
                # Filter out excluded files
                filtered_files = [f for f in commit['files'] if not self.is_excluded_file(f)]
                
                if filtered_files:  # Only include commits with non-excluded files
                    commit['filtered_files'] = filtered_files
                    filtered_commits.append(commit)
                    
                    # Get line changes for this commit (excluding dependency files)
                    added, removed = self.get_commit_line_changes(commit['hash'], filtered_files)
                    total_added += added
                    total_removed += removed
                    total_files.update(filtered_files)
        
        return {
            'commits_count': len(filtered_commits),
            'files_modified': len(total_files),
            'lines_added': total_added,
            'lines_removed': total_removed,
            'lines_net': total_added - total_removed,
            'lines_touched': total_added + total_removed,
            'filtered_commits': filtered_commits
        }
    
    def get_commit_line_changes(self, commit_hash, filtered_files):
        """Get line changes for specific files in a commit"""
        if not filtered_files:
            return 0, 0
        
        cmd = ['git', 'show', '--numstat', commit_hash, '--'] + filtered_files
        
        try:
            result = subprocess.run(cmd, capture_output=True, text=True, check=True)
            added = removed = 0
            
            for line in result.stdout.strip().split('\n'):
                if line.strip():
                    parts = line.split('\t')
                    if len(parts) >= 2 and parts[0].isdigit() and parts[1].isdigit():
                        added += int(parts[0])
                        removed += int(parts[1])
            
            return added, removed
        
        except subprocess.CalledProcessError:
            return 0, 0
    
    def get_empty_stats(self):
        """Return empty statistics structure"""
        return {
            'commits_count': 0,
            'files_modified': 0,
            'lines_added': 0,
            'lines_removed': 0,
            'lines_net': 0,
            'lines_touched': 0,
            'filtered_commits': []
        }
    
    def calculate_productivity_index(self, stats):
        """Calculate productivity index with more realistic scaling"""
        base_score = (
            stats['commits_count'] * 100 +
            stats['files_modified'] * 50 +
            stats['lines_net'] * 2 +
            stats['lines_touched'] * 1
        )
        
        # More reasonable cognitive load calculation
        cognitive_multiplier = 1.0
        if stats['files_modified'] > 50:
            cognitive_multiplier += 0.5
        if stats['commits_count'] > 30:
            cognitive_multiplier += 0.3
        if stats['lines_touched'] > 10000:
            cognitive_multiplier += 0.7
        
        return base_score * cognitive_multiplier
    
    def generate_report(self, date_str, author='fabio cherici'):
        """Generate filtered productivity report"""
        stats = self.get_filtered_git_stats(date_str, author)
        productivity_index = self.calculate_productivity_index(stats)
        
        # Classify day type based on realistic metrics
        if stats['commits_count'] > 25:
            day_type = "🚀 High Output Day"
        elif stats['lines_net'] > 5000:
            day_type = "✨ Feature Building Day"
        elif stats['commits_count'] > 15:
            day_type = "⚡ Active Development Day"
        else:
            day_type = "📝 Standard Work Day"
        
        # Realistic cognitive load (max 5x for extreme days)
        cognitive_load = min(1.0 + (stats['files_modified'] / 20) + (stats['commits_count'] / 15), 5.0)
        
        report = f"""
══════════════════════════════════════════════════════════════════
📅 FLORENCE EGI - PRODUTTIVITÀ GIORNALIERA (FILTERED)
══════════════════════════════════════════════════════════════════
📅 Data: {date_str}
👤 Autore: {author}

📊 RISULTATI GIORNALIERI (EXCLUDING DEPENDENCIES)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📝 Commit: {stats['commits_count']}
📁 File modificati: {stats['files_modified']}
➕ Righe aggiunte: {stats['lines_added']:,}
➖ Righe rimosse: {stats['lines_removed']:,}
🔄 Righe toccate: {stats['lines_touched']:,}
🚀 RIGHE NETTE: {stats['lines_net']:+,}

🎯 TIPO GIORNATA: {day_type}
🧠 Cognitive Load: {cognitive_load:.2f}x
⚡ Indice Produttività (Filtered): {productivity_index:,.1f}

💡 METRICHE PULITE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
✅ Esclusi: node_modules, vendor, lock files, build artifacts
✅ Solo codice applicativo e configurazioni
✅ Metriche cognitive realistiche (max 5x)
✅ Focus su sviluppo effettivo vs setup dependencies
        """
        
        return report

def main():
    if len(sys.argv) > 1:
        date_str = sys.argv[1]
    else:
        date_str = datetime.now().strftime('%Y-%m-%d')
    
    tracker = FilteredProductivityTracker()
    report = tracker.generate_report(date_str)
    print(report)

if __name__ == "__main__":
    main()
