#!/usr/bin/env python3
import requests

url = "https://accessoconcertificato.comune.fi.it/trasparenza-atti-cat/searchAtti"

headers = {
    'User-Agent': 'Mozilla/5.0',
    'Content-Type': 'application/json',
}

print("🔍 TEST ANNI STORICI")
print("=" * 70)

# Prova dal 2010 al 2020
for anno in range(2010, 2021):
    payload = {
        "oggetto": "",
        "notLoadIniziale": "ok",
        "numeroAdozione": "",
        "competenza": "DG",
        "annoAdozione": str(anno),
        "tipiAtto": ["DG"]
    }
    
    try:
        response = requests.post(url, json=payload, headers=headers, timeout=15)
        if response.status_code == 200:
            data = response.json()
            if data and len(data) > 0:
                print(f"   ✅ {anno}: {len(data)} atti trovati!")
            else:
                print(f"   ⚪ {anno}: 0 atti")
        else:
            print(f"   ❌ {anno}: Status {response.status_code}")
    except Exception as e:
        print(f"   ❌ {anno}: {str(e)[:40]}")
