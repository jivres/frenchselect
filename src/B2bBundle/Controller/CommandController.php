<?php

namespace B2bBundle\Controller;

use B2bBundle\Entity\Cart;
use B2bBundle\Entity\Command;
use B2bBundle\Entity\Invoice;
use B2bBundle\Form\CartCollectionRecapType;
use B2bBundle\Form\CommandEditType;
use B2bBundle\Form\CommandRecapType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommandController extends Controller
{

    public function indexAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->indexForAdmin($request);
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_CUSTOMER')) {
            return $this->indexForCustomer($request);
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            return $this->indexForBrand($request);
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            return $this->indexForCustomer($request);
        }
    }

    public function modalAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $command = $em->getRepository('B2bBundle:Command')->find($request->query->get('id'));
        $viewer = $request->query->get('viewer');
        if($command->getStatus() == Command::STATUS_VALIDATED){
            return $this->render('command/modal.html.twig', array('command' => $command, 'viewer' => $viewer, 'readonly' => true));
        }
        return $this->render('command/modal.html.twig', array('command' => $command, 'viewer' => $viewer));
    }

    public function showAction(Command $command, Request $request)
    {

        return $this->render('command/show.html.twig', array(
            'cmd' => $command
        ));
    }


    public function exportAction(Command $cmd)
    {
        return $this->render('B2bBundle:Excel:export_command.xlsx.twig', array(
            'cmd' => $cmd,
        ));
    }

    /** ADMIN */

    public function indexForAdmin(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commands = $em->getRepository('B2bBundle:Command')->findAll();

        $waitingCommands = [];

        foreach ($commands as $command) {
            if ($command->getStatus() == Command::STATUS_NOT_VALIDATED) {
                $waitingCommands[] = $command;
            }
        }

        return $this->render('command/admin-index.html.twig', array(
            'commands' => $commands,
            'waitingCommands' => $waitingCommands,
        ));
    }

    /** CLIENT **/

    public function indexForCustomer(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            $salesman = $em->getRepository('B2bBundle:Salesman')->find($user->getId());
            $customer = $salesman->getConnectedFor();
        } else {
            $customer = $em->getRepository('B2bBundle:Customer')->find($user->getId());
        }
        $commands = $em->getRepository('B2bBundle:Command')->findForCustomer($customer);
        $reports = $em->getRepository('B2bBundle:DefectiveProductReport')->findForCustomer($customer);

        $nb_validated = 0;
        $nb_not_validated = 0;

        foreach ($commands as $command) {
            if ($command->getStatus() == Command::STATUS_NOT_VALIDATED) {
                $nb_not_validated += 1;
            } else if ($command->getStatus() == Command::STATUS_VALIDATED) {
                $nb_validated += 1;
            }
        }

        return $this->render('command/customer-index.html.twig', array(
            'commands' => $commands,
            'reports' => $reports,
            'nb_validated' => $nb_validated,
            'nb_not_validated' => $nb_not_validated,
        ));
    }


    public function validate1Action(Cart $cart, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $cartCollection = $cart->getCartCollections()[0]; // On prend la première collection

        $command = $em->getRepository('B2bBundle:Command')->findForCartCollection($cartCollection);

        if (!$command) {
            $command = new Command();
            $command->setStatus(Command::STATUS_CREATED);
            $command->setCartCollection($cartCollection);
            $command->updateTotalHT();

            $em->persist($command);
            $em->flush();
        } else {
            $command = $command[0];
        }
        $canValidate = $command->getShop() != null;
        $validateMessage = "Vous n'avez pas renseigné de boutique pour la commande.";
        $shopIds = new ArrayCollection();
        foreach ($command->getCartCollection()->getCustomer()->getShops() as $shop) {
            $shopIds[] = $shop->getId();
        }

        $editForm = $this->createForm('B2bBundle\Form\CommandType', $command,
            array('shopIds' => $shopIds));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($command->getShop() != null) {

                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('command_validate2', array('id' => $command->getId()));
            }
        }

        return $this->render('command/new-1.html.twig', array(
            'command' => $command,
            'edit_form' => $editForm->createView(),
            'step' => 1,
            'canValidate' => $canValidate,
            'validateMessage' => $validateMessage,
        ));
    }

    public function validate2Action(Command $command, Request $request)
    {
        $paymentMethodIds = new ArrayCollection();
        $cart = $command->getCartCollection()->getCart();

        $minDeliveryDates = [];

        foreach ($cart->getCartCollections() as $cartCollection) {
            $minDate = new \DateTime("now");
            foreach ($cartCollection->getCartCategories() as $cartCategory) {
                foreach ($cartCategory->getCartRows() as $cartRow) {
                    $product = $cartRow->getProduct();
                    foreach ($product->getAllowedColors() as $allowedColor) {
                        if ($cartRow->getColor() == $allowedColor->getColor()) {
                            if ($minDate < $allowedColor->getDeliveryStart()) {
                                $minDate = $allowedColor->getDeliveryStart();
                            }
                        }
                    }
                }
            }
            $minDeliveryDates[] = $minDate;
        }

        foreach ($command->getCartCollection()->getBrand()->getPaymentMethods() as $paymentMethod) {
            $paymentMethodIds[] = $paymentMethod->getId();
        }

        $paymentTermsIds = new ArrayCollection();
        foreach ($command->getCartCollection()->getBrand()->getPaymentTerms() as $paymentTerm) {
            $paymentTermsIds[] = $paymentTerm->getId();
        }

        $form = $this->createFormBuilder(array('cartCollections' => $cart->getCartCollections(), 'command' => $command))

            ->add('cartCollections', CollectionType::class, array(
                'entry_type' => CartCollectionRecapType::class
            ))

            ->add('command', CommandRecapType::class, array(
                'paymentMethodIds' => $paymentMethodIds,
                'paymentTermsIds' => $paymentTermsIds
            ))

            ->getForm();





        /*$editForm = $this->createForm('B2bBundle\Form\CommandRecapType', $command,
            array('paymentMethodIds' => $paymentMethodIds,
                'paymentTermsIds' => $paymentTermsIds));*/

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('command_validate3', array('id' => $command->getId()));
        }

        return $this->render('command/new-summary.html.twig', array(
            'command' => $command,
            'edit_form' => $form->createView(),
            'step' => 2,
            'minDeliveryDates' => $minDeliveryDates,
        ));
    }

    public function validate3Action(Command $command, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cart = $command->getCartCollection()->getCart();
        $commands = [];

        $customer = $command->getCartCollection()->getCustomer();
        $brand = $command->getCartCollection()->getBrand();

        foreach ($cart->getCartCollections() as $cartCollection) {
            $currentCommand = $em->getRepository('B2bBundle:Command')->findForCartCollection($cartCollection);

            $cart->removeCartCollection($cartCollection);

            if (!$currentCommand) {
                $currentCommand = new Command();
                $currentCommand->copy($command);
                $currentCommand->setStatus(Command::STATUS_CREATED);
                $currentCommand->setCartCollection($cartCollection);
                $currentCommand->updateTotalHT();
                $em->persist($currentCommand);
            } else {
                $currentCommand = $currentCommand[0];
            }

            $currentCommand->setStatus(Command::STATUS_NOT_VALIDATED);
            $currentCommand->setDate(new \DateTime("now"));
            $em->persist($currentCommand);
            $em->flush();
            $commands[] = $currentCommand;


            // Les mails !!!
            // Le client !!!
            $mailCustomer = $customer->getMail();

            $messageCustomer = (new \Swift_Message('Mon B2B - Commande Passée'))
                ->setFrom('testb2b@french-select.com')
                ->setTo($mailCustomer)
                ->setBody($this->renderView('B2bBundle:Mails:report.html.twig', array(
                    'command' => $currentCommand,
                )), 'text/html');

            $this->get('mailer')->send($messageCustomer);

            // La marque !!!
            $mailBrand = $brand->getMail();

            $messageBrand = (new \Swift_Message('Mon B2B - Commande Passée'))
                ->setFrom('testb2b@french-select.com')
                ->setTo($mailBrand)
                ->setBody($this->renderView('B2bBundle:Mails:report.html.twig', array(
                    'command' => $currentCommand,
                )), 'text/html');


            $this->get('mailer')->send($messageBrand);

            // Les admins !!!
            $admins = $em->getRepository('B2bBundle:Admin')->findAll();
            $mailsAdmin = [];
            foreach ($admins as $admin) {
                $mailsAdmin[] = $admin->getMail();
            }

            foreach ($mailsAdmin as $mailAdmin) {
                $message = (new \Swift_Message('Nouvelle commande'))
                    ->setFrom('testb2b@french-select.com')
                    ->setTo($mailAdmin)
                    ->setBody($this->renderView('B2bBundle:Mails:report.html.twig', array(
                        'command' => $currentCommand,
                    )), 'text/html');
                $this->get('mailer')->send($message);
            }

        }

        $em->remove($cart);
        $em->flush();

        return $this->render('command/recap.html.twig', array(
            'commands' => $commands,
            'command' => $command,
            'step' => 3,
        ));
    }

    /** MARQUE **/

    public function indexForBrand(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository('B2bBundle:Brand')->find($this->getUser()->getId());
        $commands = $em->getRepository('B2bBundle:Command')->findCommands($brand);
        $invoices = $em->getRepository('B2bBundle:Invoice')->findInvoices($brand);
        $waitingCommands = [];
        $waitingInvoices = [];

        foreach ($commands as $command) {
            if ($command->getStatus() == Command::STATUS_NOT_VALIDATED) {
                $waitingCommands[] = $command;
            }
        }

        foreach ($invoices as $invoice) {
            if ($invoice->getStatus() <> Invoice::STATUS_PAID) {
                $waitingInvoices[] = $invoice;
            }
        }

        $reports = $em->getRepository('B2bBundle:DefectiveProductReport')->findForBrand($brand);

        return $this->render('command/brand-index.html.twig', array(
            'commands' => $commands,
            'waitingCommands' => $waitingCommands,
            'invoices' => $invoices,
            'waitingInvoices' => $waitingInvoices,
            'reports' => $reports,
        ));
    }

    public function statusEditAction(Command $command, Request $request)
    {
        $newStatus = $request->request->get('new_status');
        $command->setStatus($newStatus);

        $em = $this->getDoctrine()->getManager();
        $em->merge($command);
        $em->flush();
        return $this->redirectToRoute('backoffice_command_show', array(
            'id' => $command->getId()
        ));
    }

    public function editAction(Command $command, Request $request)
    {
        foreach ($command->getCartCollection()->getBrand()->getPaymentMethods() as $paymentMethod) {
            $paymentMethodIds[] = $paymentMethod->getId();
        }

        $paymentTermsIds = new ArrayCollection();
        foreach ($command->getCartCollection()->getBrand()->getPaymentTerms() as $paymentTerm) {
            $paymentTermsIds[] = $paymentTerm->getId();
        }

        $form = $this->createFormBuilder(array('command' => $command))
            ->add('command', CommandEditType::class, array('paymentMethodIds' => $paymentMethodIds,
                'paymentTermsIds' => $paymentTermsIds))
            ->getForm();

        //$form = $this->createForm('B2bBundle\Form\CommandType', $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command->updateTotalHT();
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('command_index', array('id' => $command->getId()));
        }

        return $this->render('command/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function validateAction(Command $command, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $command->setStatus(Command::STATUS_VALIDATED);
        $customer = $command->getShop()->getCustomer();
        $mail = $customer->getMail();

        $message = (new \Swift_Message('B2B - Demande de création de compte'))
            ->setFrom('testb2b@french-select.com')
            ->setTo($mail)
            ->setBody($this->renderView('B2bBundle:Mails:validate_commande.html.twig', array(
                'user' => $customer,
                'command' => $command,
            )), 'text/html');
        $this->get('mailer')->send($message);

        $em->persist($command);
        $em->flush();

        $this->addFlash('success', "Commande validée ! ");
        return $this->indexForBrand($request);
    }

    public function cancelAction(Command $command, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $command->setStatus(Command::STATUS_CANCELED);

        $em->persist($command);
        $em->flush();

        return $this->indexForBrand($request);
    }

    public function selectCustomerAction()
    {
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository('B2bBundle:Brand')->find($this->getUser()->getId());

        return $this->render('command/brand-new.html.twig', array(
            'brand' => $brand,
        ));
    }

    /**
     * Search a shop according to its client id, name, societyName with the search-bar
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function searchShopAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $brand = $em->getRepository('B2bBundle:Brand')->find($this->getUser()->getId());
            if($brand) {
                $shops = $em->getRepository('B2bBundle:Shop')->searchForBrand($request->get('search_text'), $brand);
            }
            else{
                $salesman= $em->getRepository('B2bBundle:Salesman')->find($this->getUser()->getId());
                $shops = $em->getRepository('B2bBundle:Shop')->searchForSalesman($request->get('search_text'), $salesman);
            }

            return $this->render('shop/shoplist.html.twig', array('shops' => $shops, 'checkbox' => true));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    /**
     * Search a command not completely paid according to its customer
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function searchCommandAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $customer = $em->getRepository('B2bBundle:Customer')->find($request->get('customer_id'));
            $commands = $em->getRepository('B2bBundle:Command')->searchNotPaidForCustomer($customer);
            return $this->render('command/commandlist.html.twig', array('commands' => $commands, 'checkbox' => true));
        }
        return new Response("Error : not an Ajax call, 400");
    }
}
