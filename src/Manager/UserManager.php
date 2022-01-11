<?php

namespace App\Manager;

use App\Entity\User;
use App\Manager\NotificationManager;
use Symfony\Component\Form\Form;
use Intervention\Image\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager {
    
    /**
     * @param Form user form
     * @param User object of class User
     * @param EntityManagerInterface object of class EntityManagerInterface to contact our database
     * @param UserPasswordEncoderInterface object of class UserPasswordEncoderInterface to encode user password
     * @param string user repository
     * @return array Returns the result of the insert process
     */
    public function insertUser(Form $formUser, User $user, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, string $project_users_dir)
    {
        try {
            $this->insertUserImg($project_users_dir, $formUser['imgPath']->getData(), $user);
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            $manager->persist($user);
            $manager->flush();

            return [
                "class" => "success",
                "message" => "Your account has been successfully created. Welcome to WikiEarth, {$user->getFirstname()}."
            ];
        } catch(\Exception $e) {
            return [
                "class" => "danger",
                "message" => "I'm sorry, an error occurred. A notification has been send to the moderator to check what was the problem."
            ];
        }
    }

    /**
     * @param Form
     * @param User
     * @param EntityManagerInterface
     * @param UserPasswordEncoderInterface
     * @param string project_users_dir
     */
    public function updateUser(Form $formUser, User $user, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, string $project_users_dir)
    {
        try {
            $this->insertUserImg($project_users_dir, $formUser['imgPath']->getData(), $user);

            if(!empty($formUser->get("password")->getData())) {
                $user->setPassword($encoder->encodePassword($user, $formUser->get("password")->getData()));
            }

            $manager->persist($user);
            $manager->flush();

            return [
                "class" => "success",
                "message" => "This account has been successfully updated"
            ];
        } catch(\Exception $e) {
            return [
                "class" => "danger",
                "message" => "I'm sorry, an error occurred. A notification has been send to the moderator to check what was the problem."
            ];
        }
    }

    /**
     * Problème de cache utilisateur de l'image provenant du navigateur
     * 
     * @param string
     * @param object
     * @param User
     */
    private function insertUserImg(string $project_users_dir, $mediaFile, User &$user)
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

    /**
     * @param User
     * @return string
     */
    private function checkUserRole(User $user)
    {
        $path = "chat/";

        if($user->hasRole('ROLE_ADMIN')) {
            $path .= "admin/";
        } else {
            $path .= "user/";
        }

        return $path;
    }
}