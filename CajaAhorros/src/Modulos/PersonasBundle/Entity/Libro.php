<?php

namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="libro")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\LibroRepository")
 */
class Libro
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var datetime
     * @ORM\Column(name="fecha", type="datetime",nullable=true)
     */
    private $fecha;

     /**     
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\TipoProductoContable")
     * @ORM\JoinColumn(name="producto_contable_id", referencedColumnName="id")
     */
    private $productocontableid;

    /**
     * @var string $persona
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Persona")
     * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
     */
    private $persona;

        
   /**     
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Cuenta")
     * @ORM\JoinColumn(name="cuenta_id", referencedColumnName="id")
     */
    private $cuentaid;

         
    /**
     * @var string
     *
     * @ORM\Column(name="numeroRecibo", type="integer", nullable=true)
     */
    private $numeroRecibo;



    /**
     * @var decimal
     *
     * @ORM\Column(name="debe", precision=20, scale=10, nullable=true)
     */
    private $debe;

    /**
     * @var decimal
     *
     * @ORM\Column(name="haber", precision=20, scale=10, nullable=true)
     */
    private $haber;

    /**
     * @var decimal
     *
     * @ORM\Column(name="saldo", precision=20, scale=10, nullable=true)
     */
    private $saldo;

     /**     
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\EstadosLibro")
     * @ORM\JoinColumn(name="estadoslibroid", referencedColumnName="id")
     */
    private $estadoslibroid;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="string", length=100,nullable=true)
     */
    private $info="-1";

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }


    public function getFecha()
    {
        return $this->fecha;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function getProductoContableId() {
        return $this->productocontableid;
    }
    public function setProductoContableId($producto_contable_id) {
        $this->productocontableid = $producto_contable_id;
    }
   
     public function getPersona()
    {
        return $this->persona;
    }

    public function setPersona($persona)
    {
        $this->persona = $persona;
    }

    public function getCuentaid() {
        return $this->cuentaid;
    }
    public function setCuentaid($cuentaid) {
        $this->cuentaid = $cuentaid;
    }

    public function getNumeroRecibo() {
        return $this->numeroRecibo;
    }
    public function setNumeroRecibo($numeroRecibo) {
        $this->numeroRecibo = $numeroRecibo;
    }
    public function getDebe() {
        return $this->debe;
    }
    public function setDebe($debe) {
        $this->debe = $debe;
    }
    public function getHaber() {
        return $this->haber;
    }
    public function setHaber($haber) {
        $this->haber = $haber;
    }
    public function getSaldo() {
        return $this->saldo;
    }
    public function setSaldo($saldo) {
        $this->saldo = $saldo;
    }
    
    public function getEstadosLibro() {
        return $this->estadoslibroid;
    }
    public function setEstadosLibro($estadoslibroid) {
        $this->estadoslibroid = $estadoslibroid;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }



    function __toString() {
        return $this->getNumeroRecibo();
    }
}