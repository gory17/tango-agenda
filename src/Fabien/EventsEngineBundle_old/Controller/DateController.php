<?php

namespace Fabien\EventsEngineBundle\Controller;

use Fabien\EventsEngineBundle\Entity\Date;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Date controller.
 *
 */
class DateController extends Controller
{
    /**
     * Lists all date entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dates = $em->getRepository('FabienEventsEngineBundle:Date')->findAll();

        return $this->render('date/index.html.twig', array(
            'dates' => $dates,
        ));
    }

    /**
     * Creates a new date entity.
     *
     */
    public function newAction(Request $request)
    {
        $date = new Date();
        $form = $this->createForm('Fabien\EventsEngineBundle\Form\DateType', $date);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($date);
            $em->flush($date);

            return $this->redirectToRoute('date_show', array('id' => $date->getId()));
        }

        /*
        return $this->render('date/new.html.twig', array(
            'date' => $date,
            'form' => $form->createView(),
        ));
        */

        $formBuilder = $this->createForm(DateType::class,$date);
        return $this->render('FabienEventsEngineBundle:Dates:add.html.twig',array('form'=>$formBuilder->createView(),"event"=>$event));
    }

    /**
     * Finds and displays a date entity.
     *
     */
    public function viewAction(Date $date)
    {

        return $this->render('FabienEventsEngineBundle:Dates:viewDate.html.twig', array(
            'date' => $date,
        ));
    }

    /**
     * Displays a form to edit an existing date entity.
     *
     */
    public function editAction(Request $request, Date $date)
    {
        $deleteForm = $this->createDeleteForm($date);
        $editForm = $this->createForm('Fabien\EventsEngineBundle\Form\DateType', $date);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('date_edit', array('id' => $date->getId()));
        }

        return $this->render('date/edit.html.twig', array(
            'date' => $date,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a date entity.
     *
     */
    public function deleteAction(Request $request, Date $date)
    {
        $form = $this->createDeleteForm($date);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($date);
            $em->flush($date);
        }

        return $this->redirectToRoute('date_index');
    }





    /**
     * Creates a form to delete a date entity.
     *
     * @param Date $date The date entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Date $date)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('date_delete', array('id' => $date->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
