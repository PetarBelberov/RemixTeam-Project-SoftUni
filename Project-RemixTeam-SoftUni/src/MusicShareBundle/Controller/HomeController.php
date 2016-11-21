<?php

namespace MusicShareBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="muscshare_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render("musicshare/index.html.twig");
    }
}
