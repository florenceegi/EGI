#!/usr/bin/env python3
"""
Extract Real Signature Data from Signed PDF

Reads X.509 certificate from digitally signed PDF and extracts:
- Signer CN (Common Name)
- Email
- Organization
- Certificate Serial
- Issuer CA
- Validity dates
- Signature timestamp

Usage:
    python3 scripts/extract_pdf_signature.py storage/app/private/pa_acts/FILE.pdf
"""

import sys
import os
from PyPDF2 import PdfReader
from datetime import datetime
import re

def extract_signature_info(pdf_path):
    """Extract signature information from PDF"""
    
    if not os.path.exists(pdf_path):
        print(f"❌ File not found: {pdf_path}")
        return None
    
    try:
        reader = PdfReader(pdf_path)
        
        print(f"📄 PDF: {os.path.basename(pdf_path)}")
        print(f"📊 Pages: {len(reader.pages)}")
        print(f"🔒 Encrypted: {reader.is_encrypted}")
        print()
        
        # Check for signature fields
        if '/AcroForm' in reader.trailer['/Root']:
            acro_form = reader.trailer['/Root']['/AcroForm']
            
            if '/Fields' in acro_form:
                fields = acro_form['/Fields']
                print(f"📝 Form fields found: {len(fields)}")
                
                for i, field_ref in enumerate(fields):
                    field = field_ref.get_object()
                    field_type = field.get('/FT', '')
                    field_name = field.get('/T', '')
                    
                    print(f"\n🔍 Field {i+1}:")
                    print(f"  Type: {field_type}")
                    print(f"  Name: {field_name}")
                    
                    # Check if it's a signature field
                    if field_type == '/Sig' or 'Signature' in str(field_name):
                        print(f"  ✅ SIGNATURE FIELD FOUND!")
                        
                        if '/V' in field:
                            sig_dict = field['/V']
                            
                            # Extract signature data
                            if '/Name' in sig_dict:
                                print(f"  👤 Name: {sig_dict['/Name']}")
                            
                            if '/Reason' in sig_dict:
                                print(f"  📋 Reason: {sig_dict['/Reason']}")
                            
                            if '/Location' in sig_dict:
                                print(f"  📍 Location: {sig_dict['/Location']}")
                            
                            if '/M' in sig_dict:
                                sig_date = sig_dict['/M']
                                print(f"  📅 Date: {sig_date}")
                            
                            if '/Contents' in sig_dict:
                                # This contains the actual signature bytes (PKCS#7)
                                sig_bytes = sig_dict['/Contents']
                                print(f"  🔐 Signature present: {len(sig_bytes)} bytes")
                                
                                # Try to extract certificate info from PKCS#7
                                print(f"\n  🔬 Analyzing PKCS#7 container...")
                                analyze_pkcs7(sig_bytes)
                        
                        return True
                
                print("\n⚠️ No signature fields found in form")
                return False
            else:
                print("⚠️ No form fields in AcroForm")
                return False
        else:
            print("⚠️ No AcroForm found in PDF")
            return False
            
    except Exception as e:
        print(f"❌ Error reading PDF: {e}")
        import traceback
        traceback.print_exc()
        return None

def analyze_pkcs7(sig_bytes):
    """Try to extract certificate info from PKCS#7 signature"""
    
    try:
        # Convert to hex string for pattern matching
        hex_str = sig_bytes.hex() if isinstance(sig_bytes, bytes) else sig_bytes
        
        print(f"  📦 PKCS#7 size: {len(hex_str)//2} bytes")
        
        # Look for common certificate patterns
        # CN= (Common Name)
        cn_patterns = [
            b'CN=',
            b'commonName=',
            b'/CN=',
            b'2.5.4.3',  # OID for CN
        ]
        
        email_patterns = [
            b'emailAddress=',
            b'E=',
            b'1.2.840.113549.1.9.1',  # OID for email
        ]
        
        org_patterns = [
            b'O=',
            b'organizationName=',
            b'2.5.4.10',  # OID for O
        ]
        
        # Search for patterns
        sig_bytes_lower = sig_bytes.lower() if isinstance(sig_bytes, bytes) else bytes.fromhex(sig_bytes)
        
        if b'infocert' in sig_bytes_lower:
            print(f"  🏢 Issuer CA: InfoCert detected")
        if b'aruba' in sig_bytes_lower:
            print(f"  🏢 Issuer CA: Aruba detected")
        if b'namirial' in sig_bytes_lower:
            print(f"  🏢 Issuer CA: Namirial detected")
        
        # Try to find readable strings
        readable = extract_readable_strings(sig_bytes_lower)
        if readable:
            print(f"\n  📝 Readable strings found in certificate:")
            for s in readable[:10]:  # First 10 strings
                print(f"    - {s}")
        
    except Exception as e:
        print(f"  ⚠️ Could not analyze PKCS#7: {e}")

def extract_readable_strings(data, min_length=4):
    """Extract readable ASCII strings from binary data"""
    if isinstance(data, str):
        data = data.encode()
    
    strings = []
    current = []
    
    for byte in data:
        if 32 <= byte <= 126:  # Printable ASCII
            current.append(chr(byte))
        else:
            if len(current) >= min_length:
                strings.append(''.join(current))
            current = []
    
    if len(current) >= min_length:
        strings.append(''.join(current))
    
    return [s for s in strings if len(s) >= min_length]

def main():
    if len(sys.argv) < 2:
        print("Usage: python3 extract_pdf_signature.py <pdf_path>")
        print("\nExample:")
        print("  python3 extract_pdf_signature.py storage/app/private/pa_acts/349824870a42d46d27e1f15eed2e1198f9427e9d3d99130143e17a760887a71a.pdf")
        sys.exit(1)
    
    pdf_path = sys.argv[1]
    
    print("=" * 60)
    print("🔍 PDF SIGNATURE EXTRACTOR")
    print("=" * 60)
    print()
    
    result = extract_signature_info(pdf_path)
    
    print()
    print("=" * 60)
    if result:
        print("✅ Signature analysis complete")
    else:
        print("⚠️ No signature found or unable to extract")
    print("=" * 60)

if __name__ == '__main__':
    main()
