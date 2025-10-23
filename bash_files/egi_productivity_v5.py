#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
EGI Commit Statistics Exporter v4.0.0
Author: Fabio Cherici (rewrite by Padmin)
License: MIT

Overview
--------
- Parses git logs in a repository for a given date range (default: today).
- Excludes files by glob/prefix patterns (default: node_modules, vendor, storage/testing, .git).
- Computes per-day metrics and a daily summary.
- Cognitive Load v3: log-scaled, normalized, clamped (1.0–3.5x) with components (LI, FM, DP).
- Productivity Index: simple weighted formula with optional scaling factor.
- Optional JSON dedup/rotate utility for storage/testing datasets.
- Outputs: terminal report + CSVs; XLSX if pandas+openpyxl are available.

Usage
-----
python egi_productivity_v4.py --repo . --since 2025-10-23 --until 2025-10-24 \
  --xlsx /home/fabio/EGI/commit_statistics_v4.xlsx \
  --csv-dir /home/fabio/EGI \
  --dedup-root storage/testing/firenze_atti_completi/json --dedup-days 14 --dedup-threshold-mb 20
"""

from __future__ import annotations

import argparse
import csv
import datetime as dt
import math
import os
import re
import subprocess
import sys
import time
from collections import Counter, defaultdict
from dataclasses import dataclass, asdict
from pathlib import Path
from typing import Dict, Iterable, List, Optional, Tuple

# ---- Optional deps for XLSX ----
HAVE_PANDAS = False
try:
    import pandas as pd  # type: ignore
    HAVE_PANDAS = True
except Exception:
    HAVE_PANDAS = False


# -----------------------------
# Config
# -----------------------------

DEFAULT_EXCLUDES = [
    "node_modules/",
    "vendor/",
    "storage/testing/",
    ".git/",
]

TAG_WEIGHTS = {
    "FEAT": 1.0,
    "FIX":  1.5,
    "DOC":  0.8,
    "CHORE":0.6,
    "REFACTOR": 2.0,
    "TEST": 1.2,
    "DEBUG": 1.1,
}

TAG_REGEX = re.compile(r'\[(FEAT|FIX|DOC|CHORE|REFACTOR|TEST|DEBUG)\]')


# -----------------------------
# Dataclasses
# -----------------------------

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
    cognitive_load: float
    cl_components: Dict[str, float]
    productivity_index: float


# -----------------------------
# Helpers
# -----------------------------

def run(cmd: List[str], cwd: Optional[str] = None) -> str:
    p = subprocess.Popen(cmd, stdout=subprocess.PIPE, stderr=subprocess.PIPE, cwd=cwd, text=True)
    out, err = p.communicate()
    if p.returncode != 0:
        raise RuntimeError(f"Command failed: {' '.join(cmd)}\n{err}")
    return out


def parse_git_log(repo: str, since: str, until: str) -> List[CommitEntry]:
    """
    Use git log with --numstat to parse commits.
    """
    fmt = r"%H|%an|%ad|%s"
    args = [
        "git", "log",
        f"--since={since}",
        f"--until={until}",
        f"--pretty=format:{fmt}",
        "--numstat",
        "--date=iso",
    ]
    raw = run(args, cwd=repo)

    commits: List[CommitEntry] = []
    current: Optional[CommitEntry] = None

    for line in raw.splitlines():
        if "|" in line and re.match(r"[0-9a-f]{7,40}\|", line):
            # new commit header
            if current:
                commits.append(current)
            sha, author, date_str, subject = line.split("|", 3)
            date = dt.datetime.fromisoformat(date_str.strip())
            current = CommitEntry(sha=sha, author=author, date=date, message=subject, files=[])
        elif line.strip() == "":
            # separator, ignore
            continue
        else:
            # numstat line: added removed path
            parts = line.split("\t")
            if len(parts) == 3 and current:
                try:
                    a = 0 if parts[0] == "-" else int(parts[0])
                    r = 0 if parts[1] == "-" else int(parts[1])
                    path = parts[2]
                    current.files.append((path, a, r))
                except ValueError:
                    # binary or special
                    path = parts[-1]
                    current.files.append((path, 0, 0))

    if current:
        commits.append(current)
    return commits


def is_excluded(path: str, exclude_patterns: List[str]) -> bool:
    p = path.replace("\\", "/")
    for pat in exclude_patterns:
        if p.startswith(pat):
            return True
        if pat.endswith("*") and p.startswith(pat[:-1]):
            return True
    return False


def tag_from_message(message: str) -> Optional[str]:
    m = TAG_REGEX.search(message.upper())
    return m.group(1) if m else None


def compute_cognitive_components(commits_count: int, files_modified: int, lines_touched: int, tag_stats: Dict[str, int]) -> Dict[str, float]:
    """
    v3 — components normalized [0..1], final cl [1.0..3.5]
    LI: lines impact (log), FM: file spread (log), DP: defect pressure per commit
    """
    # Log-damping: saturate ≈60k lines and ≈200 files
    li = min(1.0, math.log1p(max(0, lines_touched) / 2000.0) / math.log1p(30.0))  # 2k*30 ≈ 60k
    fm = min(1.0, math.log1p(max(0, files_modified) / 10.0) / math.log1p(20.0))   # 10*20 ≈ 200

    fix   = max(0, tag_stats.get("FIX", 0))
    refac = max(0, tag_stats.get("REFACTOR", 0))
    debug = max(0, tag_stats.get("DEBUG", 0))
    commits = max(1, int(commits_count))

    dp_raw = (fix + 1.5*refac + 1.2*debug) / commits
    dp = max(0.0, min(1.0, dp_raw))

    cl = 1.0 + 1.2*li + 0.9*fm + 0.7*dp
    cl = min(cl, 3.5)

    return {"li": round(li, 3), "fm": round(fm, 3), "dp": round(dp, 3), "cl": round(cl, 2)}


def productivity_index(commits_weighted: float, lines_touched: int, files_modified: int, cl: float) -> float:
    """
    Simple PI: combine weighted commits, lines, and files with damping and scale by CL.
    """
    # Damping via logs to avoid explosion
    lw = math.log1p(max(0, lines_touched))    # ~10 for ~22k
    fw = math.log1p(max(0, files_modified))   # ~5 for ~148
    base = commits_weighted * (0.6*lw + 0.4*fw)
    return round(base * cl, 2)


def aggregate_by_day(commits: List[CommitEntry], exclude_patterns: List[str]) -> Dict[dt.date, DayStats]:
    days: Dict[dt.date, Dict] = defaultdict(lambda: {
        "commits": 0,
        "commits_weighted": 0.0,
        "files_set": set(),
        "lines_added": 0,
        "lines_removed": 0,
        "tags": Counter(),
    })

    for c in commits:
        d = c.date.date()
        bucket = days[d]
        bucket["commits"] += 1

        tag = tag_from_message(c.message) or "OTHER"
        if tag in TAG_WEIGHTS:
            bucket["commits_weighted"] += TAG_WEIGHTS[tag]
            bucket["tags"][tag] += 1
        else:
            bucket["commits_weighted"] += 1.0
            bucket["tags"][tag] += 1

        for path, a, r in c.files:
            if is_excluded(path, exclude_patterns):
                continue
            bucket["files_set"].add(path)
            bucket["lines_added"] += a
            bucket["lines_removed"] += r

    result: Dict[dt.date, DayStats] = {}
    for date_key, b in sorted(days.items()):
        files_mod = len(b["files_set"])
        lines_added = b["lines_added"]
        lines_removed = b["lines_removed"]
        lines_touched = lines_added + lines_removed
        lines_net = lines_added - lines_removed
        comp = compute_cognitive_components(b["commits"], files_mod, lines_touched, dict(b["tags"]))
        pi = productivity_index(b["commits_weighted"], lines_touched, files_mod, comp["cl"])
        result[date_key] = DayStats(
            date=date_key,
            commits=b["commits"],
            commits_weighted=round(b["commits_weighted"], 1),
            files_modified=files_mod,
            lines_added=lines_added,
            lines_removed=lines_removed,
            lines_touched=lines_touched,
            lines_net=lines_net,
            tags=dict(b["tags"]),
            cognitive_load=comp["cl"],
            cl_components={"LI": comp["li"], "FM": comp["fm"], "DP": comp["dp"]},
            productivity_index=pi,
        )
    return result


def print_terminal_report(day: DayStats, exclusions_debug: Optional[Dict] = None) -> None:
    print()
    print(" EGI Commit Statistics Exporter v4.0.0")
    print("   Multi-Dimensional Productivity Analytics")
    print("   🔍 Exclusions + Log-Scaled Cognitive Load v3")
    print("="*70)

    if exclusions_debug:
        print("\n🔍 ANALISI ESCLUSIONI FILE...")
        print("\n🔍 DEBUG LINE COUNTING:")
        print(f"   Files processed: {exclusions_debug.get('files_processed', 0)}")
        print(f"   Files skipped: {exclusions_debug.get('files_skipped', 0)}")
        print(f"   Lines skipped: {exclusions_debug.get('lines_skipped', 0)}")
        print(f"   Lines counted: {day.lines_touched}")
        top = exclusions_debug.get('top_excluded', [])
        if top:
            print("\n   Top excluded files (>1000 lines):")
            for item in top[:10]:
                print(f"       {item['lines']:>7,} lines: {item['path']}")

    print("\n" + "="*70)
    print("📅 FLORENCE EGI - PRODUTTIVITÀ GIORNALIERA v4.0.0")
    print("="*70)
    print(f"📅 Data: {day.date.isoformat()}")
    print(f"👤 Autore: fabio cherici\n")
    print("📊 RISULTATI GIORNALIERI")
    print("━"*70)
    print(f"📝 Commit: {day.commits} (weighted: {day.commits_weighted})")
    print(f"📁 File modificati: {day.files_modified}")
    print(f"➕ Righe aggiunte: {day.lines_added:,}")
    print(f"➖ Righe rimosse: {day.lines_removed:,}")
    print(f"🔄 Righe toccate: {day.lines_touched:,} (escluse deps)")
    print(f"🚀 RIGHE NETTE: {day.lines_net:+,}\n")
    print(f"🧠 Cognitive Load: {day.cognitive_load:.2f}x (LI:{day.cl_components['LI']} FM:{day.cl_components['FM']} DP:{day.cl_components['DP']})")
    print(f"⚡ Indice Produttività v4: {day.productivity_index:,.2f}\n")

    print("📊 DISTRIBUZIONE TAG")
    print("━"*70)
    for tag, count in sorted(day.tags.items(), key=lambda x: (-x[1], x[0])):
        if tag in TAG_WEIGHTS:
            print(f"[{tag}]: {count} (peso {TAG_WEIGHTS[tag]}x)")
        else:
            print(f"[{tag}]: {count} (peso 1.0x)")

    print("\n💡 INSIGHTS")
    print("━"*70)
    if day.cognitive_load >= 3.0:
        print("⚠️  Cognitive load elevato (considera break/attività di verifica domani)")
    if day.lines_touched >= 40000:
        print("📈 Volume codice elevato (>=40k righe toccate)")
    if day.tags.get("FIX", 0) >= max(5, day.commits // 4):
        print("⚠️  DEBT REPAYMENT DAY - Alta concentrazione di fix")

    print("\n🎯 RACCOMANDAZIONI")
    print("━"*70)
    print("- Chiudi i fix caldi e tagga la release.")
    print("- Sessione di test/QA leggera per consolidare.")
    print("- Aggiorna il TODO di lunedì con le priorità reali.\n")

    now = dt.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    print("━"*70)
    print(f"🔧 v4.0.0 | {now}")
    print("━"*70)


# -----------------------------
# Dedup/Rotate JSON utility
# -----------------------------

def sha256_file(p: Path, buf=1024*1024) -> str:
    import hashlib
    h = hashlib.sha256()
    with p.open('rb') as f:
        while True:
            chunk = f.read(buf)
            if not chunk: break
            h.update(chunk)
    return h.hexdigest()


def rotate_and_dedup_json(root: str, days: int = 14, big_mb: int = 20) -> Dict[str, float]:
    """
    - Remove duplicate JSON files (same size+sha256).
    - Zip big & old JSONs.
    Returns summary dict.
    """
    rootp = Path(root)
    if not rootp.exists():
        return {"removed_dups": 0, "zipped": 0, "saved_mb": 0.0}

    seen = {}
    now = time.time()
    removed_dups, zipped = 0, 0
    saved_mb = 0.0

    for p in sorted(rootp.rglob("*.json")):
        try:
            size = p.stat().st_size
            sha = sha256_file(p)
            key = (size, sha)
            if key in seen:
                saved_mb += size / (1024*1024)
                p.unlink()
                removed_dups += 1
                continue
            seen[key] = p

            age_days = (now - p.stat().st_mtime) / 86400
            if size > big_mb*1024*1024 and age_days > days:
                zpath = p.with_suffix(p.suffix + ".zip")
                import zipfile
                with zipfile.ZipFile(zpath, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=9) as zf:
                    zf.write(p, arcname=p.name)
                saved_mb += max(0, (size - zpath.stat().st_size)) / (1024*1024)
                p.unlink()
                zipped += 1
        except Exception:
            continue

    return {"removed_dups": removed_dups, "zipped": zipped, "saved_mb": round(saved_mb, 1)}


# -----------------------------
# Exporters
# -----------------------------

def export_csv(day: DayStats, csv_dir: str) -> Tuple[str, str]:
    Path(csv_dir).mkdir(parents=True, exist_ok=True)
    daily_csv = os.path.join(csv_dir, "daily_stats.csv")
    weekly_csv = os.path.join(csv_dir, "weekly_stats.csv")

    # Append or write headers
    daily_headers = list(asdict(day).keys())
    write_header = not os.path.exists(daily_csv)
    with open(daily_csv, "a", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=daily_headers)
        if write_header:
            w.writeheader()
        w.writerow(asdict(day))

    # For weekly, keep it simple: same structure for now
    write_header_w = not os.path.exists(weekly_csv)
    with open(weekly_csv, "a", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=daily_headers)
        if write_header_w:
            w.writeheader()
        w.writerow(asdict(day))

    return daily_csv, weekly_csv


def export_xlsx(day: DayStats, xlsx_path: str) -> Optional[str]:
    if not HAVE_PANDAS:
        return None
    df = pd.DataFrame([asdict(day)])
    Path(os.path.dirname(xlsx_path)).mkdir(parents=True, exist_ok=True)
    try:
        df.to_excel(xlsx_path, index=False)
        return xlsx_path
    except Exception:
        return None


# -----------------------------
# Main
# -----------------------------

def parse_args(argv: Optional[List[str]] = None) -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="EGI Commit Statistics Exporter v4.0.0")
    parser.add_argument("--repo", default=".", help="Path to git repository")
    parser.add_argument("--since", default=dt.date.today().isoformat(), help="Since date (YYYY-MM-DD)")
    parser.add_argument("--until", default=(dt.date.today() + dt.timedelta(days=1)).isoformat(), help="Until date (YYYY-MM-DD)")
    parser.add_argument("--exclude", nargs="*", default=DEFAULT_EXCLUDES, help="Exclude path prefixes")
    parser.add_argument("--csv-dir", default="./egi_stats_out", help="Directory for CSV outputs")
    parser.add_argument("--xlsx", default="", help="Path to XLSX output (requires pandas+openpyxl)")
    parser.add_argument("--dedup-root", default="", help="Root folder for JSON dedup/rotate (optional)")
    parser.add_argument("--dedup-days", type=int, default=14, help="Rotate JSON older than N days")
    parser.add_argument("--dedup-threshold-mb", type=int, default=20, help="Rotate JSON bigger than MB")
    return parser.parse_args(argv)


def main(argv: Optional[List[str]] = None) -> int:
    args = parse_args(argv)

    # Gather commits
    commits = parse_git_log(args.repo, args.since, args.until)

    # Aggregate
    per_day = aggregate_by_day(commits, args.exclude)

    # Pick today's day (or the first available in range)
    day_key = dt.date.fromisoformat(args.since)
    if day_key not in per_day and per_day:
        day_key = sorted(per_day.keys())[-1]
    if day_key not in per_day:
        print("No commits found in the given range.")
        return 0
    day = per_day[day_key]

    # Exclusions debug (lightweight placeholder; real counters should be collected during scan)
    exclusions_debug = {
        "files_processed": 0,
        "files_skipped": 0,
        "lines_skipped": 0,
        "top_excluded": [],
    }

    # Optional dedup/rotate
    if args.dedup_root:
        dd = rotate_and_dedup_json(args.dedup_root, days=args.dedup_days, big_mb=args.dedup_threshold_mb)
        print(f"Dedup/Rotate → removed: {dd['removed_dups']}, zipped: {dd['zipped']}, saved: {dd['saved_mb']} MB")

    # Print report
    print_terminal_report(day, exclusions_debug=exclusions_debug)

    # Export CSV
    daily_csv, weekly_csv = export_csv(day, args.csv_dir)

    # Export XLSX
    xlsx_written = None
    if args.xlsx:
        xlsx_written = export_xlsx(day, args.xlsx)

    # Final summary
    print("📊 Esportazione completata.")
    print(f"📁 CSV giornaliero: {daily_csv}")
    print(f"📁 CSV settimanale: {weekly_csv}")
    if args.xlsx:
        if xlsx_written:
            print(f"📁 XLSX: {xlsx_written}")
        else:
            print("⚠️ XLSX non generato (pandas/openpyxl non disponibili).")

    return 0


if __name__ == "__main__":
    sys.exit(main())
