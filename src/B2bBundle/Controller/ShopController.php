<?php
/**
 * Created by PhpStorm.
 * User: sowipheur
 * Date: 18/10/2017
 * Time: 13:23
 */

namespace B2bBundle\Controller;

use B2bBundle\Entity\SalesmanShop;
use B2bBundle\Entity\Shop;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\StatefulGeocoder;
use Geocoder\Query\GeocodeQuery;
use Http\Adapter\Guzzle6\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends Controller
{


    /**
     * Lists all shop entities for the Salesman
     */
    public function indexForSalesmanAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $salesman = $em->getRepository('B2bBundle:Salesman')->find($user->getId());
        $brands = $em->getRepository('B2bBundle:Brand')->getBrands($salesman);
        $brandshops = [];
        foreach ($brands as $brand) {
            $brandshops[] = ['brand' => $brand, 'shops' => $em->getRepository('B2bBundle:Shop')->getShops($salesman, $brand)];
        }

        return $this->render('shop/salesman-index.html.twig', array(
            'brandshops' => $brandshops,
        ));
    }

    public static function saveShopLocation(Shop $shop)
    {
        $provider = new GoogleMaps(new Client(), null, 'AIzaSyBexeplm1izQBUU8RTFsIKtTy79dVgI5k0');
        $geocoder = new StatefulGeocoder($provider, 'en');

        $localization = $geocoder->geocodeQuery(GeocodeQuery::create($shop->getFullAddress()));
        if (!$localization->isEmpty()) {
            $latitude = $localization->first()->getCoordinates()->getLatitude();
            $longitude = $localization->first()->getCoordinates()->getLongitude();
            $shop->setLongitude($longitude);
            $shop->setLatitude($latitude);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Creates a new shop entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $shop = new Shop();
        $salesmen = $em->getRepository('B2bBundle:Salesman')->findAll();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN') ||
            $this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $selectSalesmen = true;
        } else {
            $selectSalesmen = false;
        }

        $form = $this->createForm('B2bBundle\Form\ShopType', $shop,
            array('select-salesmen' => $selectSalesmen,
                'select-customer' => $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'),
                'salesmenIds' => $salesmen));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (ShopController::saveShopLocation($shop)) {
                $em->persist($shop);
                $em->flush();

                return $this->redirectToRoute('backoffice_shop_show', array('id' => $shop->getId()));
            } else {
                $this->addFlash('danger', 'L\'adresse de la boutique n\'existe pas');
            }
        }

        return $this->render('shop/new.html.twig', array(
            'shop' => $shop,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a shop entity.
     * @param Shop $shop
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Shop $shop)
    {
        $deleteForm = $this->createDeleteForm($shop);

        return $this->render('shop/show.html.twig', array(
            'shop' => $shop,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing shop entity.
     * @param Request $request
     * @param Shop $shop
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Shop $shop, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($shop);
        $salesmen = $salesmen = $em->getRepository('B2bBundle:Salesman')->findAll();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN') ||
            $this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $selectSalesmen = true;
        } else {
            $selectSalesmen = false;
        }

        $editForm = $this->createForm('B2bBundle\Form\ShopType', $shop,
            array('select-salesmen' => $selectSalesmen,
                'select-customer' => $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'),
                'salesmenIds' => $salesmen));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if (ShopController::saveShopLocation($shop)) {
                $em->persist($shop);
                $em->flush();

                return $this->redirectToRoute('backoffice_shop_edit', array('id' => $shop->getId()));
            } else {
                $this->addFlash('danger', 'L\'adresse de la boutique n\'existe pas');
            }
        }

        return $this->render('shop/edit.html.twig', array(
            'shop' => $shop,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a shop entity.
     * @param Request $request
     * @param Shop $shop
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Shop $shop)
    {
        $form = $this->createDeleteForm($shop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($shop);
                $em->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_shop_index');
    }

    /**
     * Creates a form to delete a shop entity.
     *
     * @param shop $shop The Shop entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Shop $shop)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_shop_delete', array('id' => $shop->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Select the subordinate to assign to the Shop
     * @param Shop $shop
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function selectSubordinateAction(Shop $shop, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('B2bBundle:Brand')->find($request->get('brand'));
        return $this->render('shop/select-subordinate.html.twig', array(
            'shop' => $shop,
            'brand' => $brand,
        ));
    }

    /**
     * Search a salesman according to its salesman id, name, societyName with the search-bar
     * @param Shop $shop
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function searchSubordinateAction(Shop $shop, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $salesman = $em->getRepository('B2bBundle:Salesman')->find($this->getUser()->getId());
            $salesmen = $shop->getSalesmen();
            $brand = $em->getRepository('B2bBundle:Brand')->find($request->get('brand'));
            $salesmen = $em->getRepository('B2bBundle:Salesman')->searchForSubordinate(
                $request->get('search_text'), $salesman, $salesmen, $brand, $shop);
            return $this->render('salesman/salesmenlist.html.twig', array('salesmen' => $salesmen, 'checkbox' => true));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    /**
     * Assign salesmen into hierarchy (superior or subordinate)
     * @param Shop $shop
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function assignSubordinateAction(Shop $shop, Request $request)
    {
        $ids = json_decode($request->query->get('ids'), $assoc = true);
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('B2bBundle:Brand')->find($request->query->get('brand'));

        foreach ($ids as $id) {
            $salesman = $em->getRepository('B2bBundle:Salesman')->find($id);
            $salesmanShop = new SalesmanShop();
            $salesmanShop->setSalesman($salesman);
            $salesmanShop->setBrand($brand);
            $salesmanShop->setShop($shop);
            $salesman->addShop($salesmanShop);
            $shop->addSalesman($salesmanShop);
            $em->persist($salesmanShop);
        }
        $em->flush();

        return $this->forward('B2bBundle:Customer:show', array(
            'id' => $shop->getCustomer()->getId(),
            'brand' => $brand));
    }

    /**
     * Assign salesmen into hierarchy (superior or subordinate)
     * @param Shop $shop
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deassignSubordinateAction(Shop $shop, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $salesman = $em->getRepository('B2bBundle:Salesman')->find($request->query->get('salesman'));
        $brand = $em->getRepository('B2bBundle:Brand')->find($request->query->get('brand'));
        $salesmanShop = $em->getRepository('B2bBundle:SalesmanShop')->findBy(array(
            'salesman' => $salesman, 'shop' => $shop, 'brand' => $brand))[0];
        //$shop->removeSalesman($salesman);
        $em->remove($salesmanShop);
        $em->flush();

        return $this->forward('B2bBundle:Customer:show', array(
            'id' => $shop->getCustomer()->getId(),
            'brand' => $brand,
        ));
    }

    public function assignSalesmanAction(Shop $shop, Request $request)
    {

        $salesmanShop = new SalesmanShop();
        $salesmanShop->setShop($shop);
        $brands = $shop->getCustomer()->getBrand()->toArray();
        foreach ($brands as $brand) {
            foreach ($shop->getSalesmen() as $row) {
                if ($row->getBrand() == $brand) {
                    unset($brands[array_search($brand, $brands)]);
                    break;
                }
            }
        }

        return $this->render('shop/assign-salesman.html.twig', array(
            'shop' => $shop,
            'brands' => $brands,
        ));


    }

    public function deassignSalesmanAction(Shop $shop, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $salesmanShop = $em->getRepository('B2bBundle:SalesmanShop')->find($request->query->get('salesmanshop'));
        $em->remove($salesmanShop);
        $em->flush();

        return $this->redirectToRoute('brand_customer_show', array('id' => $shop->getCustomer()->getId()));
    }

    public function activateAction(Shop $shop, Request $request)
    {
        $shop->setActive();
        $em = $this->getDoctrine()->getManager();
        $em->persist($shop);
        $em->flush();

        $this->addFlash('success', 'La boutique a été activée');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    public function desactivateAction(Shop $shop, Request $request)
    {
        $shop->setInactive();
        $em = $this->getDoctrine()->getManager();
        $em->persist($shop);
        $em->flush();

        $this->addFlash('success', 'La boutique a été désactivée');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    //Appeler à la validation du formulaire d'assignation d'un commercial à un shop en tant qu'admin
    public function assignAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $salesmanShop = new SalesmanShop();
            $brand = $em->getRepository('B2bBundle:Brand')->findOneBy(array('brandName' => $request->get('brand')));
            $shop = $em->getRepository('B2bBundle:Shop')->findOneBy(array('id' => $request->get('shop')));
            $salesman = $em->getRepository('B2bBundle:Salesman')->findOneBy(array('lastName' => $request->get('salesman')));

            $salesmanShop->setBrand($brand);
            $salesmanShop->setSalesman($salesman);
            $salesmanShop->setShop($shop);
            $em->persist($salesmanShop);
            $em->flush();

            return $this->redirectToRoute("brand_customer_show", array('id' => $shop->getCustomer()->getId()));

        }
        return new Response("Error : not an Ajax call, 400");

    }
}