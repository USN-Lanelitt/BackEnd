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

    function report(Request $request, $iReporterId, $iReportedId){
        if(empty($iReporterId)){
            return new JsonResponse();
        }

        $subjects = array('person', 'asset');

        //Henter kommentar og emne
        $content=json_decode($request->getContent());
        $sComment = $content->comment;
        $sSubject = $content->subject;

        $oUser1 = $this->getDoctrine()->getRepository(Users::class)->find($iReporterId);
        $oUser2 = $this->getDoctrine()->getRepository(Users::class)->find($iReportedId);

        $entityManager = $this->getDoctrine()->getManager();

        //Oppretter ny rad med klage
        $report = new UnwantedBehaviorReports();
        $report->setReporter($iReporterId);
        $report->setReported($iReportedId);
        $report->setTimestamp(new \DateTime());
        $report->setComment($sComment);
        $report->setSubject(new \DateTime());

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

}
