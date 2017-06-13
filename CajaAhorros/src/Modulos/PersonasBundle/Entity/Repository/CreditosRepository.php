<?php
/**
 * Created by PhpStorm.
 * User: Soft
 * Date: 2/12/15
 * Time: 07:09 PM
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;


class CreditosRepository extends EntityRepository{

    public function findPersonasCreditosDesgrav(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e LEFT JOIN c.idProductosDeCreditos pc WHERE (e.tipo='APROBADO')  AND p.estadoPersona = 0 AND c.desgravamenPagado=false AND (pc.metodoAmortizacion=1 OR pc.metodoAmortizacion=2 OR pc.metodoAmortizacion=3) ORDER BY p.primerApellido");//OR pc.id=3
        return $consulta->getResult();
    }

    /*public function findCreditosOrdenadosPorFecha(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT p FROM ModulosPersonasBundle:Persona p WHERE EXISTS (SELECT DISTINCT IDENTITY (c.persona) FROM ModulosPersonasBundle:Creditos c ORDER BY c.fechaSolicitud) AND (p.tipo_persona = 2 OR p.tipo_persona = 1)" );//OR pc.id=3
        return $consulta->getResult();
    }*/

    public function findCreditosOrdenadosPorFecha(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT DISTINCT IDENTITY (c.persona) FROM ModulosPersonasBundle:Creditos c ORDER BY c.fechaSolicitud,c.id" );
        return $consulta->getResult();
    }

    public function findPersonasCreditosDesgravporID($persona, $id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e LEFT JOIN c.idProductosDeCreditos pc WHERE (e.tipo='APROBADO')  AND p.estadoPersona = 0 AND c.desgravamenPagado=false AND c.id=:id AND p=:persona ");
        $consulta->setParameter('persona', $persona);
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }

    public function findPersonasCreditosPagar(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e LEFT JOIN c.idProductosDeCreditos pc WHERE e.tipo='OTORGADO'  AND p.estadoPersona = 0 AND (pc.metodoAmortizacion=1 OR pc.metodoAmortizacion=2 OR pc.metodoAmortizacion=3) ORDER BY p.primerApellido");
        return $consulta->getResult();
    }

    public function findPersonasInteresCreditosPagar(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e LEFT JOIN c.idProductosDeCreditos pc WHERE (e.tipo='OTORGADO' OR e.tipo='PAGADO')  AND p.estadoPersona = 0  AND (pc.metodoAmortizacion=1 OR pc.metodoAmortizacion=2 OR pc.metodoAmortizacion=3) AND c.sininteres=1 ORDER BY p.primerApellido");
        return $consulta->getResult();
    }

    public function findPersonasCreditos(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e LEFT JOIN c.idProductosDeCreditos pc WHERE e.tipo='DESGRAVAMEN PAGADO' AND p.estadoPersona = 0 AND c.desgravamenPagado=true AND pc.metodoAmortizacion!=2 ORDER BY p.primerApellido");
        return $consulta->getResult();
    }

    public function findPersonasCreditosporID($persona, $id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e LEFT JOIN c.idProductosDeCreditos pc WHERE e.tipo='DESGRAVAMEN PAGADO' AND p.estadoPersona = 0  AND c.desgravamenPagado=true AND pc.metodoAmortizacion!=2 AND c.id=:id AND p=:persona");
        $consulta->setParameter('persona', $persona);
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }

    public function findPersonasCreditosEmergentes(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e LEFT JOIN c.idProductosDeCreditos pc WHERE e.tipo='DESGRAVAMEN PAGADO'  AND p.estadoPersona = 0 AND c.desgravamenPagado=true AND pc.metodoAmortizacion=2 ORDER BY p.primerApellido");
        return $consulta->getResult();
    }

    public function findPersonasCreditosEmergentesporID($persona, $id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e LEFT JOIN c.idProductosDeCreditos pc WHERE e.tipo='DESGRAVAMEN PAGADO'  AND p.estadoPersona = 0 AND pc.metodoAmortizacion=2 AND c.desgravamenPagado=true AND c.id=:id AND p=:persona");
        $consulta->setParameter('persona', $persona);
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }

    public function findPersonasCreditosAll(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e WHERE e.tipo='APROBADO' AND p.estadoPersona = 0  ORDER BY p.primerApellido");
        return $consulta->getResult();
    }

    public function findCreditosByPersona(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e WHERE e.tipo='APROBADO' AND p.estadoPersona = 0 ");
        return $consulta->getResult();
    }

    public function findCreditosByPersonaOrdDescPorFecha($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e WHERE p=:persona ORDER BY c.fechaSolicitud DESC ");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }

    public function findCreditosOtrogadosByPersonaOrdDescPorFecha($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e WHERE e.tipo='OTORGADO' AND p=:persona ORDER BY c.fechaSolicitud DESC ");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }
    public function findCreditosOtrogadosByPersona($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e WHERE e.tipo='OTORGADO' AND p=:persona");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }

    public function findCreditosOtrogadosByPersonaAndIDOrdDescPorFecha($persona,$id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:Creditos c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e LEFT JOIN c.idProductosDeCreditos pc LEFT JOIN pc.metodoAmortizacion x WHERE e.tipo='OTORGADO' AND p=:persona AND x.id =:id ORDER BY c.fechaSolicitud DESC ");
        $consulta->setParameter('persona', $persona);
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }

}