<?php


/*
 *John har jobbet med denne filen
 *
 */
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

/*---John-Berge Grimaas---*/


class ChatController extends AbstractController{

    public function  getChats($userId){

        $conn=$this->getDoctrine()->getConnection();
        $sql="SELECT DISTINCT(id) FROM users WHERE id IN (SELECT DISTINCT(user2_id) FROM chat where user1_id=$userId) OR id IN (SELECT DISTINCT(user1_id) FROM chat where user2_id=$userId)";
        $stmt=$conn->prepare($sql);
        $stmt->execute();

        $aUsersId = $stmt->fetchAll();

        $iIds = array_column($aUsersId, 'id');

        $users=$this->getDoctrine()->getRepository(Users::class)->findBy(array('id'=>$iIds), array('firstName'=>'ASC'));

        $info=($userId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$userId,
            'functionName'=>'getChats',
            'controllerName'=>'ChatController',
            'info'=>$info,
            'change'=>0
        ]);

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
        $chatMessages=$this->getDoctrine()->getRepository(Chat::class)->findBy(
            array('user1' => array($userId1,$userId2), 'user2' => array($userId1,$userId2)),
            array('id' => 'ASC'));

        //if tom, oppret chat
        if(empty($chatMessages)){

            $user1=$this->getDoctrine()->getRepository(Users::class)->find($userId1);
            $user2=$this->getDoctrine()->getRepository(Users::class)->find($userId2);

            $chat=new Chat();
            $chat->setUser1($user1);
            $chat->setUser2($user2);
            $chat->setMessage($user1->getFirstName()." opprettet en chat med deg.");
            $chat->setTimestampSent(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($chat);
            $entityManager->flush();

            //Logging funksjon
            $info=($userId1." - ".$userId2." - "."Opprett chat");
            $this->forward('App\Controller\UtilController:logging',[
                'userId'=>$userId1,
                'functionName'=>'writeMessage',
                'controllerName'=>'ChatController',
                'info'=>$info,
                'change'=>1
        ]);

            return $this->getChat($userId1, $userId2);

        }
        else{
            //Logging funksjon
            $info=($userId1." - ".$userId2);
            $this->forward('App\Controller\UtilController:logging',[
                'userId'=>$userId1,
                'functionName'=>'getChat',
                'controllerName'=>'ChatController',
                'info'=>$info,
                'change'=>0
        ]);

            //returner chat sortert på tidspunkt sendt
            return $this->json($chatMessages, Response::HTTP_OK, [], [
                ObjectNormalizer::SKIP_NULL_VALUES => true,
                ObjectNormalizer::GROUPS => ['groups' => 'chat'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                }
            ]);
        }
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
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$userId1,
            'functionName'=>'writeMessage',
            'controllerName'=>'ChatController',
            'info'=>$info,
            'change'=>1
        ]);

        return $this->getChat($userId1, $userId2);
    }

    public function readMessage(){

    }

}