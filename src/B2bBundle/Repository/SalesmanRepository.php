<?php

namespace B2bBundle\Repository;
use B2bBundle\Entity\Salesman;
use Doctrine\ORM\EntityRepository;

/**
 * SalesmanRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SalesmanRepository extends EntityRepository {

    public function queryIn($ids) {
        $QueryBuilder = $this->createQueryBuilder('s')
            ->where('s.id IN (:ids)')
            ->setParameters(array('ids' => $ids));
        return $QueryBuilder;
    }

    public function searchForBrand($text, $brand) {
        $QueryBuilder = $this->createQueryBuilder('s')
            ->innerJoin('s.contact', 'c')
            ->leftJoin('s.brands', 'brand_link')
            ->where('s.companyName LIKE :text OR s.id LIKE :text 
            OR c.id LIKE :text OR c.lastname LIKE :text OR c.firstname LIKE :text')
            ->andWhere('(brand_link.id IS NULL OR :brand_id <> brand_link.brand)')
            ->setParameters(array('text' => '%'.$text.'%', 'brand_id' => $brand));
        return $QueryBuilder->getQuery()->getResult();
    }


    public function searchForSalesman($text, $brand, $salesman) {
        $QueryBuilder = $this->createQueryBuilder('s')
            ->innerJoin('s.contact', 'c')
            ->leftJoin('s.subordinates', 'subordinate_link')
            ->leftJoin('s.superiors', 'superior_link')
            ->where('s.companyName LIKE :text OR s.id LIKE :text 
            OR c.id LIKE :text OR c.lastname LIKE :text OR c.firstname LIKE :text')
            ->andWhere(':salesman_id <> s.id')
            ->andWhere('(superior_link.id is NULL OR (:salesman_id <> superior_link.superior AND :salesman_id <> superior_link.subordinate) OR superior_link.brand <> :brand_id)')
            ->andWhere('(subordinate_link.id is NULL OR (:salesman_id <> subordinate_link.subordinate AND :salesman_id <> subordinate_link.superior) OR subordinate_link.brand <> :brand_id)')
            ->setParameters(array('text' => '%'.$text.'%', 'brand_id' => $brand, 'salesman_id' => $salesman));
        return $QueryBuilder->getQuery()->getResult();
    }

    /**
     * Recherche parmi les subordonnés du commercial ceux qui ne sont pas déjà assigné à une boutique pour une marque donnée
     * @param $text
     * @param $salesman Salesman le commercial
     * @param $salesmen Salesman les commerciaux actuels de la boutique
     * @param $brand
     * @param $shop
     * @return mixed
     */
    public function searchForSubordinate($text, $salesman, $salesmen, $brand, $shop) {
        $QueryBuilder = $this->createQueryBuilder('s')
            ->leftJoin('s.shops', 'salesmanshop')
            ->innerJoin('s.contact', 'c')
            ->innerJoin('s.superiors', 'superior')
            ->where('s.companyName LIKE :text OR s.id LIKE :text 
            OR c.id LIKE :text OR c.lastname LIKE :text OR c.firstname LIKE :text')
            ->andWhere('s.id NOT IN (:salesmen_ids) AND superior.superior = :salesman_id AND superior.brand = :brand_id')
            ->andWhere('(salesmanshop.id is NULL OR (salesmanshop.brand <> :brand_id AND salesmanshop.shop <> :shop_id))')
            ->setParameters(array(
                'text' => '%'.$text.'%',
                'salesman_id' => $salesman,
                'salesmen_ids' => $salesmen,
                'brand_id' => $brand,
                'shop_id' => $shop));
        return $QueryBuilder->getQuery()->getResult();
    }
}
