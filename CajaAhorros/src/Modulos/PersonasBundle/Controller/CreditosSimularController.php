<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 05/02/2016
 * Time: 11:00
 */

namespace Modulos\PersonasBundle\Controller;

use Modulos\PersonasBundle\Form\CreditosSimularType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Modulos\PersonasBundle\Entity\CreditosSimular;
use Modulos\PersonasBundle\Entity\TablaAmortizacionSimular;

/**
 * CreditosSimular controller.
 *
 */
class CreditosSimularController extends Controller
{

    public function simulacionCreditoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $reglasInvalidas=array();
        $entity = new CreditosSimular();

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

        $form = $this->createForm(new CreditosSimularType(), $entity);
        $creditosaaux = $em->getRepository('ModulosPersonasBundle:ProductoDeCreditos')->findAll();
        $form->handleRequest($request);

        if ($form->isValid()) {

            //Verificar q no haya campos necesarios en el calculo q vengan vacios
            if (!$entity->getFrecuencia_pago() == "" && !$entity->getFechaSolicitud(
                ) == "" && !$entity->getNumeroDePagos() == "" && !$entity->getMontoSolicitado(
                ) == "" && !$entity->getInteresAnual() == ""
            ) {
                if($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA FIJA"){//Francés
                    $this->getCuotaConstante($em, $entity);
                }else if($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE"){//Francés
                    $this->getCuotaCapitalConstante($em, $entity);
                }else if($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE FIJO"){//Francés
                    $this->getCuotaCapitalConstanteFijo($em, $entity);
                }

            }
//                $this->get('session')->getFlashBag()->add(
//                    'notice',
//                    'Se ha creado el credito'
//                );

            return $this->redirect($this->generateUrl(
                'despliegueSimulacionCredito', array('id' => $entity->getId())));
        }

        return $this->render(
            'ModulosPersonasBundle:CreditosSimular:simulacionCredito.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
                //'reglasInvalidas'=> $reglasInvalidas,
                'tiposCreditos'=>$creditosaaux
            )
        );
    }

    public function despliegueSimulacionCreditoAction(Request $request, $id)
    {
        $reglasInvalidas=array();
        $em = $this->getDoctrine()->getManager();
        $creditosaaux = $em->getRepository('ModulosPersonasBundle:ProductoDeCreditos')->findAll();
        $entity = $em->getRepository('ModulosPersonasBundle:CreditosSimular')->find($id);

        foreach ($entity as $entity) {
            $entity->setAporteAdicional(round($entity->getAporteAdicional(), 2));
            $entity->setAporteMinimo(round($entity->getAporteMinimo(), 2));
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Creditos entity.');
        }

        $editForm = $this->createForm(new CreditosSimularType(),$entity);
        $editForm->handleRequest($request);

        $entity = $em->getRepository('ModulosPersonasBundle:CreditosSimular')->find($id);

        $pagosrealizados = $em->getRepository(
            'ModulosPersonasBundle:PagoCuotaCredito'
        )->findPagosCuotasCreditos($id);

        $amortizaciones = $em->getRepository(
            'ModulosPersonasBundle:TablaAmortizacionSimular'
        )->findTablasAmortizacionPorCreditosSim($id);
        /*for ($i = 0; $i < count($amortizaciones); $i++) {
            $amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
            $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
            $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
            $amortizaciones[$i]->setDesgravamen(round($amortizaciones[$i]->getDesgravamen(), 2));
            $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
        }*/

        if ($editForm->isValid()) {
//                echo $entity->getNumeroDePagos();
//                die();
            $amortizacionesToDelete = $em->getRepository(
                'ModulosPersonasBundle:TablaAmortizacionSimular'
            )->findTablasAmortizacionPorCreditosSim($id);

            foreach ($amortizacionesToDelete as $amort) {
                $em->remove($amort);
            }
            $em->flush();

            if (!$entity->getFrecuencia_pago() == "" && !$entity->getFechaSolicitud(
                ) == "" && !$entity->getNumeroDePagos() == "" && !$entity->getMontoSolicitado(
                ) == "" && !$entity->getInteresAnual() == ""
            ) {
                if($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA FIJA"){//Francés
                    $this->getCuotaConstante($em, $entity);
                }else if($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE"){//Francés
                    $this->getCuotaCapitalConstante($em, $entity);
                }else if($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE FIJO"){//Francés
                    $this->getCuotaCapitalConstanteFijo($em, $entity);
                }
            }

            $amortizaciones = $em->getRepository(
                'ModulosPersonasBundle:TablaAmortizacionSimular'
            )->findTablasAmortizacionPorCreditosSim($id);
            /*for ($i = 0; $i < count($amortizaciones); $i++) {
                $amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
                $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                $amortizaciones[$i]->setDesgravamen(round($amortizaciones[$i]->getDesgravamen(), 2));
                $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
            }*/
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha generado la nueva simulacion de credito'
            );
            return $this->redirect($this->generateUrl('despliegueSimulacionCredito', array('id' => $id)));
        }

        //$documentos = $em->getRepository('ModulosPersonasBundle:CreditosDocumento')->findCreditoDocumentoPorCredito($id);
        return $this->render(
            'ModulosPersonasBundle:CreditosSimular:despliegueSimulacionCredito.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'amortizaciones' => $amortizaciones,
                'reglasInvalidas'=> $reglasInvalidas,
                'tiposCreditos'=>$creditosaaux,
                //'documentos'=>$documentos,
                'pagosrealizados'=>$pagosrealizados
            )
        );
    }

    /*
     * El capital a pagar es fijo y el interes varía entonces de mas a menos
     * El total a pagar varía de mas a menos
     * El pago de Desgravamen se aplica por un porciento y en cada periodo se paga ese mismo porciento
     */
    public function getCuotaCapitalConstante($em, $entity){
        $tipoCredito=$entity->getIdProductosDeCreditos();
        $DESGRAVAMEN=$tipoCredito->getDesgravamen();
        $TOTAL_AMORTIZADO = 0;
        $RESTO_A_AMORTIZAR =  $entity->getMontoSolicitado();
        $periodos = $entity->getNumeroDePagos();
        $interesAnual =  $entity->getInteresAnual();//dividir el interes anual por 12

        $parteFrecuencia=$this->getValorFrecuencia($entity->getFrecuencia_pago());

        $interes = ($interesAnual /  $parteFrecuencia) / 100;

        $incrementoFecha = (1).'M';
        switch ($entity->getFrecuencia_pago()) {
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
        $rowamortizacion = new TablaAmortizacionSimular();
        $rowamortizacion->setSaldo(number_format($RESTO_A_AMORTIZAR, 2, '.', ''));
        $rowamortizacion->setFechaDePago($entity->getFechaSolicitud());
        $rowamortizacion->setCuota(0);
        $rowamortizacion->setValorcuota(0);
        $rowamortizacion->setDesgravamen(0);
        $rowamortizacion->setInteres(0);
        $rowamortizacion->setCapital(0);
        $rowamortizacion->setCreditoId($entity);
        $em->persist($rowamortizacion);
        $em->flush();

        $sumaCapital=0;
        $sumaCapitalAnterior=0;
        for ($i = $periodos; $i > 0; $i--) {
            $AMORTIZACION = $entity->getMontoSolicitado()/$entity->getNumeroDePagos();
            $INTERESES = $RESTO_A_AMORTIZAR * $interes;
            $MENSUALIDAD = $AMORTIZACION + $INTERESES + $DESGRAVAMEN;
            $TOTAL_AMORTIZADO += $AMORTIZACION;
            $RESTO_A_AMORTIZAR = $RESTO_A_AMORTIZAR - $AMORTIZACION;
            $rowamortizacionnueva = new TablaAmortizacionSimular();
            $rowamortizacionnueva->setSaldo(number_format($RESTO_A_AMORTIZAR, 2, '.', ''));

            $fechacuota = $entity->getFechaSolicitud();
            $intervalo = new \DateInterval('P'.($incrementoFecha));
            $fechacuota->add($intervalo);
            $rowamortizacionnueva->setFechaDePago($fechacuota);

            if($i!=1){
                $sumaCapitalAnterior+=number_format($AMORTIZACION, 2, '.', '');
            }

            $sumaCapital+=number_format($AMORTIZACION, 2, '.', '');

            if($i==1){
                if($sumaCapital>$entity->getMontoSolicitado() || $sumaCapital<$entity->getMontoSolicitado()){
                    $rowamortizacionnueva->setCuota($periodos - $i + 1);
                    $rowamortizacionnueva->setValorcuota(number_format(($INTERESES+$DESGRAVAMEN+($entity->getMontoSolicitado()-$sumaCapitalAnterior)), 2, '.', ''));
                    $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                    $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                    $rowamortizacionnueva->setCapital(number_format(($entity->getMontoSolicitado()-$sumaCapitalAnterior), 2, '.', ''));
                    $rowamortizacionnueva->setCreditoId($entity);
                    $em->persist($rowamortizacionnueva);
                    $em->flush();
                }elseif($sumaCapital==$entity->getMontoSolicitado()){
                    $rowamortizacionnueva->setCuota($periodos - $i + 1);
                    $rowamortizacionnueva->setValorcuota(number_format($MENSUALIDAD, 2, '.', ''));
                    $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                    $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                    $rowamortizacionnueva->setCapital(number_format($AMORTIZACION, 2, '.', ''));
                    $rowamortizacionnueva->setCreditoId($entity);
                    $em->persist($rowamortizacionnueva);
                    $em->flush();
                }
            }else {
                $rowamortizacionnueva->setCuota($periodos - $i + 1);
                $rowamortizacionnueva->setValorcuota(number_format($MENSUALIDAD, 2, '.', ''));
                $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                $rowamortizacionnueva->setCapital(number_format($AMORTIZACION, 2, '.', ''));
                $rowamortizacionnueva->setCreditoId($entity);
                $em->persist($rowamortizacionnueva);
                $em->flush();
            }


        }
    }

    /*
     * El capital a pagar y el interes es fijo
     * El total a pagar es igual en todas las cuotas mensuales
     * El pago de Desgravamen se aplica por un porciento y en cada periodo se paga ese mismo porciento
     */
    public function getCuotaCapitalConstanteFijo($em, $entity){
        $tipoCredito=$entity->getIdProductosDeCreditos();
        $TOTAL_AMORTIZADO = 0;
        $RESTO_A_AMORTIZAR =  $entity->getMontoSolicitado();
        $periodos = $entity->getNumeroDePagos();
        $interesAnual =  $entity->getInteresAnual();//dividir el interes anual por 12
        $parteFrecuencia=$this->getValorFrecuencia($entity->getFrecuencia_pago());

        $interes = (($interesAnual/$parteFrecuencia) *  $periodos) / 100;

        $incrementoFecha = (1).'M';
        switch ($entity->getFrecuencia_pago()) {
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

        $DESGRAVAMEN=$tipoCredito->getDesgravamen() * ($entity->getMontoSolicitado()/100);

        $rowamortizacion = new TablaAmortizacionSimular();
        $rowamortizacion->setSaldo(number_format($RESTO_A_AMORTIZAR, 2, '.', ''));
        $rowamortizacion->setFechaDePago($entity->getFechaSolicitud());
        $rowamortizacion->setCuota(0);
        $rowamortizacion->setValorcuota(number_format($DESGRAVAMEN, 2, '.', ''));
        $rowamortizacion->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
        $rowamortizacion->setInteres(0);
        $rowamortizacion->setCapital(0);
        $rowamortizacion->setCreditoId($entity);
        $em->persist($rowamortizacion);
        $em->flush();

        $sumaCapital=0;
        $sumaCapitalAnterior=0;
        for ($i = $periodos; $i > 0; $i--) {
            $DESGRAVAMEN=0;
            $AMORTIZACION = $entity->getMontoSolicitado()/$entity->getNumeroDePagos();
            $INTERESES = ($entity->getMontoSolicitado()/$periodos)* $interes;
            $MENSUALIDAD = $AMORTIZACION + $INTERESES + $DESGRAVAMEN;
            $TOTAL_AMORTIZADO += $AMORTIZACION;
            $RESTO_A_AMORTIZAR = $RESTO_A_AMORTIZAR - $AMORTIZACION;
            $rowamortizacionnueva = new TablaAmortizacionSimular();
            $rowamortizacionnueva->setSaldo(number_format($RESTO_A_AMORTIZAR, 2, '.', ''));

            $fechacuota = $entity->getFechaSolicitud();
            $intervalo = new \DateInterval('P'.($incrementoFecha));
            $fechacuota->add($intervalo);
            $rowamortizacionnueva->setFechaDePago($fechacuota);

            if($i!=1){
                $sumaCapitalAnterior+=number_format($AMORTIZACION, 2, '.', '');
            }

            $sumaCapital+=number_format($AMORTIZACION, 2, '.', '');

            if($i==1){
                if($sumaCapital>$entity->getMontoSolicitado() || $sumaCapital<$entity->getMontoSolicitado()){
                    $rowamortizacionnueva->setCuota($periodos - $i + 1);
                    $rowamortizacionnueva->setValorcuota(number_format(($INTERESES+$DESGRAVAMEN+($entity->getMontoSolicitado()-$sumaCapitalAnterior)), 2, '.', ''));
                    $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                    $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                    $rowamortizacionnueva->setCapital(number_format(($entity->getMontoSolicitado()-$sumaCapitalAnterior), 2, '.', ''));
                    $rowamortizacionnueva->setCreditoId($entity);
                    $em->persist($rowamortizacionnueva);
                    $em->flush();
                }elseif($sumaCapital==$entity->getMontoSolicitado()){
                    $rowamortizacionnueva->setCuota($periodos - $i + 1);
                    $rowamortizacionnueva->setValorcuota(number_format($MENSUALIDAD, 2, '.', ''));
                    $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                    $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                    $rowamortizacionnueva->setCapital(number_format($AMORTIZACION, 2, '.', ''));
                    $rowamortizacionnueva->setCreditoId($entity);
                    $em->persist($rowamortizacionnueva);
                    $em->flush();
                }
            }else {
                $rowamortizacionnueva->setCuota($periodos - $i + 1);
                $rowamortizacionnueva->setValorcuota(number_format($MENSUALIDAD, 2, '.', ''));
                $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                $rowamortizacionnueva->setCapital(number_format($AMORTIZACION, 2, '.', ''));
                $rowamortizacionnueva->setCreditoId($entity);
                $em->persist($rowamortizacionnueva);
                $em->flush();
            }
        }
    }

    /*
     * El capital a pagar es variable de menos a mas y el interes varía entonces de mas a menos
     * El total a pagar es constante
     * El pago de Desgravamen se aplica por una tasa fija y se paga al inicio y todo de una vez
     */
    public function getCuotaConstante($em, $entity){
        $tipoCredito=$entity->getIdProductosDeCreditos();
        $TOTAL_AMORTIZADO = 0;
        $RESTO_A_AMORTIZAR = $entity->getMontoSolicitado();
        $periodos = $entity->getNumeroDePagos();
        $interesAnual = $entity->getInteresAnual();//dividir el interes anual por 12
        $parteFrecuencia=$this->getValorFrecuencia($entity->getFrecuencia_pago());
        $interes = ($interesAnual / $parteFrecuencia) / 100;
        $incrementoFecha = (1).'M';
        switch ($entity->getFrecuencia_pago()) {
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
        $DESGRAVAMEN=$tipoCredito->getDesgravamen() * ($entity->getMontoSolicitado()/100);

        $rowamortizacion = new TablaAmortizacionSimular();
        $rowamortizacion->setSaldo(number_format($RESTO_A_AMORTIZAR, 2, '.', ''));
        $rowamortizacion->setFechaDePago($entity->getFechaSolicitud());
        $rowamortizacion->setCuota(0);
        $rowamortizacion->setValorcuota(number_format($DESGRAVAMEN, 2, '.', ''));
        $rowamortizacion->setInteres(0);
        $rowamortizacion->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
        $rowamortizacion->setCapital(0);
        $rowamortizacion->setCreditoId($entity);
        $em->persist($rowamortizacion);
        $em->flush();

        $sumaCapital=0;
        $sumaCapitalAnterior=0;
        for ($i = $periodos; $i > 0; $i--) {
            $DESGRAVAMEN=0;
            $MENSUALIDAD = $RESTO_A_AMORTIZAR * $interes / (1 - pow((1 + $interes), -$i));
            $INTERESES = $RESTO_A_AMORTIZAR * $interes;
            $AMORTIZACION = $MENSUALIDAD - $INTERESES;
            $TOTAL_AMORTIZADO += $AMORTIZACION;
            $RESTO_A_AMORTIZAR = $RESTO_A_AMORTIZAR - $AMORTIZACION;
            $rowamortizacionnueva = new TablaAmortizacionSimular();
            $rowamortizacionnueva->setSaldo(number_format($RESTO_A_AMORTIZAR, 2, '.', ''));

            $fechacuota = $entity->getFechaSolicitud();
            $intervalo = new \DateInterval('P'.($incrementoFecha));
            $fechacuota->add($intervalo);
            $rowamortizacionnueva->setFechaDePago($fechacuota);

            if($i!=1){
                $sumaCapitalAnterior+=number_format($AMORTIZACION, 2, '.', '');
            }

            $sumaCapital+=number_format($AMORTIZACION, 2, '.', '');

            if($i==1){
                if($sumaCapital>$entity->getMontoSolicitado() || $sumaCapital<$entity->getMontoSolicitado()){
                    $rowamortizacionnueva->setCuota($periodos - $i + 1);
                    $rowamortizacionnueva->setValorcuota(number_format(($INTERESES+$DESGRAVAMEN+($entity->getMontoSolicitado()-$sumaCapitalAnterior)), 2, '.', ''));
                    $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                    $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                    $rowamortizacionnueva->setCapital(number_format(($entity->getMontoSolicitado()-$sumaCapitalAnterior), 2, '.', ''));
                    $rowamortizacionnueva->setCreditoId($entity);
                    $em->persist($rowamortizacionnueva);
                    $em->flush();
                }elseif($sumaCapital==$entity->getMontoSolicitado()){
                    $rowamortizacionnueva->setCuota($periodos - $i + 1);
                    $rowamortizacionnueva->setValorcuota(number_format($MENSUALIDAD, 2, '.', ''));
                    $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                    $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                    $rowamortizacionnueva->setCapital(number_format($AMORTIZACION, 2, '.', ''));
                    $rowamortizacionnueva->setCreditoId($entity);
                    $em->persist($rowamortizacionnueva);
                    $em->flush();
                }
            }else {
                $rowamortizacionnueva->setCuota($periodos - $i + 1);
                $rowamortizacionnueva->setValorcuota(number_format($MENSUALIDAD, 2, '.', ''));
                $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                $rowamortizacionnueva->setCapital(number_format($AMORTIZACION, 2, '.', ''));
                $rowamortizacionnueva->setCreditoId($entity);
                $em->persist($rowamortizacionnueva);
                $em->flush();
            }


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
