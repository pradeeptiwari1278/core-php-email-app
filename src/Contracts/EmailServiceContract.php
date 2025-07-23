<?php

namespace Contracts;

interface EmailServiceContract
{
    public function send(array $emailData): bool;
}
