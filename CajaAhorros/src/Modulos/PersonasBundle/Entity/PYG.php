<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 12/7/2015
 * Time: 12:26 PM
 */

namespace Modulos\PersonasBundle\Entity;


class PYG
{
    private $mes="";
    private $ingresos=array();
    private $gastos=array();
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
    public function getIngresos()
    {
        return $this->ingresos;
    }

    /**
     * @param array $ingresos
     */
    public function setIngresos($ingresos)
    {
        $this->ingresos = $ingresos;
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

    /**
     * @return array
     */
    public function getGastos()
    {
        return $this->gastos;
    }

    /**
     * @param array $gastos
     */
    public function setGastos($gastos)
    {
        $this->gastos = $gastos;
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

    public function getMesTitulo(){
        $fechaArray=explode( '-', $this->mes );
        $date=new \DateTime();
        $date->setDate($fechaArray[0],$fechaArray[1],1);
        $cantDias=$date->format('t');

        return $this->mesesMap[$fechaArray[1]]."-".$fechaArray[0];
    }
    
}