<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * EmailService using PHPMailer.
 * NOTE: reads SMTP settings from Laravel config (config/mail.php),
 * and does NOT call env() directly (avoids warnings when config is cached).
 */
class EmailService
{
    /**
     * Send verification email (HTML + plain text) using PHPMailer and SMTP.
     *
     * @param string $toEmail
     * @param string $subject
     * @param string $htmlBody
     * @param string|null $plainBody
     * @param string|null $replyTo Email address for Reply-To header
     * @return bool
     */
    public function sendMail(string $toEmail, string $subject, string $htmlBody, ?string $plainBody = null, ?string $replyTo = null): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Always use SMTP mailer config, regardless of mail.default
            $mailerConfig = config('mail.mailers.smtp', []);

            $host = $mailerConfig['host'] ?? null;
            $username = $mailerConfig['username'] ?? null;
            $password = $mailerConfig['password'] ?? null;
            $port = isset($mailerConfig['port']) ? intval($mailerConfig['port']) : 587;

            // Determine encryption based on port or config
            // Check for MAIL_ENCRYPTION env variable first, then derive from port
            $encryption = $mailerConfig['encryption'] ?? null;
            if (!$encryption) {
                // Default encryption based on port
                if ($port == 465) {
                    $encryption = 'ssl';
                } elseif ($port == 587) {
                    $encryption = 'tls';
                } else {
                    $encryption = 'tls'; // default to tls
                }
            }

            // Validate required settings
            if (!$host) {
                Log::error('SMTP host is not configured', [
                    'to' => $toEmail,
                    'config_host' => $host,
                    'config_loaded' => !empty($mailerConfig)
                ]);
                return false;
            }

            if (!$username || !$password) {
                Log::error('SMTP credentials are not configured', [
                    'to' => $toEmail,
                    'has_username' => !empty($username),
                    'has_password' => !empty($password),
                    'host' => $host,
                    'port' => $port,
                    'encryption' => $encryption,
                    'username_set' => isset($mailerConfig['username']),
                    'password_set' => isset($mailerConfig['password'])
                ]);
                return false;
            }

            // Server settings
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->SMTPAuth = true;
            $mail->Username = $username;
            $mail->Password = $password;
            $mail->SMTPSecure = $encryption; // tls or ssl
            $mail->Port = $port;
            
            // Enable verbose debug output in development (set to 0 in production)
            $mail->SMTPDebug = config('app.debug', false) ? 2 : 0;
            $mail->Debugoutput = function($str) {
                Log::debug('PHPMailer: ' . $str);
            };

            // From (use config/mail.from.*)
            $fromAddress = config('mail.from.address', 'no-reply@example.com');
            $fromName = config('mail.from.name', config('app.name', 'App'));
            $mail->setFrom($fromAddress, $fromName);

            // Recipient
            $mail->addAddress($toEmail);

            // Reply-To header (if provided)
            if ($replyTo) {
                $mail->addReplyTo($replyTo);
            }

            // Content
            $mail->isHTML();
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $plainBody ?? strip_tags($htmlBody);

            $mail->send();
            Log::info('Email sent successfully', ['to' => $toEmail, 'subject' => $subject]);
            return true;
        } catch (PHPMailerException $e) {
            Log::error('PHPMailer Exception: ' . $e->getMessage(), [
                'to' => $toEmail,
                'host' => $host ?? 'not set',
                'port' => $port ?? 'not set',
                'encryption' => $encryption ?? 'not set'
            ]);
            return false;
        } catch (Throwable $e) {
            Log::error('PHPMailer error: ' . $e->getMessage(), [
                'to' => $toEmail,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Convenience: render a blade view and send.
     *
     * @param string $toEmail
     * @param string $viewBlade
     * @param array $data
     * @param string|null $subject
     * @param string|null $replyTo Email address for Reply-To header
     * @return bool
     * @throws Throwable
     */
    public function sendView(string $toEmail, string $viewBlade, array $data = [], ?string $subject = null, ?string $replyTo = null): bool
    {
        $html = view($viewBlade, $data)->render();
        $plain = strip_tags($html);
        $subject = $subject ?? ($data['subject'] ?? 'Verification code');
        return $this->sendMail($toEmail, $subject, $html, $plain, $replyTo);
    }
}
