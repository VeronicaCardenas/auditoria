<?php
/**
 * Created by PhpStorm.
 * User: Soft
 * Date: 2/12/15
 * Time: 07:09 PM
 */

namespace Modulos\PersonasBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;


class LibroRepository extends  EntityRepository{


    public  function findDebe($id){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l.debe, l.haber, l.saldo, l.info  FROM ModulosPersonasBundle:Libro l WHERE l.id=:id");
        $consulta->setParameter('id', $id);

        return $consulta->getSingleResult();
    }

    public  function findLibrosOrdenados($fechaInicial,$fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.fecha<=:fechafin AND l.fecha>=:fechaini ORDER BY l.fecha ASC");
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);

        return $consulta->getResult();
    }
    public  function findLibrosOrdenadosEntreFechaDesc($fechaInicial,$fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.fecha<=:fechafin AND l.fecha>=:fechaini ORDER BY l.fecha DESC");
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);

        return $consulta->getResult();
    }

    public  function findLibrosOrdenadosEntreFechaDescId($fechaInicial,$fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.fecha<:fechafin AND l.fecha>=:fechaini ORDER BY l.fecha DESC, l.id DESC");
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);

        return $consulta->getResult();
    }

    public  function findLibrosOrdenadosEntreFechaAscId($fechaInicial,$fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.fecha<=:fechafin AND l.fecha>=:fechaini ORDER BY l.fecha ASC, l.id ASC");
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);

        return $consulta->getResult();
    }

    public  function findLibrosOrdenadosASC(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l  ORDER BY l.fecha DESC");
        
        return $consulta->getResult();
    }

    public  function findLibrosOrdenadosDESC(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l  ORDER BY l.fecha DESC, l.id DESC");

        return $consulta->getResult();
    }

    public  function findLibrosOrdenadosReciboDESC(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l  ORDER BY l.numeroRecibo DESC");

        return $consulta->getResult();
    }

    public  function findLibrosOrdenadosASCEND(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l  ORDER BY l.fecha ASC , l.id ASC");

        return $consulta->getResult();
    }

    public  function findLibrosOrdenadosPorfechaXtransaccion($idTransaccion,$fechaInicial,$fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.fecha<=:fechafin AND l.fecha>=:fechaini AND l.productocontableid=:idTransaccion ORDER BY l.fecha ASC");
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);
        $consulta->setParameter('idTransaccion', $idTransaccion);

        return $consulta->getResult();
    }

    public  function findLibrosOrdenadosPorPersonaTipoCartera($fechaInicial,$fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.fecha<=:fechafin AND l.fecha>=:fechaini AND ( l.productocontableid=3 OR l.productocontableid=4 OR  l.productocontableid=5 OR l.productocontableid=9 OR l.productocontableid=10) AND l.persona>0  ORDER BY l.persona DESC");
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);

        return $consulta->getResult();
    }

    public  function findLibrosOrdenadosSinFechaTipoCarteraInd(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE (l.productocontableid=3 OR l.productocontableid=4 OR  l.productocontableid=5 OR l.productocontableid=9 OR l.productocontableid=10) AND l.persona>0 ORDER BY l.persona DESC");
        return $consulta->getResult();
    }

    public  function findLibrosOrdenadosSinFechaTipoCartera(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.productocontableid=3 OR l.productocontableid=4 OR  l.productocontableid=5 OR l.productocontableid=9 OR l.productocontableid=10 OR l.productocontableid=11 ORDER BY l.fecha ASC");
        return $consulta->getResult();
    }

    public  function findSolicitudesActivasPorPersona($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l LEFT JOIN l.persona p LEFT JOIN l.productocontableid pc WHERE p=:persona AND pc.id=2 AND l.info='-1' ORDER BY l.fecha ASC");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }

    public  function findPersonasAporte(){
        $em = $this->getEntityManager();
        $consulta =     $em->createQuery("SELECT DISTINCT l FROM ModulosPersonasBundle:Libro l LEFT JOIN l.persona p LEFT JOIN l.productocontableid pc WHERE (pc.id=1 OR pc.id=18) GROUP BY p.id ORDER BY p.primerApellido");
        return $consulta->getResult();
    }

    ///////////////////
    public  function findPersonasCreditos(){
        $em = $this->getEntityManager();
        $consulta =     $em->createQuery("SELECT p FROM ModulosPersonasBundle:Persona p WHERE EXISTS (SELECT l FROM ModulosPersonasBundle:Libro l LEFT JOIN l.persona pe LEFT JOIN l.productocontableid pc WHERE (pc.id=3 OR pc.id=4 OR pc.id=5 OR pc.id=9 OR pc.id=10 OR pc.id=11 ) AND p.id = pe.id ORDER BY l.fecha ASC) ORDER BY p.primerApellido ASC");
        return $consulta->getResult();
    }

    /*public  function findPersonasCreditos(){
        $em = $this->getEntityManager();
        $consulta =     $em->createQuery("SELECT p FROM ModulosPersonasBundle:Persona p WHERE EXISTS 
                                          (SELECT l FROM ModulosPersonasBundle:Creditos l LEFT JOIN l.persona pe 
                                          WHERE p.id = pe.id ORDER BY l.numeroDePagos ASC) ");
        return $consulta->getResult();
    }*/

    public  function findPersonasXFechaDeCreditos($fecha){
        $em = $this->getEntityManager();
        $consulta =     $em->createQuery("SELECT p FROM ModulosPersonasBundle:Persona p WHERE EXISTS (
                                          SELECT l FROM ModulosPersonasBundle:Creditos l LEFT JOIN l.persona pe 
                                          LEFT JOIN l.productocontableid pc 
                                          WHERE (pc.id=3 OR pc.id=4 OR pc.id=5 OR pc.id=9 OR pc.id=10 OR pc.id=11) 
                                          AND p.id = pe.id AND l.fechaSolicitud LIKE :fecha ) ORDER BY p.primerApellido");
        $consulta->setParameter('fecha', $fecha.'-%');
        return $consulta->getResult();
    }

    public  function findPersonasCreditosXFecha($fecha){
        $em = $this->getEntityManager();
        $consulta =     $em->createQuery("SELECT p FROM ModulosPersonasBundle:Persona p WHERE EXISTS (SELECT l FROM ModulosPersonasBundle:Libro l LEFT JOIN l.persona pe LEFT JOIN l.productocontableid pc WHERE (pc.id=3 OR pc.id=4 OR pc.id=5 OR pc.id=9 OR pc.id=10 OR pc.id=11) AND p.id = pe.id AND l.fecha LIKE :fecha ) ORDER BY p.primerApellido");
        $consulta->setParameter('fecha', $fecha.'-%');
        return $consulta->getResult();
    }

    public  function findPersonasAhorros(){
        $em = $this->getEntityManager();
        $consulta =     $em->createQuery("SELECT p FROM ModulosPersonasBundle:Persona p WHERE EXISTS (SELECT l FROM ModulosPersonasBundle:Libro l LEFT JOIN l.persona pe LEFT JOIN l.productocontableid pc WHERE (pc.id=12 OR pc.id=13 OR pc.id=14 OR pc.id=15 OR pc.id=16 OR pc.id=17) AND p.id = pe.id) ORDER BY p.primerApellido");
        return $consulta->getResult();
    }

    public  function findPersonasAporteInicial(){
        $em = $this->getEntityManager();
        $consulta =     $em->createQuery("SELECT p FROM ModulosPersonasBundle:Persona p WHERE NOT EXISTS (SELECT l FROM ModulosPersonasBundle:Libro l LEFT JOIN l.persona pe WHERE p.id = pe.id) ORDER BY p.primerApellido");
        return $consulta->getResult();
    }

    public  function findAporteInicialXpersona($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l LEFT JOIN l.persona p LEFT JOIN l.productocontableid pc WHERE p=:persona AND pc.id=18");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }
    ///////////////////

    public  function findAportePorPersona($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l
                                        LEFT JOIN l.persona p
                                        LEFT JOIN l.productocontableid pc
                                        WHERE p=:persona AND (pc.id=1 OR pc.id=18) ORDER BY l.fecha ASC");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }

    public  function findCarteraPorPersona($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l
                                        LEFT JOIN l.persona p
                                        LEFT JOIN l.productocontableid pc
                                        WHERE p=:persona AND (pc.id=10 OR pc.id=9 OR pc.id=5 OR pc.id=4 OR pc.id=3 OR pc.id=11 ) ORDER BY l.fecha ASC");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }

    public  function findAhorrosPorPersona($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l
                                        LEFT JOIN l.persona p
                                        LEFT JOIN l.productocontableid pc
                                        WHERE p=:persona AND (pc.id=12 OR pc.id=13 OR pc.id=14 OR pc.id=15 OR pc.id=16 OR pc.id=17 OR pc.id=20 ) ORDER BY l.fecha ASC");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }

    public  function findRetiroAportePorPersona($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l
                                        LEFT JOIN l.persona p
                                        LEFT JOIN l.productocontableid pc
                                        WHERE p=:persona AND (pc.id=21 OR pc.id=19) ORDER BY l.fecha ASC");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }

    /*public  function findAporteInicialPorPersonaEx($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l
                                        LEFT JOIN l.persona p
                                        LEFT JOIN l.productocontableid pc
                                        WHERE p=:persona AND pc.id=1

                                        ");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }*/

    public  function findAporteInicialPorPersona($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l
                                        LEFT JOIN l.persona p
                                        LEFT JOIN l.productocontableid pc
                                        WHERE p=:persona AND pc.id=18

                                        ");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }

    public  function findAportePorPersonaEntreFechasEx($persona, $fechaInicial, $fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l
                                        LEFT JOIN l.persona p
                                        LEFT JOIN l.productocontableid pc
                                      WHERE  l.fecha >=:fechaini AND l.fecha <=:fechafin AND p=:persona AND pc.id=1

                                      ");
//                                      AND l.fecha<=:fechafin AND l.fecha>=:fechaini ");
        $consulta->setParameter('persona', $persona);
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);
        return $consulta->getResult();
    }

    public  function findAportePorPersonaEntreFechas($persona, $fechaInicial, $fechaFinal){
    $em = $this->getEntityManager();
    $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l
                                        LEFT JOIN l.persona p
                                        LEFT JOIN l.productocontableid pc
                                      WHERE  l.fecha >=:fechaini AND l.fecha <=:fechafin AND p=:persona AND (pc.id=1 OR pc.id=18)

                                      ");
//                                      AND l.fecha<=:fechafin AND l.fecha>=:fechaini ");
    $consulta->setParameter('persona', $persona);
    $consulta->setParameter('fechaini', $fechaInicial);
    $consulta->setParameter('fechafin', $fechaFinal);
    return $consulta->getResult();
}

    public  function findRetiroAportePorPersonaEntreFechas($persona, $fechaInicial, $fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l
                                        LEFT JOIN l.persona p
                                        LEFT JOIN l.productocontableid pc
                                      WHERE  l.fecha >=:fechaini AND l.fecha <=:fechafin AND p=:persona AND (pc.id=21 OR pc.id=19)

                                      ");
//                                      AND l.fecha<=:fechafin AND l.fecha>=:fechaini ");
        $consulta->setParameter('persona', $persona);
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);
        return $consulta->getResult();
    }

    public  function findAporteOrdFechaOrdPersona(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l LEFT JOIN l.persona p LEFT JOIN l.productocontableid pc WHERE pc.id=1 ORDER BY l.fecha ASC, p.id ASC ");
        return $consulta->getResult();
    }
    public  function findAporteOrdFechaOrdPersonaEntreFechas($fechaInicial, $fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l LEFT JOIN l.persona p LEFT JOIN l.productocontableid pc WHERE pc.id=1 AND l.fecha<=:fechafin AND l.fecha>=:fechaini ORDER BY l.fecha ASC, p.id ASC ");
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);
        return $consulta->getResult();
    }

    public  function findLibrosCreditosOrdenadosSinFecha(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.productocontableid=4 OR  l.productocontableid=5 ORDER BY l.fecha ASC");
        return $consulta->getResult();
    }

    public  function findLibrosByTipoTransaccion($idTransaccion){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.productocontableid=:idTransaccion ORDER BY l.fecha ASC");
        $consulta->setParameter('idTransaccion', $idTransaccion);
        return $consulta->getResult();
    }
    public  function findLibrosByTipoTransaccionAndInfo($idTransaccion, $info){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.productocontableid=:idTransaccion AND l.info=:info ORDER BY l.fecha ASC");
        $consulta->setParameter('idTransaccion', $idTransaccion);
        $consulta->setParameter('info', $info);
        return $consulta->getResult();
    }
    public  function findLibrosMayoresNumeroRecibo($numeroRecibo){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.numeroRecibo >:numero ORDER BY l.numeroRecibo ASC");
        $consulta->setParameter('numero', $numeroRecibo);
        return $consulta->getResult();
    }
    public  function findLibrosByRecibo($numeroRecibo){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.numeroRecibo =:numero");
        $consulta->setParameter('numero', $numeroRecibo);
        return $consulta->getSingleResult();
    }
    
    public  function findLibrosOrdenadosAhorros(){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE (l.productocontableid=12 OR l.productocontableid=13 OR  l.productocontableid=14 OR l.productocontableid=15 OR l.productocontableid=16 OR l.productocontableid=17 OR l.productocontableid=20) ORDER BY l.fecha ASC");
        return $consulta->getResult();
    }

    public  function findLibrosOrdenadosPorPersonaTipoAhorro($fechaInicial,$fechaFinal){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l WHERE l.fecha<=:fechafin AND l.fecha>=:fechaini AND (l.productocontableid=12 OR l.productocontableid=13 OR  l.productocontableid=14 OR l.productocontableid=15 OR l.productocontableid=16 OR l.productocontableid=17 OR l.productocontableid=20) AND l.persona>0  ORDER BY l.persona ASC");
        $consulta->setParameter('fechaini', $fechaInicial);
        $consulta->setParameter('fechafin', $fechaFinal);

        return $consulta->getResult();
    }

    public  function findMultaPorPersona($persona){
        $em = $this->getEntityManager();
        $consulta = $em->createQuery("SELECT l FROM ModulosPersonasBundle:Libro l
                                        LEFT JOIN l.persona p
                                        LEFT JOIN l.productocontableid pc
                                        WHERE p=:persona AND (pc.id=11) ORDER BY l.fecha ASC");
        $consulta->setParameter('persona', $persona);
        return $consulta->getResult();
    }
} 