#!/usr/bin/env python3
import requests
from bs4 import BeautifulSoup
import re

url = "https://accessoconcertificato.comune.fi.it/AOL/Affissione/ComuneFi/Page"

print("📥 Scaricamento pagina...")
response = requests.get(url, headers={'User-Agent': 'Mozilla/5.0'})
soup = BeautifulSoup(response.content, 'html.parser')

# Salva HTML
with open('albo_structure_debug.html', 'w', encoding='utf-8') as f:
    f.write(soup.prettify())
print("💾 HTML salvato in: albo_structure_debug.html")

# Cerca pattern "N° registro"
print("\n🔍 Cerca 'N° registro':")
registro_elements = soup.find_all(string=re.compile(r'N°.*registro', re.IGNORECASE))
print(f"   Trovati {len(registro_elements)} elementi")

if registro_elements:
    for i, elem in enumerate(registro_elements[:3], 1):
        print(f"\n📄 Elemento {i}:")
        print(f"   Text: {elem.strip()[:80]}")
        print(f"   Parent: {elem.parent.name if elem.parent else 'None'}")
        if elem.parent:
            print(f"   Parent class: {elem.parent.get('class')}")
            # Risale di 3 livelli
            parent = elem.parent
            for level in range(3):
                if parent:
                    parent = parent.parent
                    if parent:
                        print(f"   Grandparent {level+1}: <{parent.name}> class={parent.get('class')}")

# Cerca anche altri pattern
print("\n🔍 Cerca elementi con 'mobilità':")
mobilita = soup.find_all(string=re.compile(r'mobilit', re.IGNORECASE))
print(f"   Trovati {len(mobilita)} elementi")

# Cerca strutture comuni
print("\n🔍 Strutture HTML:")
print(f"   <article>: {len(soup.find_all('article'))}")
print(f"   <section>: {len(soup.find_all('section'))}")
print(f"   <div class='atto'>: {len(soup.find_all('div', class_=lambda x: x and 'atto' in str(x).lower()))}")
print(f"   <div> totali: {len(soup.find_all('div'))}")

# Cerca il container principale degli atti
main = soup.find('main') or soup.find(id='content') or soup.find(class_='content')
if main:
    print(f"\n📦 Container principale: <{main.name}>")
    direct_children = [c for c in main.children if c.name]
    print(f"   Figli diretti: {len(direct_children)}")
    if direct_children:
        for i, child in enumerate(direct_children[:5], 1):
            print(f"   {i}. <{child.name}> class={child.get('class')} id={child.get('id')}")
