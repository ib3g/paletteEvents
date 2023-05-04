<?php

namespace App\Repository;

use App\Entity\Demande;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Demande>
 *
 * @method Demande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Demande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Demande[]    findAll()
 * @method Demande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demande::class);
    }

    public function save(Demande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Demande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getNewDemandeFor(User $user) {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.event', 'e')
            ->andWhere('e.owner = :user')
            ->andWhere('d.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', Demande::STATUS_PENDING);

        return $qb->getQuery()->getResult();
    }

    public function allDemandeByEventOwner($owner): array
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.event', 'e')
            ->leftJoin('e.owner', 'owner')
            ->where('owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('e.status', 'ASC')
            ->getQuery();

        return $qb->getResult();
    }

//    /**
//     * @return Demande[] Returns an array of Demande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Demande
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
