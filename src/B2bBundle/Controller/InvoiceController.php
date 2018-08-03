<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Invoice;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InvoiceController extends Controller {

    public function indexAction() {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $em = $this->getDoctrine()->getManager();
            $invoices = $em->getRepository('B2bBundle:Invoice')->findAll();
            $waitingInvoices = $em->getRepository('B2bBundle:Invoice')->findBy(array('status' => Invoice::STATUS_WAITING));
            $remindedInvoies = $em->getRepository('B2bBundle:Invoice')->findReminded();
            return $this->render('invoice/index.html.twig', array(
                'invoices' => $invoices,
                'waitingInvoices' => $waitingInvoices,
                'remindedInvoices' => $remindedInvoies,
            ));
        } else {
            throw $this->createAccessDeniedException('You cannot access this page!');
        }
    }

    public function newAction() {
        return $this->render('invoice/new.html.twig', array(
        ));
    }

    public function createHeaderAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $customer = $em->getRepository('B2bBundle:Customer')->find($request->get('customer_id'));
        return $this->render('invoice/new-header.html.twig', array(
           'customer' =>  $customer
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $customer = $em->getRepository('B2bBundle:Customer')->find($request->get('customer_id'));
        $command  = $em->getRepository('B2bBundle:Command')->find($request->get('command_id'));

        $invoice = new Invoice();
        $invoice->setCustomer($customer);
        $invoice->setCommand($command);

        $form = $this->createForm('B2bBundle\Form\InvoiceType', $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($invoice);
            $copyId = $invoice->getNum() == null;
            if ($copyId) {
                $invoice->setNum(0);
            }
            $invoice->setStatus(Invoice::STATUS_WAITING);
            $em->flush();
            if ($copyId) {
                $invoice->setNum($invoice->getId());
            }
            $em->flush();

            return $this->redirectToRoute('command_index');
        }

        return $this->render('invoice/create.html.twig', array(
            'form' => $form->createView(),
            'invoice' => $invoice
        ));
    }

    // TODO : modifier les paramètres d'envoi ainsi que les messages envoyés

    /**
     * Relancer une Facture
     * @param Invoice $invoice
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remindAction(Invoice $invoice) {
        $em = $this->getDoctrine()->getManager();
        $mail = $invoice->getCustomer()->getContact()->getMail();

        // Envoi du mail de relance au client
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo($mail)
            ->setBody(
                $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'invoice/remind-mail.html.twig',
                    array('invoice' => $invoice)
                ),
                'text/html'
            )
        ;
        $this->get('mailer')->send($message);

        // Mise à jour du statut de la facture
        if ($invoice->getStatus() == Invoice::STATUS_REMINDER_1) {
            $invoice->setStatus(Invoice::STATUS_REMINDER_2);
        } else if ($invoice->getStatus() == Invoice::STATUS_REMINDER_2) {
            $invoice->setStatus(Invoice::STATUS_REMINDER_3);
        } else {
            $invoice->setStatus(Invoice::STATUS_REMINDER_1);
        }
        // Mise à jour de la date de la relance
        $invoice->setRemindDate(new \DateTime("now"));
        $em->flush();

        // Si troisième relance, on contacte l'administrateur et le commercial
        $to = [];
        $salesmen = $invoice->getCommand()->getShop()->getSalesmen();
        $administrator = $em->getRepository('B2bBundle:User')->find($this->getUser()->getId());
        foreach ($salesmen as $salesman) {
            $to[] = $salesman->getContact()->getMail();
        }
        $to[] = $administrator->getMail();
        if ($invoice->getStatus() == Invoice::STATUS_REMINDER_3) {
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('send@example.com')
                ->setTo($to)
                ->setBody(
                    $this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                        'invoice/remind-last.html.twig',
                        array('invoice' => $invoice)
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);
        }
        $this->get('mailer')->send($message);
        $this->addFlash("success", "Relance envoyée");

        return $this->redirectToRoute('invoice_index');
    }
}