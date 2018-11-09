<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function authenticate(): Response
    {
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
