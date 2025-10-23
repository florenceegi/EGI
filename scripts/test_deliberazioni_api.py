#!/usr/bin/env python3
import requests
import json

# Provo diverse varianti API
base_urls = [
    "https://accessoconcertificato.comune.fi.it/trasparenza-atti/api/searchAtti",
    "https://accessoconcertificato.comune.fi.it/api/searchAtti",
    "https://accessoconcertificato.comune.fi.it/trasparenza-atti/searchAtti",
]

# Parametri per anno 2022
params = {
    'anno': '2022',
    'tipo': 'DG',  # Deliberazioni di Giunta
}

for url in base_urls:
    print(f"\n🔍 Test: {url}")
    try:
        # Prova GET
        response = requests.get(url, params=params, timeout=10)
        print(f"   GET - Status: {response.status_code}")
        if response.status_code == 200:
            try:
                data = response.json()
                print(f"   ✅ JSON ricevuto: {len(data)} items" if isinstance(data, list) else f"   ✅ JSON: {list(data.keys())[:5]}")
                print(f"   📄 Primi 200 char: {str(data)[:200]}")
            except:
                print(f"   ⚠️ Non è JSON: {response.text[:100]}")
    except Exception as e:
        print(f"   ❌ Errore: {str(e)[:80]}")
    
    try:
        # Prova POST
        response = requests.post(url, json=params, timeout=10)
        print(f"   POST - Status: {response.status_code}")
        if response.status_code == 200:
            try:
                data = response.json()
                print(f"   ✅ JSON ricevuto!")
            except:
                pass
    except:
        pass
