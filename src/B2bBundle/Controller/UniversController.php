<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\StyleUnivers;
use B2bBundle\Entity\Univers;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Univers controller.
 *
 */
class UniversController extends Controller {


    /**
     * Creates a new univers entity.
     * @param \B2bBundle\Controller\Request $request
     * @return
     */
    public function newAction(Request $request) {
        $univers = new Univers();
        $form = $this->createForm('B2bBundle\Form\UniversType', $univers);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($univers);
            $em->flush();

            return $this->redirectToRoute('backoffice_univers_show', array('id' => $univers->getId()));
        }

        return $this->render('univers/new.html.twig', array(
            'univers' => $univers,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a univers entity.
     * @param Univers $univers
     * @return
     */
    public function showAction(Univers $univers) {
        $deleteForm = $this->createDeleteForm($univers);

        return $this->render('univers/show.html.twig', array(
            'univers' => $univers,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing univers entity.
     * @param Request $request
     * @param Univers $univers
     * @return
     */
    public function editAction(Request $request, Univers $univers) {
        $deleteForm = $this->createDeleteForm($univers);
        $editForm = $this->createForm('B2bBundle\Form\UniversType', $univers);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_univers_edit', array('id' => $univers->getId()));
        }

        return $this->render('univers/edit.html.twig', array(
            'univers' => $univers,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a univers entity.
     * @param Request $request
     * @param Univers $univers
     * @return
     */
    public function deleteAction(Request $request, Univers $univers) {
        $form = $this->createDeleteForm($univers);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($univers);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_style_index');
    }



    /**
     * Creates a form to delete a univers entity.
     *
     * @param Univers $univers The univers entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Univers $univers) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_univers_delete', array('id' => $univers->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
