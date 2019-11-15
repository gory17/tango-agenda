<?php

namespace Fabien\EventsEngineBundle\Controller;

use Fabien\EventsEngineBundle\Entity\RequeteFb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Requetefb controller.
 *
 */
class RequeteFbController extends Controller
{
    /**
     * Lists all requeteFb entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $requeteFbs = $em->getRepository('FabienEventsEngineBundle:RequeteFb')->findAll();

        return $this->render('FabienEventsEngineBundle:requetefb:index.html.twig', array(
            'requeteFbs' => $requeteFbs,
        ));
    }

    /**
     * Creates a new requeteFb entity.
     *
     */
    public function newAction(Request $request)
    {
        $requeteFb = new Requetefb();
        $form = $this->createForm('Fabien\EventsEngineBundle\Form\RequeteFbType', $requeteFb);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($requeteFb);
            $em->flush($requeteFb);

            return $this->redirectToRoute('requetefb_index');
        }

        return $this->render('FabienEventsEngineBundle:requetefb:new.html.twig', array(
            'requeteFb' => $requeteFb,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a requeteFb entity.
     *
     */
    public function showAction(RequeteFb $requeteFb)
    {
        $deleteForm = $this->createDeleteForm($requeteFb);

        return $this->render('FabienEventsEngineBundle:requetefb:show.html.twig', array(
            'requeteFb' => $requeteFb,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing requeteFb entity.
     *
     */
    public function editAction(Request $request, RequeteFb $requeteFb)
    {
        $deleteForm = $this->createDeleteForm($requeteFb);
        $editForm = $this->createForm('Fabien\EventsEngineBundle\Form\RequeteFbType', $requeteFb);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('requetefb_edit', array('id' => $requeteFb->getId()));
        }

        return $this->render('FabienEventsEngineBundle:requetefb:edit.html.twig', array(
            'requeteFb' => $requeteFb,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a requeteFb entity.
     *
     */
    public function deleteAction(Request $request, RequeteFb $requeteFb)
    {
        $form = $this->createDeleteForm($requeteFb);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($requeteFb);
            $em->flush();
        }

        return $this->redirectToRoute('requetefb_index');
    }

    /**
     * Creates a form to delete a requeteFb entity.
     *
     * @param RequeteFb $requeteFb The requeteFb entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(RequeteFb $requeteFb)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('requetefb_delete', array('id' => $requeteFb->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
