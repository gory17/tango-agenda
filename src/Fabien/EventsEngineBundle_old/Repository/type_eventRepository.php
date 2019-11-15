<?php

namespace Fabien\EventsEngineBundle\Repository;

/**
 * type_eventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class type_eventRepository extends \Doctrine\ORM\EntityRepository
{

  public function getTypeWithEvent(){

    return $this->createQueryBuilder('t')
            ->join("t.events","evt")
            ->addSelect('t.title,t.titleTrad,t.slug,count(evt) as cptevt')
            ->andwhere("evt.publish=1")
            ->join('evt.dates','d')
            ->andWhere("d.end>=:now")
              ->setParameter('now', new \DateTime('now'))
            ->andWhere("t.id in (3,4,5,10,2,12)")
            ->orderBy("t.id","ASC")
            ->groupBy("t.id")
            ->getQuery()
            ->getResult()
            ;

  }

  public function findAllWithCountEvents(){

    return $this->createQueryBuilder('t')
            ->join("t.events","evt")
            ->addSelect('t.id,t.title,t.titleTrad,t.slug,count(evt) as nombre')
            ->andwhere("evt.publish=0")
            ->join('evt.dates','d')
            ->andWhere("d.end>=:now")
              ->setParameter('now', new \DateTime('now'))
            ->andWhere("evt.date_creation>=:now")
                ->setParameter('now', new \DateTime('now -1Day'))
            ->orderBy("t.id","ASC")
            ->groupBy("t.id")
            ->getQuery()
            ->getResult()
            ;

  }


}
