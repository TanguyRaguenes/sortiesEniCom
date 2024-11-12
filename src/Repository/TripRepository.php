<?php

namespace App\Repository;

use App\Entity\Trip;
use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    /**
     * Trouve les voyages créés par un organisateur spécifique.
     *
     * @param Participant $user
     * @param array $criteria
     * @return Trip[]
     */
    public function findTripsByOrganizer(Participant $user, array $criteria = []): array
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.organizer = :user')
            ->setParameter('user', $user);

        if (isset($criteria['campus'])) {
            $qb->andWhere('t.campus = :campus')
               ->setParameter('campus', $criteria['campus']);
        }
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les voyages auxquels un utilisateur participe.
     *
     * @param Participant $user
     * @param array $criteria
     * @return Trip[]
     */
    public function findTripsByParticipant(Participant $user, array $criteria = []): array
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.participants', 'p')
            ->where('p = :user')
            ->setParameter('user', $user);

        if (isset($criteria['campus'])) {
            $qb->andWhere('t.campus = :campus')
               ->setParameter('campus', $criteria['campus']);
        }
        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Trip[] Returns an array of Trip objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Trip
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
