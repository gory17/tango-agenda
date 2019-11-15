<?php

namespace Fabien\EventsEngineBundle\Repository;

/**
 * GroupFbRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GroupFbRepository extends \Doctrine\ORM\EntityRepository
{

  function countReq(){
    $operation= $this->createQueryBuilder('a')
                ->select('COUNT(a)')
                ;

    $count = $operation->getQuery()->getSingleScalarResult();

    return $count;
  }
}
