<?php

namespace B2bBundle\Repository;

use Doctrine\ORM\EntityRepository;

class DefectiveProductReportRepository extends EntityRepository {
    public function findForCustomer($customer) {
        $QueryBuilder = $this->createQueryBuilder('r')
            ->innerJoin('r.command', 'c')
            ->innerJoin('c.cartCollection', 'cart')
            ->where('cart.customer = :customer_id')
            ->setParameters(array('customer_id' => $customer))
            ->orderBy('r.date', 'DESC');
        return $QueryBuilder->getQuery()->getResult();
    }

    public function findForBrand($brand) {
        $QueryBuilder = $this->createQueryBuilder('r')
            ->innerJoin('r.command', 'c')
            ->innerJoin('c.cartCollection', 'cart')
            ->where('cart.brand = :brand_id')
            ->setParameters(array('brand_id' => $brand))
            ->orderBy('r.date', 'DESC');
        return $QueryBuilder->getQuery()->getResult();
    }
}