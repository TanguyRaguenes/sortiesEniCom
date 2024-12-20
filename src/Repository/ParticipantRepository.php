<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Participant>
 */
class ParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }
    public function findAllParticipants(): array
    {
        $participants = $this->findAll();
        $result = [];

        foreach ($participants as $participant) {
            $result[$participant->getId()] = [
                'name' => $participant->getName(),
                'firstname' => $participant->getFirstname(),
                'username' => $participant->getUsername(),
                'phone' => $participant->getPhone(),
            ];
        }
        return $result;
    }

    public function findOneByEmail(String $email): ?Participant
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    public function findAllParticipants()
//    {
//        return $this->findAll();
//    }

//        /**
//         * @return Participant[] Returns an array of Participant objects
//         */
//        public function findByExampleField($value): array
//        {
//            return $this->createQueryBuilder('p')
//                ->andWhere('p.exampleField = :val')
//                ->setParameter('val', $value)
//                ->orderBy('p.id', 'ASC')
//                ->setMaxResults(10)
//                ->getQuery()
//                ->getResult()
//            ;
//        }

    //    public function findOneBySomeField($value): ?Participant
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
