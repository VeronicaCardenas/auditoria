<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 05/02/2016
 * Time: 9:57
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class TipoProductoContableRepository extends  EntityRepository
{
    public function findTipoProductoContableOrdenado(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:TipoProductoContable t ORDER BY t.tipo ASC");
        return $consulta->getResult();
    }
}