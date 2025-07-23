<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use Utils\EnvLoader;
use Core\EmailServiceManager;
use Utils\Logger;

EnvLoader::load(__DIR__ . '/.env');

try {
    Logger::log("Inside send.php");
    $to       = $_POST['to'] ?? '';
    $cc       = $_POST['cc'] ?? '';
    $bcc      = $_POST['bcc'] ?? '';
    $subject  = $_POST['subject'] ?? '';
    $message  = trim($_POST['message'] ?? '');
    $provider = $_POST['provider'] ?? getenv('EMAIL_PROVIDER');
    $file     = $_FILES['attachment'] ?? null;

    if (empty($subject)) throw new Exception("Subject is required.");
    if (empty($message)) throw new Exception("Message is required.");
    if (empty($to) && empty($cc) && empty($bcc)) {
        throw new Exception("At least one recipient is required.");
    }

    $emailData = compact('to', 'cc', 'bcc', 'subject', 'message');
    if ($file && $file['tmp_name']) $emailData['attachment'] = $file;

    $service = EmailServiceManager::make($provider);
    $sent    = $service->send($emailData);

    if ($sent)
        echo "✅ Email sent successfully.";
} catch (Exception $e) {
    Logger::log("send.php Error: " . $e->getMessage());
    echo "❌ Error: " . $e->getMessage();
}
