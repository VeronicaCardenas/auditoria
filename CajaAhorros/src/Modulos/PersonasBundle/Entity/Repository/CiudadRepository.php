<?php
/**
 * Created by PhpStorm.
 * User: Soft
 * Date: 2/12/15
 * Time: 07:09 PM
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;


class CiudadRepository extends EntityRepository{

    public  function findByProvinciaId($idProvincia){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ciudad c JOIN c.provincia p WHERE p.id =:idProvincia ");
        $consulta->setParameter('idProvincia', $idProvincia);

        return $consulta->getResult();
    }

} 