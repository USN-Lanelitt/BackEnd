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
        if(empty($asset)){
            return new JsonResponse($asset);
        }

        $content = json_decode($request->getContent());
        $sImageUrl = $content->imageUrl;
        $bMainImage = $content->mainImage;

        if($bMainImage){
            $assetImages=$this->getDoctrine()->getRepository(AssetImages::class)->findBy(array('assets'=>$assetId, 'mainImage'=>true));
            foreach ($assetImages as $a){
                $a->setMainImage(false);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($a);
                $entityManager->flush();
            }
        }

        $assetImage=new assetImages();
        $assetImage->setAssets($asset);
        $assetImage->setImageUrl($sImageUrl);
        $assetImage->setMainImage($bMainImage);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($assetImage);
        $entityManager->flush();


        return $this->json($assetImage, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::ATTRIBUTES => ['imageUrl'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);


    }

}