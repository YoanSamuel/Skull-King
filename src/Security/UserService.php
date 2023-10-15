<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class UserService
{


    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getUser(): User
    {

        /** @var User $authenticated */
        $authenticated = $this->security->getUser();
        return $authenticated;
    }
}