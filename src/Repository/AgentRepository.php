<?php

namespace App\Repository;

use App\Entity\Agent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Agent>
 *
 * @method Agent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agent[]    findAll()
 * @method Agent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agent::class);
    }

    public function add(Agent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Agent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAll(){

        return $this->createQueryBuilder('u')
            ->select('u')
            ->getQuery()
            ;
    }

    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.id = :id')
            ->orwhere('u.first_name LIKE :first_name')
            ->orwhere('u.last_name LIKE :last_name')
            ->orwhere('u.email LIKE :email')
            ->orwhere('u.birth_date LIKE :birth_date')
            ->orwhere('u.phone LIKE :phone')
            ->orwhere('u.mobil LIKE :mobil')
            ->orwhere('u.social LIKE :social')
            ->orwhere('u.street LIKE :street')
            ->orwhere('u.city LIKE :city')
            ->orwhere('u.zip_code LIKE :zip_code')
            ->orwhere('u.license LIKE :license')
            ->orwhere('u.npn LIKE :npn')
            ->orwhere('u.phone_ext LIKE :phone_ext')
            ->setParameter('id', $value)
            ->setParameter('first_name', '%'.$value.'%')
            ->setParameter('last_name', '%'.$value.'%')
            ->setParameter('email', '%'.$value.'%')
            ->setParameter('birth_date', '%'.$value.'%')
            ->setParameter('phone', '%'.$value.'%')
            ->setParameter('mobil', '%'.$value.'%')
            ->setParameter('social', '%'.$value.'%')
            ->setParameter('street', '%'.$value.'%')
            ->setParameter('city', '%'.$value.'%')
            ->setParameter('zip_code', '%'.$value.'%')
            ->setParameter('license', '%'.$value.'%')
            ->setParameter('npn', '%'.$value.'%')
            ->setParameter('phone_ext', '%'.$value.'%')
            ->getQuery()
            ;
    }

//    /**
//     * @return Agent[] Returns an array of Agent objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Agent
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
