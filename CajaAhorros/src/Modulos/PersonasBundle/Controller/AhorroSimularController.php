<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\AhorroSimular;
use Modulos\PersonasBundle\Form\AhorroSimularType;

use Modulos\PersonasBundle\Form\PagoCuotaAhorroType;

use Modulos\PersonasBundle\Entity\TablaAmortizacionAhorroSimular;
use Modulos\PersonasBundle\Entity\PagoCuotaAhorro;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * AhorroSimular controller.
 *
 */
class AhorroSimularController extends Controller
{
    public function simulacionAhorroAction(Request $request)
    {
        $entity = new AhorroSimular();

        $ano= date('Y');
        $mes= date('m');
        $dia= date('d');
        $h = date('H')-5;
        $m = date('i');
        $s = date('s');

        $date = $ano.'-'.$mes.'-'.$dia;
        $date = explode("-", $date);

        $time = $h.':'.$m.':'.$s;
        $time = explode(":", $time);

        $fechak = new \DateTime();
        $fechak->setDate($date[0],$date[1],$date[2]);
        $fechak->setTime($time[0],$time[1],$time[2]);

        $entity->setFechaSolicitud($fechak);

        $ano= date('Y');
        $mes= date('m')+1;
        $dia= date('d');
        $h = date('H')-5;
        $m = date('i');
        $s = date('s');

        $datef = $ano.'-'.$mes.'-'.$dia;
        $datef = explode("-", $datef);

        $timef = $h.':'.$m.':'.$s;
        $timef = explode(":", $timef);

        $fechakf = new \DateTime();
        $fechakf->setDate($datef[0],$datef[1],$datef[2]);
        $fechakf->setTime($timef[0],$timef[1],$timef[2]);

        $entity->setFechafinal($fechakf);

        $form = $this->createForm(new AhorroSimularType(),$entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $tiposahorros = $em->getRepository('ModulosPersonasBundle:TipoAhorro')->findAll();

        if($entity->getTipoAhorro()=="Ahorro Plazo Fijo"){

            $difAno=$entity->getFechafinal()->format('Y')- $entity->getFechaSolicitud()->format('Y');
            $difMes=(($difAno*12)+$entity->getFechafinal()->format('m'))- $entity->getFechaSolicitud()->format('m');
            $difDias =(($difMes*30)+$entity->getFechafinal()->format('d')) - $entity->getFechaSolicitud()->format('d');

                if($difMes<=1) {
                    if ($difDias < 30) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'El plazo mínimo para crear un Ahorro a Plazo Fijo es de un mes o una diferencia de 30 días superiores a la fecha de ingreso'
                        );
                        return $this->redirect($this->generateUrl('simulacionAhorro'));
                    }
                }


        }

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

            if (!$entity->getFrecuenciaDePago() == "" && !$entity->getFechaSolicitud(
                ) == "" && !$entity->getvalorAhorrar(
                ) == "" && !$entity->getTasaInteres() == ""
            ) {
                if($entity->getTipoAhorro()->getMetodoAmortizacionAhorro()->getMetodo() == "FONDO AHORRO"){//Francés
                    $this->getCuotaAhorroConstante($em, $entity);
                }
            }
            return $this->redirect($this->generateUrl(
                'despliegue_Simulacion', array('id' => $entity->getId())));


        }

        return $this->render('ModulosPersonasBundle:AhorroSimular:simulacionAhorro.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'tiposahorros'   => $tiposahorros            
        ));
    }
    
    public function despligueAction(Request $request, $id)
    {   
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:AhorroSimular')->find($id);
        $tiposahorros = $em->getRepository('ModulosPersonasBundle:TipoAhorro')->findAll();

        $pagosrealizados = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findPagosCuotasAhorros($id);

        $amortizaciones = $em->getRepository('ModulosPersonasBundle:TablaAmortizacionAhorroSimular')->findTablasAmortizacionPorAhorrosSimular($id);
        /*for ($i = 0; $i < count($amortizaciones); $i++) {
            $amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
            $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
            $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
            $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
        }*/

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AhorroSimular entity.');
        }

        $editForm = $this->createForm(new AhorroSimularType(),$entity);
        $editForm->handleRequest($request);

        $interes =0;

        if($entity->getTipoAhorro()=="Ahorro Plazo Fijo"){
            $difAno=$entity->getFechafinal()->format('Y')- $entity->getFechaSolicitud()->format('Y');
            $difMes=(($difAno*12)+$entity->getFechafinal()->format('m'))- $entity->getFechaSolicitud()->format('m');
            $difDias =(($difMes*30)+$entity->getFechafinal()->format('d')) - $entity->getFechaSolicitud()->format('d');

            //if($entity->getFechafinal()->format('Y-m')==$entity->getFechaSolicitud()->format('Y-m')||$entity->getFechafinal()->format('Y-m')>$entity->getFechaSolicitud()->format('Y-m')||$entity->getFechafinal()->format('Y-m')<$entity->getFechaSolicitud()->format('Y-m')) {
                if($difMes<=1) {
                    if ($difDias < 30) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'El plazo mínimo para crear un Ahorro a Plazo Fijo es de un mes o una diferencia de 30 días superiores a la fecha de ingreso'
                        );
                        //return $this->redirect($this->generateUrl('simulacionAhorro'));
                        return $this->redirect($this->generateUrl( 'despliegue_Simulacion', array('id' => $entity->getId())));
                    }
                }
            //}
            if($entity->getFechafinal()->format('m-Y') == $entity->getFechaSolicitud()->format('m-Y')){
                $interes=0;
            }else{
                $interes=$entity->getvalorAhorrar()*($entity->getTipoAhorro()->getTasaInteres()/12)*($difMes);
            }
        }

        if ($editForm->isValid()) {
            $amortizacionesToDelete = $em->getRepository(
                'ModulosPersonasBundle:TablaAmortizacionAhorroSimular'
            )->findTablasAmortizacionPorAhorrosSimular($id);

            foreach ($amortizacionesToDelete as $amort) {
                $em->remove($amort);
            }
            $em->flush();
            
            if (!$entity->getFrecuenciaDePago() == "" && !$entity->getFechaSolicitud(
                ) == "" && !$entity->getvalorAhorrar(
                ) == "" && !$entity->getTasaInteres() == ""
            ) {
                //if($entity->getTipoAhorro()=="Ahorro Plazo Fijo") {
                    if ($entity->getTipoAhorro()->getMetodoAmortizacionAhorro()->getMetodo() == "FONDO AHORRO") {//Francés
                        $this->getCuotaAhorroConstante($em, $entity);
                    }
                //}
            }

            $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Se ha generado la nueva simulacion de ahorro'
                );
            
            return $this->redirect($this->generateUrl(
                'despliegue_Simulacion', array('id' => $entity->getId())));
            
        }

        return $this->render('ModulosPersonasBundle:AhorroSimular:despliegueSimulacion.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'tiposahorros'=> $tiposahorros,
            'amortizaciones' => $amortizaciones,
            'pagosrealizados'=>$pagosrealizados,
            'interes'=>$interes

        ));
    }

    public function getCuotaAhorroConstante($em, $entity){
        $RESTO_A_AMORTIZAR = $entity->getvalorAhorrar();
        //$periodos = $entity->getTipoAhorro()->getPlazo();
        if($entity->getCuotas()>0) {
            $periodos = $entity->getCuotas();
        }else{
            $periodos =1;
        }
        $interesAnual = $entity->getTasaInteres();//dividir el interes anual por 12
        $parteFrecuencia=$this->getValorFrecuencia($entity->getFrecuenciaDePago());
        $interes = ($interesAnual / $parteFrecuencia) / 100;
        $incrementoFecha = (1).'M';
        switch ($entity->getFrecuenciaDePago()) {
            case "DIARIA":{
                $incrementoFecha = (1).'D';
            }break;
            case "SEMANAL":{
                $incrementoFecha = (7).'D';
            }break;
            case "MENSUAL":{
                $incrementoFecha = (1).'M';
            }break;
            case "TRIMESTRAL":{
                $incrementoFecha = (3).'M';
            }break;
            case "SEMESTRAL":{
                $incrementoFecha = (6).'M';
            }break;
            case "ANUAL":{
                $incrementoFecha = (1).'Y';
            }break;
        }
        $MENSUALIDAD = $RESTO_A_AMORTIZAR/((pow((1+$interes),$periodos)-1)/$interes);
        $TOTAL_AMORTIZADO=$MENSUALIDAD;
        $rowamortizacion = new TablaAmortizacionAhorroSimular();
        $rowamortizacion->setSaldo($MENSUALIDAD);
        $rowamortizacion->setFechaDePago($entity->getFechaSolicitud());
        $rowamortizacion->setCuota(1);
        $rowamortizacion->setValorcuota($MENSUALIDAD);
        $rowamortizacion->setInteres(0);
        $rowamortizacion->setCapital($MENSUALIDAD);
        $rowamortizacion->setAhorroId($entity);
        $em->persist($rowamortizacion);
        $em->flush();
        for ($i = 1; $i < $periodos; $i++) {
            $INTERESES = $TOTAL_AMORTIZADO * $interes;
            $TOTAL_AMORTIZADO = $TOTAL_AMORTIZADO + $INTERESES + $MENSUALIDAD;
            $rowamortizacionnueva = new TablaAmortizacionAhorroSimular();
            $rowamortizacionnueva->setSaldo($TOTAL_AMORTIZADO);

            $fechacuota = $entity->getFechaSolicitud();
            $intervalo = new \DateInterval('P'.($incrementoFecha));
            $fechacuota->add($intervalo);
            $rowamortizacionnueva->setFechaDePago($fechacuota);

            $rowamortizacionnueva->setCuota($i + 1);
            $rowamortizacionnueva->setValorcuota($MENSUALIDAD + $INTERESES);
            $rowamortizacionnueva->setInteres($INTERESES);
            $rowamortizacionnueva->setCapital($MENSUALIDAD);
            $rowamortizacionnueva->setAhorroId($entity);
            $em->persist($rowamortizacionnueva);
            $em->flush();
        }
    }

    public function getValorFrecuencia($frecuencia){
        switch ($frecuencia){
            case "DIARIA":
                return 360;
            case "SEMANAL":
                return 52;
            case "MENSUAL":
                return 12;
            case "TRIMESTRAL":
                return 4;
            case "SEMESTRAL":
                return 2;
            case "ANUAL":
                return 1;
            default:
                return 12;
        }
    }
}
