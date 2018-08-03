<?php

namespace B2bBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use B2bBundle\Entity\MyFile;
use B2bBundle\Entity\Product;
use B2bBundle\Entity\AllowedColor;

class PictureController extends Controller
{

    public function uploadAction(Request $request, AllowedColor $allowedColor)
    {
        $em = $this->getDoctrine()->getManager();
        $media = $request->files->get('file');
        $picture = new MyFile();
        $picture->setFile($media);
        $allowedColor->addFigure($picture);
        $em->persist($allowedColor);
        $em->flush();
        return new JsonResponse(array('success' => true, 'file' => $picture->getId()));
    }
    public function validAction(AllowedColor $allowedColor)
    {
        $em = $this->getDoctrine()->getManager();
        return $this->redirectToRoute('backoffice_product_show', array('id' => $allowedColor->getProduct()->getId()));
    }

    public function removeAction(Request $request, AllowedColor $allowedColor, MyFile $picture)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createDeleteForm($allowedColor, $picture);
        $form->handleRequest($request);

        // $allowedColor->removeFigure($picture);
        if ($form->isSubmitted() && $form->isValid()) {
            $allowedColor->removeFigure($picture);
            $em->remove($picture);
            $em->flush();
        }

        return $this->redirectToRoute('backoffice_product_color_picture', array('product' => $allowedColor->getProduct()->getId(), 'allowedColor' => $allowedColor->getId()));
    }

    public function editProductPicturesAction(Request $request, Product $product, AllowedColor $allowedColor){
        $pictures = $allowedColor->getFigures();
        $form = $this->createForm('B2bBundle\Form\AllowedColorType', $allowedColor, array('editPictures' => true));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('backoffice_product_show', array('id' => $product->getId()));
        }
        $pictures = array();


        foreach ($allowedColor->getFigures() as $picture ) {
            $deleteForm = $this->createDeleteForm($allowedColor, $picture)->createView();
            $pictures[] = array('picture' => $picture, 'deleteForm' => $deleteForm);
        }
        return $this->render('product/pictures.html.twig', array(
            'product' => $product,
            'pictures' => $pictures,
            'color' => $allowedColor,
            'form' => $form->createView()
        ));
    }

    private function createDeleteForm(AllowedColor $allowedColor, MyFile $picture) {
        return $this->createFormBuilder()
        ->setAction($this->generateUrl('backoffice_pictures_remove', array('allowedColor' => $allowedColor->getId(), 'picture' => $picture->getId())))
        ->setMethod('DELETE')
        ->getForm()
        ;
    }

}
