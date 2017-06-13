<?php

namespace Modulos\PersonasBundle\Entity;


class AhorroPersonaMes
{
    private $persona;
    private $saldoAnterior=0;
    private $ahorroVista=0;
    private $ahorroPlazoFijo=0;
    private $ahorroRestringido=0;
    private $retiroAhorroVista=0;
    private $retiroAhorroPlazoFijo=0;
    private $retiroAhorroRestringido=0;
    private $interesPagado=0;
    private $creditoMicroEmpPorVencerSaldoCap=0;
    private $cuenta=0;
    private $saldoFinalAhorros=0;
    private $totalPagado=0;

    public function getPersona()
    {
        return $this->persona;
    }

    public function setPersona(\Modulos\PersonasBundle\Entity\Persona $persona=null)
    {
        $this->persona = $persona;
    }
    
    public function getCuenta()
    {
        return $this->cuenta;
    }

    public function setCuenta(\Modulos\PersonasBundle\Entity\Cuenta $cuenta)
    {
        $this->cuenta = $cuenta;
    }


    public function getSaldoAnterior()
    {
        return $this->saldoAnterior;
    }

    public function setSaldoAnterior($saldoAnterior)
    {
        $this->saldoAnterior = $saldoAnterior;
    }
    
    public function getAhorroVista()
    {
        return $this->ahorroVista;
    }

    public function setAhorroVista($ahorroVista)
    {
        $this->ahorroVista = $ahorroVista;
    }
    
    public function getAhorroPlazoFijo()
    {
        return $this->ahorroPlazoFijo;
    }

    public function setAhorroPlazoFijo($ahorroPlazoFijo)
    {
        $this->ahorroPlazoFijo = $ahorroPlazoFijo;
    }
    
    public function getAhorroRestringido()
    {
        return $this->ahorroRestringido;
    }

    public function setAhorroRestringido($ahorroRestringido)
    {
        $this->ahorroRestringido = $ahorroRestringido;
    }
    
    public function getRetiroAhorroVista()
    {
        return $this->retiroAhorroVista;
    }

    public function setRetiroAhorroVista($retiroAhorroVista)
    {
        $this->retiroAhorroVista = $retiroAhorroVista;
    }
    
    public function getRetiroAhorroPlazoFijo()
    {
        return $this->retiroAhorroPlazoFijo;
    }

    public function setRetiroAhorroPlazoFijo($retiroAhorroPlazoFijo)
    {
        $this->retiroAhorroPlazoFijo = $retiroAhorroPlazoFijo;
    }
    
    public function getRetiroAhorroRestringido()
    {
        return $this->retiroAhorroRestringido;
    }

    public function setRetiroAhorroRestringido($retiroAhorroRetringido)
    {
        $this->retiroAhorroRestringido = $retiroAhorroRetringido;
    }
    
    public function getCreditoMicroEmpPorVencerSaldoCap()
    {
        return $this->creditoMicroEmpPorVencerSaldoCap;
    }

    public function setCreditoMicroEmpPorVencerSaldoCap($creditoMicroEmpPorVencerSaldoCap)
    {
        $this->creditoMicroEmpPorVencerSaldoCap = $creditoMicroEmpPorVencerSaldoCap;
    }
    
    public function getInteresPagado()
    {
        return $this->interesPagado;
    }

    public function setInteresPagado($interesPagado)
    {
        $this->interesPagado = $interesPagado;
    }
    
    public function getSaldoFinalAhorros()
    {
        return $this->saldoFinalAhorros;
    }

    public function setSaldoFinalAhorros($saldoFinalAhorros)
    {
        $this->saldoFinalAhorros = $saldoFinalAhorros;
    }
    
    public function getTotalPagado()
    {
        return $this->totalPagado;
    }

    public function setTotalPagado($totalPagado)
    {
        $this->totalPagado = $totalPagado;
    }
        
    public function updateTotalPagado(){
        $this->totalPagado = $this->ahorroVista + $this->saldoAnterior - $this->retiroAhorroVista;
    }
    
    
    public function updateSaldoFinalAhorros(){
        $this->saldoFinalAhorros = $this->ahorroPlazoFijo + $this->ahorroRestringido + $this->totalPagado -$this->retiroAhorroPlazoFijo-$this->retiroAhorroRestringido;
    }

    public function getNombreCompleto(){
        return (($this->persona->getPrimerApellido()== null ? " " : $this->persona->getPrimerApellido())." ".($this->persona->getSegundoApellido()== null ? " " : $this->persona->getSegundoApellido())." ".($this->persona->getNombre()== null ? " " : $this->persona->getNombre()));
    }


}