<?php

namespace App\Manager;

use App\Entity\User;
use Symfony\Component\Form\Form;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager {
    
    public function insertUser(Form $formUser, User $user, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, $project_users_dir)
    {
        $this->insertUserImg($project_users_dir, $formUser['imgPath']->getData(), $user);
        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
        $manager->persist($user);
        $manager->flush();
    }

    public function updateUser(Form $formUser, User $user, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, $project_users_dir)
    {
        $this->insertUserImg($project_users_dir, $formUser['imgPath']->getData(), $user);

        if(!empty($formUser->get("password")->getData())) {
            $user->setPassword($encoder->encodePassword($user, $formUser->get("password")->getData()));
        }

        $manager->persist($user);
        $manager->flush();
    }

    private function insertUserImg($project_users_dir, $mediaFile, &$user)
    {
        if(!empty($mediaFile)) {
            $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $newFilename = strtolower($user->getFirstname() .'_'. str_replace(" ", "_", $user->getLastname())) . '.' . $mediaFile->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                if(
                    array_search(
                        $project_users_dir . $user->getId() . "/" . $newFilename, 
                        glob($project_users_dir . $user->getId() . "/*." . $mediaFile->guessExtension())
                    ) !== false
                ) {
                    unlink($project_users_dir . $user->getId() . "/" . $newFilename);
                }
                
                $mediaFile->move(
                    $project_users_dir . $user->getId(),
                    $newFilename
                );
            } catch (FileException $e) {
                dd($e->getMessage());
            }

            $user->setImgPath("content/users/" . $user->getId() . "/" . $newFilename);
        }
    }
}