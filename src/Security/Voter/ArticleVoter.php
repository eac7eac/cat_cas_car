<?php

namespace App\Security\Voter;

use App\Entity\Article;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleVoter extends Voter
{
    public const MANAGE = 'MANAGE';
    public const API_MANAGE = 'API_MANAGE';

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::MANAGE, self::API_MANAGE])
            && $subject instanceof Article;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var Article $subject */

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'MANAGE':
                if ($subject->getAuthor() == $user) {
                    return true;
                }
                if ($this->security->isGranted('ROLE_ADMIN_ARTICLE')) {
                    return true;
                }
                break;

            case 'API_MANAGE':
                if ($subject->getAuthor() == $user) {
                    return true;
                }
                if ($this->security->isGranted('ROLE_API')) {
                    return true;
                }
                break;
        }

        return false;
    }
}
