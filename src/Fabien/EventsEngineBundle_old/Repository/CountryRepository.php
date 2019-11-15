<?php

namespace Fabien\EventsEngineBundle\Repository;

/**
 * CountryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CountryRepository extends \Doctrine\ORM\EntityRepository
{

  public function getLimitedCountry()
  {
    $tabCountry=array(14,21,20,54,57,58,68,74,75,76,77,78,82,88,85,94,99,100,105,107,120,125,126,127,141,144,145,155,164,175,176,179,180,193,197,198,205,211,212,223,228,230,249);

    $qb = $this->createQueryBuilder('c');
    $qb->andWhere("c.id in (:country)")
          ->setParameter('country', $tabCountry);

    return $qb;

  }

  public function getCitiesFromCountry(){
    $qb = $this->createQueryBuilder('c')
        ->join('c.state', 'state')
        ->addSelect('state')
        ->join('state.city', 'city')
        ->addSelect('city')
        ;

      $qb->andWhere("c.id in (:country)")
        ->setParameter('country', $country->getId())
        ->orderBy("c.title",'ASC')
        ;

    return $qb;
  }



public function getCountryEventsResume()
{
  $qb = $this->createQueryBuilder('c')
      ->join('c.states','state')
      //->addSelect('state')
      ->join('state.cities', 'city')
      //->addSelect('city')
      ->join('city.events', 'evt')
      //->addSelect('evt')
      ->addSelect('c.id,c.title,c.slug,c.sortname,count(evt) as cptevt')
      ->join('evt.dates','d')
      ->andWhere("d.end>=:now")
        ->setParameter('now', new \DateTime('now'))
      ->orderBy("cptevt",'DESC')
      ->groupBy("c.id")
      ;

  return $qb
        ->getQuery()
        ->getResult()
      ;
}







  public function getAllActiveCountry($TypeEvent=false){

    $qb = $this->createQueryBuilder('c')
        ->join('c.states','state')
        //->addSelect('state')
        ->join('state.cities', 'city')
        //->addSelect('city')
        ->join('city.events', 'evt')
        //->addSelect('evt')
        ->addSelect('c.title,c.sortname,c.slug,count(evt) as cptevt')
        ->join('evt.dates','d')
        ->andWhere("d.end>=:now")
          ->setParameter('now', new \DateTime('now'))
        ->orderBy("cptevt",'DESC')
        ->groupBy("c.id")
        ;

    if($TypeEvent!=""){
      $qb->andWhere("evt.type_event=:type")
         ->setParameter('type', $TypeEvent);
    }

    return $qb
      ->getQuery()
      ->getResult()
    ;
  }
/*
  public function findCountryFromCity($City)
  {
    $qb = $this->createQueryBuilder('co')
        ->join('co.state', 'state')
        ->addSelect('state')
        ->join('state.city', 'city')
        ->addSelect('city')
        ;

      $qb->andWhere("city.id = :city")
        ->setParameter('city', $City->getId());

     $qb->getQuery()
      ->getResult();


      print_r($qb);
      return $qb;
  }

  */

}