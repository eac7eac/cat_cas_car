<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class ApiLogger
{
    /**
     * @var LoggerInterface
     */
    private $apiLogger;

    public function __construct(LoggerInterface $apiLogger)
    {
        $this->apiLogger = $apiLogger;
    }

    public function apiWarning()
    {
        return $this->apiLogger->warning('На страницу зашел пользователь с ролью, отличной от ROLE_API');
    }

//    public function apiUserAuthentication(string $userName = null, string $userToken = null, string $routeName = null, string $requestURL = null)
//    {
//        return $this->apiLogger->info(sprintf(
//            'Авторизованный ользователь: %s, токен пользователя: %s, имя испольняемого маршрута: %s, URL запроса: %s',
//            $userName,
//            $userToken,
//            $routeName,
//            $requestURL));
//    }
}