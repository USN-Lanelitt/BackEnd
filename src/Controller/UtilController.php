<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UtilController extends AbstractController
{
    /**
     * @Route("/util", name="util")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UtilController.php',
        ]);
    }

    public static function makeName($sFirstname,$sMiddlename,$sLastname) {
        if (strlen(trim($sMiddlename)) > 0)
            return ($sFirstname.' '.$sMiddlename.' '.$sLastname);
        return ($sFirstname.' '.$sLastname);
    }

    public static function RandomString($iLength)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $iLength; $i++) {
            $randstring .= $characters[rand(0, strlen($characters)-1)];
        }
        return $randstring;
    }
}
