<?php

namespace App\Security;

use App\Repository\ApiTokenRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{

    /**
     * @var ApiTokenRepository
     */
    private $apiTokenRepository;

    /**
     * @var LoggerInterface
     */
    private $apiLogger;

    public function __construct(ApiTokenRepository $apiTokenRepository, LoggerInterface $apiLogger)
    {
        $this->apiTokenRepository = $apiTokenRepository;
        $this->apiLogger = $apiLogger;
    }

    public function supports(Request $request)
    {
        return $request->headers->has('Authorization') && 0 === strpos(
                $request->headers->get('Authorization'),
                'Bearer '
            );
    }

    public function getCredentials(Request $request)
    {
        return substr( $request->headers->get('Authorization'), 7);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $this->apiTokenRepository->findOneBy(['token' => $credentials]);

        if (! $token) {
            throw new CustomUserMessageAuthenticationException('Invalid token');
        }

        if ($token->isExpired()) {
            throw new CustomUserMessageAuthenticationException('Token expired');
        }

        if (! $token->isActiveUser()) {
            throw new CustomUserMessageAuthenticationException('User is inactive');
        }

        return $token->getUser();
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'message' => $exception->getMessage(),
        ], 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $this->apiLogger->info( '' .
            'User: ' . $token->getUser()->getEmail() . '; ' .
            'Token: ' . $this->getCredentials($request) . '; ' .
            'Route:' . $request->getPathInfo() . '; ' .
            'URL:' . $request->getUri()
        );
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        throw new Exception('Never called!');
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
