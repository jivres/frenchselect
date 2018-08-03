<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Cart;
use B2bBundle\Entity\CartCategory;
use B2bBundle\Entity\CartCollection;
use B2bBundle\Entity\CartRow;
use B2bBundle\Entity\Collection;
use B2bBundle\Entity\PrimaryCategory;
use B2bBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CartController extends Controller
{

    public function indexAction()
    {

    }

    /**
     * List all carts of the customer
     * @param Cart $cart
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Cart $cart, Request $request)
    {
        $em = $this->getDoctrine();

        //$customer = $em->getRepository('B2bBundle:Customer')->find($this->getUser()->getId());

        $form = $this->createForm('B2bBundle\Form\CartType', $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($cart->getCartCollections() as $cartCollection) {
                if ($cartCollection->update($em) == 0) {
                    $command = $em->getRepository('B2bBundle:Command')->findForCartCollection($cartCollection);
                    if ($command) {
                        $em->getManager()->remove($command[0]);
                    }
                    $cart->removeCartCollection($cartCollection);
                    $em->getManager()->remove($cartCollection);
                }
            }
            $em->getmanager()->flush();

            //return $this->redirectToRoute('backoffice_product_edit', array('id' => $product->getId()));
        }

        $validateMessage = '';
        $total = $cart->getTotal();

        // Vérification du montant minimum du panier
        $canValidate = $cart->getBrand()->getCommandMin() < $total;
        if (!$canValidate) {
            $validateMessage = 'Le montant total de votre panier est en-dessous du montant minimum de cette marque fixé à ' . $cart->getBrand()->getCommandMin() . '€.';
        } else {
            // Vérification si le compte a suffisamment d'informations pour passer une commande
            $validateMessage = '<p>Pour pouvoir passer une commande, vous devez renseigner d\'abord les champs suivants :</p><ul>';
            if (empty($cart->getCustomer()->getNumTVA()) && ($cart->getCustomer()->getDeductibleTVA() == 0)) {
                $canValidate = false;
                $validateMessage .= '<li>numéro de TVA</li>';
            }
            if (empty($cart->getCustomer()->getBillingAddress()) ||
                empty($cart->getCustomer()->getBillingZP()) ||
                empty($cart->getCustomer()->getBillingTown())) {
                $canValidate = false;
                $validateMessage .= '<li>adresse de facturation</li>';
            }
            if (count($cart->getCustomer()->getShops()) == 0) {
                $canValidate = false;
                $validateMessage .= '<li>boutique</li>';
            }
            $validateMessage .= '</ul>';
        }

        return $this->render('cart/index.html.twig', array(
            'form' => $form->createView(),
            'canValidate' => $canValidate,
            'validateMessage' => $validateMessage,
            'c' => $cart,
        ));
    }

    private static function getOrCreateCartCollection(ManagerRegistry $em, Cart $cart, Collection $collection)
    {
        $cartCollection = $em->getRepository('B2bBundle:CartCollection')->findCollection($cart, $collection);
        if (!$cartCollection) {
            $cartCollection = new CartCollection();
            $cartCollection->setCollection($collection);

            $cart->addCartCollection($cartCollection);

            $em->getManager()->persist($cartCollection);
            $em->getManager()->flush();
        } else {
            $cartCollection = $cartCollection[0];
        }

        return $cartCollection;
    }

    private static function getOrCreateCartCategory(ManagerRegistry $em, CartCollection $cartCollection, PrimaryCategory $category)
    {
        $cartCategory = $em->getRepository('B2bBundle:CartCategory')->findCategory($cartCollection, $category);
        if (!$cartCategory) {
            $cartCategory = new CartCategory();
            $cartCategory->setCategory($category);

            $cartCollection->addCartCategory($cartCategory);

            $em->getManager()->persist($cartCategory);
            $em->getManager()->flush();
        } else {
            $cartCategory = $cartCategory[0];
        }

        return $cartCategory;
    }

    private static function getOrCreateCartCategoryForProduct(ManagerRegistry $em, Cart $cart, Product $product)
    {
        $cartCollection = self::getOrCreateCartCollection($em, $cart, $product->getCollection());
        return self::getOrCreateCartCategory($em, $cartCollection, $product->getPrimaryCat());
    }

    public static function getRowsForProduct(ManagerRegistry $em, Cart $cart, Product $product, &$justCreated)
    {
        $cartCategory = self::getOrCreateCartCategoryForProduct($em, $cart, $product);

        $cartRows = [];
        $justCreated = true;

        // Création des lignes du panier si inexistantes
        $availabilities = $product->getAvailabilities();
        foreach ($availabilities as $availability) {
            $color = $availability->getColor();
            $cartRow = $em->getRepository('B2bBundle:CartRow')->findCartRow($cartCategory, $product, $color);
            if (!$cartRow) {
                $cartRow = new CartRow();
                $cartRow->setProduct($product, $availability);
                $cartCategory->addCartRow($cartRow);
                $em->getManager()->persist($cartRow);
                $em->getManager()->flush();
            } else {
                $cartRow = $cartRow[0];
            }
            if ($justCreated and $cartRow->hasQuantity()) {
                $justCreated = false;
            }
            $cartRows[] = $cartRow;
        }

        return $cartRows;
    }

    public function addAction(Cart $cart, Request $request)
    {
        $em = $this->getDoctrine();
        $product = $em->getRepository('B2bBundle:Product')->find($request->request->get('product'))[0];

        $cartCategory = self::getOrCreateCartCategoryForProduct($em, $cart, $product);

        // Création de la ligne du panier
        $cartRow = new CartRow();
        $cartRow->setProduct($product);

        $availabilities = $product->getAvailabilities();
        $cartRow->setColor($availabilities->first()->getColor());
        $cartRow->setSize($availabilities->first()->getSize());
        $cartRow->setQuantity(0);

        $cartCategory->addCartRow($cartRow);
        $em->getManager()->persist($cartRow);
        $em->getManager()->flush();

        //return new JsonResponse(array('count' => $cart->getCartRows()->count()));
        //return new JsonResponse(array('rows' => $rows, 'row' => $cartRow, 'cart' => $cart));
        return $this->render('cart/short-current.html.twig', array('cart' => $cart));
    }


    public function copyAction(Cart $cart, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $row = $em->getRepository('B2bBundle:CartRow')->find($request->request->get('row'));

        $cartRow = new CartRow();
        $product = $row->getProduct();
        $cartRow->setProduct($product);
        $availabilities = $product->getAvailabilities();
        $cartRow->setColor($availabilities->first()->getColor());
        $cartRow->setSize($availabilities->first()->getSize());
        $cartRow->setQuantity(0);

        $row->getCartCollection()->addCartRow($cartRow);
        $em->persist($cartRow);
        $em->flush();

        return new JsonResponse(array('id' => $cartRow->getId()));
    }

    public function editAction(Cart $cart, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cartRow = $em->getRepository('B2bBundle:CartRow')->find($request->request->get('row'));
        $cartRow->setColor($em->getRepository('B2bBundle:ColorProduct')->find($request->request->get('color')));
        $cartRow->setSize($em->getRepository('B2bBundle:Size')->find($request->request->get('size')));
        $cartRow->setQuantity($request->request->get('quantity'));
        $em->persist($cartRow);
        $em->flush();

        return new JsonResponse();
    }

    public function editQuantityAction(CartRow $cart, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('B2bBundle:Product')->find($request->request->get('product_id'));
        $color = $em->getRepository('B2bBundle:ColorProduct')->find($request->request->get('color'));
        $size = $em->getRepository('B2bBundle:Size')->find($request->request->get('size'));
        $quantity = $request->request->get('val');

        $cartCollection = $em->getRepository('B2bBundle:CartCollection')->findCollection($cart, $product->getCollection())[0];
        $cartRow = $em->getRepository('B2bBundle:CartRow')->findCartRow($cartCollection, $product, $color)[0];

        $index = $product->getIndexOfSize($size);

        $cartRow->setQuantityAt($index, $quantity);

        return new JsonResponse();
    }

    public function removeAction(Cart $cart, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $cartRow = $em->getRepository('B2bBundle:CartRow')->find($request->request->get('row'));

        $cartCollection = $cartRow->getCartCollection();
        $cartCollection->removeCartRow($cartRow);
        $em->remove($cartRow);
        $em->flush();

        if ($cartCollection->getCartRows()->size() == 0) {
            $cart->removeCartCollection($cartCollection);
            $em->remove($cartCollection);
            $em->flush();
        }

        return new JsonResponse();
    }

    public function deleteAction(Request $request, Cart $cart)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($cart);
        $em->flush();

        if ($request->get("redirect")) {
            $referer = $request->headers->get('referer');
            $this->addFlash('success', "Le panier a bien été supprimé");
            return $this->redirect($referer);

        } else {
            return $this->redirectToRoute('customers_index');
        }

    }


}
