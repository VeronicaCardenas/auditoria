<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 05/02/2016
 * Time: 9:57
 */ 
 
 namespace Modulos\PersonasBundle\Entity\Repository;
 use Doctrine\ORM\EntityRepository;


 class CreditoSimularRepository extends EntityRepository{

     public function findPersonasCreditosDesgravSim(){
         $em = $this->getEntityManager();
         $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:CreditosSimular c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e LEFT JOIN c.idProductosDeCreditos pc WHERE e.tipo='APROBADO' AND c.desgravamenPagado=false AND pc.id=1");
         return $consulta->getResult();
     }

     public function findPersonasCreditosSim(){
         $em = $this->getEntityManager();
         $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:CreditosSimular c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e LEFT JOIN c.idProductosDeCreditos pc WHERE e.tipo='APROBADO' AND c.desgravamenPagado=true AND pc.id!=2");
         return $consulta->getResult();
     }

     public function findPersonasCreditosEmergentesSim(){
         $em = $this->getEntityManager();
         $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:CreditosSimular c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e LEFT JOIN c.idProductosDeCreditos pc WHERE e.tipo='APROBADO' AND pc.id=2");
         return $consulta->getResult();
     }

     public function findPersonasCreditosAllSim(){
         $em = $this->getEntityManager();
         $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:CreditosSimular c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e WHERE e.tipo='APROBADO'");
         return $consulta->getResult();
     }

     public function findCreditosByPersonaSim(){
         $em = $this->getEntityManager();
         $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:CreditosSimular c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e WHERE e.tipo='APROBADO'");
         return $consulta->getResult();
     }

     public function findCreditosByPersonaOrdDescPorFechaSim($persona){
         $em = $this->getEntityManager();
         $consulta = $em->createQuery("SELECT c FROM ModulosPersonasBundle:0. c LEFT JOIN c.persona p LEFT JOIN c.estadocreditos e WHERE p=:persona ORDER BY c.fechaSolicitud DESC ");
         $consulta->setParameter('persona', $persona);
         return $consulta->getResult();
     }

 }