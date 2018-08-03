<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 */
class UserController extends Controller {

    /**
     * Lists all user entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('B2bBundle:User')->findAll();

        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Creates a new user entity.
     *
     */
    public function newAction(Request $request) {
        $user = new User();
        $form = $this->createForm('B2bBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user ->setUsername($user->getMail());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('backoffice_user_show', array('id' => $user->getId()));
        }

        return $this->render('user/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a user entity.
     *
     */
    public function showAction(User $user) {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('user/show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     */
    public function editAction(Request $request, User $user) {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('B2bBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_user_edit', array('id' => $user->getId()));
        }

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a user entity.
     *
     */
    public function deleteAction(Request $request, User $user) {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($user);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_user_index');
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
