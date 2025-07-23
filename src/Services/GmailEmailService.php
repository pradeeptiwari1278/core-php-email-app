<?php

namespace Services;

use Contracts\EmailServiceContract;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Utils\EnvLoader;
use Utils\EmailHelper;
use Utils\Logger;

class GmailEmailService implements EmailServiceContract
{
    public function send(array $data): bool
    {
        Logger::log("Inside GmailEmailService");
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = EnvLoader::get('SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = EnvLoader::get('SMTP_USERNAME');
            $mail->Password   = EnvLoader::get('SMTP_PASSWORD');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = EnvLoader::get('SMTP_PORT');

            $mail->setFrom(EnvLoader::get('SMTP_USERNAME'), EnvLoader::get('SMTP_FROM_NAME'));

            EmailHelper::addRecipients($mail, $data['to'], 'to');
            EmailHelper::addRecipients($mail, $data['cc'], 'cc');
            EmailHelper::addRecipients($mail, $data['bcc'], 'bcc');

            if (!empty($data['attachment'])) {
                $mail->addAttachment($data['attachment']['tmp_name'], $data['attachment']['name']);
            }

            $mail->isHTML(true);
            $mail->Subject = $data['subject'];
            $mail->Body    = nl2br($data['message']);
            $mail->AltBody = strip_tags($data['message']);

            return $mail->send();
        } catch (Exception $e) {
            $errorMsg = !empty($mail->ErrorInfo) ? $mail->ErrorInfo : $e->getMessage();
            Logger::log("GmailEmailService Error: " . $errorMsg);
            echo "❌ Error: " . $errorMsg;
            return false;
        }
    }
}
