<?php

namespace App\Controller;

use App\Entity\AssetCategories;
use App\Entity\Assets;

use App\Entity\Loans;
use App\Entity\UserConnections;
use App\Entity\RequestStatus;
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

    public function __construct(LoggerInterface $logger){
        $this->logger=$logger;
    }

    public function sendLoanRequest(Request $request, $iUserId, $iAssetId) {
        //Sjekker om requesten har innhold
        $content=json_decode($request->getContent());
        if(empty($content)){
        return new JsonResponse($content);
        }

        //Henter info om lånet
        $dStart  = $content->StartDate;
        $dEnd  = $content->endDate;

        $iStatusSent = 0;

        $oUser = $this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $oAsset = $this->getDoctrine()->getRepository(Assets::class)->find($iAssetId);
        $oStatusSent = $this->getDoctrine()->getRepository(RequestStatus::class)->find($iStatusSent);

        $entityManager = $this->getDoctrine()->getManager();

        //Sjekker om låneforholdet finnes fra før
        $conn = $this->getDoctrine()->getConnection();
        $sql = "SELECT id FROM loans 
                WHERE users_id= $iUserId 
                  AND assets_id = $iAssetId 
                  AND status_loan_id not like 3";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $oConnectionId = $stmt->fetchAll();
        
        //hvis ikke låneforholdet finnes
        if (empty($oConnectionId)){


            //Oppretter lånefohold
            $oLoan = new Loans();
            $oLoan->setUsers($oUser);
            $oLoan->setAssets($oAsset);
            $oLoan->setDateStart(\DateTime::createFromFormat('Y-m-d',$dStart));
            $oLoan->setDateEnd(\DateTime::createFromFormat('Y-m-d',$dEnd));
            $oLoan->setStatusLoan($oStatusSent);

            $entityManager->persist($oLoan);
            $entityManager->flush();

            //Logging funksjon
            $info=($oUser." - ".$oAsset." - ".$oLoan." - ".$oLoan." - ".$oLoan);
            UtilController::logging($iUserId, "sendLoanRequest", "LoanController", "$info",1);

            return new JsonResponse('Låneforhold er opprettet');
        }
        return new JsonResponse('Låneforholdet finnes fra før');
    }


    public function getLoanRequest($iUserId){ //Henter alle mottatte forespørsler
        //Sjekker $iUserId
        if(empty($iUserId)){
            return new JsonResponse();
        }
        /*if (!$events) {
            throw $this->createNotFoundException(
                'No event found'
            );
        }*/

        $iStatusSent = 0;

        //Henter alle id'ene til lånene med eiendelen eid av brukeren med status "sent"
        $conn = $this->getDoctrine()->getConnection();
        $sql = "SELECT id FROM loans 
                WHERE status_loan_id LIKE '$iStatusSent'
                    AND assets_id IN (SELECT id FROM assets WHERE users_id=$iUserId)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $iRequestIds = $stmt->fetchAll();

        $iIds = array_column($iRequestIds, 'id');
        $this->logger->info(json_encode($iIds));

        //Henter alle låne-objektene med status "sent"
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findBy(array('id'=> $iIds));

        //Logging funksjon
        $info=("null");
        UtilController::logging($iUserId, "getLoanRequest", "LoanController", "$info",0);

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanRequest'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getLoanStatusSent($iUserId) { //Henter forespørsler bruker har sendt med status sent
        //Sjekker $iUserId
        if(empty($iUserId)){
            return new JsonResponse();
        }

        //Henter alle låne-objektene der bruker har sendt en forespørsel
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findAllStatusSent($iUserId);

        //Logging funksjon
        $info=("null");
        UtilController::logging($iUserId, "getLoanStatusSent", "LoanController", "$info",0);

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanStatus', 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getLoanStatusDenied($iUserId) { //Henter forespørsler bruker har sendt med status avvist
        //Sjekker $iUserId
        if(empty($iUserId)){
            return new JsonResponse();
        }

        //Henter alle låne-objektene der bruker har sendt en forespørsel
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findAllStatusDenied($iUserId);

        //Logging funksjon
        $info=("null");
        UtilController::logging($iUserId, "getLoanStatusDenied", "LoanController", "$info",0);

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanStatus', 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function replyLoanRequest($iUserId, $iLoanId, $iStatus) {
        //Sjekker $iUserId
        if(empty($iUserId)){
            return new JsonResponse();
        }

        //Henter alle låne-objektene
        $oLoan = $this->getDoctrine()->getRepository(Loans::class)->find($iLoanId);
        $oStatusSent = $this->getDoctrine()->getRepository(RequestStatus::class)->find(0);
        $oStatusAccepted = $this->getDoctrine()->getRepository(RequestStatus::class)->find(1);
        $oStatusDenied = $this->getDoctrine()->getRepository(RequestStatus::class)->find(2);

        $entityManager = $this->getDoctrine()->getManager();

        //Logging funksjon
        $info=($iLoanId." - ".$iStatus);
        UtilController::logging($iUserId, "replyLoanRequest", "LoanController", "$info",0);

        //Hvis lånet har status "sent
        if($oLoan->getStatusLoan() == $oStatusSent){
            //Hvis bruker trykker godkjen skal status settes til 1
            if($iStatus == 1) {
                $oLoan->setStatusLoan($oStatusAccepted);
        }
            else {
                $oLoan->setStatusLoan($oStatusDenied);
            }
            $entityManager->persist($oLoan);
            $entityManager->flush();

            return new JsonResponse('Lånestatus er endret');
        }
        return new JsonResponse('Forespørsel finnes ikke');
    }


    public function getAcceptedRequests($iUserId) { //Henter alle godkjente forespørsler brukeren har sendt
        //Sjekker $iUserId
        if(empty($iUserId)){
            return new JsonResponse();
        }

        $oStatusAccepted = $this->getDoctrine()->getRepository(RequestStatus::class)->find(1);

        //Henter alle låne-objekter bruker har sendt med status = accepted
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findBy(array('users'=> $iUserId, 'statusLoan'=> $oStatusAccepted));

        //Logging funksjon
        $info=($iUserId);
        UtilController::logging($iUserId, "getAcceptedRequests", "LoanController", "$info",0);

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanStatus', 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getLoans($iUserId){ //Henter alle lån bruker har godkjent
        //Sjekker $iUserId
        if(empty($iUserId)){
            return new JsonResponse();
        }

        $iStatusAccepted = 1;

        //Henter alle id'ene til lånene med eiendelen eid av brukeren med status "sent"
        $conn = $this->getDoctrine()->getConnection();
        $sql = "SELECT id FROM loans 
                WHERE status_loan_id = $iStatusAccepted
                    AND assets_id IN (SELECT id FROM assets WHERE users_id=$iUserId)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $iRequestIds = $stmt->fetchAll();

        $iIds = array_column($iRequestIds, 'id');
        $this->logger->info(json_encode($iIds));

        //Henter alle låne-objektene med status "sent"
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findBy(array('id'=> $iIds));

        //Logging funksjon
        $info=($iUserId);
        UtilController::logging($iUserId, "getLoans", "LoanController", "$info",0);

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanRequest'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    /*John*/
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

        //Logging funksjon
        $info=("null");
        UtilController::logging(-1, "getAllUnavailableDates", "LoanController", "$info",0);

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

        //Logging funksjon
        $info=($assetId);
        UtilController::logging(-1, "assetAvailability", "LoanController", "$info",0);

        return new JsonResponse($ikkeLedig);
    }

}
