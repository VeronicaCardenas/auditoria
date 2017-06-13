<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 1/31/2016
 * Time: 12:40 AM
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class CreditoDocumentoRepository extends  EntityRepository{

    public function findCreditoDocumentoPorCredito($id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT dc FROM ModulosPersonasBundle:CreditosDocumento dc JOIN dc.credito c WHERE c.id = :id");
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }
}