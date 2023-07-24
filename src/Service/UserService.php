<?php

namespace App\Service;

use App\Entity\User;
use App\Events\UserRegisteredEvent;
use App\Form\Model\UserRegistrationFormModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param UserRegistrationFormModel $userModel
     * @return object
     */
    public function userRegister(UserRegistrationFormModel $userModel): object
    {
        $user = new User();

        $user
            ->setEmail($userModel->email)
            ->setFirstName($userModel->firstName)
            ->setPassword($this->passwordEncoder->encodePassword($user, is_string($userModel->plainPassword)))
            ->setIsActive(true)
        ;

        $this->em->persist($user);
        $this->em->flush();

        $this->dispatcher->dispatch(new UserRegisteredEvent($user));

        return $user;
    }
}