<?php

namespace MusicShareBundle\Controller;

use MusicShareBundle\Entity\PlayList;
use MusicShareBundle\Form\PlayListType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PlayListController extends Controller
{
    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/playlist/create", name="create_playlist")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createPlayList(Request $request)
    {
        $playList = new PlayList();
        $form = $this->createForm(PlayListType::class, $playList);

        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted())
        {
            $user = $this->getUser();

            $playList->setOwner($user);
            $user->addPlayList($playList);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($playList);
            $entityManager->flush();

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('playlist/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
