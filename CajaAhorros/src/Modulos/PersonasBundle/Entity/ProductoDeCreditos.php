<?php

namespace Modulos\PersonasBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Table(name="productos_de_creditos")
 * @ORM\Entity()
 */
class ProductoDeCreditos 
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
     * @ORM\Column(name="tipo", type="string", length=100,nullable=true)
     */
    private $productocredito;


    /**
     * @var integer
     * @ORM\Column(name="plazo", type="integer",nullable=true)     *
     */
    private $plazo;

    /**
     * @var integer
     * @ORM\Column(name="plazo_max", type="integer",nullable=true)     *
     */
    private $plazoMax;

    /**
     * @var decimal
     *
     * @ORM\Column(name="montominimo", precision=20, scale=10)
     */
    private $montominimo;

    /**
     * @var decimal
     *
     * @ORM\Column(name="montomaximo", precision=20, scale=10)
     */
    private $montomaximo;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tasadeinteres", precision=20, scale=10)
     */
    private $tasadeinteres;

    /**
     * @var decimal
     *
     * @ORM\Column(name="desgravamen", precision=20, scale=10)
     */
    private $desgravamen;

    /**
     * @var integer $metodoAmortizacion
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\MetodoDeAmortizacion")
     * @ORM\JoinColumn(name="metodoDeAmortizacion", referencedColumnName="id")
     */
    private $metodoAmortizacion;

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
     * @return int
     */
    public function getPlazoMax()
    {
        return $this->plazoMax;
    }

    /**
     * @param int $plazoMax
     */
    public function setPlazoMax($plazoMax)
    {
        $this->plazoMax = $plazoMax;
    }

    /**
     * @return decimal
     */
    public function getMontominimo()
    {
        return $this->montominimo;
    }

    /**
     * @param decimal $montominimo
     */
    public function setMontominimo($montominimo)
    {
        $this->montominimo = $montominimo;
    }

    /**
     * @return decimal
     */
    public function getMontomaximo()
    {
        return $this->montomaximo;
    }

    /**
     * @param decimal $montomaximo
     */
    public function setMontomaximo($montomaximo)
    {
        $this->montomaximo = $montomaximo;
    }

    /**
     * @return decimal
     */
    public function getTasadeinteres()
    {
        return $this->tasadeinteres;
    }

    /**
     * @param decimal $tasadeinteres
     */
    public function setTasadeinteres($tasadeinteres)
    {
        $this->tasadeinteres = $tasadeinteres;
    }



    public function getId() {
        return $this->id;
    }

    public function getProductocredito() {
        return $this->productocredito;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setProductocredito($productocredito) {
        $this->productocredito = $productocredito;
    }

    /**
     * @return decimal
     */
    public function getDesgravamen()
    {
        return $this->desgravamen;
    }

    /**
     * @param decimal $desgravamen
     */
    public function setDesgravamen($desgravamen)
    {
        $this->desgravamen = $desgravamen;
    }

    /**
     * @return int
     */
    public function getMetodoAmortizacion()
    {
        return $this->metodoAmortizacion;
    }

    /**
     * @param int $metodoAmortizacion
     */
    public function setMetodoAmortizacion($metodoAmortizacion)
    {
        $this->metodoAmortizacion = $metodoAmortizacion;
    }

    function __toString() {
       return $this->productocredito ;
    }

    
}
