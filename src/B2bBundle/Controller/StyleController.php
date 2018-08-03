<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Style;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Style controller.
 *
 */
class StyleController extends Controller {
    /**
     * Lists all style entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $styles = $em->getRepository('B2bBundle:Style')->findAll();
        $univers = $em->getRepository('B2bBundle:Univers')->findAll();

        return $this->render('style/index.html.twig', array(
            'styles' => $styles,
            'univers' => $univers,
        ));
    }

    /**
     * Creates a new style entity.
     * @param \B2bBundle\Controller\Request $request
     * @return
     */
    public function newAction(Request $request) {
        $style = new Style();
        $form = $this->createForm('B2bBundle\Form\StyleType', $style);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($style);
            $em->flush();

            return $this->redirectToRoute('backoffice_style_show', array('id' => $style->getId()));
        }

        return $this->render('style/new.html.twig', array(
            'style' => $style,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a style entity.
     * @param Style $style
     * @return
     */
    public function showAction(Style $style) {
        $deleteForm = $this->createDeleteForm($style);

        return $this->render('style/show.html.twig', array(
            'style' => $style,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing style entity.
     * @param Request $request
     * @param Style $style
     * @return
     */
    public function editAction(Request $request, Style $style) {
        $deleteForm = $this->createDeleteForm($style);
        $editForm = $this->createForm('B2bBundle\Form\StyleType', $style);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_style_edit', array('id' => $style->getId()));
        }

        return $this->render('style/edit.html.twig', array(
            'style' => $style,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a style entity.
     * @param Request $request
     * @param Style $style
     * @return
     */
    public function deleteAction(Request $request, Style $style) {
        $form = $this->createDeleteForm($style);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($style);
            $em->flush();
            try {
                $em->remove($style);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_style_index');
    }

    /**
     * Creates a form to delete a style entity.
     *
     * @param Style $style The style entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Style $style) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_style_delete', array('id' => $style->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
