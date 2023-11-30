<?php

namespace App\Manager;

class ContactManager {
    
    public function sendEmailToAdmin($email, $subject, $content)
    {
        $response = false;

        if(!empty($email) && !empty($subject) && !empty($content)) {
            $content = str_replace("\n.", "\n..", $content);
            $response = \mail("gary.almeida.work@gmail.com", $subject, $content, [
                "from" => "no-reply@gem-earth.com",
                'Reply-To' => "no-reply@gem-earth.com",
                'X-Mailer' => 'PHP/' . phpversion()
            ]);
        }

        return $response;
    }

    public function sendEmailToUser($destEmail, $subject, $content)
    {
        $response = false;

        if(!empty($destEmail) && !empty($subject) && !empty($content)) {
            $content = str_replace("\n.", "\n..", $content);
            $response = \mail($destEmail, $subject, $content, [
                "from" => "no-reply@gem-earth.com",
                'X-Mailer' => 'PHP/' . phpversion()
            ]);
        }

        return $response;
    }
}