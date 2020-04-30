<?php

/*
 *John har jobbet med denne filen
 *Finn - jobbet noe på assetImageUpload
 */
namespace App\Controller;

use App\Entity\AssetImages;

use App\Entity\Assets;
use App\Entity\Users;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class AssetImageController extends AbstractController{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getMainImage($assetId){//henter hoved bildet
        $assetImage=$this->getDoctrine()->getRepository(AssetImages::class)->findOneBy(array('assets'=>$assetId, 'mainImage'=>true));
        if(empty($assetImage)){
            return new JsonResponse($assetImage);
        }

        //Logging funksjon
        $info=($assetId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>-1,
            'functionName'=>'getMainImage',
            'controllerName'=>'AssetImageController',
            'info'=>$info,
            'change'=>0
        ]);

        return $this->json($assetImage, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::ATTRIBUTES => ['imageUrl'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }
    public function addImage(Request $oRequest, $userId, $assetId){//legger til et pilde på en asset
        $this->logger->info($oRequest);
        $this->logger->info($userId);
        $this->logger->info($assetId);
        $oAsset = $this->getDoctrine()->getRepository(Assets::class)->findOneBy(array('id'=>$assetId, 'users'=>$userId));


        $sImage     = $oRequest->files->get('file');
        $bMainImage = boolval($oRequest->request->get('mainImage;'));
        $this->logger->info($bMainImage);




        $aReturn['code']  = 400;
        $aReturn['image'] = "";

        $iLength = 5; // antall tegn i navnet på filnanvet på bilde
        $sImageNameRandom = UtilController::randomString($iLength);

        $ImageOriginalName = $sImage->getClientOriginalName();

        // lage nytt bildenavn
        $aTemp = explode(".", $ImageOriginalName);
        $sNewfilename = $assetId.'_'.$sImageNameRandom.'.'.end($aTemp);

        $sTargetDir = "../../FrontEnd/AssetImages/";

        $sTargetFile = $sTargetDir . $sNewfilename;
        $this->logger->info($sTargetFile);

        $aCheck = getimagesize($sImage);
        if($aCheck !== false) {
            $this->logger->info("File is an image - " . $aCheck["mime"] . ".");
            $uploadOk = 1;
        } else {
            $this->logger->info("File is not an image.");
            $uploadOk = 0;
            // returnere 400 hvis det ikke er et bilde.
            return new JsonResponse($aReturn);
        }
        if($bMainImage){
            $bMainImage = true;
        }

        // lagre bidlefilen
        if (move_uploaded_file($sImage, $sTargetFile)) {
            $this->logger->info("The file ". basename($ImageOriginalName). " has been uploaded.");

            $assetImage = new assetImages();
            $assetImage->setAssets($oAsset);
            $assetImage->setImageUrl($sNewfilename);
            $assetImage->setMainImage($bMainImage);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($assetImage);
            $entityManager->flush();

            $aReturn['code'] = 200;
            $aReturn['image'] = $sNewfilename;
        }
        else
        {
            $this->logger->info("Sorry, there was an error uploading your file.");
        }

        //Logging funksjon
        $info=($assetId." - ".$sNewfilename." - ".$bMainImage);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$userId,
            'functionName'=>'addImage',
            'controllerName'=>'AssetImageController',
            'info'=>$info,
            'change'=>1
        ]);

        return new JsonResponse($aReturn);


            /*
            $content = json_decode($request->getContent());
            $sImage = $content->image;
            $bMainImage = $content->mainImage;
            $ImageOriginalName = $sImage->getClientOriginalName();

            $aReturn['code'] = 400;
            $aReturn['image'] = "";

            // lage nutt bilde navn
            $temp = explode(".", $ImageOriginalName);
            $newfilename = $userId.'_AssetImage_' .$imageAnt.'_.'. end($temp);

            $target_dir = "../../FrontEnd/public/AssetImages/";

            $target_file = $target_dir . $newfilename;
            $this->logger->info($target_file);

            $check = getimagesize($sImage);
            if($check !== false) {
                //$this->logger->info("File is an image - " . $check["mime"] . ".");
                $uploadOk = 1;
            } else {
                $this->logger->info("File is not an image.");
                $uploadOk = 0;
                // returnere 400 hvis det ikke er et bilde.
                return new JsonResponse($aReturn);
            }

            if($bMainImage){
                /*$assetImages=$this->getDoctrine()->getRepository(AssetImages::class)->findBy(array('assets'=>$assetId, 'mainImage'=>true));
                foreach ($assetImages as $a){
                    $a->setMainImage(false);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($a);
                    $entityManager->flush();
                }*/
        /*}

        if (move_uploaded_file($sImage, $target_file)) {
            $aReturn['code'] = 200;
            $aReturn['image'] = $newfilename;


            $assetImage=new assetImages();
            $assetImage->setAssets($asset);
            $assetImage->setImageUrl($newfilename);
            $assetImage->setMainImage($bMainImage);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($assetImage);
            $entityManager->flush();
        } else {
            //$this->logger->info("Sorry, there was an error uploading your file.");
        }*/
        //$aReturn = "";
        //return new JsonResponse($aReturn);
    }
}