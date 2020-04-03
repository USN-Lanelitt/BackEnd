<?php

namespace App\Controller;

use phpDocumentor\Reflection\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Individuals;
use App\Entity\Users;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Sw;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
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
        $this->logger->info($content);
        $iUserId        = (int)$content->userId;
        $sOldPassword   = $content->currentPassword;
        $sNewPassword   = password_hash($content->newPassword, PASSWORD_DEFAULT);
        $sHashPassword  = "";

        $this->logger->info($sOldPassword);
        $this->logger->info($sNewPassword);


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
        $aReturn['code'] = 400;
        $aReturn['image'] = "";
        $sImage            = $request->files->get('file');
        $iUserId           = $request->request->get('userId');
        $ImageOriginalName = $sImage->getClientOriginalName();
        //$this->logger->info($sImage->getClientOriginalExtension());

        // lage nytt bilde navn
        $aTemp = explode(".", $ImageOriginalName);
        $sNewfilename = $iUserId.'_profileImage.'.end($aTemp);

        $sTargetDir = "../../FrontEnd/public/profileImages/";

        $sTargetFile = $sTargetDir . $sNewfilename;
        $this->logger->info($sTargetFile);

        $aCheck = getimagesize($sImage);
        if($aCheck !== false) {
            $this->logger->info("File is an image - " . $aCheck["mime"] . ".");
            $uploadOk = 1;
        } else {
            $this->logger->info("File is not an image.");
            $uploadOk = 0;
            // returnere 400 hvis det ikke er et bilde.
            return new JsonResponse($aReturn);
        }

        if (move_uploaded_file($sImage, $sTargetFile)) {
            $this->logger->info("The file ". basename($ImageOriginalName). " has been uploaded.");
            $aReturn['code'] = 200;
            $aReturn['image'] = $sNewfilename;

            $entityManager = $this->getDoctrine()->getManager();
            $oUsers = $entityManager->getRepository(Users::class)->find($iUserId);
            if (!$oUsers) {
                throw $this->createNotFoundException(
                    'No product found for id '.$iUserId
                );
            }
            $oUsers->setProfileImage($sNewfilename);
            $entityManager->flush();
        } else {
            $this->logger->info("Sorry, there was an error uploading your file.");
        }

        return new JsonResponse($aReturn);
    }

    public function getUsers()
    {
        //Henter alla brukere
        $oUsers = $this->getDoctrine()->getRepository(Users::class)->findAll();

        //Skriver ut alle objektene
        return $this->json($oUsers, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'userInfo'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getUserAmount()
    {
        //Henter antall brukere
        $oUsers = $this->getDoctrine()->getRepository(Users::class)->findAll();
        $userAmount=count($oUsers);

        return new JsonResponse($userAmount);
    }

    public function editUser(Request $request, $iUserId)
    {
        $this->logger->info($request.''.$iUserId);

        // Hente ut data fra overføring fra React
        $content = json_decode($request->getContent());
        $sNickname  = $content->nickname;
        $sFirstname  = $content->firstname;
        $sMiddlename = $content->middlename;
        $sLastname = $content->lastname;
        $sEmail      = $content->email;
        $sUsertype = $content->usertype;
        $iActive= $content->active;
        $iNewsSubscription = $content->newsSubscription;

        //$sAddress  = $content->address;
        //$sAddress2   = $content->address2;
        //$sZipCode  = $content->zipCode;
        //$sPhone = $content->phone;
        //$sPassword   = password_hash($content->password, PASSWORD_DEFAULT);
        //$iUserterms = $content->userterms;

        // Sjekke om brukeren finnes i databasen
        $oUser = $this->getDoctrine()->getRepository(Users::class)->find($iUserId);

        $oUser->setNickname($sNickname);
        $oUser->setFirstName($sFirstname);
        $oUser->setMiddleName($sMiddlename);
        $oUser->setLastName($sLastname);
        $oUser->setUserType($sUsertype);

        //$oUser->setAddress($sAddress);
        //$oUser->setAddress2($sAddress2);
        //$oUser->setZipCode($sZipCode);
        //$oUser->setPhone($sPhone);
        //$oUser->setBirthDate(\DateTime::createFromFormat('d.m.Y', $sBirthdate));

        //Setter in verdi for active
        if($iActive == "true" ) {
            $true=1;
            $oUser->setActive($true);
        }
        else {
            $false=0;
            $oUser->setActive($false);
        }

        //Setter in verdi for NewsSubscription
        if($iNewsSubscription == "true" ) {
            $true=1;
            $oUser->setNewsSubscription($true);
        }
        else {
            $false=0;
            $oUser->setNewsSubscription($false);
        }

        //Setter in verdi for Userterms
       /*if($iUserterms == "true" ) {
            $true=1;
            $oUser->setUserterms($true);
        }
        else {
            $false=0;
            $oUser->setUserterms($false);
        }*/

        //lag en sjekk på epost, har den endret seg, finnes den fra før
        if($sEmail != $oUser->getEmail() ) {
            $oEmailExist = $this->getDoctrine()->getRepository(Users::class)->findEmail($sEmail);

            if (empty($oEmailExist)) {
                $oUser->setEmail($sEmail);
            }
        }

        /*if($sPassword != $oUser->getPassword()){
            $oUser->setPassword($sPassword);
        }*/

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($oUser);
        $entityManager->flush();

        return new JsonResponse("endret");
    }
}


