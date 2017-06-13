<?php

namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="producto_documento_afectacion")
 * @ORM\Entity()
 */
class ProductoDocumentoAfectacion
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
     * @var string $tipo_producto_contable
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\TipoProductoContable")
     * @ORM\JoinColumn(name="tipo_producto_contable_id", referencedColumnName="id")
     */
    private $tipo_producto_contable;


    /**
     * @var string $cuenta
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Cuenta")
     * @ORM\JoinColumn(name="cuenta_id", referencedColumnName="id")
     */
    private $cuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="operador", type="string", length=100,nullable=true)
     */
    private $operador;


    /**
     * @var decimal
     *
     * @ORM\Column(name="valor", precision=20, scale=10, nullable=true)
     */
    private $valor;

    /**
     * @var double
     *
     * @ORM\Column(name="porciento", precision=20, scale=10, nullable=true)
     */
    private $porciento;

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
     * @return string
     */
    public function getOperador()
    {
        return $this->operador;
    }

    /**
     * @param string $operador
     */
    public function setOperador($operador)
    {
        $this->operador = $operador;
    }

    /**
     * @return decimal
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param decimal $valor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    /**
     * @return float
     */
    public function getPorciento()
    {
        return $this->porciento;
    }

    /**
     * @param float $porciento
     */
    public function setPorciento($porciento)
    {
        $this->porciento = $porciento;
    }



}
