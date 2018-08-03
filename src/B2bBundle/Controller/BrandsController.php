<?php

namespace B2bBundle\Controller;

use B2bBundle\B2bBundle;
use B2bBundle\Entity\Brand;
use B2bBundle\Entity\Cart;
use B2bBundle\Entity\BrandRecommande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BrandsController extends Controller
{

    /**
     * Lists all brand entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository('B2bBundle:Brand')->find($this->getUser()->getId());
        $carts = $em->getRepository('B2bBundle:Cart')->findAll();
        $countCommandes = 0;

        foreach($carts as $cart){
            if($cart->getBrand() == $brand){
                $countCommandes = $countCommandes + 1;
            }
        }

        return $this->render('brands/index.html.twig', array(
            'countCommandes' => $countCommandes,


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
            return $this->render('brands/brandlist.html.twig', array('brands' => $brands, 'cible' =>$cible ));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    public function brandRecommandeAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        //On vide la table BrandRecommande
        $brandsRecommande = $em->getRepository('B2bBundle:BrandRecommande')->findAll();
        foreach ($brandsRecommande as $brandRecommande) {
            $em->remove($brandRecommande);
        }

        $brands = $em->getRepository('B2bBundle:Brand')->findBy(array('isActive' => true));
        $brands2 = $brands;
        $targets = $em->getRepository('B2bBundle:Target')->findAll();
        $brandunivers = $em->getRepository('B2bBundle:BrandUniversCalcul')->findAll();

        $FINALB = array();

        $FINALHOMME = array();
        $FINALFEMME = array();
        $FINALENFANT = array();

        $SCOREHOMME = array();
        $SCOREFEMME = array();
        $SCOREENFANT = array();

        foreach ($brands as $brand) {
            array_push($FINALB, $brand, $brand, $brand, $brand);
        }

        foreach ($targets as $target) {
            foreach ($brands as $brand) {
                $styleBrand = $brand->getStyles();
                $categoryBrand = $brand->getCategories();
                $particularityBrand = $brand->getFeature();
                $StargetBrand = $brand->getTargets();
                $universBrand = $brand->getUnivers();

                $tabtarget = array();
                $res1 = 0;
                $res2 = 0;
                $res3 = 0;
                $res4 = 0;
                $M1 = "";
                $M2 = "";
                $M3 = "";
                $M4 = "";

                array_push($tabtarget, $brand->getPrimarytarget()->getLabel());

                foreach ($StargetBrand as $temp) {
                    array_push($tabtarget, $temp->getLabel());
                }

                if (!in_array($target->getLabel(), $tabtarget)) {
                    if ($target->getLabel() == "Homme") {
                        array_push($FINALHOMME, "", "", "", "");
                        array_push($SCOREHOMME, 0, 0, 0, 0);
                    } else if ($target->getLabel() == "Femme") {
                        array_push($FINALFEMME, "", "", "", "");
                        array_push($SCOREFEMME, 0, 0, 0, 0);

                    } else if ($target->getLabel() == "Enfant") {
                        array_push($FINALENFANT, "", "", "", "");
                        array_push($SCOREENFANT, 0, 0, 0, 0);
                    }
                } else {
                    foreach ($brands2 as $currentBrand) {
                        if ($currentBrand->getId() != $brand->getId()) {
                            $styleComp = $currentBrand->getStyles();
                            $categoryComp = $currentBrand->getCategories();
                            $particularityComp = $currentBrand->getFeature();
                            $StargetComp = $currentBrand->getTargets();
                            $universComp = $currentBrand->getUnivers();

                            $currentRes = 0;
                            $currtarget = array();

                            array_push($currtarget, $currentBrand->getPrimarytarget()->getLabel());

                            foreach ($StargetComp as $a) {
                                array_push($currtarget, $a->getLabel());
                            }
                            if (in_array($target->getLabel(), $currtarget)) {


                                foreach ($particularityComp as $particularityC) {
                                    foreach ($particularityBrand as $particularityB) {
                                        if ($particularityB->getLabel() == $particularityC->getLabel()) {
                                            $currentRes = $currentRes + 10;


                                        }
                                    }
                                }


                                if ($target == $currentBrand->getPrimarytarget()) {
                                    $currentRes = $currentRes + 6;

                                }

                                foreach ($styleComp as $styleC) {
                                    foreach ($styleBrand as $styleB) {
                                        if ($styleB->getLabel() == $styleC->getLabel()) {
                                            $currentRes = $currentRes + 7;
                                        }
                                    }
                                }
                                if ($brand->getPriceRange() == $currentBrand->getPriceRange()) {
                                    $currentRes = $currentRes + 4;
                                }

                                foreach ($categoryComp as $categoryC) {
                                    foreach ($categoryBrand as $categoryB) {
                                        if ($categoryB->getLabel() == $categoryC->getLabel()) {
                                            $currentRes = $currentRes + 3;
                                        }
                                    }
                                }
                                foreach ($universComp as $universC) {
                                    foreach ($universBrand as $universB) {
                                        if ($universB->getLabel() == $universC->getLabel()) {
                                            foreach ($brandunivers as $row) {
                                                if ($row->getBrand() == $currentBrand && $row->getUnivers() == $universB) {
                                                    $currentRes = $currentRes + $row->getPoids();
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($currentBrand->getRecommandation()->getId() == 0) {
                                    $currentRes = $currentRes * 0.9;
                                }
                                if ($currentBrand->getRecommandation()->getId() == 2) {
                                    $currentRes = $currentRes * 1.1;
                                }
                                if ($currentBrand->getRecommandation()->getId() == 3) {
                                    $currentRes = $currentRes * 1.25;
                                }

                                if ($currentRes > $res1) {
                                    $res4 = $res3;
                                    $res3 = $res2;
                                    $res2 = $res1;
                                    $res1 = $currentRes;
                                    $M4 = $M3;
                                    $M3 = $M2;
                                    $M2 = $M1;
                                    $M1 = $currentBrand;
                                } else if ($currentRes > $res2) {
                                    $res4 = $res3;
                                    $res3 = $res2;
                                    $res2 = $currentRes;
                                    $M4 = $M3;
                                    $M3 = $M2;
                                    $M2 = $currentBrand;
                                } else if ($currentRes > $res3) {
                                    $res4 = $res3;
                                    $res3 = $currentRes;
                                    $M4 = $M3;
                                    $M3 = $currentBrand;
                                } else if ($currentRes > $res4) {
                                    $res4 = $currentRes;
                                    $M4 = $currentBrand;
                                }
                            }
                        }

                    }

                    $tabRes = array($M1, $M2, $M3, $M4);
                    $scoreRes = array($res1, $res2, $res3, $res4);

                    if ($target->getLabel() == "Homme") {
                        foreach ($tabRes as $temp) {

                            array_push($FINALHOMME, $temp);
                        }

                        foreach ($scoreRes as $temp) {
                            array_push($SCOREHOMME, $temp);
                        }
                    } else if ($target->getLabel() == "Femme") {
                        foreach ($tabRes as $temp) {
                            array_push($FINALFEMME, $temp);
                        }

                        foreach ($scoreRes as $temp) {
                            array_push($SCOREFEMME, $temp);
                        }
                    } else if ($target->getLabel() == "Enfant") {
                        foreach ($tabRes as $temp) {
                            array_push($FINALENFANT, $temp);
                        }

                        foreach ($scoreRes as $temp) {
                            array_push($SCOREENFANT, $temp);
                        }
                    }


                }
            }
        }

        $x = count($FINALB);
        for ($i = 0; $i < $x; $i++) {
            $this->setBrandCompeting($FINALB[$i], $FINALHOMME[$i], $FINALFEMME[$i], $FINALENFANT[$i]
                , $SCOREHOMME[$i], $SCOREFEMME[$i], $SCOREENFANT[$i]);
        }

        $em->flush();


        $this->addFlash('success', "Les marques recommandées ont bien été calculées !");
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);

    }


    public function setBrandCompeting($idBrand, $idHomme, $idFemme, $idEnfant, $scoreHomme, $scoreFemme, $scoreEnfant)
    {

        $brandrecommande = new BrandRecommande();
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('B2bBundle:Brand')->find($idBrand);
        $recHomme = $em->getRepository('B2bBundle:Brand')->find($idHomme);
        $recFemme = $em->getRepository('B2bBundle:Brand')->find($idFemme);
        $recEnfant = $em->getRepository('B2bBundle:Brand')->find($idEnfant);

        $brandrecommande->setBrand($brand);
        $brandrecommande->setRecommandeHomme($recHomme);
        $brandrecommande->setRecommandeFemme($recFemme);
        $brandrecommande->setRecommandeEnfant($recEnfant);
        $brandrecommande->setScoreHomme($scoreHomme);
        $brandrecommande->setScoreFemme($scoreFemme);
        $brandrecommande->setScoreEnfant($scoreEnfant);

        $em->persist($brandrecommande);
    }
}
