# 🔧 QES Sandbox Environment Template

## 📋 Environment Variables Template

Copy these variables to your `.env` file and fill in the actual values:

```bash
# QES Sandbox Environment Variables

# Namirial Sandbox
NAMIRIAL_ENABLED=true
NAMIRIAL_SANDBOX_URL=https://sandbox.namirial.com/api/v1
NAMIRIAL_CLIENT_ID=your_namirial_client_id
NAMIRIAL_CLIENT_SECRET=your_namirial_client_secret
NAMIRIAL_CERTIFICATE_ID=your_namirial_certificate_id
NAMIRIAL_CERTIFICATE_PATH=storage/certificates/sandbox/namirial_cert.p12
NAMIRIAL_CERTIFICATE_PASSWORD=your_namirial_cert_password

# InfoCert Sandbox
INFOCERT_ENABLED=true
INFOCERT_SANDBOX_URL=https://sandbox.infocert.it/api/v1
INFOCERT_CLIENT_ID=your_infocert_client_id
INFOCERT_CLIENT_SECRET=your_infocert_client_secret
INFOCERT_CERTIFICATE_ID=your_infocert_certificate_id
INFOCERT_CERTIFICATE_PATH=storage/certificates/sandbox/infocert_cert.p12
INFOCERT_CERTIFICATE_PASSWORD=your_infocert_cert_password

# Aruba Sandbox
ARUBA_ENABLED=true
ARUBA_SANDBOX_URL=https://sandbox.aruba.it/api/v1
ARUBA_CLIENT_ID=your_aruba_client_id
ARUBA_CLIENT_SECRET=your_aruba_client_secret
ARUBA_CERTIFICATE_ID=your_aruba_certificate_id
ARUBA_CERTIFICATE_PATH=storage/certificates/sandbox/aruba_cert.p12
ARUBA_CERTIFICATE_PASSWORD=your_aruba_cert_password

# Intesi Group Sandbox
INTESI_ENABLED=true
INTESI_SANDBOX_URL=https://sandbox.intesigroup.com/api/v1
INTESI_CLIENT_ID=your_intesi_client_id
INTESI_CLIENT_SECRET=your_intesi_client_secret
INTESI_CERTIFICATE_ID=your_intesi_certificate_id
INTESI_CERTIFICATE_PATH=storage/certificates/sandbox/intesi_cert.p12
INTESI_CERTIFICATE_PASSWORD=your_intesi_cert_password

# QES Configuration
COA_SIGNATURE_ENABLED=true
COA_SIGNATURE_PROVIDER=mock
COA_SIGNATURE_FALLBACK=mock
COA_SIGNATURE_INSPECTOR_ENABLED=true
COA_SIGNATURE_TSA_ENABLED=true

# Sandbox Mode
APP_ENV=sandbox
APP_DEBUG=true
LOG_LEVEL=debug
```

## 📁 Directory Structure

```
storage/
└── certificates/
    └── sandbox/
        ├── namirial_cert.p12
        ├── infocert_cert.p12
        ├── aruba_cert.p12
        └── intesi_cert.p12
```

## 🔐 Security Notes

-   Set proper file permissions: `chmod 600 storage/certificates/sandbox/*.p12`
-   Never commit certificates to version control
-   Use strong passwords for certificate files
-   Rotate credentials regularly

## 🧪 Testing Commands

```bash
# Test certificate installation
openssl pkcs12 -in storage/certificates/sandbox/namirial_cert.p12 -info -noout

# Test API connectivity
curl -X GET "https://sandbox.namirial.com/api/v1/health" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Test certificate validity
openssl x509 -in storage/certificates/sandbox/namirial_cert.p12 -text -noout
```

## 📊 Progress Tracking

| Provider | Client ID | Client Secret | Certificate | Status      |
| -------- | --------- | ------------- | ----------- | ----------- |
| Namirial | ⏳        | ⏳            | ⏳          | In Progress |
| InfoCert | ⏳        | ⏳            | ⏳          | Pending     |
| Aruba    | ⏳        | ⏳            | ⏳          | Pending     |
| Intesi   | ⏳        | ⏳            | ⏳          | Pending     |

---

**Data creazione**: 26 settembre 2025  
**Versione**: 1.0  
**Stato**: Ready to Use


