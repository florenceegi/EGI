#!/usr/bin/env python3
import requests
from bs4 import BeautifulSoup
from datetime import datetime, timedelta

base_url = "https://accessoconcertificato.comune.fi.it/AOL/Affissione/ComuneFi/Page"

# Prova con parametri di ricerca comuni
params_tests = [
    {},  # Nessun parametro (default)
    {'archived': 'true'},
    {'stato': 'archiviato'},
    {'dataInizio': '01/01/2020', 'dataFine': '31/12/2024'},
    {'DateFrom': '2020-01-01', 'DateTo': '2024-12-31'},
    {'anno': '2024'},
    {'showArchived': '1'},
]

for i, params in enumerate(params_tests, 1):
    print(f"\n🔍 Test {i}: {params}")
    try:
        response = requests.get(base_url, params=params, timeout=10)
        soup = BeautifulSoup(response.content, 'html.parser')
        
        # Conta atti
        cards = soup.find_all('div', class_='card concorso-card multi-line')
        print(f"   ✅ Status: {response.status_code} - Atti: {len(cards)}")
        
        # URL finale
        print(f"   📍 URL: {response.url}")
        
    except Exception as e:
        print(f"   ❌ Errore: {e}")
