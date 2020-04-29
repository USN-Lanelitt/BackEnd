<?php

/*
 * Finn Svoslbru Gundersen
 */

namespace App\Controller;

use App\Entity\Zipcode;
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
use \App\Controller\SendMailController;



class UserController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function registerUser(Request $request) // Finn
    {
        //Sjekker om requesten har innhold
        $content=json_decode($request->getContent());
        if(empty($content)){
            return new JsonResponse('');
        }

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
        $bNewsletter = $content->newsletter;
        $bUserterms  = $content->terms;
        $sPassword   = password_hash($content->password, PASSWORD_DEFAULT);

        $sRegex="-,', ";
        $sFirstname=ucwords(strtolower($sFirstname),$sRegex);
        $sMiddlename=ucwords(strtolower($sMiddlename),$sRegex);
        $sLastname=ucwords(strtolower($sLastname),$sRegex);


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
            $oUser->setUsertype("user");
            $oUser->setNewsSubscription($bNewsletter);
            $oUser->setUserterms($bUserterms);
            $oUser->setProfileImage("profileimage.jpg");
            $oUser->setActive(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($oUser);
            $entityManager->flush();

            //Logging funksjon
            $loggUserId=$oUser->getId();
            $info=($loggUserId." - ".$sFirstname." - ".$sMiddlename." - ".$sLastname." - ".$sBirthdate." - ".$sEmail." - ".$sPhone);
            $this->forward('App\Controller\UtilController:logging',[
                'userId'=>$loggUserId,
                'functionName'=>'registerUser',
                'controllerName'=>'UserController',
                'info'=>$info,
                'change'=>1
            ]);

            // Hente ut individid
            //$iUserId = $oUser->getId();

            // Send ut velkomst e-post
            $sSubject = "Velkommen til Lånelitt";
            $sBody  = "Hei ".$sFirstname."<br /><br />";
            $sBody .= "<h4>Velkommen til lånelitt.no</h4><br /><br />";
            $sBody .= "Mvh Lånelitt teamet";
            $sEmailToName = UtilController::makeName($sFirstname,$sMiddlename,$sLastname);
            SendMailController::sendEmail($sSubject, $sBody, $sEmail, $sEmailToName);
        }

        return new JsonResponse($aReturn);
    }

    public function login($sUsername, $sPassword) // Finn
    {
        $this->logger->info($sUsername);
        $this->logger->info($sPassword);

        $arrayCollection['code'] = 400;
        $oRepository = $this->getDoctrine()->getRepository(Users::class);

        // sjekker om logge inn med e-post
        $oUser = $oRepository->findBy(['email' => $sUsername]);

        //Sjekker om oUser har innhold
        if (empty($oUser)) {
            $arrayCollection['code'] = 400;
            return new JsonResponse($arrayCollection);
        }

        $arrayCollection = array();
        $sHashPassword = "";
        $loggId=""; //til logging

        foreach($oUser as $oU) {
            // hente ut zipcode object og gjøre om til 4 siffret string
            $oZipcode = $oU->getZipCode();
            $sZipcode="";
            $sCity="";
            if(!empty($oZipcode)){
                $iZipcode = $oZipcode->getId();
                $sZipcode = sprintf('%04d', $iZipcode);
                $sCity    = $oZipcode->getCity();
            }

            $arrayCollection[] = array(
                'id' => $oU->getId(),
                'firstname' => $oU->getFirstname(),
                'middlename' => $oU->getMiddlename(),
                'lastname' => $oU->getLastname(),
                'email' => $oU->getEmail(),
                'phone' => $oU->getPhone(),
                'profileImage' => $oU->getProfileImage(),
                'nickname' => $oU->getNickname(),
                'address' => $oU->getAddress(),
                'address2' => $oU->getAddress2(),
                'zipcode' => $sZipcode,
                'city' => $sCity,
                'newsletter' => $oU->getNewsSubscription(),
                'active' => $oU->getActive(),
                'usertype'=> $oU->getUsertype()
            );

            $sHashPassword =  $oU->getPassword();
            $loggId.=$oU->getId();//til logging
        }

        // sjekke om personen er active bruker
        if (! $arrayCollection[0]['active'])
        {
            $arrayCollection['code'] = 400;
            return new JsonResponse($arrayCollection);
        }

        // sjekke passord.
        if ( ! password_verify($sPassword, $sHashPassword))
        {
            $this->logger->info('Feil ved innlogging');
            $arrayCollection['code'] = 400;
        }
        else
        {
            // Generere en 20 char string som lagres på bruker. Brukes for å sjekke om det er riktig logginn ved hver api spørring.
            // BRUKES IKKE; MEN KAN BRUKES HVIS SKAL LEGGE INN VALIDERING PÅ INNLOGGING DIREKTE FRA SYMFONY
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

        //Logging funksjon
        $timeStamp=new \DateTime();
        $info=($loggId." - ".$sUsername." - ".$timeStamp->format('Y-m-d H:i:s'));
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$loggId,
            'functionName'=>'login',
            'controllerName'=>'UserController',
            'info'=>$info,
            'change'=>0
        ]);

        return new JsonResponse($arrayCollection);
    }

    public function updatePassword(Request $request) // Finn - er ikke i bruk
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

        //Logging funksjon
        $info=($iUserId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$iUserId,
            'functionName'=>'updatePassword',
            'controllerName'=>'UserController',
            'info'=>$info,
            'change'=>1
        ]);

        return new JsonResponse($aCode);
    }

    public function profileimageUpload(Request $request)  // Finn
    {
        $this->logger->info($request);
        $sImage            = $request->files->get('file');
        $iUserId           = $request->request->get('userId');

        // Slette bilder som finnes fra før
        $mask = '../../FrontEnd/public/profileImages/'.$iUserId.'_*.*';
        array_map('unlink', glob($mask));

        $aReturn['code'] = 400;
        $aReturn['image'] = "";

        $iLength = 5; // antall tegn i navnet på filnanvet på bilde
        $sImageNameRandom = UtilController::randomString($iLength);

        $ImageOriginalName = $sImage->getClientOriginalName();

        // lage nytt bildenavn
        $aTemp = explode(".", $ImageOriginalName);
        $sNewfilename = $iUserId.'_'.$sImageNameRandom.'.'.end($aTemp);

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
            $oUser = $this->getDoctrine()->getRepository(Users::class)->find($iUserId);
            if (!$oUser) {
                throw $this->createNotFoundException(
                    'No product found for id '.$iUserId
                );
            }
            $oUser->setProfileImage($sNewfilename);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($oUser);
            $entityManager->flush();

            $aReturn['code'] = 200;
            $aReturn['image'] = $sNewfilename;
        }
        else
        {
            $this->logger->info("Sorry, there was an error uploading your file.");
        }

        //Logging funksjon
        $info=($iUserId." - ".$sNewfilename);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$iUserId,
            'functionName'=>'profileImageUpload',
            'controllerName'=>'UserController',
            'info'=>$info,
            'change'=>1
        ]);

        return new JsonResponse($aReturn);
    }

    public function getUsers()
    {
        //Henter alla brukere
        $oUsers = $this->getDoctrine()->getRepository(Users::class)->findAll();

        //Logging funksjon
        $info=("null");
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>-1,
            'functionName'=>'getUsers',
            'controllerName'=>'UserController',
            'info'=>$info,
            'change'=>0
        ]);

        //Skriver ut alle objektene
        return $this->json($oUsers, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'userInfo'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getOneUser($iUserId2)
    {
        if(empty($iUserId2)){
            return new JsonResponse('mangler id');
        }

        //Henter en bruker
        $oUser = $this->getDoctrine()->getRepository(Users::class)->find($iUserId2);

        //Logging funksjon
        $info = ($iUserId2);
        $this->forward('App\Controller\UtilController:logging', [
            'userId' => -1,
            'functionName' => 'getUser',
            'controllerName' => 'UserConnectionsController',
            'info' => $info,
            'change' => 0
        ]);

        //Skriver ut alle objektene
        return $this->json($oUser, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            //ObjectNormalizer::ATTRIBUTES => ['id', 'firstName', 'middleName', 'lastName'],
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

        //Logging funksjon
        $info=("null");
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>-1,
            'functionName'=>'getUserAmount',
            'controllerName'=>'UserController',
            'info'=>$info,
            'change'=>0
        ]);

        return new JsonResponse($userAmount);
    }

    public function editUser(Request $request, $iUserId)
    {
        $this->logger->info("LOL");

        // Hente ut data fra overføring fra React
        $content           = json_decode($request->getContent());
        $this->logger->info($content->usertype);
        if(!empty($content->nickname)){
            $sNickname         = $content->nickname;
        }else{
            $sNickname="";
        }
        $sFirstname        = $content->firstname;
        if(!empty($content->middlename)) {
            $sMiddlename = $content->middlename;
        }else{
            $sMiddlename="";
        }
        $sLastname = $content->lastname;
        if(!empty($content->phone)) {
            $sPhone = $content->phone;
        }else{
            $sPhone="";
        }
        if(!empty($content->address)) {
            $sAddress          = $content->address;
        }else{
            $sAddress="";
        }
        if(!empty($content->address2)) {
            $sAddress2         = $content->address2;
        }else{
            $sAddress2="";
        }
        if(!empty($content->zipcode)) {
            $iZipCode          = (int)$content->zipcode;
        }else{
            $iZipCode="";
        }
        if(!empty($content->usertype)) {
            $sUsertype         = $content->usertype;
        }else{
            $sUsertype="";
        }
        if(!empty($content->active)) {
            $bActive = boolval($content->active);
        }else{
            $bActive="";
        }
        if(!empty($content->newsSubscription)) {
            $bNewsSubscription = boolval($content->newsSubscription);
        }else{
            $bNewsSubscription="";
        }

        $this->logger->info("LOL10");

        $sRegex="-,', ";
        $sFirstname=ucwords(strtolower($sFirstname),$sRegex);
        $sMiddlename=ucwords(strtolower($sMiddlename),$sRegex);
        $sLastname=ucwords(strtolower($sLastname),$sRegex);

        // Sjekke om brukeren finnes i databasen
        $oUser = $this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $oZipcode = $this->getDoctrine()->getRepository(Zipcode::class)->find($iZipCode);
        // Hente ut by for å sende til front
        $sCity="";
        if(!empty($oZipcode)){
            $sCity = $oZipcode->getCity();
        }
        $this->logger->info("LOL11");
        if($sNickname!="") {
            $oUser->setNickname($sNickname);
        }
        $oUser->setFirstName($sFirstname);
        if($sMiddlename!="") {
            $oUser->setMiddleName($sMiddlename);
        }
        $oUser->setLastName($sLastname);
        if($sUsertype!="") {
            if (strlen(trim($sUsertype)) > 0) // komer som blank når brukeren endrer sine egne data
                $oUser->setUserType($sUsertype);
        }
        if($sAddress!="") {
            $oUser->setAddress($sAddress);
        }
        if($sAddress2!="") {
            $oUser->setAddress2($sAddress2);
        }
        if($oZipcode!="") {
            $oUser->setZipCode($oZipcode);
        }
        if($sPhone!="") {
            $oUser->setPhone($sPhone);
        }
        $this->logger->info("LOL12");

        //Setter in verdi for active -- Kun gå inn når settes fra admin , ikke fra edit user selv. da kommer den blank
        $this->logger->info(__FILE__.' '.__LINE__);
        if ($bActive || $bActive === false) {
            if ($bActive) {
                $oUser->setActive($bActive);
            } else {
                $oUser->setActive($bActive);
            }
        }

        //Setter in verdi for NewsSubscription
        if($bNewsSubscription) {
            $oUser->setNewsSubscription($bNewsSubscription);
        }
        else {
            $oUser->setNewsSubscription($bNewsSubscription);
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
        /*if($sEmail != $oUser->getEmail() ) {
            $oEmailExist = $this->getDoctrine()->getRepository(Users::class)->findEmail($sEmail);

            if (empty($oEmailExist)) {
                $oUser->setEmail($sEmail);
            }
        }*/

        /*if($sPassword != $oUser->getPassword()){
            $oUser->setPassword($sPassword);
        }*/

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($oUser);
        $entityManager->flush();
        $this->logger->info("LOL13");

        //Logging funksjon
        $loggUserId=$oUser->getId();
        $info=($loggUserId." - ".$sFirstname." - ".$sMiddlename." - ".$sLastname." - ".$sUsertype." - ".$bActive." - ".$bNewsSubscription." - ".$sPhone." - ".$sAddress." - ".$sAddress2." - ".$iZipCode);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$loggUserId,
            'functionName'=>'editUser',
            'controllerName'=>'UserController',
            'info'=>$info,
            'change'=>1
        ]);

        $aReturn = array(
            'city' => $sCity,
            'code' => 200
        );
        return new JsonResponse($aReturn);
    }

    public function getZipcode($sZipcode)
    {
        //Returnere by ved å hente fra zipcode
        $this->logger->info($sZipcode);
        $iZipcode = (int)$sZipcode;
        $oZipcode = $this->getDoctrine()->getRepository(Zipcode::class)->find($iZipcode);
        $sCity = $oZipcode->getCity();
        return new JsonResponse($sCity);
    }
}


