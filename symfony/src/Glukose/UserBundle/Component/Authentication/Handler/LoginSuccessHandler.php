<?php

namespace Glukose\UserBundle\Component\Authentication\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{

	protected $router;
	protected $security;

	public function __construct(Router $router, SecurityContext $security, Session $session)
	{
		$this->router = $router;
		$this->security = $security;
		$this->session = $session;
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token)
	{
		if ($this->security->isGranted('ROLE_SUPER_ADMIN'))
		{
			$response = new RedirectResponse($this->router->generate('sonata_admin_dashboard'));

		} 
		elseif ($this->security->isGranted('ROLE_USER'))
		{
			if($this->session->has('redirectResponse')){

				//$response = new RedirectResponse($this->router->generate('glukose_ecommerce_panier_etape2'));	
			} 
			else {
				$response = new RedirectResponse($this->router->generate('sonata_admin_dashboard'));
				/*$referer_url = $request->headers->get('referer');
				$response = new RedirectResponse($referer_url);*/
			}

		}

		return $response;
	}

}