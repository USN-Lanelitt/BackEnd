<?php

namespace App\Controller;

use App\Entity\AssetCategories;
use App\Entity\Assets;

use App\Entity\Loans;
use App\Entity\UnwantedBehaviorReports;
use App\Entity\UserConnections;
use App\Entity\RequestStatus;
use App\Entity\Users;
use DateInterval;
use DatePeriod;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Psr\Log\LoggerInterface;


$request = Request::createFromGlobals();

header("Access-Control-Allow-Origin: *");

class UnwantedBehaviorReportsController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger){
        $this->logger=$logger;
    }

    function report(Request $request, $iUserId, $iUserId2){
        if(empty($iUserId)){
            return new JsonResponse();
        }

        $this->logger->info(json_decode($request));

        //Henter kommentar og emne
        $content=json_decode($request->getContent());
        $sSubject = $content->subject;
        $sComment = $content->comment;

        $oUser1 = $this->getDoctrine()->getRepository(Users::class)->find($iUserId);
        $oUser2 = $this->getDoctrine()->getRepository(Users::class)->find($iUserId2);

        $entityManager = $this->getDoctrine()->getManager();

        //Oppretter ny rad med klage
        $report = new UnwantedBehaviorReports();
        $report->setReporter($oUser1);
        $report->setReported($oUser2);
        $report->setTimestamp(new \DateTime());
        $report->setComment($sComment);
        $report->setSubject($sSubject);

        $entityManager->persist($report);
        $entityManager->flush();

        return new JsonResponse('sendt klage på person');

        $this->logger->info('madereport');

    }

    function getReports(){
        $oReports = $this->getDoctrine()->getRepository(UnwantedBehaviorReports::class)->findAll();

        return $this->json($oReports, Response::HTTP_OK, [], [
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::GROUPS => ['groups' => 'reportInfo'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            }
        ]);
    }

    public function getReportAmount()
    {
        //Henter antall brukere
        $oReports = $this->getDoctrine()->getRepository(UnwantedBehaviorReports::class)->findAll();
        $reportAmount=count($oReports);

        return new JsonResponse($reportAmount);
    }

}
