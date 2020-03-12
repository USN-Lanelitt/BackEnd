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

    public function __construct(LoggerInterface $logger){
        $this->logger=$logger;
    }

    public function newLoan(Request $request) {
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

        $oUser = $this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $oAsset = $this->getDoctrine()->getRepository(Assets::class)->find($iAssetId);

        $entityManager = $this->getDoctrine()->getManager();

        //Sjekker om låneforholdet finnes fra før av
        $conn = $this->getDoctrine()->getConnection();
        $sql = "SELECT id FROM loans WHERE users_id= $iUserId AND status_loan=true";
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
            $oLoan->setStatusLoan(true);

            $entityManager->persist($oLoan);
            $entityManager->flush();

            return new JsonResponse('Låneforhold er opprettet');
        }
        return new JsonResponse('Låneforholdet finnes fra før');

}

/*public function getAllUsers() {
    $individer = $this->getDoctrine()->getRepository(Users::class)->findAll();

    return $this->json($individer, Response::HTTP_OK, [], [
        ObjectNormalizer::SKIP_NULL_VALUES => true,
        ObjectNormalizer::ATTRIBUTES => ['firstName', 'lastName'],
        ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
            return $object->getId();
        }
    ]);

    $this->logger->info("getAllUsers");
}

public function getAllFriends(Request $request){
    //Sjekker om requesten har innehold
    //$content=json_decode($request->getContent());
    //if(empty($content)){
        //return new JsonResponse($content);
    //}

    //Henter id til bruker
    //$iUserId1  = $content->userId1;

    //Kan hende jeg må søke finne bruker med telefon/mail

    $iUserId1  = 1;

    //Henter alle venner
    $conn = $this->getDoctrine()->getConnection();
    $sql = "SELECT user2_id FROM user_connections WHERE user1_id= $iUserId1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $aUsersId = $stmt->fetchAll();

    //Henter id'ene til alle vennene
    $iIds = array_column($aUsersId, 'user2_id');
    $this->logger->info(json_encode($iIds));

    //Henter alle venner-objektene
    $users = $this->getDoctrine()->getRepository(Users::class)->findBy(array('id' => $iIds));

    //Skriver ut alle objektene
    return $this->json($users, Response::HTTP_OK, [], [
        ObjectNormalizer::SKIP_NULL_VALUES => true,
        ObjectNormalizer::ATTRIBUTES => ['firstName', 'lastName'],
        ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
            return $object->getId();
        }
    ]);

    $this->logger->info("getAllFriends");
}

public function getFriend(Request $request){
    //Vet ikke hva som trengs av informasjon her
    //Sjekker om requesten har innehold
    //$content=json_decode($request->getContent());
    //if(empty($content)){
    //return new JsonResponse($content);
    //}

    //Henter id til bruker og venn
    //$iUserId1  = $content->userId1;
    //$iUserId2  = $content->userId2;

    $iUserId1  = 1;
    $iUserId2  = 2;

    $user = $this->getDoctrine()->getRepository(Users::class)->find($iUserId2);

    //Skriver ut alle objektene
    return $this->json($user, Response::HTTP_OK, [], [
        ObjectNormalizer::SKIP_NULL_VALUES => true,
        ObjectNormalizer::ATTRIBUTES => ['firstName', 'lastName'],
        ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
            return $object->getId();
        }
    ]);
}

public function sendFriendRequest(Request $request){
    //Sjekker om requesten har innehold
    //$content=json_decode($request->getContent());
    //if(empty($content)){
    //return new JsonResponse($content);
    //}

    //Hent id til bruker og brukeren som skal få venneforespørsel
    //$iUserId1  = $content->userId1;
    //$iUserId2  = $content->userId2;

    $iUserId1  = 1;
    $iUserId2  = 4;

    //Finner user_connections rad
    $conn = $this->getDoctrine()->getConnection();
    $sql = "SELECT id FROM user_connections WHERE user1_id= $iUserId1 AND user2_id=$iUserId2";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $oConnectionId = $stmt->fetchAll();

    $check = empty($oConnectionId);

    //Sjekker om forespørsel allerede har blitt sendt (hvis tom)
    if ($check) {

        $user1 = $this->getDoctrine()->getRepository(Users::class)->find($iUserId1);
        $user2 = $this->getDoctrine()->getRepository(Users::class)->find($iUserId2);

        $entityManager = $this->getDoctrine()->getManager();

        //Oppretter ny rad med senders info
        $userConn = new UserConnections();
        $userConn->setUser1($user1);
        $userConn->setUser2($user2);
        $userConn->setRequestStatus(false);
        $userConn->setTimestamp(new \DateTime());

        $entityManager->persist($userConn);
        $entityManager->flush();

        return new JsonResponse('sendt forsespørsel');

        $this->logger->info('sendFriendRequest');
    }
    return new JsonResponse('forespørsel er allerede sendt');
}

public function getAllFriendRequest(Request $request){
    //Sjekker om requesten har innehold
    //$content=json_decode($request->getContent());
    //if(empty($content)){
    //return new JsonResponse($content);
    //}

    //Henter brukers id
    //$iUserId1  = $content->userId1;

    //$oRequests = $this->getDoctrine()->getRepository(UserConnections::class)->findBy(array('user1' => $iUserId1),array('user1' => 'ASC'),1 ,0)[0];

    $iUserId1  = 1;

    //Henter alle venner
    $conn = $this->getDoctrine()->getConnection();
    $sql = "SELECT user2_id FROM user_connections WHERE user1_id= $iUserId1 AND request_status = false";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $aUsersId = $stmt->fetchAll();

    //Henter id'ene til alle vennene
    $iIds = array_column($aUsersId, 'user2_id');
    $this->logger->info(json_encode($iIds));

    //Henter alle venner-objektene
    $users = $this->getDoctrine()->getRepository(Users::class)->findBy(array('id' => $iIds));

    //Skriver ut alle objektene
    return $this->json($users, Response::HTTP_OK, [], [
        ObjectNormalizer::SKIP_NULL_VALUES => true,
        ObjectNormalizer::ATTRIBUTES => ['firstName', 'lastName'],
        ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
            return $object->getId();
        }
    ]);
    $this->logger->info("getAllFriendRequest");
}

public function newFriendship(Request $request){
    //Sjekker om requesten har innehold
    //$content=json_decode($request->getContent());
    //if(empty($content)){
    //return new JsonResponse($content);
    //}

    //Henter brukers id og id'ene til bruker som har sendt venneforespørsel
    //$iUserId1  = $content->userId1;
    //$iUserId2  = $content->userId2;

    $iUserId1  = 4;
    $iUserId2  = 1;

    $user1 = $this->getDoctrine()->getRepository(Users::class)->find($iUserId1);
    $user2 = $this->getDoctrine()->getRepository(Users::class)->find($iUserId2);

    $entityManager = $this->getDoctrine()->getManager();

    //Finner user_connections rad
    $conn = $this->getDoctrine()->getConnection();
    $sql = "SELECT id FROM user_connections WHERE user1_id= $iUserId2 AND user2_id=$iUserId1 AND request_status=false";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $oConnectionId = $stmt->fetchAll();

    //Henter id til bruker som har sendt forespørselen
    $iId=reset($oConnectionId);
    $this->logger->info(json_encode('gggggggg'));

    $check = empty($oConnectionId);

    //Sjekker om forespørsel har blitt sendt (hvis ikke tom)
    if (!$check){
        //Endrer request status til sann
        $userConn = $this->getDoctrine()->getRepository(UserConnections::class)->find($iId);
        $userConn->setRequestStatus(true);
        $entityManager->persist($userConn);
        $entityManager->flush();

        //Oppretter ny rad med mottakers info
        $userConn2 = new UserConnections();
        $userConn2->setUser1($user1);
        $userConn2->setUser2($user2);
        $userConn2->setRequestStatus(true);
        $userConn2->setTimestamp(new \DateTime());

        $entityManager->persist($userConn2);
        $entityManager->flush();

        return new JsonResponse('Vennskap er opprettet');
        $this->logger->info('newfriend');
    }
    return new JsonResponse('Finner ikke forespørsel');
}


public function deleteFriendship(Request $request){
    //Sjekker om requesten har innehold
    //$content=json_decode($request->getContent());
    //if(empty($content)){
    //return new JsonResponse($content);
    //}

    //Henter brukers id og id'ene til bruker som har sendt venneforespørsel
    //$iUserId1  = $content->userId1;
    //$iUserId2  = $content->userId2;

    $iUserId1  = 1;
    $iUserId2  = 4;

    $conn = $this->getDoctrine()->getConnection();

    $sql = "SELECT id FROM user_connections WHERE  (user1_id = $iUserId1 AND user2_id = $iUserId2)
               OR (user1_id = $iUserId2 and user2_id = $iUserId1)";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $oConnectionId = $stmt->fetchAll();

    $ids = array_column($oConnectionId, 'id');
    $this->logger->info(json_encode($ids));

    $check = empty($oConnectionId);

    //Sjekker om forespørsel allerede har blitt sendt (hvis tom)
    if (!$check) {

        $id1 = $ids[0];
        $id2 = $ids[1];

        $user1 = $this->getDoctrine()->getRepository(UserConnections::class)->find($id1);
        $user2 = $this->getDoctrine()->getRepository(UserConnections::class)->find($id2);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user1);
        $entityManager->remove($user2);
        $entityManager->flush();
        return new JsonResponse('slettet');

    }
        return new JsonResponse('vennskap finnes ikke');
}

public function getUserSearch(Request $request){
    //Sjekker om requesten har innehold
    //$content=json_decode($request->getContent());
    //if(empty($content)){
    //return new JsonResponse($content);
    //}

    //Henter brukers id og id'ene til bruker som har sendt venneforespørsel
    //$sSearch = $content->search;

    $sSearch = "nic";

    $conn = $this->getDoctrine()->getConnection();
    $sql = "SELECT id FROM users WHERE first_name LIKE '%$sSearch%'
                OR last_name LIKE '%$sSearch%'
                OR nickname LIKE '%$sSearch%'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $oIds = $stmt->fetchAll();

    $iIds = reset($oIds);

    //Henter alle objektene
    $users = $this->getDoctrine()->getRepository(Users::class)->findBy(array('id' => $iIds));

    return $this->json($users, Response::HTTP_OK, [], [
        ObjectNormalizer::SKIP_NULL_VALUES => true,
        ObjectNormalizer::ATTRIBUTES => ['firstName', 'lastName'],
        ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
            return $object->getId();
        }
    ]);
    $this->logger->info("getUserSearch");
}*/
}