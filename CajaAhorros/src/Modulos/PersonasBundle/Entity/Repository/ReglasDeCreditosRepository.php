<?php
/**
 * Created by PhpStorm.
 * User: jleon
 * Date: 09/11/2015
 * Time: 12:28
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class ReglasDeCreditosRepository extends  EntityRepository
{
    public function findReglasDeCreditosPorTiposCreditos($id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:ReglasDeCreditos t JOIN t.tipoCredito c WHERE c.id = :id");
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }
}