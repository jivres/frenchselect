<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\PaymentTerms;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Paymentterm controller.
 *
 */
class PaymentTermsController extends Controller
{
    /**
     * Lists all paymentTerm entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $paymentTerms = $em->getRepository('B2bBundle:PaymentTerms')->findAll();

        return $this->render('paymentterms/index.html.twig', array(
            'paymentTerms' => $paymentTerms,
        ));
    }

    /**
     * Creates a new paymentTerm entity.
     *
     */
    public function newAction(Request $request)
    {
        $paymentTerm = new Paymentterms();
        $form = $this->createForm('B2bBundle\Form\PaymentTermsType', $paymentTerm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($paymentTerm);
            $em->flush();

            return $this->redirectToRoute('backoffice_paymentterms_show', array('id' => $paymentTerm->getId()));
        }

        return $this->render('paymentterms/new.html.twig', array(
            'paymentTerm' => $paymentTerm,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a paymentTerm entity.
     *
     */
    public function showAction(PaymentTerms $paymentTerm)
    {
        $deleteForm = $this->createDeleteForm($paymentTerm);

        return $this->render('paymentterms/show.html.twig', array(
            'paymentTerm' => $paymentTerm,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing paymentTerm entity.
     *
     */
    public function editAction(Request $request, PaymentTerms $paymentTerm)
    {
        $deleteForm = $this->createDeleteForm($paymentTerm);
        $editForm = $this->createForm('B2bBundle\Form\PaymentTermsType', $paymentTerm);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_paymentterms_edit', array('id' => $paymentTerm->getId()));
        }

        return $this->render('paymentterms/edit.html.twig', array(
            'paymentTerm' => $paymentTerm,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a paymentTerm entity.
     *
     */
    public function deleteAction(Request $request, PaymentTerms $paymentTerm)
    {
        $form = $this->createDeleteForm($paymentTerm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($paymentTerm);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_paymentterms_index');
    }

    /**
     * Creates a form to delete a paymentTerm entity.
     *
     * @param PaymentTerms $paymentTerm The paymentTerm entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PaymentTerms $paymentTerm)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_paymentterms_delete', array('id' => $paymentTerm->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
