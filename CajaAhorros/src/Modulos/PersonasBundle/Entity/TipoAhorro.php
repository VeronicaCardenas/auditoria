<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Table(name="tipoAhorro")
 * @ORM\Entity()
 */
class TipoAhorro 
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;


    /**
     * @var integer
     * @ORM\Column(name="plazo", type="integer")     *
     */
    private $plazo=0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="montoMinimo", precision=20, scale=10)
     */
    private $montoMinimo;

    /**
     * @var decimal
     *
     * @ORM\Column(name="montoMaximo", precision=20, scale=10)
     */
    private $montoMaximo;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tasaInteres", precision=20, scale=10)
     */
    private $tasaInteres;

    /**
     * @var integer $metodoAmortizacion
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\MetodoAmortizacionAhorro")
     * @ORM\JoinColumn(name="metodoAmortizacionAhorro", referencedColumnName="id")
     */
    private $metodoAmortizacionAhorro;

    /**
     * @return int
     */
    public function getPlazo()
    {
        return $this->plazo;
    }

    /**
     * @param int $plazo
     */
    public function setPlazo($plazo)
    {
        $this->plazo = $plazo;
    }

    /**
     * @return decimal
     */
    public function getMontominimo()
    {
        return $this->montoMinimo;
    }

    /**
     * @param decimal $montominimo
     */
    public function setMontominimo($montominimo)
    {
        $this->montoMinimo = $montominimo;
    }

    /**
     * @return decimal
     */
    public function getMontomaximo()
    {
        return $this->montoMaximo;
    }

    /**
     * @param decimal $montomaximo
     */
    public function setMontomaximo($montomaximo)
    {
        $this->montoMaximo = $montomaximo;
    }

    /**
     * @return decimal
     */
    public function getTasaInteres()
    {
        return $this->tasaInteres;
    }

    /**
     * @param decimal $tasadeinteres
     */
    public function setTasaInteres($tasainteres)
    {
        $this->tasaInteres = $tasainteres;
    }



    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    /**
     * @return int
     */
    public function getMetodoAmortizacionAhorro()
    {
        return $this->metodoAmortizacionAhorro;
    }

    /**
     * @param int $metodoAmortizacionAhorro
     */
    public function setMetodoAmortizacionAhorro($metodoAmortizacionAhorro)
    {
        $this->metodoAmortizacionAhorro = $metodoAmortizacionAhorro;
    }



    
    
    function __toString() {
       return $this->nombre ;
    }

    
}
