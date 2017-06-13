<?php

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class ExtraccionCuotaAhorroRepository extends  EntityRepository
{
    public function findExtraccionCuotaAhorro($id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:ExtraccionCuotaAhorro t JOIN t.idAhorro c WHERE c.id = :id");
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }
}