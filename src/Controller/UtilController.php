<?php

namespace App\Controller;

use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UtilController extends AbstractController
{
    /**
     * @Route("/util", name="util")
     */
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
    public static function getLogg($userId, $loggName){

        //$loggName="logg";
        $myfile = fopen("$loggName.txt", "r") or die("Unable to open file!");
        $logg=array();
        $teller=0;
        while(!feof($myfile)) {
            $logg[$teller]= fgets($myfile);
            $teller++;
        }
        fclose($myfile);
        return new JsonResponse($logg);

    }
    public static function logging($userId, $functionName, $controllerName, $info, $change){
        $loggName="logg";
        $loggingLevel=8;
        $cSV=";";
        $timeStamp=new \DateTime();
        $data=("\n".$userId.$cSV.$functionName.$cSV.$controllerName.$cSV.$timeStamp->format('Y-m-d H:i:s'));
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
    }
}
