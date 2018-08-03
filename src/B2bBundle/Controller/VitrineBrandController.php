<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 07/06/2018
 * Time: 21:13
 */

namespace B2bBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class VitrineBrandController extends Controller
{
    public function listBrandAction(){
        $em = $this->getDoctrine()->getManager();

        $brands = $em->getRepository('B2bBundle:Brand')->findWoman();
        $categories = $em->getRepository('B2bBundle:PrimaryCategory')->findAll();
        $univers = $em->getRepository('B2bBundle:Univers')->findAll();
        $features = $em->getRepository('B2bBundle:Feature')->findAll();
        $prices = $em->getRepository('B2bBundle:PriceRange')->findAll();
        $targets = $em->getRepository('B2bBundle:Target')->findAll();
        $cible =  $em->getRepository('B2bBundle:Target')->findOneBy(array('id'=>2)); // On récupère Femme pour les afficher à l'ouverture de la page

        return $this->render('B2bBundle::Vitrine/marques.html.twig', array(
            'brands' => $brands,
            'categories' => $categories,
            'univers' => $univers,
            'features' => $features,
            'prices' => $prices,
            'targets' => $targets,
            'cible' => $cible,
        ));
    }

    /**
     * Search Brands
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
            $brands = null;
            $target= $request->get('target');
            if ($filters = $request->get('filters')) {

                $categories = array();
                $univers = array();
                $features = array();
                $prices = array();

                foreach ($filters as $filter) {
                    $temp = explode(":", $filter);
                    switch ($temp[0]) {
                        case "categorie":
                            $categories[] = $temp[1];
                            break;
                        case "univers":
                            $univers[] = $temp[1];
                            break;
                        case "feature":
                            $features[] = $temp[1];
                            break;
                        case "priceRange":
                            $prices[] = $temp[1];
                            break;
                        default :
                            break;
                    }
                }
                $brands = $em->getRepository('B2bBundle:Brand')->search($request->get('search_text'), $target, $categories, $univers, $features, $prices);
            }
            else{
                $brands = $em->getRepository('B2bBundle:Brand')->search($request->get('search_text'), $target);
            }
            $cible = $em->getRepository('B2bBundle:Target')->findOneBy(array('id'=>$target));
            return $this->render('B2bBundle::Vitrine/brandlist.html.twig', array('brands' => $brands, 'cible' =>$cible ));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    public function cardsBrandsAction($brands){
        return $this->render('B2bBundle::Vitrine/cardmarque.html.twig', array(
            'brands'=> $brands
        ));
    }

}