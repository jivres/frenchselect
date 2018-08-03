<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\DefectiveProduct;
use B2bBundle\Entity\DefectiveProductReport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;

class DefectiveProductReportController extends Controller {

    public function showAction(DefectiveProductReport $report) {
        return $this->render('defectiveproduct/show.html.twig', array(
            'report' => $report,
        ));
    }

    public function newAction(Request $request) {
        return $this->render('defectiveproduct/select-command.html.twig');
    }

    public function setStatusAction(DefectiveProductReport $report, $status) {
        $report->setStatus($status);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute("report_show", array('id' => $report->getId()));
    }

    /**
     * Search a command
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function searchCommandAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $customer = $em->getRepository('B2bBundle:Customer')->find($this->getUser()->getId());
            $commands = $em->getRepository('B2bBundle:Command')->searchValidatedForCustomer($customer, $request->get('search_text'));
            return $this->render('command/commandlist.html.twig', array('commands' => $commands, 'checkbox' => true, 'viewer' => 'customer'));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    public function newFormAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $command = $commands = $em->getRepository('B2bBundle:Command')->find($request->get('command_id'));

        $report = $em->getRepository('B2bBundle:DefectiveProductReport')->findBy(array('command' => $command));
        if (!$report) {
            $report = new DefectiveProductReport();
            $report->setCommand($command);
        } else {
            $report = $report[0];
        }

        $cartRows = $em->getRepository('B2bBundle:CartRow')->searchFromCommand($command, $request->get('search_text'));

        foreach ($cartRows as $cartRow) {
            foreach ($cartRow->getSizeQuantities() as $sizeQuantity) {
                $report->addDefectiveProductIfNotExists($cartRow->getProduct(), $sizeQuantity->getSize(), $cartRow->getColor());
            }
        }

        $form = $this->createForm('B2bBundle\Form\DefectiveProductReportType', $report, array(
            'action' => $this->generateUrl('report_new_form', array(
                'command_id' => $request->get('command_id'),
                'search_text' => $request->get('search_text'))),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach($report->getDefectiveProducts() as $defectiveProduct) {
                if ($defectiveProduct->getQuantity() < 1) {
                    $report->removeDefectiveProduct($defectiveProduct);
                }
            }
            $em->persist($report);
            $em->flush();

            $mail = $report->getCommand()->getCartCollection()->getBrand()->getMail();

            // Envoi du mail de relance au client
            $message = (new \Swift_Message('Signalement produits défectueux'))
                ->setFrom('send@example.com')
                ->setTo($mail)
                ->setBody(
                    $this->renderView(
                        'defectiveproduct/detail.html.twig',
                        array('report' => $report)
                    ),
                    'text/html'
                )
            ;
            $this->get('mailer')->send($message);

            $this->addFlash('success', 'Signalement envoyé');
            return $this->redirectToRoute('command_index');
        }

        return $this->render('defectiveproduct/new.html.twig', array(
            'cartRows' => $cartRows,
            'report' => $report,
            'form' => $form->createView(),
        ));

        /*return $this->render('brand/new.html.twig', array(
            'brand' => $brand,
            'form' => $form->createView(),
        ));*/

        /*if ($request->isXmlHttpRequest()) {
            $command = $commands = $em->getRepository('B2bBundle:Command')->find($request->get('command_id'));
            //$customer = $em->getRepository('B2bBundle:Customer')->find($this->getUser()->getId());
            $cartRows = $em->getRepository('B2bBundle:CartRow')->searchFromCommand($command, $request->get('search_text'));
            return $this->render('defectiveproduct/new.html.twig', array('cartRows' => $cartRows));
            //return $this->render('command/commandlist.html.twig', array('commands' => $commands, 'checkbox' => true, 'viewer' => 'customer'));
        }*/
    }
}