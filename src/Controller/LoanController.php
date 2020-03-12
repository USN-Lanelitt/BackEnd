<?php

namespace App\Controller;

use App\Entity\AssetCategories;
use App\Entity\Assets;
use App\Entity\IndividConnections;

use App\Entity\Loans;
use App\Entity\UserConnections;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use App\Entity\UserConnectionsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
        $sql = "SELECT * FROM loans 
                WHERE status_loan LIKE 'sent'
                    AND assets_id IN (SELECT id FROM assets WHERE users_id=$iUserId)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $oRequestId = $stmt->fetchAll();

        return $this->json($oRequestId, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::ATTRIBUTES => ['firstName', 'lastName'],
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
        //$dStart  = $content->date_start;
        //$dEnd  = $content->date_end;

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

}