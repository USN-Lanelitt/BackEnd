<?php

namespace App\Controller;


use App\Entity\AssetCategories;
use App\Entity\Assets;
use App\Entity\IndividConnections;
use App\Entity\Individuals;

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

class AssetController extends AbstractController{

    private $logger;

    public function __construct(LoggerInterface $logger){
        $this->logger=$logger;
    }

    public function getAssetSearch(Request $request){

        $content=json_decode($request->getContent());
        if(empty($content)){
            return new JsonResponse($content);
        }
        $sSearch=$content->search;
        $sSearch="Topp";

        $conn=$this->getDoctrine()->getConnection();
        $sql="SELECT id FROM assets WHERE UPPER(asset_name) LIKE UPPER('%$sSearch%') ";
        $stmt=$conn->prepare($sql);
        $stmt->execute();

        $aAssetId = $stmt->fetchAll();

        $iIds = array_column($aAssetId, 'id');
        //$iIds = reset($aAssetId);

        //Henter alle objektene
        $assets = $this->getDoctrine()->getRepository(Assets::class)->findBy(array('id' => $iIds));

        return $this->json($assets, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::ATTRIBUTES => ['name', 'description'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }

    public function getMyAssets(Request $request){

        $content = json_decode($request->getContent());
        $iUserId = $content->userId;
        $iUserId=1;
        $user=$this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $assets=$user->getAssets();
        $aAssets = $assets->toArray();


        $d1=empty($aAssets);
        $d2="not empty";
        if($d1){
            $d2="empty";
        }
        //foreach($aAssets as $a=>$value){
          //  $d1->add($a)
        //}

        //$d1=$assets->get();
        return new JsonResponse($d2);
    }

    public function addAsset(Request $request){
        $ut="\n\n**************************************************************************\n\n";
        $this->logger->info($ut);
        $content = json_decode($request->getContent());

        $iUserId = $content->userId;
        $iCategoryId = $content->categoryId;
        $sAssetName = $content->assetName;
        $tDescription = $content->description;
        $iCondition = $content->condition;

        $user=$this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        //$assetCategory=$this->getDoctrine()->getRepository(AssetCategories::class)->find($iCategoryId);

        $asset=new Assets();
        $asset->setUsers($user);
        //$asset->setCategory($assetCategory);
        $asset->setAssetname($sAssetName);
        $asset->setDescription($tDescription);
        $asset->setAssetCondition($iCondition);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($asset);
        $entityManager->flush();

        $asset->setDescription("TESTING");
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($asset);
        $entityManager->flush();

        $this->logger->info($ut);

        $this->logger->info($ut);

        return new JsonResponse("Eiendel lagd til");
    }

    public function getAllAssets(){

    }
    public function getAsset(Request $request){
    }
    public function edditAsset(){

    }
    public function removeAsset(){

    }
}