<?php
namespace App\Service;
use App\Repository\UserRepository;
use App\Service\Utils;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

    class ApiRegister{

        public function authentification(UserPasswordHasherInterface $uPass,UserRepository $repo,$mail,$password){
            $password = Utils::cleanInputStatic($password);
            $mail = Utils::cleanInputStatic($mail);
            $recup = $repo->findOneBy(['email'=>$mail]);
            if ($recup) {
                if ($uPass->isPasswordValid($recup,$password)) {
                    return true;
                }else{
                    return false;
                }
                
                
            }else{
                return false;
            }

        }
        public function vefifToken($jwt,$key)
        {
            require_once('../vendor/autoload.php');

            try {
                $token = JWT::decode($jwt,$key);
                return true;
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }

       
    }
?>