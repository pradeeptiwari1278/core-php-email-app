<?php

namespace Core;

use Services\GmailEmailService;
use Services\SendGridEmailService;
use Services\MailgunEmailService;
use Services\SESEmailService;
use Contracts\EmailServiceContract;

class EmailServiceManager
{
    public static function make(string $driver): EmailServiceContract
    {
        return match (strtolower($driver)) {
            'sendgrid'   => new SendGridEmailService(),
            'gmail'      => new GmailEmailService(),
            'mailgun'    => new MailgunEmailService(),
            'ses'        => new SESEmailService(),
            default      => throw new \InvalidArgumentException("Invalid email driver [$driver]."),
        };
    }
}
