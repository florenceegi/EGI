#!/usr/bin/env python3
import requests
from bs4 import BeautifulSoup

# Test entrambi gli URL
urls = [
    "https://attionline.cittametropolitana.fi.it/albo.aspx",
    "https://www.comune.fi.it/albo-pretorio",
    "https://albopretorionline.comune.fi.it"
]

for url in urls:
    print(f"\n🔍 Testing: {url}")
    print("=" * 60)
    try:
        response = requests.get(url, timeout=10)
        print(f"✅ Status Code: {response.status_code}")
        
        soup = BeautifulSoup(response.content, 'html.parser')
        title = soup.find('title')
        print(f"📄 Titolo: {title.text.strip() if title else 'N/A'}")
        
        # Cerca PDF
        pdf_links = soup.find_all('a', href=lambda x: x and '.pdf' in str(x).lower())
        print(f"📑 Link PDF trovati: {len(pdf_links)}")
        
        # Se questo funziona, salva HTML
        if response.status_code == 200:
            filename = f"albo_debug_{url.split('//')[1].split('/')[0].replace('.', '_')}.html"
            with open(filename, 'w', encoding='utf-8') as f:
                f.write(response.text)
            print(f"💾 HTML salvato in: {filename}")
            print(f"✅ QUESTO URL FUNZIONA!")
            
    except Exception as e:
        print(f"❌ Errore: {str(e)[:100]}")
