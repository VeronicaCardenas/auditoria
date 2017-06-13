<?php

namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="documento_contable")
 * @ORM\Entity()
 */
class DocumentoContable
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
     * @var string $cuenta
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Cuenta")
     * @ORM\JoinColumn(name="cuenta_id", referencedColumnName="id")
     */
    private $cuenta;

    /**
     * @var double
     *
     * @ORM\Column(name="valor", precision=20, scale=10, nullable=true)
     */
    private $valor;


    /**
     * @var datetime
     * @ORM\Column(name="fecha", type="datetime",nullable=true)
     */
    private $fecha;

    /**
     * @var string $tipo_producto_contable
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\TipoProductoContable")
     * @ORM\JoinColumn(name="tipo_producto_contable", referencedColumnName="id")
     */
    private $tipo_producto_contable;

    /**
     * @var string $estado_documento
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\EstadoDocumento")
     * @ORM\JoinColumn(name="estado_documento", referencedColumnName="id")
     */

    private $estado_documento;

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
    public function getCuenta()
    {
        return $this->cuenta;
    }

    /**
     * @param string $cuenta
     */
    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }

    /**
     * @return float
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param float $valor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    /**
     * @return datetime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param datetime $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * @return string
     */
    public function getTipoProductoContable()
    {
        return $this->tipo_producto_contable;
    }

    /**
     * @param string $tipo_producto_contable
     */
    public function setTipoProductoContable($tipo_producto_contable)
    {
        $this->tipo_producto_contable = $tipo_producto_contable;
    }

    /**
     * @return string
     */
    public function getEstadoDocumento()
    {
        return $this->estado_documento;
    }

    /**
     * @param string $estado_documento
     */
    public function setEstadoDocumento($estado_documento)
    {
        $this->estado_documento = $estado_documento;
    }



}
