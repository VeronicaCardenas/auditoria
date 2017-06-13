<?php

namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MetodoDeAmortizacion
 * @ORM\Table(name="metodoamortizacionahorro")
 * @ORM\Entity
 */
class MetodoAmortizacionAhorro
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
     * @ORM\Column(name="metodo", type="string", length=255)
     */
    private $metodo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_metodo", type="string", length=255)
     */
    private $nombreMetodo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var bolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo=true;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tipo
     *
     * @param string $metodo
     * @return TipoCuenta
     */
    public function setMetodo($metodo)
    {
        $this->metodo = $metodo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getMetodo()
    {
        return $this->metodo;
    }


    /**
     * @return string
     */
    public function getNombreMetodo()
    {
        return $this->nombreMetodo;
    }

    /**
     * @param string $nombreMetodo
     */
    public function setNombreMetodo($nombreMetodo)
    {
        $this->nombreMetodo = $nombreMetodo;
    }

    /**
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }


    /**
     * @return bolean
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * @param bolean $activo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    public function __toString(){
        return $this->metodo;
    }
}
