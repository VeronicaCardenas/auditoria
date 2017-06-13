<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 1/14/2016
 * Time: 12:09 AM
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;


class AhorroRepository extends EntityRepository{
    public function findAhorrosByPersonaOrdDescPorFecha($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c LEFT JOIN c.persona p WHERE p=:persona  AND p.estadoPersona = 0  ORDER BY c.fechaSolicitud DESC ");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }

    public function findAhorrosOrdenadosPorFecha(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT DISTINCT IDENTITY (c.persona) FROM ModulosPersonasBundle:Ahorro c ORDER BY c.fechaSolicitud,c.id" );
        return $consulta->getResult();
    }

    public function findAhorrosByTipo($tipo){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c LEFT JOIN c.tipoAhorro p WHERE p.id=:tipo");
        $consulta->setParameter('tipo', $tipo);
        return $consulta->getResult();
    }

    public function findAhorrosByTipoAprobado($tipo){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c LEFT JOIN c.persona x LEFT JOIN c.tipoAhorro p LEFT JOIN c.estadoAhorro e WHERE p.id=:tipo AND x.estadoPersona = 0 AND e.tipo='APROBADO' ORDER BY c.persona");
        $consulta->setParameter('tipo', $tipo);
        return $consulta->getResult();
    }

    public function findAhorrosByTipoAprobado2($tipo){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c  LEFT JOIN c.persona x LEFT JOIN c.tipoAhorro p LEFT JOIN c.estadoAhorro e WHERE p.id=:tipo AND x.estadoPersona = 0 AND e.tipo='DEPOSITADO' ORDER BY c.persona");
        $consulta->setParameter('tipo', $tipo);
        return $consulta->getResult();
    }

    public function findAhorrosByTipoAprobado3($tipo){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c  LEFT JOIN c.persona x LEFT JOIN c.tipoAhorro p LEFT JOIN c.estadoAhorro e WHERE p.id=:tipo AND x.estadoPersona = 0 AND (c.valorEnCaja < c.valorAhorrar OR c.valorEnCaja is NULL) AND (e.tipo='DEPOSITADO' OR e.tipo='APROBADO' ) ORDER BY c.persona");
        $consulta->setParameter('tipo', $tipo);
        return $consulta->getResult();
    }

    public function findAhorrosByTipoAprobado4($tipo){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c  LEFT JOIN c.persona x LEFT JOIN c.tipoAhorro p LEFT JOIN c.estadoAhorro e WHERE p.id=:tipo AND x.estadoPersona = 0 AND (e.tipo='DEPOSITADO' OR e.tipo='RENOVADO' )  ORDER BY c.persona");
        $consulta->setParameter('tipo', $tipo);
        return $consulta->getResult();
    }

    public function findAhorrosByTipoAprobadoPorId($tipo,$persona,$id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c LEFT JOIN c.persona d LEFT JOIN c.tipoAhorro p LEFT JOIN c.estadoAhorro e WHERE p.id=:tipo  AND d.estadoPersona = 0 AND c.id=:id AND d=:persona AND e.tipo='APROBADO'");
        $consulta->setParameter('tipo', $tipo);
        $consulta->setParameter('persona', $persona);
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }

    public function findAhorrosByTipoAprobadoPorId1($tipo,$id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c LEFT JOIN c.persona d LEFT JOIN c.tipoAhorro p LEFT JOIN c.estadoAhorro e WHERE p.id=:tipo AND d.estadoPersona = 0 AND c.id=:id AND e.tipo='APROBADO'");
        $consulta->setParameter('tipo', $tipo);
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }

    public function findAhorrosByTipoAprobadoPorId2($tipo,$id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c LEFT JOIN c.persona d LEFT JOIN c.tipoAhorro p LEFT JOIN c.estadoAhorro e WHERE p.id=:tipo AND c.id=:id AND d.estadoPersona = 0 AND e.tipo='DEPOSITADO'");
        $consulta->setParameter('tipo', $tipo);
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }

    public function findAhorrosByTipoAprobadoPorId3($tipo,$id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c LEFT JOIN c.persona d LEFT JOIN c.tipoAhorro p LEFT JOIN c.estadoAhorro e WHERE p.id=:tipo AND c.id=:id AND d.estadoPersona = 0 AND (c.valorEnCaja < c.valorAhorrar OR c.valorEnCaja is NULL) AND (e.tipo='DEPOSITADO' OR e.tipo='APROBADO' )");
        $consulta->setParameter('tipo', $tipo);
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }

    public function findAhorrosByTipoAprobadoPorId4($tipo,$id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c LEFT JOIN c.persona d LEFT JOIN c.tipoAhorro p LEFT JOIN c.estadoAhorro e WHERE p.id=:tipo AND c.id=:id AND d.estadoPersona = 0 AND (e.tipo='DEPOSITADO' OR e.tipo='RENOVADO' )");
        $consulta->setParameter('tipo', $tipo);
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }

    public function findAhorrosVistaByTipoDepositadoPorPersona($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c LEFT JOIN c.persona d LEFT JOIN c.tipoAhorro p LEFT JOIN c.estadoAhorro e WHERE p.id=1 AND d=:persona AND d.estadoPersona = 0 AND e.tipo='DEPOSITADO'");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }

    public function findAhorrosPlazByTipoDepositadoPorPersona($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c LEFT JOIN c.persona d LEFT JOIN c.tipoAhorro p LEFT JOIN c.estadoAhorro e WHERE p.id=2 AND d=:persona AND d.estadoPersona = 0 AND (e.tipo='DEPOSITADO' OR e.tipo='RENOVADO') ");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }

    public function findAhorrosResByTipoDepositadoPorPersona($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Ahorro c LEFT JOIN c.persona d LEFT JOIN c.tipoAhorro p LEFT JOIN c.estadoAhorro e WHERE p.id=3 AND d=:persona AND d.estadoPersona = 0 AND e.tipo='DEPOSITADO'");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }
}