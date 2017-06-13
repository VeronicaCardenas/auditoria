<?php
/**
 * Created by PhpStorm.
 * User: pedro
 * Date: 12/4/2015
 * Time: 10:10 AM
 */

namespace Modulos\PersonasBundle\Entity;

use Modulos\PersonasBundle\Entity\CarteraPersonaMes;
use Modulos\PersonasBundle\Entity\CarteraMes;

class Cartera
{
    private $carteraMeses = array();


    /**
     * @return mixed
     */
    public function getCarteraMeses()
    {
        return $this->carteraMeses;
    }

    /**
     * @param mixed $carteraMeses
     */
    public function setCarteraMeses($carteraMeses)
    {
        $this->carteraMeses = $carteraMeses;
    }

    public function getCarteraMes($carteraMesFecha)
    {
        foreach ($this->carteraMeses as $carteraMes) {
            if ($carteraMes->getMes() == $carteraMesFecha) {
                return $carteraMes;
            }
        }

        return null;
    }

    public function getSaldoAnterior($carteraMesesIn, $carteraMesFecha, $persona)
    {
        $saldoAnterior = 0;
        foreach ($carteraMesesIn as $carteraMes) {
            if ($carteraMes->getMes() == $carteraMesFecha) {
                return $saldoAnterior;
            } else {
                $carteraPersonaMes = $carteraMes->getCarteraPersonaMes($persona);
                if ($carteraPersonaMes != null) {
                    $saldoAnterior = $carteraPersonaMes->getCreditoMicroEmpPorVencerSaldoCap();
                }
            }
        }

        return $saldoAnterior;
    }

    public function getNumeroCreditos($carteraMesesIn, $carteraMesFecha, $persona)
    {
        $numeroCreditos = 0;
        foreach ($carteraMesesIn as $carteraMes) {
            if ($carteraMes->getMes() == $carteraMesFecha) {
                return $numeroCreditos;
            } else {
                $carteraPersonaMes = $numeroCreditos = $carteraMes->getCarteraPersonaMes($persona);
                if ($carteraPersonaMes != null) {
                    $numeroCreditos = $carteraPersonaMes->getCreditoCant();
                }
            }
        }

        return $numeroCreditos;
    }


}