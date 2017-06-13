<?php

namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Table(name="ahorro_simular")
 * @ORM\Entity
 */
class AhorroSimular
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
     * @var string $persona
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Persona")
     * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
     */
    private $persona;

     /**
     * @var string $tipoAhorro
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\TipoAhorro")
     * @ORM\JoinColumn(name="tipoAhorro", referencedColumnName="id")
     */
    private $tipoAhorro;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="valorAhorrar", precision=20, scale=10, nullable=true)
     */
    private $valorAhorrar;
    

    /**
     * @var integer
     * @ORM\Column(name="cuotas", type="integer",nullable=true)     *
     */
    private $cuotas;
    
     /**
     * @var decimal
     *
     * @ORM\Column(name="tasaInteres", precision=20, scale=10, nullable=true)
     */
    private $tasaInteres;

    /**
     * @var string $frecuenciadepago
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\FrecuenciaDePagos")
     * @ORM\JoinColumn(name="frecuenciadepago", referencedColumnName="id")
     */
    private $frecuenciadepago;


    /**
     * @var datetime 
     *
     * @ORM\Column(name="fechaSolicitud", type="datetime",nullable=true)
     */

    private $fechaSolicitud;

    /**
     * @var datetime
     *
     * @ORM\Column(name="fechafinal", type="datetime",nullable=true)
     */

    private $fechafinal;

    /**
     * @var string $estadoAhorro
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\EstadoAhorro")
     * @ORM\JoinColumn(name="estadoAhorro", referencedColumnName="id")
     */

    private $estadoAhorro;

    /**
     * @return datetime
     */
    public function getFechafinal()
    {
        return $this->fechafinal;
    }

    /**
     * @param datetime
     */
    public function setFechafinal($fechafinal)
    {
        $this->fechafinal = $fechafinal;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * @param string $persona
     */
    public function setPersona($persona)
    {
        $this->persona = $persona;
    }

    /**
     * @return string
     */
    public function getTipoAhorro()
    {
        return $this->tipoAhorro;
    }

    /**
     * @param string
     */
    public function setTipoAhorro($tipoAhorro)
    {
        $this->tipoAhorro = $tipoAhorro;
    }

    /**
     * @return decimal
     */
    public function getvalorAhorrar()
    {
        return $this->valorAhorrar;
    }

    /**
     * @param decimal 
     */
    public function setValorAhorrar($valorAhorrar)
    {
        $this->valorAhorrar = $valorAhorrar;
    }

    /**
     * @return integer
     */
    public function getCuotas()
    {
        return $this->cuotas;
    }

    /**
     * @param integer 
     */
    public function setCuotas($cuotas)
    {
        $this->cuotas = $cuotas;
    }

    /**
     * @return decimal
     */
    public function getTasaInteres()
    {
        return $this->tasaInteres;
    }

    /**
     * @param decimal 
     */
    public function setTasaInteres($tasaInteres)
    {
        $this->tasaInteres = $tasaInteres;
    }

     /**
     * @return string
     */
    public function getFrecuenciaDePago()
    {
        return $this->frecuenciadepago;
    }

    /**
     * @param string 
     */
    public function setFrecuenciaDePago($frecuenciadepago)
    {
        $this->frecuenciadepago = $frecuenciadepago;
    }

     /**
     * @return datetime
     */
    public function getFechaSolicitud()
    {
        return $this->fechaSolicitud;
    }

    /**
     * @param datetime 
     */
    public function setFechaSolicitud($fechaSolicitud)
    {
        $this->fechaSolicitud = $fechaSolicitud;
    } 

    /**
     * @return string
     */
    public function getEstadoAhorro()
    {
        return $this->estadoAhorro;
    }

    /**
     * @param string 
     */
    public function setEstadoAhorro($estadoAhorro)
    {
        $this->estadoAhorro = $estadoAhorro;
    }



}
