<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\ShippingAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

     /**
      * @return Customer[] Returns an array of Customer objects
      */

    public function findOrders($parameters)
    {
        extract($parameters);

        $query = $this->createQueryBuilder('c');


        if(isset($filter)){
            $filters = json_decode($filter, true);

            foreach ($filters as $filter=>$value){

               if($filter == 'orderDate' || $filter == 'feedbackDate' ){
                   $dates = explode('-', $value);
                   $from = new \DateTime(trim($dates[0]));
                   $to = new \DateTime(trim($dates[1]));
                   $to->modify('+1 day');
                   $query->andWhere("c.{$filter} BETWEEN :from AND :to")
                       ->setParameter('from', $from )
                       ->setParameter('to', $to);
               }else{
                   $query->andWhere("c.{$filter} = '{$value}'");
               }
            }
        }
        if(isset($sort) && isset($order) &&  $sort == 'product'){
            $query->join('c.product', 'cp');
            $query->orderBy('cp.name', $order);
        }
        elseif (isset($sort) && isset($order)){
            $query->orderBy('c.'.$sort, $order);
        }else{
            $query->orderBy('c.id', 'asc');
        }
        $total = count($query->getQuery()->getResult());

        if(isset($limit)){
            $query->setMaxResults($limit);
        }

        if(isset($offset)){
            $query->setFirstResult( $offset );
        }

        return ['rows' => $query->getQuery()->getResult(), 'total' => $total ];
    }

    public function hasByAddress(ShippingAddress $shippingAddress)
    {
        $query = $this->createQueryBuilder('c');

        $query
            ->select('count(c.id)')
            ->andWhere('c.shipping_info.city = :city')
            ->andWhere('c.shipping_info.street = :street')
            ->andWhere('c.shipping_info.apt = :apt')
            ->andWhere('c.isRequested = true')
            ->setParameter('city', $shippingAddress->city)
            ->setParameter('street', $shippingAddress->street)
            ->setParameter('apt', $shippingAddress->apt);

        return $query->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByEmail($email)
    {
        $query = $this->createQueryBuilder('c');

        $query->select('count(c.id)');

        $query
            ->andWhere('c.email = :email')
            ->andWhere('c.isRequested = true')
            ->setParameter('email', $email);

        return $query->getQuery()->getSingleScalarResult()>0;
    }

    public function hasByOrderId($orderId)
    {
        $query = $this->createQueryBuilder('c');

        $query->select('count(c.id)');

        $query
            ->andWhere('c.orderId = :orderId')
            ->andWhere('c.isRequested = true')
            ->setParameter('orderId', $orderId);

        return $query->getQuery()->getSingleScalarResult()>0;
    }


    /*
    public function findOneBySomeField($value): ?Customer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
