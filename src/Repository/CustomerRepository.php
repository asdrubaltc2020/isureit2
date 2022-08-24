<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function add(Customer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Customer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAll(){

        return $this->createQueryBuilder('u')
            ->select('u,s,a')
            ->join('u.state','s')
            ->join('u.birth_state','bs')
            ->join('u.agent','a')
            ->getQuery()
            ;
    }

    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->select('u, s')
            ->join('u.state','s')
            ->join('u.agent','a')
            ->join('u.birth_state','bs')
            ->where('u.id = :id')
            ->orwhere('u.first_name LIKE :first_name')
            ->orwhere('u.last_name LIKE :last_name')
            ->orwhere('u.email LIKE :email')
            ->orwhere('u.birth_date LIKE :birth_date')
            ->orwhere('u.phone LIKE :phone')
            ->orwhere('u.mobile LIKE :mobil')
            ->orwhere('u.social LIKE :social')
            ->orwhere('u.street LIKE :street')
            ->orwhere('u.city LIKE :city')
            ->orwhere('s.name LIKE :state')
            ->orwhere('u.zip_code LIKE :zip_code')
            ->orwhere('u.annual_income LIKE :annual_income')
            ->orwhere('a.first_name LIKE :agent')
            ->orwhere('a.last_name LIKE :agent')
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
            ->setParameter('state', '%'.$value.'%')
            ->setParameter('zip_code', '%'.$value.'%')
            ->setParameter('agent', '%'.$value.'%')
            ->setParameter('annual_income', '%'.$value.'%')
            ->getQuery()
            ;
    }

//    /**
//     * @return Customer[] Returns an array of Customer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Customer
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
