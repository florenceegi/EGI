#!/usr/bin/env python3
from bs4 import BeautifulSoup

# Analizza l'HTML salvato
with open('albo_debug_www_comune_fi_it.html', 'r', encoding='utf-8') as f:
    soup = BeautifulSoup(f.read(), 'html.parser')

print("🔍 ANALISI STRUTTURA ALBO PRETORIO COMUNE FIRENZE")
print("=" * 70)

# Cerca elementi comuni per atti
print("\n📋 RICERCA ELEMENTI ATTI:")

# Cerca div con classi comuni
divs_node = soup.find_all('div', class_=lambda x: x and 'node' in str(x).lower())
print(f"   - div con 'node': {len(divs_node)}")

divs_atto = soup.find_all('div', class_=lambda x: x and ('atto' in str(x).lower() or 'documento' in str(x).lower()))
print(f"   - div con 'atto/documento': {len(divs_atto)}")

divs_view = soup.find_all('div', class_=lambda x: x and 'view' in str(x).lower())
print(f"   - div con 'view': {len(divs_view)}")

# Cerca articoli
articles = soup.find_all('article')
print(f"   - tag <article>: {len(articles)}")

# Cerca liste
lists = soup.find_all(['ul', 'ol'], class_=lambda x: x)
print(f"   - liste <ul>/<ol>: {len(lists)}")

# Cerca tabelle
tables = soup.find_all('table')
print(f"   - tabelle: {len(tables)}")

# Cerca link PDF
pdf_links = soup.find_all('a', href=lambda x: x and '.pdf' in str(x).lower())
print(f"\n📄 LINK PDF TROVATI: {len(pdf_links)}")
if pdf_links:
    for i, link in enumerate(pdf_links[:3], 1):
        print(f"\n   PDF #{i}:")
        print(f"   - href: {link.get('href')}")
        print(f"   - text: {link.text.strip()[:60]}")
        print(f"   - parent: {link.parent.name} (class: {link.parent.get('class')})")

# Cerca contenitore principale
main_content = soup.find('main') or soup.find('div', {'id': 'content'}) or soup.find('div', {'class': 'content'})
if main_content:
    print(f"\n🎯 CONTENITORE PRINCIPALE TROVATO: <{main_content.name}> (classes: {main_content.get('class')})")
    
    # Conta figli diretti
    children = [child for child in main_content.children if child.name]
    print(f"   - Figli diretti: {len(children)}")
    if children:
        print(f"   - Primi 3 figli: {[f'<{c.name}>' for c in children[:3]]}")

print("\n💡 SUGGERIMENTO:")
print("   Apri il file 'albo_debug_www_comune_fi_it.html' nel browser")
print("   e ispeziona manualmente la struttura con DevTools (F12)")
