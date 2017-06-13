<?php

namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="credito_simular")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\CreditoSimularRepository")
 */
class CreditosSimular
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
     * @var string $frecuencia_pago
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\FrecuenciaDePagos")
     * @ORM\JoinColumn(name="frecuencia_pago", referencedColumnName="id")
     */
    private $frecuencia_pago;

    /**
     * @var datetime
     * @ORM\Column(name="fechaSolicitud", type="datetime",nullable=true)
     */
    private $fechaSolicitud;

    /**
     * @var integer
     * @ORM\Column(name="numeroDePagos", type="integer",nullable=true)     *
     */
    private $numeroDePagos;

    /**
     * @var string
     *
     * @ORM\Column(name="montoSolicitado", type="string", length=100,nullable=true)
     */
    private $montoSolicitado;

    /**
     * @var decimal
     *
     * @ORM\Column(name="interesAnual", precision=20, scale=10, nullable=true)
     */
    private $interesAnual;

    /**
     * @var string $id_productos_de_creditos
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\ProductoDeCreditos")
     * @ORM\JoinColumn(name="id_productos_de_creditos", referencedColumnName="id")
     */
    private $idProductosDeCreditos;


    /**
     * @var boolean $desgravamenPagado
     *
     * @ORM\Column(name="desgravamen_pagado", type="boolean", nullable=true)
     */
    private $desgravamenPagado = false;

    public function getId()
    {
        return $this->id;
    }

    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * @return string
     */
    public function getIdProductosDeCreditos()
    {
        return $this->idProductosDeCreditos;
    }

    /**
     * @param string $id_productos_de_creditos
     */
    public function setIdProductosDeCreditos($idProductosDeCreditos)
    {
        $this->idProductosDeCreditos = $idProductosDeCreditos;
    }


    public function getFrecuencia_pago()
    {
        return $this->frecuencia_pago;
    }

    public function getFechaSolicitud()
    {
        return $this->fechaSolicitud;
    }

    public function getNumeroDePagos()
    {
        return $this->numeroDePagos;
    }

    public function getMontoSolicitado()
    {
        return $this->montoSolicitado;
    }

    /**
     * @return decimal
     */
    public function getInteresAnual()
    {
        return $this->interesAnual;
    }

    /**
     * @param decimal $interesAnual
     */
    public function setInteresAnual($interesAnual)
    {
        $this->interesAnual = $interesAnual;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setPersona($persona)
    {
        $this->persona = $persona;
    }

    public function setFrecuencia_pago($frecuencia_pago)
    {
        $this->frecuencia_pago = $frecuencia_pago;
    }

    public function setFechaSolicitud($fechaSolicitud)
    {
        $this->fechaSolicitud = $fechaSolicitud;
    }

    public function setNumeroDePagos($numeroDePagos)
    {
        $this->numeroDePagos = $numeroDePagos;
    }

    public function setMontoSolicitado($montoSolicitado)
    {
        $this->montoSolicitado = $montoSolicitado;
    }


    public function __toString()
    {
        return $this->getId()."";
    }


    /**
     * Set frecuencia_pago
     *
     * @param \Modulos\PersonasBundle\Entity\FrecuenciaDePagos $frecuenciaPago
     * @return CreditosSimular
     */
    public function setFrecuenciaPago(\Modulos\PersonasBundle\Entity\FrecuenciaDePagos $frecuenciaPago = null)
    {
        $this->frecuencia_pago = $frecuenciaPago;

        return $this;
    }

    /**
     * Get frecuencia_pago
     *
     * @return \Modulos\PersonasBundle\Entity\FrecuenciaDePagos
     */
    public function getFrecuenciaPago()
    {
        return $this->frecuencia_pago;
    }

    /**
     * @return boolean
     */
    public function isDesgravamenPagado()
    {
        return $this->desgravamenPagado;
    }

    /**
     * @param boolean $desgravamenPagado
     */
    public function setDesgravamenPagado($desgravamenPagado)
    {
        $this->desgravamenPagado = $desgravamenPagado;
    }


}
