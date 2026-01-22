# EGI Productivity System v7.0 - Setup Guide

## 🚀 Quick Start

### 1. Install Dependencies
```bash
cd /home/fabio/EGI
pip3 install --user PyGithub pandas openpyxl pyyaml
```

### 2. Create GitHub Token
1. Go to: https://github.com/settings/tokens/new
2. Token name: "EGI Productivity Analytics"
3. Select scope: ✅ **repo** (Full control of private repositories)
4. Generate token
5. **Copy the token** (you won't see it again!)

### 3. Set Environment Variable
```bash
# Add to ~/.bashrc for permanent setup
export GITHUB_TOKEN='ghp_your_token_here'

# Or set for single session
export GITHUB_TOKEN='ghp_your_token_here'
```

### 4. Test GitHub Connection
```bash
cd bash_files
python3 egi_productivity_v7.py --test-github
```

Expected output:
```
✅ Connected as: YourGitHubUsername
✅ Rate limit: 5000/5000
✅ Tracking 5 repositories:
   - AutobookNft/EGI
   - AutobookNft/EGI-HUB
   - AutobookNft/EGI-HUB-HOME-REACT
   - AutobookNft/EGI-INFO
   - AutobookNft/NATAN_LOC
```

### 5. Run First Analysis
```bash
# Full report (from 2025-08-19 to today)
python3 egi_productivity_v7.py

# Custom date range
python3 egi_productivity_v7.py --since 2026-01-01 --until 2026-01-20

# Today only
python3 egi_productivity_v7.py --since $(date +%Y-%m-%d) --until $(date +%Y-%m-%d)
```

---

## 📊 Features Overview

### ✅ What's New in v7

| Feature | v6 (Local) | v7 (GitHub API) |
|---------|------------|-----------------|
| **Repository support** | 2 local folders | 5 GitHub repos |
| **TAG system** | 7 tags | 16 tags + 86 aliases |
| **Auto-categorization** | None | Rule-based + LLM |
| **Cross-machine** | ❌ Local only | ✅ Works anywhere |
| **Caching** | None | SQLite |
| **Emoji tags** | ❌ Not supported | ✅ Full support |
| **Typo handling** | ❌ Strict | ✅ Auto-corrects |

### 🏷️ Supported TAG Formats

All of these are recognized:
```
[FEAT] Add payment system          → FEAT
feat: Implement auth                → FEAT
✨ New dashboard                    → FEAT (emoji)
[RAFACTOR] Clean code              → REFACTOR (typo auto-corrected)
WIP on main: 12345                 → WIP (auto-detected)
fix: null pointer                  → FIX
Merge branch 'develop'             → MERGE (auto-detected)
```

---

## 🔧 Configuration

### Default Configuration
File: `productivity_config.yaml`

```yaml
github:
  repositories:
    - AutobookNft/EGI
    - AutobookNft/EGI-HUB
    - AutobookNft/EGI-HUB-HOME-REACT
    - AutobookNft/EGI-INFO
    - AutobookNft/NATAN_LOC

analysis:
  default_start_date: "2025-08-19"
```

### Custom Configuration
```bash
# Use custom config
python3 egi_productivity_v7.py --config /path/to/custom_config.yaml
```

---

## 📈 Output Files

### Excel Report
File: `productivity_v7_YYYYMMDD.xlsx`

**3 Sheets**:
1. **Repositories**: Summary per repo
   - Total commits
   - Weighted commits
   - Net lines
   - Additions/deletions

2. **Daily**: Day-by-day breakdown
   - Commits per day
   - TAG distribution
   - Lines changed

3. **All Commits**: Complete details
   - Repository
   - Author
   - TAG
   - Message
   - File count
   - Lines changed

---

## 🧪 Testing & Validation

### Test TAG Parsing
```bash
cd bash_files
python3 tag_system_v2.py
```

### Test Auto-Categorizer
```bash
python3 auto_categorizer.py --message "feat: Add new feature" --files "app/Service.php"
python3 auto_categorizer.py --message "WIP on main" --files "test.php"
```

### Test GitHub Client
```bash
# Fetch commits from specific repo
python3 github_client.py \
  --repos AutobookNft/EGI \
  --since 2026-01-20 \
  --until 2026-01-20
```

---

## 🐛 Troubleshooting

### "GitHub token required"
**Solution**: Set `GITHUB_TOKEN` environment variable
```bash
export GITHUB_TOKEN='your_token_here'
```

### "Rate limit exceeded"
**Solution**: Wait or use cache
- GitHub free tier: 5,000 requests/hour
- v7 uses cache to avoid re-fetching
- Cache location: `~/.egi_productivity_cache/`

### "PyGithub not installed"
**Solution**: Install dependencies
```bash
pip3 install --user PyGithub
```

### "pandas not available"
**Solution**: Excel export requires pandas
```bash
pip3 install --user pandas openpyxl
```

---

## 🔄 Migration from v6

### Side-by-Side Comparison
```bash
# Run v6 (local)
python3 egi_productivity_v6.py --since 2026-01-01 --until 2026-01-20

# Run v7 (GitHub)
python3 egi_productivity_v7.py --since 2026-01-01 --until 2026-01-20

# Compare files
diff productivity_20260120.xlsx productivity_v7_20260120.xlsx
```

### Deprecation Plan
Once v7 is validated:
1. Archive `egi_productivity_v6.py` as `egi_productivity_v6_legacy.py`
2. Update workflows to use v7
3. Document differences in git commits

---

## 📚 Next Steps

### Optional Enhancements

1. **WakaTime Integration** (automatic time tracking)
   ```bash
   pip install wakatime
   # Add API key to config
   ```

2. **LLM Auto-Categorization** ($0.16 one-time for historical data)
   ```bash
   export ANTHROPIC_API_KEY='your_key_here'
   # Enable in config
   ```

3. **Automated Daily Reports**
   ```bash
   # Add to crontab
   0 18 * * * cd /home/fabio/EGI/bash_files && python3 egi_productivity_v7.py >> /var/log/productivity.log 2>&1
   ```

---

## 🆘 Support

Issues? Check:
1. GitHub token is valid and has 'repo' scope
2. All dependencies installed (`pip3 install -r requirements.txt`)
3. Repositories exist and you have access
4. Internet connection active (for GitHub API)

Still stuck? Check error messages and stack traces.
