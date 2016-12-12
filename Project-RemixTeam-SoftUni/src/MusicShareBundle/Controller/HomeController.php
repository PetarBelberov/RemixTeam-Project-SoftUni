<?php

namespace MusicShareBundle\Controller;

use MusicShareBundle\Form\UserType;
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

        $form = $this->createForm(UserType::class);

        return $this->render("musicshare/index.html.twig", [
            'form' => $form->createView()
        ]);
    }
}
