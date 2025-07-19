<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once __DIR__ . '/load_env.php';

$mail = new PHPMailer(true);

try {
    $toRaw     = $_POST['to'] ?? '';
    $ccRaw     = $_POST['cc'] ?? '';
    $bccRaw    = $_POST['bcc'] ?? '';
    $subject   = $_POST['subject'] ?? '';
    $message   = trim($_POST['message'] ?? '');
    $provider  = $_POST['provider'] ?? 'gmail';

    if (empty($subject)) {
        throw new Exception("You must provide subject.");
    }

    if (empty($toRaw) && empty($ccRaw) && empty($bccRaw)) {
        throw new Exception("You must provide at least one recipient email address (To, CC, or BCC).");
    }

    switch ($provider) {
        case 'gmail':
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USERNAME');
            $mail->Password   = getenv('SMTP_PASSWORD');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = getenv('SMTP_PORT');
            $mail->setFrom(getenv('SMTP_USERNAME'), getenv('SMTP_FROM_NAME'));
            break;

        case 'sendgrid':
            $mail->isSMTP();
            $mail->Host       = getenv('SENDGRID_SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = 'apikey';
            $mail->Password   = getenv('SENDGRID_API_KEY');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = getenv('SENDGRID_SMTP_PORT');
            $mail->setFrom(getenv('SENDGRID_FROM'), getenv('SENDGRID_FROM_NAME'));
            break;
        case 'ses':
            $mail->isSMTP();
            $mail->Host       = getenv('SES_SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SES_SMTP_USERNAME');
            $mail->Password   = getenv('SES_SMTP_PASSWORD');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = getenv('SES_SMTP_PORT');
            $mail->setFrom(getenv('SES_FROM'), getenv('SES_FROM_NAME'));
            break;
        case 'mailgun':
            $mail->isSMTP();
            $mail->Host       = getenv('MAILGUN_SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('MAILGUN_SMTP_USERNAME');
            $mail->Password   = getenv('MAILGUN_SMTP_PASSWORD');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = getenv('MAILGUN_SMTP_PORT');
            $mail->setFrom(getenv('MAILGUN_SMTP_USERNAME'), 'Mailgun Sandbox');
            break;
        default:
            throw new Exception("Unsupported email provider selected.");
    }

    function addRecipients(PHPMailer $mail, string $list, string $type = 'to')
    {
        $entries = array_filter(array_map('trim', explode(',', $list)));

        foreach ($entries as $entry) {
            preg_match('/^(.*?)<(.+@.+\..+)>$/', $entry, $matches);
            $name  = trim($matches[1] ?? '');
            $email = trim($matches[2] ?? $entry);

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if ($type === 'to') $mail->addAddress($email, $name);
                if ($type === 'cc') $mail->addCC($email, $name);
                if ($type === 'bcc') $mail->addBCC($email, $name);
            }
        }
    }

    addRecipients($mail, $toRaw, 'to');
    addRecipients($mail, $ccRaw, 'cc');
    addRecipients($mail, $bccRaw, 'bcc');

    if (!empty($_FILES['attachment']['tmp_name'])) {
        $mail->addAttachment($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
    }

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = !empty($message) ? nl2br($message) : ' ';
    $mail->AltBody = !empty($message) ? strip_tags($message) : ' ';

    $mail->send();
    echo "✅ Email sent successfully.";
} catch (Exception $e) {
    if ($mail->ErrorInfo)
        echo "Error: " . $mail->ErrorInfo;
    else
        echo "Error: " . $e->getMessage();
}
