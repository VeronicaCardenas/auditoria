<?php
/**
 * Created by PhpStorm.
 * User: Soft
 * Date: 2/12/15
 * Time: 07:09 PM
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;


class ParroquiaRepository extends EntityRepository{

    public  function findByCiudadId($idCiudad){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT p FROM ModulosPersonasBundle:Parroquia p JOIN p.ciudad c WHERE c.id =:idCiudad");
        $consulta->setParameter('idCiudad', $idCiudad);

        return $consulta->getResult();
    }

} 