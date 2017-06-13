<?php
/**
 * Created by PhpStorm.
 * User: jleon
 * Date: 09/11/2015
 * Time: 12:28
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class TablaAmortizacionRepository extends  EntityRepository
{
    public function findTablasAmortizacionPorCreditos($id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:TablaAmortizacion t JOIN t.credito_id c WHERE c.id =:id");
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }
}