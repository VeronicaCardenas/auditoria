<?php
/**
 * Created by PhpStorm.
 * User: Soft
 * Date: 2/12/15
 * Time: 07:09 PM
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;


class ProvinciaRepository extends EntityRepository{

    public  function findByRegionId($idRegion){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT p FROM ModulosPersonasBundle:Provincia p JOIN p.region r WHERE r.id =:idRegion ");
        $consulta->setParameter('idRegion', $idRegion);

        return $consulta->getResult();
    }

} 