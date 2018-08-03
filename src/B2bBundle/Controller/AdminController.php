<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Admin;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Admin controller.
 *
 */
class AdminController extends Controller {

    /**
     * Lists all admin entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $admins = $em->getRepository('B2bBundle:Admin')->findAll();

        return $this->render('admin/index.html.twig', array(
            'admins' => $admins,
        ));
    }

    /**
     * Creates a new admin entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('B2bBundle:User')->findAll();
        $admin = new Admin();
        $admin->setPassword("");
        $form = $this->createForm('B2bBundle\Form\AdminType', $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($users as $u){
                if($u->getMail() == $admin->getMail()){
                    $this->addFlash("danger", "L'adresse email est déjà utilisée par un autre compte ");
                    return $this->render('admin/new.html.twig', array(
                        'admin' => $admin,
                        'form' => $form->createView(),
                    ));
                }
            }
            $admin->setUsername($admin->getMail());
            $em = $this->getDoctrine()->getManager();
            $em->persist($admin);
            $em->flush();

            return $this->redirectToRoute('backoffice_admin_show', array('id' => $admin->getId()));
        }

        return $this->render('admin/new.html.twig', array(
            'admin' => $admin,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a admin entity.
     * @param Admin $admin
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Admin $admin) {
        $deleteForm = $this->createDeleteForm($admin);

        return $this->render('admin/show.html.twig', array(
            'admin' => $admin,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing admin entity.
     * @param Request $request
     * @param Admin $admin
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Admin $admin) {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('B2bBundle:User')->findAll();
        $mail = $admin->getMail();
        $deleteForm = $this->createDeleteForm($admin);
        $editForm = $this->createForm('B2bBundle\Form\AdminType', $admin);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($users as $u){
                if( ($u->getMail() == $admin->getMail()) && ($admin->getMail() != $mail) ){
                    $this->addFlash("danger", "L'adresse email est déjà utilisée par un autre compte ");
                    return $this->render('admin/edit.html.twig', array(
                        'admin' => $admin,
                        'edit_form' => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                    ));
                }
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_admin_edit', array('id' => $admin->getId()));
        }

        return $this->render('admin/edit.html.twig', array(
            'admin' => $admin,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a admin entity.
     * @param Request $request
     * @param Admin $admin
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Admin $admin) {
        $form = $this->createDeleteForm($admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($admin);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_admin_index');
    }

    public function exportAction() {
        $em = $this->getDoctrine()->getManager();

        $admins = $em->getRepository('B2bBundle:Admin')->findAll();

        return $this->render('B2bBundle:Excel:liste_admins.xlsx.twig', array(
            'admins' => $admins,
        ));
    }


    /**
     * Creates a form to delete a admin entity.
     *
     * @param Admin $admin The admin entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Admin $admin) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_admin_delete', array('id' => $admin->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
