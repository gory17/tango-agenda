<?php

namespace Fabien\EventsEngineBundle\Controller;

use Fabien\EventsEngineBundle\Entity\CategoryPost;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Categorypost controller.
 *
 */
class CategoryPostController extends Controller
{
    /**
     * Lists all categoryPost entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categoryPosts = $em->getRepository('FabienEventsEngineBundle:CategoryPost')->findAll();

        return $this->render('FabienEventsEngineBundle:categorypost:index.html.twig', array(
            'categoryPosts' => $categoryPosts,
        ));
    }

    /**
     * Creates a new categoryPost entity.
     *
     */
    public function newAction(Request $request)
    {
        $categoryPost = new Categorypost();
        $form = $this->createForm('Fabien\EventsEngineBundle\Form\CategoryPostType', $categoryPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categoryPost);
            $em->flush($categoryPost);

            return $this->redirectToRoute('admin_categorypost_show', array('id' => $categoryPost->getId()));
        }

        return $this->render('FabienEventsEngineBundle:categorypost:new.html.twig', array(
            'categoryPost' => $categoryPost,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a categoryPost entity.
     *
     */
    public function showAction(CategoryPost $categoryPost)
    {
        $deleteForm = $this->createDeleteForm($categoryPost);

        return $this->render('FabienEventsEngineBundle:categorypost:show.html.twig', array(
            'categoryPost' => $categoryPost,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing categoryPost entity.
     *
     */
    public function editAction(Request $request, CategoryPost $categoryPost)
    {
        $deleteForm = $this->createDeleteForm($categoryPost);
        $editForm = $this->createForm('Fabien\EventsEngineBundle\Form\CategoryPostType', $categoryPost);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_categorypost_edit', array('id' => $categoryPost->getId()));
        }

        return $this->render('FabienEventsEngineBundle:categorypost:edit.html.twig', array(
            'categoryPost' => $categoryPost,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a categoryPost entity.
     *
     */
    public function deleteAction(Request $request, CategoryPost $categoryPost)
    {
        $form = $this->createDeleteForm($categoryPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($categoryPost);
            $em->flush($categoryPost);
        }

        return $this->redirectToRoute('admin_categorypost_index');
    }

    /**
     * Creates a form to delete a categoryPost entity.
     *
     * @param CategoryPost $categoryPost The categoryPost entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CategoryPost $categoryPost)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_categorypost_delete', array('id' => $categoryPost->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
