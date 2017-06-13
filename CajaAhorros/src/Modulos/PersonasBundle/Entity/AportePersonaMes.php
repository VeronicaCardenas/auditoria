<?php


namespace Modulos\PersonasBundle\Entity;


class AportePersonaMes
{
    private $persona;
    private $saldoAnterior=0;
    private $capitalPagado=0;
    private $totalPagado=0;
    private $retiroAportes=0;
    private $creditoMicroEmpPorVencerSaldoCap=0;

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
    
    /**
     * @return int
     */
    public function getRetiroAportes()
    {
        return $this->retiroAportes;
    }

    /**
     * @param int $retiroAportes
     */
    public function setRetiroAportes($retiroAportes)
    {
        $this->retiroAportes = $retiroAportes;
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

    public function updateTotalPagado(){
        $this->totalPagado=$this->capitalPagado + $this->saldoAnterior;
    }

    public function getNombreCompleto(){
        return (($this->persona->getNombre()== null ? " " : $this->persona->getNombre())." ".($this->persona->getPrimerApellido()== null ? " " : $this->persona->getPrimerApellido())." ".($this->persona->getSegundoApellido()== null ? " " : $this->persona->getSegundoApellido()));
    }


}