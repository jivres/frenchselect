<?php
/**
 * Created by PhpStorm.
 * User: sowipheur
 * Date: 18/10/2017
 * Time: 14:53
 */

namespace B2bBundle\Controller;


use B2bBundle\Entity\Access;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccessController
 */
class AccessController extends Controller {
    /**
     * Lists all access entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $accessToHandle  = $em->getRepository('B2bBundle:Access')->findBy(array('state' => Access::STATUS_SUBMITTED));
        $accessRefused   = $em->getRepository('B2bBundle:Access')->findBy(array('allowed' => false, 'state' => Access::STATUS_HANDLED));
        $accessAccepted  = $em->getRepository('B2bBundle:Access')->findBy(array('allowed' => true, 'state' => Access::STATUS_HANDLED));

        return $this->render('access/index.html.twig', array(
            'accessToHandle' => $accessToHandle,
            'accessRefused'  => $accessRefused,
            'accessAccepted' => $accessAccepted,
        ));
    }

    /**
     * Accept an Access request
     * @param Request $request
     * @return Response
     */
    public function acceptAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $access = $em->getRepository('B2bBundle:Access')->find($request->get('id'));
            $access->setReason($request->get('reason'));
            $access->setHandled();
            $access->setAllowed(true);

            $em->persist($access);
            $em->flush();
            //return $this->redirectToRoute($this->generateUrl('backoffice_access_index'));
            //return new JsonResponse(array('data' => json_encode($products)));
        }
        return new Response("Error : not an Ajax call, 400");
    }

    /**
     * Refuse an Access request
     * @param Request $request
     * @return Response
     */
    public function refuseAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $access = $em->getRepository('B2bBundle:Access')->find($request->get('id'));
            $access->setReason($request->get('reason'));
            $access->setHandled();
            $access->setAllowed(false);

            $em->persist($access);
            $em->flush();
            //return new Response();
            //return $this->redirectToRoute($this->generateUrl('backoffice_access_index'));
            //return new JsonResponse(array('data' => json_encode($products)));
        }
        return new Response("Error : not an Ajax call, 400");
    }
}