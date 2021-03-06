<?php

/*
 *John har jobbet med denne filen
 *
 */
namespace App\Controller;

use App\Entity\Assets;
use App\Entity\AssetTypes;
use App\Entity\Users;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Psr\Log\LoggerInterface;


/*---John-Berge Grimaas---*/


class AssetController extends AbstractController{

    private $logger;

    public function __construct(LoggerInterface $logger){
        $this->logger=$logger;
    }
    public function getUsersAssets($userId1, $userId2){

        $conn=$this->getDoctrine()->getConnection();
        $sql="SELECT id FROM assets 
              WHERE users_id LIKE $userId2
              AND published 
              AND ((users_id IN (SELECT user2_id FROM user_connections WHERE user1_id LIKE $userId1 AND request_status_id=1)  OR public LIKE TRUE) OR (users_id LIKE $userId1 ))";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
        /*Spørring som kjøres mot databasen som henter alle idene på eiendelene til userId2 som userId1 har lov til å se*/

        $aAssetId = $stmt->fetchAll();

        $iIds = array_column($aAssetId, 'id');

        //Henter alle asset objektene som userId1 har lov til å se
        $assets = $this->getDoctrine()->getRepository(Assets::class)->findBy(array('id' => $iIds));

        //Logging funksjon
        $info=($userId1." - ".$userId2);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$userId1,
            'functionName'=>'getUsersAssets',
            'controllerName'=>'AssetController',
            'info'=>$info,
            'change'=>0
        ]);

        return $this->json($assets, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }

    public function getAssetType($userId, $typeId){


        $conn=$this->getDoctrine()->getConnection();
        $sql="SELECT id FROM assets 
              WHERE asset_type_id LIKE $typeId /**/   
              AND published 
              AND (users_id IN (SELECT user2_id FROM user_connections WHERE user1_id LIKE $userId AND  request_status_id=1) OR (users_id LIKE $userId OR public LIKE TRUE))";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
        /*Spørring som kjøres mot databasen som henter alle idene på eiendelene som hører til typeId som userId1 har lov til å se*/

        $aAssetId = $stmt->fetchAll();

        $iIds = array_column($aAssetId, 'id');
        //$iIds = reset($aAssetId);

        //Henter alle asset objektene som userId1 har lov til å se
        $assets = $this->getDoctrine()->getRepository(Assets::class)->findBy(array('id' => $iIds));

        //Logging funksjon
        $info=($userId." - ".$typeId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$userId,
            'functionName'=>'getAssetType',
            'controllerName'=>'AssetController',
            'info'=>$info,
            'change'=>0
        ]);


        return $this->json($assets, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }
    public function getAssetSearch($userId, $search){


        $conn=$this->getDoctrine()->getConnection();
        $sql="SELECT id FROM assets 
              WHERE 
              (UPPER(asset_name) LIKE UPPER('%$search%') OR asset_type_id IN(SELECT id FROM asset_types WHERE asset_type LIKE UPPER('%$search%')))
              /**/ 
              AND published 
              AND (users_id IN (SELECT user2_id FROM user_connections WHERE user1_id LIKE $userId AND request_status_id=1) OR (users_id LIKE $userId OR public LIKE TRUE))";
        $stmt=$conn->prepare($sql);
        $stmt->execute();
        /*Spørring som kjøres mot databasen som henter alle idene på eiendelene som hører med navn eller type som inneholder 'search' som userId1 har lov til å se*/

        $aAssetId = $stmt->fetchAll();

        $iIds = array_column($aAssetId, 'id');
        //$iIds = reset($aAssetId);

        //Henter alle asset objektene som userId1 har lov til å se
        $assets = $this->getDoctrine()->getRepository(Assets::class)->findBy(array('id' => $iIds));


        //Logging funksjon
        $info=($userId." - ".$search);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$userId,
            'functionName'=>'getAssetSearch',
            'controllerName'=>'AssetController',
            'info'=>$info,
            'change'=>0
        ]);


        return $this->json($assets, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }

    public function getMyAssets($userId){

        //henter brukeren fra databasen
        $user=$this->getDoctrine()->getRepository(Users::class)->find($userId);
        if(empty($user)){
            //returnerer vis brukeren ikke eksisterer
            return new JsonResponse("empty");
        }
        //henter alle assetene til brukeren
        $assets=$user->getAssets();
        $aAssets = $assets->toArray();


        $d1=empty($aAssets);
        $d2="not empty";
        if($d1){
            //Vis brukeren ikke har assets, return empty
            $d2="empty";
        }

        //Logging funksjon
        $info=($userId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$userId,
            'functionName'=>'getMyAssets',
            'controllerName'=>'AssetController',
            'info'=>$info,
            'change'=>0
        ]);

        return $this->json($aAssets, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getAsset($assetId){

        /*henter en asset fra databasen*/
        $asset=$this->getDoctrine()->getRepository(Assets::class)->find($assetId);
        if(empty($asset)){
            return new JsonResponse($asset);
        }


        //Logging funksjon
        $info=($assetId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>-1,
            'functionName'=>'getAsset',
            'controllerName'=>'AssetController',
            'info'=>$info,
            'change'=>0
        ]);

        return $this->json($asset, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function addAsset(Request $request){
        $ut="\n\n**************************************************************************\n\n";
        $this->logger->info($ut);

        $this->logger->info("*****LALALALAL*******");

        $iUserId = $request->request->get('userId');
        $iTypeId = $request->request->get('typeId');
        $sAssetName = $request->request->get('assetName');
        $tDescription = $request->request->get('description');
        $iCondition = $request->request->get('condition');
        $bPublic = $request->request->get('public');
        //Henter ut all informasjonen om asseten fra requst

        if(!$request->files->get('file')){
            //Sjekk om at bildet eksisterer
            $ut="\n\n*******************d*******************************************************\n\n";
            return new JsonResponse(false);
        }
        $this->logger->info("*****LALALALAL2*******".$iUserId." - ".$sAssetName." - ".$bPublic);
        //*/

        $user=$this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $oAssetType=$this->getDoctrine()->getRepository(AssetTypes::class)->find($iTypeId);

        //oppreter ett database objekt
        $asset=new Assets();
        $asset->setUsers($user);
        $asset->setAssetType($oAssetType);
        $asset->setAssetname($sAssetName);
        $asset->setDescription($tDescription);
        $asset->setAssetCondition($iCondition);
        $asset->setPublic($bPublic);
        $asset->setPublished(false);

        //kjører objektet opp mot databasen
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($asset);
        $entityManager->flush();
        $id=$asset->getId();

        //Logging funksjon
        $info=($iUserId." - ".$iTypeId." - ".$sAssetName." - ".$tDescription." - ".$iCondition." - ".$bPublic);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$iUserId,
            'functionName'=>'addAsset',
            'controllerName'=>'AssetController',
            'info'=>$info,
            'change'=>1
            ]);

        //Setter bilde på asseten
        $this->forward('App\Controller\AssetImageController::addImage',[
            'userId'=>$iUserId,
            'assetId'=>$id,
            '$oRequest'=>$request]);

        return new JsonResponse($id);
    }
    public function editAsset(Request $request, $userId, $assetId){
        $asset=$this->getDoctrine()->getRepository(Assets::class)->findOneBy(array('id'=>$assetId, 'users'=>$userId));

        if(empty($asset)){
            //sjekker at asseten eksisterer
            return new JsonResponse($asset);
        }

        $content = json_decode($request->getContent());
        $iUserId = $content->users->id;
        $iTypeId = $content->assetType->id;
        $sAssetName = $content->assetName;
        $tDescription = $content->description;
        $iCondition = $content->assetCondition;
        $bPublic=$content->public;
        //henter data ut fra request

        //*
        $oUser=$this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $oAssetType=$this->getDoctrine()->getRepository(AssetTypes::class)->find($iTypeId);

        $asset->setUsers($oUser);
        //$asset->setAssetType($oAssetType);
        $asset->setAssetname($sAssetName);
        $asset->setDescription($tDescription);
        $asset->setAssetCondition($iCondition);
        $asset->setPublic($bPublic);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($asset);
        $entityManager->flush();
        //*/

        //Logging funksjon
        $info=($userId." - ".$assetId." - ".$iTypeId." - ".$sAssetName." - ".$tDescription." - ".$iCondition." - ".$bPublic);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$iUserId,
            'functionName'=>'editAsset',
            'controllerName'=>'AssetController',
            'info'=>$info,
            'change'=>1
        ]);

        return $this->json($asset, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }
    public function setPublished($userId, $assetId, $published){

        $asset=$this->getDoctrine()->getRepository(Assets::class)->find($assetId);
        //henter asseten fra databasen

        $user=$asset->getUsers();
        if(!($user->getId()==$userId)){
            //Sjekker att brukeren eiger asseten
            return new JsonResponse("Not correct user");
        }
        $asset->setPublished($published);
        //Setter asseten til published/unpublished og pusher det til databasen
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($asset);
        $entityManager->flush();
        $publisert="publisert";
        if(!$published){
            $publisert="upublisert";
        }
        return new JsonResponse("Eiendel ".$publisert);
    }
    public function removeAsset($assetId){

        $oAsset=$this->getDoctrine()->getRepository(Assets::class)->find($assetId);
        //henter asseten

        $user=$oAsset->getUsers();
        $userId=$user->getId();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($oAsset);
        $entityManager->flush();

        //Logging funksjon
        $info=($userId." - ".$assetId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$userId,
            'functionName'=>'removeAsset',
            'controllerName'=>'AssetController',
            'info'=>$info,
            'change'=>1
        ]);

        return new JsonResponse("Eiendel slettet");
    }
    public function getIndividAssetAmount($userId){//henter hvor mange assets en enkelt person har

        $assets=$this->getDoctrine()->getRepository(Assets::class)->findBy(array('users'=>$userId));
        $assetAmount=count($assets);

        //Logging funksjon
        $info=($userId." - ".$assetAmount);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>-1,
            'functionName'=>'getIndividAssetAmount',
            'controllerName'=>'AssetController',
            'info'=>$info,
            'change'=>0
        ]);

        return new JsonResponse($assetAmount);
    }
    public function getAssetAmount(){//Henter mengden assets som eksisterer

        $assets=$this->getDoctrine()->getRepository(Assets::class)->findAll();
        $assetAmount=count($assets);

        //Logging funksjon
        $info=($assetAmount);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>-1,
            'functionName'=>'getAssetAmount',
            'controllerName'=>'AssetController',
            'info'=>$info,
            'change'=>0
        ]);

        return new JsonResponse($assetAmount);
    }
}