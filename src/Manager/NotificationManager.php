<?php

/*
 * This file is part of the Kuraq project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Developed by Hello Pomelo <contact@hello-pomelo.com>
 *
 */

namespace App\Manager;

use App\Entity\Demande;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class NotificationManager
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var Security $security */
    private $security;

    /**
     * @param EntityManagerInterface $em
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    // Used in twig
    public function checkNewDemande() {
        /** @var User $user */
        $user = $this->security->getUser();
        $demandes = $this->em->getRepository(Demande::Class)->getNewDemandeFor($user);
        return count($demandes);
    }
}
