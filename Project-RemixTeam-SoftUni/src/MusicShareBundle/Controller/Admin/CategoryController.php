<?php

namespace MusicShareBundle\Controller\Admin;

use MusicShareBundle\Entity\Category;
use MusicShareBundle\Entity\Sound;
use MusicShareBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("admin/categories")
 * Class CategoryController
 * @package MusicShareBundle\Controller\Admin
 */

class CategoryController extends Controller
{
    /**
     * @Route("/", name="admin_categories")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategories()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('admin/category/list.html.twig',
                                     ['categories' => $categories]);
    }

    /**
     * @Route("/create", name="admin_categories_create")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createCategory (Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/create.html.twig',
            ['form'=> $form->createView()]);

    }

    /**
     * @Route("/edit/{id}", name="admin_categories_edit")
     *
     * @param $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function editCategory($id, Request $request)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)->find($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/edit.html.twig',
            ['category' => $category, 'form' => $form->createView()]);

    }

    /**
     * @Route("/delete/{id}", name="admin_categories_delete")
     *
     * @param $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function deleteCategory($id, Request $request)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)->find($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            foreach ($category->getSongs() as $song){
                $em->remove($song);
            }

            $em->remove($category);
            $em->flush();

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/delete.html.twig',
            ['category' => $category, 'form' => $form->createView()]);
    }

    /**
     * @Route("/category/{name}", name="songs_by_category")
     * @param $name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function songs($name)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['name' => $name]);

        return $this->render('search/song.html.twig', ['category' => $category]);
    }
}
