<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\SecondaryCategory;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Secondarycategory controller.
 *
 */
class SecondaryCategoryController extends Controller {

    /**
     * Lists all secondaryCategory entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $secondaryCategories = $em->getRepository('B2bBundle:SecondaryCategory')->findAll();

        return $this->render('secondarycategory/index.html.twig', array(
            'secondaryCategories' => $secondaryCategories,
        ));
    }

    /**
     * Creates a new secondaryCategory entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request) {
        $secondaryCategory = new Secondarycategory();
        $form = $this->createForm('B2bBundle\Form\SecondaryCategoryType', $secondaryCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($secondaryCategory);
            $em->flush();

            return $this->redirectToRoute('backoffice_secondarycategory_show', array('id' => $secondaryCategory->getId()));
        }

        return $this->render('secondarycategory/new.html.twig', array(
            'secondaryCategory' => $secondaryCategory,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a secondaryCategory entity.
     *
     */
    public function showAction(SecondaryCategory $secondaryCategory) {
        $deleteForm = $this->createDeleteForm($secondaryCategory);

        return $this->render('secondarycategory/show.html.twig', array(
            'secondaryCategory' => $secondaryCategory,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing secondaryCategory entity.
     * @param Request $request
     * @param SecondaryCategory $secondaryCategory
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, SecondaryCategory $secondaryCategory) {
        $deleteForm = $this->createDeleteForm($secondaryCategory);
        $editForm = $this->createForm('B2bBundle\Form\SecondaryCategoryType', $secondaryCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_secondarycategory_edit', array('id' => $secondaryCategory->getId()));
        }

        return $this->render('secondarycategory/edit.html.twig', array(
            'secondaryCategory' => $secondaryCategory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a secondaryCategory entity.
     * @param Request $request
     * @param SecondaryCategory $secondaryCategory
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SecondaryCategory $secondaryCategory) {
        $form = $this->createDeleteForm($secondaryCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($secondaryCategory);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_secondarycategory_index');
    }

    /**
     * Creates a form to delete a secondaryCategory entity.
     *
     * @param SecondaryCategory $secondaryCategory The secondaryCategory entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(SecondaryCategory $secondaryCategory) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_secondarycategory_delete', array('id' => $secondaryCategory->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
