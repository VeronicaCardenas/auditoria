<?php
/**
 * Created by PhpStorm.
 * User: Soft
 * Date: 2/12/15
 * Time: 07:09 PM
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;


class DTVCRepository extends EntityRepository{

    public function findDTVCMeses(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT DISTINCT t.mes, t.fecha FROM ModulosPersonasBundle:VCHR t");
        return $consulta->getResult();
    }

    /*public function findDTVCDeudora($codigo, $mes){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT sum(t.valor) FROM ModulosPersonasBundle:DTVC t JOIN t.cuentaDeudoraId cd JOIN t.idVchr vchr WHERE cd.codigo =:codigo AND vchr.mes=:mes ");
        $consulta->setParameter('codigo', $codigo);
        $consulta->setParameter('mes', $mes);
        return $consulta->getResult();
    }

    public function findDTVCAcreedora($codigo, $mes){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT sum(t.valor) FROM ModulosPersonasBundle:DTVC t JOIN t.cuentaAcreedoraId cd JOIN t.idVchr vchr WHERE cd.codigo =:codigo AND vchr.mes=:mes ");
        $consulta->setParameter('codigo', $codigo);
        $consulta->setParameter('mes', $mes);
        return $consulta->getResult();
    }*/


    public function findDTVCDeudora($codigo, $mes, $fechaInicial, $fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT sum(t.valor) FROM ModulosPersonasBundle:DTVC t JOIN t.cuentaDeudoraId cd JOIN t.idVchr vchr WHERE cd.codigo =:codigo AND vchr.mes=:mes AND vchr.fecha<=:fechafin AND vchr.fecha>=:fechaini");
        $consulta->setParameter('codigo', $codigo);
        $consulta->setParameter('mes', $mes);
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);
        return $consulta->getResult();
    }

    public function findDTVCAcreedora($codigo, $mes, $fechaInicial, $fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT sum(t.valor) FROM ModulosPersonasBundle:DTVC t JOIN t.cuentaAcreedoraId cd JOIN t.idVchr vchr WHERE cd.codigo =:codigo AND vchr.mes=:mes AND vchr.fecha<=:fechafin AND vchr.fecha>=:fechaini");
        $consulta->setParameter('codigo', $codigo);
        $consulta->setParameter('mes', $mes);
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);
        return $consulta->getResult();
    }
    
    public function findDTVCDeudoraAnual($codigo, $fechaInicial,$fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT sum(t.valor) FROM ModulosPersonasBundle:DTVC t JOIN t.cuentaDeudoraId cd JOIN t.idVchr vchr WHERE cd.codigo =:codigo AND vchr.fecha<=:fechafin AND vchr.fecha>=:fechaini ");
        $consulta->setParameter('codigo', $codigo);
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);
        return $consulta->getResult();
    }

    public function findDTVCAcreedoraAnual($codigo, $fechaInicial,$fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT sum(t.valor) FROM ModulosPersonasBundle:DTVC t JOIN t.cuentaAcreedoraId cd JOIN t.idVchr vchr WHERE cd.codigo =:codigo AND vchr.fecha<=:fechafin AND vchr.fecha>=:fechaini ");
        $consulta->setParameter('codigo', $codigo);
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);
        return $consulta->getResult();
    }

    public  function findDTVCAporFechasAsc($fechaInicial,$fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:DTVC l JOIN l.idVchr vchr WHERE vchr.fecha<=:fechafin AND vchr.fecha>=:fechaini ORDER BY vchr.fecha ASC");
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);

        return $consulta->getResult();
    }

    public  function findAllDTVCAporFechasAsc(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:DTVC l JOIN l.idVchr vchr ORDER BY vchr.fecha ASC");

        return $consulta->getResult();
    }

    public  function findDTVCAporFechasDesc($fechaInicial,$fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:DTVC l JOIN l.idVchr vchr WHERE vchr.fecha<=:fechafin AND vchr.fecha>=:fechaini ORDER BY vchr.fecha DESC ");
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);

        return $consulta->getResult();
    }

    public  function findAllDTVCAporFechasDesc(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:DTVC l JOIN l.idVchr vchr ORDER BY vchr.fecha DESC ");

        return $consulta->getResult();
    }

    public  function findAllDTVCByVchr($idVchr){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:DTVC l JOIN l.idVchr vchr WHERE vchr.id =:idVchr ");
        $consulta->setParameter('idVchr', $idVchr);

        return $consulta->getSingleResult();
    }

} 