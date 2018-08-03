<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Size;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Size controller.
 *
 */
class SizeController extends Controller {
    /**
     * Lists all size entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $sizes = $em->getRepository('B2bBundle:Size')->findAll();

        return $this->render('size/index.html.twig', array(
            'sizes' => $sizes,
        ));
    }

    /**
     * Creates a new size entity.
     * @param Request $request
     * @return
     */
    public function newAction(Request $request) {
        $size = new Size();
        $form = $this->createForm('B2bBundle\Form\SizeType', $size);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($size);
            $em->flush();

            return $this->redirectToRoute('backoffice_size_show', array('id' => $size->getId()));
        }

        return $this->render('size/new.html.twig', array(
            'size' => $size,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a size entity.
     * @param Size $size
     * @return
     */
    public function showAction(Size $size) {
        $deleteForm = $this->createDeleteForm($size);

        return $this->render('size/show.html.twig', array(
            'size' => $size,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing size entity.
     * @param Request $request
     * @param Size $size
     * @return
     */
    public function editAction(Request $request, Size $size) {
        $deleteForm = $this->createDeleteForm($size);
        $editForm = $this->createForm('B2bBundle\Form\SizeType', $size);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_size_edit', array('id' => $size->getId()));
        }

        return $this->render('size/edit.html.twig', array(
            'size' => $size,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a size entity.
     * @param Request $request
     * @param Size $size
     * @return
     */
    public function deleteAction(Request $request, Size $size) {
        $form = $this->createDeleteForm($size);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($size);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_size_index');
    }

    /**
     * Creates a form to delete a size entity.
     *
     * @param Size $size The size entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Size $size) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_size_delete', array('id' => $size->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
