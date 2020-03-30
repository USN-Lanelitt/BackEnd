<?php

namespace App\Controller;

use App\Entity\AssetImages;

use App\Entity\Assets;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

header("Access-Control-Allow-Origin: *");

class AssetImageController extends AbstractController{

    public function getMainImage($assetId){
        $assetImage=$this->getDoctrine()->getRepository(AssetImages::class)->findOneBy(array('assets'=>$assetId, 'mainImage'=>true));
        if(empty($assetImage)){
            return new JsonResponse($assetImage);
        }
        return $this->json($assetImage, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::ATTRIBUTES => ['imageUrl'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }
    public function addImage(Request $request, $userId, $assetId){
        $asset=$this->getDoctrine()->getRepository(Assets::class)->findOneBy(array('id'=>$assetId, 'users'=>$userId));
        $imageAnt=$this->getDoctrine()->getRepository(AssetImages::class)->findBy(array('assets'=>$assetId))->count();
        $imageAnt++;


        if(empty($asset)){
            return new JsonResponse($asset);
        }

        $content = json_decode($request->getContent());
        $sImage = $content->image;
        $bMainImage = $content->mainImage;
        $ImageOriginalName = $sImage->getClientOriginalName();

        $aReturn['code'] = 400;
        $aReturn['image'] = "";

        // lage nutt bilde navn
        $temp = explode(".", $ImageOriginalName);
        $newfilename = $userId.'AssetImage.' .$imageAnt. end($temp);

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
            $assetImages=$this->getDoctrine()->getRepository(AssetImages::class)->findBy(array('assets'=>$assetId, 'mainImage'=>true));
            foreach ($assetImages as $a){
                $a->setMainImage(false);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($a);
                $entityManager->flush();
            }
        }

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
        }
        return new JsonResponse($aReturn);
    }
}