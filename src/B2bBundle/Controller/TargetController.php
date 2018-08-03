<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Target;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Target controller.
 *
 */
class TargetController extends Controller {
    /**
     * Lists all target entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $targets = $em->getRepository('B2bBundle:Target')->findAll();

        return $this->render('target/index.html.twig', array(
            'targets' => $targets,
        ));
    }

    /**
     * Creates a new target entity.
     * @param Request $request
     * @return
     */
    public function newAction(Request $request) {
        $target = new Target();
        $form = $this->createForm('B2bBundle\Form\TargetType', $target);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($target);
            $em->flush();

            return $this->redirectToRoute('backoffice_target_show', array('id' => $target->getId()));
        }

        return $this->render('target/new.html.twig', array(
            'target' => $target,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a target entity.
     * @param Target $target
     * @return
     */
    public function showAction(Target $target) {
        $deleteForm = $this->createDeleteForm($target);

        return $this->render('target/show.html.twig', array(
            'target' => $target,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing target entity.
     * @param Request $request
     * @param Target $target
     * @return
     */
    public function editAction(Request $request, Target $target) {
        $deleteForm = $this->createDeleteForm($target);
        $editForm = $this->createForm('B2bBundle\Form\TargetType', $target);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_target_edit', array('id' => $target->getId()));
        }

        return $this->render('target/edit.html.twig', array(
            'target' => $target,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a target entity.
     * @param Request $request
     * @param Target $target
     * @return
     */
    public function deleteAction(Request $request, Target $target) {
        $form = $this->createDeleteForm($target);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($target);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_target_index');
    }

    /**
     * Creates a form to delete a target entity.
     *
     * @param Target $target The target entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Target $target) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_target_delete', array('id' => $target->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
