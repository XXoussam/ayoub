<?php

namespace App\services;

class SendNotif
{
    private $email;

    public function __construct($email)
    {
        dump($email); die;
        $this->email = $email;
    }

    public function sendNotif($message)
    {

    }

}