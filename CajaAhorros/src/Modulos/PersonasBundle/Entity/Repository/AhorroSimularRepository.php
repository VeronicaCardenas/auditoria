<?php

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;


class AhorroSimularRepository extends EntityRepository{
    public function findAhorrosSimularByPersonaOrdDescPorFecha($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:AhorroSimular c LEFT JOIN c.persona p WHERE p=:persona  ORDER BY c.fechaSolicitud DESC ");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }
}