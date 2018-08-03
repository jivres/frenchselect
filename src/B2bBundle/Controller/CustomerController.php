<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Customer;
use B2bBundle\Entity\Access;
use B2bBundle\Entity\ContactBrand;
use B2bBundle\Entity\Shop;
use B2bBundle\Entity\User;
use B2bBundle\Entity\Brand;
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
 * Customer controller.
 *
 */
class CustomerController extends Controller {

    /**
     * List all customers and shops for a brand
     */
    public function indexForBrandAction() {
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository('B2bBundle:Brand')->find($this->getUser()->getId());

        return $this->render('customer/brand-index.html.twig', array(
            'brand' => $brand
        ));
    }

    /**
     * Lists all customer entities.
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $customers = $em->getRepository('B2bBundle:Customer')->findAll();

        return $this->render('customer/index.html.twig', array(
            'customers' => $customers,
        ));
    }

    /**
     * Creates a new customer entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('B2bBundle:User')->findAll();
        $customer = new Customer();
        $form = $this->createForm('B2bBundle\Form\CustomerType', $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($users as $u){
                if($u->getMail() == $customer->getMail()){
                    $this->addFlash("danger", "L'adresse email est déjà utilisée par un autre compte ");
                    return $this->render('customer/new.html.twig', array(
                        'customer' => $customer,
                        'form' => $form->createView(),
                    ));
                }
            }
            $customer->setUsername($customer->getMail());
            $em = $this->getDoctrine()->getManager();
            $canSave = true;
            foreach ($customer->getShops() as $shop) {
                $customer->addShop($shop);
                $shop->setActive();
                //$canSave = $canSave && ShopController::saveShopLocation($shop);
            }

            if ($canSave) {
                $em->persist($customer);

                if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                    $route = 'backoffice_customer_show';
                } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
                    $route = 'backoffice_customer_show';
                } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
                    $route = 'brand_customer_show';
                    $brand = $em->getRepository('B2bBundle:Brand')->find($this->getUser()->getId());
                    $access = new Access($brand, $customer);
                    $access->setHandled();
                    $access->setAllowed(true);
                    $em->persist($access);
                }

                $customer->setInactive();
                $customer->setPassword(User::NO_PASSWORD);

                $em->flush();

                if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                    SecurityController::activateAccount($customer, $this);
                }

                return $this->redirectToRoute($route, array('id' => $customer->getId()));
            } else {
                $this->addFlash('danger', 'L\'adresse de la boutique n\'existe pas');
            }
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $view = 'customer/admin-new.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            $view = 'customer/salesman-new.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $view = 'customer/brand-new.html.twig';
        }

        return $this->render($view, array(
            'customer' => $customer,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a customer entity.
     * @param Customer $customer
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Customer $customer, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($customer);
        $brands = $em->getRepository('B2bBundle:Brand')->findBy(array('isActive' => true));
        $assignForm = $this->createForm('B2bBundle\Form\AssignBrandToCustomerType', $customer, array('brands' => $brands));
        $assignForm->handleRequest($request);


        $brand = null;

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $view = 'customer/admin-show.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            $em = $this->getDoctrine()->getManager();
            $brand = $em->getRepository('B2bBundle:Brand')->find($request->get('brand'));
            $view = 'customer/salesman-show.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $view = 'customer/brand-show.html.twig';
        }

        if(($assignForm->isSubmitted()) && ($assignForm->isValid())){
            $em->flush();
        }

        return $this->render($view, array(
            'customer' => $customer,
            'delete_form' => $deleteForm->createView(),
            'brand' => $brand,
            'assignForm' => $assignForm->createView(),
        ));
    }

    public function shopEditAction(Request $request, Shop $shop) {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteFormShop($shop);
        $canDelete = true;
        $deleteMessage = '';
        $commands = $em->getRepository('B2bBundle:Command')->findForShop($shop);
        if ($commands) {
            $canDelete = false;
            $deleteMessage = 'Impossible de supprimer la boutique, des commandes passées la référencent';
        }

        /*$salesmen = [];
        $selectSalesmen = true;

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $salesmen = $em->getRepository('B2bBundle:Salesman')->findAll();
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $brand = $em->getRepository('B2bBundle:Brand')->find($this->getUser()->getId());
            foreach ($brand->getSalesmen() as $salesman_link) {
                $salesmen[] = $salesman_link->getSalesman();
            }
            if (count($salesmen) == 0) {
                $selectSalesmen = false;
            }
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            $salesman = $em->getRepository('B2bBundle:Salesman')->find($this->getUser()->getId());
            foreach ($salesman->getSubordinates() as $subsalesmanlink) {
                $salesmen[] = $subsalesmanlink->subordinate;
            }
            if (count($salesmen) == 0) {
                $selectSalesmen = false;
            }
        } else {
            $selectSalesmen = false;
        }*/

        $editForm = $this->createForm('B2bBundle\Form\ShopType', $shop);

        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {
         //   if (ShopController::saveShopLocation($shop)) {



                $em->flush();
                if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                    $route = 'backoffice_customer_show';
                } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
                    $route = 'brand_customer_show';
                } else {
                    $route = 'b2b_customer_shops';
                }
                return $this->redirectToRoute($route, array('id' => $shop->getCustomer()->getId()));
           /* } else {
                $this->addFlash('danger', 'L\'adresse de la boutique n\'existe pas');
            }*/
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $view = 'shop/admin-edit.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND') ||
                   $this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            $view = 'shop/brand-edit.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_CUSTOMER')) {
            $view = 'shop/customer-edit.html.twig';
        }

        return $this->render($view, array(
            'shop' => $shop,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'canDelete' => $canDelete,
            'deleteMessage' => $deleteMessage,
            //'selectSalesmen' => $selectSalesmen,
        ));
    }


    /**
     * Displays a form to edit an existing customer entity.
     * @param Request $request
     * @param Customer $customer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Customer $customer) {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('B2bBundle:User')->findAll();
        $mail = $customer->getMail();
        $deleteForm = $this->createDeleteForm($customer);
        $editForm = $this->createForm('B2bBundle\Form\CustomerType', $customer, array('shops' => false));
        $editForm->handleRequest($request);

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $view = 'customer/admin-edit.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            $view = 'customer/salesman-edit.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $view = 'customer/brand-edit.html.twig';
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($users as $u){
                if($u->getMail() == $customer->getMail() && $customer->getMail() != $mail){
                    $this->addFlash("danger", "L'adresse email est déjà utilisée par un autre compte ");
                    return $this->render($view, array(
                        'customer' => $customer,
                        'edit_form' => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                    ));
                }
            }
            $canSave = true;
            foreach($customer->getShops() as $shop) {
                $customer->addShop($shop);
                $shop->setActive();
               // $canSave = $canSave && ShopController::saveShopLocation($shop);
            }
            if ($canSave) {
                $this->getDoctrine()->getManager()->flush();

                if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                    $route = 'backoffice_customer_show';
                } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
                    $route = 'backoffice_customer_edit';
                } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
                    $route = 'brand_customer_edit';
                }
                return $this->redirectToRoute($route, array('id' => $customer->getId()));
            } else {
                $this->addFlash('danger', 'L\'adresse de la boutique n\'existe pas');
            }
        }

        return $this->render($view, array(
            'customer' => $customer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a new shop entity.
     * @param Customer $customer
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function shopAddAction(Customer $customer, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $shop = new Shop();
        $shop->setActive();

        /*$salesmen = [];

        $selectSalesmen = true;
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $salesmen = $em->getRepository('B2bBundle:Salesman')->findAll();
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $brand = $em->getRepository('B2bBundle:Brand')->find($this->getUser()->getId());
            foreach ($brand->getSalesmen() as $salesman_link) {
                $salesmen[] = $salesman_link->getSalesman();
            }
            if (count($salesmen) == 0) {
                $selectSalesmen = false;
            }
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            $salesman = $em->getRepository('B2bBundle:Salesman')->find($this->getUser()->getId());
            foreach ($salesman->getSubordinates() as $subsalesmanlink) {
                $salesmen[] = $subsalesmanlink->subordinate;
            }
            if (count($salesmen) == 0) {
                $selectSalesmen = false;
            }
        } else {
            $selectSalesmen = false;
        }*/

        $form = $this->createForm('B2bBundle\Form\ShopType', $shop,
            array(//'select-salesmen' => $selectSalesmen,
                'select-customer' => $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'),
                /*'salesmenIds' => $salesmen*/));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //if (ShopController::saveShopLocation($shop)) {
                $customer->addShop($shop);
                /*if ($this->get('security.authorization_checker')->isGranted('ROLE_CUSTOMER')) {
                    $em->getRepository('B2bBundle:Customer')->find($this->getUser()->getId())->addShop($shop);
                }*/
                $em->persist($shop);
                $em->flush();

                if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
                    $route = 'backoffice_customer_show';
                } else {
                    $route = 'b2b_customer_shops';
                }
                return $this->redirectToRoute($route, array('id' => $shop->getCustomer()->getId()));
           /* } else {
                $this->addFlash('danger', 'L\'adresse de la boutique n\'existe pas');
            }*/
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $view = 'shop/admin-new.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $view = 'shop/brand-new.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_CUSTOMER')) {
            $view = 'shop/customer-new.html.twig';
        }

        return $this->render($view, array(
            'customer' => $customer,
            'shop' => $shop,
            'form' => $form->createView(),
            //'selectSalesmen' => $selectSalesmen,
        ));
    }


    /**
     * Deletes a customer entity.
     *
     */
    public function deleteAction(Request $request, Customer $customer) {
        $form = $this->createDeleteForm($customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($customer);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $route = 'backoffice_customer_index';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            $route = 'backoffice_customer_index';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $route = 'brand_customer_index';
        }
        return $this->redirectToRoute($route);
    }

    public function shopsAction() {
        $user = $this->getUser();
        return $this->render('B2bBundle:Customer:index.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Creates a form to delete a customer entity.
     *
     * @param Customer $customer The Customer entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(Customer $customer) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_customer_delete', array('id' => $customer->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Deletes a shop entity.
     * @param Request $request
     * @param Shop $shop
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function shopDeleteAction(Request $request, Shop $shop) {
        $customer = $shop->getCustomer();
        $form = $this->createDeleteFormShop($shop);
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

        if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $route = 'backoffice_customer_show';
        } else {
            $route = 'b2b_customer_shops';
        }

        return $this->redirectToRoute($route, array('id' => $customer->getId()));
    }

    /**
     * Creates a form to delete a shop entity.
     *
     * @param shop $shop The Shop entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteFormShop(Shop $shop) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('b2b_customer_shop_delete', array('id' => $shop->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * Search a salesman according to its salesman id, name, societyName with the search-bar
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $customers = $em->getRepository('B2bBundle:Customer')->searchCustomer($request->get('search_text'));
            return $this->render('customer/customerslist.html.twig', array('customers' => $customers, 'checkbox' => true));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    public function mailCampaignAction(Request $request) {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $form = $this->createFormBuilder()
                ->add('brand', EntityType::class, array(
                    'label'       => 'Clients travaillant avec la marque :',
                    'class'       => 'B2bBundle:Brand',
                    'empty_data'  => null,
                    'placeholder' => 'Aucune marque spécifique',
                    'required'    => false,
                    'expanded'    =>false,
                    'multiple'    =>false,
                    'attr'        => ['class' => 'custom-select col-md-6']))
                ->add('departements', EntityType::class, array(
                    'label'    => 'Clients ayant une boutique dans les départements :',
                    'class'    => 'B2bBundle:Departement',
                    //'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'attr'     => ['style' => 'column-width:220px'],))
                ->add('categories', EntityType::class, array(
                    'label'    => 'Clients ayant une boutique vendant des produits des catégories :',
                    'class'    => 'B2bBundle:PrimaryCategory',
                    'expanded' =>true,
                    'multiple' =>true,
                    'attr'     => ['style' => 'column-width:220px'],))
                ->add('objet', TextType::class)
                ->add('message', TextareaType::class, array(
                    'attr' => ['rows' => '10']
                ))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() /*&& $form->isValid()*/) {
                $em = $this->getDoctrine()->getManager();
                // data is an array with "name", "email", and "message" keys
                $data = $form->getData();

                $depNums = [];
                foreach ($data['departements'] as $departement) {
                    $depNums[] = $departement->getZipCode();
                }

                // Sélection des clients
                $customers = $em->getRepository('B2bBundle:Customer')->findCustomers($data['brand'], $depNums, $data['categories']);
                $mails = [];
                foreach ($customers as $customer) {
                    $mails[] = $customer->getContact()->getMail();
                }

                if (count($mails) > 0) {
                    // Envoi des mails aux clients
                    $message = (new \Swift_Message($data['objet']))
                        ->setFrom('send@example.com')
                        ->setBcc($mails)
                        ->setBody($data['message']);
                    $this->get('mailer')->send($message);


                    $this->addFlash("success", "Campagne d'email envoyée à " . count($customers) . " clients");
                    return $this->redirectToRoute('b2b_homepage');
                } else {
                    $this->addFlash("danger", "Aucun client ne correspond à vos critères de filtre.");
                }
            }

            return $this->render('customer/mail-campaign.html.twig', array(
                'form' => $form->createView(),
            ));
        }
        else {
            throw $this->createAccessDeniedException('You cannot access this page!');
        }
    }






    /***************************
     * BEGIN IMPORTATION EXCEL *
     ***************************/


    public function importAction(Request $request){
        $form = $this->createForm('B2bBundle\Form\UploadedXLSType', null, array('isCustomerImport' => true));
        $form->handleRequest($request);

        if($request->isMethod('POST')){
            // $file = $request->get('file');
            $file = $form['file']->getData();
            $brand = $form['brand']->getData();
            $reader = $this->get('arodiss.xls.reader');
            $iterator = $reader->getReadIterator($file->getRealPath());
            dump($iterator->current());
            // On saute la ligne des entetes
            $iterator->next();
            $errors = array();
            $buf = array();
            $entityList = array();

            $em = $this->getDoctrine()->getManager();


            while($iterator->valid())
            {
                $line = $iterator->current();
                $noError = $this->parsingLine($line, $buf, $errors, $entityList);
                $iterator->next();
            }
            if (count($errors) > 0) {
                $this->get('session')->set('buf_entities', $buf);
                $this->get('session')->set('brand_id', $brand->getId());
                return $this->render('customer/revise.import.html.twig', array(
                    'errors' => $errors,
                    'entityList' => $entityList
                ));
            }
            return $this->importFixing(null, $buf, $brand);
        }
        return $this->render('customer/import.html.twig', array(
            'form' => $form->createView()
        ));
    }

    private function parsingLine($line, &$buf, &$errors, &$entityList)
    {
        $noError = true;
        $em = $this->getDoctrine()->getManager();
        $username = $line[3];
        if($username == ""){
            // on ignore la ligne
            return true;
        }
        if($em->getRepository('B2bBundle:User')->findByUsername($username) != null){
            $this->addFlash(
                'warning',
                "Le nom d'utilisateur $username est déjà utilisé (Le compte existe peut-être déjà, ou un autre compte utilise le même)"
            );
            return false;
        }
        // $this->preSetter($buf, $username, 'password', 'mdp', null, 'setPassword'); //
        $this->preSetter($buf, $username, 'mail', $line[2], null, 'setMail');
        $this->preSetter($buf, $username, 'username', $username, null, 'setUsername');
        $this->preSetter($buf, $username, 'contactName', $line[4], null, 'getContact', false, true, false, null, null, null, 'setLastname');
        $this->preSetter($buf, $username, 'contactFirstName', $line[5], null, 'getContact', false, true, false, null, null, null, 'setFirstname');
        // $this->preSetter($buf, $username, 'contactRole', $line[6], null, 'getContact', false, true, false, null, null, null, 'setRole');
        $this->preSetter($buf, $username, 'contactMail', $line[7], null, 'getContact', false, true, false, null, null, null, 'setMail');
        $this->preSetter($buf, $username, 'contactPhone', $line[8], null, 'getContact', false, true, false, null, null, null, 'setPhone');
        // $this->preSetter($buf, $username, 'salesman', $line[9], null, '');
        // $noError = $this->preSetterEntity(
        //     $buf, $errors, $entityList, $username,
        //     'salesman',
        //     $line[9],
        //     'setSalesman',
        //     $em->getRepository('B2bBundle:Salesman'),
        //     'findOneByUsername',
        //     'B2bBundle\Entity\Salesman',
        //     ''
        //     ) && $noError;

        // $this->preSetter($buf, $username, 'paymentRicks', $line[10], null, '');
        $this->preSetter($buf, $username, 'companyName', $line[11], null, 'setCompanyName');
        $this->preSetter($buf, $username, 'TVA', $line[12], null, 'setNumTVA');
        // $this->preSetter($buf, $username, '', $line[13], null, '');
        // $this->preSetter($buf, $username, 'siren', $line[14], null, 'setSiren');
        $this->preSetter($buf, $username, 'billingAddress', $line[15], null, 'setBillingAddress');
        $this->preSetter($buf, $username, 'billingZP', $line[16], null, 'setBillingZP');
        $this->preSetter($buf, $username, 'billingTown', $line[17], null, 'setBillingTown');
        // $this->preSetter($buf, $username, 'billingCountry', $line[18], null, 'setBillingCountry');
        $noError = $this->preSetterEntity(
                $buf, $errors, $entityList, $username,
                'country',
                $line[18],
                'setBillingCountry',
                $em->getRepository('B2bBundle:Country'),
                'findOneByName',
                'B2bBundle\Entity\Country',
                'setName'
            ) && $noError;
        return $noError;

    }


    private function preSetterEntity(&$buf, &$errors, &$entityList, $username, $field, $val, $setter, $repo, $finder, $entity, $setVal, $isCollection = false)
    {
        $obj = $repo->$finder($val);
        if ($obj == null) {
            $entityList[$entity] = $repo->findAll();
            $this->preSetter($buf, $username, $field, $val, null, $setter, $isCollection, false, true, $repo, $finder, $entity, $setVal);
            $this->preSetter($errors, $username, $field, $val, null, $setter, $isCollection, false, true, $repo, $finder, $entity, $setVal);
            return false;
        } else {
            $this->preSetter($buf, $username, $field, $val, $obj->getId(), $setter, $isCollection, false, false, $repo, $finder, $entity, $setVal);
            return true;
        }

    }



    private function preSetter(&$buf, $username, $field, $val, $id, $setter, $isCollection = false, $isContact = false, $hasError = false, $repo = null, $finder = null, $entity = null, $setVal = null){
        if ($isCollection) {
            if (!isset($buf[$username][$field])) {
                $buf[$username][$field]['data'] = array();
            }
            $buf[$username][$field]['type'] = 'array';
            $buf[$username][$field]['data'][] = array(
                'hasError' => $hasError,
                'val' => $val,
                'id' => $id,
                'setter' => $setter,
                // 'repo' => $repo,
                // 'finder' => $finder,
                'entity' => $entity,
                'setVal' => $setVal
            );
        } elseif ($isContact) {
            if (!isset($buf[$username]['contact'])) {
                $buf[$username]['contact']['data'] = array();
            }
            $buf[$username]['contact']['type'] = 'contact';
            $buf[$username]['contact']['data'][$field] = array(
                'hasError' => $hasError,
                'val' => $val,
                'id' => $id,
                'setter' => $setter,
                // 'repo' => $repo,
                // 'finder' => $finder,
                'entity' => $entity,
                'setVal' => $setVal
            );
        } else {
            $buf[$username][$field]['type'] = 'element';
            $buf[$username][$field]['data'] = array(
                'hasError' => $hasError,
                'val' => $val,
                'id' => $id,
                'setter' => $setter,
                // 'repo' => $repo,
                // 'finder' => $finder,
                'entity' => $entity,
                'setVal' => $setVal
            );
        }
    }


    private function importFixing($corrections, $buf, $brand){
        $logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();

        $nbCustomerPersisted = 0;
        dump($buf);
        foreach ($buf as $username => $customer_array) {
            $customer = new Customer();
            foreach ($customer_array as $field => $data) {
                // dump($field);
                // dump($data);
                if ($data['type'] == 'array') {
                    foreach ($data['data'] as $elem) {
                        $willPersist = $this->fixImport($customer, $elem, $corrections, $username, $field, true);
                        if (!$willPersist) {
                            break;
                        }
                    }
                } elseif ($data['type'] == 'contact') {
                    dump($data);
                    if($customer->getContact() == null){
                        $contact = new ContactBrand();
                        $customer->setContact($contact);
                    }
                    foreach ($data['data'] as $elem ) {
                        $setVal = $elem['setVal'];
                        $contact->$setVal($elem['val']);
                    }
                } else {
                    $willPersist = $this->fixImport($customer, $data['data'], $corrections, $username, $field);
                }
                if (!$willPersist) {
                    break;
                }
            }

            if ($willPersist) {
                $logger->debug('persit - '.$username);
                $access = new Access($brand, $customer);
                $access->setAllowed(true);
                $access->setMotive('[Client importé]');
                $access->setHandled(Access::STATUS_HANDLED);
                $customer->addAccess($access);
                $em->persist($customer);
                $nbCustomerPersisted++;
            } else {
                $logger->debug('don\'t persit - '.$username);
            }
        }
        $em->flush();
        if ($nbCustomerPersisted == 0) {
            $this->addFlash(
                'warning',
                "Aucun client n'a été importé"
            );
        } elseif ($nbCustomerPersisted == 1) {
            $this->addFlash(
                'success',
                "$nbCustomerPersisted client a été importé"
            );
        } else {
            $this->addFlash(
                'success',
                "$nbCustomerPersisted clients ont été importés"
            );
        }
        return $this->redirectToRoute('backoffice_customer_import');

    }

    private function fixImport($customer, $elem, &$corrections, $username, $field, $isArray = false){
        if ($elem['hasError']) {
            if ($isArray) {
                foreach ($corrections[$username][$field] as $correc) {
                    if ($correc['val'] == $elem['val']) {
                        die('TODO gestion erreur');
                        return $this->applyFix($customer, $elem, $correc);
                    }
                }
            } else {
                die('TODO gestion erreur');
                return $this->applyFix($customer, $elem, $corrections[$username][$field]);
            }
        } else {
            return $this->setByArray($customer, $elem);
        }
    }

    private function setByArray($customer, $elem){
        $setter = $elem['setter'];
        if ($elem['id'] == null) {
            dump($elem['val']);
            dump($setter);
            $customer->$setter($elem['val']);
        }else{
            $em = $this->getDoctrine()->getManager();
            $customer->$setter($em->getReference($elem['entity'], $elem['id']));
        }
        return true;
    }




    /*************************
     * END IMPORTATION EXCEL *
     *************************/

}
