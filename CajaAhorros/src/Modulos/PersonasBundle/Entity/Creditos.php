<?php

namespace Modulos\PersonasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Table(name="credito")
 * @ORM\Entity(repositoryClass="Modulos\PersonasBundle\Entity\Repository\CreditosRepository")
 */
class Creditos
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
     * @var string $persona
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\Persona")
     * @ORM\JoinColumn(name="garante_id", referencedColumnName="id")
     */
    private $garante;


    /**
     * @var string $frecuencia_pago
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\FrecuenciaDePagos")
     * @ORM\JoinColumn(name="frecuencia_pago", referencedColumnName="id")
     */
    private $frecuencia_pago;


    /**
     * @var string $tipo_parcialidad
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\TipoDeParcialidad")
     * @ORM\JoinColumn(name="tipo_parcialidad", referencedColumnName="id")
     */
    private $tipo_parcialidad;

    /**
     * @var datetime
     * @ORM\Column(name="fechaDesembolso", type="datetime",nullable=true)
     */
    private $fechaDesembolso;

    /**
     * @var datetime
     * @ORM\Column(name="fechaVencimiento", type="datetime",nullable=true)
     */
    private $fechaVencimiento;


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
     * @var string
     *
     * @ORM\Column(name="montoEnLetras", type="string", length=100,nullable=true)
     */
    private $montoEnLetras;


    /**
     * @var string $destino_financiamiento
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\DestinoFinanciamiento")
     * @ORM\JoinColumn(name="destino_financiamiento", referencedColumnName="id")
     */
    private $destino_financiamiento;


    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=100,nullable=true)
     */
    private $observaciones;


    /**
     * @var string $tipo_cobro
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\TipoDeCobro")
     * @ORM\JoinColumn(name="destino_financiamiento", referencedColumnName="id")
     */
    private $tipo_cobro;


    /**
     * @var string $id_productos_de_creditos
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\ProductoDeCreditos")
     * @ORM\JoinColumn(name="id_productos_de_creditos", referencedColumnName="id")
     */
    private $idProductosDeCreditos;

    /**
     * @var string $id_MetodoDeAmortizacion
     *
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\MetodoDeAmortizacion")
     * @ORM\JoinColumn(name="id_MetodoDeAmortizacion", referencedColumnName="id")
     */
    private $id_MetodoDeAmortizacion;

    /**
     * @var string
     *
     * @ORM\Column(name="estadoCredito", type="boolean", nullable=true)
     */
    private $estadoCredito = false;

    /**
     * @var decimal
     *
     * @ORM\Column(name="gastoAdministrativo", type="decimal", precision=20, scale=10, nullable=true)
     */
    private $gastoAdministrativo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcionGasto", type="string",length=5000, nullable=true)
     */
    private $descripcionGasto;

    /**
     * @var string
     *
     * @ORM\Column(name="noIdentidadBeneficiario", type="string", nullable=true)
     */
    private $noIdentidadBeneficiario;
    /**
     * @var string
     *
     * @ORM\Column(name="nombreBeneficiario", type="string", nullable=true)
     */
    private $nombreBeneficiario;

    /**
     * @var string $estadocreditos
     * @ORM\ManyToOne(targetEntity="Modulos\PersonasBundle\Entity\EstadoCreditos")
     * @ORM\JoinColumn(name="estadocreditos", referencedColumnName="id")
     */

    private $estadocreditos;


    /**
     * @var boolean $sininteres
     * @ORM\Column(name="sininteres", type="boolean",nullable=true)
     */
    private $sininteres=false;

        /**
     * @var boolean $desgravamenPagado
     *
     * @ORM\Column(name="desgravamen_pagado", type="boolean", nullable=true)
     */
    private $desgravamenPagado = false;


    /**
     * @param string $descripcionGasto
     */

    public function setDescripcionGasto($descripcionGasto)
    {
        $this->descripcionGasto = $descripcionGasto;
    }

    /**
     * @return string
     */
    public function getDescripcionGasto()
    {
        return $this->descripcionGasto;
    }

    /**
     * @param \Modulos\PersonasBundle\Entity\decimal $gastoAdministrativo
     */
    public function setGastoAdministrativo($gastoAdministrativo)
    {
        $this->gastoAdministrativo = $gastoAdministrativo;
    }

    /**
     * @return \Modulos\PersonasBundle\Entity\decimal
     */
    public function getGastoAdministrativo()
    {
        return $this->gastoAdministrativo;
    }

    /**
     * @param string $noIdentidadBeneficiario
     */
    public function setNoIdentidadBeneficiario($noIdentidadBeneficiario)
    {
        $this->noIdentidadBeneficiario = $noIdentidadBeneficiario;
    }

    /**
     * @return string
     */
    public function getNoIdentidadBeneficiario()
    {
        return $this->noIdentidadBeneficiario;
    }

    /**
     * @param string $nombreBeneficiario
     */
    public function setNombreBeneficiario($nombreBeneficiario)
    {
        $this->nombreBeneficiario = $nombreBeneficiario;
    }

    /**
     * @return string
     */
    public function getNombreBeneficiario()
    {
        return $this->nombreBeneficiario;
    }

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
    public function getIdMetodoDeAmortizacion()
    {
        return $this->id_MetodoDeAmortizacion;
    }

    /**
     * @param string $id_MetodoDeAmortizacion
     */
    public function setIdMetodoDeAmortizacion($id_MetodoDeAmortizacion)
    {
        $this->id_MetodoDeAmortizacion = $id_MetodoDeAmortizacion;
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

    public function getTipo_parcialidad()
    {
        return $this->tipo_parcialidad;
    }


    public function getFechaDesembolso()
    {
        return $this->fechaDesembolso;
    }

    public function getFechaVencimiento()
    {
        return $this->fechaVencimiento;
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


    public function getMontoEnLetras()
    {
        return $this->montoEnLetras;
    }

    public function getDestino_financiamiento()
    {
        return $this->destino_financiamiento;
    }



    public function getObservaciones()
    {
        return $this->observaciones;
    }

    public function getTipo_cobro()
    {
        return $this->tipo_cobro;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setPersona($persona)
    {
        $this->persona = $persona;
    }

    public function setGarante($garante)
    {
        $this->garante = $garante;
    }

    /**
     * @return string
     */
    public function getGarante()
    {
        return $this->garante;
    }

    /**
     * @return string
     */
    public function getEstadocreditos()
    {
        return $this->estadocreditos;
    }

    /**
     * @param string $estadocreditos
     */
    public function setEstadocreditos($estadocreditos)
    {
        $this->estadocreditos = $estadocreditos;
    }

    /*/**
     * @return boolean
     */
    public function getEstadoCredito()
    {
        return $this->estadoCredito;
    }

    /**
     * @param string $estadoCredito
     */
    public function setEstadoCredito($estadoCredito)
    {
        $this->estadoCredito = $estadoCredito;
    }

    public function setFrecuencia_pago($frecuencia_pago)
    {
        $this->frecuencia_pago = $frecuencia_pago;
    }

    public function setTipo_parcialidad($tipo_parcialidad)
    {
        $this->tipo_parcialidad = $tipo_parcialidad;
    }


    public function setFechaDesembolso($fechaDesembolso)
    {
        $this->fechaDesembolso = $fechaDesembolso;
    }

    public function setFechaVencimiento($fechaVencimiento)
    {
        $this->fechaVencimiento = $fechaVencimiento;
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

    public function setMontoEnLetras($montoEnLetras)
    {
        $this->montoEnLetras = $montoEnLetras;
    }

    public function setDestino_financiamiento($destino_financiamiento)
    {
        $this->destino_financiamiento = $destino_financiamiento;
    }



    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    }

    public function setTipo_cobro($tipo_cobro)
    {
        $this->tipo_cobro = $tipo_cobro;
    }


    public function __toString()
    {
        return $this->getId()."";
    }


    /**
     * Set frecuencia_pago
     *
     * @param \Modulos\PersonasBundle\Entity\FrecuenciaDePagos $frecuenciaPago
     * @return Creditos
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
     * Set tipo_parcialidad
     *
     * @param \Modulos\PersonasBundle\Entity\TipoDeParcialidad $tipoParcialidad
     * @return Creditos
     */
    public function setTipoParcialidad(\Modulos\PersonasBundle\Entity\TipoDeParcialidad $tipoParcialidad = null)
    {
        $this->tipo_parcialidad = $tipoParcialidad;

        return $this;
    }

    /**
     * Get tipo_parcialidad
     *
     * @return \Modulos\PersonasBundle\Entity\TipoDeParcialidad
     */
    public function getTipoParcialidad()
    {
        return $this->tipo_parcialidad;
    }

    /**
     * Set destino_financiamiento
     *
     * @param \Modulos\PersonasBundle\Entity\DestinoFinanciamiento $destinoFinanciamiento
     * @return Creditos
     */
    public function setDestinoFinanciamiento(
        \Modulos\PersonasBundle\Entity\DestinoFinanciamiento $destinoFinanciamiento = null
    ) {
        $this->destino_financiamiento = $destinoFinanciamiento;

        return $this;
    }

    /**
     * Get destino_financiamiento
     *
     * @return \Modulos\PersonasBundle\Entity\DestinoFinanciamiento
     */
    public function getDestinoFinanciamiento()
    {
        return $this->destino_financiamiento;
    }

    /**
     * Set tipo_cobro
     *
     * @param \Modulos\PersonasBundle\Entity\TipoDeCobro $tipoCobro
     * @return Creditos
     */
    public function setTipoCobro(\Modulos\PersonasBundle\Entity\TipoDeCobro $tipoCobro = null)
    {
        $this->tipo_cobro = $tipoCobro;

        return $this;
    }

    /**
     * Get tipo_cobro
     *
     * @return \Modulos\PersonasBundle\Entity\TipoDeCobro
     */
    public function getTipoCobro()
    {
        return $this->tipo_cobro;
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

    /**
     * @return boolean
     */
    public function getSininteres()
    {
        return $this->sininteres;
    }

    /**
     * @param boolean $sininteres
     */
    public function setSininteres($sininteres)
    {
        $this->sininteres = $sininteres;
    }

}
