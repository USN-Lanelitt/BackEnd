<?php

namespace App\Controller;

use App\Entity\RequestStatus;
use App\Entity\UserConnections;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Psr\Log\LoggerInterface;

$request = Request::createFromGlobals();

header("Access-Control-Allow-Origin: *");

class UserConnectionsController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger){
        $this->logger=$logger;
    }

    public function getFriend($iUserId, $iFriendId){
        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());

        //$iUserId = $request->headers->get('x-userid');
        //$this->logger->info("iUserid:" .$iUserId);

        if(empty($iUserId)){
            return new JsonResponse();
        }

        //Ser om de er venner
        $oFriend = $this->getDoctrine()->getRepository(UserConnections::class)->findFriend($iUserId, $iFriendId);

        //Logging funksjon
        $info=($iUserId." - ".$iFriendId);
        UtilController::logging($iUserId, "getFriend", "UserConnectionsController", "$info",0);

        //Skriver ut alle objektene
        return $this->json($oFriend, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            //ObjectNormalizer::ATTRIBUTES => ['firstName', 'lastName'],
            ObjectNormalizer::GROUPS => ['groups' => 'friendInfo'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getFriends($iUserId){
        if(empty($iUserId)){
            return new JsonResponse();
        }

        $oFriends = $this->getDoctrine()->getRepository(UserConnections::class)->findFriends($iUserId);

        //Logging funksjon
        $info=($iUserId);
        UtilController::logging($iUserId, "getFriends", "UserConnectionsController", "$info",0);

        //Skriver ut alle objektene
        return $this->json($oFriends, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'friendInfo'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

        $this->logger->info("getAllFriends");
    }

    public function sendFriendRequest($iUserId, $iFriendId){
        if(empty($iUserId)){
            return new JsonResponse();
        }

        $iStatusSent = 0;
        //Sjekker om forespørsel allerede har blitt sendt
        $oConnection = $this->getDoctrine()->getRepository(UserConnections::class)->findBy(array('user1'=>$iUserId, 'user2'=>$iFriendId));

        //hvis den ikke finnes
        if (empty($oConnection)) {
            $oUser1 = $this->getDoctrine()->getRepository(Users::class)->find($iUserId);
            $oUser2 = $this->getDoctrine()->getRepository(Users::class)->find($iFriendId);
            $oStatusSent = $this->getDoctrine()->getRepository(RequestStatus::class)->find($iStatusSent);

            $entityManager = $this->getDoctrine()->getManager();

            //Oppretter ny rad med senders info
            $userConn = new UserConnections();
            $userConn->setUser1($oUser1);
            $userConn->setUser2($oUser2);
            $userConn->setRequestStatus($oStatusSent);

            $entityManager->persist($userConn);
            $entityManager->flush();

            return new JsonResponse('sendt forsespørsel');

            $this->logger->info('sendFriendRequest');
        }

        //Logging funksjon
        $info=($iUserId." - ".$iFriendId);
        UtilController::logging($iUserId, "sendFriendRequest", "UserConnectionsController", "$info",1);

        return new JsonResponse('forespørsel er allerede sendt');
    }

    public function getFriendRequest($iUserId){
        if(empty($iUserId)){
            return new JsonResponse();
        }

        //Henter alle venneforespørsler
        $users = $this->getDoctrine()->getRepository(UserConnections::class)->findFriendRequest($iUserId);

        //Logging funksjon
        $info=($iUserId);
        UtilController::logging($iUserId, "getFriendRequest", "UserConnectionsController", "$info",0);

        //Skriver ut alle objektene
        return $this->json($users, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'friendRequestInfo'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
        $this->logger->info("getAllFriendRequest");
    }

    public function replyFriendRequest($iUserId, $iFriendId, $iStatus){

        if(empty($iUserId)){
            return new JsonResponse();
        }


        if(empty($iFriendId)){
            return new JsonResponse();
        }

        $statusSent = 0;

        //Henter user objektene
        $oUser1 = $this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $oUser2 = $this->getDoctrine()->getRepository(Users::class)->find($iFriendId);
        $oStatus = $this->getDoctrine()->getRepository(RequestStatus::class)->find($iStatus);

        //Henter forespørselen
        $oUserConn = $this->getDoctrine()->getRepository(UserConnections::class)->findOneBy(array('user1'=> $iFriendId, 'user2'=> $iUserId, 'requestStatus'=>$statusSent));

        //Logging funksjon
        $info=($iUserId." - ".$iFriendId." - ".$iStatus);
        UtilController::logging($iUserId, "replyFriendRequest", "UserConnectionsController", "$info",1);

        //Sjekker om forespørsel har blitt sendt
        if ($oUserConn) {
            $entityManager = $this->getDoctrine()->getManager();

            //Hvis bruker trykker godkjenn (vi mottar 1), settes status til 1(accepted)
            if($iStatus == 1) {
                $oUserConn->setRequestStatus($oStatus);
                $oUserConn->setTimestamp(new \DateTime());
                $entityManager->persist($oUserConn);

                //Oppretter ny rad med mottakers info
                $oUserConn2 = new UserConnections();
                $oUserConn2->setUser1($oUser1);
                $oUserConn2->setUser2($oUser2);
                $oUserConn2->setRequestStatus($oStatus);
                $oUserConn2->setTimestamp(new \DateTime());
                $entityManager->persist($oUserConn2);
                $entityManager->flush();

                return new JsonResponse('Vennskap er opprettet');
                $this->logger->info('newfriend');
            }
            else {
                //Sletter forespørsel
                $entityManager->remove($oUserConn);
                $entityManager->flush();

                return new JsonResponse('Venneforespørsel er ikke godtatt og forespørsel slettet');
                $this->logger->info('deniedfriend');

            }

        }
        return new JsonResponse('Finner ikke forespørsel');
    }

    public function deleteFriendship($iUserId, $iFriendId){
        if(empty($iUserId)){
            return new JsonResponse();
        }

        $conn = $this->getDoctrine()->getConnection();

        $sql = "SELECT id FROM user_connections WHERE  (user1_id = $iUserId AND user2_id = $iFriendId)
                   OR (user1_id = $iFriendId and user2_id = $iUserId)";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $oConnectionId = $stmt->fetchAll();

        $ids = array_column($oConnectionId, 'id');
        $this->logger->info(json_encode($ids));

        //Logging funksjon
        $info=($iUserId." - ".$iFriendId);
        UtilController::logging($iUserId, "deleteFriendship", "UserConnectionsController", "$info",1);

        //Sjekker om vennskapet finnes
        if ($oConnectionId) {
            $id1 = $ids[0];
            $id2 = $ids[1];

            $oUser1 = $this->getDoctrine()->getRepository(UserConnections::class)->find($id1);
            $oUser2 = $this->getDoctrine()->getRepository(UserConnections::class)->find($id2);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($oUser1);
            $entityManager->remove($oUser2);
            $entityManager->flush();
            return new JsonResponse('slettet');
        }
            return new JsonResponse('vennskap finnes ikke');
    }

    public function getUserSearch($iUserId, $sSearch){

        if(empty($iUserId)){
            return new JsonResponse();
        }

        //Finner alle brukere med fornavn, mellomnavn, etternavn eller nickname som matcher søket
        $conn = $this->getDoctrine()->getConnection();
        $sql = "SELECT * FROM users WHERE first_name LIKE '%$sSearch%'
                    OR middle_name LIKE '%$sSearch%'
                    OR last_name LIKE '%$sSearch%'
                    OR nickname LIKE '%$sSearch%'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $oIds = $stmt->fetchAll();

        $iIds = array_column($oIds, 'id');
        $this->logger->info(json_encode($iIds));

        //Henter alle objektene
        $users = $this->getDoctrine()->getRepository(Users::class)->findBy(array('id' => $iIds));

        //Logging funksjon
        $info=($iUserId." - ".$sSearch);
        UtilController::logging($iUserId, "getUserSearch", "UserConnectionsController", "$info",0);

        return $this->json($users, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'friendInfo'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }
}
