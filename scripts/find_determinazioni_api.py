#!/usr/bin/env python3
import requests
from bs4 import BeautifulSoup

print("🔍 RICERCA PAGINA DETERMINAZIONI")
print("=" * 70)

# URL base trasparenza
base_urls = [
    "https://accessoconcertificato.comune.fi.it/trasparenza-atti/",
    "https://accessoconcertificato.comune.fi.it/trasparenza-atti-cat/",
]

for base_url in base_urls:
    print(f"\n📍 Controllo: {base_url}")
    try:
        response = requests.get(base_url, timeout=10)
        soup = BeautifulSoup(response.content, 'html.parser')
        
        # Cerca link a "determinazioni"
        links = soup.find_all('a', href=True)
        for link in links:
            text = link.get_text(strip=True).lower()
            href = link.get('href')
            
            if 'determinaz' in text or 'determin' in text:
                full_url = href if href.startswith('http') else base_url + href
                print(f"   ✅ {text[:50]}")
                print(f"      → {full_url}")
        
    except Exception as e:
        print(f"   ❌ Errore: {e}")

# Prova direttamente URL noti
print("\n\n🎯 TEST URL DIRETTI:")
test_urls = [
    "https://accessoconcertificato.comune.fi.it/trasparenza-atti/#/determinazioni",
    "https://accessoconcertificato.comune.fi.it/trasparenza-atti-cat/searchDeterminazioni",
    "https://accessoconcertificato.comune.fi.it/trasparenza-atti/determinazioni",
]

for url in test_urls:
    print(f"\n   🔗 {url}")
    try:
        response = requests.get(url, timeout=10)
        print(f"      Status: {response.status_code}")
        if response.status_code == 200:
            print(f"      ✅ FUNZIONA!")
    except Exception as e:
        print(f"      ❌ {str(e)[:60]}")
