<?php
/**
 * Created by PhpStorm.
 * User: Guillaume
 * Date: 07/06/2018
 * Time: 21:14
 */

namespace B2bBundle\Controller;


use B2bBundle\Entity\Target;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class VitrineBrandDetailsController extends Controller
{
    /**
     *
     * Cette fonction permet de retourner toutes les infos d'une marque à partir de son nom
     * Retourne également la liste des marques pour permettre de voir les marques concurentes
     *
     * @param $marque : nom de la marque dont on veut les détails
     * @return Response : brand => détails d'une marque, brands => liste des marques
     */
    public function getOneBrandAction($marque, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cible = $em->getRepository('B2bBundle:Target')->findOneBy(array('id' => $request->get('cible')));
        $brand = $em->getRepository('B2bBundle:Brand')->findOneByBrandName($marque);
        $brandRecommande = $em->getRepository('B2bBundle:BrandRecommande')->findByBrand($brand);

        return $this->render('B2bBundle::Vitrine/marque_details.html.twig', array(
            'brand' => $brand,
            'brandRecommande' => $brandRecommande ,
            'cible' => $cible,
        ));
    }

}