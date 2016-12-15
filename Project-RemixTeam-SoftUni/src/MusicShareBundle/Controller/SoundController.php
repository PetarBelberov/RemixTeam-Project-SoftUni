<?php

namespace MusicShareBundle\Controller;

use MusicShareBundle\Entity\Sound;
use MusicShareBundle\Form\SoundType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SoundController extends Controller
{
    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/song/upload", name="upload_song")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadSong(Request $request)
    {
        $song = new Sound();

        $form = $this->createForm(SoundType::class, $song);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $file = $song->getFile();
            $user = $this->getUser();

            $fileName = $this
                ->get('app.file_uploader')
                ->setDir($this->get('kernel')->getRootDir()."/../web".$this->getParameter('songs_directory'))
                ->upload($file);

            $song->setFile($fileName);

            $file = $song->getCoverFile();
            if ($file === null)
            {
                $song->setCoverFile($this->getParameter('default_cover'));
            }
            else
            {
                $fileName = $this
                    ->get('app.file_uploader')
                    ->setDir($this->get('kernel')->getRootDir()."/../web".$this->getParameter('covers_directory'))
                    ->upload($file);

                $song->setCoverFile($fileName);
            }

            $song->setUploader($user);
            $song->setUploaderID($user->getId());
            $user->addSong($song);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($song);
            $entityManager->flush();

            return $this->redirectToRoute('song_view', [
                'id' => $song->getId()
            ]);
        }

        return $this->render('song/upload.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/song/{id}", name="song_view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewSong($id)
    {
        $song = $this->getDoctrine()->getRepository(Sound::class)->find($id);

        if ($song === null)
        {
            return $this->render('song/notfound.html.twig');
        }

        return $this->render('song/view.html.twig', [
            'song' => $song
        ]);
    }

    /**
     * @Route("/catalog", name="print_all_songs")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function printAllSongs()
    {
        $songs = $this->getDoctrine()->getRepository(Sound::class)->findAll();

        if (!$songs) {
            return $this->render('error.html.twgi', [
                'error' => 'There is no content to be displayed'
            ]);

        }
        return $this->render('song/view_all.html.twig', [
            'songs' => $songs
        ]);
    }


    /**
     * @Route("/song/delete/{id}", name="song_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id, Request $request)
    {
        $song = $this->getDoctrine()->getRepository(Sound::class)->find($id);

        if ($song === null){
            return $this->render('error.html.twig', [
                'error' => ' 404: Song not found.'
            ]);
        }

        //Check the current user if he is the author or admin
        $currentUser = $this->getUser();

        if (!$currentUser->isAuthor($song) && !$currentUser->isAdmin()) {
            return $this->render('error.html.twig', [
                'error' => ' 403: Access denied. You have no permission to perform this action!'
            ]);
        }

        $form = $this->createForm(SoundType::class, $song);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($song);
            $em->flush();

            return $this->redirectToRoute('musicshare_index');
        }

        return $this->render('song/delete.html.twig',
            array(
                'song' => $song,
                'form' => $form->createView(),
            ));
    }
}
