<?php

namespace Modulos\PersonasBundle\Entity;

use Modulos\PersonasBundle\Entity\AhorroPersonaMes;
use Modulos\PersonasBundle\Entity\AhorroMes;

class AhorroLista
{
    private $ahorroMeses = array();


    /**
     * @return mixed
     */
    public function getAhorroMeses()
    {
        return $this->ahorroMeses;
    }

    /**
     * @param mixed $ahorroMeses
     */
    public function setAhorroMeses($ahorroMeses)
    {
        $this->ahorroMeses = $ahorroMeses;
    }

    public function getAhorroMes($ahorroMesFecha)
    {
        foreach ($this->ahorroMeses as $ahorroMes) {
            if ($ahorroMes->getMes() == $ahorroMesFecha) {
                return $ahorroMes;
            }
        }

        return null;
    }

    public function getSaldoAnterior($ahorroMesesIn, $ahorroMesFecha, $persona)
    {
        $saldoAnterior = 0;
        foreach ($ahorroMesesIn as $ahorroMes) {
            if ($ahorroMes->getMes() == $ahorroMesFecha) {
                return $saldoAnterior;
            } else {
                $ahorroPersonaMes = $ahorroMes->getAhorroPersonaMes($persona);
                if ($ahorroPersonaMes != null) {
                    $saldoAnterior = $ahorroPersonaMes->getSaldoFinalAhorros();
                }
            }
        }

        return $saldoAnterior;
    }


}