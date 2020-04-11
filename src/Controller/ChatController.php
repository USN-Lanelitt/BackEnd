<?php


namespace App\Controller;

use App\Entity\Assets;
use App\Entity\AssetTypes;
use App\Entity\Users;
use App\Entity\Chat;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Psr\Log\LoggerInterface;

$request = Request::createFromGlobals();

header("Access-Control-Allow-Origin: *");

class ChatController extends AbstractController{

    public function  getChats($userId){

        $conn=$this->getDoctrine()->getConnection();
        $sql="SELECT id FROM users WHERE id IN (SELECT DISTINCT(user2_id) FROM chat where user1_id=$userId)";
        $stmt=$conn->prepare($sql);
        $stmt->execute();

        $aUsersId = $stmt->fetchAll();

        $iIds = array_column($aUsersId, 'id');

        $users=$this->getDoctrine()->getRepository(Users::class)->findBy(array('id'=>$iIds));

        $info=($userId);
        UtilController::logging($userId, "writeMessage", "ChatController", "$info",0);

        return $this->json($users, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'userInfo'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getChat($userId1, $userId2){

        //hent chatt fra DB basert på brukerid1 og brukerid2
        $chatMessages=$this->getDoctrine()->getRepository(Chat::class)->findBy(array('user1' => array($userId1,$userId2), 'user2' => array($userId1,$userId2)));

        //if tom, returner tom

        //Logging funksjon
        $info=($userId1." - ".$userId2);
        UtilController::logging($userId1, "writeMessage", "ChatController", "$info",0);

        //returner chat sortert på tidspunkt sendt
        return $this->json($chatMessages, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'chat'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function writeMessage(Request $request, $userId1, $userId2){
        $content = json_decode($request->getContent());
        $message = $content->message;

        $user1=$this->getDoctrine()->getRepository(Users::class)->find($userId1);
        $user2=$this->getDoctrine()->getRepository(Users::class)->find($userId2);


        $chat=new Chat();
        $chat->setUser1($user1);
        $chat->setUser2($user2);
        $chat->setMessage($message);
        $chat->setTimestampSent(new \DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($chat);
        $entityManager->flush();

        //Logging funksjon
        $info=($userId1." - ".$userId2." - ".$message);
        UtilController::logging($userId1, "writeMessage", "ChatController", "$info",1);

        return $this->getChat($userId1, $userId2);
    }

    public function readMessage(){

    }

}