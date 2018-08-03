<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 07/06/2018
 * Time: 21:16
 */

namespace B2bBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class VitrineSalonController extends Controller
{
    public function listSalonAction(){
        $em = $this->getDoctrine()->getManager();

        $salons = $em->getRepository('B2bBundle:Salon')->findBy(array('isActive' => 1), array('dateDebut' => 'asc'));


        return $this->render('B2bBundle::Vitrine/salons_boutiques.html.twig', array(
            'salons' => $salons
        ));
    }
}