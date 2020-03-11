<?php

namespace App\Controller;

use App\Entity\AssetCategories;
use App\Entity\Users;
use Psr\Log\LoggerInterface;
use App\Entity\Assets;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

header("Access-Control-Allow-Origin: *");

class AssetController extends AbstractController{

    private $logger;

    public function __construct(LoggerInterface $logger){
        $this->logger=$logger;
    }

    public function getMyAssets(Request $request){
        $content = json_decode($request->getContent());

        //$iUserId = $content->userId;
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