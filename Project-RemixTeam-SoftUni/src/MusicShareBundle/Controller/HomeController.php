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
<<<<<<< HEAD
=======

>>>>>>> 0e9b02cc7a39eccf23e29067a586cc1289ff7500
        return $this->render("musicshare/index.html.twig");
    }
}
