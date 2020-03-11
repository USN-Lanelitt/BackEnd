<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Individuals;
use App\Entity\Users;
use Symfony\Component\Sw;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use \DateTime;
use \App\Controller\SendEmailController;
use \App\Controller\UtilController;

class UserController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    public function registerUser(Request $request)
    {
        $bRegistreUser = false;
        $aReturn = array();
        /* STATUS Koder
         * 200 - OK
         * 100 - Feil ved oppretting av bruker
         * 101 - e-post finnes
         * 102 - telefonnummer finnes
         * 103 - Feil ved innlogging
         */
        $this->logger->info($request);

        // Hente ut data fra overføring fra React
        $content = json_decode($request->getContent());
        $sFirstname  = $content->firstname;
        $sMiddlename = $content->middlename;
        $sLastname   = $content->lastname;
        $sBirthdate  = $content->birthdate;
        $sEmail      = $content->email;
        $sPhone      = $content->phone;
        $sPassword   = password_hash($content->password, PASSWORD_DEFAULT);

        $sBirthdate = new DateTime($sBirthdate);


        // Sjekke om e-post finnes i databasen
        $oUserExist = $this->getDoctrine()->getRepository(Users::class)->findOneBy(['email'=>$sEmail]);

        if ($oUserExist === null) { // bruker finnes ikke - Opprette ny bruker
            //$this->logger->info("200");
            $bRegistreUser = true;
            $aReturn[0] = 200;
        }
        else { // bruker finnes
            //$this->logger->info("101");
            $bRegistreUser = false;
            $aReturn[0] = 101;
        }

        //Sjekke om telefon finnes fra før
        $oUserExist = $this->getDoctrine()->getRepository(Users::class)->findOneBy(['phone'=>$sPhone]);

        if ($oUserExist === null) {
            if (! $bRegistreUser) { // kun sette 200 hvis begge ikke finnes.
                // ikke gjøre noe
            }
            else
            {
                //$this->logger->info("200");
                $bRegistreUser = true;
                $aReturn[0] = 200;
            }
        }
        else
        {
            //$this->logger->info("102");
            $bRegistreUser = false;
            $aReturn[0] = 102;
        }

        if ($bRegistreUser) {
            // lagre brukerinfo
            $oUser = new Users();
            $oUser->setFirstName($sFirstname);
            $oUser->setMiddleName($sMiddlename);
            $oUser->setLastName($sLastname);
            $oUser->setBirthDate($sBirthdate);
            $oUser->setEmail($sEmail);
            $oUser->setPhone($sPhone);
            $oUser->setPassword($sPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($oUser);
            $entityManager->flush();

            // Hente ut individid
            //$iUserId = $oUser->getId();

            // Send ut velkomst e-post
            /*$sSubject = "Velkommen til Lånelitt";
            $sBody  = "Hei ".$sFirstname."<br /><br />";
            $sBody .= "<h4>Velkommen til lånelitt.no</h4><br /><br />";
            $sBody .= "Mvh Lånelitt teamet";
            $sEmailToName = UtilController::makeName($sFirstname,$sMiddlename,$sLastname);
            SendEmailController::sendEmail($sSubject, $sBody, $sEmail, $sEmailToName);*/
        }

        return new JsonResponse($aReturn);
    }

    public function login(Request $request)
    {
        $this->logger->info($request);

        $content = json_decode($request->getContent());
        $sUsername = $content->username;
        $sPassword = $content->password;

        $oRepository = $this->getDoctrine()->getRepository(Users::class);
        $oUser = "";

        if(strpos($sUsername, "@") !== false) // logger inn med e-post
            $oUser = $oRepository->findBy([ 'email' => $sUsername ]);
        else // logger inn med telefonnummer
            $oUser = $oRepository->findBy([ 'phone' => $sUsername ]);

        $arrayCollection = array();
        $sHashPassword = "";

        foreach($oUser as $oU) {
            $arrayCollection[] = array(
                'id' => $oU->getId(),
                'firstname' => $oU->getFirstname(),
                'middlename' => $oU->getMiddlename(),
                'lastname' => $oU->getLastname(),
                'email' => $oU->getEmail(),
                'mobile' => $oU->getPhone()
                // ... Same for each property you want
            );
            $sHashPassword =  $oU->getPassword();
        }

        // sjekke passord.
        if ( ! password_verify($sPassword, $sHashPassword))
        {
            $arrayCollection = array(103);
            $this->logger->info('Feil ved innlogging');
        }

        $this->logger->info(json_encode($arrayCollection));

        return new JsonResponse($arrayCollection);
    }
}
