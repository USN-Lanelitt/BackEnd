<?php

namespace App\Controller;

use App\Entity\LogingLevels;
use App\Entity\Users;
use App\Entity\Variables;
use App\Repository\VariablesRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class UtilController extends AbstractController
{
    /**
     * @Route("/util", name="util")
     */

    private $logger;

    public function __construct(LoggerInterface $logger){
        $this->logger=$logger;
    }
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UtilController.php',
        ]);
    }

    public static function makeName($sFirstname,$sMiddlename,$sLastname) {
        if (strlen(trim($sMiddlename)) > 0)
            return ($sFirstname.' '.$sMiddlename.' '.$sLastname);
        return ($sFirstname.' '.$sLastname);
    }

    public static function RandomString($iLength)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $iLength; $i++) {
            $randstring .= $characters[rand(0, strlen($characters)-1)];
        }
        return $randstring;
    }

    static $loggingLevel;
    static $aLoggLevel=array(0=>"0: No Logging",   1=>"1: Simple change log, 1 file", 2=>"2: Detailed change log, 1 file",
                                                3=>"3: Simple change log, multiple files", 4=>"4: Detailed change log, multiple files",
                                                5=>"5: Simple change and get log, 1 file", 6=>"6: Detailed change and get log, 1 file",
                                                7=>"7: Simple change and get log, multiple files", 8=>"8: Detailed change and get log, multiple files");

    public function getLevels(){
        $levels= $this->getDoctrine()->getRepository(LogingLevels::class)->findAll();

        return $this->json($levels, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }
    public function setLevel($newLevel){
        $varId=1;
        $level = $this->getDoctrine()->getRepository(Variables::class)->find($varId);
        $levels = $this->getDoctrine()->getRepository(LogingLevels::class)->find($newLevel);
        $level->setValue($levels);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($level);
        $entityManager->flush();

        return $this->json($level, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }
    public function getLevel(){
        $level = $this->getDoctrine()->getRepository(Variables::class)->find(1);
        $level=$level->getValue();
        $level=$level->getId();
        return new JsonResponse($level);
    }

    public function getLogg(){

        $loggName="log";
        $this->logger->info(1);
        if(!file_exists("$loggName.txt")){
            file_put_contents("$loggName.txt","BrukerId;KontrollerNamn;FunksjonsNamn;Tidspunkt;FunksjonsType;Datainnhold", FILE_APPEND);
        }
        $this->logger->info(1.5);
        $myfile = fopen("$loggName.txt", "r+") or die("Unable to open file!");
        $this->logger->info(2);

        $this->logger->info(3);
        $logg=array();
        $teller=0;
        while(!feof($myfile)) {
            $logg[$teller]= fgets($myfile);
            $teller++;
        }
        fclose($myfile);
        return new JsonResponse($logg);

    }
    public function logging($userId, $functionName, $controllerName, $info, $change){

        $level = $this->getDoctrine()->getRepository(Variables::class)->find(1);
        $var=$level->getValue();
        $loggingLevel=$var->getId();
        //$loggingLevel=8;
        $loggName="log";

        /*if(!file_exists("$loggName.txt")){
            file_put_contents("$loggName.txt","BrukerId;KontrollerNamn;FunksjonsNamn;Tidspunkt;FunksjonsType;Datainnhold", FILE_APPEND);
        }*/
        $myfile = fopen("$loggName.txt", "r") or die("Unable to open file!");

        $cSV=";";
        $timeStamp=new \DateTime();
        $data=("\n".$userId.$cSV.$controllerName.$cSV.$functionName.$cSV.$timeStamp->format('Y-m-d H:i:s'));
        if($change==1){
            $data.=($cSV."POST/DELETE");
        }
        else{
            $data.=($cSV."GET");
        }
        //Nivå 0 logger ikke

        //Nivå 1 logger kun set funksjoner, logger ikke $info, logger alt til hovedloggen
        //Nivå 2 logger kun set funksjoner, logger $info, logger alt til hovedloggen
        //Nivå 3 logger kun set funksjoner, logger ikke $info, logger tilhørende controller fil og til hovedloggen
        //Nivå 4 logger kun set funksjoner, logger $info, logger tilhørende controller fil og til hovedloggen
        if($loggingLevel<=4&&$loggingLevel>0&&$change==1){
            if($loggingLevel<=2){
                if($loggingLevel==2){
                    $data.=("".$info);
                }
                file_put_contents("$loggName.txt", $data, FILE_APPEND);
            }
            else if($loggingLevel>=3){
                if($loggingLevel==4){
                    $data.=("".$info);
                }
                file_put_contents("$controllerName.txt", $data, FILE_APPEND);
                file_put_contents("$loggName.txt", $data, FILE_APPEND);
            }
        }
        //Nivå 5 logger set og get funksjoner, logger ikke $info, logger alt til hovedloggen
        //Nivå 6 logger set og get funksjoner, logger $info, logger alt til hovedloggen
        //Nivå 7 logger kun set og get funksjoner, logger ikke $info, logger tilhørende controller fil og til hovedloggen
        //Nivå 8 logger kun set og get funksjoner, logger $info, logger tilhørende controller fil og til hovedloggen
        else if($loggingLevel>4){
            if($loggingLevel<=6){
                if($loggingLevel==6){
                    $data.=($cSV.$info);
                }
                file_put_contents("$loggName.txt", $data, FILE_APPEND);
            }
            else if($loggingLevel>=7){
                if($loggingLevel==8){
                    $data.=($cSV.$info);
                }
                file_put_contents("$controllerName.txt", $data, FILE_APPEND);
                file_put_contents("$loggName.txt", $data, FILE_APPEND);
            }
        }
        return new JsonResponse("test");
    }
}
