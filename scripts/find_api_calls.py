#!/usr/bin/env python3
from bs4 import BeautifulSoup
import re

with open('albo_real_page.html', 'r', encoding='utf-8') as f:
    html = f.read()

soup = BeautifulSoup(html, 'html.parser')

print("🔍 CERCA API ENDPOINTS")
print("=" * 70)

# Pattern comuni per API
patterns = [
    r'(api[/\w\-\.]*)',
    r'(https?://[^\s\'"]+/api[^\s\'"]*)',
    r'(fetch\([\'"]([^\'"]+)[\'"]\))',
    r'(ajax.*?url[:\s]+[\'"]([^\'"]+)[\'"  ])',
    r'(\$\.get\([\'"]([^\'"]+)[\'"]\))',
    r'(\/AOL\/[^\s\'"]+)',
]

found_urls = set()

for script in soup.find_all('script'):
    script_text = script.string
    if script_text:
        for pattern in patterns:
            matches = re.findall(pattern, script_text, re.IGNORECASE)
            for match in matches:
                if isinstance(match, tuple):
                    for m in match:
                        if m and len(m) > 5:
                            found_urls.add(m)
                else:
                    if match and len(match) > 5:
                        found_urls.add(match)

print(f"\n🎯 URL/ENDPOINTS TROVATI: {len(found_urls)}")
for url in sorted(found_urls)[:20]:  # Primi 20
    print(f"   → {url}")

# Cerca anche nello script inline
print(f"\n🔎 CERCA 'affissione' o 'atti' negli script:")
for i, script in enumerate(soup.find_all('script'), 1):
    if script.string:
        text = script.string.lower()
        if 'affissione' in text or 'ricerca' in text or '/aol/' in text:
            print(f"\n   📜 Script #{i} contiene keyword!")
            # Estrai snippet
            lines = script.string.split('\n')
            for line in lines:
                if 'affissione' in line.lower() or 'ricerca' in line.lower() or '/aol/' in line.lower():
                    print(f"      {line.strip()[:100]}")
