<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Individuals;
use App\Entity\Users;
use Symfony\Component\Sw;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use \DateTime;
use \App\Controller\SendEmailController;

header("Access-Control-Allow-Origin: *");

class UserController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function registerUser(Request $request)
    {
        $bRegistreUser = false;
        $aReturn = array();
        /* STATUS Koder
         * 200 - OK
         * 400 - Generell feil
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

        // Sjekke om e-post finnes i databasen
        $oUserExist = $this->getDoctrine()->getRepository(Users::class)->findOneBy(['email'=>$sEmail]);

        if ($oUserExist === null) { // bruker finnes ikke - Opprette ny bruker
            $bRegistreUser = true;
            $aReturn['code'] = 200;
        }
        else { // bruker finnes
            $bRegistreUser = false;
            $aReturn['code'] = 400;
        }

        //Sjekke om telefon finnes fra før
        $oUserExist = $this->getDoctrine()->getRepository(Users::class)->findOneBy(['phone'=>$sPhone]);
        // sjekke om telefonnummeret har riktig antall tegn
        if (strlen($sPhone) === 8) {
            if ($oUserExist === null) {
                if (!$bRegistreUser) { // kun sette 200 hvis begge ikke finnes.
                    // ikke gjøre noe
                } else {
                    $bRegistreUser = true;
                    $aReturn['code'] = 200;
                }
            } else {
                $bRegistreUser = false;
                $aReturn['code'] = 400;
            }
        }
        else // hvis ikke riktig antall tegn, sette blank.
        {
            $sPhone = "";
        }

        if ($bRegistreUser) {
            // lagre brukerinfo
            $oUser = new Users();
            $oUser->setFirstName($sFirstname);
            $oUser->setMiddleName($sMiddlename);
            $oUser->setLastName($sLastname);
            $oUser->setBirthDate(\DateTime::createFromFormat('d.m.Y', $sBirthdate));
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

    public function login($sUsername, $sPassword)
    {
        $this->logger->info($sUsername);
        $this->logger->info($sPassword);

        $arrayCollection['code'] = 400;

        $oRepository = $this->getDoctrine()->getRepository(Users::class);

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
                'phone' => $oU->getPhone()
                // ... Same for each property you want
            );
            $sHashPassword =  $oU->getPassword();
        }

        // sjekke passord.
        if ( ! password_verify($sPassword, $sHashPassword))
        {
            $this->logger->info('Feil ved innlogging');
        }
        else
        {
            // Generere en 20 char string som lagres på bruker. Brukes for å sjekke om det er riktig logginn ved hver api spørring.
            //$sAuthCode = UtilController::RandomString();
            //$arrayCollection[0]['authCode'] = $sAuthCode;

            // lagre til databasen
            /*$oUser2 = new Users();
            $oUser2->setAuthCode($sAuthCode);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($oUser2);
            $entityManager->flush();*/

            //$this->logger->info("AuthCode");
            //$this->logger->info($sAuthCode);
            $arrayCollection['code'] = 200;
        }

        $this->logger->info(json_encode($arrayCollection));

        return new JsonResponse($arrayCollection);
    }


    public function updatePassword(Request $request)
    {
        $this->logger->info($request);
        $aCode['code'] = 400;

        // Hente ut data fra overføring fra React
        $content = json_decode($request->getContent());
        $iUserId        = (int)$content->userId;
        $sOldPassword   = $content->currentPassword;
        $sNewPassword   = password_hash($content->newPassword, PASSWORD_DEFAULT);
        $sHashPassword  = "";

        $oRepository = $this->getDoctrine()->getRepository(Users::class);
        $oUser = $oRepository->findBy([ 'id' => $iUserId ]);

        foreach($oUser as $oU) {
            $sHashPassword = $oU->getPassword();
        }

        if (password_verify($sOldPassword, $sHashPassword)) // passordene stemmer
        {
            // Lagre nytt passord
            $oUser = new Users();
            $oUser->setPassword($sNewPassword);
            $aCode['code'] = 200;
        }

        return new JsonResponse($aCode);
    }

    public function profileimageUpload(Request $request)
    {
        $sFile = $request->files->get('file');
        $this->logger->info($sFile);
        $this->logger->info("Er kommet hit **************************");

        $content = file_get_contents($sFile);
        $fp = fopen($sFile, "w");
        fwrite($fp, $content);
        fclose($fp);


        //$this->logger->info($content);
        return new JsonResponse(true);
    }
}
