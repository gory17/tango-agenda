<?php

namespace Fabien\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FabienUserBundle:Default:index.html.twig');
    }
}
