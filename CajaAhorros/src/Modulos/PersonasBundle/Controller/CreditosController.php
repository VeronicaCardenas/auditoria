<?php

namespace Modulos\PersonasBundle\Controller;

use Modulos\PersonasBundle\Entity\CreditosDocumento;
use Modulos\PersonasBundle\Form\CreditosDocumentoType;
use Modulos\PersonasBundle\Form\OtorgarCreditoEmergenteType;
use Modulos\PersonasBundle\Form\OtorgarCreditoType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\Creditos;
use Modulos\PersonasBundle\Form\CreditosType;
use Modulos\PersonasBundle\Form\PagoCuotaCreditoType;

use Modulos\PersonasBundle\Entity\Libro;
use Modulos\PersonasBundle\Form\PagoDesgravamenType;
use Modulos\PersonasBundle\Form\PagoCreditosType;
use Modulos\PersonasBundle\Entity\DTVC;
use Modulos\PersonasBundle\Entity\PagoCuotaCredito;
use Modulos\PersonasBundle\Entity\VCHR;

use Modulos\PersonasBundle\Entity\TablaAmortizacion;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Creditos controller.
 *
 */
class CreditosController extends Controller
{
    /**
     * Lists all Creditos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:Creditos')->findAll();

        return $this->render(
            'ModulosPersonasBundle:Creditos:index.html.twig',
            array(
                'entities' => $entities,
                'doc' => "",
            )
        );
    }

    /**
     * Lists all Creditos entities.
     *
     */
    public function indexAuxAction($idCredito)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:Creditos')->findAll();

        return $this->render(
            'ModulosPersonasBundle:Creditos:index.html.twig',
            array(
                'entities' => $entities,
                'doc'=>$this->impresionTablaAmortizacionAction($idCredito),
            )
        );
    }

    /**
     * Creates a new Creditos entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $reglasInvalidas = array();
        $entity = new Creditos();

        $ano = date('Y');
        $mes = date('m');
        $dia = date('d');
        $h = date('H') - 5;
        $m = date('i');
        $s = date('s');

        $date = $ano . '-' . $mes . '-' . $dia;
        $date = explode("-", $date);

        $time = $h . ':' . $m . ':' . $s;
        $time = explode(":", $time);

        $fechak = new \DateTime();
        $fechak->setDate($date[0], $date[1], $date[2]);
        $fechak->setTime($time[0], $time[1], $time[2]);

        $entity->setFechaSolicitud($fechak);

        $form = $this->createForm(new CreditosType(), $entity);
        $creditosaaux = $em->getRepository('ModulosPersonasBundle:ProductoDeCreditos')->findAll();
        $form->handleRequest($request);

        if ($form->isValid()) {

            $estadoCreditos = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->find(2);
            $entity->setEstadocreditos($estadoCreditos);
            $creditosaaux = $em->getRepository('ModulosPersonasBundle:Creditos')->findAll();

            /// validar reglas
            $tipo_credito = $entity->getIdProductosDeCreditos();

//            echo "<pre>";
//            print_r($reglasInvalidas);
//            die();

            //validar esa persona tenga un pago de solicitud sin credito creado
            $solicitudes = $em->getRepository('ModulosPersonasBundle:Libro')->findSolicitudesActivasPorPersona($entity->getPersona());

            if (count($solicitudes) > 0 || $entity->getIdProductosDeCreditos()->getId() == 2) {//"CREDITO EMERGENTE"
                if ($entity->getIdProductosDeCreditos()->getId() != 2) {//"CREDITO EMERGENTE"
                    $solicitud = $solicitudes[0];
                    $solicitud->setInfo("1");
                    $em->persist($solicitud);
                }

                $em->persist($entity);
                $em->flush();
            } else {
                $this->get('session')->getFlashBag()->add(
                    'danger',
                    'No se puede crear el crédito. La persona ' . $entity->getPersona() . ' no ha pagado la solicitud de crédito.'
                );
                return $this->redirect($this->generateUrl('creditos_create'));

            }

            //Verificar q no haya campos necesarios en el calculo q vengan vacios
            if (!$entity->getFrecuencia_pago() == "" && !$entity->getFechaSolicitud() == "" && !$entity->getNumeroDePagos() == "" && !$entity->getMontoSolicitado() == "" && !$entity->getInteresAnual() == ""
            ) {
                if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA FIJA") {//Francés
                    $this->getCuotaConstante($em, $entity);
                } else if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE") {//Francés
                    $this->getCuotaCapitalConstante($em, $entity);
                } else if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE FIJO") {//Francés
                    $this->getCuotaCapitalConstanteFijo($em, $entity);
                }

            }
//                $this->get('session')->getFlashBag()->add(
//                    'notice',
//                    'Se ha creado el credito'
//                );

            return $this->redirect($this->generateUrl(
                'creditos', array('id' => $entity->getId())));
        }

        return $this->render(
            'ModulosPersonasBundle:Creditos:new.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
                'reglasInvalidas' => $reglasInvalidas,
                'tiposCreditos' => $creditosaaux
            )
        );

    }


    /**
     * Finds and displays a Creditos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Creditos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'ModulosPersonasBundle:Creditos:show.html.twig',
            array(
                'entity' => $entity,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Creditos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Creditos entity.');
        }


        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        $amortizaciones = $em->getRepository(
            'ModulosPersonasBundle:TablaAmortizacion'
        )->findTablasAmortizacionPorCreditos($id);
        /*for ($i = 0; $i < count($amortizaciones); $i++) {
            $amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
            $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
            $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
            $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
        }*/

        return $this->render(
            'ModulosPersonasBundle:Creditos:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'amortizaciones' => $amortizaciones,
            )
        );
    }

    /**
     * Creates a form to edit a Creditos entity.
     *
     * @param Creditos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Creditos $entity)
    {
        $form = $this->createForm(
            new CreditosType(),
            $entity,
            array(
                'action' => $this->generateUrl('creditos_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Creditos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $reglasInvalidas = array();
        $em = $this->getDoctrine()->getManager();
        $creditosaaux = $em->getRepository('ModulosPersonasBundle:ProductoDeCreditos')->findAll();
        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);

        foreach ($entity as $entity) {
            $entity->setAporteAdicional(round($entity->getAporteAdicional(), 2));
            $entity->setAporteMinimo(round($entity->getAporteMinimo(), 2));
            $estadoCreditos = $entity->getEstadocreditos();
            $entity->setEstadocreditos($estadoCreditos);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Creditos entity.');
        }

        $editForm = $this->createForm(new CreditosType(), $entity);
        $editForm->handleRequest($request);

        //$entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);

        $pagosrealizados = $em->getRepository(
            'ModulosPersonasBundle:PagoCuotaCredito'
        )->findPagosCuotasCreditos($id);

        $amortizaciones = $em->getRepository(
            'ModulosPersonasBundle:TablaAmortizacion'
        )->findTablasAmortizacionPorCreditos($id);
        /*for ($i = 0; $i < count($amortizaciones); $i++) {
            $amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
            $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
            $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
            $amortizaciones[$i]->setDesgravamen(round($amortizaciones[$i]->getDesgravamen(), 2));
            $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
        }*/

        if ($editForm->isValid()) {

            $estadoCreditos = $entity->getEstadocreditos();
            $entity->setEstadocreditos($estadoCreditos);
//                echo $entity->getNumeroDePagos();
//                die();
            $amortizacionesToDelete = $em->getRepository(
                'ModulosPersonasBundle:TablaAmortizacion'
            )->findTablasAmortizacionPorCreditos($id);

            foreach ($amortizacionesToDelete as $amort) {
                $em->remove($amort);
            }
            $em->flush();

            if (!$entity->getFrecuencia_pago() == "" && !$entity->getFechaSolicitud() == "" && !$entity->getNumeroDePagos() == "" && !$entity->getMontoSolicitado() == "" && !$entity->getInteresAnual() == ""
            ) {
                if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA FIJA") {//Francés
                    $this->getCuotaConstante($em, $entity);
                } else if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE") {//Francés
                    $this->getCuotaCapitalConstante($em, $entity);
                } else if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE FIJO") {//Francés
                    $this->getCuotaCapitalConstanteFijo($em, $entity);
                }
            }

            $amortizaciones = $em->getRepository(
                'ModulosPersonasBundle:TablaAmortizacion'
            )->findTablasAmortizacionPorCreditos($id);
            /*for ($i = 0; $i < count($amortizaciones); $i++) {
                $amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
                $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                $amortizaciones[$i]->setDesgravamen(round($amortizaciones[$i]->getDesgravamen(), 2));
                $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
            }*/
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el credito'
            );
            return $this->redirect($this->generateUrl('creditos_update', array('id' => $id)));
        }

        $documentos = $em->getRepository('ModulosPersonasBundle:CreditosDocumento')->findCreditoDocumentoPorCredito($id);
        return $this->render(
            'ModulosPersonasBundle:Creditos:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'amortizaciones' => $amortizaciones,
                'reglasInvalidas' => $reglasInvalidas,
                'tiposCreditos' => $creditosaaux,
                'documentos' => $documentos,
                'pagosrealizados' => $pagosrealizados
            )
        );
    }

    /*
     * El capital a pagar es fijo y el interes varía entonces de mas a menos
     * El total a pagar varía de mas a menos
     * El pago de Desgravamen se aplica por un porciento y en cada periodo se paga ese mismo porciento
     */
    public function getCuotaCapitalConstante($em, $entity)
    {
        $tipoCredito = $entity->getIdProductosDeCreditos();
        $DESGRAVAMEN = $tipoCredito->getDesgravamen();
        $TOTAL_AMORTIZADO = 0;
        $RESTO_A_AMORTIZAR = $entity->getMontoSolicitado();
        $periodos = $entity->getNumeroDePagos();
        $interesAnual = $entity->getInteresAnual();//dividir el interes anual por 12

        $parteFrecuencia = $this->getValorFrecuencia($entity->getFrecuencia_pago());

        $interes = ($interesAnual / $parteFrecuencia) / 100;

        $incrementoFecha = (1) . 'M';
        switch ($entity->getFrecuencia_pago()) {
            case "DIARIA": {
                $incrementoFecha = (1) . 'D';
            }
                break;
            case "SEMANAL": {
                $incrementoFecha = (7) . 'D';
            }
                break;
            case "MENSUAL": {
                $incrementoFecha = (1) . 'M';
            }
                break;
            case "TRIMESTRAL": {
                $incrementoFecha = (3) . 'M';
            }
                break;
            case "SEMESTRAL": {
                $incrementoFecha = (6) . 'M';
            }
                break;
            case "ANUAL": {
                $incrementoFecha = (1) . 'Y';
            }
                break;
        }
        $rowamortizacion = new TablaAmortizacion();
        $rowamortizacion->setSaldo($RESTO_A_AMORTIZAR);
        $rowamortizacion->setFechaDePago($entity->getFechaSolicitud());
        $rowamortizacion->setCuota(0);
        $rowamortizacion->setValorcuota(0);
        $rowamortizacion->setDesgravamen(0);
        $rowamortizacion->setInteres(0);
        $rowamortizacion->setCapital(0);
        $rowamortizacion->setCreditoId($entity);
        $em->persist($rowamortizacion);
        $em->flush();

        $sumaCapital = 0;
        $sumaCapitalAnterior = 0;
        for ($i = $periodos; $i > 0; $i--) {
            $AMORTIZACION = $entity->getMontoSolicitado() / $entity->getNumeroDePagos();
            $INTERESES = $RESTO_A_AMORTIZAR * $interes;
            $MENSUALIDAD = $AMORTIZACION + $INTERESES + $DESGRAVAMEN;
            $TOTAL_AMORTIZADO += $AMORTIZACION;
            $RESTO_A_AMORTIZAR = $RESTO_A_AMORTIZAR - $AMORTIZACION;
            $rowamortizacionnueva = new TablaAmortizacion();
            $rowamortizacionnueva->setSaldo($RESTO_A_AMORTIZAR);

            $fechacuota = $entity->getFechaSolicitud();
            $intervalo = new \DateInterval('P' . ($incrementoFecha));
            $fechacuota->add($intervalo);
            $rowamortizacionnueva->setFechaDePago($fechacuota);

            if ($i != 1) {
                $sumaCapitalAnterior += number_format($AMORTIZACION, 2, '.', '');
            }

            $sumaCapital += number_format($AMORTIZACION, 2, '.', '');

            if ($i == 1) {
                if ($sumaCapital > $entity->getMontoSolicitado() || $sumaCapital < $entity->getMontoSolicitado()) {
                    $rowamortizacionnueva->setCuota($periodos - $i + 1);
                    $rowamortizacionnueva->setValorcuota(number_format(($INTERESES + $DESGRAVAMEN + ($entity->getMontoSolicitado() - $sumaCapitalAnterior)), 2, '.', ''));
                    $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                    $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                    $rowamortizacionnueva->setCapital(number_format(($entity->getMontoSolicitado() - $sumaCapitalAnterior), 2, '.', ''));
                    $rowamortizacionnueva->setCreditoId($entity);
                    $em->persist($rowamortizacionnueva);
                    $em->flush();
                } elseif ($sumaCapital == $entity->getMontoSolicitado()) {
                    $rowamortizacionnueva->setCuota($periodos - $i + 1);
                    $rowamortizacionnueva->setValorcuota(number_format($MENSUALIDAD, 2, '.', ''));
                    $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                    $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                    $rowamortizacionnueva->setCapital(number_format($AMORTIZACION, 2, '.', ''));
                    $rowamortizacionnueva->setCreditoId($entity);
                    $em->persist($rowamortizacionnueva);
                    $em->flush();
                }
            } else {
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
    public function getCuotaCapitalConstanteFijo($em, $entity)
    {
        $tipoCredito = $entity->getIdProductosDeCreditos();
        $TOTAL_AMORTIZADO = 0;
        $RESTO_A_AMORTIZAR = $entity->getMontoSolicitado();
        $periodos = $entity->getNumeroDePagos();
        $interesAnual = $entity->getInteresAnual();//dividir el interes anual por 12
        $parteFrecuencia = $this->getValorFrecuencia($entity->getFrecuencia_pago());

        $interes = (($interesAnual / $parteFrecuencia) * $periodos) / 100;

        $incrementoFecha = (1) . 'M';
        switch ($entity->getFrecuencia_pago()) {
            case "DIARIA": {
                $incrementoFecha = (1) . 'D';
            }
                break;
            case "SEMANAL": {
                $incrementoFecha = (7) . 'D';
            }
                break;
            case "MENSUAL": {
                $incrementoFecha = (1) . 'M';
            }
                break;
            case "TRIMESTRAL": {
                $incrementoFecha = (3) . 'M';
            }
                break;
            case "SEMESTRAL": {
                $incrementoFecha = (6) . 'M';
            }
                break;
            case "ANUAL": {
                $incrementoFecha = (1) . 'Y';
            }
                break;
        }

        $DESGRAVAMEN = $tipoCredito->getDesgravamen() * ($entity->getMontoSolicitado() / 100);

        $rowamortizacion = new TablaAmortizacion();
        $rowamortizacion->setSaldo($RESTO_A_AMORTIZAR);
        $rowamortizacion->setFechaDePago($entity->getFechaSolicitud());
        $rowamortizacion->setCuota(0);
        $rowamortizacion->setValorcuota($DESGRAVAMEN);
        $rowamortizacion->setDesgravamen($DESGRAVAMEN);
        $rowamortizacion->setInteres(0);
        $rowamortizacion->setCapital(0);
        $rowamortizacion->setCreditoId($entity);
        $em->persist($rowamortizacion);
        $em->flush();

        $sumaCapital = 0;
        $sumaCapitalAnterior = 0;
        for ($i = $periodos; $i > 0; $i--) {
            $DESGRAVAMEN = 0;
            $AMORTIZACION = $entity->getMontoSolicitado() / $entity->getNumeroDePagos();
            $INTERESES = ($entity->getMontoSolicitado() / $periodos) * $interes;
            $MENSUALIDAD = $AMORTIZACION + $INTERESES + $DESGRAVAMEN;
            $TOTAL_AMORTIZADO += $AMORTIZACION;
            $RESTO_A_AMORTIZAR = $RESTO_A_AMORTIZAR - $AMORTIZACION;
            $rowamortizacionnueva = new TablaAmortizacion();
            $rowamortizacionnueva->setSaldo($RESTO_A_AMORTIZAR);

            $fechacuota = $entity->getFechaSolicitud();
            $intervalo = new \DateInterval('P' . ($incrementoFecha));
            $fechacuota->add($intervalo);
            $rowamortizacionnueva->setFechaDePago($fechacuota);

            if ($i != 1) {
                $sumaCapitalAnterior += number_format($AMORTIZACION, 2, '.', '');
            }

            $sumaCapital += number_format($AMORTIZACION, 2, '.', '');

            if ($i == 1) {
                if ($sumaCapital > $entity->getMontoSolicitado() || $sumaCapital < $entity->getMontoSolicitado()) {
                    $rowamortizacionnueva->setCuota($periodos - $i + 1);
                    $rowamortizacionnueva->setValorcuota(number_format(($INTERESES + $DESGRAVAMEN + ($entity->getMontoSolicitado() - $sumaCapitalAnterior)), 2, '.', ''));
                    $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                    $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                    $rowamortizacionnueva->setCapital(number_format(($entity->getMontoSolicitado() - $sumaCapitalAnterior), 2, '.', ''));
                    $rowamortizacionnueva->setCreditoId($entity);
                    $em->persist($rowamortizacionnueva);
                    $em->flush();
                } elseif ($sumaCapital == $entity->getMontoSolicitado()) {
                    $rowamortizacionnueva->setCuota($periodos - $i + 1);
                    $rowamortizacionnueva->setValorcuota(number_format($MENSUALIDAD, 2, '.', ''));
                    $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                    $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                    $rowamortizacionnueva->setCapital(number_format($AMORTIZACION, 2, '.', ''));
                    $rowamortizacionnueva->setCreditoId($entity);
                    $em->persist($rowamortizacionnueva);
                    $em->flush();
                }
            } else {
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
    public function getCuotaConstante($em, $entity)
    {
        $tipoCredito = $entity->getIdProductosDeCreditos();
        $TOTAL_AMORTIZADO = 0;
        $RESTO_A_AMORTIZAR = $entity->getMontoSolicitado();
        $periodos = $entity->getNumeroDePagos();
        $interesAnual = $entity->getInteresAnual();//dividir el interes anual por 12
        $parteFrecuencia = $this->getValorFrecuencia($entity->getFrecuencia_pago());
        $interes = ($interesAnual / $parteFrecuencia) / 100;
        $incrementoFecha = (1) . 'M';
        switch ($entity->getFrecuencia_pago()) {
            case "DIARIA": {
                $incrementoFecha = (1) . 'D';
            }
                break;
            case "SEMANAL": {
                $incrementoFecha = (7) . 'D';
            }
                break;
            case "MENSUAL": {
                $incrementoFecha = (1) . 'M';
            }
                break;
            case "TRIMESTRAL": {
                $incrementoFecha = (3) . 'M';
            }
                break;
            case "SEMESTRAL": {
                $incrementoFecha = (6) . 'M';
            }
                break;
            case "ANUAL": {
                $incrementoFecha = (1) . 'Y';
            }
                break;
        }
        $DESGRAVAMEN = $tipoCredito->getDesgravamen() * ($entity->getMontoSolicitado() / 100);

        $rowamortizacion = new TablaAmortizacion();
        $rowamortizacion->setSaldo($RESTO_A_AMORTIZAR);
        $rowamortizacion->setFechaDePago($entity->getFechaSolicitud());
        $rowamortizacion->setCuota(0);
        $rowamortizacion->setValorcuota($DESGRAVAMEN);
        $rowamortizacion->setInteres(0);
        $rowamortizacion->setDesgravamen($DESGRAVAMEN);
        $rowamortizacion->setCapital(0);
        $rowamortizacion->setCreditoId($entity);
        $em->persist($rowamortizacion);
        $em->flush();

        $sumaCapital = 0;
        $sumaCapitalAnterior = 0;
        for ($i = $periodos; $i > 0; $i--) {
            $DESGRAVAMEN = 0;
            $MENSUALIDAD = $RESTO_A_AMORTIZAR * $interes / (1 - pow((1 + $interes), -$i));
            $INTERESES = $RESTO_A_AMORTIZAR * $interes;
            $AMORTIZACION = $MENSUALIDAD - $INTERESES;
            $TOTAL_AMORTIZADO += $AMORTIZACION;
            $RESTO_A_AMORTIZAR = $RESTO_A_AMORTIZAR - $AMORTIZACION;
            $rowamortizacionnueva = new TablaAmortizacion();
            $rowamortizacionnueva->setSaldo($RESTO_A_AMORTIZAR);

            $fechacuota = $entity->getFechaSolicitud();
            $intervalo = new \DateInterval('P' . ($incrementoFecha));
            $fechacuota->add($intervalo);
            $rowamortizacionnueva->setFechaDePago($fechacuota);

            if ($i != 1) {
                $sumaCapitalAnterior += number_format($AMORTIZACION, 2, '.', '');
            }

            $sumaCapital += number_format($AMORTIZACION, 2, '.', '');

            if ($i == 1) {
                if ($sumaCapital > $entity->getMontoSolicitado() || $sumaCapital < $entity->getMontoSolicitado()) {
                    $rowamortizacionnueva->setCuota($periodos - $i + 1);
                    $rowamortizacionnueva->setValorcuota(number_format(($INTERESES + $DESGRAVAMEN + ($entity->getMontoSolicitado() - $sumaCapitalAnterior)), 2, '.', ''));
                    $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                    $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                    $rowamortizacionnueva->setCapital(number_format(($entity->getMontoSolicitado() - $sumaCapitalAnterior), 2, '.', ''));
                    $rowamortizacionnueva->setCreditoId($entity);
                    $em->persist($rowamortizacionnueva);
                    $em->flush();
                } elseif ($sumaCapital == $entity->getMontoSolicitado()) {
                    $rowamortizacionnueva->setCuota($periodos - $i + 1);
                    $rowamortizacionnueva->setValorcuota(number_format($MENSUALIDAD, 2, '.', ''));
                    $rowamortizacionnueva->setInteres(number_format($INTERESES, 2, '.', ''));
                    $rowamortizacionnueva->setDesgravamen(number_format($DESGRAVAMEN, 2, '.', ''));
                    $rowamortizacionnueva->setCapital(number_format($AMORTIZACION, 2, '.', ''));
                    $rowamortizacionnueva->setCreditoId($entity);
                    $em->persist($rowamortizacionnueva);
                    $em->flush();
                }
            } else {
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

    public function getValorFrecuencia($frecuencia)
    {
        switch ($frecuencia) {
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

    /**
     * Deletes a Creditos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el crédito'
            );
        } catch (\Doctrine\DBAL\DBALException $e) {
            if ($e->getCode() == 0) {
                if ($e->getPrevious()->getCode() == 23000) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        "No se puede eliminar porque tiene registros relacionados."
                    );

                    return $this->redirect($this->generateUrl('creditos'));
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }

        return $this->redirect($this->generateUrl('creditos'));
    }


    /**
     * Creates a form to delete a Creditos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('creditos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    /**
     * Displays a form to pay a part existing Creditos entity.
     *
     */
    public function pagoAction($id, Request $request)
    {
        $entityPago = new PagoCuotaCredito();
        $variable = 0;
        $estadopago = "pago";

        $em = $this->getDoctrine()->getManager();

        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);

        // $tiposProductosContables = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findAll();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Creditos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        $amortizaciones = $em->getRepository(
            'ModulosPersonasBundle:TablaAmortizacion'
        )->findTablasAmortizacionPorCreditos($id);


        $pagosrealizados = $em->getRepository(
            'ModulosPersonasBundle:PagoCuotaCredito'
        )->findPagosCuotasCreditos($id);

        //if($editForm->isValid()){

        /*for ($i = 1; $i < count($amortizaciones); $i++) {
            $amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
            $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
            $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
            $amortizaciones[$i]->setDesgravamen(round($amortizaciones[$i]->getDesgravamen(), 2));
            $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
            //ECHO $amortizaciones[$i]->getId()." t";
        }*/


        $estadoLibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneByEstado("ABIERTO");
        $interesADescontar = 0;
        $valorApagarConInteres = 0;

        if (count($amortizaciones) > 1 && (count($amortizaciones) - 1 > count($pagosrealizados))) {
            $entityPago->setValorIngresado(number_format($amortizaciones[count($pagosrealizados) + 1]->getValorcuota(), 2, '.', ' '));
            $interesADescontar = number_format($amortizaciones[count($pagosrealizados) + 1]->getInteres(), 2, '.', ' ');
            $valorApagarConInteres = number_format($amortizaciones[count($pagosrealizados) + 1]->getValorcuota(), 2, '.', ' ');

        } else {
            $estadopago = "completado";
            $interesADescontar = 0;
        }


        $ano = date('Y');
        $mes = date('m');
        $dia = date('d');
        $h = date('H') - 5;
        $m = date('i');
        $s = date('s');

        $date = $ano . '-' . $mes . '-' . $dia;
        $date = explode("-", $date);

        $time = $h . ':' . $m . ':' . $s;
        $time = explode(":", $time);

        $fechak = new \DateTime();
        $fechak->setDate($date[0], $date[1], $date[2]);
        $fechak->setTime($time[0], $time[1], $time[2]);

        $entityPago->setFechaDePago($fechak);
        $entityPago->setCreditoId($entity);
        $entityPago->setSininteres(false);

        $new_pago_form = $this->createForm(new PagoCuotaCreditoType(), $entityPago);
        $new_pago_form->handleRequest($request);

        if ($new_pago_form->isValid()) {
            if (count($libros)) {
                foreach ($libros as $libro) {

                    /*if ($libro->getEstadosLibro()->getEstado() == 'ABIERTO' && $libro->getFecha()->format(
                            'm-Y'
                        ) != $libro->getFecha()->format('m-Y')
                    ) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'No se puede crear un libro en un mes, si hay un mes anterior abierto'
                        );

                        return $this->redirect($this->generateUrl('creditos_pago', array('id' => $id)));
                    }*/

                    if ($libro->getEstadosLibro()->getEstado() == 'CERRADO' && $libro->getFecha()->format('m-Y') == $fechak->format('m-Y')
                    ) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'No se puede crear un libro en un mes cerrado.'
                        );
                        return $this->redirect($this->generateUrl('creditos_pago', array('id' => $id)));
                    }
                }
            }

            $new_pago_form->add('submit', 'submit', array('label' => 'Create'));
        }

        return $this->render(
            'ModulosPersonasBundle:Creditos:pago.html.twig',
            array(
                'entity' => $entity,
                'pagocuotaentity' => $entityPago,
                'edit_form' => $editForm->createView(),
                'new_pago_form' => $new_pago_form->createView(),
                'delete_form' => $deleteForm->createView(),
                'amortizaciones' => $amortizaciones,
                'pagosrealizados' => $pagosrealizados,
                'interesDescontado' => $interesADescontar,
                'valorpago' => $valorApagarConInteres,
                'pago' => $estadopago,
            )
        );


    }

    public function pagoMensualAction($id, $sininteres)
    {

        $variable = 0;
        $estadopago = "pago";

        $em = $this->getDoctrine()->getManager();

        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);

        // $tiposProductosContables = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findAll();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Creditos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        $amortizaciones = $em->getRepository(
            'ModulosPersonasBundle:TablaAmortizacion'
        )->findTablasAmortizacionPorCreditos($id);


        $pagosrealizados = $em->getRepository(
            'ModulosPersonasBundle:PagoCuotaCredito'
        )->findPagosCuotasCreditos($id);


        if (count($libros)) {
            foreach ($libros as $libro) {

                if ($libro->getEstadosLibro()->getEstado() == 'ABIERTO' && $libro->getFecha()->format(
                        'm-Y'
                    ) != $libro->getFecha()->format('m-Y')
                ) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'No se puede crear un libro en un mes, si hay un mes anterior abierto'
                    );

                    return $this->redirect($this->generateUrl('creditos_pago', array('id' => $id)));
                }

                if ($libro->getEstadosLibro()->getEstado() == 'CERRADO' && $libro->getFecha()->format(
                        'm-Y'
                    ) == new \DateTime()
                ) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'No se puede crear un libro en un mes cerrado.'
                    );
                    return $this->redirect($this->generateUrl('creditos_pago', array('id' => $id)));
                }
            }
        }
        //if($editForm->isValid()){

        /*for ($i = 1; $i < count($amortizaciones); $i++) {
            $amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
            $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
            $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
            $amortizaciones[$i]->setDesgravamen(round($amortizaciones[$i]->getDesgravamen(), 2));
            $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
            //ECHO $amortizaciones[$i]->getId()." t";
        }*/


        $estadoLibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneByEstado("ABIERTO");

        $entityPago = new PagoCuotaCredito();
        if (count($amortizaciones) > 1 && (count($amortizaciones) - 1 > count($pagosrealizados))) {
            $entityPago->setValorIngresado($amortizaciones[count($pagosrealizados) + 1]->getValorcuota());
            $amortizacion = $amortizaciones[count($pagosrealizados)];
        } else {
            $estadopago = "completado";
            //return $this->redirect($this->generateUrl('creditos'));
        }

        //$tipoTransaccion=$entity->getProductoContableId();
        if ($sininteres == 1) {
            $entityPago->setSininteres(true);
        } else {
            $entityPago->setSininteres(false);
        }
        $entityPago->setFechaDePago($pagosrealizados[count($pagosrealizados) - 1]->getFechaDePago());
        $entityPago->setCreditoId($entity);


        $new_pago_form = $this->createForm(
            new PagoCuotaCreditoType(),
            $entityPago);

        $new_pago_form->add('submit', 'submit', array('label' => 'Create'));
        //$new_pago_form->isSubmitted();

        //
        //if($new_pago_form->add('submit', 'submit', array('label' => 'Create'))){
        if ($amortizacion = $amortizaciones[count($pagosrealizados)]) {

            $librosr = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosReciboDESC();

            if (count($librosr) > 0) {
                if ($librosr[0]->getNumeroRecibo() != null) {
                    $consec = $librosr[0]->getNumeroRecibo() + 1;
                } else {
                    if (count($librosr) > 1) {
                        $consec = $librosr[1]->getNumeroRecibo() + 1;
                    } else {
                        $consec = 1;
                    }
                }

            } else {
                $consec = 1;
            }

            if (count($libros) > 0) {
                $this->actualizarSaldo($libros[count($libros) - 1]);
                $saldo = $libros[0]->getSaldo();

            } else {
                $saldo = 0;
            }

            $transaccionPagoCredito = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findOneById(9);

            if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE") {
                $transaccionPagoDesgravamen = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findOneById(3);

                $libroPagoDesgravamen = new Libro();
                $libroPagoDesgravamen->setFecha($entityPago->getFechaDePago());
                $libroPagoDesgravamen->setPersona($entity->getPersona());
                $libroPagoDesgravamen->setDebe($amortizacion->getDesgravamen());
                $libroPagoDesgravamen->setHaber(0);
                $libroPagoDesgravamen->setProductoContableId($transaccionPagoDesgravamen);
                $libroPagoDesgravamen->setCuentaid($transaccionPagoDesgravamen->getCuentaHaber());
                $libroPagoDesgravamen->setInfo($id);
                //$libroPagoDesgravamen->setSaldo(
                //    $saldo + $libroPagoDesgravamen->getDebe()
                //);
                $libroPagoDesgravamen->setNumeroRecibo($consec);
                $libroPagoDesgravamen->setEstadosLibro($estadoLibro);

                //$entity->setNumeroRecibo($entity->getNumeroRecibo() + 2);

                $VCHRPagoDesgravamen = new VCHR();
                $VCHRPagoDesgravamen->setFecha($libroPagoDesgravamen->getFecha());
                $VCHRPagoDesgravamen->setMes($libroPagoDesgravamen->getFecha()->format('m'));
                $VCHRPagoDesgravamen->setLibroId($libroPagoDesgravamen);


                $DTVCPagoDesgravamen = new DTVC();

                $DTVCPagoDesgravamen->setCuentaDeudoraId(
                    $libroPagoDesgravamen->getProductoContableId()->getCuentaHaber()
                );
                $DTVCPagoDesgravamen->setCuentaAcreedoraId(
                    $libroPagoDesgravamen->getProductoContableId()->getCuentaDebe()
                );
                $DTVCPagoDesgravamen->setValor(
                    $libroPagoDesgravamen->getDebe() + $libroPagoDesgravamen->getHaber()
                );
                $DTVCPagoDesgravamen->setIdVchr($VCHRPagoDesgravamen);
                $DTVCPagoDesgravamen->setEsDebe($libroPagoDesgravamen->getDebe() > 0);

                $em->persist($libroPagoDesgravamen);
                $em->persist($VCHRPagoDesgravamen);
                $em->persist($DTVCPagoDesgravamen);

                $em->flush();

                //$this->actualizarSaldo($libroPagoDesgravamen);
            }

            $libroPagoDeuda = new Libro();
            $libroPagoDeuda->setFecha($entityPago->getFechaDePago());
            $libroPagoDeuda->setPersona($entity->getPersona());
            $libroPagoDeuda->setDebe($amortizacion->getCapital());//$entityPago->getValorIngresado());//$amortizacion->getCapital());
            $libroPagoDeuda->setHaber(0);
            $libroPagoDeuda->setProductoContableId($transaccionPagoCredito);
            $libroPagoDeuda->setCuentaid($transaccionPagoCredito->getCuentaHaber());
            $libroPagoDeuda->setInfo($id);
            //$libroPagoDeuda->setSaldo($saldo);
            //  $saldo + $amortizacion->getCapital());
            if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE") {
                $libroPagoDeuda->setNumeroRecibo($consec + 1);
            } else {
                $libroPagoDeuda->setNumeroRecibo($consec);
            }

            $libroPagoDeuda->setEstadosLibro($estadoLibro);

            $VCHRPagoDeuda = new VCHR();
            $VCHRPagoDeuda->setFecha($libroPagoDeuda->getFecha());
            $VCHRPagoDeuda->setMes($libroPagoDeuda->getFecha()->format('m'));
            $VCHRPagoDeuda->setLibroId($libroPagoDeuda);


            $DTVCPagoDeuda = new DTVC();

            $DTVCPagoDeuda->setCuentaDeudoraId($libroPagoDeuda->getProductoContableId()->getCuentaHaber());
            $DTVCPagoDeuda->setCuentaAcreedoraId($libroPagoDeuda->getProductoContableId()->getCuentaDebe());
            $DTVCPagoDeuda->setValor($libroPagoDeuda->getDebe() + $libroPagoDeuda->getHaber());
            $DTVCPagoDeuda->setIdVchr($VCHRPagoDeuda);
            $DTVCPagoDeuda->setEsDebe($libroPagoDeuda->getDebe() > 0);


            $em->persist($libroPagoDeuda);
            $em->persist($VCHRPagoDeuda);
            $em->persist($DTVCPagoDeuda);

            $em->flush();

            if ($sininteres != 1) {
                $transaccionPagoInteres = $em->getRepository(
                    'ModulosPersonasBundle:TipoProductoContable'
                )->findOneByTipo("Pago Interés");


                $libroPagoInteres = new Libro();
                $libroPagoInteres->setFecha($entityPago->getFechaDePago());
                $libroPagoInteres->setPersona($entity->getPersona());
                $libroPagoInteres->setDebe($amortizacion->getInteres());
                $libroPagoInteres->setHaber(0);
                $libroPagoInteres->setProductoContableId($transaccionPagoInteres);
                $libroPagoInteres->setCuentaid($transaccionPagoInteres->getCuentaHaber());
                $libroPagoInteres->setInfo($id);
                //$libroPagoInteres->setSaldo($libroPagoDeuda->getSaldo() + $libroPagoInteres->getDebe());

                if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE") {
                    $libroPagoInteres->setNumeroRecibo($consec + 2);
                } else {
                    $libroPagoInteres->setNumeroRecibo($consec + 1);
                }


                $libroPagoInteres->setEstadosLibro($estadoLibro);

                $this->actualizarSaldo($libros[count($libros) - 1]);

                $VCHRPagoInteres = new VCHR();
                $VCHRPagoInteres->setFecha($libroPagoInteres->getFecha());
                $VCHRPagoInteres->setMes($libroPagoInteres->getFecha()->format('m'));
                $VCHRPagoInteres->setLibroId($libroPagoInteres);


                $DTVCPagoInteres = new DTVC();

                $DTVCPagoInteres->setCuentaDeudoraId($libroPagoInteres->getProductoContableId()->getCuentaHaber());
                $DTVCPagoInteres->setCuentaAcreedoraId($libroPagoInteres->getProductoContableId()->getCuentaDebe());
                $DTVCPagoInteres->setValor($libroPagoInteres->getDebe() + $libroPagoInteres->getHaber());
                $DTVCPagoInteres->setIdVchr($VCHRPagoInteres);
                $DTVCPagoInteres->setEsDebe($libroPagoInteres->getDebe() > 0);

                $em->persist($libroPagoInteres);
                $em->persist($VCHRPagoInteres);
                $em->persist($DTVCPagoInteres);

                $em->flush();


            }

            //$this->actualizarSaldo($libroPagoDeuda);
            if ($sininteres != 1) {
                //$this->actualizarSaldo($libroPagoInteres);
            }

            $valorAux = 0;
            for ($i = 0; $i < count($pagosrealizados); $i++) {
                $valorAux += $pagosrealizados[$i]->getSininteres();

                if ($valorAux > 0) {
                    $entity->setSininteres(1);
                    $em->persist($entity);
                    $em->flush();
                } else {
                    $entity->setSininteres(0);
                    $em->persist($entity);
                    $em->flush();
                }
            }
        }


        return $this->redirect($this->generateUrl('creditos_pago', array('id' => $id)));


    }

    public function liquidacionTotalCreditoAction($id, $ano, $mes, $dia)
    {
        $em = $this->getDoctrine()->getManager();

        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);

        $amortizaciones = $em->getRepository('ModulosPersonasBundle:TablaAmortizacion')->findTablasAmortizacionPorCreditos($id);

        $pagosrealizados = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->findPagosCuotasCreditos($id);

        if (count($libros)) {
            foreach ($libros as $libro) {

                /*if ($libro->getEstadosLibro()->getEstado() == 'ABIERTO' && $libro->getFecha()->format(
                        'm-Y'
                    ) != $libro->getFecha()->format('m-Y')
                ) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'No se puede crear un libro en un mes, si hay un mes anterior abierto'
                    );

                    return $this->redirect($this->generateUrl('creditos_pago', array('id' => $id)));
                }*/

                if ($libro->getEstadosLibro()->getEstado() == 'CERRADO' && $libro->getFecha()->format('m-Y') == $mes . '-' . $ano) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'No se puede crear un libro en un mes cerrado.'
                    );
                    return $this->redirect($this->generateUrl('creditos_pago', array('id' => $id)));
                }
            }
        }

        $estadoLibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneByEstado("ABIERTO");

        $librosr = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosReciboDESC();

        $horas = $librosr[0]->getFecha()->format('H');
        $min = $librosr[0]->getFecha()->format('i');
        $sec = $librosr[0]->getFecha()->format('s');

        $date = $ano . '-' . $mes . '-' . $dia;
        $date = explode("-", $date);

        $time = $horas . ':' . $min . ':' . $sec;
        $time = explode(":", $time);

        $fechak = new \DateTime();
        $fechak->setDate($date[0], $date[1], $date[2]);
        $fechak->setTime($time[0], $time[1], $time[2]);
        //$fecha = date_format($fechak, 'Y-m-d H:i:s');
        $fecha = $fechak;

        if (count($librosr) > 0) {
            if ($librosr[0]->getNumeroRecibo() != null) {
                $consec = $librosr[0]->getNumeroRecibo() + 1;
            } else {
                if (count($librosr) > 1) {
                    $consec = $librosr[1]->getNumeroRecibo() + 1;
                } else {
                    $consec = 1;
                }
            }

        } else {
            $consec = 1;
        }

        if (count($libros) > 0) {
            $this->actualizarSaldo($libros[count($libros) - 1]);
            $saldo = $libros[0]->getSaldo();

        } else {
            $saldo = 0;
        }

        $valorCapitalAmort = 0;
        $valorInteresAmort = 0;
        $valorDesgraAmort = 0;


        for ($i = (count($pagosrealizados) + 1); $i < count($amortizaciones); $i++) {
            $valorCapitalAmort += $amortizaciones[$i]->getCapital();
            $valorDesgraAmort += $amortizaciones[$i]->getDesgravamen();

            if ($amortizaciones[$i]->getFechaDePago()->format('Y-m-d') <= $fecha->format('Y-m-d')) {
                $interes = $amortizaciones[$i]->getInteres();
            } else {
                $interes = 0;
            }

            $valorInteresAmort += $interes;

            $pagoCredito = new PagoCuotaCredito();
            $pagoCredito->setValorIngresado($amortizaciones[$i]->getCapital() + $amortizaciones[$i]->getDesgravamen() + $interes);
            $pagoCredito->setFechaDePago($fecha);//$amortizaciones[$i]->getFechaDePago());
            $pagoCredito->setCreditoId($entity);
            $em->persist($pagoCredito);
            $em->flush();
        }

        $valorCreditos = $valorCapitalAmort;
        $valorInteresCreditos = $valorInteresAmort;//$amortizaciones[count($pagosrealizados) + 1]->getInteres();
        $valorDesgraCreditos = $valorDesgraAmort;

        $transaccionPagoCredito = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findOneById(9);

        if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE") {
            $transaccionPagoDesgravamen = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findOneById(3);

            $libroPagoDesgravamen = new Libro();
            $libroPagoDesgravamen->setFecha($fecha);//$amortizaciones[count($pagosrealizados) + 1]->getFechaDePago());
            $libroPagoDesgravamen->setPersona($entity->getPersona());
            $libroPagoDesgravamen->setDebe($valorDesgraCreditos);
            $libroPagoDesgravamen->setHaber(0);
            $libroPagoDesgravamen->setProductoContableId($transaccionPagoDesgravamen);
            $libroPagoDesgravamen->setCuentaid($transaccionPagoDesgravamen->getCuentaHaber());
            $libroPagoDesgravamen->setInfo($entity);
            $libroPagoDesgravamen->setNumeroRecibo($consec);
            $libroPagoDesgravamen->setEstadosLibro($estadoLibro);

            $VCHRPagoDesgravamen = new VCHR();
            $VCHRPagoDesgravamen->setFecha($libroPagoDesgravamen->getFecha());
            $VCHRPagoDesgravamen->setMes($libroPagoDesgravamen->getFecha()->format('m'));
            $VCHRPagoDesgravamen->setLibroId($libroPagoDesgravamen);

            $DTVCPagoDesgravamen = new DTVC();
            $DTVCPagoDesgravamen->setCuentaDeudoraId($libroPagoDesgravamen->getProductoContableId()->getCuentaHaber());
            $DTVCPagoDesgravamen->setCuentaAcreedoraId($libroPagoDesgravamen->getProductoContableId()->getCuentaDebe());
            $DTVCPagoDesgravamen->setValor($libroPagoDesgravamen->getDebe() + $libroPagoDesgravamen->getHaber());
            $DTVCPagoDesgravamen->setIdVchr($VCHRPagoDesgravamen);
            $DTVCPagoDesgravamen->setEsDebe($libroPagoDesgravamen->getDebe() > 0);

            $em->persist($libroPagoDesgravamen);
            $em->persist($VCHRPagoDesgravamen);
            $em->persist($DTVCPagoDesgravamen);

            $em->flush();

            //$this->actualizarSaldo($libroPagoDesgravamen);
        }

        $libroPagoDeuda = new Libro();
        $libroPagoDeuda->setFecha($fecha);//$amortizaciones[count($pagosrealizados) + 1]->getFechaDePago());
        $libroPagoDeuda->setPersona($entity->getPersona());
        $libroPagoDeuda->setDebe($valorCreditos);//$entityPago->getValorIngresado());//$amortizacion->getCapital());
        $libroPagoDeuda->setHaber(0);
        $libroPagoDeuda->setProductoContableId($transaccionPagoCredito);
        $libroPagoDeuda->setCuentaid($transaccionPagoCredito->getCuentaHaber());
        $libroPagoDeuda->setInfo($entity);
        $libroPagoDeuda->setSaldo($saldo);
        if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE") {
            $libroPagoDeuda->setNumeroRecibo($consec + 1);
        } else {
            $libroPagoDeuda->setNumeroRecibo($consec);
        }

        $libroPagoDeuda->setEstadosLibro($estadoLibro);

        $VCHRPagoDeuda = new VCHR();
        $VCHRPagoDeuda->setFecha($libroPagoDeuda->getFecha());
        $VCHRPagoDeuda->setMes($libroPagoDeuda->getFecha()->format('m'));
        $VCHRPagoDeuda->setLibroId($libroPagoDeuda);

        $DTVCPagoDeuda = new DTVC();
        $DTVCPagoDeuda->setCuentaDeudoraId($libroPagoDeuda->getProductoContableId()->getCuentaHaber());
        $DTVCPagoDeuda->setCuentaAcreedoraId($libroPagoDeuda->getProductoContableId()->getCuentaDebe());
        $DTVCPagoDeuda->setValor($libroPagoDeuda->getDebe() + $libroPagoDeuda->getHaber());
        $DTVCPagoDeuda->setIdVchr($VCHRPagoDeuda);
        $DTVCPagoDeuda->setEsDebe($libroPagoDeuda->getDebe() > 0);

        $em->persist($libroPagoDeuda);
        $em->persist($VCHRPagoDeuda);
        $em->persist($DTVCPagoDeuda);

        $em->flush();

        $transaccionPagoInteres = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findOneByTipo("Pago Interés");

        $libroPagoInteres = new Libro();
        $libroPagoInteres->setFecha($fecha);//$amortizaciones[count($pagosrealizados) + 1]->getFechaDePago());
        $libroPagoInteres->setPersona($entity->getPersona());
        $libroPagoInteres->setDebe($valorInteresCreditos);
        $libroPagoInteres->setHaber(0);
        $libroPagoInteres->setProductoContableId($transaccionPagoInteres);
        $libroPagoInteres->setCuentaid($transaccionPagoInteres->getCuentaHaber());
        $libroPagoInteres->setInfo($entity);

        if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE") {
            $libroPagoInteres->setNumeroRecibo($consec + 2);
        } else {
            $libroPagoInteres->setNumeroRecibo($consec + 1);
        }

        $this->actualizarSaldo($libros[count($libros) - 1]);

        $libroPagoInteres->setEstadosLibro($estadoLibro);

        $VCHRPagoInteres = new VCHR();
        $VCHRPagoInteres->setFecha($libroPagoInteres->getFecha());
        $VCHRPagoInteres->setMes($libroPagoInteres->getFecha()->format('m'));
        $VCHRPagoInteres->setLibroId($libroPagoInteres);

        $DTVCPagoInteres = new DTVC();
        $DTVCPagoInteres->setCuentaDeudoraId($libroPagoInteres->getProductoContableId()->getCuentaHaber());
        $DTVCPagoInteres->setCuentaAcreedoraId($libroPagoInteres->getProductoContableId()->getCuentaDebe());
        $DTVCPagoInteres->setValor($libroPagoInteres->getDebe() + $libroPagoInteres->getHaber());
        $DTVCPagoInteres->setIdVchr($VCHRPagoInteres);
        $DTVCPagoInteres->setEsDebe($libroPagoInteres->getDebe() > 0);

        $em->persist($libroPagoInteres);
        $em->persist($VCHRPagoInteres);
        $em->persist($DTVCPagoInteres);

        $em->flush();

        //$this->actualizarSaldo($libroPagoDeuda);


        return $this->redirect($this->generateUrl('creditos_pago', array('id' => $id)));
    }


    public function liquidacionParcialCreditoFormAction($id)
    {
        $estadopago = "pago";

        $em = $this->getDoctrine()->getManager();

        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Creditos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        $amortizaciones = $em->getRepository(
            'ModulosPersonasBundle:TablaAmortizacion'
        )->findTablasAmortizacionPorCreditos($id);


        $pagosrealizados = $em->getRepository(
            'ModulosPersonasBundle:PagoCuotaCredito'
        )->findPagosCuotasCreditos($id);

        /*for ($i = 1; $i < count($amortizaciones); $i++) {
            $amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
            $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
            $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
            $amortizaciones[$i]->setDesgravamen(round($amortizaciones[$i]->getDesgravamen(), 2));
            $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
            //ECHO $amortizaciones[$i]->getId()." t";
        }*/

        $estadoLibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneByEstado("ABIERTO");

        $entityPago = new PagoCuotaCredito();
        $coutas = $entityPago->getCoutas();
        if (count($amortizaciones) > 1 && (count($amortizaciones) - 1 > count($pagosrealizados))) {
            //$entityPago->setValorIngresado($amortizaciones[count($pagosrealizados) + 1]->getValorcuota());
        } else {
            $estadopago = "completado";
        }

        $ano = date('Y');
        $mes = date('m');
        $dia = date('d');
        $h = date('H') - 5;
        $m = date('i');
        $s = date('s');

        $date = $ano . '-' . $mes . '-' . $dia;
        $date = explode("-", $date);

        $time = $h . ':' . $m . ':' . $s;
        $time = explode(":", $time);

        $fechak = new \DateTime();
        $fechak->setDate($date[0], $date[1], $date[2]);
        $fechak->setTime($time[0], $time[1], $time[2]);

        $entityPago->setFechaDePago($fechak);
        $entityPago->setCreditoId($entity);


        $new_pago_form = $this->createForm(new PagoCuotaCreditoType(), $entityPago);

        $new_pago_form->add('submit', 'submit', array('label' => 'Create'));

        $a = count($amortizaciones);
        $b = count($pagosrealizados);

        return $this->render(
            'ModulosPersonasBundle:Creditos:pagoAnticipado.html.twig',
            array(
                'entity' => $entity,
                'amort' => $a,
                'preal' => $b,
                'pagocuotaentity' => $entityPago,
                'edit_form' => $editForm->createView(),
                'new_pago_form' => $new_pago_form->createView(),
                'delete_form' => $deleteForm->createView(),
                'amortizaciones' => $amortizaciones,
                'pagosrealizados' => $pagosrealizados,
                'pago' => $estadopago,
                'coutas' => $coutas,
            )
        );


    }

    public function liquidacionParcialCreditoAction($id, $cuotas, $ano, $mes, $dia, $h, $m, $s)
        //public function liquidacionParcialCredito($id, $cuotas)
    {
        $em = $this->getDoctrine()->getManager();

        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);

        $amortizaciones = $em->getRepository('ModulosPersonasBundle:TablaAmortizacion')->findTablasAmortizacionPorCreditos($id);

        $pagosrealizados = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->findPagosCuotasCreditos($id);

        if (count($libros)) {
            foreach ($libros as $libro) {

                /*if ($libro->getEstadosLibro()->getEstado() == 'ABIERTO' && $libro->getFecha()->format(
                        'm-Y'
                    ) != $libro->getFecha()->format('m-Y')
                ) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'No se puede crear un libro en un mes, si hay un mes anterior abierto'
                    );

                    return $this->redirect($this->generateUrl('libro_create'));
                }*/

                if ($libro->getEstadosLibro()->getEstado() == 'CERRADO' && $libro->getFecha()->format('m-Y') == $mes . '-' . $ano) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'No se puede crear un libro en un mes cerrado.'
                    );

                    return $this->redirect($this->generateUrl('libro_create'));
                }
            }
        }

        $estadoLibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneByEstado("ABIERTO");

        $librosr = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosReciboDESC();

        $horas = $librosr[0]->getFecha()->format('H');
        $min = $librosr[0]->getFecha()->format('i');
        $sec = $librosr[0]->getFecha()->format('s');

        $date = $ano . '-' . $mes . '-' . $dia;
        $date = explode("-", $date);

        $time = $h . ':' . $m . ':' . $s;
        $time = explode(":", $time);

        $fechak = new \DateTime();
        $fechak->setDate($date[0], $date[1], $date[2]);
        $fechak->setTime($time[0], $time[1], $time[2]);
        //$fecha = date_format($fechak, 'Y-m-d H:i:s');
        $fecha = $fechak;

        if (count($librosr) > 0) {
            if ($librosr[0]->getNumeroRecibo() != null) {
                $consec = $librosr[0]->getNumeroRecibo() + 1;
            } else {
                if (count($librosr) > 1) {
                    $consec = $librosr[1]->getNumeroRecibo() + 1;
                } else {
                    $consec = 1;
                }
            }

        } else {
            $consec = 1;
        }

        if (count($libros) > 0) {
            $this->actualizarSaldo($libros[count($libros) - 1]);
            $saldo = $libros[0]->getSaldo();

        } else {
            $saldo = 0;
        }

        $valorCapitalAmort = 0;
        $valorInteresAmort = 0;
        $valorDesgraAmort = 0;

        for ($i = (count($pagosrealizados) + 1); $i < (count($pagosrealizados) + $cuotas + 1); $i++) {
            $valorCapitalAmort += $amortizaciones[$i]->getCapital();
            $valorDesgraAmort += $amortizaciones[$i]->getDesgravamen();

            //if($amortizaciones[$i]->getFechaDePago()->format('d-m-Y') <= $dia.'-'.$mes.'-'.$ano){
            if ($amortizaciones[$i]->getFechaDePago()->format('Y-m-d') <= $ano . '-' . $mes . '-' . $dia) {
                $interes = $amortizaciones[$i]->getInteres();
            } else {
                $interes = 0;
            }

            $valorInteresAmort += $interes;

            $pagoCredito = new PagoCuotaCredito();
            $pagoCredito->setValorIngresado($amortizaciones[$i]->getCapital() + $amortizaciones[$i]->getDesgravamen() + $interes);
            $pagoCredito->setFechaDePago($fecha);//$amortizaciones[$i]->getFechaDePago());
            $pagoCredito->setCreditoId($entity);
            $em->persist($pagoCredito);
            $em->flush();
        }

        $valorCreditos = $valorCapitalAmort;
        $valorInteresCreditos = $valorInteresAmort;//$amortizaciones[count($pagosrealizados) + 1]->getInteres();
        $valorDesgraCreditos = $valorDesgraAmort;

        $transaccionPagoCredito = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findOneById(9);

        if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE") {
            $transaccionPagoDesgravamen = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findOneById(3);

            $libroPagoDesgravamen = new Libro();
            $libroPagoDesgravamen->setFecha($fecha);//$amortizaciones[count($pagosrealizados) + 1]->getFechaDePago());
            $libroPagoDesgravamen->setPersona($entity->getPersona());
            $libroPagoDesgravamen->setDebe($valorDesgraCreditos);
            $libroPagoDesgravamen->setHaber(0);
            $libroPagoDesgravamen->setProductoContableId($transaccionPagoDesgravamen);
            $libroPagoDesgravamen->setCuentaid($transaccionPagoDesgravamen->getCuentaHaber());
            $libroPagoDesgravamen->setInfo($entity);
            $libroPagoDesgravamen->setNumeroRecibo($consec);
            $libroPagoDesgravamen->setEstadosLibro($estadoLibro);

            $VCHRPagoDesgravamen = new VCHR();
            $VCHRPagoDesgravamen->setFecha($libroPagoDesgravamen->getFecha());
            $VCHRPagoDesgravamen->setMes($libroPagoDesgravamen->getFecha()->format('m'));
            $VCHRPagoDesgravamen->setLibroId($libroPagoDesgravamen);

            $DTVCPagoDesgravamen = new DTVC();
            $DTVCPagoDesgravamen->setCuentaDeudoraId($libroPagoDesgravamen->getProductoContableId()->getCuentaHaber());
            $DTVCPagoDesgravamen->setCuentaAcreedoraId($libroPagoDesgravamen->getProductoContableId()->getCuentaDebe());
            $DTVCPagoDesgravamen->setValor($libroPagoDesgravamen->getDebe() + $libroPagoDesgravamen->getHaber());
            $DTVCPagoDesgravamen->setIdVchr($VCHRPagoDesgravamen);
            $DTVCPagoDesgravamen->setEsDebe($libroPagoDesgravamen->getDebe() > 0);

            $em->persist($libroPagoDesgravamen);
            $em->persist($VCHRPagoDesgravamen);
            $em->persist($DTVCPagoDesgravamen);

            $em->flush();

            //$this->actualizarSaldo($libroPagoDesgravamen);
        }

        $libroPagoDeuda = new Libro();
        $libroPagoDeuda->setFecha($fecha);//$amortizaciones[count($pagosrealizados) + 1]->getFechaDePago());
        $libroPagoDeuda->setPersona($entity->getPersona());
        $libroPagoDeuda->setDebe($valorCreditos);//$entityPago->getValorIngresado());//$amortizacion->getCapital());
        $libroPagoDeuda->setHaber(0);
        $libroPagoDeuda->setProductoContableId($transaccionPagoCredito);
        $libroPagoDeuda->setCuentaid($transaccionPagoCredito->getCuentaHaber());
        $libroPagoDeuda->setInfo($entity);
        $libroPagoDeuda->setSaldo($saldo);
        if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE") {
            $libroPagoDeuda->setNumeroRecibo($consec + 1);
        } else {
            $libroPagoDeuda->setNumeroRecibo($consec);
        }

        $libroPagoDeuda->setEstadosLibro($estadoLibro);

        $this->actualizarSaldo($libros[count($libros) - 1]);

        $VCHRPagoDeuda = new VCHR();
        $VCHRPagoDeuda->setFecha($libroPagoDeuda->getFecha());
        $VCHRPagoDeuda->setMes($libroPagoDeuda->getFecha()->format('m'));
        $VCHRPagoDeuda->setLibroId($libroPagoDeuda);

        $DTVCPagoDeuda = new DTVC();
        $DTVCPagoDeuda->setCuentaDeudoraId($libroPagoDeuda->getProductoContableId()->getCuentaHaber());
        $DTVCPagoDeuda->setCuentaAcreedoraId($libroPagoDeuda->getProductoContableId()->getCuentaDebe());
        $DTVCPagoDeuda->setValor($libroPagoDeuda->getDebe() + $libroPagoDeuda->getHaber());
        $DTVCPagoDeuda->setIdVchr($VCHRPagoDeuda);
        $DTVCPagoDeuda->setEsDebe($libroPagoDeuda->getDebe() > 0);

        $em->persist($libroPagoDeuda);
        $em->persist($VCHRPagoDeuda);
        $em->persist($DTVCPagoDeuda);

        $em->flush();

        $transaccionPagoInteres = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findOneByTipo("Pago Interés");

        $libroPagoInteres = new Libro();
        $libroPagoInteres->setFecha($fecha);//$amortizaciones[count($pagosrealizados) + 1]->getFechaDePago());
        $libroPagoInteres->setPersona($entity->getPersona());
        $libroPagoInteres->setDebe($valorInteresCreditos);
        $libroPagoInteres->setHaber(0);
        $libroPagoInteres->setProductoContableId($transaccionPagoInteres);
        $libroPagoInteres->setCuentaid($transaccionPagoInteres->getCuentaHaber());
        $libroPagoInteres->setInfo($entity);

        if ($entity->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() == "CUOTA CAPITAL CONSTANTE") {
            $libroPagoInteres->setNumeroRecibo($consec + 2);
        } else {
            $libroPagoInteres->setNumeroRecibo($consec + 1);
        }


        $libroPagoInteres->setEstadosLibro($estadoLibro);

        $VCHRPagoInteres = new VCHR();
        $VCHRPagoInteres->setFecha($libroPagoInteres->getFecha());
        $VCHRPagoInteres->setMes($libroPagoInteres->getFecha()->format('m'));
        $VCHRPagoInteres->setLibroId($libroPagoInteres);

        $DTVCPagoInteres = new DTVC();
        $DTVCPagoInteres->setCuentaDeudoraId($libroPagoInteres->getProductoContableId()->getCuentaHaber());
        $DTVCPagoInteres->setCuentaAcreedoraId($libroPagoInteres->getProductoContableId()->getCuentaDebe());
        $DTVCPagoInteres->setValor($libroPagoInteres->getDebe() + $libroPagoInteres->getHaber());
        $DTVCPagoInteres->setIdVchr($VCHRPagoInteres);
        $DTVCPagoInteres->setEsDebe($libroPagoInteres->getDebe() > 0);

        $em->persist($libroPagoInteres);
        $em->persist($VCHRPagoInteres);
        $em->persist($DTVCPagoInteres);

        $em->flush();

        //$this->actualizarSaldo($libroPagoDeuda);
        //$this->actualizarSaldo($libroPagoInteres);

        return $this->redirect($this->generateUrl('creditos_pago', array('id' => $id)));
    }

    public function cargarliquidacionParcialCreditoAction($id, $cuotas, $ano, $mes, $dia)
    {
        $em = $this->getDoctrine()->getManager();

        $amortizaciones = $em->getRepository('ModulosPersonasBundle:TablaAmortizacion')->findTablasAmortizacionPorCreditos($id);

        $pagosrealizados = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->findPagosCuotasCreditos($id);

        $valorCapitalAmort = 0;
        $valorInteresAmort = 0;
        $valorDesgraAmort = 0;

        //$fecha = $ano.'-'.$mes;

        for ($i = (count($pagosrealizados) + 1); $i < (count($pagosrealizados) + $cuotas + 1); $i++) {
            $valorCapitalAmort += $amortizaciones[$i]->getCapital();
            $valorDesgraAmort += $amortizaciones[$i]->getDesgravamen();

            //if($amortizaciones[$i]->getFechaDePago()->format('d-m-Y') <= $dia.'-'.$mes.'-'.$ano){
            if ($amortizaciones[$i]->getFechaDePago()->format('Y-m-d') <= $ano . '-' . $mes . '-' . $dia) {
                $interes = $amortizaciones[$i]->getInteres();
            } else {
                $interes = 0;
            }

            $valorInteresAmort += $interes;
        }

        $valorCreditos = $valorCapitalAmort;
        $valorInteresCreditos = $valorInteresAmort;//$amortizaciones[count($pagosrealizados) + 1]->getInteres();
        $valorDesgraCreditos = $valorDesgraAmort;

        $total = $valorCreditos + $valorDesgraCreditos + $valorInteresCreditos;

        return $this->render(
            'ModulosPersonasBundle:Creditos:cargarpagoAnticipado.html.twig',
            array(
                'total' => $total,
                'interes' => $valorInteresCreditos,
                'cuotas' => $valorCreditos,
                'desgravamen' => $valorDesgraCreditos,
            )
        );
    }

    public function exportarCreditosAction()
    {
        $em = $this->getDoctrine()->getManager();
        $creditos = $em->getRepository('ModulosPersonasBundle:Creditos')->findAll();


        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Giulio De Donato")
            ->setTitle("Listado de Creditos")
            ->setSubject("Listado de Creditos")
            ->setDescription("Listado de Creditos")
            ->setKeywords("Listado de Creditos")
            ->setCategory("Reporte excel");

        $tituloReporte = "Reporte de Creditos";
        $titulosColumnas = array('Persona', 'Garante', 'Frecuencia pago', 'Tipo parcialidad', 'Fecha Desembolso', 'Fecha Vencimiento', 'Fecha Solicitud', 'Numero Pagos', 'Monto Solicitado', 'Interes Anual', 'Monto Letras', 'Destino financiamiento', 'Descripcion financiamiento', 'Observaciones', 'Tipo cobro', 'Productos creditos', 'Metodo Amortizacion', 'Estado credito', 'Gasto administrativo', 'Descripcion gasto', 'No Identidad beneficiario', 'Nombre beneficiario');
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A1:D1');

        // Se agregan los titulos del reporte
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', "Reporte de Creditos")
            ->setCellValue('A3', $titulosColumnas[0])
            ->setCellValue('B3', $titulosColumnas[1])
            ->setCellValue('C3', $titulosColumnas[2])
            ->setCellValue('D3', $titulosColumnas[3])
            ->setCellValue('E3', $titulosColumnas[4])
            ->setCellValue('F3', $titulosColumnas[5])
            ->setCellValue('G3', $titulosColumnas[6])
            ->setCellValue('H3', $titulosColumnas[7])
            ->setCellValue('I3', $titulosColumnas[8])
            ->setCellValue('J3', $titulosColumnas[9])
            ->setCellValue('K3', $titulosColumnas[10])
            ->setCellValue('L3', $titulosColumnas[11])
            ->setCellValue('M3', $titulosColumnas[12])
            ->setCellValue('N3', $titulosColumnas[13])
            ->setCellValue('O3', $titulosColumnas[14])
            ->setCellValue('P3', $titulosColumnas[15])
            ->setCellValue('Q3', $titulosColumnas[16])
            ->setCellValue('R3', $titulosColumnas[17])
            ->setCellValue('S3', $titulosColumnas[18])
            ->setCellValue('T3', $titulosColumnas[19])
            ->setCellValue('U3', $titulosColumnas[20])
            ->setCellValue('V3', $titulosColumnas[21]);
        $i = 4;
        for ($j = 0; $j < count($creditos); $j++) {
            $persona = "";
            $frecuenciapago = "";
            $tipoparcialidad = "";
            $tipocobro = "";
            $productosDeCreditos = "";
            $metodoDeAmortizacion = "";
            $getFechaDesembolso = "";
            $getFechaVencimiento = "";
            $getFechaSolicitud = "";

            if ($creditos[$j]->getPersona() != null) {
                $persona = $creditos[$j]->getPersona()->getNombre() . ' ' . $creditos[$j]->getPersona()->getPrimerApellido() . ' ' . $creditos[$j]->getPersona()->getSegundoApellido();

            }
            if ($creditos[$j]->getFrecuencia_pago() != null) {
                $frecuenciapago = $creditos[$j]->getFrecuencia_pago()->getFrecuencia();
            }
            if ($creditos[$j]->getTipo_parcialidad() != null) {
                $tipoparcialidad = $creditos[$j]->getTipo_parcialidad()->getParcialidad();
            }
            if ($creditos[$j]->getTipo_cobro() != null) {
                $tipocobro = $creditos[$j]->getTipo_cobro()->getTipo();
            }
            if ($creditos[$j]->getIdProductosDeCreditos() != null) {
                $productosDeCreditos = $creditos[$j]->getIdProductosDeCreditos()->getProductocredito();

            }
            if
            ($creditos[$j]->getFechaDesembolso() != null
            ) {
                $getFechaDesembolso = $creditos[$j]->getFechaDesembolso()->format('Y-m-d H:i');
            }
            if
            ($creditos[$j]->getFechaVencimiento() != null
            ) {
                $getFechaVencimiento = $creditos[$j]->getFechaVencimiento()->format('Y-m-d H:i');
            }
            if
            ($creditos[$j]->getFechaSolicitud() != null
            ) {
                $getFechaSolicitud = $creditos[$j]->getFechaSolicitud()->format('Y-m-d H:i');
            }
            $destinoFinanciamiento = '';
            if ($creditos[$j]->getDestino_financiamiento() != null) {
                $destinoFinanciamiento = $creditos[$j]->getDestino_financiamiento()->getFinanciamiento();
            }
            $garante = '';
            if ($creditos[$j]->getGarante() != null) {
                $garante = $creditos[$j]->getGarante()->getNombre() . ' ' . $creditos[$j]->getGarante()->getPrimerApellido() . ' ' . $creditos[$j]->getGarante()->getSegundoApellido();
            }
            $phpExcelObject->setActiveSheetIndex(0)
                // ->setCellValue('A'.$i, $factura)
                ->setCellValue('A' . $i, $persona)
                ->setCellValue('B' . $i, $garante)
                ->setCellValue('C' . $i, $frecuenciapago)
                ->setCellValue('D' . $i, $tipoparcialidad)
                ->setCellValue('E' . $i, $getFechaDesembolso)
                ->setCellValue('F' . $i, $getFechaVencimiento)
                ->setCellValue('G' . $i, $getFechaSolicitud)
                ->setCellValue('H' . $i, $creditos[$j]->getNumeroDePagos())
                ->setCellValue('I' . $i, $creditos[$j]->getMontoSolicitado())
                ->setCellValue('J' . $i, $creditos[$j]->getInteresAnual())
                ->setCellValue('K' . $i, $creditos[$j]->getMontoEnLetras())
                ->setCellValue('L' . $i, $destinoFinanciamiento)
                ->setCellValue('M' . $i, "")
                ->setCellValue('N' . $i, $creditos[$j]->getObservaciones())
                ->setCellValue('O' . $i, $tipocobro)
                ->setCellValue('P' . $i, $productosDeCreditos)
                ->setCellValue('Q' . $i, $metodoDeAmortizacion)
                ->setCellValue('R' . $i, $creditos[$j]->getEstadoCredito())
                ->setCellValue('S' . $i, $creditos[$j]->getGastoAdministrativo())
                ->setCellValue('T' . $i, $creditos[$j]->getDescripcionGasto())
                ->setCellValue('U' . $i, $creditos[$j]->getNoIdentidadBeneficiario())
                ->setCellValue('V' . $i, $creditos[$j]->getNombreBeneficiario());
            $i++;
        }
        $estiloTituloReporte = array(
            'font' => array(
                'name' => 'Verdana',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 16,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'argb' => 'FF220835')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_NONE
                )
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            )
        );

        $estiloTituloColumnas = array(
            'font' => array(
                'name' => 'Arial',
                'bold' => true,
                'color' => array(
                    'rgb' => 'blue'
                )
            ),
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startcolor' => array(
                    'rgb' => 'c47cf2'
                ),
                'endcolor' => array(
                    'argb' => 'FF431a5d'
                )
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '143860'
                    )
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '143860'
                    )
                )
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => TRUE
            )
        );

        /* $estiloInformacion = new \PHPExcel_Style();
         $estiloInformacion->applyFromArray( array(
             'font' => array(
                 'name'  => 'Arial',
                 'color' => array(
                     'rgb' => '000000'
                 )
             ),
             'fill' => array(
                 'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
                 'color' => array(
                     'argb' => 'FFd9b7f4')
             ),
             'borders' => array(
                 'left' => array(
                     'style' => \PHPExcel_Style_Border::BORDER_THIN ,
                     'color' => array(
                         'rgb' => '3a2a47'
                     )
                 )
             )
         ));*/
        $phpExcelObject->getActiveSheet()->getStyle('A1:D1')->applyFromArray($estiloTituloReporte);
        $phpExcelObject->getActiveSheet()->getStyle('A3:V3')->applyFromArray($estiloTituloColumnas);
        //$phpExcelObject->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:W".($i-1));
        for ($i = 'A'; $i <= 'Q'; $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension($i)->setAutoSize(TRUE);
        }

        $phpExcelObject->getActiveSheet()->setTitle($tituloReporte);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Creditos.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    public function aprobarAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);
        $estadoAprobado = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->findOneByTipo("APROBADO");
        try {
            $entity->setEstadocreditos($estadoAprobado);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha aprobado el crédito ' . $id//." ".$this->impresionTablaAmortizacionAction($id)
            );
        } catch (\Doctrine\DBAL\DBALException $e) {
            if ($e->getCode() == 0) {
                if ($e->getPrevious()->getCode() == 23000) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        "No se puede aprobar."
                    );

                    return $this->redirect($this->generateUrl('creditos'));
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }
        //$this->impresionTablaAmortizacionAction($id);
        return $this->redirect($this->generateUrl('creditos'));
        //return $this->redirect($this->generateUrl('creditosAux', array('idCredito' => $id)));

    }

    public function rechazarAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);
        $estadoAprobado = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->findOneByTipo("RECHAZADO");
        try {
            $entity->setEstadocreditos($estadoAprobado);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha rechazado el crédito ' . $id
            );
        } catch (\Doctrine\DBAL\DBALException $e) {
            if ($e->getCode() == 0) {
                if ($e->getPrevious()->getCode() == 23000) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        "No se puede rechazar."
                    );

                    return $this->redirect($this->generateUrl('creditos'));
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }
        return $this->redirect($this->generateUrl('creditos'));

    }

    public function enRevisionAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);
        $estadoAprobado = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->findOneByTipo("EN REVISION");
        try {
            $entity->setEstadocreditos($estadoAprobado);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha iniciado la revisión en el crédito: ' . $id
            );
        } catch (\Doctrine\DBAL\DBALException $e) {
            if ($e->getCode() == 0) {
                if ($e->getPrevious()->getCode() == 23000) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        "No se puede cambiar el estado a 'En Revision'."
                    );

                    return $this->redirect($this->generateUrl('asociarDocumentoCredito', array('id' => $id)));
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }
        return $this->redirect($this->generateUrl('asociarDocumentoCredito', array('id' => $id)));

    }

    public function pagadoAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);
        $estadoAprobado = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->findOneByTipo("PAGADO");
        try {
            $entity->setEstadocreditos($estadoAprobado);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Los pagos se han realizado y completado en el crédito: ' . $id
            );
        } catch (\Doctrine\DBAL\DBALException $e) {
            if ($e->getCode() == 0) {
                if ($e->getPrevious()->getCode() == 23000) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        "No se puede cambiar el estado a 'Pagado'."
                    );

                    return $this->redirect($this->generateUrl('creditos_pago', array('id' => $id)));
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }
        return $this->redirect($this->generateUrl('creditos', array('id' => $id)));

    }

    public function asociarDocumentoAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $documento = new CreditosDocumento();
        $documentos = $em->getRepository('ModulosPersonasBundle:CreditosDocumento')->findCreditoDocumentoPorCredito($id);
        $form = $this->createForm(new CreditosDocumentoType(), $documento);
        $form->handleRequest($request);
        $credito = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);
        if ($form->isValid()) {

            $documento->subirDocumento($this->container->getParameter('modulos.directorio.documentos'));
            $documento->setCredito($credito);
            $em->persist($documento);
            $em->flush();
            return $this->redirect($this->generateUrl('creditos'));
        }

        return $this->render('ModulosPersonasBundle:Creditos:AsociarDocumento.html.twig', array(
            'form' => $form->createView(),
            'id' => $id,
            'documentos' => $documentos,
            'credito' => $credito


        ));
    }

    public function editarasociarDocumentoAction($id, $idCredito, Request $request)
    {


        $em = $this->getDoctrine()->getManager();
        $documento = $em->getRepository('ModulosPersonasBundle:CreditosDocumento')->findOneById($id);
        $form = $this->createForm(new CreditosDocumentoType(), $documento);
        $credito = $em->getRepository('ModulosPersonasBundle:Creditos')->findOneById($idCredito);
        $rutaDocumentoOriginal = $form->getData()->getRutaDocumento();
        $form->handleRequest($request);
        if ($form->isValid()) {

            if (null == $documento->getDocumento()) {
                $documento->setRutaDocumento($rutaDocumentoOriginal);
            } else {
                if ($rutaDocumentoOriginal != null) {
                    $documento->subirDocumento($this->container->getParameter('modulos.directorio.documentos'));
                    // Borrar el documento anterior
                    unlink($this->container->getParameter('modulos.directorio.documentos') . $rutaDocumentoOriginal);
                } else {
                    $documento->subirDocumento($this->container->getParameter('modulos.directorio.documentos'));
                }

            }
            $em->persist($documento);
            $em->flush();
            return $this->redirect($this->generateUrl('asociarDocumentoCredito', array('id' => $idCredito)));
        }
        return $this->render('ModulosPersonasBundle:Creditos:AsociarDocumentoEditar.html.twig', array(
            'documento' => $documento,
            'idCredito' => $idCredito,
            'form' => $form->createView(),
            'credito' => $credito
        ));


    }

    public function eliminarasociarDocumentoAction($id, $idCredito)
    {
        $em = $this->getDoctrine()->getManager();
        $documento = $em->getRepository('ModulosPersonasBundle:CreditosDocumento')->findOneById($id);
        if ($documento->getRutaDocumento() != "") {
            unlink($this->container->getParameter('modulos.directorio.documentos') . $documento->getRutaDocumento());
        }

        $em->remove($documento);
        $em->flush();

        return $this->redirect($this->generateUrl('asociarDocumentoCredito', array('id' => $idCredito)));
    }

    public function pagareAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:Creditos')->findBy(array("estadocreditos" => '6'));

        return $this->render(
            'ModulosPersonasBundle:Creditos:pagare.html.twig',
            array(
                'entities' => $entities,
            )
        );
    }


    public
    function pagoDesgravamenAction(Request $request, $persona, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new Libro();

        $ano = date('Y');
        $mes = date('m');
        $dia = date('d');
        $h = date('H') - 5;
        $m = date('i');
        $s = date('s');

        $date = $ano . '-' . $mes . '-' . $dia;
        $date = explode("-", $date);

        $time = $h . ':' . $m . ':' . $s;
        $time = explode(":", $time);

        $fechak = new \DateTime();
        $fechak->setDate($date[0], $date[1], $date[2]);
        $fechak->setTime($time[0], $time[1], $time[2]);

        $creditoD = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);

        //$entity->setFecha($fechak);
        $entity->setFecha($creditoD->getFechaSolicitud());
        $entity->setPersona($creditoD->getPersona());

        $valorDesgravamen = $em->getRepository('ModulosPersonasBundle:TablaAmortizacion')->findTablasAmortizacionPorCreditos($id);
        $entity->setDebe(round($valorDesgravamen[0]->getDesgravamen(), 2));


        $entity->setHaber(0);
        $idpersona = $em->getRepository('ModulosPersonasBundle:Persona')->find($persona);
        //echo $idpersona;
        $entity->setPersona($idpersona);

        $form = $this->createForm(new PagoDesgravamenType(), $entity);
        $form->handleRequest($request);
        $array = array();
        $fecha1 = new \DateTime();
        $fecha1 = $fecha1->format('d-m-Y');
        $fecha2 = new \DateTime();
        $fecha2 = $fecha2->format('d-m-Y');
        $saldo = 0;

        //$estadoslibro = new EstadosLibro();

        $estadoslibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findAll();
        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();
        $tiposProductosContables = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findAll();

        $librosr = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosReciboDESC();

        if (count($librosr) > 0) {
            if ($librosr[0]->getNumeroRecibo() != null) {
                $consec = $librosr[0]->getNumeroRecibo() + 1;
            } else {
                if (count($librosr) > 1) {
                    $consec = $librosr[1]->getNumeroRecibo() + 1;
                } else {
                    $consec = 1;
                }
            }

            //$entity->setFecha($librosr[0]->getFecha());


        } else {
            $consec = 1;
        }

        $form = $this->createForm(new PagoDesgravamenType(), $entity);
        $form->handleRequest($request);

        if (count($libros) > 0) {
            $this->actualizarSaldo($libros[count($libros) - 1]);
            $saldo = $libros[0]->getSaldo();

        } else {
            $saldo = 0;
        }

        $tmp = '';

        if ($form->isValid()) {
            $tipoTransaccion = $entity->getProductoContableId();
            if (count($libros) > 0) {
                $ano = $libros[0]->getFecha()->format('Y');
                $anoParametro = $entity->getFecha()->format('Y');
                $mes = $libros[0]->getFecha()->format('m');
                $mesParametro = $entity->getFecha()->format('m');

                $fechaInicial = $libros[count($libros) - 1]->getFecha();
                $fechaIngreso = $entity->getFecha();


                $librosAnteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaDescId($fechaInicial, $fechaIngreso);

                $saldoAnterior = 0;
                if (count($librosAnteriores) != 1 && count($librosAnteriores) > 0) {
                    $saldoAnterior = $librosAnteriores[0]->getSaldo();
                }

                $fechaUltima = $libros[0]->getFecha();
                $librosPosteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaAscId($fechaIngreso, $fechaUltima);
                $evalua = 0;
                if (count($librosPosteriores) > 0) {
                    $librosPosteriores[count($librosPosteriores) - 1]->getSaldo();
                    for ($i = 1; $i < count($librosPosteriores);) {
                        if ($librosPosteriores[$i]->getSaldo() - $entity->getHaber() < 0) {
                            $evalua = 1;
                            break;
                        } else {
                            $i++;
                        }
                    }
                }

                //if ($entity->getSaldo() < 0) {
                if (($saldoAnterior - $entity->getHaber()) < 0 || $evalua == 1) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        //'No se puede realizar la transacción, la caja no dispone de dinero suficiente.'
                        'No se puede realizar la transacción, la caja no dispone de dinero suficiente en la fecha solicitada.'
                    );

                    //return $this->redirect($this->generateUrl('libro_create'));
                    //return $this->redirect($this->generateUrl('creditos_pagoLibro', array('id' => $entity->getCreditoId()->getId())));
                    return $this->redirect($this->generateUrl('pagoDesgravamen', array('persona' => $persona, 'id' => $id)));
                }

                /*if (($anoParametro == $ano && $mesParametro < $mes) || $anoParametro < $ano) {


                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'No se puede abrir un mes, si existen libros de meses superiores.'
                    );

                    return $this->redirect($this->generateUrl('pagoDesgravamen',array('persona' => $persona)));
                }*/

                foreach ($libros as $libro) {

                    /*if ($libro->getEstadosLibro()->getEstado() == 'ABIERTO' && $libro->getFecha()->format(
                            'm-Y'
                        //) != $entity->getFecha()->format('m-Y')
                        ) != $libro->getFecha()->format('m-Y')
                    ) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'No se puede crear un libro en un mes, si hay un mes anterior abierto'
                        );

                        return $this->redirect($this->generateUrl('pagoDesgravamen',array('persona' => $persona)));
                    }*/

                    if ($libro->getEstadosLibro()->getEstado() == 'CERRADO' && $libro->getFecha()->format('m-Y') == $entity->getFecha()->format('m-Y')
                    ) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'No se puede crear un libro en un mes cerrado.'
                        );

                        return $this->redirect($this->generateUrl('pagoDesgravamen', array('persona' => $persona, 'id' => $id)));
                    }


                }

            }
            $estadoLibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneByEstado("ABIERTO");
            if ($tipoTransaccion->getId() == 3) {//Pago de Desgravamen
                $idCredito = $entity->getInfo();
                $credito = $em->getRepository(
                    'ModulosPersonasBundle:Creditos'
                )->find($idCredito);

                $estadoCredito = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->find(7);

                $credito->setEstadocreditos($estadoCredito);
                $credito->setDesgravamenPagado(true);


                $em = $this->getDoctrine()->getManager();
                $entity->setEstadosLibro($estadoLibro);
                $entity->setNumeroRecibo($consec);

                $this->actualizarSaldo($libros[count($libros) - 1]);

//            creando DTVC y VCHR
                $VCHR = new VCHR();
                $VCHR->setFecha($entity->getFecha());
                $VCHR->setMes($entity->getFecha()->format('m'));
                $VCHR->setLibroId($entity);


                $DTVC = new DTVC();

                $DTVC->setCuentaDeudoraId($tipoTransaccion->getCuentaHaber());
                $DTVC->setCuentaAcreedoraId($tipoTransaccion->getCuentaDebe());
                $DTVC->setValor($entity->getDebe() + $entity->getHaber());
                $DTVC->setIdVchr($VCHR);
                $DTVC->setEsDebe($entity->getDebe() > 0);

                $em->persist($credito);
                $em->persist($entity);
                $em->persist($VCHR);
                $em->persist($DTVC);

                $em->flush();
            } else {

                $idCredito = $entity->getInfo();
                $credito = $em->getRepository(
                    'ModulosPersonasBundle:Creditos'
                )->find($idCredito);

                $estadoCredito = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->find(6);

                $credito->setEstadocreditos($estadoCredito);

                $em = $this->getDoctrine()->getManager();
                $entity->setEstadosLibro($estadoLibro);
                $entity->setNumeroRecibo($consec);

//            creando DTVC y VCHR

                $VCHR = new VCHR();
                $VCHR->setFecha($entity->getFecha());
                $VCHR->setMes($entity->getFecha()->format('m'));
                $VCHR->setLibroId($entity);


                $DTVC = new DTVC();

                $DTVC->setCuentaDeudoraId($tipoTransaccion->getCuentaHaber());
                $DTVC->setCuentaAcreedoraId($tipoTransaccion->getCuentaDebe());
                $DTVC->setValor($entity->getDebe() + $entity->getHaber());
                $DTVC->setIdVchr($VCHR);
                $DTVC->setEsDebe($entity->getDebe() > 0);


                $em->persist($entity);
                $em->persist($VCHR);
                $em->persist($DTVC);

                $em->flush();


            }

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado correctamente.'
            );

            //$this->actualizarSaldo($entity);

            return $this->redirect($this->generateUrl('creditos'));


        }

        return $this->render(
            'ModulosPersonasBundle:Creditos:pagoDesgravamen.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
                'array' => $array,
                'fecha1' => $fecha1,
                'fecha2' => $fecha2,
                'estadoslibro' => $estadoslibro,
                'saldo' => $saldo,
                'consec' => $consec,
                'tiposProductosContables' => $tiposProductosContables,
                'persona' => $persona,
                'id' => $id,
            )
        );
    }

    public
    function otorgarCreditoAction(Request $request, $persona, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new Libro();

        $ano = date('Y');
        $mes = date('m');
        $dia = date('d');
        $h = date('H') - 5;
        $m = date('i');
        $s = date('s');

        $date = $ano . '-' . $mes . '-' . $dia;
        $date = explode("-", $date);

        $time = $h . ':' . $m . ':' . $s;
        $time = explode(":", $time);

        $fechak = new \DateTime();
        $fechak->setDate($date[0], $date[1], $date[2]);
        $fechak->setTime($time[0], $time[1], $time[2]);

        $creditoD = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);

        //$entity->setFecha($fechak);
        $entity->setFecha($creditoD->getFechaSolicitud());
        $entity->setPersona($creditoD->getPersona());

        //$valorPrestamo = $em->getRepository('ModulosPersonasBundle:TablaAmortizacion')->findTablasAmortizacionPorCreditos($id);
        $entity->setHaber($creditoD->getMontoSolicitado());

        $form = $this->createForm(new OtorgarCreditoType(), $entity);
        $form->handleRequest($request);
        $idpersona = $em->getRepository('ModulosPersonasBundle:Persona')->find($persona);
        $entity->setPersona($idpersona);
        $entity->setDebe(0);
        $librosOrdenados = array();
        $array = array();
        $fecha1 = new \DateTime();
        $fecha1 = $fecha1->format('d-m-Y');
        $fecha2 = new \DateTime();
        $fecha2 = $fecha2->format('d-m-Y');
        $saldo = 0;

        //$estadoslibro = new EstadosLibro();

        $estadoslibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findAll();
        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();
        $tiposProductosContables = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findAll();

        $librosr = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosReciboDESC();

        if (count($librosr) > 0) {
            if ($librosr[0]->getNumeroRecibo() != null) {
                $consec = $librosr[0]->getNumeroRecibo() + 1;
            } else {
                if (count($librosr) > 1) {
                    $consec = $librosr[1]->getNumeroRecibo() + 1;
                } else {
                    $consec = 1;
                }
            }

            //$entity->setFecha($librosr[0]->getFecha());
            $form = $this->createForm(new OtorgarCreditoType(), $entity);
            $form->handleRequest($request);
        } else {
            $consec = 1;
        }

        if (count($libros) > 0) {
            $this->actualizarSaldo($libros[count($libros) - 1]);
            $saldo = $libros[0]->getSaldo();

        } else {
            //$this->actualizarSaldo($libros[count($libros)-1]);
            $saldo = 0;
        }

        $tmp = '';

        if ($form->isValid()) {
            $tipoTransaccion = $entity->getProductoContableId();
            if (count($libros) > 0) {
                $ano = $libros[0]->getFecha()->format('Y');
                $anoParametro = $entity->getFecha()->format('Y');
                $mes = $libros[0]->getFecha()->format('m');
                $mesParametro = $entity->getFecha()->format('m');
                $fechaInicial = $libros[count($libros) - 1]->getFecha();
                $fechaIngreso = $entity->getFecha();


                $librosAnteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaDescId($fechaInicial, $fechaIngreso);

                $saldoAnterior = 0;
                if (count($librosAnteriores) > 0) {


                    $saldoAnterior = $librosAnteriores[0]->getSaldo();
                }

                $fechaUltima = $libros[0]->getFecha();
                $librosPosteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaAscId($fechaIngreso, $fechaUltima);
                $evalua = 0;
                if (count($librosPosteriores) > 0) {
                    $librosPosteriores[count($librosPosteriores) - 1]->getSaldo();
                    for ($i = 1; $i < count($librosPosteriores);) {
                        if ($librosPosteriores[$i]->getSaldo() - $entity->getHaber() < 0) {
                            $evalua = 1;
                            break;
                        } else {
                            $i++;
                        }
                    }
                }

                //if ($entity->getSaldo() < 0) {
                if (($saldoAnterior - $entity->getHaber()) < 0 || $evalua == 1) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        //'No se puede realizar la transacción, la caja no dispone de dinero suficiente.'
                        'No se puede realizar la transacción, la caja no dispone de dinero suficiente en la fecha solicitada.'
                    );

                    //return $this->redirect($this->generateUrl('libro_create'));
                    return $this->redirect($this->generateUrl('otorgarCredito', array('persona' => $persona, 'id' => $id)));
                }

                /*if (($anoParametro == $ano && $mesParametro < $mes) || $anoParametro < $ano) {


                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'No se puede abrir un mes, si existen libros de meses superiores.'
                    );

                    return $this->redirect($this->generateUrl('otorgarCredito',array('persona' => $persona)));
                    # code...
                }*/

                foreach ($libros as $libro) {

                    /*if ($libro->getEstadosLibro()->getEstado() == 'ABIERTO' && $libro->getFecha()->format(
                            'm-Y'
                        //) != $entity->getFecha()->format('m-Y')
                        ) != $libro->getFecha()->format('m-Y')
                    ) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'No se puede crear un libro en un mes, si hay un mes anterior abierto'
                        );

                        return $this->redirect($this->generateUrl('libro_create'));
                    }*/

                    if ($libro->getEstadosLibro()->getEstado() == 'CERRADO' && $libro->getFecha()->format(
                            'm-Y'
                        ) == $entity->getFecha()->format('m-Y')
                    ) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'No se puede crear un libro en un mes cerrado.'
                        );

                        return $this->redirect($this->generateUrl('otorgarCredito', array('persona' => $persona, 'id' => $id)));
                    }


                }

            }
            $estadoLibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneByEstado("ABIERTO");

            $idCredito = $entity->getInfo();
            //echo $entity->getInfo();
            $entity->setEstadosLibro($estadoLibro);
            //$entity->setNumeroRecibo($entity->getNumeroRecibo() + 1);

            $credito = $em->getRepository(
                'ModulosPersonasBundle:Creditos'
            )->find($idCredito);

            $estadoCredito = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->find(6);

            $credito->setEstadocreditos($estadoCredito);
            $entity->setNumeroRecibo($consec);


            $this->actualizarSaldo($libros[count($libros) - 1]);
//

//
//            creando DTVC y VCHR


            $em = $this->getDoctrine()->getManager();

            $VCHR = new VCHR();
            $VCHR->setFecha($entity->getFecha());
            $VCHR->setMes($entity->getFecha()->format('m'));
            $VCHR->setLibroId($entity);


            $DTVC = new DTVC();

            $DTVC->setCuentaDeudoraId($tipoTransaccion->getCuentaHaber());
            $DTVC->setCuentaAcreedoraId($tipoTransaccion->getCuentaDebe());
            $DTVC->setValor($entity->getDebe() + $entity->getHaber());
            $DTVC->setIdVchr($VCHR);
            $DTVC->setEsDebe($entity->getDebe() > 0);

            $em->persist($credito);
            $em->persist($entity);
            $em->persist($VCHR);
            $em->persist($DTVC);

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado correctamente.'
            );

            //$this->actualizarSaldo($entity);

            return $this->redirect($this->generateUrl('creditos'));


        }

        return $this->render(
            'ModulosPersonasBundle:Creditos:otorgarCreditos.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
                'listado' => $librosOrdenados,
                'array' => $array,
                'fecha1' => $fecha1,
                'fecha2' => $fecha2,
                'estadoslibro' => $estadoslibro,
                'saldo' => $saldo,
                'consec' => $consec,
                'tiposProductosContables' => $tiposProductosContables,
                'persona' => $persona,
                'id' => $id,
            )
        );
    }

    public
    function otorgarCreditoEmergenteAction(Request $request, $persona, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new Libro();

        $ano = date('Y');
        $mes = date('m');
        $dia = date('d');
        $h = date('H') - 5;
        $m = date('i');
        $s = date('s');

        $date = $ano . '-' . $mes . '-' . $dia;
        $date = explode("-", $date);

        $time = $h . ':' . $m . ':' . $s;
        $time = explode(":", $time);

        $fechak = new \DateTime();
        $fechak->setDate($date[0], $date[1], $date[2]);
        $fechak->setTime($time[0], $time[1], $time[2]);

        $creditoD = $em->getRepository('ModulosPersonasBundle:Creditos')->find($id);

        //$entity->setFecha($fechak);
        $entity->setFecha($creditoD->getFechaSolicitud());
        $entity->setPersona($creditoD->getPersona());

        //$valorPrestamo = $em->getRepository('ModulosPersonasBundle:TablaAmortizacion')->findTablasAmortizacionPorCreditos($id);
        $entity->setHaber($creditoD->getMontoSolicitado());

        $form = $this->createForm(new OtorgarCreditoEmergenteType(), $entity);
        $form->handleRequest($request);
        $idpersona = $em->getRepository('ModulosPersonasBundle:Persona')->find($persona);
        $entity->setPersona($idpersona);
        $entity->setDebe(0);
        $librosOrdenados = array();
        $array = array();
        $fecha1 = new \DateTime();
        $fecha1 = $fecha1->format('d-m-Y');
        $fecha2 = new \DateTime();
        $fecha2 = $fecha2->format('d-m-Y');
        $saldo = 0;

        //$estadoslibro = new EstadosLibro();

        $estadoslibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findAll();
        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();
        $tiposProductosContables = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findAll();

        $librosr = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosReciboDESC();

        if (count($librosr) > 0) {
            if ($librosr[0]->getNumeroRecibo() != null) {
                $consec = $librosr[0]->getNumeroRecibo() + 1;
            } else {
                if (count($librosr) > 1) {
                    $consec = $librosr[1]->getNumeroRecibo() + 1;
                } else {
                    $consec = 1;
                }
            }

            //$entity->setFecha($librosr[0]->getFecha());
            $form = $this->createForm(new OtorgarCreditoEmergenteType(), $entity);
            $form->handleRequest($request);
        } else {
            $consec = 1;
        }

        if (count($libros) > 0) {
            $this->actualizarSaldo($libros[count($libros) - 1]);
            $saldo = $libros[0]->getSaldo();

        } else {
            $saldo = 0;
        }

        $tmp = '';

        if ($form->isValid()) {
            $tipoTransaccion = $entity->getProductoContableId();
            if (count($libros) > 0) {
                $ano = $libros[0]->getFecha()->format('Y');
                $anoParametro = $entity->getFecha()->format('Y');
                $mes = $libros[0]->getFecha()->format('m');
                $mesParametro = $entity->getFecha()->format('m');
                $fechaInicial = $libros[count($libros) - 1]->getFecha();
                $fechaIngreso = $entity->getFecha();


                $librosAnteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaDescId($fechaInicial, $fechaIngreso);


                $saldoAnterior = 0;
                if (count($librosAnteriores) != 1 && count($librosAnteriores) > 0) {
                    $saldoAnterior = $librosAnteriores[0]->getSaldo();
                }

                $fechaUltima = $libros[0]->getFecha();
                $librosPosteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaAscId($fechaIngreso, $fechaUltima);
                $evalua = 0;
                if (count($librosPosteriores) > 0) {
                    $librosPosteriores[count($librosPosteriores) - 1]->getSaldo();
                    for ($i = 1; $i < count($librosPosteriores);) {
                        if ($librosPosteriores[$i]->getSaldo() - $entity->getHaber() < 0) {
                            $evalua = 1;
                            break;
                        } else {
                            $i++;
                        }
                    }
                }

                //if ($entity->getSaldo() < 0) {
                if (($saldoAnterior - $entity->getHaber()) < 0 || $evalua == 1) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        //'No se puede realizar la transacción, la caja no dispone de dinero suficiente.'
                        'No se puede realizar la transacción, la caja no dispone de dinero suficiente en la fecha solicitada.'
                    );

                    //return $this->redirect($this->generateUrl('libro_create'));
                    return $this->redirect($this->generateUrl('otorgarCreditoEmergente', array('persona' => $persona, 'id' => $id)));
                }

                /*if (($anoParametro == $ano && $mesParametro < $mes) || $anoParametro < $ano) {


                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'No se puede abrir un mes, si existen libros de meses superiores.'
                    );

                    return $this->redirect($this->generateUrl('otorgarCreditoEmergente',array('persona' => $persona)));
                    # code...
                }*/

                foreach ($libros as $libro) {

                    /*if ($libro->getEstadosLibro()->getEstado() == 'ABIERTO' && $libro->getFecha()->format(
                            'm-Y'
                       // ) != $entity->getFecha()->format('m-Y')
                        ) != $libro->getFecha()->format('m-Y')
                    ) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'No se puede crear un libro en un mes, si hay un mes anterior abierto'
                        );

                        return $this->redirect($this->generateUrl('libro_create'));
                    }*/

                    if ($libro->getEstadosLibro()->getEstado() == 'CERRADO' && $libro->getFecha()->format(
                            'm-Y'
                        ) == $entity->getFecha()->format('m-Y')
                    ) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'No se puede crear un libro en un mes cerrado.'
                        );

                        return $this->redirect($this->generateUrl('otorgarCreditoEmergente', array('persona' => $persona, 'id' => $id)));
                    }


                }

            }
            $estadoLibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneByEstado("ABIERTO");

            $idCredito = $entity->getInfo();
            //echo $entity->getInfo();
            $entity->setEstadosLibro($estadoLibro);
            //$entity->setNumeroRecibo($entity->getNumeroRecibo() + 1);


            $credito = $em->getRepository(
                'ModulosPersonasBundle:Creditos'
            )->find($idCredito);

            $estadoCredito = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->find(6);

            $credito->setEstadocreditos($estadoCredito);
            $entity->setNumeroRecibo($consec);

            $this->actualizarSaldo($libros[count($libros) - 1]);

            //$entity->setHaber($credito->getMontoSolicitado());
//            creando DTVC y VCHR
            $em = $this->getDoctrine()->getManager();

            $VCHR = new VCHR();
            $VCHR->setFecha($entity->getFecha());
            $VCHR->setMes($entity->getFecha()->format('m'));
            $VCHR->setLibroId($entity);


            $DTVC = new DTVC();

            $DTVC->setCuentaDeudoraId($tipoTransaccion->getCuentaHaber());
            $DTVC->setCuentaAcreedoraId($tipoTransaccion->getCuentaDebe());
            $DTVC->setValor($entity->getDebe() + $entity->getHaber());
            $DTVC->setIdVchr($VCHR);
            $DTVC->setEsDebe($entity->getDebe() > 0);

            $em->persist($credito);
            $em->persist($entity);
            $em->persist($VCHR);
            $em->persist($DTVC);

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado correctamente.'
            );

            //$this->actualizarSaldo($entity);

            return $this->redirect($this->generateUrl('creditos'));


        }

        return $this->render(
            'ModulosPersonasBundle:Creditos:otorgarCreditoEmergente.html.twig',
            array(
                'entity' => $entity,
                'form' => $form->createView(),
                'listado' => $librosOrdenados,
                'array' => $array,
                'fecha1' => $fecha1,
                'fecha2' => $fecha2,
                'estadoslibro' => $estadoslibro,
                'saldo' => $saldo,
                'consec' => $consec,
                'tiposProductosContables' => $tiposProductosContables,
                'persona' => $persona,
                'id' => $id,
            )
        );
    }

    public function cargarPersonasAction($idTransaccion, $persona, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $flag = 1;
        if ($idTransaccion == 4) {//Credito Otorgado q no esté ya como transacción en el libro
            $flag = 2;
            $lista = $em->getRepository('ModulosPersonasBundle:Creditos')->findPersonasCreditosporID($persona, $id);

        } else {
            if ($idTransaccion == 5) {//Credito Emergente q no esté ya como transacción en el libro
                $flag = 2;
                $lista = $em->getRepository('ModulosPersonasBundle:Creditos')->findPersonasCreditosEmergentesporID($persona, $id);

            } else {
                if ($idTransaccion == 3) {//Pago desgravamen
                    $flag = 2;
                    $lista = $em->getRepository('ModulosPersonasBundle:Creditos')->findPersonasCreditosDesgravporID($persona, $id);

                }
            }
        }

        return $this->render(
            'ModulosPersonasBundle:Creditos:cargardes.html.twig',
            array(
                'flag' => $flag,
                'lista' => $lista,
            )
        );
    }

    public function actualizarSaldo($entity)
    {

        $fechaIngreso = $entity->getFecha();
        $debe = $entity->getDebe();
        $haber = $entity->getHaber();

        $em = $this->getDoctrine()->getManager();
        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

        if (count($libros) > 0) {
            $fechaReciente = $libros[0]->getFecha();
            $fechaInicial = $libros[count($libros) - 1]->getFecha();
        } else {
            $saldo = 0;
        }

        $librosAnteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaDescId($fechaInicial, $fechaIngreso);

        if (count($librosAnteriores) > 0) {
            $saldoAnterior = $librosAnteriores[1]->getSaldo();
            $entity->setSaldo($saldoAnterior + $debe - $haber);
        } else {
            if ($debe > 0) {
                $entity->setSaldo($debe);
            }//elseif($haber>0){
            // $entity->setSaldo(0-$haber);
            //}

        }


        $librosPosteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaAscId($fechaIngreso, $fechaReciente);
        if (count($librosPosteriores) > 0) {
            $librosPosteriores[count($librosPosteriores) - 1]->getSaldo();
            for ($i = 0; $i < count($librosPosteriores); $i++) {
                if ($i > 0) {
                    $id = $librosPosteriores[$i]->getId();
                    $libroActualizar = $em->getRepository('ModulosPersonasBundle:Libro')->find($id);
                    $libroActualizar->setSaldo($librosPosteriores[$i - 1]->getSaldo() + $libroActualizar->getDebe() - $libroActualizar->getHaber());
                    $em->persist($libroActualizar);
                }
            }
        }

        $em->persist($entity);

        $em->flush();

        return $this->redirect($this->generateUrl('creditos'));

    }

    public function impresionTablaAmortizacionAction($idCredito)
    {
        $reglasInvalidas = array();
        $em = $this->getDoctrine()->getManager();
        $creditosaaux = $em->getRepository('ModulosPersonasBundle:ProductoDeCreditos')->findAll();
        $entity = $em->getRepository('ModulosPersonasBundle:Creditos')->find($idCredito);

        foreach ($entity as $entity) {
            $entity->setAporteAdicional(round($entity->getAporteAdicional(), 2));
            $entity->setAporteMinimo(round($entity->getAporteMinimo(), 2));
            $estadoCreditos = $entity->getEstadocreditos();
            $entity->setEstadocreditos($estadoCreditos);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Creditos entity.');
        }

        $pagosrealizados = $em->getRepository(
            'ModulosPersonasBundle:PagoCuotaCredito'
        )->findPagosCuotasCreditos($idCredito);

        $amortizaciones = $em->getRepository(
            'ModulosPersonasBundle:TablaAmortizacion'
        )->findTablasAmortizacionPorCreditos($idCredito);

        $documentos = $em->getRepository('ModulosPersonasBundle:CreditosDocumento')->findCreditoDocumentoPorCredito($idCredito);

        $html = $this->renderView(
            'ModulosPersonasBundle:Creditos:formatoAmortizacion.html.twig',
            array(
                'someDataToView' => 'Something',
                'entity' => $entity,
                'amortizaciones' => $amortizaciones,
                'reglasInvalidas' => $reglasInvalidas,
                'tiposCreditos' => $creditosaaux,
                'documentos' => $documentos,
                'pagosrealizados' => $pagosrealizados
            )
        );

        $this->returnPDFResponseFromHTML($html, $idCredito);
    }

    public function returnPDFResponseFromHTML($html, $idCredito)
    {
        //set_time_limit(30); uncomment this line according to your needs
        // If you are not in a controller, retrieve of some way the service container and then retrieve it
        //$pdf = $this->container->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //if you are in a controlller use :
        $pdf = $this->get("white_october.tcpdf")->create();// create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('Conquito');
        $pdf->SetTitle(('Recibo de Crédito'));
        $pdf->SetSubject('Recibo de Crédito');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 10, '', true);
        //$pdf->SetMargins(20,20,40, true);
        $pdf->AddPage();
        //$pdf->AddPage('P', 'Letter');

        $em = $this->getDoctrine()->getManager();
        $credito = $em->getRepository('ModulosPersonasBundle:Creditos')->find($idCredito);

        $persona = $credito->getPersona();

        $filename = 'Credito ('.$idCredito.') ' . $persona;

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

        $pdf->Output($filename . ".pdf", 'D'); // This will output the PDF as a response directly

    }
}
