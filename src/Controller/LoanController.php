<?php

namespace App\Controller;

use App\Entity\AssetCategories;
use App\Entity\Assets;

use App\Entity\Loans;
use App\Entity\UserConnections;
use App\Entity\Users;
use DateInterval;
use DatePeriod;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Psr\Log\LoggerInterface;

$request = Request::createFromGlobals();

header("Access-Control-Allow-Origin: *");

class LoanController extends AbstractController
{
    private $logger;
    private $sStatusLoan = array("sent", "accepted", "denied", "available");

    public function __construct(LoggerInterface $logger){
        $this->logger=$logger;
    }

    public function sendLoanRequest(Request $request) {
        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());
        //if(empty($content)){
        //return new JsonResponse($content);
        //}

        //Henter info om lånet
        //$iUserId  = $content->userId;
        //$iAssetId  = $content->assetId;
        //$dStart  = $content->StartDate;
        //$dEnd  = $content->endDate;

        //HARDKODE
        $iUserId  = 1;
        $iAssetId  = 16;
        $dStart  = "2020-03-17";
        $dEnd  = "2020-04-17";
        $sStatusLoan = array("sent", "accepted", "denied", "available");

        $oUser = $this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $oAsset = $this->getDoctrine()->getRepository(Assets::class)->find($iAssetId);

        $entityManager = $this->getDoctrine()->getManager();

        //Sjekker om låneforholdet finnes fra før av
        $conn = $this->getDoctrine()->getConnection();
        $sql = "SELECT id FROM loans 
                WHERE users_id= $iUserId 
                  AND assets_id = $iAssetId 
                  AND status_loan not like '$sStatusLoan[3]'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $oConnectionId = $stmt->fetchAll();

        $check = empty($oConnectionId);

        /// trenger ikke dette
        
        //hvis ikke låneforholdet finnes
        if ($check){

            $dUNIXStart  = strtotime($dStart);
            $dUNIXEnd  = strtotime($dEnd);

            // Specify the start date. This date can be any English textual format
            /*$date_from = "2010-02-03";
            $date_from = strtotime($date_from); // Convert date to a UNIX timestamp

            // Specify the end date. This date can be any English textual format
            $date_to = "2010-09-10";
            $date_to = strtotime($date_to); // Convert date to a UNIX timestamp  */

            // Loop from the start date to end date and output all dates inbetween
            $ikkeLedig = array();
            $teller = 0;
            for ($i=$dUNIXStart; $i<=$dUNIXEnd; $i+=86400) {
                $teller += 1;
                $ikkeLedig[$teller] = date("Y-m-d", $i);
            }

            return new JsonResponse($ikkeLedig);
            // if(){ }
            //Oppretter lånefohold
            $oLoan = new Loans();
            $oLoan->setUsers($oUser);
            $oLoan->setAssets($oAsset);
            $oLoan->setDateStart(\DateTime::createFromFormat('Y-m-d',$dStart));
            $oLoan->setDateEnd(\DateTime::createFromFormat('Y-m-d',$dEnd));
            $oLoan->setStatusLoan($sStatusLoan[0]);

            $entityManager->persist($oLoan);
            $entityManager->flush();

            //return new JsonResponse('Låneforhold er opprettet');
        }
        return new JsonResponse('Låneforholdet finnes fra før');
    }


    public function getLoanRequest(Request $request){ //Henter alle mottatte forespørsler
        $sStatusLoan = array("sent", "accepted", "denied", "available");
        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());
        //if(empty($content)){
        //return new JsonResponse($content);
        //}
        /*if (!$events) {
            throw $this->createNotFoundException(
                'No event found'
            );
        }*/

        //$iUserId  = $content->userId;

        //HARDKODE
        $iUserId  = 1;

        //Henter alle id'ene til lånene med eiendelen eid av brukeren med status "sent"
        $conn = $this->getDoctrine()->getConnection();
        $sql = "SELECT id FROM loans 
                WHERE status_loan LIKE '$sStatusLoan[0]'
                    AND assets_id IN (SELECT id FROM assets WHERE users_id=$iUserId)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $iRequestIds = $stmt->fetchAll();

        $iIds = array_column($iRequestIds, 'id');
        $this->logger->info(json_encode($iIds));

        //Henter alle låne-objektene med status "sent"
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findBy(array('id'=> $iIds));

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanRequest'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getLoanRequestStatus(Request $request) { //Henter status på alle forespørsler bruker har sendt

        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());
        //if(empty($content)){
        //return new JsonResponse($content);
        //}

        //Henter info om låneforespørsel
        //$iUserId  = $content->userId;

        //HARDKODE
        $sStatusLoan = array("sent", "accepted", "denied", "available");
        $iUserId  = 1;

        //Henter alle låne-objektene der bruker har sendt en forespørsel
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findBy(array('users'=> $iUserId));

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            //ObjectNormalizer::ATTRIBUTES => [],
            ObjectNormalizer::GROUPS => ['groups' => 'loanStatus'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function replyLoanRequest(Request $request) {
        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());
        //if(empty($content)){
        //return new JsonResponse($content);
        //}

        //Henter info om lånet
        //$iStatus  = $content->newStatus;
        //$iLoanId  = $content->loanId;

        //HARDKODE
        $sStatusLoan = array("sent", "accepted", "denied", "available");
        $iStatus = 1;
        $sStatus  = $sStatusLoan[$iStatus];
        $iLoanId  = 2;

        //Henter alle låne-objektene
        $oLoan = $this->getDoctrine()->getRepository(Loans::class)->find($iLoanId);

        $entityManager = $this->getDoctrine()->getManager();

        //Hvis lånet har status "sent
        if($oLoan->getStatusLoan() == $sStatusLoan[0]){
            //Hvis bruker trykker godkjen (vi mottar $sStatusLoan[1])
            //skal status settes til $sStatusLoan[1]
            if($sStatus == $sStatusLoan[1]) {
                $oLoan->setStatusLoan($sStatusLoan[1]);
            }
            else {
                $oLoan->setStatusLoan($sStatusLoan[2]);
            }
            $entityManager->persist($oLoan);
            $entityManager->flush();

            return new JsonResponse('Lånestatus er endret');
        }
        return new JsonResponse('Forespørsel finnes ikke');
    }


    public function getAcceptedRequests(Request $request) { //Henter alle godkjente forespørsler brukeren har sendt
        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());
        //if(empty($content)){
        //return new JsonResponse($content);
        //}

        //Henter info om låneforespørsel
        //$iUserId  = $content->userId;

        //HARDKODE
        $sStatusLoan = array("sent", "accepted", "denied", "available");
        $iUserId  = 1;

        //Henter alle låne-objekter bruker har sendt med status = accepted
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findBy(array('users'=> $iUserId, 'statusLoan'=> $sStatusLoan[1]));

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanStatus'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getLoans(Request $request){ //Henter alle lån bruker har godkjent
        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());
        //if(empty($content)){
        //return new JsonResponse($content);
        //}

        //$iUserId  = $content->userId;

        //HARDKODE
        $sStatusLoan = array("sent", "accepted", "denied", "available");
        $iUserId  = 1;

        //Henter alle id'ene til lånene med eiendelen eid av brukeren med status "sent"
        $conn = $this->getDoctrine()->getConnection();
        $sql = "SELECT id FROM loans 
                WHERE status_loan LIKE '$sStatusLoan[1]'
                    AND assets_id IN (SELECT id FROM assets WHERE users_id=$iUserId)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $iRequestIds = $stmt->fetchAll();

        $iIds = array_column($iRequestIds, 'id');
        $this->logger->info(json_encode($iIds));

        //Henter alle låne-objektene med status "sent"
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findBy(array('id'=> $iIds));

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanRequest'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getAllUnavailableDates(){
        $assetId = 15;
        $aIds = $this->getDoctrine()->getRepository(Loans::class)->findAllAssetLoans($assetId);

        $aUnavailableDates = $aIds;
        $teller = 0;
        foreach($aUnavailableDates as $id) {
            $teller += 1;
            //$dates = array($id->getDateStart(), $id->getDateEnd());
            $aUnavailableDate[$teller]  = $aUnavailableDates[0]['dateStart'];
            $aUnavailableDate[$teller+1]  = $aUnavailableDates[0]['dateEnd'];
            //for ($i = $dUNIXStart; $i <= $dUNIXEnd; $i+=86400) {
                // $teller += 1;

                //$aUnavailableDate[$teller] = date("Y-m-d", $i);
                //}
           // }
        }

        return $this->json($aUnavailableDate, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanRequest'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function assetAvailability($assetId){
        $assetLoans=$this->getDoctrine()->getRepository(Loans::class)->findBy(array('assets'=>$assetId, 'statusLoan'=>1));

        $ikkeLedig = array();
        $teller = 0;
        if(!empty($assetLoans)) {
            foreach ($assetLoans as $assetLoan) {

                $dStart = $assetLoan->getDateStart();
                //return new JsonResponse($dStart);

                $dEnd = $assetLoan->getDateEnd();
                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($dStart, $interval, $dEnd->modify( '+1 day' ));

                foreach ($period as $dt) {
                    $teller += 1;
                    $ikkeLedig[$teller] = $dt->format("Y-m-d");
                }
                /*
                $dStart = strtotime($assetLoan->getDateStart()->format('Y-m-d'));
                $dEnd = strtotime($assetLoan->getDateEnd()->format('Y-m-d'));
                for ($i = $dStart; $i <= $dEnd; $i += 86400) {
                    $teller += 1;
                    $ikkeLedig[$teller] = date("Y-m-d", $i);
                }*/
            }

        }
        return new JsonResponse($ikkeLedig);
    }

}
