<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 12/4/2015
 * Time: 10:13 AM
 */

namespace Modulos\PersonasBundle\Entity;

use Modulos\PersonasBundle\Entity\CarteraPersonaMes;
use Symfony\Component\Validator\Constraints\DateTime;


class CarteraMes
{
    private $mes;
    private $personasMes=array();
    private $totalesMes;
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
     * @return mixed
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * @param mixed $mes
     */
    public function setMes($mes)
    {
        $this->mes = $mes;
    }

    /**
     * @return mixed
     */
    public function getPersonasMes()
    {
        return $this->personasMes;
    }

    /**
     * @param mixed $personasMes
     */
    public function setPersonaMes($personasMes)
    {
        $this->personasMes = $personasMes;
    }

    /**
     * @return mixed
     */
    public function getTotalesMes()
    {
        return $this->totalesMes;
    }

    /**
     * @param mixed $totalesMes
     */
    public function setTotalesMes($totalesMes)
    {
        $this->totalesMes = $totalesMes;
    }



    public function getCarteraPersonaMes($persona){
        foreach ($this->personasMes as $personaMes){
            if($personaMes->getPersona()->getId()==$persona->getId()){
                return $personaMes;
            }
        }
        return null;
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