<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Brand;
use B2bBundle\Entity\Salesman;
use B2bBundle\Entity\SalesmanBrandLink;
use B2bBundle\Entity\SubSalesmanLink;
use B2bBundle\Entity\User;
use B2bBundle\Form\SalesmanDepartmentType;
use B2bBundle\Form\SalesmanType;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Salesman controller.
 *
 */
class SalesmanController extends Controller {
    /**
     * Lists all salesman entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $salesmen = $em->getRepository('B2bBundle:Salesman')->findAll();

        return $this->render('salesman/index.html.twig', array(
            'salesmen' => $salesmen,
        ));
    }

    /**
     * List all salesman entities for a brand
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexForBrandAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $brand = $em->getRepository('B2bBundle:Brand')->find($user->getId());
        $salesmen = $brand->getSalesmen();

        return $this->render('salesman/brand-index.html.twig', array(
            'salesmen' => $salesmen,
            'brand' => $brand,
        ));
    }

    /**
     * List all subordinates of a salesman
     */
    public function indexSubordinatesAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $salesman = $em->getRepository('B2bBundle:Salesman')->find($user->getId());
        $subordinates = $salesman->getSubordinates();

        return $this->render('salesman/salesman-index.html.twig', array(
            'salesmen' => $subordinates,
            'noclick' => true,
        ));
    }

    /**
     * Change the departments assignment
     * @param SubSalesmanLink $subSalesmanLink
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function departmentEditAction(SubSalesmanLink $subSalesmanLink, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $salesman = $em->getRepository('B2bBundle:Salesman')->find($request->query->get('salesman'));
        $brand = $subSalesmanLink->getBrand();
        $salesmanBrandLink = $em->getRepository('B2bBundle:SalesmanBrandLink')->findBy(array('salesman' => $salesman, 'brand' => $brand))[0];

        $form = $this->createForm('B2bBundle\Form\SubSalesmanLinkType', $subSalesmanLink,
            array('departmentIds' => $salesmanBrandLink->getDepartments()));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('salesman_show', array('id' => $salesman->getId()));
            } else {
                return $this->redirectToRoute('salesman_subordinates');
            }
        }

        return $this->render('salesman/department-edit.html.twig', array(
            'form' => $form->createView(),
            'salesman' => $salesman,
        ));
        /*$em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $salesman = $em->getRepository('B2bBundle:Salesman')->find($user->getId());

        $subordinates = $salesman->getSubordinates();

        $form = $this->createFormBuilder(array('salesmen' => $subordinates))
            ->add('salesmen', CollectionType::class, array(
                'entry_type' => SalesmanDepartmentType::class,
                'entry_options'  => array(
                    'departmentIds' => $salesman->getDepartments())))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($subordinates as $subordinate) {
                $em->persist($subordinate);
            }
            //$em->getManager()->persist($cart);
            //$em->getManager()->persist($form->getViewData());
            $em->flush();
            return $this->redirectToRoute('salesman_subordinates');
        }

        return $this->render('salesman/department-edit.html.twig', array(
            'form' => $form->createView(),
        ));*/
    }

    /**
     * Creates a new salesman entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('B2bBundle:User')->findAll();
        $salesman = new Salesman();
        $form = $this->createForm('B2bBundle\Form\SalesmanType', $salesman);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($users as $u){
                if($u->getMail() == $salesman->getMail()){
                    $this->addFlash("danger", "L'adresse email est déjà utilisée par un autre compte ");
                    return $this->render('salesman/admin-new.html.twig', array(
                        'saleman' => $salesman,
                        'form' => $form->createView(),
                    ));
                }
            }
            $salesman->setUsername($salesman->getMail());
            $em = $this->getDoctrine()->getManager();
            $salesman->setPassword(User::NO_PASSWORD);
            $salesman->setInactive();


            /*$data = $form->getData();
            $username = $data['username'];
            $salesman->setUsername($username);
            $salesman->setMail($username);*/


            $em->persist($salesman);
            $em->flush();

            if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') &&
                $this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
                $brand = $em->getRepository('B2bBundle:Brand')->find($this->getUser());
                $salesmanbrand = new SalesmanBrandLink();
                $salesmanbrand->setBrand($brand);
                $salesmanbrand->setSalesman($salesman);
                $em->persist($salesmanbrand);
                $em->flush();
            }

            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $route = 'backoffice_salesman_show';
            } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
                $route = 'salesman_show';
            }

            return $this->redirectToRoute($route, array('id' => $salesman->getId()));
        }

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $view = 'salesman/admin-new.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $view = 'salesman/brand-new.html.twig';
        }

        return $this->render($view, array(
            'salesman' => $salesman,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a salesman entity.
     * @param Salesman $salesman
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Salesman $salesman) {
        $deleteForm = $this->createDeleteForm($salesman);

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $view = 'salesman/admin-show.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $view = 'salesman/brand-show.html.twig';
        }

        return $this->render($view, array(
            'salesman' => $salesman,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing salesman entity.
     * @param Request $request
     * @param Salesman $salesman
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Salesman $salesman) {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('B2bBundle:User')->findAll();
        $mail = $salesman->getMail();
        $deleteForm = $this->createDeleteForm($salesman);
        $editForm = $this->createForm('B2bBundle\Form\SalesmanType', $salesman);
        $editForm->handleRequest($request);

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $view = 'salesman/admin-edit.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $view = 'salesman/brand-edit.html.twig';
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($users as $u){
                if( ($u->getMail() == $salesman->getMail()) && ($salesman->getMail() != $mail) ){
                    $this->addFlash("danger", "L'adresse email est déjà utilisée par un autre compte ");
                    return $this->render($view, array(
                        'salesman' => $salesman,
                        'edit_form' => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                    ));
                }
            }

            $this->getDoctrine()->getManager()->flush();

            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $route = 'backoffice_salesman_show';
            } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
                $route = 'salesman_edit';
            }

            return $this->redirectToRoute($route, array('id' => $salesman->getId()));
        }

        return $this->render($view, array(
            'salesman' => $salesman,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a salesman entity.
     * @param Request $request
     * @param Salesman $salesman
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Salesman $salesman) {
        $form = $this->createDeleteForm($salesman);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($salesman);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_salesman_index');
    }

    /**
     * Retirer le commercial de la liste des commerciaux de la marque
     * @param Request $request
     * @param Salesman $salesman
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request, Salesman $salesman) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $brand = $em->getRepository('B2bBundle:Brand')->find($user->getId());

        $brand->removeSalesman($salesman);
        $em->persist($brand);
        $em->persist($salesman);
        $em->flush();

        return $this->redirectToRoute('salesman_index');
    }

    /**
     * Creates a form to delete a salesman entity.
     *
     * @param Salesman $salesman The salesman entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Salesman $salesman) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_salesman_delete', array('id' => $salesman->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Select the customer to connect for
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function selectCustomerAction() {
        return $this->render('shop/salesman-connect.html.twig');
    }

    /**
     * Search a shop according to its client id, name, societyName with the search-bar
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function searchShopAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $salesman = $em->getRepository('B2bBundle:Salesman')->find($this->getUser()->getId());
            $shops = $em->getRepository('B2bBundle:Shop')->searchForSalesman($request->get('search_text'), $salesman);
            return $this->render('shop/shoplist.html.twig', array('shops' => $shops, 'checkbox' => true));
            //return new JsonResponse(array('data' => json_encode($products)));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    /**
     * Select the salesman to put in the hierarchy
     * @param Salesman $salesman
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function selectSalesmanAction(Salesman $salesman, Request $request) {
        return $this->render('salesman/select-salesman.html.twig', array(
            'salesman' => $salesman,
            'hierarchy' => $request->get('hierarchy')
        ));
    }

    /**
     * Search a salesman according to its salesman id, name, societyName with the search-bar
     * @param Salesman $salesman
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function searchSalesmanAction(Salesman $salesman, Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $salesmen = $em->getRepository('B2bBundle:Salesman')->searchForSalesman($request->get('search_text'),
                                                                                    $request->get('brand_id'),
                                                                                    $salesman);
            return $this->render('salesman/salesmenlist.html.twig', array('salesmen' => $salesmen, 'checkbox' => true));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    /**
     * Assign salesmen into hierarchy (superior or subordinate)
     * @param Salesman $salesman
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function assignSalesmanAction(Salesman $salesman, Request $request) {
        $ids = json_decode($request->query->get('ids'), $assoc = true);
        $em = $this->getDoctrine()->getManager();

        $hierarchy = $request->query->get('hierarchy');
        $brand = $em->getRepository('B2bBundle:Brand')->find($request->query->get('brand'));
        if ($hierarchy == 'superior') {
            $subordinate = $salesman;
        } else {
            $superior = $salesman;
        }

        foreach ($ids as $id) {
            if ($hierarchy == 'superior') {
                $superior = $em->getRepository('B2bBundle:Salesman')->find($id);
            } else {
                $subordinate = $em->getRepository('B2bBundle:Salesman')->find($id);
            }
            $subSalesmanLink = new SubSalesmanLink();
            $subSalesmanLink->setBrand($brand);
            $subSalesmanLink->setSubordinate($subordinate);
            $subSalesmanLink->setSuperior($superior);

            $superior->addSubordinate($subSalesmanLink);
            $subordinate->addSuperior($subSalesmanLink);
            $em->persist($subSalesmanLink);
            $em->flush();
        }
        $em->flush();

        return $this->redirectToRoute('backoffice_salesman_select_salesman', array('id' => $salesman->getId()));
    }

    /**
     * Dessign salesmen
     * @param Salesman $salesman
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deassignSalesmanAction(SubSalesmanLink $salesmanLink, Request $request) {
        $em = $this->getDoctrine()->getManager();
        /*$hierarchy = $request->query->get('hierarchy');
        if ($hierarchy == 'superior') {
            $superior = $em->getRepository('B2bBundle:Salesman')->find($request->query->get('salesman'));
            $salesman->removeSuperior($superior);
            $superior->removeSubordinate($salesman);
        } else {
            $subordinate = $em->getRepository('B2bBundle:Salesman')->find($request->query->get('salesman'));
            $salesman->removeSubordinate($subordinate);
            $subordinate->removeSuperior($salesman);
        }*/
        $em->remove($salesmanLink);
        $em->flush();

        return $this->redirectToRoute('backoffice_salesman_show', array('id' => $request->query->get('salesman')));
    }

    public function addBrandAction(Salesman $salesman, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $salesmanBrandLink = new SalesmanBrandLink();
        $salesmanBrandLink->setSalesman($salesman);
        $brands = $em->getRepository('B2bBundle:Brand')->getAvailableForSalesman($salesman);

        $form = $this->createForm('B2bBundle\Form\SalesmanBrandLinkType', $salesmanBrandLink,
            array('brandIds' => $brands));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($salesmanBrandLink);
            $em->flush();
            return $this->redirectToRoute('salesman_show', array('id' => $salesman->getId()));
        }

        return $this->render('salesmanbrandlink/new.html.twig', array(
            'form' => $form->createView(),
            'salesman' => $salesman,
        ));
    }

    private function createBrandLinkDeleteForm(SalesmanBrandLink $salesmanBrandLink) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_salesman_delete_brand', array('id' => $salesmanBrandLink->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function editBrandAction(SalesmanBrandLink $salesmanBrandLink, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $salesman = $em->getRepository('B2bBundle:Salesman')->find($request->query->get('salesman'));

        $deleteForm = $this->createBrandLinkDeleteForm($salesmanBrandLink);
        $editForm = $this->createForm('B2bBundle\Form\SalesmanBrandLinkType', $salesmanBrandLink, array('editBrand' => false));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();

            return $this->redirectToRoute('backoffice_salesman_show', array('id' => $salesman->getId()));
        }

        return $this->render('salesmanbrandlink/edit.html.twig', array(
            'salesman' => $salesman,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function deleteBrandAction(SalesmanBrandLink $salesmanBrandLink, Request $request) {
        $salesman = $salesmanBrandLink->getSalesman();
        $form = $this->createBrandLinkDeleteForm($salesmanBrandLink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($salesmanBrandLink);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_salesman_show', array('id' => $salesman->getId()));
    }

    public function activateAction(Salesman $salesman, Request $request) {
        $salesman->setActiveStatus();
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('backoffice_user_activate', array('id' => $salesman->getId(), 'request' => $request));
    }


    public function showBrandLinkAction(Salesman $salesman,Brand $brand, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $salesmanBrandLink = $em->getRepository('B2bBundle:SalesmanBrandLink')->findOneBy(array('brand' => $brand, 'salesman'=>$salesman));
        $salesmanShop = $em->getRepository('B2bBundle:SalesmanShop')->findBy(array('brand' => $brand, 'salesman'=>$salesman));



        return $this->render('salesmanbrandlink/detail.html.twig', array(
            'salesmanbrandlink' => $salesmanBrandLink,
            'salesmanshop' => $salesmanShop,
            'salesman' => $salesman,
            'brand' => $brand,
        ));
    }
}
