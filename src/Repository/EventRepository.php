<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function save(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // query builder returns last 4 events
        public function findLastEvents($max): array
        {
            return $this->createQueryBuilder('e')
                ->orderBy('e.id', 'DESC')
                ->setMaxResults($max)
                ->getQuery()
                ->getResult()
            ;
        }
    public function findEventsWithSharedCategories(Event $event, int $max): array
    {
        $categoryArray = $event->getCategories(); // Get the category array of the event passed in parameter
        $qb = $this->createQueryBuilder('e')
            ->where(':categoryArray MEMBER OF e.categories') // Check if any of the categories of an event match any of the categories of the event passed in parameter
            ->andWhere('e.id != :id') // Exclude the event passed in parameter
            ->setParameter('categoryArray', $categoryArray)
            ->setParameter('id', $event->getId())
            ->orderBy('e.id', 'DESC')
            ->setMaxResults($max)
            ->getQuery();

        return $qb->getResult();
    }

//    /**
//     * @return Event[] Returns an array of Event objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
