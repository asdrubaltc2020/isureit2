<?php

namespace App\Service;

use App\Entity\Log;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

class LogService
{
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function add($action, $parentId, $parentClass){

        $user = $this->security->getUser();

        $log=new Log();
        $log->setAction($action);
        $log->setParentId($parentId);
        $log->setParentClass($parentClass);
        $log->setUser($user);

        $this->em->persist($log);
        $this->em->flush();
    }


}