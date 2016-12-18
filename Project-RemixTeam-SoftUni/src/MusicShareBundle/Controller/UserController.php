<?php

namespace MusicShareBundle\Controller;

use MusicShareBundle\Entity\Role;
use MusicShareBundle\Entity\User;
use MusicShareBundle\Form\SoundType;
use MusicShareBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{

    /**
     * @Route("/", name="user_register")
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

            $roleRepository = $this->getDoctrine()->getRepository(Role::class);
            $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);

            $user->addRole($userRole);

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
        $form = $this->createForm(SoundType::class);

        return $this->render("user/profile.html.twig", [
            'form' => $form->createView()
        ]);
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

        if ($user === null) {
            return $this->render('error.html.twig', [
                'error' => ' 404: User not found.'
            ]);
        }

        return $this->render('user/view_profile.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
      * @param $id
     * @Route("/view_profile/{id}/uploads", name="view_user_uploads")
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
