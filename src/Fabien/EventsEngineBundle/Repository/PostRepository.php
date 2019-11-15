<?php

namespace Fabien\EventsEngineBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{

  public function findByDate(){
     return $this->findBy(array(), array('date' => 'DESC'));
  }

  public function listPublic(){
    return $this->createQueryBuilder("d")
      ->Where("d.date<=:now")
        ->setParameter('now', new \DateTime('now'))
      ->andWhere("d.publish=:publish")
          ->setParameter('publish', 1)
      ->orderBy("d.date",'desc')
      ->getQuery()
      ->getResult()
        ;
  }

  public function countPosts()
  {
    return $this->createQueryBuilder("d")
      ->select('COUNT(d)')
      ->Where("d.date<=:now")
        ->setParameter('now', new \DateTime('now'))
      ->andWhere("d.publish=:publish")
          ->setParameter('publish', 1)
      ->orderBy("d.date",'desc')
      ->getQuery()
      ->getSingleScalarResult()
        ;
  }

}
