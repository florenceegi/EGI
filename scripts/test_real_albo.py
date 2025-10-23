#!/usr/bin/env python3
import requests
from bs4 import BeautifulSoup
import json

url = "https://accessoconcertificato.comune.fi.it/AOL/Affissione/ComuneFi/Page"

print(f"🔍 Testing URL reale: {url}")
print("=" * 70)

try:
    response = requests.get(url, timeout=15)
    print(f"✅ Status Code: {response.status_code}")
    
    soup = BeautifulSoup(response.content, 'html.parser')
    
    # Salva HTML
    with open('albo_real_page.html', 'w', encoding='utf-8') as f:
        f.write(response.text)
    print(f"💾 HTML salvato in: albo_real_page.html")
    
    # Info base
    title = soup.find('title')
    print(f"📄 Titolo: {title.text.strip() if title else 'N/A'}")
    
    # Cerca elementi con atti
    tables = soup.find_all('table')
    print(f"\n📊 Tabelle trovate: {len(tables)}")
    
    # Cerca grid/dataTable
    grids = soup.find_all(['div', 'table'], class_=lambda x: x and ('grid' in str(x).lower() or 'datatable' in str(x).lower() or 'risultati' in str(x).lower()))
    print(f"🗂️  Grid/DataTable: {len(grids)}")
    
    # Cerca link PDF
    pdf_links = soup.find_all('a', href=lambda x: x and '.pdf' in str(x).lower())
    print(f"📑 Link PDF: {len(pdf_links)}")
    
    # Cerca righe di risultati
    rows = soup.find_all('tr')
    print(f"📝 Righe tabella: {len(rows)}")
    
    if rows and len(rows) > 1:
        print(f"\n📋 ESEMPIO PRIMA RIGA (oltre header):")
        first_row = rows[1] if len(rows) > 1 else rows[0]
        cells = first_row.find_all(['td', 'th'])
        for i, cell in enumerate(cells[:5], 1):  # Prime 5 celle
            print(f"   Col {i}: {cell.text.strip()[:50]}")
    
    # Cerca script con dati JSON
    scripts = soup.find_all('script')
    print(f"\n🔧 Script tags: {len(scripts)}")
    
    print(f"\n✅ PAGINA CARICATA! Analizza 'albo_real_page.html' per dettagli")
    
except Exception as e:
    print(f"❌ Errore: {e}")
    import traceback
    traceback.print_exc()
