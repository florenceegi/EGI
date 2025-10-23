#!/usr/bin/env python3
import requests
from bs4 import BeautifulSoup
import re

print("🔍 RICERCA SEZIONI ATTI DEL COMUNE")
print("=" * 70)

# Prova l'albo pretorio con ricerca vuota
albo_url = "https://accessoconcertificato.comune.fi.it/AOL/Affissione/ComuneFi/Page"

print("\n1️⃣ Test Albo Pretorio (ricerca vuota):")
try:
    response = requests.get(albo_url, timeout=10)
    soup = BeautifulSoup(response.content, 'html.parser')
    
    # Cerca "Pagina X di Y"
    pagination = soup.find(string=re.compile(r'Pagina.*di.*', re.IGNORECASE))
    if pagination:
        print(f"   ✅ Paginazione: {pagination}")
        match = re.search(r'di\s+(\d+)', pagination)
        if match:
            total_pages = int(match.group(1))
            print(f"   📊 Pagine totali: {total_pages}")
            print(f"   📊 Atti stimati: {total_pages * 20}")
    
    # Cerca form di ricerca avanzata
    forms = soup.find_all('form')
    print(f"\n   🔎 Form trovati: {len(forms)}")
    for i, form in enumerate(forms, 1):
        action = form.get('action', '')
        inputs = form.find_all(['input', 'select'])
        if len(inputs) > 2:  # Form significativo
            print(f"      Form #{i}: {len(inputs)} campi - action: {action}")
            # Mostra campi interessanti
            for inp in inputs[:5]:
                name = inp.get('name', '')
                if name and 'data' in name.lower() or 'anno' in name.lower():
                    print(f"         - {name}")
                    
except Exception as e:
    print(f"   ❌ Errore: {e}")

# Prova Amministrazione Trasparente
print("\n\n2️⃣ Test Amministrazione Trasparente:")
trasp_urls = [
    "https://accessoconcertificato.comune.fi.it/trasparenza-atti/",
    "https://www.comune.fi.it/amministrazione-trasparente",
]

for url in trasp_urls:
    print(f"\n   📍 {url}")
    try:
        response = requests.get(url, timeout=10)
        soup = BeautifulSoup(response.content, 'html.parser')
        
        # Cerca link a sezioni atti
        keywords = ['determin', 'ordinanz', 'decret', 'deliber', 'provvediment', 'atti']
        links = soup.find_all('a', href=True)
        
        found = set()
        for link in links:
            text = link.get_text(strip=True).lower()
            href = link.get('href')
            
            for kw in keywords:
                if kw in text and len(text) < 100:
                    found.add((text[:60], href))
        
        if found:
            print(f"   ✅ Trovati {len(found)} link rilevanti:")
            for text, href in list(found)[:10]:
                print(f"      • {text}")
                print(f"        {href[:80]}")
    
    except Exception as e:
        print(f"   ❌ {str(e)[:60]}")

print("\n\n3️⃣ Test API dirette:")
api_tests = [
    ("searchAtti", {"oggetto": "", "annoAdozione": "2022"}),
    ("searchDeterminazioni", {"anno": "2022"}),
    ("search", {"tipo": "DD", "anno": "2022"}),
]

base_url = "https://accessoconcertificato.comune.fi.it/trasparenza-atti-cat/"

for endpoint, payload in api_tests:
    url = base_url + endpoint
    print(f"\n   POST {endpoint}")
    try:
        response = requests.post(
            url,
            json=payload,
            headers={'Content-Type': 'application/json'},
            timeout=10
        )
        print(f"      Status: {response.status_code}")
        if response.status_code == 200:
            try:
                data = response.json()
                if isinstance(data, list):
                    print(f"      ✅ {len(data)} risultati!")
            except:
                pass
    except Exception as e:
        print(f"      ❌ {str(e)[:50]}")
