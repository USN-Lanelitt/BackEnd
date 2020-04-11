<?php

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

$request = Request::createFromGlobals();

header("Access-Control-Allow-Origin: *");

class AssetController extends AbstractController{

    private $logger;

    public function __construct(LoggerInterface $logger){
        $this->logger=$logger;
    }
    public function getUsersAssets($userId1, $userId2){

        //Sjekk at tingen ikke er lånt ut

        $conn=$this->getDoctrine()->getConnection();
        $sql="SELECT id FROM assets 
              WHERE users_id LIKE $userId2
              AND (users_id IN (SELECT user2_id FROM user_connections WHERE user1_id LIKE $userId1) OR (users_id LIKE $userId1 OR public LIKE TRUE))";
        $stmt=$conn->prepare($sql);
        $stmt->execute();

        $aAssetId = $stmt->fetchAll();

        $iIds = array_column($aAssetId, 'id');
        //$iIds = reset($aAssetId);

        //Henter alle objektene
        $assets = $this->getDoctrine()->getRepository(Assets::class)->findBy(array('id' => $iIds));

        //Logging funksjon
        $info=($userId1." - ".$userId2);
        UtilController::logging($userId1, "getUsersAssets", "AssetController", "$info",0);

        return $this->json($assets, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }
    public function getAssetSearch($userId, $search){

        //Sjekk at tingen ikke er lånt ut

        $conn=$this->getDoctrine()->getConnection();
        $sql="SELECT id FROM assets 
              WHERE UPPER(asset_name) LIKE UPPER('%$search%') /**/   
              AND (users_id IN (SELECT user2_id FROM user_connections WHERE user1_id LIKE $userId) OR (users_id LIKE $userId OR public LIKE TRUE))";
        $stmt=$conn->prepare($sql);
        $stmt->execute();

        $aAssetId = $stmt->fetchAll();

        $iIds = array_column($aAssetId, 'id');
        //$iIds = reset($aAssetId);

        //Henter alle objektene
        $assets = $this->getDoctrine()->getRepository(Assets::class)->findBy(array('id' => $iIds));


        //Logging funksjon
        $info=($userId." - ".$search);
        UtilController::logging($userId, "getAssetSearch", "AssetController", "$info",0);


        return $this->json($assets, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }

    public function getMyAssets($userId){


        $user=$this->getDoctrine()->getRepository(Users::class)->find($userId);
        if(empty($user)){
            return new JsonResponse("empty");
        }
        $assets=$user->getAssets();
        $aAssets = $assets->toArray();


        $d1=empty($aAssets);
        $d2="not empty";
        if($d1){
            $d2="empty";
        }

        //Logging funksjon
        $info=($userId);
        UtilController::logging($userId, "getMyAssets", "AssetController", "$info",0);

        return $this->json($aAssets, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getAsset($assetId){

        $asset=$this->getDoctrine()->getRepository(Assets::class)->find($assetId);
        if(empty($asset)){
            return new JsonResponse($asset);
        }


        //Logging funksjon
        $info=($assetId);
        UtilController::logging(-1, "getAsset", "AssetController", "$info",0);

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
        $content = json_decode($request->getContent());

        $iUserId = $content->userId;
        $iTypeId = $content->typeId;
        $sAssetName = $content->assetName;
        $tDescription = $content->description;
        $iCondition = $content->condition;
        $bPublic=$content->public;

        $user=$this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $oAssetType=$this->getDoctrine()->getRepository(AssetTypes::class)->find($iTypeId);

        $asset=new Assets();
        $asset->setUsers($user);
        $asset->setAssetType($oAssetType);
        $asset->setAssetname($sAssetName);
        $asset->setDescription($tDescription);
        $asset->setAssetCondition($iCondition);
        $asset->setPublic($bPublic);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($asset);
        $entityManager->flush();


        //Logging funksjon
        $info=($iUserId." - ".$iTypeId." - ".$sAssetName." - ".$tDescription." - ".$iCondition." - ".$bPublic);
        UtilController::logging($iUserId, "addAsset", "AssetController", "$info",1);

        return new JsonResponse("Eiendel lagd til");
    }

    public function editAsset(Request $request, $userId, $assetId){
        $asset=$this->getDoctrine()->getRepository(Assets::class)->findOneBy(array('id'=>$assetId, 'users'=>$userId));

        if(empty($asset)){
            return new JsonResponse($asset);
        }

        $content = json_decode($request->getContent());
        $iUserId = $content->users->id;
        $iTypeId = $content->assetType->id;
        $sAssetName = $content->assetName;
        $tDescription = $content->description;
        $iCondition = $content->assetCondition;
        $bPublic=$content->public;

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
        UtilController::logging($iUserId, "editAsset", "AssetController", "$info",1);

        return $this->json($asset, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'asset'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }
    public function removeAsset($assetId){

        $oAsset=$this->getDoctrine()->getRepository(Assets::class)->find($assetId);

        $user=$oAsset->getUsers();
        $userId=$user->getId();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($oAsset);
        $entityManager->flush();

        //Logging funksjon
        $info=($userId." - ".$assetId);
        UtilController::logging($userId, "removeAsset", "AssetController", "$info",1);

        return new JsonResponse("Eiendel slettet");
    }
    public function getIndividAssetAmount($userId){

        $assets=$this->getDoctrine()->getRepository(Assets::class)->findBy(array('users'=>$userId));
        $assetAmount=count($assets);

        //Logging funksjon
        $info=($userId." - ".$assetAmount);
        UtilController::logging(-1, "getIndividAssetAmount", "AssetController", "$info",0);

        return new JsonResponse($assetAmount);
    }
    public function getAssetAmount(){

        $assets=$this->getDoctrine()->getRepository(Assets::class)->findAll();
        $assetAmount=count($assets);

        //Logging funksjon
        $info=($assetAmount);
        UtilController::logging(-1, "getAssetAmount", "AssetController", "$info",0);

        return new JsonResponse($assetAmount);
    }
}