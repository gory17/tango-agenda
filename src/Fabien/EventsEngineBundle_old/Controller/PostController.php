<?php

namespace Fabien\EventsEngineBundle\Controller;

use Fabien\EventsEngineBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Fabien\EventsEngineBundle\Entity\Image;


/**
 * Post controller.
 *
 */
class PostController extends Controller
{
    /**
     * Lists all post entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('FabienEventsEngineBundle:Post')->findByDate();

        return $this->render('FabienEventsEngineBundle:Post:index.html.twig', array(
            'posts' => $posts,
        ));
    }

    /**
     * Creates a new post entity.
     *
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm('Fabien\EventsEngineBundle\Form\PostType', $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if($post->getFile()){

              $image=new Image();
              $nameDoc=$image->upload($post->getFile());
              $image->setUrl($nameDoc);
              $image->setAlt("Image de l'article");

              $em->persist($image);
              $post->setImageDefault($image);
            }

            $em->persist($post);
            $em->flush($post);

            return $this->redirectToRoute('admin_post_show', array('id' => $post->getId()));
        }

        return $this->render('FabienEventsEngineBundle:Post:new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a post entity.
     *
     */
    public function showAction(Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);

        return $this->render('FabienEventsEngineBundle:Post:show.html.twig', array(
            'post' => $post,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing post entity.
     *
     */
    public function editAction(Request $request, Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm('Fabien\EventsEngineBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
          $em = $this->getDoctrine()->getManager();
          if($post->getFile()){
            $this->deleteImagesPost($post);
            $image=new Image();
            $nameDoc=$image->upload($post->getFile());
            $image->setUrl($nameDoc);
            $image->setAlt("Image de l'article");

            $em->persist($image);
            $post->setImageDefault($image);
          }


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_post_edit', array('id' => $post->getId()));
        }

        return $this->render('FabienEventsEngineBundle:Post:edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    public function deleteImagesPost($post){
        $em = $this->getDoctrine()->getManager();
        $image=$post->getImageDefault();
        if($image){
          $urlImage=$image->getUrl();
          @unlink($image->getUploadRootDir().$image->getUploadDir()."/".$urlImage);
          @unlink($image->getUploadRootDir().$image->getUploadThumbDir()."/".$urlImage);

        //  $event->setImage(null);
          //$em->flush($event);

          $em->remove($image);

          $em->flush();
        }
    }


    /**
     * Deletes a post entity.
     *
     */
    public function deleteAction(Request $request, Post $post)
    {
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush($post);
        }

        return $this->redirectToRoute('admin_post_index');
    }

    /**
     * Creates a form to delete a post entity.
     *
     * @param Post $post The post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }




    public function listPublicAction(){
      $em = $this->getDoctrine()->getManager();

      $posts = $em->getRepository('FabienEventsEngineBundle:Post')->listPublic();

      return $this->render('FabienEventsEngineBundle:Post:list.html.twig', array(
          'posts' => $posts
      ));
    }


    public function rssAction(){
      $em = $this->getDoctrine()->getManager();

      $posts = $em->getRepository('FabienEventsEngineBundle:Post')->listPublic();

      return $this->render('FabienEventsEngineBundle:Post:rss.html.twig', array(
          'posts' => $posts
      ));
    }


    public function viewPublicAction(Post $post){
      return $this->render('FabienEventsEngineBundle:Post:show.html.twig', array(
          'post' => $post,
      ));
    }


}
