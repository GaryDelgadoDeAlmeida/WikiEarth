<?php

namespace App\Manager;

use App\Entity\User;
use App\Manager\NotificationManager;
use Symfony\Component\Form\Form;
use Intervention\Image\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager {
    
    public function insertUser(Form $formUser, User $user, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, $project_users_dir)
    {
        try {
            $this->insertUserImg($project_users_dir, $formUser['imgPath']->getData(), $user);
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $manager->persist($user);
            $manager->flush();

            return [
                "class" => "success",
                "message" => "Your account has been successfully created. Welcome to GemEarth, {$user->getFirstname()}."
            ];
        } catch(\Exception $e) {
            return [
                "class" => "danger",
                "message" => "I'm sorry, an error occurred. A notification has been send to the moderator to check what was the problem."
            ];
        } finally {}
    }

    public function updateUser(Form $formUser, User $user, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, $project_users_dir)
    {
        $message = [];
        try {
            $this->insertUserImg($project_users_dir, $formUser['imgPath']->getData(), $user);

            if(!empty($formUser->get("password")->getData())) {
                $user->setPassword($encoder->encodePassword($user, $formUser->get("password")->getData()));
            }

            $manager->persist($user);
            $manager->flush();

            $message = [
                "class" => "success",
                "message" => "This account has been successfully updated"
            ];
        } catch(\Exception $e) {
            $message = [
                "class" => "danger",
                "message" => "I'm sorry, an error occurred. A notification has been send to the moderator to check what was the problem."
            ];
        } finally {}

        return $message;
    }

    /**
     * Problème de cache utilisateur de l'image provenant du navigateur
     */
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

                // $manager = new ImageManager(array('driver' => 'imagick'));
                // $image = $manager->make($project_users_dir . $user->getId() . "/{$newFilename}")->resize(300, 200);
            } catch (FileException $e) {
                dd($e->getMessage());
            }

            $user->setImgPath("content/users/" . $user->getId() . "/" . $newFilename);
        }
    }
}