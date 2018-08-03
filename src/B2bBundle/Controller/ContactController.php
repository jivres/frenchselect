<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\ContactBrand;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * ContactBrand controller.
 *
 */
class ContactController extends Controller {
    /**
     * Lists all contact entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $contacts = $em->getRepository('B2bBundle:ContactBrand')->findAll();

        return $this->render('contact/index.html.twig', array(
            'contacts' => $contacts,
        ));
    }

    /**
     * Creates a new contact entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request) {
        $contact = new ContactBrand();
        $form = $this->createForm('B2bBundle\Form\ContactBrandType', $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            return $this->redirectToRoute('backoffice_contact_show', array('id' => $contact->getId()));
        }

        return $this->render('contact/new.html.twig', array(
            'contact' => $contact,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a contact entity.
     * @param ContactBrand $contact
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(ContactBrand $contact) {
        $deleteForm = $this->createDeleteForm($contact);

        return $this->render('contact/show.html.twig', array(
            'contact' => $contact,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing contact entity.
     *
     */
    public function editAction(Request $request, ContactBrand $contact)
    {
        $deleteForm = $this->createDeleteForm($contact);
        $editForm = $this->createForm('B2bBundle\Form\ContactBrandType', $contact);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_contact_edit', array('id' => $contact->getId()));
        }

        return $this->render('contact/edit.html.twig', array(
            'contact' => $contact,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a contact entity.
     * @param Request $request
     * @param ContactBrand $contact
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, ContactBrand $contact) {
        $form = $this->createDeleteForm($contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($contact);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_contact_index');
    }

    /**
     * Creates a form to delete a contact entity.
     *
     * @param ContactBrand $contact The contact entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(ContactBrand $contact) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_contact_delete', array('id' => $contact->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
