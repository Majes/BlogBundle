<?php

namespace Majes\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArticleLangRepository extends EntityRepository {


    /**
    * GET last 5 updated pages
    */
     public function getArticlesByCategory($lang, $isActive, $deleted, $category, $limit = null, $offset = null) {

        $query = $this->createQueryBuilder('a');

        $query=$query->where('a.locale LIKE :lang')
             ->andWhere('a.isActive = :isActive')
             ->andWhere('a.deleted = :deleted')
             ->setParameter('lang', $lang)
             ->setParameter('isActive', $isActive)
             ->setParameter('deleted', $deleted)
             ->leftJoin('a.categories', 'c')
             ->andWhere($query->expr()->in('c', ':c'))
             ->setParameter('c', $category)
             ->orderBy('a.date', 'DESC');

        if (!is_null($limit)) {
          $query->setMaxResults( $limit );
        }
        if (!is_null($offset)) {
          $query->setFirstResult( $offset );
        }

        return $query->getQuery()->getResult();
     }

     /**
     * GET last 5 updated pages
     */
    public function getArticlesCurrentYear($lang, $isActive, $deleted) {

    	$currentYear = new \DateTime();
    	$currentYear->setDate(date('Y'), 1, 1)->setTime(0,0,0);

        $query = $this->createQueryBuilder('a')
            ->where('a.date > :date')
            ->andWhere('a.isActive = :isActive')
            ->andWhere('a.deleted = :deleted')
            ->setParameter('date', $currentYear)
            ->setParameter('isActive', $isActive)
            ->setParameter('deleted', $deleted)
            ->orderBy('a.date', 'DESC')
            ->getQuery();

        return $query->getResult();
    }


    /**
     * GET last 5 updated pages
     */
    public function getArticlesPastYears($lang, $isActive, $deleted) {

    	$currentYear = new \DateTime();
    	$currentYear->setDate(date('Y'), 1, 1)->setTime(0,0,0);

        $query = $this->createQueryBuilder('a')
            ->where('a.date < :date')
            ->andWhere('a.isActive = :isActive')
            ->andWhere('a.deleted = :deleted')
            ->setParameter('date', $currentYear)
            ->setParameter('isActive', $isActive)
            ->setParameter('deleted', $deleted)
            ->orderBy('a.date', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * GET last 5 updated pages
     */
    public function searchArticles($lang, $slug, $isActive, $deleted) {

        $currentYear = new \DateTime();
        $currentYear->setDate(date('Y'), 1, 1)->setTime(0,0,0);

        $query = $this->createQueryBuilder('a')
            ->where('a.isActive = :isActive')
            ->andWhere('a.deleted = :deleted')
            ->andWhere('a.content LIKE :slug')
            ->orWhere('a.title LIKE :slug')
            ->setParameter('isActive', $isActive)
            ->setParameter('deleted', $deleted)
            ->setParameter('slug', '%'.$slug.'%')
            ->orderBy('a.date', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

}
