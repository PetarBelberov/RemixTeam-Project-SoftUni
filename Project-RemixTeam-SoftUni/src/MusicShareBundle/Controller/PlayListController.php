<?php

namespace MusicShareBundle\Controller;

use MusicShareBundle\Entity\PlayList;
use MusicShareBundle\Entity\Sound;
use MusicShareBundle\Entity\User;
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

            return $this->redirectToRoute('print_all_songs');
        }

        return $this->render('playlist/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/playlist/delete/{id}", name="delete_playlist")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deletePlayList($id) {
        $playList = $this->getDoctrine()->getRepository(PlayList::class)->find($id);

        if ($playList == null) {
            return $this->render('error.html.twig', [
                'error' => ' 404: Playlist not found.'
            ]);
        }

        $user = $this->getUser();

        if ($playList->getOwner() != $user && !$user->isAdmin()) {
            return $this->render('error.html.twig', [
                'error' => ' 403: Access denied. You have no permission to perform this action!'
            ]);
        }

        $songs = $playList->getSongs()->toArray();
        $entityManager = $this->getDoctrine()->getManager();

        /** @var Sound $song */
        foreach ($songs as $song) {
            $playLists = $song->getPlayLists()->toArray();
            $index = array_search($playLists, [ $playList ]);
            unset($playLists[$index]);
            $song->setPlayLists($playLists);
            $entityManager->persist($song);
        }

        $entityManager->remove($playList);
        $entityManager->flush();

        return $this->redirectToRoute('user_profile');
    }

    /**
     * @Route("/playlist/{id}", name="view_playlist")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewPlayList($id) {
        $playList = $this->getDoctrine()->getRepository(PlayList::class)->find($id);

        if ($playList === null) {
            return $this->render('error.html.twig', [
                'error' => ' 404: Playlist not found.'
            ]);
        }

        $songs = $playList->getSongs()->toArray();

        return $this->render('playlist/view.html.twig', [
            'playlist' => $playList,
            'songs' => $songs
        ]);
    }

    /**
     * @param $songId
     *  @param $listId
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/playlist/addsong/{songId}/{listId}", name="add_song_to_playlist")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addSongToPlayList($songId, $listId) {

        $playList = $this->getDoctrine()->getRepository(PlayList::class)->find($listId);
        $song = $this->getDoctrine()->getRepository(Sound::class)->find($songId);


        if ($playList == null || $song == null) {
            return $this->render('error.html.twig', [
                'error' => ' 404: Playlist or song not found.'
            ]);

        }

        $user = $this->getUser();

        if ($playList->getOwner() != $user && !$user->isAdmin()) {
            return $this->render('error.html.twig', [
                'error' => ' 403: Access denied. You have no permission to perform this action!'
            ]);
        }

        if ($playList->getSongs()->contains($song)) {
            return $this->render('error.html.twig', [
                'error' => ': The song already exists in this play list!'
            ]);
        }

        $playList->addSong($song);
        $song->addToPlayList($playList);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($playList);
        $entityManager->persist($song);
        $entityManager->flush();

        return $this->redirectToRoute('view_playlist', [
            'id' => $listId
        ]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/playlist/removesong/{songId}/{listId}", name="remove_song_from_playlist")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeSongToPlayList($songId, $listId) {
        $playList = $this->getDoctrine()->getRepository(PlayList::class)->find($listId);
        $song = $this->getDoctrine()->getRepository(Sound::class)->find($songId);

        if ($playList == null || $song == null) {
            return $this->render('error.html.twig', [
                'error' => ' 404: Playlist or song not found.'
            ]);
        }

        $user = $this->getUser();

        $songs = $playList->getSongs()->toArray();
        $playLists = $song->getPlayLists()->toArray();

        if ($playList->getOwner() != $user && !$user->isAdmin()) {
            return $this->render('error.html.twig', [
                'error' => ' 403: Access denied. You have no permission to perform this action!'
            ]);
        }

        if (!$playList->getSongs()->contains($song)) {
            return $this->render('error.html.twig', [
                'error' => ': The song does not exist in this play list!'
            ]);
        }

        $index = array_search($songs, [ $song ]);
        unset($songs[$index]);

        $index = array_search($playLists, [ $playList ]);
        unset($playLists[$index]);

        $song->setPlayLists($playLists);
        $playList->setSongs($songs);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($playList);
        $entityManager->persist($song);
        $entityManager->flush();

        return $this->redirectToRoute('view_playlist', [
            'id' => $listId
        ]);
    }
}
