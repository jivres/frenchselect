<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\PriceRange;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Pricerange controller.
 *
 */
class PriceRangeController extends Controller {
    /**
     * Lists all priceRange entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $priceRanges = $em->getRepository('B2bBundle:PriceRange')->findAll();

        return $this->render('pricerange/index.html.twig', array(
            'priceRanges' => $priceRanges,
        ));
    }

    /**
     * Creates a new priceRange entity.
     * @param Request $request
     * @return
     */
    public function newAction(Request $request) {
        $priceRange = new Pricerange();
        $form = $this->createForm('B2bBundle\Form\PriceRangeType', $priceRange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($priceRange);
            $em->flush();

            return $this->redirectToRoute('backoffice_pricerange_show', array('id' => $priceRange->getId()));
        }

        return $this->render('pricerange/new.html.twig', array(
            'priceRange' => $priceRange,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a priceRange entity.
     * @param PriceRange $priceRange
     * @return
     */
    public function showAction(PriceRange $priceRange) {
        $deleteForm = $this->createDeleteForm($priceRange);

        return $this->render('pricerange/show.html.twig', array(
            'priceRange' => $priceRange,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing priceRange entity.
     * @param Request $request
     * @param PriceRange $priceRange
     * @return
     */
    public function editAction(Request $request, PriceRange $priceRange) {
        $deleteForm = $this->createDeleteForm($priceRange);
        $editForm = $this->createForm('B2bBundle\Form\PriceRangeType', $priceRange);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_pricerange_edit', array('id' => $priceRange->getId()));
        }

        return $this->render('pricerange/edit.html.twig', array(
            'priceRange' => $priceRange,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a priceRange entity.
     * @param Request $request
     * @param PriceRange $priceRange
     * @return
     */
    public function deleteAction(Request $request, PriceRange $priceRange) {
        $form = $this->createDeleteForm($priceRange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($priceRange);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_pricerange_index');
    }

    /**
     * Creates a form to delete a priceRange entity.
     *
     * @param PriceRange $priceRange The priceRange entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PriceRange $priceRange) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_pricerange_delete', array('id' => $priceRange->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
