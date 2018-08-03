<?php

namespace B2bBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DeliveryController extends Controller
{
    public function indexAction() {

        return $this->render('deliveryform/index.html.twig', array(
        ));
    }
}
?>