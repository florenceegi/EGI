# KMS Providers Setup Guide

## Overview

FlorenceEGI KmsClient supports multiple enterprise-grade KMS providers for production wallet mnemonic encryption. This guide covers setup for each supported provider.

## Supported Providers

### 1. AWS KMS (recommended for AWS infrastructure)

-   **HSM-backed key storage**
-   **Fine-grained IAM policies**
-   **CloudTrail audit integration**
-   **Multi-region key replication**

### 2. Azure Key Vault (recommended for Azure infrastructure)

-   **Hardware Security Module (HSM) support**
-   **Azure Active Directory integration**
-   **Azure Monitor audit logging**
-   **Private endpoint support**

### 3. HashiCorp Vault (recommended for multi-cloud/on-premise)

-   **Transit secrets engine**
-   **Policy-based access control**
-   **Detailed audit logging**
-   **Encryption as a Service (EaaS)**

### 4. Google Cloud KMS (recommended for GCP infrastructure)

-   **Hardware Security Module (HSM) backing**
-   **IAM-based access control**
-   **Cloud Audit Logs integration**
-   **Automatic key rotation**

## Installation Instructions

### Prerequisites

```bash
# Ensure PHP sodium extension is installed
php -m | grep sodium
```

### AWS KMS Setup

1. **Install AWS SDK:**

```bash
composer require aws/aws-sdk-php
```

2. **Configure AWS credentials:**

```bash
# .env file
KMS_PROVIDER=aws
AWS_KMS_REGION=eu-west-1
AWS_KMS_ACCESS_KEY_ID=your_access_key
AWS_KMS_SECRET_ACCESS_KEY=your_secret_key
AWS_KMS_KEK_KEY_ID=arn:aws:kms:eu-west-1:123456789012:key/12345678-1234-1234-1234-123456789012
```

3. **Create KMS key in AWS:**

```bash
aws kms create-key \
  --description "FlorenceEGI Wallet Mnemonic KEK" \
  --key-usage ENCRYPT_DECRYPT \
  --key-spec SYMMETRIC_DEFAULT
```

### Azure Key Vault Setup

1. **Install Azure SDK:**

```bash
composer require azure/azure-key-vault
```

2. **Configure Azure credentials:**

```bash
# .env file
KMS_PROVIDER=azure
AZURE_TENANT_ID=your_tenant_id
AZURE_CLIENT_ID=your_client_id
AZURE_CLIENT_SECRET=your_client_secret
AZURE_VAULT_URL=https://your-vault.vault.azure.net/
AZURE_KEK_KEY_NAME=florenceegi-wallet-kek
```

3. **Create Key Vault and key:**

```bash
az keyvault create --name your-vault --resource-group your-rg --location westeurope
az keyvault key create --vault-name your-vault --name florenceegi-wallet-kek --kty RSA
```

### HashiCorp Vault Setup

1. **Install Vault SDK:**

```bash
composer require vault/vault-php
```

2. **Configure Vault:**

```bash
# .env file
KMS_PROVIDER=vault
VAULT_SERVER_URL=https://vault.yourcompany.com:8200
VAULT_TOKEN=your_vault_token
VAULT_KEK_KEY_NAME=florenceegi-wallet-kek
```

3. **Enable transit engine and create key:**

```bash
vault auth -method=userpass username=admin
vault secrets enable transit
vault write -f transit/keys/florenceegi-wallet-kek
```

### Google Cloud KMS Setup

1. **Install GCP SDK:**

```bash
composer require google/cloud-kms
```

2. **Configure GCP credentials:**

```bash
# .env file
KMS_PROVIDER=gcp
GCP_PROJECT_ID=your-project-id
GCP_KMS_LOCATION=europe-west1
GCP_KMS_KEY_RING=florenceegi-keyring
GCP_KEK_KEY_NAME=wallet-mnemonic-kek
GCP_SERVICE_ACCOUNT_KEY_PATH=/path/to/service-account.json
```

3. **Create key ring and key:**

```bash
gcloud kms keyrings create florenceegi-keyring --location=europe-west1
gcloud kms keys create wallet-mnemonic-kek \
  --location=europe-west1 \
  --keyring=florenceegi-keyring \
  --purpose=encryption
```

## Configuration Management

### Environment-based Configuration

The KmsClient automatically switches between development (mock) and production modes based on `APP_ENV`:

-   **Development** (`APP_ENV=local`): Uses mock KMS with local KEK
-   **Production** (`APP_ENV=production`): Uses configured KMS provider

### Security Best Practices

1. **Key Rotation:**

    - Implement regular KEK rotation (quarterly recommended)
    - Store rotation schedule in security documentation
    - Test backup/restore procedures

2. **Access Control:**

    - Use principle of least privilege for KMS access
    - Implement service accounts with minimal permissions
    - Regular access review and audit

3. **Monitoring:**

    - Enable KMS access logging
    - Set up alerts for unusual access patterns
    - Monitor key usage statistics

4. **Backup and Recovery:**
    - Document key recovery procedures
    - Test backup restoration regularly
    - Maintain offline key escrow for disaster recovery

## Development Testing

### Mock Mode Testing

```bash
# Test KMS functionality in development
php artisan tinker

$kms = app(App\Services\Security\KmsClient::class);
$encrypted = $kms->secureEncrypt('test wallet mnemonic');
$decrypted = $kms->secureDecrypt($encrypted);
echo $decrypted; // Should output: test wallet mnemonic
```

### Production Readiness Checklist

-   [ ] KMS provider SDK installed
-   [ ] Environment variables configured
-   [ ] KMS keys created and accessible
-   [ ] IAM policies configured
-   [ ] Audit logging enabled
-   [ ] Backup procedures documented
-   [ ] Emergency contact procedures established

## Troubleshooting

### Common Issues

1. **SDK not installed:**

    ```
    Error: Class 'Aws\Kms\KmsClient' not found
    Solution: composer require aws/aws-sdk-php
    ```

2. **Invalid credentials:**

    ```
    Error: KMS provider unavailable
    Solution: Check .env credentials and IAM permissions
    ```

3. **Key not found:**
    ```
    Error: KMS configuration invalid
    Solution: Verify key IDs and existence in KMS provider
    ```

### Support Contacts

-   **Infrastructure Team:** infra@florenceegi.com
-   **Security Team:** security@florenceegi.com
-   **Emergency:** +39 XXX XXX XXXX

## Compliance Notes

### GDPR Compliance

-   All KMS operations are logged via AuditLogService
-   Key access includes user context and purpose
-   Data subject rights include key deletion procedures

### Enterprise Audit Requirements

-   KMS provider audit logs must be retained for 7 years
-   Annual security assessment required
-   Quarterly access review mandatory
-   Incident response plan must include key compromise scenarios
