<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Collection;
use DateTime;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use B2bBundle\Form\MyFileType;

/**
 * Collection controller.
 *
 */
class CollectionController extends Controller {

    /**
     * Lists all collection entities.
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $collections = $em->getRepository('B2bBundle:Collection')->findAll();
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $collections = $em->getRepository('B2bBundle:Brand')->find($this->getUser()->getId())->getCollections();
        }

        return $this->render('collection/index.html.twig', array(
            'collections' => $collections,
        ));
    }
    /**
     * Finds and displays a collection entity.
     * @param Collection $collection
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Collection $collection) {
        $deleteForm = $this->createDeleteForm($collection);

        return $this->render('collection/show.html.twig', array(
            'collection' => $collection,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing collection entity.
     * @param Request $request
     * @param Collection $collection
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Collection $collection) {
        $deleteForm = $this->createDeleteForm($collection);
        $editForm = $this->createForm('B2bBundle\Form\CollectionType', $collection);
        $editForm ->add('lifestyle', MyFileType::class, array(
            'label' => 'Photo Lifestyle', 'data_class' => 'B2bBundle\Entity\MyFile', 'required' => false
        ));
        $editForm->handleRequest($request);


        $lifestyle = $collection->getLifestyle();
        $lookbook = $collection->getLookbook();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $collection->setEndDate(new DateTime(($collection->getEndDate()->format("Y-m-d"))));
            if ($editForm['lifestyle']->getData() == null) {
                $collection->setLifestyle($lifestyle);
            }

            if ($editForm['lookbook']->getData() == null) {
                $collection->setLookbook($lookbook);
            }
            $this->getDoctrine()->getManager()->persist($collection);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('collection_show', array('id' => $collection->getId()));
        }

        return $this->render('collection/edit.html.twig', array(
            'collection' => $collection,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a collection entity.
     * @param Request $request
     * @param Collection $collection
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Collection $collection) {
        $form = $this->createDeleteForm($collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($collection);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_brand_show', array('id' => $collection->getBrand()->getId()));
    }

    /**
     * Creates a form to delete a collection entity.
     *
     * @param Collection $collection The collection entity
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Collection $collection) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_collection_delete', array('id' => $collection->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function activateAction(Collection $collection, Request $request) {
        $collection->setActive();
        $em = $this->getDoctrine()->getManager();
        $em->persist($collection);
        $em->flush();

        $this->addFlash('success', 'La collection a été activée');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    public function desactivateAction(Collection $collection, Request $request) {
        $collection->setInactive();
        $em = $this->getDoctrine()->getManager();
        $em->persist($collection);
        $em->flush();

        $this->addFlash('danger', 'La collection a été désactivée');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
