<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Service: Critical Alert Service
 * 🎯 Purpose: Multi-channel notifications for critical platform errors
 * 🔔 Channels: Email, SMS, EGI-HUB notifications
 * 🚨 Use Case: Missing Stripe accounts, platform wallets issues, critical failures
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Critical Platform Alerts)
 * @compliance P0-2 Translation Keys
 */
class CriticalAlertService
{
    protected UltraLogManager $logger;

    public function __construct(UltraLogManager $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Send critical alert to all configured channels
     *
     * @param string $errorCode UEM error code
     * @param array $context Error context data
     * @param string $severity 'critical'|'error'|'warning'
     * @return void
     */
    public function sendCriticalAlert(string $errorCode, array $context, string $severity = 'critical'): void
    {
        $this->logger->emergency('Critical Alert Triggered', [
            'error_code' => $errorCode,
            'severity' => $severity,
            'context' => $context,
        ]);

        // 1. Send Email Alert
        $this->sendEmailAlert($errorCode, $context, $severity);

        // 2. Send SMS Alert (if enabled)
        $this->sendSmsAlert($errorCode, $context, $severity);

        // 3. Send EGI-HUB Notification (placeholder for future implementation)
        // $this->sendEgiHubAlert($errorCode, $context, $severity);
    }

    /**
     * Send email alert to admin
     */
    protected function sendEmailAlert(string $errorCode, array $context, string $severity): void
    {
        $adminEmail = config('app.critical_alerts.admin_email');

        if (!$adminEmail) {
            $this->logger->warning('Critical alert email skipped: no admin email configured');
            return;
        }

        try {
            $subject = "🚨 [{$severity}] {$errorCode} - Florence EGI Platform";
            $body = $this->formatEmailBody($errorCode, $context, $severity);

            Mail::raw($body, function ($message) use ($adminEmail, $subject) {
                $message->to($adminEmail)
                    ->subject($subject);
            });

            $this->logger->info('Critical alert email sent', [
                'to' => $adminEmail,
                'error_code' => $errorCode,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send critical alert email', [
                'error' => $e->getMessage(),
                'error_code' => $errorCode,
            ]);
        }
    }

    /**
     * Send SMS alert to admin via AWS SNS (PRODUCTION)
     */
    protected function sendSmsAlert(string $errorCode, array $context, string $severity): void
    {
        if (!config('app.critical_alerts.sms_enabled')) {
            return;
        }

        $adminPhone = config('app.critical_alerts.admin_phone');
        $smsProvider = config('app.critical_alerts.sms_provider');

        if (!$adminPhone) {
            $this->logger->warning('SMS alert skipped: Admin phone not configured', [
                'error_code' => $errorCode,
            ]);
            return;
        }

        $message = $this->formatSmsMessage($errorCode, $context, $severity);

        try {
            // AWS SNS SMS (PRODUCTION)
            if ($smsProvider === 'aws_sns' || empty($smsProvider)) {
                $snsClient = new \Aws\Sns\SnsClient([
                    'version' => config('services.sns.version'),
                    'region' => config('services.sns.region'),
                    'credentials' => [
                        'key' => config('services.sns.key'),
                        'secret' => config('services.sns.secret'),
                    ],
                ]);

                $result = $snsClient->publish([
                    'PhoneNumber' => $adminPhone,
                    'Message' => $message,
                    'MessageAttributes' => [
                        'AWS.SNS.SMS.SMSType' => [
                            'DataType' => 'String',
                            'StringValue' => 'Transactional', // High priority delivery
                        ],
                    ],
                ]);

                $this->logger->info('SMS alert sent successfully via AWS SNS', [
                    'to' => $adminPhone,
                    'message_id' => $result['MessageId'] ?? null,
                    'error_code' => $errorCode,
                    'severity' => $severity,
                ]);
            } else {
                $this->logger->warning('SMS provider not supported', [
                    'provider' => $smsProvider,
                    'supported' => ['aws_sns'],
                ]);
            }
        } catch (\Aws\Exception\AwsException $e) {
            $this->logger->error('AWS SNS SMS failed', [
                'error' => $e->getMessage(),
                'code' => $e->getAwsErrorCode(),
                'to' => $adminPhone,
                'error_code' => $errorCode,
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Failed to send SMS alert', [
                'error' => $e->getMessage(),
                'to' => $adminPhone,
                'error_code' => $errorCode,
            ]);
        }

        // match ($smsProvider) {
        //     'twilio' => $this->sendViaTwilio($adminPhone, $message),
        //     'nexmo' => $this->sendViaNexmo($adminPhone, $message),
        //     default => null,
        // };
    }

    /**
     * Format email body for critical alert
     */
    protected function formatEmailBody(string $errorCode, array $context, string $severity): string
    {
        $timestamp = now()->toDateTimeString();
        $contextJson = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<EMAIL
🚨 CRITICAL PLATFORM ALERT
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Error Code: {$errorCode}
Severity: {$severity}
Timestamp: {$timestamp}
Platform: Florence EGI

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
CONTEXT:
{$contextJson}
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

⚠️  IMMEDIATE ACTION REQUIRED
This is a critical platform error that requires immediate attention.

🔍 Next Steps:
1. Check UEM logs for detailed error trace
2. Verify platform wallet configurations
3. Check Stripe Connect account status
4. Review recent deployment changes

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Florence EGI Platform - Automated Alert System
EMAIL;
    }

    /**
     * Format SMS message with ALL useful info (max 160 chars for standard SMS)
     */
    protected function formatSmsMessage(string $errorCode, array $context, string $severity): string
    {
        $missingCount = $context['missing_count'] ?? 0;
        $missingAccounts = $context['missing_accounts'] ?? [];
        
        // Estrai i nomi dei wallet mancanti
        $walletNames = array_map(fn($a) => $a['platform_role'] ?? 'Unknown', $missingAccounts);
        $walletList = implode(', ', $walletNames);

        return match ($errorCode) {
            'MINT_MISSING_STRIPE_ACCOUNTS',
            'TEST_MINT_MISSING_STRIPE_ACCOUNTS' => "🚨 EGI CRITICO: Stripe mancante su {$walletList}. Mint BLOCCATO. Configura subito stripe_account_id!",
            default => "🚨 EGI [{$severity}]: {$errorCode}. Verifica immediata richiesta.",
        };
    }

    /**
     * Send notification to EGI-HUB (placeholder for future implementation)
     * This would integrate with EGI-HUB notification system when available
     */
    protected function sendEgiHubAlert(string $errorCode, array $context, string $severity): void
    {
        $this->logger->info('EGI-HUB alert ready to send (not yet implemented)', [
            'error_code' => $errorCode,
            'severity' => $severity,
            'context' => $context,
        ]);

        // TODO: Implement EGI-HUB notification integration
        // This could be:
        // - Real-time WebSocket notification to admin dashboard
        // - Database notification record for admin UI
        // - Push notification via Firebase/OneSignal
    }
}
