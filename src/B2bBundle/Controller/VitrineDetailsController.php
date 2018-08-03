<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 07/06/2018
 * Time: 21:15
 */

namespace B2bBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class VitrineDetailsController extends Controller
{

    public function getOneShowroomAction($id){
        $em = $this->getDoctrine()->getManager();

        $salon = $em->getRepository('B2bBundle:Salon')->findOneById($id);


        return $this->render('B2bBundle::Vitrine/salon_details.html.twig', array(
            'salon' => $salon,
        ));
    }


}