<?php

namespace App\Controller;


use App\Entity\AssetCategories;
use App\Entity\Assets;
use App\Entity\Loans;
use App\Entity\IndividConnections;
use App\Entity\Individuals;

use App\Entity\Users;
use DateInterval;
use DatePeriod;
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
        $iUserId=$content->userId;
        $sSearch=$content->search;

        /*$sSearch="Topp";*/
        //Sjekk om lånetingen er offentlig
        //Hvis ikke sjekk om personer er venner
        //Sjekk at tingen ikke er lånt ut

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
            ObjectNormalizer::ATTRIBUTES => ['id'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }

    public function getMyAssets(Request $request){
        $content = json_decode($request->getContent());
        if(empty($content)){
            return new JsonResponse($content);
        }
        $iUserId = $content->userId;

        $user=$this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $assets=$user->getAssets();
        $aAssets = $assets->toArray();

        if(empty($aAssets)){
            return new JsonResponse($aAssets);
        }

        $d1=empty($aAssets);
        $d2="not empty";
        if($d1){
            $d2="empty";
        }
        return $this->json($aAssets, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::ATTRIBUTES => ['id','assetName', 'description'],
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

        return new JsonResponse("Eiendel lagd til");
    }

    public function getAllAssets(){

    }
    public function getAsset(Request $request){


       /*
        $content = json_decode($request->getContent());
        if(empty($content)){
            return new JsonResponse($content);
        }
        $iAssetId = $content->assetId;
       */
        $iAssetId = 1;

        $asset=$this->getDoctrine()->getRepository(Assets::class)->find($iAssetId);
        if(empty($asset)){
            return new JsonResponse($asset);
        }

        return $this->json($asset, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::ATTRIBUTES => ['id','assetName', 'description'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }
    public function assetAvailebility($id){
        $assetLoans=$this->getDoctrine()->getRepository(Loans::class)->findBy(array('assets'=>$id, 'statusLoan'=>2));

        $ikkeLedig = array();
        $teller = 0;
        if(!empty($assetLoans)) {
            foreach ($assetLoans as $assetLoan) {

                $dStart = $assetLoan->getDateStart();
                //return new JsonResponse($dStart);

                $dEnd = $assetLoan->getDateEnd();
                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($dStart, $interval, $dEnd);

                foreach ($period as $dt) {
                    $teller += 1;
                    $ikkeLedig[$teller] = $dt->format("Y-m-d");
                }
                /*
                $dStart = strtotime($assetLoan->getDateStart()->format('Y-m-d'));
                $dEnd = strtotime($assetLoan->getDateEnd()->format('Y-m-d'));
                for ($i = $dStart; $i <= $dEnd; $i += 86400) {
                    $teller += 1;
                    $ikkeLedig[$teller] = date("Y-m-d", $i);
                }*/
            }

        }
        return new JsonResponse($ikkeLedig);
    }

    public function edditAsset(){

    }
    public function removeAsset(){

    }
}