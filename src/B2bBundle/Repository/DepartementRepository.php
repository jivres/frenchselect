<?php

namespace B2bBundle\Repository;

/**
 * DepartementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DepartementRepository extends \Doctrine\ORM\EntityRepository {

    public function queryIn($ids) {
        $QueryBuilder = $this->createQueryBuilder('d')
            ->where('d.id IN (:ids)')
            ->setParameters(array('ids' => $ids));
        return $QueryBuilder;
    }
}
