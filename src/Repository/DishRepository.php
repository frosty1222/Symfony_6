<?php

namespace App\Repository;

use App\Entity\Dish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
/**
 * @extends ServiceEntityRepository<Dish>
 *
 * @method Dish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dish[]    findAll()
 * @method Dish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dish::class);
    }

    public function add(Dish $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Dish $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findAllData($page,$perPage,$start, $end,$value,Request $request): array
    {
        //  dd($page);
        $name = $request->query->get('search');
        // dd($name);
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC');
        if($name){
            $qb = $this->createQueryBuilder('p')
            ->where('p.name = :name')
            ->setParameter('name', $name);
        }
        if(is_numeric($name)){
            $qb = $this->createQueryBuilder('p')
            ->where('p.price = :name')
            ->setParameter('name', $name);
        }
         $query = $qb->getQuery();
         $max = round(count($query->execute())) / $perPage;
          if($page){
             $dataStart = $query->setFirstResult($page * $perPage)
            ->setMaxResults($perPage);
             return $query->execute();
          }else if($page == 0){
            $dataStart = $query
            ->setMaxResults($perPage);
             return $query->execute();
          }
            
    }
//    /**
//     * @return Dish[] Returns an array of Dish objects
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

   public function findOneById($value): ?Dish
   {
       return $this->createQueryBuilder('d')
           ->where('d.id = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult();
   }
}
