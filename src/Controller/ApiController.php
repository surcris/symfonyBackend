<?php 
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Service\ApiRegister;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiController extends AbstractController{
    #[Route('/api/verif',name:'app_api_verif')]
    public function verifConnexion (UserPasswordHasherInterface $uPass,UserRepository $repo,ApiRegister $apiR,Request $request)
    {
        $mail = $request->query->get('email');
        $password = $request->query->get('password');

        $check = $apiR->authentification($uPass,$repo,$mail,$password);
        if ($check == true) {
            return $this->json(['connexion'=>'OK'], 200, 
                ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> 'localhost',
                'Access-Control-Allow-Methods'=> 'GET'],[]);
        }else{
            return $this->json(['connexion'=>'INVALIDE'], 400, 
            ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);
        }
    }

    public function genToken($mail, $secretKey, $repo)
    {
        //autolaod composer
        require_once('../vendor/autoload.php');
        //Variables pour le token
        $issuedAt   = new \DateTimeImmutable();
        $expire     = $issuedAt->modify('+60 minutes')->getTimestamp();
        $serverName = "localhost";
        $username   = $repo->findOneBy(['email' => $mail])->getNom();
        //Contenu du token
        $data = [
                'iat'  => $issuedAt->getTimestamp(),         // Timestamp génération du token
                'iss'  => $serverName,                       // Serveur
                'nbf'  => $issuedAt->getTimestamp(),         // Timestamp empécher date antérieure
                'exp'  => $expire,                           // Timestamp expiration du token
                'userName' => $username,                     // Nom utilisateur
            ];

        $token = JWT::encode($data,$secretKey,'HS512');

        return $token;
    }

    #[Route('/api/register',  name:'app_api_register')]
    public function getToken(Request $request,UserRepository $repo,UserPasswordHasherInterface $uPass,ApiRegister $apiR)
    {
        $mail = $request->query->get('email');
        $password = $request->query->get('password');

        $key = $this->getParameter('token');

        if ($mail AND $password) {
            if ($apiR->authentification($uPass,$repo,$mail,$password)) {

                $mToken = $this->genToken($mail,$key,$repo);
                return $this->json(['Token_JWT'=>$mToken], 200, 
                ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> 'localhost',
                'Access-Control-Allow-Methods'=> 'GET'],[]);
                

            }else{
                return $this->json(['Erreur'=>'Erreur lors de l\'authentification.'], 400, 
                ['Content-Type'=>'application/json',
                'Access-Control-Allow-Origin'=> 'localhost',
                'Access-Control-Allow-Methods'=> 'GET'],[]);
            }
            
        }else{
            return $this->json(['Erreur'=>'Veuillez entrer tous les informations'], 400, 
            ['Content-Type'=>'application/json',
            'Access-Control-Allow-Origin'=> 'localhost',
            'Access-Control-Allow-Methods'=> 'GET'],[]);
        }
    }
}
?>