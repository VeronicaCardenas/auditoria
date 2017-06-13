<?php

namespace Modulos\PersonasBundle\Entity;

use Modulos\PersonasBundle\Entity\AportePersonaMes;
use Modulos\PersonasBundle\Entity\AporteMes;

class Aporte
{
    private $aporteMeses = array();


    /**
     * @return mixed
     */
    public function getAporteMeses()
    {
        return $this->aporteMeses;
    }

    /**
     * @param mixed $aporteMeses
     */
    public function setAporteMeses($aporteMeses)
    {
        $this->aporteMeses = $aporteMeses;
    }

    public function getAporteMes($aporteMesFecha)
    {
        foreach ($this->aporteMeses as $aporteMes) {
            if ($aporteMes->getMes() == $aporteMesFecha) {
                return $aporteMes;
            }
        }

        return null;
    }

    public function getSaldoAnterior($aporteMesesIn, $aporteMesFecha, $persona)
    {
        $saldoAnterior = 0;
        foreach ($aporteMesesIn as $aporteMes) {
            if ($aporteMes->getMes() == $aporteMesFecha) {
                return $saldoAnterior;
            } else {
                $aportePersonaMes = $aporteMes->getAportePersonaMes($persona);
                if ($aportePersonaMes != null) {
                    $saldoAnterior = $aportePersonaMes->getCreditoMicroEmpPorVencerSaldoCap();
                }
            }
        }

        return $saldoAnterior;
    }
    
    public function getSaldoAnteriorRes($aporteMesesIn, $aporteMesFecha, $persona)
    {
        $saldoAnterior = 0;
        foreach ($aporteMesesIn as $aporteMes) {
            if ($aporteMes->getMes() == $aporteMesFecha) {
                return $saldoAnterior;
            } 
        }

        return $saldoAnterior;
    }

    public function getNumeroCreditos($aporteMesesIn, $aporteMesFecha, $persona)
    {
        $numeroCreditos = 0;
        foreach ($aporteMesesIn as $aporteMes) {
            if ($aporteMes->getMes() == $aporteMesFecha) {
                return $numeroCreditos;
            } else {
                $aportePersonaMes = $numeroCreditos = $aporteMes->getAportePersonaMes($persona);
                if ($aportePersonaMes != null) {
                    $numeroCreditos = $aportePersonaMes->getCreditoCant();
                }
            }
        }

        return $numeroCreditos;
    }


}