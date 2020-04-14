<?php

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
        //$this->logger->info($sSubject);

        // her skal brukernavn og passord

        // Create the Mailer using your created Transport
        /*$mailer = new \Swift_Mailer($transport);

        // Create a message
        $message = (new \Swift_Message($sSubject))
            ->setFrom(['kontakt@lanelitt.no' => 'Lånelitt'])
            ->setTo([$sEmailTo => $sEmailToName])
            //->setTo(['finn@altermulig.no', 'post@altermulig.no => 'A name'])
            ->setBody($sBody, 'text/html')
        ;

        // Send the message
        $result = $mailer->send($message);*/
    }
}
