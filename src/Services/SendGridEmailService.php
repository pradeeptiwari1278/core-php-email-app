<?php

namespace Services;

use Contracts\EmailServiceContract;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Utils\EnvLoader;
use Utils\Logger;
use Utils\EmailHelper;

class SendGridEmailService implements EmailServiceContract
{
    public function send(array $data): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = EnvLoader::get('SENDGRID_SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = 'apikey';
            $mail->Password   = EnvLoader::get('SENDGRID_API_KEY');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = EnvLoader::get('SENDGRID_SMTP_PORT');
            $mail->setFrom(EnvLoader::get('SENDGRID_FROM'), EnvLoader::get('SENDGRID_FROM_NAME'));

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
