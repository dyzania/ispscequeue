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
            $mail->Subject = 'Your Turn! Ticket #' . $ticketNumber;
            
            $mail->Body    = "
                <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; rounded: 10px;'>
                    <h2 style='color: #15803d;'>It's Your Turn!</h2>
                    <p>Hello $fullName,</p>
                    <p>Your ticket <strong>#$ticketNumber</strong> is now being called at:</p>
                    <h3 style='color: #15803d; background: #f0fdf4; padding: 15px; border-radius: 8px; text-align: center;'>$windowName</h3>
                    <p>Please proceed to the counter immediately. Thank you for waiting.</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #666;'>Automatic notification from " . APP_NAME . ".</p>
                </div>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Calling Notification Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendTicketCompleted($toEmail, $fullName, $ticketNumber, $staffNotes = null, $waitTime = null) {
        $mail = $this->getMailer();
        if (!$mail) return false;

        try {
            $mail->addAddress($toEmail, $fullName);
            $mail->isHTML(true);
            $mail->Subject = 'Transaction Completed - ' . $ticketNumber;
            
            $notesHtml = $staffNotes ? "
                <div style='margin-top: 20px; padding: 15px; background-color: #f8fafc; border-left: 4px solid #15803d; border-radius: 4px;'>
                    <p style='margin: 0; font-size: 14px; color: #475569; font-weight: bold;'>Message from Counter:</p>
                    <p style='margin: 5px 0 0; color: #1e293b;'>$staffNotes</p>
                </div>
            " : "";
            
            $timeHtml = $waitTime ? "
                <div style='margin-top: 15px; padding: 10px; background-color: #f0fdf4; border-radius: 8px; text-align: center;'>
                    <p style='margin: 0; font-size: 13px; color: #15803d;'>Total Wait Time</p>
                    <p style='margin: 0; font-size: 18px; font-weight: bold; color: #166534;'>$waitTime</p>
                </div>
            " : "";

            $mail->Body    = "
                <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                    <h2 style='color: #15803d;'>Transaction Completed</h2>
                    <p>Hello $fullName,</p>
                    <p>Your transaction for ticket <strong>#$ticketNumber</strong> has been successfully completed.</p>
                    $timeHtml
                    $notesHtml
                    <p style='margin-top: 20px;'>We value your feedback! Please tell us how we did by rating your experience on our portal.</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #666;'>Thank you for using " . APP_NAME . ".</p>
                </div>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Completion Notification Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
