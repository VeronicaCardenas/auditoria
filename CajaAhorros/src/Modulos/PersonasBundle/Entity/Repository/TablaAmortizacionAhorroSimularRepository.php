<?php

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class TablaAmortizacionAhorroSimularRepository extends  EntityRepository
{
    
    public function findTablasAmortizacionPorAhorrosSimular($id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:TablaAmortizacionAhorroSimular t JOIN t.ahorro_id c WHERE c.id = :id");
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }
}