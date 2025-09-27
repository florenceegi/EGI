# 🚀 QES Sandbox Registration Guide

## 📋 OVERVIEW

Guida dettagliata per la registrazione agli ambienti sandbox dei 4 provider QES selezionati.

---

## 🎯 PROVIDER TARGET

### **1. Namirial**

-   **Website**: https://www.namirial.com
-   **Sandbox**: https://sandbox.namirial.com
-   **API Docs**: https://docs.namirial.com
-   **Support**: support@namirial.com

### **2. InfoCert**

-   **Website**: https://www.infocert.it
-   **Sandbox**: https://sandbox.infocert.it
-   **API Docs**: https://knowledgecenter.infocert.digital
-   **Support**: support@infocert.it

### **3. Aruba**

-   **Website**: https://www.aruba.it
-   **Sandbox**: https://sandbox.aruba.it
-   **API Docs**: https://docs.aruba.it
-   **Support**: support@aruba.it

### **4. Intesi Group**

-   **Website**: https://www.intesigroup.com
-   **Sandbox**: https://sandbox.intesigroup.com
-   **API Docs**: https://docs.intesigroup.com
-   **Support**: support@intesigroup.com

---

## 📝 REGISTRATION CHECKLIST

### **Namirial Sandbox Registration**

-   [ ] **Account Creation**

    -   [ ] Visit https://sandbox.namirial.com
    -   [ ] Click "Register" or "Sign Up"
    -   [ ] Fill registration form with company details
    -   [ ] Verify email address
    -   [ ] Complete profile setup

-   [ ] **API Credentials**

    -   [ ] Access Developer Portal
    -   [ ] Create new application
    -   [ ] Generate API key
    -   [ ] Generate client secret
    -   [ ] Download certificate

-   [ ] **Certificate Download**

    -   [ ] Download sandbox certificate
    -   [ ] Install certificate in system
    -   [ ] Verify certificate installation
    -   [ ] Test certificate validity

-   [ ] **Documentation Study**
    -   [ ] Read API documentation
    -   [ ] Study authentication flow
    -   [ ] Review PAdES implementation
    -   [ ] Check rate limits and quotas

### **InfoCert Sandbox Registration**

-   [ ] **Account Creation**

    -   [ ] Visit https://sandbox.infocert.it
    -   [ ] Click "Register" or "Sign Up"
    -   [ ] Fill registration form with company details
    -   [ ] Verify email address
    -   [ ] Complete profile setup

-   [ ] **API Credentials**

    -   [ ] Access Developer Portal
    -   [ ] Create new application
    -   [ ] Generate API key
    -   [ ] Generate client secret
    -   [ ] Download certificate

-   [ ] **Certificate Download**

    -   [ ] Download sandbox certificate
    -   [ ] Install certificate in system
    -   [ ] Verify certificate installation
    -   [ ] Test certificate validity

-   [ ] **Documentation Study**
    -   [ ] Read API documentation
    -   [ ] Study authentication flow
    -   [ ] Review PAdES implementation
    -   [ ] Check rate limits and quotas

### **Aruba Sandbox Registration**

-   [ ] **Account Creation**

    -   [ ] Visit https://sandbox.aruba.it
    -   [ ] Click "Register" or "Sign Up"
    -   [ ] Fill registration form with company details
    -   [ ] Verify email address
    -   [ ] Complete profile setup

-   [ ] **API Credentials**

    -   [ ] Access Developer Portal
    -   [ ] Create new application
    -   [ ] Generate API key
    -   [ ] Generate client secret
    -   [ ] Download certificate

-   [ ] **Certificate Download**

    -   [ ] Download sandbox certificate
    -   [ ] Install certificate in system
    -   [ ] Verify certificate installation
    -   [ ] Test certificate validity

-   [ ] **Documentation Study**
    -   [ ] Read API documentation
    -   [ ] Study authentication flow
    -   [ ] Review PAdES implementation
    -   [ ] Check rate limits and quotas

### **Intesi Group Sandbox Registration**

-   [ ] **Account Creation**

    -   [ ] Visit https://sandbox.intesigroup.com
    -   [ ] Click "Register" or "Sign Up"
    -   [ ] Fill registration form with company details
    -   [ ] Verify email address
    -   [ ] Complete profile setup

-   [ ] **API Credentials**

    -   [ ] Access Developer Portal
    -   [ ] Create new application
    -   [ ] Generate API key
    -   [ ] Generate client secret
    -   [ ] Download certificate

-   [ ] **Certificate Download**

    -   [ ] Download sandbox certificate
    -   [ ] Install certificate in system
    -   [ ] Verify certificate installation
    -   [ ] Test certificate validity

-   [ ] **Documentation Study**
    -   [ ] Read API documentation
    -   [ ] Study authentication flow
    -   [ ] Review PAdES implementation
    -   [ ] Check rate limits and quotas

---

## 🔧 ENVIRONMENT SETUP

### **Environment Variables (.env)**

```bash
# Namirial Sandbox
NAMIRIAL_ENABLED=true
NAMIRIAL_SANDBOX_URL=https://sandbox.namirial.com/api/v1
NAMIRIAL_CLIENT_ID=your_client_id
NAMIRIAL_CLIENT_SECRET=your_client_secret
NAMIRIAL_CERTIFICATE_ID=your_certificate_id
NAMIRIAL_CERTIFICATE_PATH=/path/to/namirial_cert.p12
NAMIRIAL_CERTIFICATE_PASSWORD=your_cert_password

# InfoCert Sandbox
INFOCERT_ENABLED=true
INFOCERT_SANDBOX_URL=https://sandbox.infocert.it/api/v1
INFOCERT_CLIENT_ID=your_client_id
INFOCERT_CLIENT_SECRET=your_client_secret
INFOCERT_CERTIFICATE_ID=your_certificate_id
INFOCERT_CERTIFICATE_PATH=/path/to/infocert_cert.p12
INFOCERT_CERTIFICATE_PASSWORD=your_cert_password

# Aruba Sandbox
ARUBA_ENABLED=true
ARUBA_SANDBOX_URL=https://sandbox.aruba.it/api/v1
ARUBA_CLIENT_ID=your_client_id
ARUBA_CLIENT_SECRET=your_client_secret
ARUBA_CERTIFICATE_ID=your_certificate_id
ARUBA_CERTIFICATE_PATH=/path/to/aruba_cert.p12
ARUBA_CERTIFICATE_PASSWORD=your_cert_password

# Intesi Group Sandbox
INTESI_ENABLED=true
INTESI_SANDBOX_URL=https://sandbox.intesigroup.com/api/v1
INTESI_CLIENT_ID=your_client_id
INTESI_CLIENT_SECRET=your_client_secret
INTESI_CERTIFICATE_ID=your_certificate_id
INTESI_CERTIFICATE_PATH=/path/to/intesi_cert.p12
INTESI_CERTIFICATE_PASSWORD=your_cert_password
```

### **Certificate Installation**

```bash
# Create certificates directory
mkdir -p storage/certificates/sandbox

# Install certificates (example for Namirial)
# Download certificate from sandbox portal
# Place in storage/certificates/sandbox/namirial_cert.p12
# Set proper permissions
chmod 600 storage/certificates/sandbox/*.p12
```

### **Network Configuration**

```bash
# Firewall rules for sandbox access
# Allow outbound HTTPS to sandbox domains
# Port 443 for API communication
# Port 80 for certificate validation
```

---

## 📚 DOCUMENTATION LINKS

### **Namirial**

-   [API Documentation](https://docs.namirial.com)
-   [PAdES Implementation](https://docs.namirial.com/pades)
-   [Authentication Guide](https://docs.namirial.com/auth)
-   [Rate Limits](https://docs.namirial.com/limits)

### **InfoCert**

-   [API Documentation](https://knowledgecenter.infocert.digital)
-   [PAdES Implementation](https://knowledgecenter.infocert.digital/pades)
-   [Authentication Guide](https://knowledgecenter.infocert.digital/auth)
-   [Rate Limits](https://knowledgecenter.infocert.digital/limits)

### **Aruba**

-   [API Documentation](https://docs.aruba.it)
-   [PAdES Implementation](https://docs.aruba.it/pades)
-   [Authentication Guide](https://docs.aruba.it/auth)
-   [Rate Limits](https://docs.aruba.it/limits)

### **Intesi Group**

-   [API Documentation](https://docs.intesigroup.com)
-   [PAdES Implementation](https://docs.intesigroup.com/pades)
-   [Authentication Guide](https://docs.intesigroup.com/auth)
-   [Rate Limits](https://docs.intesigroup.com/limits)

---

## 🧪 TESTING CHECKLIST

### **Basic Connectivity Test**

-   [ ] **Namirial**: Test API endpoint accessibility
-   [ ] **InfoCert**: Test API endpoint accessibility
-   [ ] **Aruba**: Test API endpoint accessibility
-   [ ] **Intesi**: Test API endpoint accessibility

### **Authentication Test**

-   [ ] **Namirial**: Test OAuth2 flow
-   [ ] **InfoCert**: Test OAuth2 flow
-   [ ] **Aruba**: Test OAuth2 flow
-   [ ] **Intesi**: Test OAuth2 flow

### **Certificate Test**

-   [ ] **Namirial**: Test certificate validity
-   [ ] **InfoCert**: Test certificate validity
-   [ ] **Aruba**: Test certificate validity
-   [ ] **Intesi**: Test certificate validity

### **API Functionality Test**

-   [ ] **Namirial**: Test basic API calls
-   [ ] **InfoCert**: Test basic API calls
-   [ ] **Aruba**: Test basic API calls
-   [ ] **Intesi**: Test basic API calls

---

## 📊 PROGRESS TRACKING

### **Registration Status**

| Provider | Account | API Credentials | Certificate | Documentation | Status      |
| -------- | ------- | --------------- | ----------- | ------------- | ----------- |
| Namirial | ⏳      | ⏳              | ⏳          | ⏳            | In Progress |
| InfoCert | ⏳      | ⏳              | ⏳          | ⏳            | In Progress |
| Aruba    | ⏳      | ⏳              | ⏳          | ⏳            | In Progress |
| Intesi   | ⏳      | ⏳              | ⏳          | ⏳            | In Progress |

### **Testing Status**

| Provider | Connectivity | Authentication | Certificate | API | Status  |
| -------- | ------------ | -------------- | ----------- | --- | ------- |
| Namirial | ⏳           | ⏳             | ⏳          | ⏳  | Pending |
| InfoCert | ⏳           | ⏳             | ⏳          | ⏳  | Pending |
| Aruba    | ⏳           | ⏳             | ⏳          | ⏳  | Pending |
| Intesi   | ⏳           | ⏳             | ⏳          | ⏳  | Pending |

---

## 🚨 TROUBLESHOOTING

### **Common Issues**

1. **Certificate Installation**

    - Verify certificate format (P12/PFX)
    - Check certificate password
    - Ensure proper file permissions

2. **API Authentication**

    - Verify client ID and secret
    - Check OAuth2 flow implementation
    - Validate token expiration

3. **Network Connectivity**

    - Check firewall rules
    - Verify DNS resolution
    - Test SSL/TLS connectivity

4. **Rate Limiting**
    - Monitor API usage
    - Implement proper retry logic
    - Check quota limits

### **Support Contacts**

-   **Namirial**: support@namirial.com
-   **InfoCert**: support@infocert.it
-   **Aruba**: support@aruba.it
-   **Intesi**: support@intesigroup.com

---

## 📅 TIMELINE

### **Week 1: Registration**

-   **Day 1-2**: Namirial and InfoCert registration
-   **Day 3-4**: Aruba and Intesi registration
-   **Day 5**: Environment setup and testing

### **Week 2: Implementation**

-   **Day 1-2**: Namirial adapter implementation
-   **Day 3-4**: InfoCert adapter implementation
-   **Day 5**: Aruba adapter implementation

### **Week 3: Completion**

-   **Day 1-2**: Intesi adapter implementation
-   **Day 3-4**: Factory pattern and integration
-   **Day 5**: Testing and validation

---

**Data creazione**: 26 settembre 2025  
**Versione**: 1.0  
**Stato**: Ready to Start  
**Priorità**: CRITICA - Demo Investitori


