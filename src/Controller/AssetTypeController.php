<?php

namespace App\Controller;

use App\Entity\AssetCategories;
use App\Entity\AssetImages;

use App\Entity\Assets;
use App\Entity\AssetTypes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

header("Access-Control-Allow-Origin: *");

class AssetTypeController extends AbstractController{
    public function getAssetCategories(){
        $assetCategory=$this->getDoctrine()->getRepository(AssetCategories::class)->findAll();

        return $this->json($assetCategory, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::ATTRIBUTES => ['id', 'categoryName'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }
    public function getAssetTypes($iCatId){
        $assetType=$this->getDoctrine()->getRepository(AssetTypes::class)->findBy(array('assetCategories'=>$iCatId));

        return $this->json($assetType, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::ATTRIBUTES => ['id', 'assetType'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getAllAssetTypes(){
        $assetType=$this->getDoctrine()->getRepository(AssetTypes::class)->findAll();

        return $this->json($assetType, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::ATTRIBUTES => ['id', 'assetType'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }
}