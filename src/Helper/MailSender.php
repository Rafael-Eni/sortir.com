<?php

namespace App\Helper;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailSender
{

    public function __construct(private MailerInterface $mailer)
    {


    }

    public function sendEmail(string $subject, string $text, string $dest)
    {

        $email = new Email();
        $email->subject($subject)
            ->text($text)
            ->from('noreply@sortir.com')
            ->to($dest);

        $this->mailer->send($email);

    }


}