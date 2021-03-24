<?php

namespace App\Manager;

class ContactManager {

    private $sendTo = "gary.almeida.work@gmail.com";
    
    public function sendEmail($email, $subject, $content)
    {
        $response = false;

        if(!empty($email) && !empty($subject) && !empty($content)) {
            $content = str_replace("\n.", "\n..", $content);
            $response = \mail($this->sendTo, $subject, $content, [
                "from" => $email,
                'Reply-To' => $email,
                'X-Mailer' => 'PHP/' . phpversion()
            ]);
        }

        return $response;
    }
}