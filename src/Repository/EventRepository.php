<?php

namespace App\Repository;

use App\Entity\Event;
use App\Helper\FormSearchHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    final public function querySearch(FormSearchHelper $formSearch, bool $count = false): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e');

        $subQuery = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(o.id)')
            ->from('App\Entity\Order', 'o')
            ->where('o.event = e');

        if ($formSearch->getSortParams()) {
            $qb->orderBy($formSearch->getSortValue(), $formSearch->getSortDir());
        } else {
            $qb->orderBy('e.name', 'ASC');
        }

        if ($formSearch->get('name')) {
            $qb
                ->andWhere('e.name LIKE :name')
                ->setParameter('name', "%{$formSearch->get('name')}%");
        }

        if ($formSearch->get('date')) {
            $qb
                ->andWhere('e.date = :date')
                ->setParameter('date', $formSearch->get('date'));
        }

        $qb->andWhere('e.online = true');

        $qb->andWhere('e.date >= :today')
            ->setParameter('today', new \DateTimeImmutable('today'));

        $qb->andWhere(sprintf('e.quantity > (%s)', $subQuery->getDQL()));

        if ($count) {
            $qb->select('COUNT(e.id)');
        } else {
            $qb->setMaxResults(20);
        }

        return $qb;
    }

    public function findTopViewedEvents(int $limit = 3): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.clicks', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
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
