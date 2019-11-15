<?php

namespace Fabien\EventsEngineBundle\Repository;

/**
 * BannedRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BannedRepository extends \Doctrine\ORM\EntityRepository
{
  public function countElements(){
    $qb = $this->createQueryBuilder('a');
    $qb->select('COUNT(a)');

    $count = $qb->getQuery()->getSingleScalarResult();

    return $count;
  }


  public function isBanned($idFb){

    $operation= $this->createQueryBuilder('a')
                ->select('COUNT(a)')
                ->Where("a.eventurl like :recherche")
                ->setParameter("recherche",'%'.$idFb.'%')
                ;

    $count = $operation->getQuery()->getSingleScalarResult();

    return $count;
  }
}
