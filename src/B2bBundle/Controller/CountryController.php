<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Country;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Country controller.
 *
 */
class CountryController extends Controller {
    /**
     * Lists all country entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $countries = $em->getRepository('B2bBundle:Country')->findAll();

        return $this->render('country/index.html.twig', array(
            'countries' => $countries,
        ));
    }

    /**
     * Creates a new country entity.
     * @param Request $request
     * @return
     */
    public function newAction(Request $request) {
        $country = new Country();
        $form = $this->createForm('B2bBundle\Form\CountryType', $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($country);
            $em->flush();

            return $this->redirectToRoute('backoffice_country_show', array('id' => $country->getId()));
        }

        return $this->render('country/new.html.twig', array(
            'country' => $country,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a country entity.
     * @param Country $country
     * @return
     */
    public function showAction(Country $country) {
        $deleteForm = $this->createDeleteForm($country);

        return $this->render('country/show.html.twig', array(
            'country' => $country,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing country entity.
     * @param Request $request
     * @param Country $country
     * @return
     */
    public function editAction(Request $request, Country $country) {
        $deleteForm = $this->createDeleteForm($country);
        $editForm = $this->createForm('B2bBundle\Form\CountryType', $country);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_country_edit', array('id' => $country->getId()));
        }

        return $this->render('country/edit.html.twig', array(
            'country' => $country,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a country entity.
     * @param Request $request
     * @param Country $country
     * @return
     */
    public function deleteAction(Request $request, Country $country) {
        $form = $this->createDeleteForm($country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($country);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_country_index');
    }

    /**
     * Creates a form to delete a country entity.
     *
     * @param Country $country The country entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Country $country) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_country_delete', array('id' => $country->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
