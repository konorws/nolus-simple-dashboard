<?php

namespace App\Action;

use App\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractAction extends AbstractController
{

    public function getUser(): User
    {
        return parent::getUser();
    }
}