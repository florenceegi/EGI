#!/usr/bin/env python3
from requests_html import HTMLSession
import time

print("🚀 TEST CON REQUESTS-HTML")
print("=" * 70)

url = "https://accessoconcertificato.comune.fi.it/AOL/Affissione/ComuneFi/Page"

try:
    session = HTMLSession()
    print(f"🌐 Caricamento: {url}")
    
    r = session.get(url, timeout=15)
    print(f"✅ Status: {r.status_code}")
    
    print("\n⏳ Rendering JavaScript (può richiedere 1-2 minuti al primo avvio)...")
    print("   (Scarica Chromium la prima volta)")
    
    # Render JavaScript
    r.html.render(timeout=30, sleep=3)
    
    print("✅ JavaScript eseguito!")
    
    # Salva HTML
    with open('albo_rendered.html', 'w', encoding='utf-8') as f:
        f.write(r.html.html)
    print("💾 Salvato: albo_rendered.html")
    
    # Analisi
    tables = r.html.find('table')
    rows = r.html.find('tr')
    pdf_links = r.html.find('a[href*=".pdf"]')
    
    print(f"\n📊 RISULTATI:")
    print(f"   - Tabelle: {len(tables)}")
    print(f"   - Righe: {len(rows)}")
    print(f"   - PDF: {len(pdf_links)}")
    
    if rows:
        print(f"\n📋 PRIMA RIGA:")
        first_row = rows[0 if len(rows) == 1 else 1]
        cells = first_row.find('td')
        for i, cell in enumerate(cells[:5], 1):
            print(f"   Col {i}: {cell.text[:50]}")
    
    session.close()
    print(f"\n✅ COMPLETATO!")
    
except Exception as e:
    print(f"❌ ERRORE: {e}")
    import traceback
    traceback.print_exc()
