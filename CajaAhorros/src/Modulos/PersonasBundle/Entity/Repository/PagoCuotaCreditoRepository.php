<?php
/**
 * Created by PhpStorm.
 * User: jleon
 * Date: 09/11/2015
 * Time: 12:28
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class PagoCuotaCreditoRepository extends  EntityRepository
{
    public function findPagosCuotasCreditos($id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:PagoCuotaCredito t JOIN t.creditoId c WHERE c.id = :id");
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }

    public function findPagosCuotasCreditosSinInteres(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:PagoCuotaCredito t WHERE t.sininteres = 1");
        return $consulta->getResult();
    }
}