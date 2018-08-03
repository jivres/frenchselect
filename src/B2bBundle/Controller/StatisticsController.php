<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Command;
use B2bBundle\Repository\BrandRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;

class StatisticsController extends Controller {

    private function createBrandPeriodForm($brands) {
        $form = $this->createFormBuilder()
            ->add('brand', EntityType::class, array(
                'label'         => false,
                'class'         => 'B2bBundle:Brand',
                'empty_data'    => null,
                'placeholder'   => 'Aucune marque spécifique',
                'required'      => false,

            ))
            ->add('from', DateType::class, array(
                'label'    => false,
                'widget'   => 'single_text',
                'html5'    => false,
                'required' => false,
                'attr'     => ['class' => 'js-datepicker'],
            ))
            ->add('to', DateType::class, array(
                'label'    => false,
                'widget'   => 'single_text',
                'html5'    => false,
                'required' => false,
                'attr'     => ['class' => 'js-datepicker'],
            ))
            ->getForm();
        return $form;
    }

    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $totalSold = 0.;
        $countActiveCustomers = 0;
        $countActiveBrands = 0;
        $countActiveSalesman = 0;
        $commands = $em->getRepository('B2bBundle:Command')->findBy(array('status' => Command::STATUS_VALIDATED));
        foreach ($commands as $command) {
            $totalSold += $command->getTotalHT();
        }

        $nbTotalCommands     = count($commands);
        $nbTotalProductsSold = $em->getRepository('B2bBundle:Product')->countSold();
        $nbTotalProducts     = count($em->getRepository('B2bBundle:Product')->findAll());


        $brands       = $em->getRepository('B2bBundle:Brand')->findAll();
        $customers       = $em->getRepository('B2bBundle:Customer')->findAll();
        $salesmen       = $em->getRepository('B2bBundle:Salesman')->findAll();


        foreach ($brands as $brand){
            if($brand->getIsActive()){
                $countActiveBrands = $countActiveBrands +1;
            }
        }

        foreach ($customers as $customer){
            if($customer->getIsActive()){
                $countActiveCustomers = $countActiveCustomers +1;
            }
        }

        foreach ($salesmen as $salesman){
            if($salesman->getIsActive()){
                $countActiveSalesman = $countActiveSalesman +1;
            }
        }

        $brands = null;
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $brands = $em->getRepository('B2bBundle:Brand')->findAll();
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            $salesman = $em->getRepository('B2bBundle:Salesman')->find($this->getUser()->getId());
            $brands = $em->getRepository('B2bBundle:Brand')->findForSalesman($salesman);
        }

        $formForBrand    = $this->createBrandPeriodForm($brands);
        $formBestSellers = $this->createBrandPeriodForm($brands);

        return $this->render('statistics/index.html.twig', array(
            'nbTotalCommands'     => $nbTotalCommands,
            'nbTotalProductsSold' => $nbTotalProductsSold,
            'totalSold'           => $totalSold,
            'nbTotalBrandsActifs'       => $countActiveBrands,
            'nbTotalProducts'     => $nbTotalProducts,
            'formForBrand'        => $formForBrand->createView(),
            'formBestSellers'     => $formBestSellers->createView(),
            'nbTotalCustomersActifs'    => $countActiveCustomers,
            'nbTotalSalesmanActifs'     => $countActiveSalesman,
        ));
    }

    public function salesForBrandAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $brands = null;
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $brands = $em->getRepository('B2bBundle:Brand')->findAll();
            } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
                $salesman = $em->getRepository('B2bBundle:Salesman')->find($this->getUser()->getId());
                $brands = $em->getRepository('B2bBundle:Brand')->findForSalesman($salesman);
            }

            $form = $this->createBrandPeriodForm($brands);
            $form->handleRequest($request);

            $data  = $form->getData();
            $brand = $data['brand'];
            $from  = $data['from'];
            $to    = $data['to'];

            $totalSold = 0.;
            $commands = $em->getRepository('B2bBundle:Command')->statistics($brand, $from, $to);
            $nbCommands     = count($commands);
            $nbProductsSold = $em->getRepository('B2bBundle:Product')->countSold($brand, $from, $to);
            foreach ($commands as $command) {
                $totalSold += $command->getTotalHT();
            }

            return $this->render('statistics/sales.html.twig', array(
                'nbCommands'     => $nbCommands,
                'nbProductsSold' => $nbProductsSold,
                'totalSold'      => $totalSold,
            ));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    public function bestsellersAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $brands = null;
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $brands = $em->getRepository('B2bBundle:Brand')->findAll();
            } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
                $salesman = $em->getRepository('B2bBundle:Salesman')->find($this->getUser()->getId());
                $brands = $em->getRepository('B2bBundle:Brand')->findForSalesman($salesman);
            }

            $form = $this->createBrandPeriodForm($brands);
            $form->handleRequest($request);

            $data  = $form->getData();
            $brand = $data['brand'];
            $from  = $data['from'];
            $to    = $data['to'];

            $bestsellers = [];
            // TODO : remettre
            /*
            $bestproducts = $em->getRepository('B2bBundle:Product')->bestSellers($brand, $from, $to);
            foreach ($bestproducts as $bestproduct) {
                $bestsellers[] = $bestproduct[0];
            }*/

            return $this->render('statistics/productlist.html.twig', array(
                'products'  => $bestsellers,
                'hideCount' => true,
            ));
        }
        return new Response("Error : not an Ajax call, 400");
    }
}