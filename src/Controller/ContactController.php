<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;
use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Messagerie;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);


    }

    
    #[Route('/contact/addContact', name: 'app_Add_contact')]
    public function addContact(EntityManagerInterface $em, Request $request,
    ContactRepository $repo,Messagerie $messagerie): Response
    {
        $msg = "";
        $status = "";
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if($form->isSubmitted() AND $form->isValid()){
            //récupération de l'enregistrement
            $contact->setContenu( Utils::cleanInputStatic($request->request->all('contact')['contenu']));
            $contact->setMail( Utils::cleanInputStatic($request->request->all('contact')['mail']));
            $contact->setNom( Utils::cleanInputStatic($request->request->all('contact')['nom']));
            $contact->setPrenom( Utils::cleanInputStatic($request->request->all('contact')['prenom']));
            $contact->setObjet( Utils::cleanInputStatic($request->request->all('contact')['objet']));

            $recup = $repo->findOneBy(['nom'=>$contact->getNom(), 'mail'=>$contact->getMail(), 'prenom'=>$contact->getPrenom(),
             'objet'=>$contact->getObjet(), 'contenu'=>$contact->getContenu() ]);

            
            //tester si la catégorie existe déja
            if(!$recup){
                //persister les données du formulaire
                $em->persist($contact);
                //ajouter en BDD
                $em->flush();
                $msg = "L'article ".$contact->getNom()." a été ajouté en BDD";

                
                $login = $this->getParameter('login');
                $mdp = $this->getParameter('mdp');
                $date = $contact->getDate()->format('d-m-Y');
                $objet = $contact->getObjet();
                $content = '<p>Nom : <strong>'.$contact->getNom().'</strong></p>'.
                '<p>Prénom : <strong>'.$contact->getPrenom().'</strong></p>'.
                '<p>Mail : <strong>'.$contact->getMail().'</strong></p>'.
                '<p>Contenu : <strong>'.mb_convert_encoding($contact->getContenu(), 'ISO-8859-1', 'UTF-8').'</strong></p>'.
                '<p>Date envoie : <strong>'.$date.'</strong></p>';
                

                $destinataire = 'sympta113@gmail.com';
                $status = $messagerie->sendMail($login,$mdp,$destinataire,$objet,$content);
            }
            else{
                $msg = "L'article ".$contact->getNom()." existe déja en BDD";
            }
        }
        return $this->render('contact/addContact.html.twig', [
            'status' => $status,
            'form' => $form->createView(),
            'msg' => $msg,
        ]);


    }
}
