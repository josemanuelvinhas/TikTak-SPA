<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';

class Mail
{

    private PHPMailer $mail;

    public function __construct($address, $name, $subject, $body)
    {
        $this->mail = new PHPMailer();

        $this->mail->isSMTP();

        $this->mail->SMTPDebug = SMTP::DEBUG_OFF;

        $this->mail->Host = 'smtp.gmail.com';

        $this->mail->Port = 587;

        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $this->mail->SMTPAuth = true;

        $this->mail->Username = 'tiktak.info.noreply@gmail.com';

        $this->mail->Password = 'tiktakpass1234';

        $this->mail->setFrom('tiktak.info.noreply@gmail.com', 'TikTak');

        $this->mail->addAddress($address, $name);

        $this->mail->Subject = $subject;

        $this->mail->Body= $body;


    }

    public function sendMail()
    {
        try {
            $this->mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {

        }
    }
}