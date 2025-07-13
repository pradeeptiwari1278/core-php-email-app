<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once __DIR__ . '/load_env.php';

$mail = new PHPMailer(true);

try {
    $from      = $_POST['from'] ?? '';
    $fromName  = $_POST['from_name'] ?? '';
    $toRaw     = $_POST['to'] ?? '';
    $ccRaw     = $_POST['cc'] ?? '';
    $bccRaw    = $_POST['bcc'] ?? '';
    $subject   = $_POST['subject'] ?? '';
    $message   = trim($_POST['message'] ?? '');

    if (empty($from)) {
        throw new Exception("You must provide sender email address.");
    }

    if (empty($subject)) {
        throw new Exception("You must provide subject.");
    }

    if (empty($toRaw) && empty($ccRaw) && empty($bccRaw)) {
        throw new Exception("You must provide at least one recipient email address (To, CC, or BCC).");
    }

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'shivamtiwari1278@gmail.com';
    $mail->Password   = 'csvxwylybwalxkzx';  // App password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST');
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USERNAME');
    $mail->Password   = getenv('SMTP_PASSWORD');  // App password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = getenv('SMTP_PORT');

    // From Name & Email
    $mail->setFrom(
        !empty($from) ? $from : getenv('SMTP_USERNAME'),
        !empty($fromName) ? $fromName : getenv('SMTP_FROM_NAME')
    );

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
