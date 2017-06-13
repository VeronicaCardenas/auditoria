<?php
/**
 * Created by PhpStorm.
 * User: Soft
 * Date: 2/12/15
 * Time: 07:09 PM
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;


class VCHRRepository extends EntityRepository{

    public  function findVCHRByLibroId($idLibro){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT v FROM ModulosPersonasBundle:VCHR v JOIN v.libroId l WHERE l.id =:idLibro ");
        $consulta->setParameter('idLibro', $idLibro);

        return $consulta->getSingleResult();
    }

} 