<?php

namespace Fabien\EventsEngineBundle\Controller;

use Fabien\EventsEngineBundle\Entity\Publicite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Fabien\EventsEngineBundle\Entity\Image;

/**
 * Publicite controller.
 *
 */
class PubliciteController extends Controller
{
    /**
     * Lists all publicite entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $publicites = $em->getRepository('FabienEventsEngineBundle:Publicite')->findAll();

        return $this->render('FabienEventsEngineBundle:Publicite:index.html.twig', array(
            'publicites' => $publicites,
        ));
    }

    /**
     * Creates a new publicite entity.
     *
     */
    public function newAction(Request $request)
    {
        $publicite = new Publicite();
        $form = $this->createForm('Fabien\EventsEngineBundle\Form\PubliciteType', $publicite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($publicite);

            if($publicite->getFile()){
              $image=new Image();
              $nameDoc=$image->upload($publicite->getFile());
              $image->setUrl($nameDoc);
              $image->setAlt("Image de la publicité");

              $em->persist($image);
              $publicite->setImage($image);
            }

            if($publicite->getFile2()){
              $image2=new Image();
              $nameDoc2=$image2->upload($publicite->getFile2());
              $image2->setUrl($nameDoc2);
              $image2->setAlt("Image de la publicité");

              $em->persist($image2);
              $publicite->setImage2($image2);
            }

            $em->flush();

            return $this->redirectToRoute('publicite_index');
        }

        return $this->render('FabienEventsEngineBundle:Publicite:new.html.twig', array(
            'publicite' => $publicite,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a publicite entity.
     *
     */
    public function showAction(Publicite $publicite)
    {
        $deleteForm = $this->createDeleteForm($publicite);

        return $this->render('FabienEventsEngineBundle:Publicite:show.html.twig', array(
            'publicite' => $publicite,
            'delete_form' => $deleteForm->createView(),
        ));
    }


    public function deleteImages($image){


        if($image){
          $urlImage=$image->getUrl();
          @unlink($image->getUploadRootDir().$image->getUploadDir()."/".$urlImage);
          @unlink($image->getUploadRootDir().$image->getUploadThumbDir()."/".$urlImage);
        }
    }


    /**
     * Displays a form to edit an existing publicite entity.
     *
     */
    public function editAction(Request $request, Publicite $publicite)
    {
        $deleteForm = $this->createDeleteForm($publicite);
        $editForm = $this->createForm('Fabien\EventsEngineBundle\Form\PubliciteType', $publicite);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->getDoctrine()->getManager()->flush();

            if($publicite->getFile()){
              $this->deleteImages($publicite->getImage());
              $image=new Image();
              $nameDoc=$image->upload($publicite->getFile());
              $image->setUrl($nameDoc);
              $image->setAlt("Image de la publicité");

              $em->persist($image);
              $publicite->setImage($image);
            }

            if($publicite->getFile2()){
              $this->deleteImages($publicite->getImage2());
              $image2=new Image();
              $nameDoc2=$image2->upload($publicite->getFile2());
              $image2->setUrl($nameDoc2);
              $image2->setAlt("Image de la publicité");

              $em->persist($image2);
              $publicite->setImage($image2);
            }

            return $this->redirectToRoute('publicite_index');
        }

        return $this->render('FabienEventsEngineBundle:Publicite:edit.html.twig', array(
            'publicite' => $publicite,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a publicite entity.
     *
     */
    public function deleteAction(Request $request, Publicite $publicite)
    {
        $form = $this->createDeleteForm($publicite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($publicite);
            $em->flush();
        }

        return $this->redirectToRoute('publicite_index');
    }

    /**
     * Creates a form to delete a publicite entity.
     *
     * @param Publicite $publicite The publicite entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Publicite $publicite)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('publicite_delete', array('id' => $publicite->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
