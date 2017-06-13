<?php
/**
 * Created by PhpStorm.
 * User: Soft
 * Date: 8/11/15
 * Time: 09:24 PM
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class PersonaDocumentoRepository extends  EntityRepository{

    public function findPersonaDocumentoPorPersona($id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT p FROM ModulosPersonasBundle:PersonaDocumento p JOIN p.persona e WHERE e.id = :id");
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }
} 