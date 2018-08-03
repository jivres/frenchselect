<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Access;
use B2bBundle\Entity\Brand;
use B2bBundle\Entity\Cart;
use B2bBundle\Entity\Collection;
use B2bBundle\Entity\Customer;
use B2bBundle\Entity\Product;
use B2bBundle\Entity\Target;
use B2bBundle\Form\CartRowType;
use B2bBundle\Form\ProductManageType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductsController extends Controller
{

    private function indexForCustomer(Brand $brand, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $customer = $em->getRepository('B2bBundle:Customer')->find($user->getId());

        return $this->productsForCustomer($brand, $customer, $request);
    }

    public function productsForCustomer(Brand $brand, Customer $customer, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Boutique la plus proche
        if (count($customer->getshops()) > 0) {
            $concurrentShops = $em->getRepository('B2bBundle:Shop')->searchNearestForBrand($brand, $customer);
            $shops = $customer->getShops();
            if ($concurrentShops) {
                $smallestDistance = PHP_INT_MAX;

                // Pour chaque boutique
                foreach ($shops as $shop) {
                    // On cherche la boutique concurrente la plus proche
                    $distances = $em->createQueryBuilder()
                        ->from('B2bBundle:Shop', 's')
                        ->select('GEO(s.latitude = :latitude, s.longitude = :longitude)')
                        ->where('s IN (:ids)')
                        ->setParameter('latitude', $shop->getLatitude())
                        ->setParameter('longitude', $shop->getLongitude())
                        ->setParameter('ids', $concurrentShops)
                        ->getQuery()
                        ->execute();
                    foreach ($distances as $distance) {
                        if (array_values($distance)[0] < $smallestDistance) {
                            $smallestDistance = array_values($distance)[0];
                        }
                    }
                }
            } else {
                $smallestDistance = -1;
            }
        } else {
            $smallestDistance = -1;
        }

        $bestsellers = [];
        // TODO : remettre les meilleurs ventes (ne fonctionne pas sur le serveur)
        /*
        $bestproducts = $em->getRepository('B2bBundle:Product')->bestSellers($brand, null, null);
        foreach ($bestproducts as $bestproduct) {
            $bestsellers[] = $bestproduct[0];
        }*/
        $collections = array();
        $tests = $em->getRepository('B2bBundle:Collection')->findFor($brand);
        foreach ($tests as $test) {
            if ($test->getisActive()) {
                $collections[] = $test;
            }
        }
        $collection = null;
        $products = new ArrayCollection();

        $isRestricted = $brand->getAccessRestricted();
        if ($isRestricted) {
            $access = $em->getRepository('B2bBundle:Access')->findBy(array('brand' => $brand->getId(), 'customer' => $customer));
        } else {
            $access = null;
        }
        $cart = null;
        $carts = $em->getRepository('B2bBundle:Cart')->findDifferentFrom($brand, $customer);//->findBy(array('customer' => $customer))->where('brand' <> $brand->getId());

        if ($isRestricted && !$access)
            $access = new Access($brand, $customer);
        else {
            if ($isRestricted) {
                $access = $access[0];
            }
            $cart = $em->getRepository('B2bBundle:Cart')->findFor($brand, $customer);
            if (!$cart) {
                $cart = new Cart($brand, $customer);
                $em->persist($cart);
                $em->flush();
            } else {
                $cart = $cart[0];
            }
        }
        $cible = $em->getRepository('B2bBundle:Target')->findOneBy(array('id' => $request->get('cible')));
        if ($collections) {
            $collection = $collections[0];
            if (!$isRestricted || $access->getAllowed()) {

                $products = $em->getRepository('B2bBundle:Product')->getForCollection($collection, $cible);
            }
        }

        if ($isRestricted) {
            $accessForm = $this->createForm('B2bBundle\Form\AccessType', $access);

            $accessForm->handleRequest($request);
            if ($accessForm->isSubmitted() && $accessForm->isValid()) {
                $access->setSubmitted();
                $em->persist($access);
                $em->flush();
            }

            $accessForm = $accessForm->createView();
        } else {
            $accessForm = null;
        }


        $primaryCategories = $em->getRepository('B2bBundle:PrimaryCategory')->findAll();
        $colors = $em->getRepository('B2bBundle:Color')->findAll();
        $targets = $em->getRepository('B2bBundle:Target')->findAll();
        $sizes = $em->getRepository('B2bBundle:Size')->findAll();
        $brandRecommande = $em->getRepository('B2bBundle:BrandRecommande')->findByBrand($brand);
        return $this->render('products/index.html.twig', array(
            'brand' => $brand,
            'primaryCategories' => $primaryCategories,
            'collections' => $collections,
            'collection' => $collection,
            'cart' => $cart,
            'carts' => $carts,
            'access' => $access,
            'accessForm' => $accessForm,
            'products' => $products,
            'colors' => $colors,
            'targets' => $targets,
            'sizes' => $sizes,
            'cible' => $cible,
            'brandRecommande' => $brandRecommande,
        ));
    }

    /**
     * Lists all products entities for a specific brand
     * @param Brand $brand
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Brand $brand, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_CUSTOMER')) {
            return $this->indexForCustomer($brand, $request);
        }



        $bestsellers = [];
        // TODO : remettre
        /*
        $bestproducts = $em->getRepository('B2bBundle:Product')->bestSellers($brand, null, null);
        foreach ($bestproducts as $bestproduct) {
            $bestsellers[] = $bestproduct[0];
        }*/

        $collections = $em->getRepository('B2bBundle:Collection')->findFor($brand);
        $collection = $collections[0]; // TODO : récupérer la plus récente
        $products = $em->getRepository('B2bBundle:Product')->getForCollection($collection);

        $targets = $em->getRepository('B2bBundle:Target')->findAll();//['Femme', 'Homme', 'Enfant'];
        // TODO : Les catégories doivent avoir des sous-rubriques
        $categories = $em->getRepository('B2bBundle:PrimaryCategory')->findAll();//['Prêt-à-porter', 'Sacs / Maroquinerie', 'Accessoires', 'Montres / Bijoux', 'Chaussures', 'Décoration'];
        $colors = $em->getRepository('B2bBundle:ColorProduct')->findAll();
        $sizes = $em->getRepository('B2bBundle:Size')->findAll();
        $shippings = ['Livraison de suite'];
        $offers = ['En promotion'];

        return $this->render('products/index.html.twig', array(
            'brand' => $brand,
            'bestsellers' => $bestsellers,
            'collections' => $collections,
            'collection' => $collection,
            'products' => $products,
            'targets' => $targets,
            'categories' => $categories,
            'colors' => $colors,
            'sizes' => $sizes,
            'shippings' => $shippings,
            'offers' => $offers,
        ));
    }

    public function detailsAction(Product $product, Request $request)
    {
        $em = $this->getDoctrine();

        $sizes = $em->getManager()->getRepository('B2bBundle:AllowedSize')->findForProduct($product);

        $brand = $product->getBrand();
        $user = $this->getUser();
        $customer = null;

        if ($this->get('security.authorization_checker')->isGranted('ROLE_CUSTOMER')) {
            $customer = $em->getManager()->getRepository('B2bBundle:Customer')->find($user->getId());
        } else if ($user->isConnectedFor()) {
            $customer = $user->getConnectedFor();
        }

        if ($customer != null) {
            $cart = $em->getManager()->getRepository('B2bBundle:Cart')->findFor($brand, $customer);
            $carts = $em->getRepository('B2bBundle:Cart')->findDifferentFrom($brand, $customer);

            if (!$cart) {
                $cart = new Cart($brand, $customer);
                $em->getManager()->persist($cart);
                $em->getManager()->flush();
            } else {
                $cart = $cart[0];
            }

            // Ajout du produit dans le panier si celui-ci n'y était pas encore et récupération des lignes de panier
            $cartRows = CartController::getRowsForProduct($em, $cart, $product, $justCreated);
        } else {
            $cart = null;
            $carts = [];
            $cartRows = null;
            $justCreated = true;
        }

        $form = $this->createFormBuilder(array('cartRows' => $cartRows))
            ->add('cartRows', CollectionType::class, array(
                'entry_type' => CartRowType::class))->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cart->update($em);
            //$em->getManager()->persist($cart);
            //$em->getManager()->persist($form->getViewData());
            $em->getManager()->flush();
            if ($this->get('security.authorization_checker')->isGranted('ROLE_CUSTOMER')) {
                return $this->redirectToRoute('products_index',
                    array('id' => $product->getCollection()->getBrand()->getId(),
                        'cible' => $product->getTarget()->getId()));
            }
        }

        return $this->render('products/details.html.twig', array(
            'brand' => $brand,
            'product' => $product,
            'sizes' => $sizes,
            'carts' => $carts,
            'cart' => $cart,
            'cartRows' => $cartRows,
            'form' => $form->createView(),
            'justCreated' => $justCreated
        ));
    }

    /*
     * // query for a single product matching the given name and price
        $product = $repository->findOneBy(
            array('name' => 'Keyboard', 'price' => 19.99)
        );

        // query for multiple products matching the given name, ordered by price
        $products = $repository->findBy(
            array('name' => 'Keyboard'),
            array('price' => 'ASC')
        );


    $result = $em->getRepository("Orders")->createQueryBuilder('o')
   ->where('o.OrderEmail = :email')
   ->andWhere('o.Product LIKE :product')
   ->setParameter('email', 'some@mail.com')
   ->setParameter('product', 'My Products%')
   ->getQuery()
   ->getResult();
     */

    /**
     * Search a product according to its name with the search-bar
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
            $products = null;
            if ($filters = $request->get('filters')) {

                $primaryCategories = array();
                $targets = array();
                $sizes = array();
                $colors = array();
                $reduction = false;
                $favourite = false;
                foreach ($filters as $filter) {
                    $temp = explode(":", $filter);
                    switch ($temp[0]) {
                        case "category":
                            $primaryCategories[] = $temp[1];
                            break;
                        case "target":
                            $targets[] = $temp[1];
                            break;
                        case "size":
                            $sizes[] = $temp[1];
                            break;
                        case "color":
                            $colors[] = $temp[1];
                            break;
                        case "reduction":
                            $reduction = true;
                            break;
                        case "favourite":
                            $favourite = true;
                            break;
                        default :
                            break;
                    }
                }
                $products = $em->getRepository('B2bBundle:Product')->search($request->get('search_text'), $request->get('collection'), $primaryCategories, $targets, $sizes,$colors, $reduction, $favourite);
            }
            else{
                $products = $em->getRepository('B2bBundle:Product')->search($request->get('search_text'), $request->get('collection'));
            }
            return $this->render('products/productlist.html.twig', array('products' => $products));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    /**
     * Get all the products of the requested collection
     * @param Collection $collection
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function productsForCollectionAction(Collection $collection, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm('B2bBundle\Form\CollectionManageType', $collection, array(
            'action' => $this->generateUrl('products_for_collection', array('id' => $collection->getId())),
            'method' => 'POST'));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($collection);
            $em->flush();
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('collection/productlist.html.twig', array(
                'form' => $form->createView(),
                'collection' => $collection,
            ));
        }
        return $this->redirectToRoute('collection_index');
    }

    public function twig_json_decode($json)
    {
        return json_decode($json, true);
    }
}