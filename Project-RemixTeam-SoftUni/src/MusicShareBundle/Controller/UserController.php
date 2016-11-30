<?php

namespace MusicShareBundle\Controller;

use MusicShareBundle\Entity\User;
use MusicShareBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('security_login');
        }
        return $this->render(
            'user/register.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/profile", name="user_profile")
     */
    public function profileAction()
    {
        $user = $this->getUser();
        return $this->render("user/profile.html.twig", ['user'=>$user]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/view_profile/{id}", name="view_user_profile")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewProfileAction($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($user === null)
        {
            return $this->redirectToRoute('musicshare_index');
        }

        return $this->render('user/view_profile.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/view_profile/{id}/uploads", name="view_user_uploads")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewUserUploads($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $songs = $user->getSongs()->toArray();

        return $this->render('user/list_uploads.html.twig', [
            'songs' => $songs,
            'user' => $user
        ]);
    }
}
