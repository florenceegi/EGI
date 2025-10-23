#!/usr/bin/env python3
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
import time

print("🚀 AVVIO SELENIUM TEST")
print("=" * 70)

# Setup Chrome in headless mode
chrome_options = Options()
chrome_options.add_argument("--headless=new")
chrome_options.add_argument("--no-sandbox")
chrome_options.add_argument("--disable-dev-shm-usage")
chrome_options.add_argument("--disable-gpu")
chrome_options.add_argument("--window-size=1920,1080")

# Initialize driver
print("📦 Installazione/Verifica ChromeDriver...")
try:
    service = Service(ChromeDriverManager().install())
    driver = webdriver.Chrome(service=service, options=chrome_options)
    
    url = "https://accessoconcertificato.comune.fi.it/AOL/Affissione/ComuneFi/Page"
    print(f"\n🌐 Caricamento pagina: {url}")
    driver.get(url)
    
    # Aspetta caricamento
    print("⏳ Attendo caricamento JavaScript...")
    time.sleep(5)
    
    # Salva HTML dopo JavaScript
    with open('albo_selenium_loaded.html', 'w', encoding='utf-8') as f:
        f.write(driver.page_source)
    print("💾 HTML salvato: albo_selenium_loaded.html")
    
    # Info pagina
    title = driver.title
    print(f"📄 Titolo: {title}")
    
    # Cerca elementi
    print(f"\n🔍 CERCA ELEMENTI DOPO JAVASCRIPT:")
    
    # Tabelle
    tables = driver.find_elements(By.TAG_NAME, "table")
    print(f"   - Tabelle: {len(tables)}")
    
    # Righe
    rows = driver.find_elements(By.TAG_NAME, "tr")
    print(f"   - Righe <tr>: {len(rows)}")
    
    # Link PDF
    links = driver.find_elements(By.TAG_NAME, "a")
    pdf_links = [l for l in links if '.pdf' in l.get_attribute('href') or '']
    print(f"   - Link PDF: {len(pdf_links)}")
    
    # Grid/Container con atti
    grids = driver.find_elements(By.CSS_SELECTOR, "[class*='grid'], [class*='table'], [class*='risultati'], [class*='atti']")
    print(f"   - Grid/Container: {len(grids)}")
    
    # Cerca primo atto
    print(f"\n📋 CERCA PRIMO ATTO:")
    if rows and len(rows) > 1:
        first_row = rows[1]
        cells = first_row.find_elements(By.TAG_NAME, "td")
        if cells:
            print(f"   ✅ Trovata riga con {len(cells)} celle:")
            for i, cell in enumerate(cells[:5], 1):
                text = cell.text.strip()[:60]
                print(f"      Col {i}: {text}")
        else:
            print(f"   ❌ Nessuna cella TD trovata")
    
    print(f"\n✅ TEST COMPLETATO!")
    driver.quit()
    
except Exception as e:
    print(f"❌ ERRORE: {e}")
    import traceback
    traceback.print_exc()
