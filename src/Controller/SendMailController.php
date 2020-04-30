<?php

/*
 *Finn har jobbet med denne filen
 *
 */
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SendMailController extends AbstractController
{
    /**
     * @Route("/send/mail", name="send_mail")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SendMailController.php',
        ]);
    }

    public static function sendEmail($sSubject, $sBody, $sEmailTo, $sEmailToName)
    {

        // Denne koden er tatt bort fra tet på grunn av brukernavn og passord til mailgun. Dette ligger på server og virker
        //Dette på grunn av gitreository er public og da vil brukernavn og passord være synlig for alle.
    }
}
