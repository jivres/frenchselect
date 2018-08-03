<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Brand;
use B2bBundle\Entity\Collection;
use B2bBundle\Entity\Salesman;
use B2bBundle\Entity\BrandUniversCalcul;
use B2bBundle\Entity\SalesmanBrandLink;
use B2bBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use B2bBundle\Form\MyFileType;

/**
 * Brand controller.
 *
 */
class BrandController extends Controller
{

    /**
     * Lists all brand entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $brands = $em->getRepository('B2bBundle:Brand')->findAll();

        return $this->render('brand/index.html.twig', array(
            'brands' => $brands,
        ));
    }

    /**
     * Creates a new brand entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('B2bBundle:User')->findAll();
        $brand = new Brand();
        $form = $this->createForm('B2bBundle\Form\BrandType', $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($users as $u) {
                if ($u->getMail() == $brand->getMail()) {
                    $this->addFlash("danger", "L'adresse email est déjà utilisée par un autre compte ");
                    return $this->render('brand/new.html.twig', array(
                        'brand' => $brand,
                        'form' => $form->createView(),
                    ));
                }
            }
            $brand->setPassword(sha1(User::NO_PASSWORD));
            $brand->setUsername($brand->getMail());
            $brand->setInactive();
            $brand->setDateInscription(date_create(date("Y-m-d")));
            $em = $this->getDoctrine()->getManager();
            $em->persist($brand);
            $em->flush();
            $this->calculUnivers($brand);
            return $this->redirectToRoute('backoffice_brand_index', array('id' => $brand->getId()));
        }

        return $this->render('brand/new.html.twig', array(
            'brand' => $brand,
            'form' => $form->createView(),
        ));
    }


    public function calculUnivers(Brand $brand)
    {
        $em = $this->getDoctrine()->getManager();
        $univers = $em->getRepository('B2bBundle:Univers')->findAll();
        $styleunivers = $em->getRepository('B2bBundle:StyleUnivers')->findAll();

        $tabUniversCalcul = array();
        $tabPoidsCalcul = array();
        $brandStyle = $brand->getStyles()->toArray();
        foreach ($univers as $univ) {
            $cpt = 0;
            $univStyle = $univ->getStyles();
            foreach ($univStyle as $style) {
                if (in_array($style, $brandStyle)) {
                    foreach ($styleunivers as $row) {
                        if ($row->getStyle() == $style && $row->getUnivers() == $univ) {
                            $cpt = $cpt + $row->getPoids();
                        }
                    }
                }
            }
            array_push($tabUniversCalcul, $univ->getId());
            array_push($tabPoidsCalcul, $cpt);
        }
        $cpt = 0;
        $currentUnivers = array();
        for ($i = 0; $i < count($tabUniversCalcul); $i++) {
            if ($cpt < $tabPoidsCalcul[$i]) {
                $cpt = $tabPoidsCalcul[$i];
                $currentUnivers = array($tabUniversCalcul[$i]);
            } else if ($cpt == $tabPoidsCalcul[$i]) {
                array_push($currentUnivers, $tabUniversCalcul[$i]);
            }
        }


        for ($i = 0; $i < count($tabUniversCalcul); $i++) {
            $univers = $em->getRepository('B2bBundle:Univers')->find($tabUniversCalcul[$i]);
            $row = new BrandUniversCalcul();
            $row->setBrand($brand);
            $row->setUnivers($univers);
            $row->setPoids($tabPoidsCalcul[$i]);
            $em->persist($row);
        }
        for ($i = 0; $i < count($currentUnivers); $i++) {
            $univers = $em->getRepository('B2bBundle:Univers')->find($currentUnivers[$i]);
            $brand->addUnivers($univers);
            $em->persist($brand);

        }
        $em->flush();

    }


    /**
     * Finds and displays a brand entity.
     * @param Brand $brand
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function showAction(Brand $brand)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($brand);
        $repo = $em->getRepository('B2bBundle:Command');
        $brandRecommande = $em->getRepository('B2bBundle:BrandRecommande')->findByBrand($brand);
        $brandUniversCalcul = $em->getRepository('B2bBundle:BrandUniversCalcul')->findByBrand($brand);

        return $this->render('brand/show.html.twig', array(
            'brand' => $brand,
            'commands' => $repo->findCommands($brand),
            'delete_form' => $deleteForm->createView(),
            'brandRecommande' => $brandRecommande,
            'brandUniversCalcul' => $brandUniversCalcul,
        ));
    }

    /**
     * Displays a form to edit an existing brand entity.
     *
     */
    public function editAction(Request $request, Brand $brand)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('B2bBundle:User')->findAll();
        $mail = $brand->getMail();
        $deleteForm = $this->createDeleteForm($brand);
        $form = $this->createForm('B2bBundle\Form\BrandType', $brand);
        $form->add('logo', MyFileType::class, array(
            'label' => 'Logo', 'data_class' => 'B2bBundle\Entity\MyFile', 'required' => false
        ))->add('lifestyle2', MyFileType::class, array(
            'label' => 'Image Lifestyle secondaire', 'data_class' => 'B2bBundle\Entity\MyFile', 'required' => false
        ))->add('CGV', MyFileType::class, array(
            'label' => 'Conditions Générales de Vente', 'data_class' => 'B2bBundle\Entity\MyFile', 'required' => false
        ));
        $form->get('lifestyle2')->setData($brand->getLifestyle2());
        $form->get('logo')->setData($brand->getLogo());
        $form->get('CGV')->setData($brand->getCGV());
        $form->get('pictureHomme')->setData($brand->getPictureHomme());
        $form->get('pictureFemme')->setData($brand->getPictureFemme());
        $form->get('pictureEnfant')->setData($brand->getPictureEnfant());
        $form->get('lifestyleHomme')->setData($brand->getLifestyleHomme());
        $form->get('lifestyleFemme')->setData($brand->getLifestyleFemme());
        $form->get('lifestyleEnfant')->setData($brand->getLifestyleEnfant());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($users as $u){
                if(($u->getMail() == $brand->getMail()) && ($brand->getMail() != $mail) ){

                    $this->addFlash("danger", "L'adresse email est déjà utilisée par un autre compte ");
                    return $this->render('brand/edit.html.twig', array(
                        'brand' => $brand,
                        'form' => $form->createView(),
                        'delete_form' => $deleteForm->createView(),
                    ));
                }
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_brand_show', array('id' => $brand->getId()));
        }

        return $this->render('brand/edit.html.twig', array(
            'brand' => $brand,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a brand entity.
     * @param Request $request
     * @param Brand $brand
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Brand $brand)
    {
        $form = $this->createDeleteForm($brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $branduniverscalcul = $em->getRepository('B2bBundle:BrandUniversCalcul')->findByBrand($brand);
                foreach ($branduniverscalcul as $temp) $em->remove($temp);
                $em->remove($brand);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_brand_index');
    }

    public function exportCommandsAction(Brand $brand)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('B2bBundle:Command');
        return $this->render('B2bBundle:Excel:export_command_list.xlsx.twig', array(
            'cmds' => $repo->findCommands($brand)
        ));
    }

    public function exportCommandsBetweenAction(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('B2bBundle:Command');
        $brand = $this->getUser()->getId();
        $from = $request->get('from');
        $to = $request->get('to');
        return $this->render('B2bBundle:Excel:export_command_list.xlsx.twig', array(
            'cmds' => $repo->findCommandsByDate($brand, $from, $to)
        ));
    }


    /**
     * Creates a form to delete a brand entity.
     *
     * @param Brand $brand The brand entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Brand $brand)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_brand_delete', array('id' => $brand->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function addCollectionAction(Brand $brand, Request $request)
    {
        $collection = new Collection();
        $brand->addCollection($collection);
        $collection->setBrand($brand);

        $form = $this->createForm('B2bBundle\Form\CollectionType', $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $collection->setInactive();
            $em = $this->getDoctrine()->getManager();
            $em->persist($collection);
            $em->flush();

            return $this->redirectToRoute('collection_show', array('id' => $collection->getId()));
        }

        return $this->render('collection/new.html.twig', array(
            'collection' => $collection,
            'form' => $form->createView(),
        ));
    }

    /**
     * Select salesmen to assign to the brand
     * @param Brand $brand
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function selectSalesmenAction(Brand $brand, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $departments = $em->getRepository('B2bBundle:Departement')->findAll();
        return $this->render('brand/select-salesmen.html.twig', array(
            'brand' => $brand,
            'departments' => $departments,
        ));
    }


    public function findSalesmenAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $brand = $em->getRepository('B2bBundle:Brand')->findOneBy(array('brandName' => $request->get('search_brand')));
            $salesmen = array();
            foreach ($brand->getSalesmen() as $row) {
                $bool = true;
                $temp = $row->getSalesman();
                foreach($temp->getShops() as $salesmanshop){
                    if($salesmanshop->getShop()->getName() == $request->get('shopName') && $salesmanshop->getBrand() == $brand){
                        $bool = false;
                        break;
                    }
                }
                if($bool)$salesmen[] = $row->getSalesman();
            }
            return $this->render('shop/salesman-list.html.twig', array('salesmen' => $salesmen));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    /**
     * Search a salesman according to its salesman id, name, societyName with the search-bar
     * @param Brand $brand
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function searchSalesmenAction(Brand $brand, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $salesmen = $em->getRepository('B2bBundle:Salesman')->searchForBrand($request->get('search_text'), $brand);
            return $this->render('salesman/salesmenlist.html.twig', array('salesmen' => $salesmen, 'checkbox' => true));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    /**
     * Assign salesmen to a specific brand
     * @param Brand $brand
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function assignSalesmenAction(Brand $brand, Request $request)
    {
        $ids = json_decode($request->query->get('ids'), $assoc = true);
        $deps = json_decode($request->query->get('departments'), $assoc = true);
        $em = $this->getDoctrine()->getManager();

        $departments = [];
        foreach ($deps as $dep) {
            $departments[] = $em->getRepository('B2bBundle:Departement')->find($dep);
        }

        foreach ($ids as $id) {
            $salesman = $em->getRepository('B2bBundle:Salesman')->find($id);

            $salesmanBrandLink = new SalesmanBrandLink();
            $salesmanBrandLink->setBrand($brand);
            $salesmanBrandLink->setSalesman($salesman);
            foreach ($departments as $department) {
                $salesmanBrandLink->addDepartment($department);
            }

            $brand->addSalesman($salesmanBrandLink);
            $salesman->addBrand($salesmanBrandLink);

            $em->persist($salesmanBrandLink);
            $em->flush();
        }

        return $this->redirectToRoute('backoffice_select_salesmen', array('id' => $brand->getId()));
    }

    /**
     * Deassign salesman to a specific brand
     * @param SalesmanBrandLink $salesmanBrandLink
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deassignSalesmenAction(SalesmanBrandLink $salesmanBrandLink)
    {
        $em = $this->getDoctrine()->getManager();
        $brand = $salesmanBrandLink->getBrand();
        $salesman = $salesmanBrandLink->getSalesman();

        $brand->removeSalesman($salesmanBrandLink);
        $salesman->removeBrand($salesmanBrandLink);
        $em->remove($salesmanBrandLink);
        $em->flush();

        return $this->redirectToRoute('backoffice_brand_show', array('id' => $brand->getId()));
    }


    public function assignCustomerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $brands = $em->getRepository('B2bBundle:Brand')->findBy(array('isActive' => true));
        $customers = $em->getRepository('B2bBundle:Customer')->findBy(array('isActive' => true));
        $form = $this->createForm('B2bBundle\Form\AssignCustomerType', null, array('brands' => $brands, 'customers' => $customers));
        $form->handleRequest($request);


        if (($form->isSubmitted()) && ($form->isValid())) {
            $brand = $form->get('brand')->getData();
            $customer = $form->get('customer')->getData();

            $brand->addCustomer($customer);
            $em->persist($brand);
            $em->flush();

            return $this->redirectToRoute('b2b_backoffice_index');
        }


        return $this->render('brand/assign.html.twig', array(
            "form" => $form->createView()
        ));
    }


    public function deassignCustomerAction($b, $c, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('B2bBundle:Brand')->find($b);
        $customer = $em->getRepository('B2bBundle:Customer')->find($c);

        $brand->removeCustomer($customer);
        $em->persist($brand);
        $em->flush();

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
