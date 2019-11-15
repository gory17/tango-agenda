<?php

namespace Fabien\EventsEngineBundle\Controller;

use Fabien\EventsEngineBundle\Entity\GroupFb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Groupfb controller.
 *
 */
class GroupFbController extends Controller
{
    /**
     * Lists all groupFb entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $groupFbs = $em->getRepository('FabienEventsEngineBundle:GroupFb')->findAll();

        return $this->render('FabienEventsEngineBundle:groupfb:index.html.twig', array(
            'groupFbs' => $groupFbs,
        ));
    }

    /**
     * Creates a new groupFb entity.
     *
     */
    public function newAction(Request $request)
    {
        $groupFb = new Groupfb();
        $form = $this->createForm('Fabien\EventsEngineBundle\Form\GroupFbType', $groupFb);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($groupFb);
            $em->flush();

            return $this->redirectToRoute('groupfb_show', array('id' => $groupFb->getId()));
        }

        return $this->render('FabienEventsEngineBundle:groupfb:new.html.twig', array(
            'groupFb' => $groupFb,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a groupFb entity.
     *
     */
    public function showAction(GroupFb $groupFb)
    {
        $deleteForm = $this->createDeleteForm($groupFb);

        return $this->render('FabienEventsEngineBundle:groupfb:show.html.twig', array(
            'groupFb' => $groupFb,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing groupFb entity.
     *
     */
    public function editAction(Request $request, GroupFb $groupFb)
    {
        $deleteForm = $this->createDeleteForm($groupFb);
        $editForm = $this->createForm('Fabien\EventsEngineBundle\Form\GroupFbType', $groupFb);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('groupfb_edit', array('id' => $groupFb->getId()));
        }

        return $this->render('FabienEventsEngineBundle:groupfb:edit.html.twig', array(
            'groupFb' => $groupFb,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a groupFb entity.
     *
     */
    public function deleteAction(Request $request, GroupFb $groupFb)
    {
        $form = $this->createDeleteForm($groupFb);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($groupFb);
            $em->flush();
        }

        return $this->redirectToRoute('groupfb_index');
    }

    /**
     * Creates a form to delete a groupFb entity.
     *
     * @param GroupFb $groupFb The groupFb entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(GroupFb $groupFb)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('groupfb_delete', array('id' => $groupFb->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
