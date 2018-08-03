<?php

namespace B2bBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * BrandRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BrandRepository extends EntityRepository {


    public function search($text, $target, $categories = null, $univers = null, $features = null,$prices =null ) {

        $categoriesExpression = null;
        $universExpression = null;
        $featuresExpression = null;
        $pricesExpression = null;
        if ($categories) {
            $categoriesExpression = 'categorie ='.$categories[0];
            for ($i = 1; $i < count($categories); $i++) {
                $categoriesExpression = $categoriesExpression .' OR categorie ='. $categories[$i];
            }
        }
        if ($univers) {
            $universExpression = 'univers ='.$univers[0];
            for ($i = 1; $i < count($univers); $i++) {
                $universExpression = $universExpression .' OR univers ='. $univers[$i];
            }
        }
        if ($features) {
            $featuresExpression = 'feature ='.$features[0];
            for ($i = 1; $i < count($features); $i++) {
                $featuresExpression = $featuresExpression .' OR feature ='. $features[$i];
            }
        }
        if ($prices) {
            $pricesExpression = 'priceRange ='.$prices[0];
            for ($i = 1; $i < count($prices); $i++) {
                $pricesExpression = $pricesExpression .' OR priceRange ='. $prices[$i];
            }
        }
        $QueryBuilder = $this->createQueryBuilder('b')
            ->leftJoin('b.categories', 'categorie')
            ->leftJoin('b.univers', 'univers')
            ->leftJoin('b.feature', 'feature')
            ->leftJoin('b.priceRange', 'priceRange')
            ->leftJoin('b.targets', 'targets')
            ->where('b.isActive = TRUE')
            ->andWhere('b.brandName LIKE :text')
            ->andWhere($categoriesExpression)
            ->andWhere($universExpression)
            ->andWhere($featuresExpression)
            ->andWhere($pricesExpression)
            ->andWhere('b.primarytarget = :target OR targets = :target')
            ->setParameters(array('text' => '%'.$text.'%','target' => $target));

        return (new Paginator($QueryBuilder))->getIterator()->getArrayCopy();
    }

    public function getAvailableForSalesman($salesman) {
        $QueryBuilder = $this->createQueryBuilder('b')
            ->leftJoin('b.salesmen', 'salesman_link')
            ->where('(salesman_link.id IS NULL) OR (salesman_link.salesman <> :salesman_id)')
            ->andWhere('b.isActive = 1')
            ->setParameters(array('salesman_id' => $salesman));
        return $QueryBuilder->getQuery()->getResult();
    }

    public function findForSalesman($salesman) {
        $QueryBuilder = $this->createQueryBuilder('b')
            ->innerJoin('b.salesmen', 'salesman_link')
            ->where(':salesman = salesman_link.salesman')
            ->setParameters(array('salesman' => $salesman));
        return $QueryBuilder->getQuery()->getResult();
    }

    public function getBrands($salesman) {
        $QueryBuilder = $this->createQueryBuilder('b')
            ->innerJoin('b.salesmen', 'salesman_link')
            ->where(':salesman = salesman_link.salesman')
            ->orderBy('b.brandName')
            ->setParameters(array('salesman' => $salesman));
        return $QueryBuilder->getQuery()->getResult();
    }

    public function queryIn($brands) {
        $QueryBuilder = $this->createQueryBuilder('b')
            ->where('b.id IN (:brands)')
            ->setParameters(array('brands' => $brands));
        return $QueryBuilder;
    }

    public function findWoman() {

        $QueryBuilder = $this->createQueryBuilder('b')
            ->leftJoin('b.targets', 'targets')
            ->where('b.isActive = TRUE')
            ->andWhere('b.primarytarget = 2 OR targets = 2');

        return (new Paginator($QueryBuilder))->getIterator()->getArrayCopy();
    }
}
