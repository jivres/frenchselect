<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\AllowedSize;
use B2bBundle\Entity\ColorProduct;
use B2bBundle\Entity\Product;
use B2bBundle\Entity\Collection;
use B2bBundle\Entity\AllowedColor;
use B2bBundle\Form\ColorProductImportType;
use B2bBundle\Form\AllowedColorImportType;
use B2bBundle\Form\MyFileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Product controller.
 *
 */
class ProductController extends Controller
{

    /**
     * Lists all product entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('B2bBundle:Product')->findAll();
        return $this->render('product/index.html.twig', array(
            'products' => $products,
        ));
    }

    /**
     * Creates a new product entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm('B2bBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('backoffice_product_show', array('id' => $product->getId()));
        }

        return $this->render('product/admin-new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }


    /**
     * Ajoute un produit à une Collection
     * (i.e. crée un produit en pré-renseignant la marque)
     * @param Collection $collection
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addToCollectionAction(Collection $collection, Request $request)
    {
        $product = new Product();
        $collection->addProduct($product);
        $product->setCollection($collection);
        $form = $this->createForm('B2bBundle\Form\ProductType', $product, array('allow_extra_fields' => true));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $sizes = $form->get("sizes")->getData();
            foreach ($sizes as $size) {
                $allowedsize = new AllowedSize();
                $allowedsize->setSize($size);
                $product->addAllowedSize($allowedsize);
            }
            $product->setActive();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('backoffice_product_show', array('id' => $product->getId()));
        }


        return $this->render('product/admin-new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
            'collection' => $collection,
        ));
    }

    /**
     * Finds and displays a product entity.
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $view = 'product/admin-show.html.twig';
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $view = 'product/brand-show.html.twig';
        }

        return $this->render($view, array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     * @param Request $request
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm('B2bBundle\Form\ProductType', $product, array('color' => false));
        $sizes = [];
        foreach ($product->getAllowedSizes() as $size) {
            $sizes[] = $size->getSize();
        }
        $editForm->get('sizes')->setData($sizes);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $sizesForm = $editForm->get("sizes")->getData();
            foreach($sizesForm as $sizeForm){
                if(!in_array($sizeForm,$sizes)){
                    $allowedsize = new AllowedSize();
                    $allowedsize->setSize($sizeForm);
                    $product->addAllowedSize($allowedsize);
                }
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_product_show', array('id' => $product->getId()));
        }


        return $this->render('product/admin-edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a product entity.
     * @param Request $request
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Product $product)
    {
        $em = $this->getDoctrine()->getManager();
        try {
            $em->remove($product);
            $em->flush();
        } catch (DBALException $e) {
            return $this->render('error-delete.html.twig', array('error' => $e->getMessage()));
        }
        return $this->redirectToRoute('collection_show', array('id' => $product->getCollection()->getId()));

    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param Product $product The product entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function availabilitiesAction(Request $request, Product $product)
    {
        $form = $this->createForm('B2bBundle\Form\ProductAvailabilitiesType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_product_show', array('id' => $product->getId()));
        }

        return $this->render('product/availability.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }


    public function importAction(Collection $collection = null, Request $request)
    {
        $form = $this->createForm('B2bBundle\Form\UploadedXLSType', null);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            // $file = $request->get('file');
            $file = $form['file']->getData();
            $reader = $this->container->get('arodiss.xls.reader');
            $iterator = $reader->getReadIterator($file->getRealPath());
            // On saute la ligne des entetes
            $iterator->next();
            $errors = array();
            $buf = array();
            $entityList = array();
            $alreadyExists = array();
            $duplicates = array();

            $em = $this->getDoctrine()->getManager();
            while ($iterator->valid()) {
                $line = $iterator->current();
                $noError = $this->parsingLine($line, $buf, $errors, $entityList, $alreadyExists, $duplicates);
                $iterator->next();
            }

            $alreadyList = "";
            $cptAL = 0;
            foreach ($alreadyExists as $refUnique) {
                if ($cptAL == 0) {
                    $alreadyList = (string)$refUnique;
                } else {
                    $alreadyList = $alreadyList . " - $refUnique";
                }
                $cptAL++;
            }
            if ($cptAL > 0) {
                $this->addFlash(
                    'warning',
                    "Les $cptAL produits suivant existent déjà : $alreadyList"
                );
            }

            $duplicateList = "";
            $cptAL = 0;
            foreach ($duplicates as $refUnique) {
                if ($cptAL == 0) {
                    $duplicateList = (string)$refUnique;
                } else {
                    $duplicateList = $duplicateList . " - $refUnique";
                }
                $cptAL++;
            }
            if ($cptAL > 0) {
                $this->addFlash(
                    'warning',
                    "Les $cptAL références suivants sont utilisées par plusieurs produits : $duplicateList"
                );
            }


            if (count($errors) > 0) {
                // Bug :
                // $this->get('session')->set('errors', $errors);
                // Fonctionne :
                // $this->get('session')->set('errors_byRef', $errors['byRef']);
                // $this->get('session')->set('errors_byEntity', $errors['byEntity']);


                $this->get('session')->set('buf_entities', $buf);
                $this->get('session')->set('collection_id', $collection->getId());
                return $this->render('product/revise.import.html.twig', array(
                    'errors' => $errors,
                    'entityList' => $entityList
                ));
            }
            return $this->importFixing(null, $buf, $collection->getId(), $request);
        }
        return $this->render('product/import.html.twig', array(
            'form' => $form->createView(),
            'collection' => $collection,
        ));
    }

    private function parsingLine($line, &$buf, &$errors, &$entityList, &$alreadyExists, &$duplicates)
    {
        /*****************************************
         *  IMFORMATIONS LIEES A LA REF UNIQUE
         *****************************************
         * 0 => "Référence Unique" c
         * 4 => "Code EAN" c x
         * 9 => "Code Couleur" c x
         * 10 => "Couleur" c
         * 11 => "Taille" p
         * 12 => "Prix d'achat" p
         * 13 => "Réduction" p  TODO Si possible mettre dans couleur
         * 14 => "Prix de vente" p
         * 19 => "Description" p
         * 20 => "Informations complémentaires" ? x
         * 21 => "Date de disponibilité" p ==> c x ?? TODO Si possible IMPORTANT
         *
         *******************************************
         * INFORMATIONS LIEES A LA REF MODELE
         *******************************************
         * 1 => "Référence Modèle" p
         * 2 => "Nom" p
         * 3 => "Libellé complet" p
         * 5 => "Cible" p
         * 6 => "Catégorie primaire" p
         * 7 => "Catégorie Secondaire" p
         * 8 => "Catégorie Tertiaire" p
         * 15 => "Pays de fabrication" p
         * 16 => "Matières" p
         * 17 => "Entretien" p
         * 18 => "Dimensions" p
         * *****************************************
         * p : Attribut dans Product
         * c : Attribut dans AllowedColor
         */
        /*
DELETE FROM availability;
DELETE FROM allowed_size;
DELETE FROM allowed_color;
DELETE FROM product;

         */
        $noError = true;
        $em = $this->getDoctrine()->getManager();
        $refUnique = $line[0];
        if ($line[0] == "") {
            return true;
        }
        $refModel = $line[1];
        if ($refModel == "") {
            $refModel = "NC";
            $noError = false;
        }
        $modelFirstTime = !isset($buf[$refModel]);
        if ($modelFirstTime) {
            $productRepo = $em->getRepository('B2bBundle\Entity\Product');
            $modelFirstTime = $productRepo->findOneByRefModel($refModel) == null;
        }
        // TODO refModel 1--n refUnique
        if (isset($buf[$refModel]['color'][$refUnique])) {
            $duplicates[] = $refUnique;
            return false;
        } elseif ($em->getRepository('B2bBundle:AllowedColor')->findByRefUnique($refUnique) != null) {
            //
            // Génère trop de message en cas de grand nombre de doublons !
            //
            // $this->addFlash(
            //     'warning',
            //     "Le produit ayant la référence [$refUnique] existe déjà"
            // );
            $alreadyExists[] = $refUnique;
            return false;
        }
        if ($modelFirstTime) {
            $this->preSetter($buf, $refModel, $refUnique, 'refModel', $refModel, null, 'setRefModel', true);

            $this->preSetter($buf, $refModel, $refUnique, 'name', $line[2], null, 'setName', true);

            // * 4 => "Code EAN" c x

            $noError = $this->preSetterEntity(
                    $buf, $errors, $entityList, $refModel, $refUnique,
                    'target',
                    $line[5],
                    'setTarget',
                    $em->getRepository('B2bBundle:Target'),
                    'findOneByLabel',
                    'B2bBundle\Entity\Target',
                    'setLabel',
                    true
                ) && $noError;
            $noError = $this->preSetterEntity(
                    $buf, $errors, $entityList, $refModel, $refUnique,
                    'primaryCat',
                    $line[6],
                    'setPrimaryCat',
                    $em->getRepository('B2bBundle:PrimaryCategory'),
                    'findOneByLabel',
                    'B2bBundle\Entity\PrimaryCategory',
                    'setLabel',
                    true
                ) && $noError;
            $noError = $this->preSetterEntity(
                    $buf, $errors, $entityList, $refModel, $refUnique,
                    'secondaryCat',
                    $line[7],
                    'setSecondaryCat',
                    $em->getRepository('B2bBundle:SecondaryCategory'),
                    'findOneByLabel',
                    'B2bBundle\Entity\SecondaryCategory',
                    'setLabel',
                    true
                ) && $noError;
            $this->preSetter($buf, $refModel, $refUnique, 'tertiaryCategory', $line[8], null, 'setTertiaryCategory', true);
            // * 9 => "Code Couleur" c x


            $sizes = explode(';', $line[11]);
            foreach ($sizes as $value) {
                $noError = $this->preSetterEntity(
                        $buf, $errors, $entityList, $refModel, $refUnique,
                        'allowedSize',
                        $value,
                        'addAllowedSizeFromSize',
                        $em->getRepository('B2bBundle:Size'),
                        'findOneByVal',
                        'B2bBundle\Entity\Size',
                        'setVal',
                        true,
                        true
                    ) && $noError;
            }

            $this->preSetter($buf, $refModel, $refUnique, 'priceHT', $line[12], null, 'setPriceHT', true);

            $this->preSetter($buf, $refModel, $refUnique, 'recommendedPriceTTC', $line[14], null, 'setRecommendedPriceTTC', true);


            $noError = $this->preSetterEntity(
                    $buf, $errors, $entityList, $refModel, $refUnique,
                    'country',
                    $line[15],
                    'setCountry',
                    $em->getRepository('B2bBundle:Country'),
                    'findOneByName',
                    'B2bBundle\Entity\Country',
                    'setName',
                    true
                ) && $noError;

            $this->preSetter($buf, $refModel, $refUnique, 'material', $line[16], null, 'setMaterial', true);
            $this->preSetter($buf, $refModel, $refUnique, 'maintenance', $line[17], null, 'setMaintenance', true);
            $this->preSetter($buf, $refModel, $refUnique, 'dimensions', $line[18], null, 'setDimensions', true);
            $this->preSetter($buf, $refModel, $refUnique, 'description', $line[19], null, 'setDescription', true);


            // Conversion date xls -> date php
            // https://stackoverflow.com/questions/11119631/excel-date-conversion-using-php-excel
        }

        $this->preSetter($buf, $refModel, $refUnique, 'refUnique', $refUnique, null, 'setRefUnique', false);
        $this->preSetter($buf, $refModel, $refUnique, 'eanCode', $line[4], null, 'setEanCode', false);
        $this->preSetter($buf, $refModel, $refUnique, 'colorCode', $line[9], null, 'setColorCode', false);
        $noError = $this->preSetterEntity2(
                $buf, $errors, $entityList, $refModel, $refUnique,
                'allowedColor',
                $line[10],
                'setColor',
                // 'addAllowedColorFromColor',
                $em->getRepository('B2bBundle:ColorProduct'),
                'findOneByLabel',
                'B2bBundle\Entity\ColorProduct',
                'setLabel',
                false
            ) && $noError;

        $this->preSetter($buf, $refModel, $refUnique, 'reduction', $line[13], null, 'setReduction', false);
        $date = new \DateTime();
        $date->setTimestamp(($line[21] - 25569) * 86400);
        $this->preSetter($buf, $refModel, $refUnique, 'deliveryStart', $date, null, 'setDeliveryStart', false);

        return $noError;
    }

    private function preSetter(&$buf, $refModel, $refUnique, $field, $val, $id, $setter, $isModelInfo, $isCollection = false, $hasError = false, $repo = null, $finder = null, $entity = null, $setVal = null)
    {
        $array = array(
            'hasError' => $hasError,
            'val' => $val,
            'id' => $id,
            'setter' => $setter,
            'entity' => $entity,
            'setVal' => $setVal
        );
        if ($isModelInfo) {
            // $buf[$refModel][$refUnique][$field]['type'] = 'element';
            if ($isCollection) {

                if (!isset($buf[$refModel]['data'][$field])) {
                    // $buf[$refModel][$refUnique][$field]['data'] = array();
                    $buf[$refModel]['data'][$field]['data'] = array();
                    $buf[$refModel]['data'][$field]['type'] = 'array';
                }
                // $buf[$refModel][$refUnique][$field]['type'] = 'array';
                $buf[$refModel]['data'][$field]['data'][] = $array;
            } else {
                $buf[$refModel]['data'][$field]['type'] = 'element';
                $buf[$refModel]['data'][$field]['data'] = $array;
            }
            if (!isset($buf[$refModel]['this'])) {
                $buf[$refModel]['this'] = 'null';
            }
            // $buf[$refModel][$refUnique][$field]['data'] = array(
        } else {
            $buf[$refModel]['color'][$refUnique]['data'][$field]['type'] = 'element';
            $buf[$refModel]['color'][$refUnique]['data'][$field]['data'] = $array;
            // $buf[$refModel][$refUnique][$field]['type'] = 'element';
            if ($isCollection) {
                if (!isset($buf[$refModel]['color'][$refUnique]['data'][$field])) {
                    // $buf[$refModel][$refUnique][$field]['data'] = array();
                    $buf[$refModel]['color'][$refUnique]['data'][$field]['data'] = array();
                    $buf[$refModel]['color'][$refUnique]['data'][$field]['type'] = 'array';
                }
                // $buf[$refModel][$refUnique][$field]['type'] = 'array';
                $buf[$refModel]['color'][$refUnique]['data'][$field]['data'][] = $array;
            } else {
                $buf[$refModel]['color'][$refUnique]['data'][$field]['type'] = 'element';
                $buf[$refModel]['color'][$refUnique]['data'][$field]['data'] = $array;
            }
            if (!isset($buf[$refModel]['color'][$refUnique]['this'])) {
                $buf[$refModel]['color'][$refUnique]['this'] = 'null';
            }

            // $buf[$refModel][$refUnique][$field]['data'] = array(

        }
    }

    private function preSetterEntity(&$buf, &$errors, &$entityList, $refModel, $refUnique, $field, $val, $setter, $repo, $finder, $entity, $setVal, $isModelInfo, $isCollection = false)
    {
        $obj = $repo->$finder($val);
        if ($obj == null) {
            if (!isset($entityList[$entity])) {
                $entityList[$entity]['list'] = $repo->findAll();
                $entityList[$entity]['new'] = array();
            }
            if (!in_array($val, $entityList[$entity]['new'])) {
                $entityList[$entity]['new'][] = $val;
            }
            $this->preSetter($buf, $refModel, $refUnique, $field, $val, null, $setter, $isModelInfo, $isCollection, true, $repo, $finder, $entity, $setVal);
            $this->preSetter($errors, $refModel, $refUnique, $field, $val, null, $setter, $isModelInfo, $isCollection, true, $repo, $finder, $entity, $setVal);
            return false;
        } else {
            $this->preSetter($buf, $refModel, $refUnique, $field, $val, $obj->getId(), $setter, $isModelInfo, $isCollection, false, $repo, $finder, $entity, $setVal);
            return true;
        }

    }

    private function preSetterEntity2(&$buf, &$errors, &$entityList, $refModel, $refUnique, $field, $val, $setter, $repo, $finder, $entity, $setVal, $isModelInfo, $isCollection = false)
    {
        $obj= new ColorProduct();
        $obj -> setLabel($val);
        $this->preSetter($buf, $refModel, $refUnique, $field, $val, $obj->getId(), $setter, $isModelInfo, $isCollection, false, $repo, $finder, $entity, $setVal);
        return true;
    }

    private function importFixing($corrections, &$buf, $collection_id, $request)
    {
        $logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();
        $productRepo = $em->getRepository('B2bBundle\Entity\Product');
        $nbProductPersisted = 0;
        $refModel = "";
        $newEntities = array();
        // $buf[$refModel]['data'][$field]['data'] = $array;
        // $buf[$refModel]['color'][$refUnique]['data'][$field]['data'] = $array;
        foreach ($buf as $refModel => $model_array) {

            $product = $productRepo->findOneByRefModel($refModel);
            if ($product == null) {
                $product = new Product();
            }
            $buf[$refModel]['this'] = $product;
            $product->setCollection($em->getReference('B2bBundle\Entity\Collection', $collection_id));
            if (isset($model_array['data'])) {
                foreach ($model_array['data'] as $field => $data) {
                    if ($data['type'] == 'array') {
                        foreach ($data['data'] as $elem) {
                            $willPersist = $this->fixImport($product, $elem, $corrections, $newEntities, $refModel, "", $field, true);
                            if (!$willPersist) {
                                break;
                            }
                        }
                    } else {
                        $willPersist = $this->fixImport($product, $data['data'], $corrections, $newEntities, $refModel, "", $field);
                    }
                    if (!$willPersist) {
                        break;
                    }
                }
            }
            if (isset($model_array['color'])) {
                foreach ($model_array['color'] as $refUnique => $color_array) {
                    $color = new AllowedColor();
                    $buf[$refModel]['color'][$refUnique]['this'] = $color;
                    foreach ($color_array['data'] as $field => $data) {
                        if ($data['type'] == 'array') {
                            foreach ($data['data'] as $elem) {
                                $willPersist = $this->fixImport($color, $elem, $corrections, $newEntities, $refModel, $refUnique, $field, true);
                                if (!$willPersist) {
                                    break;
                                }
                            }
                        } else {
                            $willPersist = $this->fixImport($color, $data['data'], $corrections, $newEntities, $refModel, $refUnique, $field);
                        }
                    }
                    $product->addAllowedColor($color);
                }
            }

            if ($willPersist) {
                $logger->debug('persit - ' . $refModel);
                $em->persist($product);
                $nbProductPersisted++;
            } else {
                $logger->debug('don\'t persit - ' . $refModel);
            }
        }
        $em->flush();
        if ($nbProductPersisted == 0) {
            $this->addFlash(
                'warning',
                "Aucun produit n'a été importé"
            );
        } elseif ($nbProductPersisted == 1) {
            $this->addFlash(
                'success',
                "$nbProductPersisted produit a été importé"
            );
        } else {
            $this->addFlash(
                'success',
                "$nbProductPersisted produits ont été importés"
            );
        }

        return $this->redirectToRoute('backoffice_product_import_ending_1', array('id' => $collection_id));

    }

    public function importFixingAction(Request $request)
    {
        $corrections = $request->get('correction');
        $session = $this->get('session');
        $buf = $session->get('buf_entities');
        $collection_id = $session->get('collection_id');
        // dump($corrections);
        // dump($buf);
        // die();
        return $this->importFixing($corrections, $buf, $collection_id, $request);
    }

    private function fixImport($elemObj, &$elem, &$corrections, &$newEntities, $refModel, $refUnique, $field, $isArray = false)
    {
        $logger = $this->get('logger');
        if ($elem['hasError']) {
            // dump($elem);
            // dump($corrections);
            // die();
            $objCorrections = null;
            if ($refUnique == null) {
                $objCorrections = $corrections[$refModel]['data'][$field];
            } else {
                $objCorrections = $corrections[$refModel]['color'][$refUnique]['data'][$field];
            }
            if ($isArray) {
                foreach ($objCorrections as $correc) {
                    if ($correc['val'] == $elem['val']) {
                        return $this->applyFix($elemObj, $elem, $correc, $newEntities);

                    }
                }
            } else {
                return $this->applyFix($elemObj, $elem, $objCorrections, $newEntities);

            }
        } else {
            return $this->setByArray($elemObj, $elem);
        }
    }

    private function applyFix($elemObj, &$elem, &$correction, &$newEntities)
    {
        $setter = $elem['setter'];
        $choice = $correction['choice'];
        $id = -1;
        if (isset($correction['existingValue'])) {
            $id = (int)$correction['existingValue'];
        }
        if ($correction == null) {
            $choice = 'change';
            $id = $elem['id'];
        } else {
            // dump($correction);
            // dump($elem);
            // die();
        }
        switch ($choice) {
            case 'ignore':
                return false;
                break;
            case 'create':
                $obj = null;
                if (isset($newEntities[$correction['entity']])) {
                    foreach ($newEntities[$correction['entity']] as $newEntity) {
                        if ($newEntity['val'] == $correction['val']) {
                            $obj = $newEntity['this'];
                        }
                    }
                    if ($obj == null) {
                        $obj = new $correction['entity']();
                        $setVal = $elem['setVal'];
                        $obj->$setVal($correction['val']);
                        $newEntities[$correction['entity']][] = array(
                            'val' => $correction['val'],
                            'this' => $obj
                        );
                    }
                } else {
                    $obj = new $correction['entity']();
                    $setVal = $elem['setVal'];
                    $obj->$setVal($correction['val']);
                    $newEntities[$correction['entity']] = array();
                    $newEntities[$correction['entity']][] = array(
                        'val' => $correction['val'],
                        'this' => $obj
                    );
                }
                $elemObj->$setter($obj);
                break;
            case 'change':
                // dump($correction);
                // dump($elemObj);
                // dump($elem);
                // dump($newEntities);
                // dump($id);
                // die();
                $em = $this->getDoctrine()->getManager();
                $elemObj->$setter($em->getReference($correction['entity'], $id));
                break;
            default:
                // dump($elemObj);
                // dump($elem);
                // dump($correction);
                die("ERROR : applyFix - choice didn't match");
                break;
        }
        return true;
    }

    private function setByArray($elemObj, &$elem)
    {
        $setter = $elem['setter'];
        if ($elem['id'] == null) {
            $elemObj->$setter($elem['val']);
        } else {
            $em = $this->getDoctrine()->getManager();
            $elemObj->$setter($em->getReference($elem['entity'], $elem['id']));
        }
        return true;
    }

    public function importEnding1Action(Request $request, Collection $collection)
    {

        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('B2bBundle:Product')->findBy(array('collection' => $collection));
        $colorProducts = array();

        foreach ($products as $product) {
            foreach ($product->getAllowedColors() as $allowedColor) {
                if ($allowedColor->getColor()->getPicture() == null && !in_array($allowedColor->getColor(), $colorProducts)) {
                    $colorProducts[] = $allowedColor->getColor();
                }
            }
        }
        $form = $this->createFormBuilder(array('colorProducts' => $colorProducts))
            ->add('colorProducts', CollectionType::class, array(
                'entry_type' => ColorProductImportType::class))->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($colorProducts as $colorProduct) {
                $em->persist($colorProduct);
            }
            $em->flush();
            $this->addFlash('success', 'ENFIN');
        }

        return $this->render('product/end.import.html.twig', array(
            'colorProducts' => $colorProducts,
            'form' => $form->createView(),
        ));
    }
}
