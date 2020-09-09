<?php

namespace App\Manager;

use App\Entity\User;
use Symfony\Component\Form\Form;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager {
    
    public function insertUser()
    {
        # code...
    }

    public function updateUser(Form $formUser, User $user, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, $project_wikiearth_dir)
    {
        $mediaFile = $formUser['imgPath']->getData();

        if(!empty($mediaFile)) {
            $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $newFilename = $user->getFirstname().'_'.$user->getLastname() . '.' . $mediaFile->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                if(
                    array_search(
                        $project_wikiearth_dir . "user/" . $user->getFirstname().'-'.$user->getLastname() . "/" . $newFilename, 
                        glob($project_wikiearth_dir . "user/" . $user->getFirstname().'-'.$user->getLastname() . "/*." . $mediaFile->guessExtension())
                    )
                ) {
                    unlink($project_wikiearth_dir . "user/" . $user->getFirstname().'-'.$user->getLastname() . "/" . $newFilename);
                }
                
                $mediaFile->move(
                    $project_wikiearth_dir . "user/" . $user->getFirstname().'-'.$user->getLastname(),
                    $newFilename
                );
            } catch (FileException $e) {
                dd($e->getMessage());
            }

            $user->setImgPath("content/wikiearth/user/" . $user->getFirstname().'-'.$user->getLastname() . "/" . $newFilename);
        }

        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
        $manager->persist($user);
        $manager->flush();
    }
}