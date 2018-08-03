<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\PaymentMethod;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Paymentmethod controller.
 *
 */
class PaymentMethodController extends Controller {
    /**
     * Lists all paymentMethod entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $paymentMethods = $em->getRepository('B2bBundle:PaymentMethod')->findAll();

        return $this->render('paymentmethod/index.html.twig', array(
            'paymentMethods' => $paymentMethods,
        ));
    }

    /**
     * Creates a new paymentMethod entity.
     * @param \B2bBundle\Controller\Request $request
     * @return
     */
    public function newAction(Request $request) {
        $paymentMethod = new Paymentmethod();
        $form = $this->createForm('B2bBundle\Form\PaymentMethodType', $paymentMethod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($paymentMethod);
            $em->flush();

            return $this->redirectToRoute('backoffice_paymentmethod_show', array('id' => $paymentMethod->getId()));
        }

        return $this->render('paymentmethod/new.html.twig', array(
            'paymentMethod' => $paymentMethod,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a paymentMethod entity.
     * @param PaymentMethod $paymentMethod
     * @return
     */
    public function showAction(PaymentMethod $paymentMethod) {
        $deleteForm = $this->createDeleteForm($paymentMethod);

        return $this->render('paymentmethod/show.html.twig', array(
            'paymentMethod' => $paymentMethod,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing paymentMethod entity.
     * @param Request $request
     * @param PaymentMethod $paymentMethod
     * @return
     */
    public function editAction(Request $request, PaymentMethod $paymentMethod) {
        $deleteForm = $this->createDeleteForm($paymentMethod);
        $editForm = $this->createForm('B2bBundle\Form\PaymentMethodType', $paymentMethod);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_paymentmethod_show', array('id' => $paymentMethod->getId()));
        }

        return $this->render('paymentmethod/edit.html.twig', array(
            'paymentMethod' => $paymentMethod,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a paymentMethod entity.
     * @param Request $request
     * @param PaymentMethod $paymentMethod
     * @return
     */
    public function deleteAction(Request $request, PaymentMethod $paymentMethod) {
        $form = $this->createDeleteForm($paymentMethod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($paymentMethod);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_paymentmethod_index');
    }

    /**
     * Creates a form to delete a paymentMethod entity.
     *
     * @param PaymentMethod $paymentMethod The paymentMethod entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PaymentMethod $paymentMethod) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_paymentmethod_delete', array('id' => $paymentMethod->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
