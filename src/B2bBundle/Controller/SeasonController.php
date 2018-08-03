<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Season;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Season controller.
 *
 */
class SeasonController extends Controller {
    /**
     * Lists all season entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $seasons = $em->getRepository('B2bBundle:Season')->findAll();

        return $this->render('season/index.html.twig', array(
            'seasons' => $seasons,
        ));
    }

    /**
     * Creates a new season entity.
     * @param Request $request
     * @return
     */
    public function newAction(Request $request) {
        $season = new Season();
        $form = $this->createForm('B2bBundle\Form\SeasonType', $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($season);
            $em->flush();

            return $this->redirectToRoute('backoffice_season_show', array('id' => $season->getId()));
        }

        return $this->render('season/new.html.twig', array(
            'season' => $season,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a season entity.
     * @param Season $season
     * @return
     */
    public function showAction(Season $season) {
        $deleteForm = $this->createDeleteForm($season);

        return $this->render('season/show.html.twig', array(
            'season' => $season,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing season entity.
     * @param Request $request
     * @param Season $season
     * @return
     */
    public function editAction(Request $request, Season $season) {
        $deleteForm = $this->createDeleteForm($season);
        $editForm = $this->createForm('B2bBundle\Form\SeasonType', $season);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_season_edit', array('id' => $season->getId()));
        }

        return $this->render('season/edit.html.twig', array(
            'season' => $season,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a season entity.
     * @param Request $request
     * @param Season $season
     * @return
     */
    public function deleteAction(Request $request, Season $season) {
        $form = $this->createDeleteForm($season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($season);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_season_index');
    }

    /**
     * Creates a form to delete a season entity.
     *
     * @param Season $season The season entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Season $season) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_season_delete', array('id' => $season->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
