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

    public function getAllLoanRequest(Request $request){
        $sStatusLoan = array("sent", "accepted", "denied", "available");
        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());
        //if(empty($content)){
        //return new JsonResponse($content);
        //}

        //$iUserId  = $content->userId;
        $iUserId  = 1;

        $conn = $this->getDoctrine()->getConnection();
        $sql = "SELECT id FROM loans 
                WHERE status_loan LIKE 'sent'
                    AND assets_id IN (SELECT id FROM assets WHERE users_id=$iUserId)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $iRequestIds = $stmt->fetchAll();

        $iIds = array_column($iRequestIds, 'id');
        $this->logger->info(json_encode($iIds));

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
        //$iUserId  = $content->userId;
        //$iLoanId  = $content->loanId;

        $sStatusLoan = array("sent", "accepted", "denied", "available");
        $sStatus  = $sStatusLoan[1];
        $iLoanId  = 2;

        $oLoan = $this->getDoctrine()->getRepository(Loans::class)->find($iLoanId);

        $entityManager = $this->getDoctrine()->getManager();

        if($oLoan->getStatusLoan() == $sStatusLoan[0]){
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

    public function getLoanRequestStatus(Request $request) {

        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());
        //if(empty($content)){
        //return new JsonResponse($content);
        //}

        $sStatusLoan = array("sent", "accepted", "denied", "available");

        //Henter info om låneforespørsel
        //$iUserId  = $content->userId;

        $iUserId  = 1;

        //$oUser = $this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $oRequestIds = $this->getDoctrine()->getRepository(Loans::class)->findBy(array('users'=> $iUserId));

        //Sjekker om bruker har sendt forespørsel
        /*$conn = $this->getDoctrine()->getConnection();
        $sql = "SELECT id FROM loans WHERE users_id= $iUserId";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $oRequestId = $stmt->fetchAll();*/

        return $this->json($oRequestIds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            //ObjectNormalizer::ATTRIBUTES => [],
            ObjectNormalizer::GROUPS => ['groups' => 'loanStatus'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }

}