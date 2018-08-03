<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\AllowedColor;
use B2bBundle\Entity\Product;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Allowedcolor controller.
 *
 */
class AllowedColorController extends Controller
{
    /**
     * Lists all allowedColor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $allowedColors = $em->getRepository('B2bBundle:AllowedColor')->findAll();

        return $this->render('allowedcolor/index.html.twig', array(
            'allowedColors' => $allowedColors,
        ));
    }

    /**
     * Creates a new allowedColor entity.
     * @param Product $product, Request $request
     * @return
     */
    public function newAction(Product $product, Request $request)
    {
        $allowedColor = new Allowedcolor();
        $form = $this->createForm('B2bBundle\Form\AllowedColorType', $allowedColor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->addAllowedColor($allowedColor);
            $em = $this->getDoctrine()->getManager();
            $em->persist($allowedColor);
            $em->flush();

            return $this->redirectToRoute('backoffice_product_show', array('id' => $product->getId()));
        }

        return $this->render('allowedcolor/new.html.twig', array(
            'allowedColor' => $allowedColor,
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a allowedColor entity.
     * @param AllowedColor $allowedColor
     * @return
     */
    public function showAction(AllowedColor $allowedColor)
    {
        $deleteForm = $this->createDeleteForm($allowedColor);

        return $this->render('allowedcolor/show.html.twig', array(
            'allowedColor' => $allowedColor,
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing allowedColor entity.
     * @param Request $request
     * @param AllowedColor $allowedColor
     * @return
     */
    public function editAction(Request $request, AllowedColor $allowedColor)
    {
        $deleteForm = $this->createDeleteForm($allowedColor);
        $editForm = $this->createForm('B2bBundle\Form\AllowedColorType', $allowedColor);
        $editForm->get('color')->get('picture')->setData($allowedColor->getColor()->getPicture());
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('backoffice_product_show', array('id' => $allowedColor->getProduct()->getId()));
        }

        return $this->render('allowedcolor/edit.html.twig', array(
            'allowedColor' => $allowedColor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a allowedColor entity.
     * @param Request $request
     * @param AllowedColor $allowedColor
     * @return
     */
    public function deleteAction(Request $request, AllowedColor $allowedColor)
    {
        $product = $allowedColor->getProduct();
        $form = $this->createDeleteForm($allowedColor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try {
                foreach ($product->getAvailabilities() as $availability) {
                    if ($availability->getColor()->getId() == $allowedColor->getColor()->getId() && $availability->getProduct()->getId() == $product->getId()) {
                        $em->remove($availability);
                    }
                }
                $em->remove($allowedColor);
                $em->flush();
            } catch (DBALException $e) {
                return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
            }
        }

        return $this->redirectToRoute('backoffice_product_show', array('id' => $product->getId()));
    }

    /**
     * Creates a form to delete a allowedColor entity.
     *
     * @param AllowedColor $allowedColor The allowedColor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(AllowedColor $allowedColor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('allowedcolor_delete', array('id' => $allowedColor->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
