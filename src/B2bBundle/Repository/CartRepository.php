<?php

namespace B2bBundle\Repository;

/**
 * CartRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CartRepository extends \Doctrine\ORM\EntityRepository {

    public function findFor($brand, $customer) {
        $QueryBuilder = $this->createQueryBuilder('c')
            ->where('c.customer = :customer_id')
            ->andWhere('c.brand = :brand_id')
            //->andWhere('not exists (select co from B2bBundle:Command co where co.cart = c and co.status <> \'created\')')
            ->setParameters(array('customer_id' => $customer, 'brand_id' => $brand));
        return $QueryBuilder->getQuery()->getResult();
    }

    public function findDifferentFrom($brand, $customer) {
        $QueryBuilder = $this->createQueryBuilder('c')
            ->where('c.customer = :customer_id')
            ->andWhere('c.brand <> :brand_id')
            //->andWhere('not exists (select co from B2bBundle:Command co where co.cart = c and co.status <> \'created\')')
            ->setParameters(array('customer_id' => $customer, 'brand_id' => $brand));
        return $QueryBuilder->getQuery()->getResult();
    }

    public function findCarts($customer) {
        $QueryBuilder = $this->createQueryBuilder('c')
            ->innerJoin('c.cartCollections', 'r')
            ->where('c.customer = :customer_id')
            //->andWhere('not exists (select co from B2bBundle:Command co where co.cart = c and co.status <> \'created\')')
            ->having('count(r.id) > 0')
            ->setParameters(array('customer_id' => $customer));
        return $QueryBuilder->getQuery()->getResult();
    }
}