<?php

namespace MusicShareBundle\Controller;

use MusicShareBundle\Entity\Sound;
use MusicShareBundle\Form\SoundType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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

        return $this->render('song/view_all.html.twig', [
            'songs' => $songs
        ]);
    }


    /**
     * @Route("/song/edit/{id}", name="song_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editSound($id, Request $request)
    {
        $song = $this->getDoctrine()->getRepository(Sound::class)->find($id);

        if ($song === null){
            return $this->redirectToRoute("musicshare_index");
        }

        $form = $this->createForm(SoundType::class, $song);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($song);
            $em->flush();

            return $this->redirectToRoute('song_view',
                array('id' => $song->getId()));
        }

        return $this->render('song/edit.html.twig',
            array(
                'song' => $song,
                'form' => $form->createView(),
            ));
    }
}
