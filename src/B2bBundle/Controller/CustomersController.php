<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Customer;
use B2bBundle\Entity\Access;
use B2bBundle\Entity\ContactBrand;
use B2bBundle\Entity\Shop;
use B2bBundle\Entity\User;
use B2bBundle\Form\BrandType;
use Doctrine\DBAL\DBALException;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Query\GeocodeQuery;
use Geocoder\StatefulGeocoder;
use Http\Adapter\Guzzle6\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Customers controller.
 *
 */
class CustomersController extends Controller {




    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $brands = $em->getRepository('B2bBundle:Brand')->findWoman();
        $categories = $em->getRepository('B2bBundle:PrimaryCategory')->findAll();
        $univers = $em->getRepository('B2bBundle:Univers')->findAll();
        $features = $em->getRepository('B2bBundle:Feature')->findAll();
        $prices = $em->getRepository('B2bBundle:PriceRange')->findAll();
        $targets = $em->getRepository('B2bBundle:Target')->findAll();
        $cible =  $em->getRepository('B2bBundle:Target')->findOneBy(array('id'=>2)); // On récupère Femme pour les afficher à l'ouverture de la page

        return $this->render('customers/index.html.twig', array(
            'brands' => $brands,
            'categories' => $categories,
            'univers' => $univers,
            'features' => $features,
            'prices' => $prices,
            'targets' => $targets,
            'cible' => $cible,
        ));
    }





}
