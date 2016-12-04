<?php

namespace MusicShareBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="musicshare_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        if ($this->getUser())
        {
            return $this->redirectToRoute('user_profile');
        }
        return $this->render("musicshare/index.html.twig");
    }
}
