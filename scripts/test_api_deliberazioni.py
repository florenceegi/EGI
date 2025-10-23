#!/usr/bin/env python3
import requests
import json

url = "https://accessoconcertificato.comune.fi.it/trasparenza-atti-cat/searchAtti"

headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Accept': 'application/json, text/plain, */*',
    'Accept-Language': 'it-IT,it;q=0.9,en-US;q=0.8,en;q=0.7',
    'Content-Type': 'application/json',
    'Origin': 'https://accessoconcertificato.comune.fi.it',
    'Referer': 'https://accessoconcertificato.comune.fi.it/trasparenza-atti/',
}

payload = {
    "oggetto": "",
    "notLoadIniziale": "ok",
    "numeroAdozione": "",
    "competenza": "DG",
    "annoAdozione": "2022",
    "tipiAtto": ["DG", "DIG"]
}

print("🚀 TEST API DELIBERAZIONI (con headers)")
print("=" * 70)

try:
    response = requests.post(url, json=payload, headers=headers, timeout=30)
    print(f"✅ Status: {response.status_code}")
    
    if response.status_code == 200:
        data = response.json()
        
        if isinstance(data, list):
            print(f"🎉 ATTI TROVATI: {len(data)}")
            
            if data:
                print(f"\n📄 PRIMO ATTO:")
                print(json.dumps(data[0], indent=2, ensure_ascii=False))
                
                # Salva tutto
                with open('deliberazioni_2022.json', 'w', encoding='utf-8') as f:
                    json.dump(data, f, indent=2, ensure_ascii=False)
                print(f"\n💾 Salvato: deliberazioni_2022.json")
        
        elif isinstance(data, dict):
            print(f"📋 Struttura dict con chiavi: {list(data.keys())}")
    
    else:
        print(f"❌ Errore: {response.status_code}")
        print(response.text[:200])

except Exception as e:
    print(f"❌ ERRORE: {e}")
