<?php

namespace B2bBundle\Controller;

use B2bBundle\B2bBundle;
use B2bBundle\Entity\ContactBrand;
use B2bBundle\Entity\ContactCustomer;
use B2bBundle\Entity\Customer;
use B2bBundle\Entity\Country;
use B2bBundle\Entity\Salesman;
use B2bBundle\Entity\Shop;
use B2bBundle\Entity\User;
use B2bBundle\Form\MyFileType;
use Proxies\__CG__\B2bBundle\Entity\StatusContactCustomer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller
{

    const CRYPT = "jemappellemichel";

    private function redirectAfterLogin()
    {

        $user = $this->getUser();
        if (get_class($user) == "B2bBundle\Entity\Brand") {
            $em = $this->getDoctrine()->getManager();
            $user->clearConnectedFor();
            $em->flush();
        }
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('b2b_backoffice_index');
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            return $this->redirectToRoute('brands_index');
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_CUSTOMER')) {
            return $this->redirectToRoute('customers_index');
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            return $this->redirectToRoute('brands_index');
        }
        
        return $this->redirectToRoute('account_infos');
    }

    public function createNewForm(Request $request)
    {

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('account_new'))
            ->add('societyName', TextType::class, array('attr' => array('placeholder' => 'Société')))
            ->add('shopName', TextType::class, array('attr' => array('placeholder' => 'Nom de la boutique')))
            ->add('name', TextType::class, array('attr' => array('placeholder' => 'Nom')))
            ->add('firstname', TextType::class, array('attr' => array('placeholder' => 'Prénom')))
            ->add('mail', EmailType::class, array('attr' => array('placeholder' => 'Adresse mail')))
            ->add('phone', TelType::class, array('attr' => array('placeholder' => 'Numéro de téléphone')))
            ->add('billingAddress', null, array(
                'label' => 'Adresse de facturation',
            ))
            ->add('billingZP', null, array(
                'label' => 'Code Postal',
            ))
            ->add('billingTown', null, array(
                'label' => 'Ville',
            ))
            ->add('billingCountry', EntityType::class, array(
                'label' => 'Pays de facturation',
                'class' => Country::class,
                'choice_label' => 'name',
                'preferred_choices' => function ($value, $key) {
                    if ($value == "France") return $value;
                },
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() /*&& $form->isValid()*/) {
            $em = $this->getDoctrine()->getManager();
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();

            $mail = $data['mail'];
            $username = $data['mail'];
            if ($em->getRepository('B2bBundle:User')->findBy(array('username' => $username)) ||
                $em->getRepository('B2bBundle:User')->findBy(array('mail' => $mail))) {
                $this->addFlash('danger', 'Un utilisateur existe déjà pour cette adresse mail ou cette société');
                return null;
            }

            $customer = new Customer();
            $customer->setInactive();
            $customer->setUsername($username);
            $customer->setPassword(sha1(SecurityController::CRYPT));

            $customer->setCompanyName($data['societyName']);
            $customer->setPhone($data['phone']);
            $customer->setMail($mail);
            $customer->setBillingAddress($data['billingAddress']);
            $customer->setBillingZP($data['billingZP']);
            $customer->setBillingTown($data['billingTown']);
            $customer->setBillingCountry(($data['billingCountry']));

            $function = $em->getRepository('B2bBundle:StatusContactCustomer')->findOneBy(array('id'=>2));
            $contact = new ContactCustomer();
            $contact->setFirstname($data['firstname']);
            $contact->setLastname($data['name']);
            $contact->setFunction($function);
            $customer->addContact($contact);

            $shop = new Shop();
            $shop->setAddress($data['billingAddress']);
            $shop->setZipCode($data['billingZP']);
            $shop->setTown($data['billingTown']);
            $shop->setCountry($data['billingCountry']);
            $shop->setName($data['shopName']);
            $shop->setPhone($data['phone']);
            $shop->setDeliverySameAddress(true);
            $shop->setInactive();
            $customer->addShop($shop);


            $em->persist($shop);
            $em->persist($customer);
            $em->flush();

            $admins = $em->getRepository('B2bBundle:Admin')->findAll();
            $mails = [];
            foreach ($admins as $admin) {
                $mails[] = $admin->getMail();
            }

            $message = (new \Swift_Message('Demande de création de compte client'))
                ->setFrom('testb2b@french-select.com')
                ->setTo($mails)
                ->setBody($this->renderView('B2bBundle:Mails:creation_boutique_admin.html.twig', array(
                    'user' => $customer,
                )), 'text/html');
            $this->get('mailer')->send($message);

            $message = (new \Swift_Message('B2B - Demande de création de compte'))
                ->setFrom('testb2b@french-select.com')
                ->setTo($mail)
                ->setBody($this->renderView('B2bBundle:Mails:creation_boutique_customer.html.twig', array(
                    'user' => $customer,
                )), 'text/html');
            $this->get('mailer')->send($message);
        }
        return $form;
    }

    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        // Si le visiteur est déjà identifié, on le redirige vers l'accueil
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectAfterLogin();
        }

        $authUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();


        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }


    public function accountCreationAction(Request $request)
    {
        $new_form = $this->createNewForm($request);
        $em = $this->getDoctrine()->getManager();

        $formDemandeMarque = $this->createForm('B2bBundle\Form\DemandeMarqueType');
        $formDemandeMarque->handleRequest($request);


        $formDemandeCommercial = $this->createForm('B2bBundle\Form\DemandeCommercialType');
        $formDemandeCommercial->handleRequest($request);

        $formDemandeAutre = $this->createForm('B2bBundle\Form\DemandeAutreType');
        $formDemandeAutre->handleRequest($request);

        $admins = $em->getRepository('B2bBundle:Admin')->findAll();
        $mails = [];
        foreach ($admins as $admin) {
            $mails[] = $admin->getMail();
        }

        if ((/*($formDemande->isValid()) &&*/
        ($formDemandeMarque->isSubmitted()))) {
            $message = (new \Swift_Message('French Select - Nouvelle demande d\'une marque'))
                ->setFrom('testb2b@french-select.com')
                ->setTo($mails)
                ->setBody($this->renderView('B2bBundle:Mails:demande_autre_admin.html.twig', array(
                    'nom' => $formDemandeMarque->get('name')->getData(),
                    'prenom' => $formDemandeMarque->get('firstname')->getData(),
                    'mail' => $formDemandeMarque->get('mail')->getData(),
                    'tel' => $formDemandeMarque->get('phone')->getData(),
                    'text' => $formDemandeMarque->get('text')->getData(),
                )), 'text/html');
            $this->get('mailer')->send($message);

            $message = (new \Swift_Message('French Select - Demande envoyée'))
                ->setFrom('testb2b@french-select.com')
                ->setTo($formDemandeMarque->get('mail')->getData())
                ->setBody($this->renderView('B2bBundle:Mails:demande_autre_client.html.twig'),
                    'text/html');
            $this->get('mailer')->send($message);

            return $this->render('security/confirmation_demande.html.twig');
        }

        if ((/*($formDemande->isValid()) &&*/
        ($formDemandeCommercial->isSubmitted()))) {
            $message = (new \Swift_Message('French Select - Nouvelle demande d\'un commercial'))
                ->setFrom('testb2b@french-select.com')
                ->setTo($mails)
                ->setBody($this->renderView('B2bBundle:Mails:demande_autre_admin.html.twig', array(
                    'nom' => $formDemandeCommercial->get('name')->getData(),
                    'prenom' => $formDemandeCommercial->get('firstname')->getData(),
                    'mail' => $formDemandeCommercial->get('mail')->getData(),
                    'tel' => $formDemandeCommercial->get('phone')->getData(),
                    'text' => $formDemandeCommercial->get('text')->getData(),
                )), 'text/html');
            $this->get('mailer')->send($message);

            $message = (new \Swift_Message('French Select - Demande envoyée'))
                ->setFrom('testb2b@french-select.com')
                ->setTo($formDemandeCommercial->get('mail')->getData())
                ->setBody($this->renderView('B2bBundle:Mails:demande_autre_client.html.twig'),
                    'text/html');
            $this->get('mailer')->send($message);

            return $this->render('security/confirmation_demande.html.twig');
        }

        if ((/*($formDemande->isValid()) &&*/
        ($formDemandeAutre->isSubmitted()))) {
            $message = (new \Swift_Message('French Select - Nouvelle demande d\'une autre personne '))
                ->setFrom('testb2b@french-select.com')
                ->setTo($mails)
                ->setBody($this->renderView('B2bBundle:Mails:demande_autre_admin.html.twig', array(
                    'nom' => $formDemandeAutre->get('name')->getData(),
                    'prenom' => $formDemandeAutre->get('firstname')->getData(),
                    'mail' => $formDemandeAutre->get('mail')->getData(),
                    'tel' => $formDemandeAutre->get('phone')->getData(),
                    'text' => $formDemandeAutre->get('text')->getData(),
                )), 'text/html');
            $this->get('mailer')->send($message);

            $message = (new \Swift_Message('French Select - Demande envoyée'))
                ->setFrom('testb2b@french-select.com')
                ->setTo($formDemandeAutre->get('mail')->getData())
                ->setBody($this->renderView('B2bBundle:Mails:demande_autre_client.html.twig'),
                    'text/html');
            $this->get('mailer')->send($message);

            return $this->render('security/confirmation_demande.html.twig');
        }

        return $this->render('security/inscription.html.twig', array(
            'new_form' => $new_form->createView(),
            'formDemandeMarque' => $formDemandeMarque->createView(),
            'formDemandeCommercial' => $formDemandeCommercial->createView(),
            'formDemandeAutre' => $formDemandeAutre->createView(),
        ));
    }


    //// https://symfony.com/doc/current/security/remember_me.html

    public function accountAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $customer = null;
        $admin = null;
        $brand = null;
        $salesman = null;
        $brandRecommande = null;
        $brandUniversCalcul = null;

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $admin = $em->getRepository('B2bBundle:Admin')->find($user->getId());
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_CUSTOMER')) {
            $customer = $em->getRepository('B2bBundle:Customer')->find($user->getId());
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $brandRecommande = $em->getRepository('B2bBundle:BrandRecommande')->findByBrand($brand);
            $brand = $em->getRepository('B2bBundle:Brand')->find($user->getId());
            $brandUniversCalcul = $em->getRepository('B2bBundle:BrandUniversCalcul')->findByBrand($brand);
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            $salesman = $em->getRepository('B2bBundle:Salesman')->find($user->getId());
        }

        return $this->render('security/account.html.twig', array(
            'user' => $user,
            'customer' => $customer,
            'admin' => $admin,
            'brand' => $brand,
            'salesman' => $salesman,
            'brandRecommande' => $brandRecommande,
            'brandUniversCalcul' => $brandUniversCalcul,
        ));
    }

    public function editAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $editForm = null;

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $admin = $em->getRepository('B2bBundle:Admin')->find($user->getId());
            $editForm = $this->createForm('B2bBundle\Form\AdminType', $admin, array('editPassword' => true));
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_CUSTOMER')) {
            $customer = $em->getRepository('B2bBundle:Customer')->find($user->getId());
            $editForm = $this->createForm('B2bBundle\Form\CustomerType', $customer, array('editPassword' => true));
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $brand = $em->getRepository('B2bBundle:Brand')->find($user->getId());
            $editForm = $this->createForm('B2bBundle\Form\BrandEditType', $brand, array('editPassword' => true));
            $editForm->get('CGV')->setData($brand->getCGV());
            $editForm->add('CGV', MyFileType::class, array(
                'label' => 'Conditions Générales de Vente', 'data_class' => 'B2bBundle\Entity\MyFile', 'required' => false
            ));

        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {
            $salesman = $em->getRepository('B2bBundle:Salesman')->find($user->getId());
            $editForm = $this->createForm('B2bBundle\Form\SalesmanType', $salesman, array('editPassword' => true));
        }
        $editForm->handleRequest($request);
        $canValidate = true;
        $validateMessage = '<p>Impossible de valider les informations pour les raisons suivantes :</p><ul>';

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if (isset($customer)) {
                $customer = $editForm->getData();
                foreach ($customer->getShops() as $shop) {
                    if (!ShopController::saveShopLocation($shop)) {
                        $canValidate = false;
                        $validateMessage .= '<li>l\'adresse de la boutique ' . $shop . ' n\'existe pas</li>';
                    }
                }
            }
            if ($canValidate) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('account_infos');
            }
        }

        return $this->render('security/edit.html.twig', array(
            'edit_form' => $editForm->createView(),
            'canValidate' => $canValidate,
            'validateMessage' => $validateMessage,
        ));
    }

    public function connectShopAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        /*if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            $user = $em->getRepository('B2bBundle:Brand')->find($user->getId());
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_SALESMAN')) {

        }*/
        $ids = json_decode($request->query->get('ids'), $assoc = true);

        $user->clearConnectedFor();
        $customer = null;

        foreach ($ids as $id) {
            $shop = $em->getRepository('B2bBundle:Shop')->find($id);
            $user->addConnectedForShop($shop);
            $customer = $shop->getCustomer();
            $user->setConnectedFor($customer);
        }
        $em->flush();

        if ($this->get('security.authorization_checker')->isGranted('ROLE_BRAND')) {
            return $this->forward('B2bBundle\Controller\ProductsController::productsForCustomer', array(
                'brand' => $em->getRepository('B2bBundle:Brand')->find($user->getId()),
                'customer' => $customer,
            ));
        } else {
            return $this->redirectToRoute('brands_index');
        }
    }

    public function deconnectShopAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user->clearConnectedFor();
        $em->flush();

        return $this->redirectToRoute('brands_index');
    }

    public function newAction(Request $request)
    {
        $form = $this->createNewForm($request);
        if ($form == null) {
            return $this->redirectToRoute('b2b_homepage');
        }

        return $this->render('security/new-customer.html.twig', array(
            'last_username' => ''
        ));
    }

    public static function activateAccount(User $user, Controller $controller)
    {

        if (get_class($user) == "B2bBundle\Entity\Customer") {
            $user->validate();
        }
        $user->setActive();
        $em = $controller->getDoctrine()->getManager();
        $em->flush();
        $encryptId = sha1($user->getId());

        $message = (new \Swift_Message('French Select - Demande de création de compte'))
            ->setFrom('testb2b@french-select.com')
            ->setTo($user->getMail())
            ->setBody($controller->renderView('B2bBundle:Mails:activation.html.twig', array(
                'customer' => $user,
                'encryptId' => $encryptId,
            )), 'text/html');
        $controller->get('mailer')->send($message);
    }

    public static function desactivateAccount(User $user, Controller $controller)
    {
        $user->setInactive();
        $em = $controller->getDoctrine()->getManager();
        $em->flush();
    }

    public function activateAction(User $user, Request $request)
    {
        SecurityController::activateAccount($user, $this);
        $this->addFlash('success', 'Le compte a été activé');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    public function desactivateAction(User $user, Request $request)
    {
        SecurityController::desactivateAccount($user, $this);

        $this->addFlash('success', 'Le compte a été désactivé');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    public function changePasswordAction(Request $request, String $lambda)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('B2bBundle:User')->findAll();
        foreach ($users as $occur) {
            $id = $occur->getId();
            if (sha1($id) == $lambda) {
                $user = $occur;
            }
        }

        if ($user->getPassword() == sha1(SecurityController::CRYPT)) {


            $utilisateur = $em->getRepository('B2bBundle:User')->find($user);
            $temp = new User();
            $temp->setMail($utilisateur->getMail());

            $form = $this->createForm('B2bBundle\Form\PasswordRepeated', $temp);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $utilisateur->setPassword($temp->getPassword());
                $em->persist($utilisateur);
                $em->flush();
                $this->addFlash('success', "Votre mot de passe a bien été changé ! ");
                return $this->redirectToRoute('account_login');
            }
            return $this->render('security/change_password.html.twig', array(
                'user' => $temp,
                'form' => $form->createView(),
            ));
        }

        return $this->redirectToRoute('account_login');


    }

    public function insertPasswordAction(int $id, String $password)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('B2bBundle:User')->findOneById($id);

        $user->setPassword($password);
        $em->persist($user);
        $em->flush;

        return $this->redirectToRoute('account_infos');

    }

    public function forgottenPasswordAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $temp = new User();
        $form = $this->createForm('B2bBundle\Form\ForgottenPasswordType', $temp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $em->getRepository('B2bBundle:User')->findOneByMail($temp->getMail());
            if ($user != null) {
                if ($user->getPassword() != sha1(SecurityController::CRYPT)) {
                    $this->setDefaultPasswordAction($user->getId());
                    $encryptId = sha1($user->getId());

                    $message = (new \Swift_Message('French Select - Demande de création de compte'))
                        ->setFrom('testb2b@french-select.com')
                        ->setTo($user->getMail())
                        ->setBody($this->renderView('B2bBundle:Mails:forgotten_password.html.twig', array(
                            'user' => $user,
                            'encryptId' => $encryptId,

                        )), 'text/html');
                    $this->get('mailer')->send($message);

                    return $this->render('security/reset_password.html.twig');
                } else {
                    $this->addFlash('danger', "Le mot de passe a déjà été réinitialisé ! ");

                    return $this->redirectToRoute('account_login');
                }
            } else {

                $error = true;

                return $this->render('security/forgotten_password.html.twig', array(
                    'form' => $form->createView(),
                    'error' => $error,
                ));
            }

        }

        return $this->render('security/forgotten_password.html.twig', array(
            'form' => $form->createView(),
            'error' => false,
        ));


    }

    public function setDefaultPasswordAction(int $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('B2bBundle:User')->findOneById($id);

        $user->setPassword(sha1(SecurityController::CRYPT));
        $em->persist($user);
        $em->flush();
    }

}