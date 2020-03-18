<?php

namespace App\Controller;

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


    public function getAllFriends(Request $request){
        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());
        //if(empty($content)){
            //return new JsonResponse($content);
        //}

        //Henter id til bruker
        //$iUserId1  = $content->userId1;

        //Kan hende jeg må søke finne bruker med telefon/mail

        //HARDKODE
        $iUserId1  = 1;

        $oFirnds = $this->getDoctrine()->getRepository(UserConnections::class)->findFriends($iUserId1);

        //Skriver ut alle objektene
        return $this->json($oFirnds, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'friendInfo'],
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

        //HARDKODE
        $iUserId2  = 2;

        $user = $this->getDoctrine()->getRepository(Users::class)->find($iUserId2);

        //Skriver ut alle objektene
        return $this->json($user, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            //ObjectNormalizer::ATTRIBUTES => ['firstName', 'lastName'],
            ObjectNormalizer::GROUPS => ['groups' => 'friendInfo'],
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

        //HARDKODE
        $iUserId1  = 1;
        $iUserId2  = 3;

        $oConnection = $this->getDoctrine()->getRepository(UserConnections::class)->findBy(array('user1'=>$iUserId1, 'user2'=>$iUserId2));

        $check = empty($oConnection);

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

        //HARDKODE
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
            //ObjectNormalizer::ATTRIBUTES => ['firstName', 'lastName'],
            ObjectNormalizer::GROUPS => ['groups' => 'friendInfo'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
        $this->logger->info("getAllFriendRequest");
    }

    public function replyFriendRequest (Request $request){
        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());
        //if(empty($content)){
        //return new JsonResponse($content);
        //}

        //Henter brukers id og id'ene til bruker som har sendt venneforespørsel
        //$iUserId1  = $content->userId1;
        //$iUserId2  = $content->userId2;
        //$iStatus  = $content->newStatus;

        //HARDKODE
        $iUserId1  = 1;
        $iUserId2  = 3;
        $iStatus  = 0;

        //Henter objektene til brukerne
        $oUser1 = $this->getDoctrine()->getRepository(Users::class)->find($iUserId1);
        $oUser2 = $this->getDoctrine()->getRepository(Users::class)->find($iUserId2);

        //Henter Forespørselen
        $oUserConn = $this->getDoctrine()->getRepository(UserConnections::class)->findOneBy(array('user1'=> $iUserId2, 'user2'=> $iUserId1, 'requestStatus'=>0));

        $check = empty($oUserConn);

        //Sjekker om forespørsel har blitt sendt (hvis ikke tom)
        if (!$check) {
            $entityManager = $this->getDoctrine()->getManager();
            //Hvis bruker trykker godkjen (vi mottar 1), settes status til 1
            if($iStatus == 1) {
                $oUserConn->setRequestStatus(1);
                $entityManager->persist($oUserConn);

                //Oppretter ny rad med mottakers info
                $oUserConn2 = new UserConnections();
                $oUserConn2->setUser1($oUser1);
                $oUserConn2->setUser2($oUser2);
                $oUserConn2->setRequestStatus(1);
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



    public function deleteFriendship(Request $request){
        //Sjekker om requesten har innehold
        //$content=json_decode($request->getContent());
        //if(empty($content)){
        //return new JsonResponse($content);
        //}

        //Henter brukers id og id'ene til bruker som har sendt venneforespørsel
        //$iUserId1  = $content->userId1;
        //$iUserId2  = $content->userId2;

        //HARDKODE
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

        //Sjekker om vennskapet finnes (hvis ikke tom)
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

        //HARDKODE
        $sSearch = "n";

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

        return $this->json($users, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'friendInfo'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }
}