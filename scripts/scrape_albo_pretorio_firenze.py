#!/usr/bin/env python3
"""
Albo Pretorio Firenze - Scraper Atti Amministrativi

Script per scaricare atti pubblici dall'albo pretorio del Comune di Firenze
per testing sistema N.A.T.A.N.

LEGAL: Gli albi pretori sono pubblici per legge (D.Lgs. 33/2013 - Trasparenza PA).
Lo scraping di dati pubblici è legale se rispetta rate limiting e robots.txt.

Author: Fabio Cherici
Date: 2025-10-23
"""

import requests
from bs4 import BeautifulSoup
import time
import os
import json
from datetime import datetime
from urllib.parse import urljoin, urlparse
import hashlib

class AlboPretorioFirenzeScraper:
    def __init__(self, output_dir='storage/testing/firenze_atti'):
        """
        Inizializza scraper
        
        Args:
            output_dir: Directory dove salvare gli atti scaricati
        """
        self.base_url = "https://albopretorionline.comune.fi.it"
        self.output_dir = output_dir
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 FlorenceEGI-Research-Bot/1.0'
        })
        
        # Rate limiting (rispettoso)
        self.delay_between_requests = 2  # secondi
        
        # Crea directory output
        os.makedirs(output_dir, exist_ok=True)
        os.makedirs(f"{output_dir}/pdf", exist_ok=True)
        os.makedirs(f"{output_dir}/metadata", exist_ok=True)
        
        # Log file
        self.log_file = f"{output_dir}/scraping_log_{datetime.now().strftime('%Y%m%d_%H%M%S')}.txt"
        
    def log(self, message):
        """Log message to file and console"""
        timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        log_message = f"[{timestamp}] {message}"
        print(log_message)
        with open(self.log_file, 'a') as f:
            f.write(log_message + '\n')
    
    def safe_filename(self, text, max_length=100):
        """Crea filename sicuro da testo"""
        # Rimuovi caratteri non validi
        valid_chars = "-_.() abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
        filename = ''.join(c for c in text if c in valid_chars)
        filename = filename.replace(' ', '_')
        
        # Limita lunghezza
        if len(filename) > max_length:
            filename = filename[:max_length]
        
        return filename or 'atto_senza_nome'
    
    def fetch_atti_list(self, limit=20):
        """
        Recupera lista atti dall'albo pretorio
        
        Args:
            limit: Numero massimo di atti da recuperare
            
        Returns:
            List[dict]: Lista di atti con metadata
        """
        self.log(f"Inizio scraping albo pretorio Firenze (limit: {limit})")
        
        # URL albo pretorio Firenze
        # NOTA: L'URL esatto può variare. Questo è un esempio generico.
        # Potrebbe essere necessario adattarlo alla struttura reale del sito.
        search_url = f"{self.base_url}/albopretorio/ricerca"
        
        atti = []
        
        try:
            self.log(f"Fetching: {search_url}")
            response = self.session.get(search_url, timeout=30)
            response.raise_for_status()
            
            soup = BeautifulSoup(response.content, 'html.parser')
            
            # Trova elementi atti (struttura HTML da adattare alla realtà)
            # Esempio generico - DA ADATTARE DOPO ISPEZIONE SITO REALE
            atti_elements = soup.find_all('div', class_='atto-item')[:limit]
            
            if not atti_elements:
                self.log("ATTENZIONE: Nessun atto trovato. Struttura HTML potrebbe essere cambiata.")
                self.log("Salvando HTML pagina per debug...")
                with open(f"{self.output_dir}/debug_page.html", 'w') as f:
                    f.write(response.text)
                return []
            
            for idx, atto_el in enumerate(atti_elements, 1):
                try:
                    # Estrai metadata (DA ADATTARE)
                    title_el = atto_el.find('h3') or atto_el.find('a', class_='title')
                    title = title_el.text.strip() if title_el else f"Atto {idx}"
                    
                    # Link PDF
                    pdf_link_el = atto_el.find('a', href=lambda x: x and '.pdf' in x.lower())
                    if not pdf_link_el:
                        self.log(f"  Atto {idx}: nessun PDF trovato, skip")
                        continue
                    
                    pdf_url = urljoin(self.base_url, pdf_link_el['href'])
                    
                    # Altri metadata
                    protocol_el = atto_el.find('span', class_='protocol') or atto_el.find(text=lambda x: 'Prot' in str(x))
                    protocol = protocol_el.text.strip() if protocol_el else None
                    
                    date_el = atto_el.find('span', class_='date') or atto_el.find('time')
                    pub_date = date_el.text.strip() if date_el else None
                    
                    doc_type_el = atto_el.find('span', class_='type')
                    doc_type = doc_type_el.text.strip() if doc_type_el else "Atto Generico"
                    
                    atto = {
                        'index': idx,
                        'title': title,
                        'protocol_number': protocol,
                        'publication_date': pub_date,
                        'doc_type': doc_type,
                        'pdf_url': pdf_url,
                        'source_page': search_url,
                        'scraped_at': datetime.now().isoformat()
                    }
                    
                    atti.append(atto)
                    self.log(f"  Atto {idx}/{limit}: {title[:50]}...")
                    
                except Exception as e:
                    self.log(f"  Errore parsing atto {idx}: {e}")
                    continue
                
                # Rate limiting
                time.sleep(0.5)
            
            self.log(f"Trovati {len(atti)} atti con PDF")
            
        except requests.RequestException as e:
            self.log(f"ERRORE fetch lista atti: {e}")
        
        return atti
    
    def download_pdf(self, atto):
        """
        Scarica PDF atto
        
        Args:
            atto: Dict con metadata atto
            
        Returns:
            str: Path file scaricato o None se fallito
        """
        try:
            pdf_url = atto['pdf_url']
            filename = self.safe_filename(atto['title'])
            filepath = f"{self.output_dir}/pdf/{filename}_{atto['index']}.pdf"
            
            self.log(f"  Download PDF: {pdf_url}")
            
            response = self.session.get(pdf_url, timeout=60, stream=True)
            response.raise_for_status()
            
            # Verifica sia effettivamente un PDF
            content_type = response.headers.get('Content-Type', '')
            if 'pdf' not in content_type.lower():
                self.log(f"  ATTENZIONE: Content-Type non è PDF: {content_type}")
            
            # Salva file
            with open(filepath, 'wb') as f:
                for chunk in response.iter_content(chunk_size=8192):
                    f.write(chunk)
            
            # Verifica file non vuoto
            file_size = os.path.getsize(filepath)
            if file_size < 1024:  # < 1KB sospetto
                self.log(f"  ATTENZIONE: File molto piccolo ({file_size} bytes)")
            
            # Calcola hash
            with open(filepath, 'rb') as f:
                file_hash = hashlib.sha256(f.read()).hexdigest()
            
            self.log(f"  Salvato: {filepath} ({file_size} bytes, SHA256: {file_hash[:16]}...)")
            
            # Aggiungi info a atto
            atto['local_path'] = filepath
            atto['file_size'] = file_size
            atto['file_hash'] = file_hash
            
            return filepath
            
        except Exception as e:
            self.log(f"  ERRORE download PDF: {e}")
            return None
    
    def save_metadata(self, atti):
        """Salva metadata JSON"""
        metadata_file = f"{self.output_dir}/metadata/atti_metadata_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
        
        with open(metadata_file, 'w', encoding='utf-8') as f:
            json.dump(atti, f, indent=2, ensure_ascii=False)
        
        self.log(f"Metadata salvati: {metadata_file}")
        
        # Salva anche summary
        summary_file = f"{self.output_dir}/SUMMARY.txt"
        with open(summary_file, 'w', encoding='utf-8') as f:
            f.write(f"Albo Pretorio Firenze - Atti Scaricati\n")
            f.write(f"Data: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
            f.write(f"=" * 60 + "\n\n")
            f.write(f"Totale atti: {len(atti)}\n")
            f.write(f"Atti con PDF: {sum(1 for a in atti if a.get('local_path'))}\n\n")
            
            for atto in atti:
                f.write(f"\nAtto #{atto['index']}: {atto['title']}\n")
                f.write(f"  Protocollo: {atto.get('protocol_number', 'N/A')}\n")
                f.write(f"  Data: {atto.get('publication_date', 'N/A')}\n")
                f.write(f"  Tipo: {atto.get('doc_type', 'N/A')}\n")
                f.write(f"  PDF: {atto.get('local_path', 'NON SCARICATO')}\n")
                if atto.get('file_size'):
                    f.write(f"  Dimensione: {atto['file_size']} bytes\n")
        
        self.log(f"Summary salvato: {summary_file}")
    
    def scrape(self, limit=20):
        """
        Esegue scraping completo
        
        Args:
            limit: Numero atti da scaricare
        """
        self.log("=" * 60)
        self.log("INIZIO SCRAPING ALBO PRETORIO FIRENZE")
        self.log("=" * 60)
        
        # Step 1: Recupera lista atti
        atti = self.fetch_atti_list(limit=limit)
        
        if not atti:
            self.log("ERRORE: Nessun atto trovato. Verifica URL e struttura HTML.")
            self.log("SUGGERIMENTO: Visita manualmente l'albo pretorio e ispeziona HTML.")
            return
        
        # Step 2: Download PDF
        self.log(f"\nDownload {len(atti)} PDF...")
        
        for atto in atti:
            self.log(f"\nAtto {atto['index']}/{len(atti)}: {atto['title'][:60]}...")
            self.download_pdf(atto)
            
            # Rate limiting (rispettoso)
            time.sleep(self.delay_between_requests)
        
        # Step 3: Salva metadata
        self.save_metadata(atti)
        
        # Summary finale
        self.log("\n" + "=" * 60)
        self.log("SCRAPING COMPLETATO")
        self.log("=" * 60)
        self.log(f"Atti trovati: {len(atti)}")
        self.log(f"PDF scaricati: {sum(1 for a in atti if a.get('local_path'))}")
        self.log(f"Directory: {self.output_dir}")
        self.log(f"Log completo: {self.log_file}")


def main():
    """Entry point"""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Scraper Albo Pretorio Comune di Firenze',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Esempi:
  # Scarica 10 atti (default)
  python scrape_albo_pretorio_firenze.py
  
  # Scarica 30 atti
  python scrape_albo_pretorio_firenze.py --limit 30
  
  # Salva in directory custom
  python scrape_albo_pretorio_firenze.py --output /tmp/atti_firenze
        """
    )
    
    parser.add_argument(
        '--limit',
        type=int,
        default=20,
        help='Numero massimo di atti da scaricare (default: 20)'
    )
    
    parser.add_argument(
        '--output',
        type=str,
        default='storage/testing/firenze_atti',
        help='Directory output (default: storage/testing/firenze_atti)'
    )
    
    parser.add_argument(
        '--delay',
        type=float,
        default=2.0,
        help='Delay tra richieste in secondi (default: 2.0 - rispettoso)'
    )
    
    args = parser.parse_args()
    
    # Crea scraper
    scraper = AlboPretorioFirenzeScraper(output_dir=args.output)
    scraper.delay_between_requests = args.delay
    
    # Esegui scraping
    try:
        scraper.scrape(limit=args.limit)
    except KeyboardInterrupt:
        scraper.log("\n\nScraping interrotto dall'utente (Ctrl+C)")
    except Exception as e:
        scraper.log(f"\n\nERRORE CRITICO: {e}")
        import traceback
        scraper.log(traceback.format_exc())


if __name__ == '__main__':
    main()

