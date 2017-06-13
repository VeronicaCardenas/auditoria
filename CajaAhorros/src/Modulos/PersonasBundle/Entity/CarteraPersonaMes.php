<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 12/4/2015
 * Time: 10:14 AM
 */

namespace Modulos\PersonasBundle\Entity;


class CarteraPersonaMes
{
    private $persona;
    private $socio="S";
    private $saldoAnterior=0;
    private $capitalPagado=0;
    private $interesGanado=0;
    private $moraPagada=0;
    private $pagoDegravamen=0;
    private $totalPagado=0;
    private $creditoMes=0;
    private $creditoCant=0;
    private $cuenta=0;
    private $creditoMicroEmpPorVencerSaldoCap=0;
    private $creditoMicroEmpVencidaCapMora=0;
    private $creditoConsumoPorVencerSaldoCap=0;
    private $creditoConsumoVencidaCapMora=0;
    private $diasAtrazo=0;
    private $hombre=0;
    private $mujer=0;
    private $aporte=0;

    /**
     * @return mixed
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * @param mixed $persona
     */
    public function setPersona(\Modulos\PersonasBundle\Entity\Persona $persona=null)
    {
        $this->persona = $persona;
    }

    /**
     * @return string
     */
    public function getSocio()
    {
        return $this->socio;
    }

    /**
     * @param string $socio
     */
    public function setSocio($socio)
    {
        $this->socio = $socio;
    }

    /**
     * @return int
     */
    public function getSaldoAnterior()
    {
        return $this->saldoAnterior;
    }

    /**
     * @param int $saldoAnterior
     */
    public function setSaldoAnterior($saldoAnterior)
    {
        $this->saldoAnterior = $saldoAnterior;
    }

    /**
     * @return int
     */
    public function getCapitalPagado()
    {
        return $this->capitalPagado;
    }

    /**
     * @param int $capitalPagado
     */
    public function setCapitalPagado($capitalPagado)
    {
        $this->capitalPagado = $capitalPagado;
    }
    
    public function getAporte()
    {
        return $this->aporte;
    }

    /**
     * @param int $aporte
     */
    public function setAporte($aporte)
    {
        $this->aporte = $aporte;
    }
    
    /**
     * @return int
     */
    public function getInteresGanado()
    {
        return $this->interesGanado;
    }

    /**
     * @param int $interesGanado
     */
    public function setInteresGanado($interesGanado)
    {
        $this->interesGanado = $interesGanado;
    }

    /**
     * @return int
     */
    public function getMoraPagada()
    {
        return $this->moraPagada;
    }

    /**
     * @param int $moraPagada
     */
    public function setMoraPagada($moraPagada)
    {
        $this->moraPagada = $moraPagada;
    }



    /**
     * @return int
     */
    public function getPagoDegravamen()
    {
        return $this->pagoDegravamen;
    }

    /**
     * @param int $pagoDegravamen
     */
    public function setPagoDegravamen($pagoDegravamen)
    {
        $this->pagoDegravamen = $pagoDegravamen;
    }

    /**
     * @return int
     */
    public function getTotalPagado()
    {
        return $this->totalPagado;
    }

    /**
     * @param int $totalPagado
     */
    public function setTotalPagado($totalPagado)
    {
        $this->totalPagado = $totalPagado;
    }

    /**
     * @return int
     */
    public function getCreditoMes()
    {
        return $this->creditoMes;
    }

    /**
     * @param int $creditoMes
     */
    public function setCreditoMes($creditoMes)
    {
        $this->creditoMes = $creditoMes;
    }

    /**
     * @return int
     */
    public function getCreditoCant()
    {
        return $this->creditoCant;
    }

    /**
     * @param int $creditoCant
     */
    public function setCreditoCant($creditoCant)
    {
        $this->creditoCant = $creditoCant;
    }

    /**
     * @return int
     */
    public function getCuenta()
    {
        return $this->cuenta;
    }

    /**
     * @param int $tipoCredito
     */
    public function setCuenta(\Modulos\PersonasBundle\Entity\Cuenta $cuenta)
    {
        $this->cuenta = $cuenta;
    }

    /**
     * @return int
     */
    public function getCreditoMicroEmpPorVencerSaldoCap()
    {
        return $this->creditoMicroEmpPorVencerSaldoCap;
    }

    /**
     * @param int $creditoMicroEmpPorVencerSaldoCap
     */
    public function setCreditoMicroEmpPorVencerSaldoCap($creditoMicroEmpPorVencerSaldoCap)
    {
        $this->creditoMicroEmpPorVencerSaldoCap = $creditoMicroEmpPorVencerSaldoCap;
    }

    /**
     * @return int
     */
    public function getCreditoMicroEmpVencidaCapMora()
    {
        return $this->creditoMicroEmpVencidaCapMora;
    }

    /**
     * @param int $creditoMicroEmpVencidaCapMora
     */
    public function setCreditoMicroEmpVencidaCapMora($creditoMicroEmpVencidaCapMora)
    {
        $this->creditoMicroEmpVencidaCapMora = $creditoMicroEmpVencidaCapMora;
    }

    /**
     * @return int
     */
    public function getCreditoConsumoPorVencerSaldoCap()
    {
        return $this->creditoConsumoPorVencerSaldoCap;
    }

    /**
     * @param int $creditoConsumoPorVencerSaldoCap
     */
    public function setCreditoConsumoPorVencerSaldoCap($creditoConsumoPorVencerSaldoCap)
    {
        $this->creditoConsumoPorVencerSaldoCap = $creditoConsumoPorVencerSaldoCap;
    }

    /**
     * @return int
     */
    public function getCreditoConsumoVencidaCapMora()
    {
        return $this->creditoConsumoVencidaCapMora;
    }

    /**
     * @param int $creditoConsumoVencidaCapMora
     */
    public function setCreditoConsumoVencidaCapMora($creditoConsumoVencidaCapMora)
    {
        $this->creditoConsumoVencidaCapMora = $creditoConsumoVencidaCapMora;
    }

    /**
     * @return int
     */
    public function getDiasAtrazo()
    {
        return $this->diasAtrazo;
    }

    /**
     * @param int $diasAtrazo
     */
    public function setDiasAtrazo($diasAtrazo)
    {
        $this->diasAtrazo = $diasAtrazo;
    }

    /**
     * @return int
     */
    public function getHombre()
    {
        return $this->hombre;
    }

    /**
     * @param int $hombre
     */
    public function setHombre($hombre)
    {
        $this->hombre = $hombre;
    }

    /**
     * @return int
     */
    public function getMujer()
    {
        return $this->mujer;
    }

    /**
     * @param int $mujer
     */
    public function setMujer($mujer)
    {
        $this->mujer = $mujer;
    }


    public function updateTotalPagado(){
        $this->totalPagado=$this->capitalPagado + $this->interesGanado + $this->moraPagada + $this->pagoDegravamen;
    }

    public function getNombreCompleto(){
        return (($this->persona->getNombre()== null ? " " : $this->persona->getNombre())." ".($this->persona->getPrimerApellido()== null ? " " : $this->persona->getPrimerApellido())." ".($this->persona->getSegundoApellido()== null ? " " : $this->persona->getSegundoApellido()));
    }


}