<?php

/*
 *Nicole har jobbet med denne filen
 *
 */
namespace App\Controller;

use App\Entity\AssetCategories;
use App\Entity\Assets;

use App\Entity\Loans;
use App\Entity\UserConnections;
use App\Entity\RequestStatus;
use App\Entity\Users;
use Cassandra\Date;
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


class LoanController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger){
        $this->logger=$logger;
    }

    public function sendLoanRequest(Request $request, $iUserId, $iAssetId)
    {
        //Sjekker om requesten har innhold
        $content=json_decode($request->getContent());
        if(empty($content)){
            return new JsonResponse('');
        }

        $this->logger->info(json_decode($request));

        //Henter info om lånet
        $dStart  = $content->startDate;
        $dEnd  = $content->endDate;

        $this->logger->info("iUserid: " .$iUserId. "assetid: " .$iAssetId. "start: " .$dStart. "end: " .$dEnd);

        $iStatusSent = 0;

        $oUser = $this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $oAsset = $this->getDoctrine()->getRepository(Assets::class)->find($iAssetId);
        $oStatusSent = $this->getDoctrine()->getRepository(RequestStatus::class)->find($iStatusSent);

        $entityManager = $this->getDoctrine()->getManager();


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
            $info=($iUserId." - ".$iAssetId." - ".$dStart." - ".$dEnd." - "."0");
            $this->forward('App\Controller\UtilController:logging',[
                'userId'=>$iUserId,
                'functionName'=>'sendLoanRequest',
                'controllerName'=>'LoanController',
                'info'=>$info,
                'change'=>1
            ]);

            return new JsonResponse('Låneforhold er opprettet');
        }



    public function getLoanRequest($iUserId){ //Henter alle mottatte forespørsler
        //Sjekker $iUserId for innhold
        if(empty($iUserId)){
            return new JsonResponse();
        }


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
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$iUserId,
            'functionName'=>'getLoanRequest',
            'controllerName'=>'LoanController',
            'info'=>$info,
            'change'=>0
        ]);


        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanRequest'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }


    public function replyLoanRequest($iUserId, $iLoanId, $iStatus) {
        //Sjekker $iUserId for innhold
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
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$iUserId,
            'functionName'=>'replyLoanRequest',
            'controllerName'=>'LoanController',
            'info'=>$info,
            'change'=>0
        ]);

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
        //Sjekker $iUserId for innhold
        if(empty($iUserId)){
            return new JsonResponse();
        }

        $oStatusAccepted = $this->getDoctrine()->getRepository(RequestStatus::class)->find(1);

        //Henter alle låne-objekter bruker har sendt med status = accepted
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findBy(array('users'=> $iUserId, 'statusLoan'=> $oStatusAccepted));

        //Logging funksjon
        $info=($iUserId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$iUserId,
            'functionName'=>'getAcceptedRequests',
            'controllerName'=>'LoanController',
            'info'=>$info,
            'change'=>0
        ]);

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanStatus', 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getSentRequests($iUserId) { //Henter alle forespørsler brukeren har sendt som ikke har blitt behandlet
        //Sjekker $iUserId for innhold
        if(empty($iUserId)){
            return new JsonResponse();
        }

        $oStatusSent = $this->getDoctrine()->getRepository(RequestStatus::class)->find(0);

        //Henter alle låne-objekter bruker har sendt med status = sent
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findBy(array('users'=> $iUserId, 'statusLoan'=> $oStatusSent));

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanStatus', 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getDeniedRequests($iUserId) { //Henter alle avviste forespørsler brukeren har sendt
        //Sjekker $iUserId
        if(empty($iUserId)){
            return new JsonResponse();
        }

        $oStatusDenied = $this->getDoctrine()->getRepository(RequestStatus::class)->find(2);

        //Henter alle låne-objekter bruker har sendt med status = denied
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findBy(array('users'=> $iUserId, 'statusLoan'=> $oStatusDenied));

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loanStatus', 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }
    public function getLoans($iUserId){ //Henter alle lån bruker har godkjent
        //Sjekker $iUserId innhold
        if(empty($iUserId)){
            return new JsonResponse();
        }

        $iStatusAccepted = 1;

        //Henter alle id'ene til lånene med eiendelen eid av brukeren med status "accepted"
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
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$iUserId,
            'functionName'=>'getLoans',
            'controllerName'=>'LoanController',
            'info'=>$info,
            'change'=>0
        ]);

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

            $aUnavailableDate[$teller]  = $aUnavailableDates[0]['dateStart'];
            $aUnavailableDate[$teller+1]  = $aUnavailableDates[0]['dateEnd'];
        }

        //Logging funksjon
        $info=("null");
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>-1,
            'functionName'=>'getAllUnavailableDates',
            'controllerName'=>'LoanController',
            'info'=>$info,
            'change'=>0
        ]);

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

            }

        }

        //Logging funksjon
        $info=($assetId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>-1,
            'functionName'=>'assetAvailability',
            'controllerName'=>'LoanController',
            'info'=>$info,
            'change'=>0
        ]);

        return new JsonResponse($ikkeLedig);
    }

}
