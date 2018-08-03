<?php

namespace B2bBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface {

    public function handle(Request $request, AccessDeniedException $accessDeniedException) {
        return new Response('', 403);
        //return $this->redirectToRoute('b2b_homepage');
    }
}