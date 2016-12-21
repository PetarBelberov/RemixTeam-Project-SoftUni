<?php

namespace MusicShareBundle\Controller;
use Doctrine\Common\Collections\Criteria;
use MusicShareBundle\Entity\Category;
use MusicShareBundle\Entity\Rating;
use MusicShareBundle\Entity\Sound;
use MusicShareBundle\Form\SoundType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        $ratio['like'] =$this->getDoctrine()->getRepository(Rating::class)->getLikedRating($id);
        $ratio['dislike'] = $this->getDoctrine()->getRepository(Rating::class)->getDisLikedRating($id);
        $song = $this->getDoctrine()->getRepository(Sound::class)->find($id);

        if ($song === null)
        {
            return $this->render('song/notfound.html.twig');
        }

        return $this->render('song/view.html.twig', [
            'song' => $song,
            'ratio' => $ratio
        ]);
    }

    /**
     * @Route("/categories", name="print_all_categories")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function printAllCategories()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        if (!$categories) {
            return new Response('There is no content to be displayed');

        }
        return $this->render('song/all_categories.html.twig',
            ['categories' => $categories
            ]);
    }


    /**
     * @Route("/catalog", name="print_all_songs")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function printAllSongs( )
    {
        $songs = $this->getDoctrine()->getRepository(Sound::class)->findAll();

        if (!$songs) {
            return $this->render('error.html.twig', [
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


    /**
     * @Route("/category/{id}", name="category_songs")
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function listSongs($id)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);

        $songs = $category->getSongs()->toArray();
        return $this->render('song/list.html.twig', [
            'songs'=>$songs
        ]);
    }


    private function rateSong($soundId, $liked){
        $user = $this->getUser();
        $usrId = $user->getId();
        $em = $this->getDoctrine()->getManager();
        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria
            ->andWhere($criteria->expr()->contains('soundId', $soundId ) )
            ->andWhere($criteria->expr()->contains('userId', $usrId ) );
        $rating = $em->getRepository(Rating::class)->matching($criteria);


        if (count( $rating) < 1   ){
            $rating = new Rating();
            $rating->setSoundId($soundId);
            $rating->setUserId($usrId);
            $rating->setLiked($liked);
            $em->persist($rating);
            $em->flush();
        }else{
            $rating = $rating[0];
            $rating->setLiked($liked);
            $em->merge($rating);
            $em->flush();
        }
    }

    /**
     * @Route("/song/dislike/{id}", name="dislike_songs")
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dislikeSong($id){
        $this->rateSong($id,-1);
        $ratio['like'] =$this->getDoctrine()->getRepository(Rating::class)->getLikedRating($id);
        $ratio['dislike'] = $this->getDoctrine()->getRepository(Rating::class)->getDisLikedRating($id);
        return new JsonResponse(array($ratio));
    }


    /**
     * @Route("/song/like/{id}", name="like_songs")
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function likeSong($id){
        $this->rateSong($id,1);
        $ratio['like'] =$this->getDoctrine()->getRepository(Rating::class)->getLikedRating($id);
        $ratio['dislike'] = $this->getDoctrine()->getRepository(Rating::class)->getDisLikedRating($id);
        return new JsonResponse(array($ratio));
    }


}
