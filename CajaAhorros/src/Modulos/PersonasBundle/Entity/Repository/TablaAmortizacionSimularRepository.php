<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 05/02/2016
 * Time: 9:57
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class TablaAmortizacionSimularRepository extends  EntityRepository
{
    public function findTablasAmortizacionPorCreditosSim($id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:TablaAmortizacionSimular t JOIN t.credito_id c WHERE c.id =:id");
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }
}