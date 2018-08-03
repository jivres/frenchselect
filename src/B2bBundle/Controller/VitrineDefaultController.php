<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 07/06/2018
 * Time: 21:14
 */

namespace B2bBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class VitrineDefaultController extends Controller
{
    public function indexAction()
    {


        $em = $this->getDoctrine()->getManager();
        $brands = $em->getRepository('B2bBundle:Brand')->findBy(array('isActive' => 1), array('recommandation' => 'DESC', 'dateInscription' => 'DESC'),4,0);
        $brands2 = $em->getRepository('B2bBundle:Brand')->findBy(array('isActive' => 1), array('recommandation' => 'DESC','dateInscription' => 'DESC'),4,4);
        $salons = $em->getRepository('B2bBundle:Salon')->findBy((array('isActive' => 1)));

        return $this->render('B2bBundle::Vitrine/index.html.twig', array(
            'brands1' => $brands,
            'brands2' => $brands2,
            'salons' => $salons
        ));

    }

    public function crmAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/crm.html.twig');

        return new Response($content);

    }

    public function marquesAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/marques.html.twig');

        return new Response($content);
    }

    public function offresMarquesAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/offres_marques.html.twig');

        return new Response($content);
    }

    public function histoireAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/histoire.html.twig');

        return new Response($content);
    }

    public function equipeAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/equipe.html.twig');

        return new Response($content);
    }

    public function contactAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/contact.html.twig');

        return new Response($content);
    }

    public function showroomAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/showroom.html.twig');

        return new Response($content);
    }


    public function salonsMarquesAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/salons_marques.html.twig');

        return new Response($content);
    }

    public function presseAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/presse.html.twig');

        return new Response($content);
    }

    public function agenceCommercialeAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/agent_commercial.html.twig');

        return new Response($content);
    }

    public function salonsBoutiquesAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/salons_boutiques.html.twig');

        return new Response($content);
    }


    public function connexionAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/connexion.html.twig');

        return new Response($content);
    }

    public function mentionsLegalesAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/mentions_legales.html.twig');

        return new Response($content);
    }

    public function cguAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/cgu.html.twig');

        return new Response($content);
    }

    public function confidentialiteAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/confidentialite.html.twig');

        return new Response($content);
    }

    public function cookiesAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/cookies.html.twig');

        return new Response($content);
    }

    public function tarifsMarquesAction(){
        $content = $this->get('templating')->render('B2bBundle::Vitrine/tarifs.html.twig');

        return new Response($content);
    }
}