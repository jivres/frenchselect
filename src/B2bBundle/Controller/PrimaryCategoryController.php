<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\PrimaryCategory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Primarycategory controller.
 *
 */
class PrimaryCategoryController extends Controller {
    /**
     * Lists all primaryCategory entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $primaryCategories = $em->getRepository('B2bBundle:PrimaryCategory')->findAll();

        return $this->render('primarycategory/index.html.twig', array(
            'primaryCategories' => $primaryCategories,
        ));
    }

    /**
     * Creates a new primaryCategory entity.
     * @param \B2bBundle\Controller\Request $request
     * @return
     */
    public function newAction(Request $request) {
        $primaryCategory = new Primarycategory();
        $form = $this->createForm('B2bBundle\Form\PrimaryCategoryType', $primaryCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($primaryCategory);
            $em->flush();

            return $this->redirectToRoute('backoffice_primarycategory_show', array('id' => $primaryCategory->getId()));
        }

        return $this->render('primarycategory/new.html.twig', array(
            'primaryCategory' => $primaryCategory,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a primaryCategory entity.
     * @param PrimaryCategory $primaryCategory
     * @return
     */
    public function showAction(PrimaryCategory $primaryCategory) {
        $deleteForm = $this->createDeleteForm($primaryCategory);

        return $this->render('primarycategory/show.html.twig', array(
            'primaryCategory' => $primaryCategory,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing primaryCategory entity.
     * @param Request $request
     * @param PrimaryCategory $primaryCategory
     * @return
     */
    public function editAction(Request $request, PrimaryCategory $primaryCategory) {
        $deleteForm = $this->createDeleteForm($primaryCategory);
        $editForm = $this->createForm('B2bBundle\Form\PrimaryCategoryType', $primaryCategory);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_primarycategory_show', array('id' => $primaryCategory->getId()));
        }

        return $this->render('primarycategory/edit.html.twig', array(
            'primaryCategory' => $primaryCategory,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a primaryCategory entity.
     * @param Request $request
     * @param PrimaryCategory $primaryCategory
     * @return
     */
    public function deleteAction(Request $request, PrimaryCategory $primaryCategory) {
        $form = $this->createDeleteForm($primaryCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($primaryCategory);
                $em->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_primarycategory_index');
    }

    /**
     * Creates a form to delete a primaryCategory entity.
     *
     * @param PrimaryCategory $primaryCategory The primaryCategory entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PrimaryCategory $primaryCategory) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_primarycategory_delete', array('id' => $primaryCategory->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
