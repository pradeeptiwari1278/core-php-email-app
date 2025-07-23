<?php

namespace Utils;

use PHPMailer\PHPMailer\PHPMailer;

class EmailHelper
{
    public static function addRecipients(PHPMailer $mail, string $list = '', string $type = 'to')
    {
        $entries = array_filter(array_map('trim', explode(',', $list)));

        foreach ($entries as $entry) {
            preg_match('/^(.*?)<(.+@.+\..+)>$/', $entry, $matches);
            $name  = trim($matches[1] ?? '');
            $email = trim($matches[2] ?? $entry);

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                match ($type) {
                    'to'  => $mail->addAddress($email, $name),
                    'cc'  => $mail->addCC($email, $name),
                    'bcc' => $mail->addBCC($email, $name),
                };
            }
        }
    }
}
