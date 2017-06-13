<?php
/**
 * Created by PhpStorm.
 * User: Soft
 * Date: 2/12/15
 * Time: 07:09 PM
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;


class PersonaRepository extends  EntityRepository{
    public  function findOrdenados(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Persona l WHERE (l.tipo_persona = 2 OR l.tipo_persona = 1) ORDER BY l.primerApellido ASC");

        return $consulta->getResult();
    }

    public  function findOrdenadosConCredito(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Persona l WHERE (l.tipo_persona = 2 OR l.tipo_persona = 1) ORDER BY l.primerApellido ASC");

        return $consulta->getResult();
    }

    public  function findOrdenadosActivos(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Persona l WHERE (l.tipo_persona = 2 OR l.tipo_persona = 1) AND l.estadoPersona = 0 ORDER BY l.primerApellido ASC");

        return $consulta->getResult();
    }

    public  function findOrdenadosEmpleados(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Persona l WHERE (l.tipo_persona = 3) ORDER BY l.primerApellido ASC");

        return $consulta->getResult();
    }

} 