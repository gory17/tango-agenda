<?php

namespace Fabien\EventsEngineBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class FbimportRepository extends \Doctrine\ORM\EntityRepository
{
  public function countElements(){
    $qb = $this->createQueryBuilder("a");
    $qb->select('COUNT(a.id)');

    $count = $qb->getQuery()->getSingleScalarResult();

    return $count;
  }


  public function existFB($queryString){

    $operation= $this->createQueryBuilder('a')
                ->select('COUNT(a)')
                ->Where("a.fbid like :recherche")
                ->setParameter("recherche",'%'.$queryString.'%')
                ;

    $count = $operation->getQuery()->getSingleScalarResult();

    return  $count;
  }
}
