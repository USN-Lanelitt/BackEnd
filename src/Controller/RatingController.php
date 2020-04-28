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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Psr\Log\LoggerInterface;

$request = Request::createFromGlobals();

/*---John-Berge Grimaas---*/

class RatingController extends AbstractController{


    public function rateAsset(Request $request, $userId, $loanId, $newRating){

        $content = json_decode($request->getContent());
        if(!empty($content)){
            $comment= $content->comment;
        }
        if(empty($comment)){
            $comment="No comment";
        }

        $loan=$this->getDoctrine()->getRepository(Loans::class)->find($loanId);

        $rating=$this->getDoctrine()->getRepository(RatingLoans::class)->findBy(array('loans'=>$loan));

        if(empty($loan)||!empty($rating)){
            return new JsonResponse("Ikke lov");
        }

        $rating=new RatingLoans();
        $rating->setLoans(($loan));
        $rating->setRatingAsset($newRating);
        $rating->setCommentFromBorrower($comment);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($rating);
        $entityManager->flush();

        //Logging funksjon
        $info=($userId." - ".$loanId." - ".$newRating." - $comment");
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$userId,
            'functionName'=>'rateAsset',
            'controllerName'=>'RatingController',
            'info'=>$info,
            'change'=>1
        ]);

        return new JsonResponse("Fullført tilbakemelding");
    }
    public function getAverageAssetRating($assetId){
        $conn=$this->getDoctrine()->getConnection();
        $sql="SELECT SUM(rating_asset)/COUNT(rating_asset) AS rating FROM rating_loans WHERE loans_id in (SELECT id FROM loans WHERE assets_id=$assetId)";
        $stmt=$conn->prepare($sql);
        $stmt->execute();

        $ratings=$stmt->fetch();
        $ratings=doubleval($ratings['rating']);

        //Logging funksjon
        $info=($assetId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>-1,
            'functionName'=>'getAverageAssetRating',
            'controllerName'=>'RatingController',
            'info'=>$info,
            'change'=>0
        ]);

        return new JsonResponse($ratings);
    }

    public function getUnratedLoans($iUserId){
        $conn=$this->getDoctrine()->getConnection();

        $sql="SELECT loans.id from assets, loans 
            where loans.assets_id=assets.id 
            and loans.users_id=$iUserId 
            and loans.date_end >= CURRENT_DATE()-14
            and loans.status_loan_id=1
            and loans.id not in
            (select loans_id from rating_loans);";
        $stmt=$conn->prepare($sql);
        $stmt->execute();

        $loanId=$stmt->fetchAll();

        $iIds = array_column($loanId, 'id');

        $loans = $this->getDoctrine()->getRepository(Loans::class)->findBy(array('id' => $iIds), array('id'=>'DESC'));

        //Logging funksjon
        $info=($iUserId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$iUserId,
            'functionName'=>'getUnratedLoans',
            'controllerName'=>'RatingController',
            'info'=>$info,
            'change'=>0
        ]);

        return $this->json($loans, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loaned'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }

    public function getMyAssetsRating($iUserId){
        $conn=$this->getDoctrine()->getConnection();
        $sql="SELECT id from rating_loans
            where loans_id in 
            (select loans.id from loans, assets
            where loans.assets_id=assets.id
            and assets.users_id=$iUserId)";
        $stmt=$conn->prepare($sql);
        $stmt->execute();

        $iIds=$stmt->fetchAll();

        $iIds = array_column($iIds, 'id');

        $ratings = $this->getDoctrine()->getRepository(RatingLoans::class)->findBy(array('id' => $iIds), array('id'=>'DESC'));

        //Logging funksjon
        $info=($iUserId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$iUserId,
            'functionName'=>'getMyAssetsRating',
            'controllerName'=>'RatingController',
            'info'=>$info,
            'change'=>0
        ]);
        return $this->json($ratings, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loaned'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getMyRating($iUserId){

        $conn=$this->getDoctrine()->getConnection();
        $sql="select id from rating_loans
                where loans_id in
                (select id from loans
                where users_id=$iUserId)";

        $stmt=$conn->prepare($sql);
        $stmt->execute();

        $iIds=$stmt->fetchAll();

        $iIds = array_column($iIds, 'id');

        $ratings = $this->getDoctrine()->getRepository(RatingLoans::class)->findBy(array('id' => $iIds), array('id'=>'DESC'));


        //Logging funksjon
        $info=($iUserId);
        $this->forward('App\Controller\UtilController:logging',[
            'userId'=>$iUserId,
            'functionName'=>'getMyRating',
            'controllerName'=>'RatingController',
            'info'=>$info,
            'change'=>0
        ]);
        return $this->json($ratings, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'loaned'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);

    }

}