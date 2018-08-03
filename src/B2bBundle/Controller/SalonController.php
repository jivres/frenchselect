<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Salesman;
use B2bBundle\Entity\Salon;
use B2bBundle\Entity\ParticipeSalon;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SalonController extends Controller
{
    public function indexAction(){
        $em = $this->getDoctrine()->getManager();


        $salons = $em->getRepository('B2bBundle:Salon')->findAll();

        return $this->render('salon/index.html.twig', array(
            'salons' => $salons,
        ));
    }

    public function newAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $salon = new Salon();
        $formNouveauSalon = $this->createForm('B2bBundle\Form\SalonType', $salon, array('allow_extra_fields' => true,));
        $formNouveauSalon->handleRequest($request);

        if(($formNouveauSalon->isSubmitted()) && ($formNouveauSalon->isValid()) ){
            $marquesparticipantes = $formNouveauSalon->get('brandparticipantes')->getData();
            foreach ($marquesparticipantes as $marque){
                $marque->setSalon($salon);
                $em->persist($marque);
            }
            $salon->setInactive();
            $em->persist($salon);
            $em->flush();
            return $this->redirectToRoute('backoffice_salon_index');
        }

        return $this->render('salon/new.html.twig', array(
            'form' => $formNouveauSalon->createView(),
        ));
    }

    public function editAction(Request $request, Salon $salon){

        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($salon);
        $brands = $em->getRepository('B2bBundle:Brand')->findBy(array('isActive'=> 1));
        $formNouveauSalon = $this->createForm('B2bBundle\Form\SalonType', $salon, array('brands'=>$brands));
        $formNouveauSalon->get('lifestyle')->setData($salon->getLifestyle());
        $formNouveauSalon->get('picture')->setData($salon->getPicture());
        $formNouveauSalon->handleRequest($request);


        if(($formNouveauSalon->isSubmitted()) && ($formNouveauSalon->isValid()) ){
            $em->persist($salon);
            $em->flush();
            return $this->redirectToRoute('backoffice_salon_details', array('id' => $salon->getId()));
        }

        return $this->render('salon/edit.html.twig', array(
            'salon' => $salon,
            'form' => $formNouveauSalon->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function getOneShowroomAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();


        $salon = $em->getRepository('B2bBundle:Salon')->findOneById($id);
        $deleteForm = $this->createDeleteForm($salon);
        $deleteForm->handleRequest($request);


        return $this->render('salon/details.html.twig', array(
            'salon' => $salon,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function activateAction(Salon $salon, Request $request) {
        $salon->setActive();
        $em = $this->getDoctrine()->getManager();
        $em->persist($salon);
        $em->flush();

        $this->addFlash('success', 'Le salon a été activé');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    public function desactivateAction(Salon $salon, Request $request) {
        $salon->setInactive();
        $em = $this->getDoctrine()->getManager();
        $em->persist($salon);
        $em->flush();

        $this->addFlash('danger', 'Le salon a été désactivé');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * Deletes a salon entity.
     * @param Request $request
     * @param Salon $salon
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Salon $salon) {
    $form = $this->createDeleteForm($salon);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        try {
            $em->remove($salon);
            $em->flush();
        } catch (\Doctrine\DBAL\DBALException $e) {
            return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
        }
    }
    return $this->redirectToRoute('backoffice_salon_index');
}

    /**
     * Creates a form to delete a salon entity.
     *
     * @param Salon $salon The Salon entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Salon $salon) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_salon_delete', array('id' => $salon->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

}
?>