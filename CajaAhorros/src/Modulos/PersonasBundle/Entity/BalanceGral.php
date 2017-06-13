<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 12/7/2015
 * Time: 12:26 PM
 */

namespace Modulos\PersonasBundle\Entity;


class BalanceGral
{
    private $mes="";
    private $activos=array();
    private $pasivos=array();
    private $patrimonio=array();
    private $totales=array();

    private $mesesMap = [
        "01" => "Enero",
        "02" => "Febrero",
        "03" => "Marzo",
        "04" => "Abril",
        "05" => "Mayo",
        "06" => "Junio",
        "07" => "Julio",
        "08" => "Agosto",
        "09" => "Septiembre",
        "10" => "Octubre",
        "11" => "Noviembre",
        "12" => "Diciembre",
    ];

    /**
     * @return string
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * @param string $mes
     */
    public function setMes($mes)
    {
        $this->mes = $mes;
    }

    /**
     * @return array
     */
    public function getActivos()
    {
        return $this->activos;
    }

    /**
     * @param array $activos
     */
    public function setActivos($activos)
    {
        $this->activos = $activos;
    }

    /**
     * @return array
     */
    public function getPasivos()
    {
        return $this->pasivos;
    }

    /**
     * @param array $pasivos
     */
    public function setPasivos($pasivos)
    {
        $this->pasivos = $pasivos;
    }

    /**
     * @return array
     */
    public function getPatrimonio()
    {
        return $this->patrimonio;
    }

    /**
     * @param array $patrimonio
     */
    public function setPatrimonio($patrimonio)
    {
        $this->patrimonio = $patrimonio;
    }




    /**
     * @return array
     */
    public function getTotales()
    {
        return $this->totales;
    }

    /**
     * @param array $totales
     */
    public function setTotales($totales)
    {
        $this->totales = $totales;
    }


    public function getIntervaloFecha(){
        $fechaArray=explode( '-', $this->mes );
        $date=new \DateTime();
        $date->setDate($fechaArray[0],$fechaArray[1],1);
        $cantDias=$date->format('t');

        return "1 al ".$cantDias." de ".$this->mesesMap[$fechaArray[1]]." de ".$fechaArray[0];
    }

    public function getMesFecha(){
        $fechaArray=explode( '-', $this->mes );
        $date=new \DateTime();
        $date->setDate($fechaArray[0],$fechaArray[1],1);
        $cantDias=$date->format('t');

        return $this->mesesMap[$fechaArray[1]]."-".$fechaArray[0];
    }

}