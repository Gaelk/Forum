<?php

namespace AppBundle\Repository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{

    public function getPostsGroupedByYear(){
        $qb= $this->createQueryBuilder("p");
        $qb->select("YEAR(p.createdAt) as yearPublished, COUNT(p.id) as numberOfPosts")
            ->groupBy("yearPublished");
        return $qb->getQuery()->getArrayResult();
    }

    public function getPostByYear($year){
        $qb= $this->createQueryBuilder("p");
        $qb->select("p")
            ->where("YEAR(p.createdAt)=:year")
        ->setParameter("year", $year);

        return $qb->getQuery()->getResult();
    }
}
