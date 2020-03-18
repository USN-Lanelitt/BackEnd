<?php

namespace App\Controller;

use App\Entity\AssetCategories;
use App\Entity\Assets;

use App\Entity\Loans;
use App\Entity\UserConnections;
use App\Entity\Users;
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

    public function getAllLoanRequest(Request $request){ //Henter alle mottatte forespørsler
        $sStatusLoan = array("sent", "accepted", "denied", "available");
        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());
        //if(empty($content)){
        //return new JsonResponse($content);
        //}

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

    public function changeLoanStatus(Request $request) {
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
        $iAssetId  = 2;
        $dStart  = new\DateTime();
        $dEnd  = new\DateTime();
        $sStatusLoan = array("sent", "accepted", "denied", "available");

        $oUser = $this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $oAsset = $this->getDoctrine()->getRepository(Assets::class)->find($iAssetId);

        $entityManager = $this->getDoctrine()->getManager();

        //Sjekker om låneforholdet finnes fra før av
        $conn = $this->getDoctrine()->getConnection();
        $sql = "SELECT id FROM loans WHERE users_id= $iUserId AND assets_id = $iAssetId AND status_loan not like '$sStatusLoan[3]'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $oConnectionId = $stmt->fetchAll();

        $check = empty($oConnectionId);

        //hvis ikke låneforholdet finnes
        if ($check){
            //Oppretter lånefohold
            $oLoan = new Loans();
            $oLoan->setUsers($oUser);
            $oLoan->setAssets($oAsset);
            $oLoan->setDateStart($dStart);
            $oLoan->setDateEnd($dEnd);
            $oLoan->setStatusLoan($sStatusLoan[0]);

            $entityManager->persist($oLoan);
            $entityManager->flush();

            return new JsonResponse('Låneforhold er opprettet');
        }
        return new JsonResponse('Låneforholdet finnes fra før');
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

    public function getAcceptedRequest(Request $request) { //Henter alle godkjente forespørsler brukeren har sendt
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

    public function getAllLoans(Request $request){ //Henter alle lån bruker har godkjent
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

}