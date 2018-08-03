<?php

namespace B2bBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use B2bBundle\Entity\Command;
use B2bBundle\Entity\Invoice;
use B2bBundle\Entity\Salesman;
use B2bBundle\Entity\Brand;
use B2bBundle\Entity\Access;
use B2bBundle\Entity\Customer;
use B2bBundle\Repository\BrandRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;

class BackofficeController extends Controller {

    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $waitingInvoices = $em->getRepository('B2bBundle:Invoice')->findBy(array('status' => Invoice::STATUS_WAITING));
        $customers  = $em->getRepository('B2bBundle:Customer')->findAll();
        $accessToHandle  = $em->getRepository('B2bBundle:Access')->findBy(array('state' => Access::STATUS_SUBMITTED));
        $waitingacces = [];
        $countActiveCustomers = 0;
        $countActiveBrands = 0;
        $countActiveSalesman = 0;
        foreach ($customers as $customer){
            if(!$customer->isValid()){
                $waitingacces[] = $customer;
            }
        }

        $totalSold = 0.;
        $commands = $em->getRepository('B2bBundle:Command')->findBy(array('status' => Command::STATUS_VALIDATED));
        foreach ($commands as $command) {
            $totalSold += $command->getTotalHT();
        }
        $commands = $em->getRepository('B2bBundle:Command')->findAll();

        $waitingCommands = [];

        foreach ($commands as $command) {
            if ($command->getStatus() == Command::STATUS_NOT_VALIDATED) {
                $waitingCommands[] = $command;
            }
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

        return $this->render('B2bBundle:Backoffice:index.html.twig', array(
            'nbTotalCommands'     => $nbTotalCommands,
            'nbTotalProductsSold' => $nbTotalProductsSold,
            'totalSold'           => $totalSold,
            'nbTotalBrandsActifs' => $countActiveBrands,
            'nbTotalProducts'     => $nbTotalProducts,
            'waitinginvoices'     => $waitingInvoices,
            'waitingcommands'     => $waitingCommands,
            'waitingacces'        => $waitingacces,
            'nbTotalCustomersActifs'    => $countActiveCustomers,
            'nbTotalSalesmanActifs'     => $countActiveSalesman,
            'accessToHandle' => $accessToHandle,
        ));
    }
}
