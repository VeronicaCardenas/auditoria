<?php

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class PagoCuotaAhorroRepository extends  EntityRepository
{
    public function findPagosCuotasAhorros($id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:PagoCuotaAhorro t JOIN t.idAhorro c WHERE c.id = :id ORDER BY t.fechaDeEntrada ASC");
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }
    public function findPagosCuotasAhorrosPorFechas($id, $fechaini, $fechafin){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:PagoCuotaAhorro t JOIN t.idAhorro c WHERE c.id = :id  AND l.fecha<=:fechafin AND l.fecha>=:fechaini ORDER BY t.fechaDeEntrada ASC");
        $consulta->setParameter('id', $id);
        $consulta->setParameter('fechaini', $fechaini);
        $consulta->setParameter('fechafin', $fechafin);
        return $consulta->getResult();
    }
    public function findDepositosAhorros($id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:PagoCuotaAhorro t JOIN t.idAhorro c WHERE c.id = :id AND t.tipo=1 ORDER BY t.fechaDeEntrada ASC");
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }
    public function findRetirosAhorros($id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:PagoCuotaAhorro t JOIN t.idAhorro c WHERE c.id = :id AND t.tipo=-1 ORDER BY t.fechaDeEntrada ASC");
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }
    public function findMayoresQ($id, $idPagoCuota){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:PagoCuotaAhorro t JOIN t.idAhorro c WHERE c.id = :id AND t.id >:idPagoCuota ORDER BY t.id ASC ");
        $consulta->setParameter('id', $id);
        $consulta->setParameter('idPagoCuota', $idPagoCuota);
        return $consulta->getResult();
    }
    public function findMenoresQ($id, $idPagoCuota){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:PagoCuotaAhorro t JOIN t.idAhorro c WHERE c.id = :id AND t.id <:idPagoCuota ORDER BY t.id ASC ");
        $consulta->setParameter('id', $id);
        $consulta->setParameter('idPagoCuota', $idPagoCuota);
        return $consulta->getResult();
    }
    public function findDepositosAhorrosMayoresQ($id, $idPagoCuota){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:PagoCuotaAhorro t JOIN t.idAhorro c WHERE c.id = :id AND t.id >:idPagoCuota AND t.tipo=1");
        $consulta->setParameter('id', $id);
        $consulta->setParameter('idPagoCuota', $idPagoCuota);
        return $consulta->getResult();
    }
    public function findRetirosAhorrosMayoresQ($id, $idPagoCuota){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:PagoCuotaAhorro t JOIN t.idAhorro c WHERE c.id = :id AND t.id >:idPagoCuota AND t.tipo=-1");
        $consulta->setParameter('id', $id);
        $consulta->setParameter('idPagoCuota', $idPagoCuota);
        return $consulta->getResult();
    }
    public function findDepositosAhorrosMenoresQ($id, $idPagoCuota){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:PagoCuotaAhorro t JOIN t.idAhorro c WHERE c.id = :id AND t.id <:idPagoCuota AND t.tipo=1");
        $consulta->setParameter('id', $id);
        $consulta->setParameter('idPagoCuota', $idPagoCuota);
        return $consulta->getResult();
    }
    public function findRetirosAhorrosMenoresQ($id, $idPagoCuota){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT t FROM ModulosPersonasBundle:PagoCuotaAhorro t JOIN t.idAhorro c WHERE c.id = :id AND t.id <:idPagoCuota AND t.tipo=-1");
        $consulta->setParameter('id', $id);
        $consulta->setParameter('idPagoCuota', $idPagoCuota);
        return $consulta->getResult();
    }
    public function deletePagoCuota($idPagoCuota){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("DELETE ModulosPersonasBundle:PagoCuotaAhorro u WHERE u.id=:idPagoCuota");
        $consulta->setParameter('idPagoCuota', $idPagoCuota);
        return $consulta->getResult();
    }
}