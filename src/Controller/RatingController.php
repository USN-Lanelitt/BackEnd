<?php

namespace App\Controller;

use App\Entity\AssetCategories;
use App\Entity\Assets;

use App\Entity\Loans;
use App\Entity\RatingLoans;
use App\Entity\UserConnections;
use App\Entity\RequestStatus;
use App\Entity\Users;
use DateInterval;
use DatePeriod;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Psr\Log\LoggerInterface;

$request = Request::createFromGlobals();

header("Access-Control-Allow-Origin: *");

class RatingController extends AbstractController{


    public function rateAsset($userId, $assetId, $newRating){

        $loan=$this->getDoctrine()->getRepository(Loans::class)->findOneBy(array('assets'=>$assetId, 'users'=>$userId));

        $loanId=$loan->getId();
        $rating=$this->getDoctrine()->getRepository(RatingLoans::class)->findOneBy(array('loans'=>$loan));

        if(empty($loan)||!empty($rating)){
            return new JsonResponse("Ikke lov");
        }

        $rating=new RatingLoans();
        $rating->setLoans(($loan));
        $rating->setRatingAsset($newRating);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($rating);
        $entityManager->flush();

        return new JsonResponse("Fullført tilbakemelding");
    }
    public function getAssetRating($assetId){
        $conn=$this->getDoctrine()->getConnection();
        $sql="SELECT SUM(rating_asset)/COUNT(rating_asset) AS rating FROM rating_loans WHERE loans_id in (SELECT id FROM loans WHERE assets_id=$assetId)";
        $stmt=$conn->prepare($sql);
        $stmt->execute();

        $ratings=$stmt->fetchAll();

        return new JsonResponse($ratings);

    }

}