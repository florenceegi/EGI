#!/usr/bin/env python3
from bs4 import BeautifulSoup

with open('albo_debug_www_comune_fi_it.html', 'r', encoding='utf-8') as f:
    soup = BeautifulSoup(f.read(), 'html.parser')

print("🔍 CERCA LINK AGLI ATTI PUBBLICATI")
print("=" * 70)

# Cerca tutti i link
all_links = soup.find_all('a', href=True)
print(f"📌 Link totali nella pagina: {len(all_links)}")

# Filtra link interessanti
keywords = ['pubblica', 'atti', 'ricerca', 'deliber', 'determin', 'ordinanz', 'avviso', 'bandi']

print(f"\n🎯 LINK INTERESSANTI (keywords: {', '.join(keywords)}):")
for link in all_links:
    href = link.get('href', '')
    text = link.text.strip()
    
    # Cerca nei link
    if any(kw in href.lower() for kw in keywords) or any(kw in text.lower() for kw in keywords):
        print(f"\n   📄 {text[:60]}")
        print(f"      → {href}")

# Cerca form di ricerca
forms = soup.find_all('form')
print(f"\n🔎 FORM DI RICERCA: {len(forms)}")
for i, form in enumerate(forms, 1):
    action = form.get('action', '')
    method = form.get('method', 'GET')
    print(f"\n   Form #{i}: {method} → {action}")
    inputs = form.find_all(['input', 'select'])
    print(f"   - Campi: {len(inputs)}")
