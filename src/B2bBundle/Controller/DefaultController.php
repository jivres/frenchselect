<?php

namespace B2bBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('B2bBundle:Default:index.html.twig');
    }
}
