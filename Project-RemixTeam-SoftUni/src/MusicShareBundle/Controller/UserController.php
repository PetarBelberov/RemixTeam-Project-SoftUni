<?php

namespace MusicShareBundle\Controller;

use MusicShareBundle\Entity\Role;
use MusicShareBundle\Entity\Sound;
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
     * @param $id
     * @Route("/view_profile/{id}/uploads", name="view_user_uploads")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewUserUploads($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($user === null) {
            return $this->render('error.html.twig', [
                'error' => ' 404: User not found.'
            ]);
        }

        $songs = $user->getSongs()->toArray();

        return $this->render('user/list_uploads.html.twig', [
            'songs' => $songs,
            'user' => $user
        ]);
    }

    /**
     * @param $id
     * @Route("/view_profile/{id}/favorites", name="view_user_favorites")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewUserFavorites($id) {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($user === null) {
            return $this->render('error.html.twig', [
                'error' => ' 404: User not found.'
            ]);
        }

        $favorites = $user->getFavoriteSongs()->toArray();

        return $this->render('user/list_favorites.html.twig', [
            'favorites' => $favorites,
            'user' => $user
        ]);
    }

    /**
     * @Route("favorites/add/{songId}", name="add_to_favorites")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addToFavorites($songId) {
        $song = $this->getDoctrine()->getRepository(Sound::class)->find($songId);

        if ($song === null) {
            return $this->render('error.html.twig', [
                'error' => ' 404: Song not found.'
            ]);
        }

        $user = $this->getUser();
        $user->addSongToFavorites($song);
        $song->addToFavorites($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->persist($song);
        $entityManager->flush();

        return $this->redirectToRoute('view_user_favorites', [
            'id' => $user->getId()
        ]);
    }

    /**
     * @Route("favorites/remove/{songId}", name="remove_from_favorites")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function removeFromFavorites($songId) {
        $song = $this->getDoctrine()->getRepository(Sound::class)->find($songId);

        if ($song === null) {
            return $this->render('error.html.twig', [
                'error' => ' 404: Song not found.'
            ]);
        }

        $user = $this->getUser();
        $favorites = $user->getFavoriteSongs()->toArray();

        if (!$user->getFavoriteSongs()->contains($song)) {
            return $this->render('error.html.twig', [
                'error' => ' 404: Song not found in your favorites list.'
            ]);
        }

        $index = array_search($favorites, [ $song ]);
        unset($favorites[$index]);

        $users = $song->getFavorites()->toArray();
        $index = array_search($users, [$user]);
        unset($users[$index]);

        $user->setFavoriteSongs($favorites);
        $song->setFavorites($users);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->persist($song);
        $entityManager->flush();

        return $this->redirectToRoute('view_user_favorites', [
            'id' => $user->getId()
        ]);
    }
}
