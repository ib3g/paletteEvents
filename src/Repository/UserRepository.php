<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Env\Request;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }
// get users with role ROLE_ANIMATEUR
    public function findAnimateurs()
    {
        return $this->createQueryBuilder('u')
            ->join('u.role', 'r')
            ->andWhere('r.role = :role')
            ->setParameter('role', 'ROLE_ANIMATEUR')
            ->getQuery()
            ->getResult();
    }
    public function update(User $user, $data,$role){
        $user->setFullName($data->getFullName());
        $user->setEmail($data->getEmail());
        $user->setProfession($data->getProfession());
        $user->setCentreInteret($data->getCentreInteret());
        $user->setRole($role);
        $user->setBirthday($data->getBirthday());
    }

    public function findByEvent(Event $event) {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.tickets', 'tickets')
            ->leftJoin('tickets.prix', 'prix')
            ->leftJoin('u.animatedEvents', 'animatedEvents')
            ->andWhere('prix.event = :event OR animatedEvents.id = :event')
            ->setParameter('event', $event->getId())
            ->getQuery()
            ->getResult();
    }

    public function findAllUsers()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // get user by role admin
    public function findAdmin()
    {
        return $this->createQueryBuilder('u')
            ->join('u.role', 'r')
            ->andWhere('r.role = :role')
            ->setParameter('role', 'ROLE_ADMIN')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
