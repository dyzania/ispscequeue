<?php
require_once __DIR__ . '/../config/config.php';
require_once MAILER_PATH;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailService {
    private function getMailer() {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            // Recipients
            $mail->setFrom(FROM_EMAIL, FROM_NAME);
            return $mail;
        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
            return null;
        }
    }

    public function sendOTPEmail($toEmail, $fullName, $otpCode, $type = 'verification') {
        $mail = $this->getMailer();
        if (!$mail) return false;

        try {
            $mail->addAddress($toEmail, $fullName);
            $mail->isHTML(true);
            
            $subject = ($type === 'reset') ? 'Password Reset Request' : 'Verify Your Account';
            $actionText = ($type === 'reset') ? 'reset your password' : 'verify your account';
            
            $mail->Subject = "$subject - " . APP_NAME;
            
            $mail->Body    = "
                <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #8b0101;'>$subject</h2>
                    <p>Hello $fullName,</p>
                    <p>Use the following One-Time Password (OTP) to $actionText:</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <span style='background-color: #f3f4f6; color: #8b0101; padding: 15px 30px; font-size: 32px; font-weight: bold; letter-spacing: 5px; border-radius: 10px; border: 2px solid #8b0101;'>$otpCode</span>
                    </div>
                    <p>This code is valid for 15 minutes.</p>
                    <p>If you did not request this, please ignore this email.</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #666;'>Automatic notification from " . APP_NAME . ".</p>
                </div>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log("OTP Email Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendTicketCalled($toEmail, $fullName, $ticketNumber, $windowName) {
        $mail = $this->getMailer();
        if (!$mail) return false;

        try {
            $mail->addAddress($toEmail, $fullName);
            $mail->isHTML(true);
            $mail->Subject = 'Service Notification: Ticket #' . $ticketNumber;
            
            $mail->Body    = "
                <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #1e3a8a;'>Ticket Assignment Notification</h2>
                    <p>Dear $fullName,</p>
                    <p>Please be informed that your ticket <strong>#$ticketNumber</strong> is now being served at:</p>
                    <h3 style='color: #1e3a8a; background: #eff6ff; padding: 15px; border-radius: 8px; text-align: center;'>$windowName</h3>
                    <p>Kindly proceed to the designated counter at your earliest convenience. Thank you for your patience.</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 11px; color: #64748b;'>This is an automated notification from " . APP_NAME . ". Please do not reply to this email.</p>
                </div>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Calling Notification Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendTicketCompleted($toEmail, $fullName, $ticketNumber, $serviceName, $staffNotes = null, $waitTime = null) {
        $mail = $this->getMailer();
        if (!$mail) return false;

        try {
            $mail->addAddress($toEmail, $fullName);
            $mail->isHTML(true);
            $mail->Subject = 'Official Transaction Notice - ' . $ticketNumber;
            
            $notesHtml = $staffNotes ? "
                <div style='margin-top: 20px; padding: 15px; background-color: #f8fafc; border-left: 4px solid #1e3a8a; border-radius: 4px;'>
                    <p style='margin: 0; font-size: 13px; color: #64748b; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em;'>Representative Remarks:</p>
                    <p style='margin: 5px 0 0; color: #1e293b; font-style: italic;'>\"$staffNotes\"</p>
                </div>
            " : "";
            
            $timeHtml = $waitTime ? "
                <div style='margin-top: 15px; padding: 12px; background-color: #f1f5f9; border-radius: 8px; text-align: center; border: 1px solid #e2e8f0;'>
                    <p style='margin: 0; font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: bold;'>Processing Duration</p>
                    <p style='margin: 0; font-size: 16px; font-weight: bold; color: #1e3a8a;'>$waitTime</p>
                </div>
            " : "";

            $mail->Body    = "
                <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 30px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff;'>
                    <h2 style='color: #1e3a8a; margin-top: 0;'>Transaction Completion Notice</h2>
                    <p>Dear $fullName,</p>
                    <p>This is to formally notify you that your request for <strong>$serviceName</strong> associated with Ticket <strong>#$ticketNumber</strong> has been successfully processed and completed.</p>
                    $timeHtml
                    $notesHtml
                    <p style='margin-top: 25px; color: #334155;'>We value your input and would appreciate a moment of your time to evaluate your service experience through our online portal.</p>
                    <hr style='border: 0; border-top: 1px solid #f1f5f9; margin: 25px 0;'>
                    <p style='font-size: 11px; color: #94a3b8; text-align: center;'>This is a system-generated notification from " . APP_NAME . ".<br>Thank you for choosing our services.</p>
                </div>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Completion Notification Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendAnnouncementEmail($toEmail, $fullName, $title, $content) {
        $mail = $this->getMailer();
        if (!$mail) return false;

        try {
            $mail->addAddress($toEmail, $fullName);
            $mail->isHTML(true);
            $mail->Subject = 'New Announcement: ' . $title;
            
            $mail->Body    = "
                <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; background-color: #ffffff;'>
                    <div style='text-align: center; margin-bottom: 20px;'>
                        <h1 style='color: #8b0101; margin: 0; font-size: 24px;'>" . APP_NAME . "</h1>
                        <p style='color: #64748b; font-size: 14px; margin-top: 5px;'>News & Updates</p>
                    </div>
                    
                    <div style='background-color: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;'>
                        <h2 style='color: #1e293b; margin-top: 0; font-size: 20px; border-bottom: 2px solid #8b0101; padding-bottom: 10px;'>$title</h2>
                        <div style='color: #334155; line-height: 1.6; font-size: 16px; margin-top: 20px;'>
                            $content
                        </div>
                    </div>
                    
                    <div style='text-align: center; margin-top: 30px;'>
                        <a href='" . BASE_URL . "/user/announcements.php' style='background-color: #8b0101; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;'>View in News Feed</a>
                    </div>
                    
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 30px 0;'>
                    <p style='font-size: 12px; color: #64748b; text-align: center;'>This is an official announcement from " . APP_NAME . ".<br>You received this because you are a registered member.</p>
                </div>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Announcement Email Error: " . $e->getMessage());
            return false;
        }
    }
    public function sendLockoutWarning($toEmail, $fullName) {
        $mail = $this->getMailer();
        if (!$mail) return false;

        try {
            $mail->addAddress($toEmail, $fullName);
            $mail->isHTML(true);
            $mail->Subject = 'Security Warning - Account Locked Out';
            
            $mail->Body    = "
                <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #8b0101;'>Account Security Warning</h2>
                    <p>Hello $fullName,</p>
                    <p>There have been 5 consecutive failed login attempts on your account. As a security measure, your account has been <strong>locked out for 15 minutes</strong>.</p>
                    <p>If this was not you, we recommend resetting your password immediately once the lockout period ends.</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #666;'>Automatic security notification from " . APP_NAME . ".</p>
                </div>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Lockout Warning Email Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
