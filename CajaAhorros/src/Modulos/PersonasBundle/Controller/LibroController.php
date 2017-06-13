<?php

namespace Modulos\PersonasBundle\Controller;

use Modulos\PersonasBundle\Entity\Cartera;
use Modulos\PersonasBundle\Entity\CarteraPersonaMes;
use Modulos\PersonasBundle\Entity\CarteraMes;
use Modulos\PersonasBundle\Entity\DTVC;
use Modulos\PersonasBundle\Entity\PagoCuotaAhorro;
use Modulos\PersonasBundle\Entity\PagoCuotaCredito;
use Modulos\PersonasBundle\Entity\VCHR;
use Modulos\PersonasBundle\Entity\PYG;
use Modulos\PersonasBundle\Entity\BalanceGral;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Form\PagoCreditosType;

use Modulos\PersonasBundle\Entity\Aporte;
use Modulos\PersonasBundle\Entity\AportePersonaMes;
use Modulos\PersonasBundle\Entity\AporteMes;

use Modulos\PersonasBundle\Entity\Libro;
use Modulos\PersonasBundle\Form\LibroType;
use Modulos\PersonasBundle\Form\PagoDesgravamenType;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Modulos\PersonasBundle\Entity\EstadosLibro;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Libro controller.
 *
 */
class LibroController extends Controller
{

    /**
     * Lists all Libro entities.
     *
     */
    public function indexAction()
    {
        $array = array();
        $em = $this->getDoctrine()->getManager();

        /*$personas = $em->getRepository('ModulosPersonasBundle:Creditos')->findCreditosOrdenadosPorFecha();

        for($i=0;$i<count($personas);$i++){
            echo $personas[$i][1]."<br>";
        }*/

        $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

        if (count($librosOrdenados) > 0) {
            $saldo = $librosOrdenados[0]->getSaldo();
            $this->actualizarSaldo($librosOrdenados[count($librosOrdenados) - 1]);

        } else {
            $saldo = 0;
        }
//      $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosASCEND();
        if (count($librosOrdenados) > 0) {
            $aux = $librosOrdenados[0]->getFecha()->format('Y-m');
            $array [] = $aux;
            for ($i = 1; $i < count($librosOrdenados); $i++) {
                if ($librosOrdenados[$i]->getFecha()->format('Y-m') != $aux) {
                    $aux = $librosOrdenados[$i]->getFecha()->format('Y-m');
                    $array [] = $aux;
                }
            }
            $f = $librosOrdenados[0]->getFecha();
            $n = $librosOrdenados[0]->getNumeroRecibo();
        } else {
            $f = "";
            $n = 0;
        }

        return $this->render(
            'ModulosPersonasBundle:Libro:index.html.twig',
            array(
                'array' => $array,
                'listado' => $librosOrdenados,
                'estado' => '',
                'saldo' => $saldo,
                'fechasaldo' => $f,
                'numsaldo' => $n,
            )
        );
    }


    public function cargarPeronasAction($idTransaccion)
    {

        $em = $this->getDoctrine()->getManager();
        $flag = 1;
        if ($idTransaccion == 4) {//Credito Otorgado q no esté ya como transacción en el libro
            $flag = 2;
            $lista = $em->getRepository('ModulosPersonasBundle:Creditos')->findPersonasCreditos();
            /*$listaCreditos = $em->getRepository('ModulosPersonasBundle:Creditos')->findPersonasCreditos();
            $listalibros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosByTipoTransaccion(4);
            $lista = array();
            foreach ($listaCreditos as $credito) {
                $esta=false;
                foreach ($listalibros as $libro) {
                    if ($libro->getInfo() == $credito->getId()) {
                        $esta=true;
                        break;
                    }
                }
                if(!$esta){
                    $lista[]=$credito;
                }
            }*/
        } else {
            if ($idTransaccion == 5) {//Credito Emergente q no esté ya como transacción en el libro
                $flag = 2;
                $lista = $em->getRepository('ModulosPersonasBundle:Creditos')->findPersonasCreditosEmergentes();
                /*$listaCreditos = $em->getRepository('ModulosPersonasBundle:Creditos')->findPersonasCreditosEmergentes();
                $listalibros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosByTipoTransaccion(5);
                $lista = array();
                foreach ($listaCreditos as $creditoEmergente) {
                    $esta=false;
                    foreach ($listalibros as $libro) {
                        if ($libro->getInfo() == $creditoEmergente->getId()) {
                            $esta=true;
                            break;
                        }
                    }
                    if(!$esta){
                        $lista[]=$creditoEmergente;
                    }
                }*/
            } else {
                if ($idTransaccion == 3) {//Pago desgravamen
                    $flag = 2;
                    $lista = $em->getRepository('ModulosPersonasBundle:Creditos')->findPersonasCreditosDesgrav();

                } else {
                    if ($idTransaccion == 9) {//pago credito
                        $flag = 7;
                        $listalibros = $em->getRepository(
                            'ModulosPersonasBundle:Libro'
                        )->findLibrosCreditosOrdenadosSinFecha();

                        //$lista = array();
                        $lista = $em->getRepository('ModulosPersonasBundle:Creditos')->findPersonasCreditosPagar();
                        /*foreach ($listalibros as $libro) {
                            $creditoLibro = $em->getRepository('ModulosPersonasBundle:Creditos')->findOneById($libro->getInfo());
                            //$creditoLibro = $em->getRepository('ModulosPersonasBundle:Creditos')->findPersonasCreditosPagar();
                            if ($creditoLibro != null) {
                                $lista[] = $creditoLibro;
                            }
                        }*/
                    } else {
                        if ($idTransaccion == 12 || $idTransaccion == 13 || $idTransaccion == 14 || $idTransaccion == 15 || $idTransaccion == 16 || $idTransaccion == 17) {//Ahorros depositos
                            switch ($idTransaccion) {
                                case 12: {
                                    $listaAhorros = $em->getRepository(
                                        'ModulosPersonasBundle:Ahorro'
                                    )->findAhorrosByTipoAprobado3(1);
                                }
                                    break;
                                case 13: {
                                    $listaAhorros = $em->getRepository(
                                        'ModulosPersonasBundle:Ahorro'
                                    )->findAhorrosByTipoAprobado(2);
                                }
                                    break;
                                case 14: {
                                    $listaAhorros = $em->getRepository(
                                        'ModulosPersonasBundle:Ahorro'
                                    )->findAhorrosByTipoAprobado3(3);
                                }
                                    break;
                                case 15: {
                                    $listaAhorros = $em->getRepository(
                                        'ModulosPersonasBundle:Ahorro'
                                    )->findAhorrosByTipoAprobado2(1);
                                }
                                    break;
                                case 16: {
                                    $listaAhorros = $em->getRepository(
                                        'ModulosPersonasBundle:Ahorro'
                                    )->findAhorrosByTipoAprobado2(3);
                                }
                                    break;
                                case 17: {
                                    $listaAhorros = $em->getRepository(
                                        'ModulosPersonasBundle:Ahorro'
                                    )->findAhorrosByTipoAprobado4(2);
                                }
                                    break;
                            }
                            $flag = 4;
//                            $listalibros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosByTipoTransaccion($idTransaccion);
                            $lista = $listaAhorros;
//                            foreach ($listaAhorros as $ahorro) {
//                                $esta=false;
//                                foreach ($listalibros as $libro) {
//                                    if ($libro->getInfo() == $ahorro->getId()) {
//                                        $esta=true;
//                                        break;
//                                    }
//                                }
//                                if(!$esta){
//                                    $lista[]=$ahorro;
//                                }
//                            }
                        } else {
                            if ($idTransaccion == 1) {
                                $flag = 5;
                                $lista = $em->getRepository('ModulosPersonasBundle:Libro')->findPersonasAporte();

                            } else {
                                if ($idTransaccion == 18) {
                                    $flag = 6;
                                    //$lista = $em->getRepository('ModulosPersonasBundle:Libro')->findPersonasAporteInicial();
                                    $lista = $em->getRepository('ModulosPersonasBundle:Persona')->findOrdenados();

                                } else {
                                    if ($idTransaccion == 19) {
                                        $flag = 8;
                                        $lista = $em->getRepository('ModulosPersonasBundle:Libro')->findPersonasAporte(
                                        );

                                    } else {
                                        if ($idTransaccion == 21) {
                                            $flag = 8;
                                            $lista = $em->getRepository(
                                                'ModulosPersonasBundle:Libro'
                                            )->findPersonasAporte();

                                        } else {
                                            if ($idTransaccion == 8 || $idTransaccion == 7 || $idTransaccion == 6) {
                                                $flag = 11;
                                                $lista = $em->getRepository(
                                                    'ModulosPersonasBundle:Persona'
                                                )->findOrdenadosEmpleados();

                                            } else {
                                                if ($idTransaccion == 10) {
                                                    $flag = 7;
                                                    $lista = $em->getRepository(
                                                        'ModulosPersonasBundle:Creditos'
                                                    )->findPersonasInteresCreditosPagar();

                                                } else {
                                                    $flag = 3;
                                                    $lista = $em->getRepository(
                                                        'ModulosPersonasBundle:Persona'
                                                    )->findOrdenados();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->render(
            'ModulosPersonasBundle:Libro:cargar.html.twig',
            array(
                'flag' => $flag,
                'lista' => $lista,
            )
        );
    }

    public function cargarTablaAmortAction(
        $idCredito
    ) {
        $em = $this->getDoctrine()->getManager();
        $amortizaciones = $em->getRepository(
            'ModulosPersonasBundle:TablaAmortizacion'
        )->findTablasAmortizacionPorCreditos($idCredito);
        /*for ($i = 0; $i < count($amortizaciones); $i++) {
            $amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
            $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
            $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
            $amortizaciones[$i]->setDesgravamen(round($amortizaciones[$i]->getDesgravamen(), 2));
            $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
        }*/

        $credito = $em->getRepository(
            'ModulosPersonasBundle:Creditos'
        )->find($idCredito);

        $pagosrealizados = $em->getRepository(
            'ModulosPersonasBundle:PagoCuotaCredito'
        )->findPagosCuotasCreditos($idCredito);

        $pagocuota = 0;
//        if (count($amortizaciones) > 1 && (count($amortizaciones) >= count($pagosrealizados))) {
//            $pagocuota=$amortizaciones[count($pagosrealizados) + 1]->getValorcuota();
//        }

        return $this->render(
            'ModulosPersonasBundle:Libro:tablaamortiz.html.twig',
            array(
                'amortizaciones' => $amortizaciones,
                'pagosrealizados' => $pagosrealizados,
                'entity' => $credito,
                'pagocuota' => $pagocuota,
                'pago' => "amort",
            )
        );

    }

    public function cargarTablaLiquidacionAction(
        $idpersona,
        //$fecha){
        $ano,
        $mes
    ) {

        $em = $this->getDoctrine()->getManager();

        $persona = $em->getRepository('ModulosPersonasBundle:Persona')->find($idpersona);

        //          TOTAL APORTES

        $aportes = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersona($idpersona);
        $retiro = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersona($idpersona);

        $personaAportes = 0;
        $personaRetiroAportes = 0;

        for ($j = 0; $j < count($aportes); $j++) {
            $personaAportes += $aportes[$j]->getDebe();
        }

        for ($j = 0; $j < count($retiro); $j++) {
            $personaRetiroAportes += $retiro[$j]->getHaber();
        }

        $saldoAporte = $personaAportes;
        $saldoRetiro = $personaRetiroAportes;
        $saldoFinal = $personaAportes - $personaRetiroAportes;

        //           TOTAL AHORROS DEPOSITADOS

        $ahorrosVista = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosVistaByTipoDepositadoPorPersona(
            $idpersona
        );
        $ahorrosPlazo = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosPlazByTipoDepositadoPorPersona(
            $idpersona
        );
        $ahorrosRes = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosResByTipoDepositadoPorPersona(
            $idpersona
        );


        $valorAhorroVista = 0;
        $valorInteresAhorroVista = 0;
        $valorAhorroPlazoFijo = 0;
        $valorInteresAhorroPlazoFijo = 0;
        $valorAhorroRestringido = 0;
        $valorInteresAhorroRestringido = 0;

        for ($j = 0; $j < count($ahorrosVista); $j++) {
            $valorAhorroVista += $ahorrosVista[$j]->getValorEnCaja();
            $difAno = $ano - $ahorrosVista[$j]->getFechaSolicitud()->format('Y');
            $difMes = (($difAno * 12) + $mes) - $ahorrosVista[$j]->getFechaSolicitud()->format('m');

            if ($ahorrosVista[$j]->getFechaSolicitud()->format('Y-m') >= $ano.'-'.$mes) {
                $interes = 0;
            } else {
                $interes = $ahorrosVista[$j]->getValorEnCaja() * ($ahorrosVista[$j]->getTipoAhorro()->getTasaInteres(
                        ) / 12) * ($difMes);
            }
            $valorInteresAhorroVista += $interes;
        }

        $totalAhorroVista = $valorAhorroVista;
        $totalInteresAhorroVista = $valorInteresAhorroVista;

        for ($j = 0; $j < count($ahorrosPlazo); $j++) {

            $valorAhorroPlazoFijo += $ahorrosPlazo[$j]->getValorEnCaja();

            if ($ahorrosPlazo[$j]->getFechaRenovacion()) {
                $difAno = $ano - $ahorrosPlazo[$j]->getFechaRenovacion()->format('Y');
                $difMes = (($difAno * 12) + $mes) - $ahorrosPlazo[$j]->getFechaRenovacion()->format('m');

                if ($ahorrosPlazo[$j]->getFechaRenovacion()->format('Y-m') >= $ano.'-'.$mes) {
                    $interes = 0;
                } else {
                    $interes = $ahorrosPlazo[$j]->getValorEnCaja() * ($ahorrosPlazo[$j]->getTipoAhorro(
                            )->getTasaInteres() / 12) * ($difMes);
                }
            } else {
                $difAno = $ano - $ahorrosPlazo[$j]->getFechaSolicitud()->format('Y');
                $difMes = (($difAno * 12) + $mes) - $ahorrosPlazo[$j]->getFechaSolicitud()->format('m');

                if ($ahorrosPlazo[$j]->getFechaSolicitud()->format('Y-m') >= $ano.'-'.$mes) {
                    $interes = 0;
                } else {
                    $interes = $ahorrosPlazo[$j]->getValorEnCaja() * ($ahorrosPlazo[$j]->getTipoAhorro(
                            )->getTasaInteres() / 12) * ($difMes);
                }
            }

            $valorInteresAhorroPlazoFijo += $interes;
        }

        $totalAhorroPlazoFijo = $valorAhorroPlazoFijo;
        $totalInteresAhorroPlazoFijo = $valorInteresAhorroPlazoFijo;

        for ($j = 0; $j < count($ahorrosRes); $j++) {
            $valorAhorroRestringido += $ahorrosRes[$j]->getValorEnCaja();
            $difAno = $ano - $ahorrosRes[$j]->getFechaSolicitud()->format('Y');
            $difMes = (($difAno * 12) + $mes) - $ahorrosRes[$j]->getFechaSolicitud()->format('m');

            if ($ahorrosRes[$j]->getFechaSolicitud()->format('Y-m') >= $ano.'-'.$mes) {
                $interes = 0;
            } else {
                $interes = $ahorrosRes[$j]->getValorEnCaja() * ($ahorrosRes[$j]->getTipoAhorro()->getTasaInteres(
                        ) / 12) * ($difMes);
            }
            $valorInteresAhorroRestringido += $interes;
        }

        $totalAhorroRestringido = $valorAhorroRestringido;
        $totalInteresAhorroRestringido = $valorInteresAhorroRestringido;

        //           TOTAL CREDITOS PENDIENTES DE PAGAR

        $creditos = $em->getRepository('ModulosPersonasBundle:Creditos')->findCreditosOtrogadosByPersonaOrdDescPorFecha(
            $idpersona
        );
        $creditosCF = $em->getRepository(
            'ModulosPersonasBundle:Creditos'
        )->findCreditosOtrogadosByPersonaAndIDOrdDescPorFecha($idpersona, 1);
        $creditosCCC = $em->getRepository(
            'ModulosPersonasBundle:Creditos'
        )->findCreditosOtrogadosByPersonaAndIDOrdDescPorFecha($idpersona, 2);
        $creditosCCCF = $em->getRepository(
            'ModulosPersonasBundle:Creditos'
        )->findCreditosOtrogadosByPersonaAndIDOrdDescPorFecha($idpersona, 3);

        $cuotaFija = 0;
        $interesCuotaFija = 0;
        $cuotaCapitalCte = 0;
        $interesCuotaCapitalCte = 0;
        $desgraCuotaCapitalCte = 0;
        $cuotaCapitalCteFijo = 0;
        $interesCuotaCapitalCteFijo = 0;
        $desgraCuotaCapitalCteFijo = 0;

        for ($j = 0; $j < count($creditosCF); $j++) {

            $pagosrealizadosCF = $em->getRepository(
                'ModulosPersonasBundle:PagoCuotaCredito'
            )->findPagosCuotasCreditos($creditosCF[$j]->getId());

            $amortizacionesCF = $em->getRepository(
                'ModulosPersonasBundle:TablaAmortizacion'
            )->findTablasAmortizacionPorCreditos($creditosCF[$j]->getId());

            $valorCapitalAmortCF = 0;
            $valorInteresAmortCF = 0;

            for ($i = (count($pagosrealizadosCF) + 1); $i < count($amortizacionesCF); $i++) {
                $valorCapitalAmortCF += $amortizacionesCF[$i]->getCapital();
                //$valorInteresAmortCF += $amortizacionesCF[$i]->getInteres();

                //if($amortizacionesCF[$i]->getFechaDePago()->format('d-m-Y') >= $amortizacionesCF[$i]->getFechaDePago()->format('d').'-'.$mes.'-'.$ano){
                if ($ano.'-'.$mes < $amortizacionesCF[$i]->getFechaDePago()->format('Y-m')) {
                    $interes = 0;
                } else {
                    $interes = $amortizacionesCF[$i]->getInteres();
                }

                $valorInteresAmortCF += $interes;
            }

            $cuotaFija += $valorCapitalAmortCF;
            $interesCuotaFija += $valorInteresAmortCF;
        }
        $totalCuotaFija = $cuotaFija;
        $totalInteresCoutaFija = $interesCuotaFija;

        for ($j = 0; $j < count($creditosCCC); $j++) {

            $pagosrealizadosCCC = $em->getRepository(
                'ModulosPersonasBundle:PagoCuotaCredito'
            )->findPagosCuotasCreditos($creditosCCC[$j]->getId());

            $amortizacionesCCC = $em->getRepository(
                'ModulosPersonasBundle:TablaAmortizacion'
            )->findTablasAmortizacionPorCreditos($creditosCCC[$j]->getId());

            $valorCapitalAmortCCC = 0;
            $valorInteresAmortCCC = 0;
            $valorDesgraAmortCCC = 0;

            for ($i = (count($pagosrealizadosCCC) + 1); $i < count($amortizacionesCCC); $i++) {
                $valorCapitalAmortCCC += $amortizacionesCCC[$i]->getCapital();
                //$valorInteresAmortCCC += $amortizacionesCCC[$i]->getInteres();
                $valorDesgraAmortCCC += $amortizacionesCCC[$i]->getDesgravamen();

                //if($amortizacionesCCC[$i]->getFechaDePago()->format('d-m-Y') >= $amortizacionesCCC[$i]->getFechaDePago()->format('d').'-'.$mes.'-'.$ano){
                if ($ano.'-'.$mes < $amortizacionesCCC[$i]->getFechaDePago()->format('Y-m')) {
                    $interes = 0;
                } else {
                    $interes = $amortizacionesCCC[$i]->getInteres();
                }

                $valorInteresAmortCCC += $interes;
            }

            $cuotaCapitalCte += $valorCapitalAmortCCC;
            $interesCuotaCapitalCte += $valorInteresAmortCCC;
            $desgraCuotaCapitalCte += $valorDesgraAmortCCC;
        }
        $totalCuotaCapitalCte = $cuotaCapitalCte;
        $totalInteresCuotaCapitalCte = $interesCuotaCapitalCte;
        $totalDesgraCuotaCapitalCte = $desgraCuotaCapitalCte;

        for ($j = 0; $j < count($creditosCCCF); $j++) {

            $pagosrealizadosCCCF = $em->getRepository(
                'ModulosPersonasBundle:PagoCuotaCredito'
            )->findPagosCuotasCreditos($creditosCCCF[$j]->getId());

            $amortizacionesCCCF = $em->getRepository(
                'ModulosPersonasBundle:TablaAmortizacion'
            )->findTablasAmortizacionPorCreditos($creditosCCCF[$j]->getId());

            $valorCapitalAmortCCCF = 0;
            $valorInteresAmortCCCF = 0;
            $valorDesgraAmortCCCF = 0;

            for ($i = (count($pagosrealizadosCCCF) + 1); $i < count($amortizacionesCCCF); $i++) {
                $valorCapitalAmortCCCF += $amortizacionesCCCF[$i]->getCapital();
                //$valorInteresAmortCCCF += $amortizacionesCCCF[$i]->getInteres();

                //if($amortizacionesCCCF[$i]->getFechaDePago()->format('d-m-Y') >= $amortizacionesCCCF[$i]->getFechaDePago()->format('d').'-'.$mes.'-'.$ano){
                if ($ano.'-'.$mes < $amortizacionesCCCF[$i]->getFechaDePago()->format('Y-m')) {
                    $interes = 0;
                } else {
                    $interes = $amortizacionesCCCF[$i]->getInteres();
                }

                $valorInteresAmortCCCF += $interes;
            }
            $cuotaCapitalCteFijo += $valorCapitalAmortCCCF;
            $interesCuotaCapitalCteFijo += $valorInteresAmortCCCF;
            $desgraCuotaCapitalCteFijo += $valorDesgraAmortCCCF;
        }
        $totalCuotaCapitalCteFijo = $cuotaCapitalCteFijo;
        $totalInteresCuotaCapitalCteFijo = $interesCuotaCapitalCteFijo;
        $totalDesgraCuotaCapitalCteFijo = $desgraCuotaCapitalCteFijo;

        $totalLiquidacion = $saldoFinal +
            $totalInteresAhorroRestringido + $totalInteresAhorroPlazoFijo +
            $totalInteresAhorroVista + $totalAhorroRestringido +
            $totalAhorroVista + $totalAhorroPlazoFijo -
            $cuotaFija -
            $interesCuotaFija -
            $cuotaCapitalCte -
            $interesCuotaCapitalCte -
            $desgraCuotaCapitalCte -
            $cuotaCapitalCteFijo -
            $interesCuotaCapitalCteFijo -
            $desgraCuotaCapitalCteFijo;

        //                  MORA


        //                  MULTAS


        return $this->render(
            'ModulosPersonasBundle:Libro:tablaliqui.html.twig',
            array(
                'persona' => $persona,
                'aportes' => $saldoAporte,
                'retiro' => $saldoRetiro,
                'saldoAporte' => $saldoFinal,
                'saldoAhorroVista' => $totalAhorroVista,
                'interesVista' => $totalInteresAhorroVista,
                'saldoAhorroPlazoFijo' => $totalAhorroPlazoFijo,
                'interesPlazo' => $totalInteresAhorroPlazoFijo,
                'saldoAhorroRestringido' => $totalAhorroRestringido,
                'interesRes' => $totalInteresAhorroRestringido,
                'creditoCuotaFija' => $totalCuotaFija,
                'interescreditoCuotaFija' => $totalInteresCoutaFija,
                'creditoCuotaCapitalCte' => $totalCuotaCapitalCte,
                'interescreditoCuotaCapitalCte' => $totalInteresCuotaCapitalCte,
                'desgracreditoCuotaCapitalCte' => $totalDesgraCuotaCapitalCte,
                'creditoCuotaCapitalCteFijo' => $totalCuotaCapitalCteFijo,
                'interescreditoCuotaCapitalCteFijo' => $totalInteresCuotaCapitalCteFijo,
                'desgracreditoCuotaCapitalCteFijo' => $totalDesgraCuotaCapitalCteFijo,
                'liquidacion' => $totalLiquidacion,
                'ano' => $ano,
                'mes' => $mes,
            )
        );

    }

    public function exportarTablaLiquidacionAction($idpersona, $ano, $mes)
    {

        $em = $this->getDoctrine()->getManager();

        $cajahorro = $em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja = $cajahorro->getRazonSocial();

        $persona = $em->getRepository('ModulosPersonasBundle:Persona')->find($idpersona);

        //          TOTAL APORTES

        $aportes = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersona($idpersona);
        $retiro = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersona($idpersona);

        $personaAportes = 0;
        $personaRetiroAportes = 0;

        for ($j = 0; $j < count($aportes); $j++) {
            $personaAportes += $aportes[$j]->getDebe();
        }

        for ($j = 0; $j < count($retiro); $j++) {
            $personaRetiroAportes += $retiro[$j]->getHaber();
        }

        $saldoAporte = $personaAportes;
        $saldoRetiro = $personaRetiroAportes;
        $saldoFinal = $personaAportes - $personaRetiroAportes;

        //           TOTAL AHORROS DEPOSITADOS

        $ahorrosVista = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosVistaByTipoDepositadoPorPersona(
            $idpersona
        );
        $ahorrosPlazo = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosPlazByTipoDepositadoPorPersona(
            $idpersona
        );
        $ahorrosRes = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosResByTipoDepositadoPorPersona(
            $idpersona
        );


        $valorAhorroVista = 0;
        $valorInteresAhorroVista = 0;
        $valorAhorroPlazoFijo = 0;
        $valorInteresAhorroPlazoFijo = 0;
        $valorAhorroRestringido = 0;
        $valorInteresAhorroRestringido = 0;

        for ($j = 0; $j < count($ahorrosVista); $j++) {
            $valorAhorroVista += $ahorrosVista[$j]->getValorEnCaja();
            $difAno = $ano - $ahorrosVista[$j]->getFechaSolicitud()->format('Y');
            $difMes = (($difAno * 12) + $mes) - $ahorrosVista[$j]->getFechaSolicitud()->format('m');

            if ($ahorrosVista[$j]->getFechaSolicitud()->format('Y-m') >= $ano.'-'.$mes) {
                $interes = 0;
            } else {
                $interes = $ahorrosVista[$j]->getValorEnCaja() * ($ahorrosVista[$j]->getTipoAhorro()->getTasaInteres(
                        ) / 12) * ($difMes);
            }
            $valorInteresAhorroVista += $interes;
        }

        $totalAhorroVista = $valorAhorroVista;
        $totalInteresAhorroVista = $valorInteresAhorroVista;

        for ($j = 0; $j < count($ahorrosPlazo); $j++) {

            $valorAhorroPlazoFijo += $ahorrosPlazo[$j]->getValorEnCaja();

            if ($ahorrosPlazo[$j]->getFechaRenovacion()) {
                $difAno = $ano - $ahorrosPlazo[$j]->getFechaRenovacion()->format('Y');
                $difMes = (($difAno * 12) + $mes) - $ahorrosPlazo[$j]->getFechaRenovacion()->format('m');

                if ($ahorrosPlazo[$j]->getFechaRenovacion()->format('Y-m') >= $ano.'-'.$mes) {
                    $interes = 0;
                } else {
                    $interes = $ahorrosPlazo[$j]->getValorEnCaja() * ($ahorrosPlazo[$j]->getTipoAhorro(
                            )->getTasaInteres() / 12) * ($difMes);
                }
            } else {
                $difAno = $ano - $ahorrosPlazo[$j]->getFechaSolicitud()->format('Y');
                $difMes = (($difAno * 12) + $mes) - $ahorrosPlazo[$j]->getFechaSolicitud()->format('m');

                if ($ahorrosPlazo[$j]->getFechaSolicitud()->format('Y-m') >= $ano.'-'.$mes) {
                    $interes = 0;
                } else {
                    $interes = $ahorrosPlazo[$j]->getValorEnCaja() * ($ahorrosPlazo[$j]->getTipoAhorro(
                            )->getTasaInteres() / 12) * ($difMes);
                }
            }

            $valorInteresAhorroPlazoFijo += $interes;
        }

        $totalAhorroPlazoFijo = $valorAhorroPlazoFijo;
        $totalInteresAhorroPlazoFijo = $valorInteresAhorroPlazoFijo;

        for ($j = 0; $j < count($ahorrosRes); $j++) {
            $valorAhorroRestringido += $ahorrosRes[$j]->getValorEnCaja();
            $difAno = $ano - $ahorrosRes[$j]->getFechaSolicitud()->format('Y');
            $difMes = (($difAno * 12) + $mes) - $ahorrosRes[$j]->getFechaSolicitud()->format('m');

            if ($ahorrosRes[$j]->getFechaSolicitud()->format('Y-m') >= $ano.'-'.$mes) {
                $interes = 0;
            } else {
                $interes = $ahorrosRes[$j]->getValorEnCaja() * ($ahorrosRes[$j]->getTipoAhorro()->getTasaInteres(
                        ) / 12) * ($difMes);
            }
            $valorInteresAhorroRestringido += $interes;
        }

        $totalAhorroRestringido = $valorAhorroRestringido;
        $totalInteresAhorroRestringido = $valorInteresAhorroRestringido;

        //           TOTAL CREDITOS PENDIENTES DE PAGAR

        $creditos = $em->getRepository('ModulosPersonasBundle:Creditos')->findCreditosOtrogadosByPersonaOrdDescPorFecha(
            $idpersona
        );
        $creditosCF = $em->getRepository(
            'ModulosPersonasBundle:Creditos'
        )->findCreditosOtrogadosByPersonaAndIDOrdDescPorFecha($idpersona, 1);
        $creditosCCC = $em->getRepository(
            'ModulosPersonasBundle:Creditos'
        )->findCreditosOtrogadosByPersonaAndIDOrdDescPorFecha($idpersona, 2);
        $creditosCCCF = $em->getRepository(
            'ModulosPersonasBundle:Creditos'
        )->findCreditosOtrogadosByPersonaAndIDOrdDescPorFecha($idpersona, 3);

        $cuotaFija = 0;
        $interesCuotaFija = 0;
        $cuotaCapitalCte = 0;
        $interesCuotaCapitalCte = 0;
        $desgraCuotaCapitalCte = 0;
        $cuotaCapitalCteFijo = 0;
        $interesCuotaCapitalCteFijo = 0;
        $desgraCuotaCapitalCteFijo = 0;

        for ($j = 0; $j < count($creditosCF); $j++) {

            $pagosrealizadosCF = $em->getRepository(
                'ModulosPersonasBundle:PagoCuotaCredito'
            )->findPagosCuotasCreditos($creditosCF[$j]->getId());

            $amortizacionesCF = $em->getRepository(
                'ModulosPersonasBundle:TablaAmortizacion'
            )->findTablasAmortizacionPorCreditos($creditosCF[$j]->getId());

            $valorCapitalAmortCF = 0;
            $valorInteresAmortCF = 0;

            for ($i = (count($pagosrealizadosCF) + 1); $i < count($amortizacionesCF); $i++) {
                $valorCapitalAmortCF += $amortizacionesCF[$i]->getCapital();
                //$valorInteresAmortCF += $amortizacionesCF[$i]->getInteres();

                //if($amortizacionesCF[$i]->getFechaDePago()->format('d-m-Y') >= $amortizacionesCF[$i]->getFechaDePago()->format('d').'-'.$mes.'-'.$ano){
                if ($ano.'-'.$mes < $amortizacionesCF[$i]->getFechaDePago()->format('Y-m')) {
                    $interes = 0;
                } else {
                    $interes = $amortizacionesCF[$i]->getInteres();
                }

                $valorInteresAmortCF += $interes;
            }

            $cuotaFija += $valorCapitalAmortCF;
            $interesCuotaFija += $valorInteresAmortCF;
        }
        $totalCuotaFija = $cuotaFija;
        $totalInteresCoutaFija = $interesCuotaFija;

        for ($j = 0; $j < count($creditosCCC); $j++) {

            $pagosrealizadosCCC = $em->getRepository(
                'ModulosPersonasBundle:PagoCuotaCredito'
            )->findPagosCuotasCreditos($creditosCCC[$j]->getId());

            $amortizacionesCCC = $em->getRepository(
                'ModulosPersonasBundle:TablaAmortizacion'
            )->findTablasAmortizacionPorCreditos($creditosCCC[$j]->getId());

            $valorCapitalAmortCCC = 0;
            $valorInteresAmortCCC = 0;
            $valorDesgraAmortCCC = 0;

            for ($i = (count($pagosrealizadosCCC) + 1); $i < count($amortizacionesCCC); $i++) {
                $valorCapitalAmortCCC += $amortizacionesCCC[$i]->getCapital();
                //$valorInteresAmortCCC += $amortizacionesCCC[$i]->getInteres();
                $valorDesgraAmortCCC += $amortizacionesCCC[$i]->getDesgravamen();

                //if($amortizacionesCCC[$i]->getFechaDePago()->format('d-m-Y') >= $amortizacionesCCC[$i]->getFechaDePago()->format('d').'-'.$mes.'-'.$ano){
                if ($ano.'-'.$mes < $amortizacionesCCC[$i]->getFechaDePago()->format('Y-m')) {
                    $interes = 0;
                } else {
                    $interes = $amortizacionesCCC[$i]->getInteres();
                }

                $valorInteresAmortCCC += $interes;
            }

            $cuotaCapitalCte += $valorCapitalAmortCCC;
            $interesCuotaCapitalCte += $valorInteresAmortCCC;
            $desgraCuotaCapitalCte += $valorDesgraAmortCCC;
        }
        $totalCuotaCapitalCte = $cuotaCapitalCte;
        $totalInteresCuotaCapitalCte = $interesCuotaCapitalCte;
        $totalDesgraCuotaCapitalCte = $desgraCuotaCapitalCte;

        for ($j = 0; $j < count($creditosCCCF); $j++) {

            $pagosrealizadosCCCF = $em->getRepository(
                'ModulosPersonasBundle:PagoCuotaCredito'
            )->findPagosCuotasCreditos($creditosCCCF[$j]->getId());

            $amortizacionesCCCF = $em->getRepository(
                'ModulosPersonasBundle:TablaAmortizacion'
            )->findTablasAmortizacionPorCreditos($creditosCCCF[$j]->getId());

            $valorCapitalAmortCCCF = 0;
            $valorInteresAmortCCCF = 0;
            $valorDesgraAmortCCCF = 0;

            for ($i = (count($pagosrealizadosCCCF) + 1); $i < count($amortizacionesCCCF); $i++) {
                $valorCapitalAmortCCCF += $amortizacionesCCCF[$i]->getCapital();
                //$valorInteresAmortCCCF += $amortizacionesCCCF[$i]->getInteres();

                //if($amortizacionesCCCF[$i]->getFechaDePago()->format('d-m-Y') >= $amortizacionesCCCF[$i]->getFechaDePago()->format('d').'-'.$mes.'-'.$ano){
                if ($ano.'-'.$mes < $amortizacionesCCCF[$i]->getFechaDePago()->format('Y-m')) {
                    $interes = 0;
                } else {
                    $interes = $amortizacionesCCCF[$i]->getInteres();
                }

                $valorInteresAmortCCCF += $interes;
            }
            $cuotaCapitalCteFijo += $valorCapitalAmortCCCF;
            $interesCuotaCapitalCteFijo += $valorInteresAmortCCCF;
            $desgraCuotaCapitalCteFijo += $valorDesgraAmortCCCF;
        }
        $totalCuotaCapitalCteFijo = $cuotaCapitalCteFijo;
        $totalInteresCuotaCapitalCteFijo = $interesCuotaCapitalCteFijo;
        $totalDesgraCuotaCapitalCteFijo = $desgraCuotaCapitalCteFijo;

        $totalLiquidacion = $saldoFinal +
            $totalInteresAhorroRestringido + $totalInteresAhorroPlazoFijo +
            $totalInteresAhorroVista + $totalAhorroRestringido +
            $totalAhorroVista + $totalAhorroPlazoFijo -
            $cuotaFija -
            $interesCuotaFija -
            $cuotaCapitalCte -
            $interesCuotaCapitalCte -
            $desgraCuotaCapitalCte -
            $cuotaCapitalCteFijo -
            $interesCuotaCapitalCteFijo -
            $desgraCuotaCapitalCteFijo;

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Conquito")
            ->setLastModifiedBy("Conquito")
            ->setTitle("Recibo Liquidacion")
            ->setSubject("Recibo Liquidacion")
            ->setDescription("Recibo Liquidacion")
            ->setKeywords("Recibo Liquidacion")
            ->setCategory("Reporte excel");

        //$tituloReporte1 = "Listado de libros de cajas por meses de:".$fecha1->format('d-m-Y').' a '.$fecha2->format('d-m-Y');
        $tituloReporte = "RECIBO LIQUIDACIÓN";
        $NOMBREPERSONA = $persona->getPrimerApellido()." ".$persona->getNombre();
        $APELLIDOPERSONA = $persona->getPrimerApellido();
        $tituloHoja = "Recibo Liquidación";

        $estiloTituloCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 14,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );

        $estiloCeldaDerecha = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloCeldaIzq = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );

        $estiloSubCabCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 12,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );
        $estiloTituloColumnasCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'size' => 9,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloCeldaCartera = array(
            'font' => array(
                'name' => 'Arial',
                'bold' => false,
                'size' => 10,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloDivisor = array(

            'borders' => array(
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
                    'color' => array(
                        'rgb' => 'FFFFFF',
                    ),
                ),

            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A2', $nombrecaja);
        $phpExcelObject->getActiveSheet()->getStyle('A2:E2')->applyFromArray($estiloSubCabCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A2:E2');

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A3', $tituloReporte);
        $phpExcelObject->getActiveSheet()->getStyle('A3:E3')->applyFromArray($estiloSubCabCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A3:E3');

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B5', 'NOMBRE: ');
        $phpExcelObject->getActiveSheet()->getStyle('B5')->applyFromArray($estiloSubCabCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C5', $NOMBREPERSONA);
        $phpExcelObject->getActiveSheet()->getStyle('C5')->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C5')->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D5', 'FECHA: ');
        $phpExcelObject->getActiveSheet()->getStyle('D5')->applyFromArray($estiloSubCabCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('E5', $ano.'-'.$mes);
        $phpExcelObject->getActiveSheet()->getStyle('E5')->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E5')->applyFromArray($estiloCeldaIzq);

        $saldoAFavor = $saldoFinal + $totalAhorroPlazoFijo + $totalInteresAhorroPlazoFijo + $totalAhorroRestringido + $totalInteresAhorroRestringido + $totalAhorroVista + $totalInteresAhorroVista;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B7', 'Saldo a Favor: ');
        $phpExcelObject->getActiveSheet()->getStyle('B7')->applyFromArray($estiloSubCabCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C7', '$ '.number_format($saldoAFavor, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C7')->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C7')->applyFromArray($estiloCeldaIzq);

        $saldoEnContra = $totalCuotaFija + $totalCuotaCapitalCte + $totalCuotaCapitalCteFijo;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D7', 'Saldo en Contra: ');
        $phpExcelObject->getActiveSheet()->getStyle('D7')->applyFromArray($estiloSubCabCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('E7', '$ '.number_format($saldoEnContra, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('E7')->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E7')->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B8', 'Total: ');
        $phpExcelObject->getActiveSheet()->getStyle('B8')->applyFromArray($estiloSubCabCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C8', '$ '.number_format($totalLiquidacion, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C8')->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C8')->applyFromArray($estiloCeldaIzq);

        if ($totalLiquidacion >= 0) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D8', 'Neto a Recibir: ');
            $phpExcelObject->getActiveSheet()->getStyle('D8')->applyFromArray($estiloSubCabCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E8', '$ '.number_format($totalLiquidacion, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('E8')->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('E8')->applyFromArray($estiloCeldaIzq);
        } else {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D8', 'Neto a Recibir: ');
            $phpExcelObject->getActiveSheet()->getStyle('D8')->applyFromArray($estiloSubCabCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E8', '$ 0.00');
            $phpExcelObject->getActiveSheet()->getStyle('E8')->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('E8')->applyFromArray($estiloCeldaIzq);
        }

        $titulosColumnas = array(
            'No ',
            'Operación',
            'Saldo a Favor',
            'Saldo en Contra',
            'Valor Acumulado',
        );
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A1:E1');


        $i = 9;
        $mesIndex = 0;


        // Se agregan los titulos del reporte

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 'Tabla de Movimientos Realizados');
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($estiloTituloCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A'.$i.':E'.$i);
        $i++;


        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $titulosColumnas[0]);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A'.$i.':A'.($i + 1));
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':A'.($i + 1))->applyFromArray(
            $estiloTituloColumnasCartera
        );

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, $titulosColumnas[1]);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('B'.$i.':B'.($i + 1));
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':B'.($i + 1))->applyFromArray(
            $estiloTituloColumnasCartera
        );


        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, $titulosColumnas[2]);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('C'.$i.':C'.($i + 1));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i.':C'.($i + 1))->applyFromArray(
            $estiloTituloColumnasCartera
        );

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, $titulosColumnas[3]);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('D'.$i.':D'.($i + 1));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i.':D'.($i + 1))->applyFromArray(
            $estiloTituloColumnasCartera
        );

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('E'.$i, $titulosColumnas[4]);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('E'.$i.':E'.($i + 1));
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i.':E'.($i + 1))->applyFromArray(
            $estiloTituloColumnasCartera
        );

        $i++;
        $i++;
        $iIndice = $i;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 1);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Aportes');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format($saldoAporte, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('E'.$i, number_format($saldoAporte, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 2);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Retiro Aportes');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format($saldoRetiro, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('E'.$i, number_format($saldoFinal, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 3);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Ahorros a Plazo Fijo');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format($totalAhorroPlazoFijo, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('E'.$i, number_format(($saldoFinal + $totalAhorroPlazoFijo), 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 4);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Interés Ahorros a Plazo Fijo');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format($totalInteresAhorroPlazoFijo, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo), 2, '.', '')
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 5);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Ahorros Vista');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format($totalAhorroVista, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalAhorroVista),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 6);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Interés Ahorros Vista');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format($totalInteresAhorroVista, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalInteresAhorroVista + $totalAhorroVista),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 7);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Ahorros Restringidos');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format($totalAhorroRestringido, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalInteresAhorroVista + $totalAhorroVista + $totalAhorroRestringido),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 8);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Interés Ahorros Restringidos');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format($totalInteresAhorroRestringido, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalInteresAhorroVista + $totalAhorroVista + $totalAhorroRestringido + $totalInteresAhorroRestringido),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 9);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Créditos Cuota Fija');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format($totalCuotaFija, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalInteresAhorroVista + $totalAhorroVista + $totalAhorroRestringido + $totalInteresAhorroRestringido - $totalCuotaFija),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 10);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Interés Créditos Cuota Fija');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format($totalInteresCoutaFija, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalInteresAhorroVista + $totalAhorroVista + $totalAhorroRestringido + $totalInteresAhorroRestringido - $totalCuotaFija - $totalInteresCoutaFija),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 11);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Créditos Cuota Capital Constante');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format($totalCuotaCapitalCte, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalInteresAhorroVista + $totalAhorroVista + $totalAhorroRestringido + $totalInteresAhorroRestringido - $totalCuotaFija - $totalInteresCoutaFija - $totalCuotaCapitalCte),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 12);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Interés Créditos Cuota Capital Constante');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format($totalInteresCuotaCapitalCte, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalInteresAhorroVista + $totalAhorroVista + $totalAhorroRestringido + $totalInteresAhorroRestringido - $totalCuotaFija - $totalInteresCoutaFija - $totalCuotaCapitalCte - $totalInteresCuotaCapitalCte),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 13);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Desgravamen Créditos Cuota Capital Constante');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format($totalDesgraCuotaCapitalCte, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalInteresAhorroVista + $totalAhorroVista + $totalAhorroRestringido + $totalInteresAhorroRestringido - $totalCuotaFija - $totalInteresCoutaFija - $totalCuotaCapitalCte - $totalInteresCuotaCapitalCte - $totalDesgraCuotaCapitalCte),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 14);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Créditos Cuota Capital Constante Fijo');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format($totalCuotaCapitalCteFijo, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalInteresAhorroVista + $totalAhorroVista + $totalAhorroRestringido + $totalInteresAhorroRestringido - $totalCuotaFija - $totalInteresCoutaFija - $totalCuotaCapitalCte - $totalInteresCuotaCapitalCte - $totalDesgraCuotaCapitalCte - $totalCuotaCapitalCteFijo),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 15);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Interés Créditos Cuota Capital Constante Fijo');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format($totalInteresCuotaCapitalCteFijo, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalInteresAhorroVista + $totalAhorroVista + $totalAhorroRestringido + $totalInteresAhorroRestringido - $totalCuotaFija - $totalInteresCoutaFija - $totalCuotaCapitalCte - $totalInteresCuotaCapitalCte - $totalDesgraCuotaCapitalCte - $totalCuotaCapitalCteFijo - $totalInteresCuotaCapitalCteFijo),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, 16);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, 'Total Desgravamen Créditos Cuota Capital Constante Fijo');
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, number_format(0, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, number_format($totalDesgraCuotaCapitalCteFijo, 2, '.', ''));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalInteresAhorroVista + $totalAhorroVista + $totalAhorroRestringido + $totalInteresAhorroRestringido - $totalCuotaFija - $totalInteresCoutaFija - $totalCuotaCapitalCte - $totalInteresCuotaCapitalCte - $totalDesgraCuotaCapitalCte - $totalCuotaCapitalCteFijo - $totalInteresCuotaCapitalCteFijo - $totalDesgraCuotaCapitalCteFijo),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;


        //totales
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, " ");
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, "TOTAL");
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloTituloColumnasCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, '=SUM(C13:C28)');
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, '=SUM(D13:D28)');
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue(
                'E'.$i,
                number_format(
                    ($saldoFinal + $totalInteresAhorroPlazoFijo + $totalAhorroPlazoFijo + $totalInteresAhorroVista + $totalAhorroVista + $totalAhorroRestringido + $totalInteresAhorroRestringido - $totalCuotaFija - $totalInteresCoutaFija - $totalCuotaCapitalCte - $totalInteresCuotaCapitalCte - $totalDesgraCuotaCapitalCte - $totalCuotaCapitalCteFijo - $totalInteresCuotaCapitalCteFijo - $totalDesgraCuotaCapitalCteFijo),
                    2,
                    '.',
                    ''
                )
            );
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

        $i++;


        $phpExcelObject->getActiveSheet()->getStyle('A'.$i++.':E'.$i)->applyFromArray($estiloDivisor);
        $i++;

        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('A')
            ->setWidth(5);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(30);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(25);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(25);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(25);

        $phpExcelObject->getActiveSheet()->setTitle($tituloHoja);
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Liquidacion '.$APELLIDOPERSONA.'.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;

    }

    public function cargarTablaRetiroAportesAction($idpersona)
    {

        $em = $this->getDoctrine()->getManager();

        /*          TOTAL APORTES      */

        $aportes = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersona($idpersona);
        $retiro = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersona($idpersona);

        $personaAportes = 0;
        $personaRetiroAportes = 0;

        for ($j = 0; $j < count($aportes); $j++) {
            $personaAportes += $aportes[$j]->getDebe();
        }

        for ($j = 0; $j < count($retiro); $j++) {
            $personaRetiroAportes += $retiro[$j]->getHaber();
        }

        $saldoFinal = $personaAportes - $personaRetiroAportes;


        return $this->render(
            'ModulosPersonasBundle:Libro:tablaretiroaporte.html.twig',
            array(
                'saldoAporte' => $saldoFinal,
            )
        );

    }

    public function cargarTablaAhorroAction(
        $idAhorro
        //, $fecha
    )
    {
        $em = $this->getDoctrine()->getManager();

        $ahorro = $em->getRepository(
            'ModulosPersonasBundle:Ahorro'
        )->find($idAhorro);

        $pagosrealizados = $em->getRepository(
            'ModulosPersonasBundle:PagoCuotaAhorro'
        )->findPagosCuotasAhorros($idAhorro);

        $posibleExtraccion = 0;
        foreach ($pagosrealizados as $pago) {
            $posibleExtraccion += ($pago->getCuota() * $pago->getTipo());
            if ( //$pago->getFechaDeEntrada()->format("d-m")
                //!= $fecha
                //!= $ahorro[count($ahorro)-1]->getFechaSolicitud()
                //&&
                $pago->getTipo() == 1
            ) {
                $posibleExtraccion += $pago->getInteres();
            }
        }

        $pagocuota = 0;
        // valor disponible para ahorro es buscar todos los pagoahorro y comparar con la fecha
        // del mes en curso para el caso de q sea de este mes no sumarle el interes
        $dateActual = new \DateTime();
//        if (count($amortizaciones) > 1 && (count($amortizaciones) >= count($pagosrealizados))) {
//            $pagocuota=$amortizaciones[count($pagosrealizados) + 1]->getValorcuota();
//        }

        return $this->render(
            'ModulosPersonasBundle:Libro:tablaahorro.html.twig',
            array(
                'pagosrealizados' => $pagosrealizados,
                'entity' => $ahorro,
                'pagocuota' => $pagocuota,
                'posibleExtraccion' => $posibleExtraccion,
                'pago' => "amort",
            )
        );

    }

    public
    function cargarTablaAmortPagosAction(
        $idCredito
    ) {
        $estadopago = "pago";
        $em = $this->getDoctrine()->getManager();

        $amortizaciones = $em->getRepository(
            'ModulosPersonasBundle:TablaAmortizacion'
        )->findTablasAmortizacionPorCreditos($idCredito);

        /*for ($i = 0; $i < count($amortizaciones); $i++) {
                $amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
                $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                $amortizaciones[$i]->setDesgravamen(round($amortizaciones[$i]->getDesgravamen(), 2));
                $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
            }*/

        $credito = $em->getRepository(
            'ModulosPersonasBundle:Creditos'
        )->find($idCredito);

        $pagosrealizados = $em->getRepository(
            'ModulosPersonasBundle:PagoCuotaCredito'
        )->findPagosCuotasCreditos($idCredito);

        $pagocuota = 0;
        $pagointeres = 0;
        if (count($amortizaciones) > 1 && (count($amortizaciones) - 1 > count($pagosrealizados))) {
            $pagocuota = $amortizaciones[count($pagosrealizados) + 1]->getValorcuota();

        } else {
            $estadopago = "completado";
        }

        for ($i = 0; $i < count($pagosrealizados); $i++) {
            if ($pagosrealizados[$i]->getSininteres() == 1) {
                $pagointeres += $amortizaciones[$i + 1]->getInteres();
            } else {
                $pagointeres += 0;
            }
        }

        $interesPendiente = $pagointeres;

        return $this->render(
            'ModulosPersonasBundle:Libro:tablaamortiz.html.twig',
            array(
                'amortizaciones' => $amortizaciones,
                'pagosrealizados' => $pagosrealizados,
                'entity' => $credito,
                'pagocuota' => $pagocuota,
                'pagointeres' => $interesPendiente,
                'pago' => $estadopago,

            )
        );

    }


    public
    function createAction(
        Request $request
    ) {
        $entity = new Libro();

        $ano = date('Y');
        $mes = date('m');
        $dia = date('d');
        $h = date('H') - 5;
        $m = date('i');
        $s = date('s');

        $date = $ano.'-'.$mes.'-'.$dia;
        $date = explode("-", $date);

        $time = $h.':'.$m.':'.$s;
        $time = explode(":", $time);

        $fechak = new \DateTime();
        $fechak->setDate($date[0], $date[1], $date[2]);
        $fechak->setTime($time[0], $time[1], $time[2]);

        $entity->setFecha($fechak);


        $form = $this->createForm(new LibroType(), $entity);
        $form->handleRequest($request);
        $librosOrdenados = array();
        //$personaMes=0;
        $array = array();
        $fecha1 = new \DateTime();
        $fecha1 = $fecha1->format('d-m-Y');
        $fecha2 = new \DateTime();
        $fecha2 = $fecha2->format('d-m-Y');
        $saldo = 0;

        //$estadoslibro = new EstadosLibro();
        $em = $this->getDoctrine()->getManager();
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
            $form = $this->createForm(new LibroType(), $entity);
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


                $librosAnteriores = $em->getRepository(
                    'ModulosPersonasBundle:Libro'
                )->findLibrosOrdenadosEntreFechaDescId($fechaInicial, $fechaIngreso);

                $saldoAnterior = 0;

                if (count($librosAnteriores) > 0) {
                    $saldoAnterior = $librosAnteriores[0]->getSaldo();
                }

                $fechaUltima = $libros[0]->getFecha();
                $librosPosteriores = $em->getRepository(
                    'ModulosPersonasBundle:Libro'
                )->findLibrosOrdenadosEntreFechaAscId($fechaIngreso, $fechaUltima);
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
                    //if (($saldo - $entity->getHaber()) < 0) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        //'No se puede realizar la transacción, la caja no dispone de dinero suficiente.'
                        'No se puede realizar la transacción, la caja no dispone de dinero suficiente en la fecha solicitada.'
                    );

                    return $this->redirect($this->generateUrl('libro_create'));
                }

                /*if (($anoParametro == $ano && $mesParametro < $mes) || $anoParametro < $ano) {


                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'No se puede abrir un mes, si existen libros de meses superiores.'
                    );

                    return $this->redirect($this->generateUrl('libro_create'));
                    # code...
                }*/

                foreach ($libros as $libro) {

                    /*if ($libro->getEstadosLibro()->getEstado() == 'ABIERTO' && $libro->getFecha()->format(
                            'm-Y'
                        ) != $entity->getFecha()->format('m-Y')
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

                        return $this->redirect($this->generateUrl('libro_create'));
                    }


                }

            }
            $estadoLibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneByEstado("ABIERTO");
            if ($tipoTransaccion->getId() == 9) {
                $idCredito = $entity->getInfo();
                //si es pago credito tengo q crear un pagocredito de esa tabla, ademas tengo q dividir en dos la transaccion para entrar el pago de
                // la cuota de manera q se entren dos transacciones una de pago y otra de interés
                $amortizaciones = $em->getRepository(
                    'ModulosPersonasBundle:TablaAmortizacion'
                )->findTablasAmortizacionPorCreditos($idCredito);
                /*for ($i = 0; $i < count($amortizaciones); $i++) {
                    $amortizaciones[$i]->setValorCuota(
                        round($amortizaciones[$i]->getValorcuota(), 2)
                    );
                    $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                    $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                    $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
                }*/

                $credito = $em->getRepository(
                    'ModulosPersonasBundle:Creditos'
                )->find($idCredito);

                $pagosrealizados = $em->getRepository(
                    'ModulosPersonasBundle:PagoCuotaCredito'
                )->findPagosCuotasCreditos($idCredito);

                if (count($pagosrealizados) < (count($amortizaciones) - 1)) {
                    $amortizacion = $amortizaciones[count($pagosrealizados) + 1];
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        'El crédito '.$idCredito.' tiene todos sus pagos realizados'
                    );

                    return $this->redirect($this->generateUrl('libro_create'));
                }
                $transaccionPagoCredito = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findOneById(
                    9
                );

                //if ($credito->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo() != "CUOTA FIJA") {
                if ($credito->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo(
                    ) == "CUOTA CAPITAL CONSTANTE"
                ) {
                    $transaccionPagoDesgravamen = $em->getRepository(
                        'ModulosPersonasBundle:TipoProductoContable'
                    )->findOneById(3);

                    $libroPagoDesgravamen = new Libro();
                    $libroPagoDesgravamen->setFecha($entity->getFecha());
                    $libroPagoDesgravamen->setPersona($credito->getPersona());
                    $libroPagoDesgravamen->setDebe($amortizacion->getDesgravamen());
                    $libroPagoDesgravamen->setHaber(0);
                    $libroPagoDesgravamen->setProductoContableId($transaccionPagoDesgravamen);
                    $libroPagoDesgravamen->setCuentaid($transaccionPagoDesgravamen->getCuentaHaber());
                    $libroPagoDesgravamen->setInfo($idCredito);
                    //$libroPagoDesgravamen->setSaldo(
                    // $entity->getSaldo() - $entity->getDebe() + $libroPagoDesgravamen->getDebe()
                    //);
                    $libroPagoDesgravamen->setNumeroRecibo($entity->getNumeroRecibo());
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
                $libroPagoDeuda->setFecha($entity->getFecha());
                $libroPagoDeuda->setPersona($credito->getPersona());
                $libroPagoDeuda->setDebe($amortizacion->getCapital());
                $libroPagoDeuda->setHaber(0);
                $libroPagoDeuda->setProductoContableId($transaccionPagoCredito);
                $libroPagoDeuda->setCuentaid($transaccionPagoCredito->getCuentaHaber());
                $libroPagoDeuda->setInfo($idCredito);
                //$libroPagoDeuda->setSaldo($saldo);
                ///   $saldo + $libroPagoDeuda->getDebe()+$amortizacion->getDesgravamen());

                if ($credito->getIdProductosDeCreditos()->getMetodoAmortizacion()->getMetodo(
                    ) == "CUOTA CAPITAL CONSTANTE"
                ) {
                    $libroPagoDeuda->setNumeroRecibo($entity->getNumeroRecibo() + 1);
                } else {
                    $libroPagoDeuda->setNumeroRecibo($entity->getNumeroRecibo());

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


                $transaccionPagoInteres = $em->getRepository(
                    'ModulosPersonasBundle:TipoProductoContable'
                )->findOneByTipo("Pago Interés");


                $libroPagoInteres = new Libro();
                $libroPagoInteres->setFecha($entity->getFecha());
                $libroPagoInteres->setPersona($credito->getPersona());
                $libroPagoInteres->setDebe($amortizacion->getInteres());
                $libroPagoInteres->setHaber(0);
                $libroPagoInteres->setProductoContableId($transaccionPagoInteres);
                $libroPagoInteres->setCuentaid($transaccionPagoInteres->getCuentaHaber());
                $libroPagoInteres->setInfo($idCredito);
                //$libroPagoInteres->setSaldo($libroPagoDeuda->getSaldo() + $libroPagoInteres->getDebe());
                $libroPagoInteres->setNumeroRecibo($libroPagoDeuda->getNumeroRecibo() + 1);
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


                //pregunto si el tipo de credito lleva pago de desgravamen mensual y si el es del q lleva un solo pago pagar al inicio


                $pagoCredito = new PagoCuotaCredito();
                $pagoCredito->setValorIngresado($entity->getDebe());
                $pagoCredito->setFechaDePago($entity->getFecha());
                $pagoCredito->setCreditoId($credito);
                $em->persist($pagoCredito);
                $em->flush();


                //$this->actualizarSaldo($libroPagoDeuda);
                //$this->actualizarSaldo($libroPagoInteres);


                if ((count($pagosrealizados) + 1) == (count($amortizaciones) - 1)) {
                    $estadoCerrado = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->findOneByTipo(
                        "PAGADO"
                    );
                    $credito->setEstadocreditos($estadoCerrado);
                    $em->persist($credito);
                    $em->flush();
                }


            } elseif ($tipoTransaccion->getId() == 3) {//Pago de Desgravamen
                $idCredito = $entity->getInfo();
                $credito = $em->getRepository(
                    'ModulosPersonasBundle:Creditos'
                )->find($idCredito);

                $estadoCredito = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->find(7);

                $credito->setEstadocreditos($estadoCredito);
                $credito->setDesgravamenPagado(true);


                $em = $this->getDoctrine()->getManager();
                $entity->setEstadosLibro($estadoLibro);
                $entity->setNumeroRecibo($entity->getNumeroRecibo());

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
                if ($tipoTransaccion->getId() == 12 || $tipoTransaccion->getId() == 13 || $tipoTransaccion->getId(
                    ) == 14
                ) {//Ahorros Depositos


                    $idAhorro = $entity->getInfo();
                    $ahorro = $em->getRepository(
                        'ModulosPersonasBundle:Ahorro'
                    )->find($idAhorro);

                    $estadoAhorro = $em->getRepository('ModulosPersonasBundle:EstadoAhorro')->find(6);

                    $ahorro->setEstadoAhorro($estadoAhorro);

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findPagosCuotasAhorros($idAhorro);
                    $depositosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findDepositosAhorros(
                        $idAhorro
                    );
                    $saldoAnterior = 0;
                    $fechaAnterior = new \DateTime();
                    if (count($cuotaAhorroList) > 0) {
                        $saldoAnterior = $cuotaAhorroList[count($cuotaAhorroList) - 1]->getCuotaAcumulada();
                    }
                    if (count($depositosList) > 0) {
                        $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                    }
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                    $cuotaAhorro = new PagoCuotaAhorro();
                    $cuotaAhorro->setCuota($entity->getDebe());
                    $cuotaAhorro->setFechaDeEntrada($entity->getFecha());
                    $cuotaAhorro->setTipo(1);
                    $cuotaAhorro->setIdAhorro($ahorro);
                    //si son del mismo año
//                $difAno=$datetime2->format('Y') - $datetime1->format('Y');
//                $difAno=(($difAno*12)+$datetime2->format('m')) - $datetime1->format('m');
                    /*$difAno=$cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                $difMes=(($difAno*12)+$cuotaAhorro->getFechaDeEntrada()->format('m')) - $fechaAnterior->format('m');
                if($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')){
                    $interes=$entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres()/12);
                }else{
                    $interes=($saldoAnterior + $entity->getDebe())*($ahorro->getTipoAhorro()->getTasaInteres()/12)*($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                }*/
                    $interes = 0;
                    $cuotaAhorro->setInteres($interes);
                    $cuotaAhorro->setCuotaAcumulada($interes + $saldoAnterior + $entity->getDebe());

                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() + $entity->getDebe());

                    switch ($tipoTransaccion->getId()) {
                        case 12: {

                        }
                            break;
                        case 13: {

                        }
                            break;
                        case 14: {

                        }
                            break;
                    }


                    $em = $this->getDoctrine()->getManager();
                    $entity->setEstadosLibro($estadoLibro);
                    $entity->setNumeroRecibo($entity->getNumeroRecibo());

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


                    $em->persist($ahorro);
                    $em->persist($cuotaAhorro);
                    $em->flush();

                    $entity->setInfo($cuotaAhorro->getId());
                    $em->persist($entity);
                    $em->persist($VCHR);
                    $em->persist($DTVC);

                    $em->flush();
                } else {
                    if ($tipoTransaccion->getId() == 15 || $tipoTransaccion->getId() == 16 || $tipoTransaccion->getId(
                        ) == 17
                    ) {//Ahorros Retiros
                        $idAhorro = $entity->getInfo();
                        $ahorro = $em->getRepository(
                            'ModulosPersonasBundle:Ahorro'
                        )->find($idAhorro);

                        if ($tipoTransaccion->getId() == 17) {
                            if ($entity->getHaber() < $ahorro->getValorEnCaja()) {
                                $estadoAhorro = $em->getRepository('ModulosPersonasBundle:EstadoAhorro')->find(7);
                            }
                        } else {
                            if ($entity->getHaber() < $ahorro->getValorEnCaja()) {
                                $estadoAhorro = $em->getRepository('ModulosPersonasBundle:EstadoAhorro')->find(6);
                            } else {
                                if ($entity->getHaber() == $ahorro->getValorEnCaja()) {
                                    $estadoAhorro = $em->getRepository('ModulosPersonasBundle:EstadoAhorro')->find(5);
                                }
                            }
                        }


                        $ahorro->setEstadoAhorro($estadoAhorro);

                        $depositosList = $em->getRepository(
                            'ModulosPersonasBundle:PagoCuotaAhorro'
                        )->findDepositosAhorros($idAhorro);

                        $cuotaAhorroList = $em->getRepository(
                            'ModulosPersonasBundle:PagoCuotaAhorro'
                        )->findPagosCuotasAhorros($idAhorro);
                        $saldoAnterior = 0;
                        if (count($cuotaAhorroList) > 0) {
                            $saldoAnterior = $cuotaAhorroList[count($cuotaAhorroList) - 1]->getCuotaAcumulada();
                            $interesAPagar = $cuotaAhorroList[count($cuotaAhorroList) - 1]->getInteres();
                            $fechaAnt = $cuotaAhorroList[count($cuotaAhorroList) - 1]->getFechaDeEntrada();
                        }

                        $fechaAnterior = new \DateTime();
                        if (count($cuotaAhorroList) > 0) {
                            $saldoAnterior = $cuotaAhorroList[count($cuotaAhorroList) - 1]->getCuotaAcumulada();
                        }

                        if (count($depositosList) > 0) {
                            $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                        }

                        if ($tipoTransaccion->getId() == 17) {

                            if ($entity->getFecha()->format('Y-m-d') < $ahorro->getFechafinal()->format('Y-m-d')) {
                                $this->get('session')->getFlashBag()->add(
                                    'danger',
                                    'El Ahorro a Plazo Fijo de '.$ahorro->getPersona(
                                    ).' con ID '.$idAhorro.' no puede ser retirado antes de la fecha final: '.$ahorro->getFechafinal(
                                    )->format('d-m-Y').''
                                );

                                return $this->redirect($this->generateUrl('libro_create'));
                            }
                        }

                        $cuotaAhorro = new PagoCuotaAhorro();
                        $cuotaAhorro->setCuota($entity->getHaber());
                        $cuotaAhorro->setFechaDeEntrada($entity->getFecha());


                        //($ahorro->setFechaaux($ahorro->getFechafinal());
                        if ($tipoTransaccion->getId() == 17) {
                            if ($ahorro->getFechaRenovacion()) {
                                $difAno = $ahorro->getFechafinal()->format('Y') - $ahorro->getFechaRenovacion()->format(
                                        'Y'
                                    );
                                $difMes = (($difAno * 12) + $ahorro->getFechafinal()->format(
                                            'm'
                                        )) - $ahorro->getFechaRenovacion()->format('m');
                            } else {
                                $difAno = $ahorro->getFechafinal()->format('Y') - $ahorro->getFechaSolicitud()->format(
                                        'Y'
                                    );
                                $difMes = (($difAno * 12) + $ahorro->getFechafinal()->format(
                                            'm'
                                        )) - $ahorro->getFechaSolicitud()->format('m');
                            }
                        } else {
                            $difAno = $entity->getFecha()->format('Y') - $fechaAnterior->format('Y');
                            $difMes = (($difAno * 12) + $entity->getFecha()->format('m')) - $fechaAnterior->format('m');
                        }


                        //echo $difAno;

                        if ($fechaAnterior->format('m-Y') == $entity->getFecha()->format('m-Y')) {
                            //$interes=$entity->getHaber() * ($ahorro->getTipoAhorro()->getTasaInteres()/12);
                            $interes = 0;
                        } else {
                            //$interes=($saldoAnterior + $entity->getHaber())*($ahorro->getTipoAhorro()->getTasaInteres()/12)*($difMes);
                            if ($tipoTransaccion->getId() == 17) {
                                //$interes=$ahorro->getValorAhorrar()*($ahorro->getTipoAhorro()->getTasaInteres()/12)*($difMes);
                                $interes = $ahorro->getValorEnCaja() * ($ahorro->getTipoAhorro()->getTasaInteres(
                                        ) / 12) * ($difMes);
                            } else {
                                $interes = $entity->getHaber() * ($ahorro->getTipoAhorro()->getTasaInteres(
                                        ) / 12) * ($difMes);
                            }
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }

                        $cuotaAhorro->setInteres($interes);
                        //$cuotaAhorro->setFechaDeEntrada($entity->getFecha());
                        $cuotaAhorro->setTipo(-1);
                        $cuotaAhorro->setIdAhorro($ahorro);
                        $cuotaAhorro->setCuotaAcumulada($saldoAnterior - $entity->getHaber());
                        $ahorro->setValorEnCaja($ahorro->getValorEnCaja() - $entity->getHaber());


                        switch ($tipoTransaccion->getId()) {
                            case 15: {

                            }
                                break;
                            case 16: {

                            }
                                break;
                            case 17: {

                            }
                                break;
                        }
                        $em = $this->getDoctrine()->getManager();
                        $entity->setEstadosLibro($estadoLibro);

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


                        $em->persist($ahorro);
                        $em->persist($cuotaAhorro);
                        $em->flush();
                        $entity->setInfo($cuotaAhorro->getId());

                        //$entity->setSaldo($saldo);
                        //$entity->setHaber($cuotaAhorro->getCuota()+$interesAPagar);
                        $em->persist($entity);
                        $em->persist($VCHR);
                        $em->persist($DTVC);

                        $em->flush();

                        //$this->actualizarSaldo($entity);

                        //Interes a pagar

                        $transaccionPagoInteres = $em->getRepository(
                            'ModulosPersonasBundle:TipoProductoContable'
                        )->findOneByTipo("Pago Intereses Ahorros");

                        $libroPagoInteres = new Libro();
                        $libroPagoInteres->setFecha($entity->getFecha());
                        $libroPagoInteres->setPersona($entity->getPersona());
                        $libroPagoInteres->setDebe(0);
                        $libroPagoInteres->setHaber($interes);
                        $libroPagoInteres->setProductoContableId($transaccionPagoInteres);
                        $libroPagoInteres->setCuentaid($transaccionPagoInteres->getCuentaDebe());
                        $libroPagoInteres->setInfo($cuotaAhorro->getId());
                        //$libroPagoInteres->setSaldo($libroPagoDeuda->getSaldo() + $libroPagoInteres->getDebe());

                        $libroPagoInteres->setNumeroRecibo(
                            $entity->getNumeroRecibo() + 1
                        );


                        $libroPagoInteres->setEstadosLibro($estadoLibro);


                        $VCHRPagoInteres = new VCHR();
                        $VCHRPagoInteres->setFecha($libroPagoInteres->getFecha());
                        $VCHRPagoInteres->setMes($libroPagoInteres->getFecha()->format('m'));
                        $VCHRPagoInteres->setLibroId($libroPagoInteres);


                        $DTVCPagoInteres = new DTVC();

                        $DTVCPagoInteres->setCuentaDeudoraId(
                            $libroPagoInteres->getProductoContableId()->getCuentaHaber()
                        );
                        $DTVCPagoInteres->setCuentaAcreedoraId(
                            $libroPagoInteres->getProductoContableId()->getCuentaDebe()
                        );
                        $DTVCPagoInteres->setValor($libroPagoInteres->getDebe() + $libroPagoInteres->getHaber());
                        $DTVCPagoInteres->setIdVchr($VCHRPagoInteres);
                        $DTVCPagoInteres->setEsDebe($libroPagoInteres->getDebe() > 0);

                        $em->persist($libroPagoInteres);
                        $em->persist($VCHRPagoInteres);
                        $em->persist($DTVCPagoInteres);


                        $em->flush();

                        //
                        //$this->actualizarSaldo($libroPagoInteres);

                        if ($tipoTransaccion->getId() == 17) {

                            if ($entity->getFecha()->format('Y-m-d') >= $ahorro->getFechafinal()->format('Y-m-d')) {

                                if ($ahorro->getFechaRenovacion()) {
                                    $incanio = $ahorro->getFechafinal()->format('Y') - $ahorro->getFechaRenovacion(
                                        )->format('Y');
                                    $incmes = (($incanio * 12) + $ahorro->getFechafinal()->format(
                                                'm'
                                            )) - $ahorro->getFechaRenovacion()->format('m');
                                } else {
                                    $incanio = $ahorro->getFechafinal()->format('Y') - $ahorro->getFechaSolicitud(
                                        )->format('Y');
                                    $incmes = (($incanio * 12) + $ahorro->getFechafinal()->format(
                                                'm'
                                            )) - $ahorro->getFechaSolicitud()->format('m');
                                }

                                $ahorro->setFechaRenovacion($entity->getFecha());
                                $em->persist($ahorro);
                                $em->flush();

                                $fechanueva = $entity->getFecha();


                                $incrementoFecha = ($incmes).'M';
                                $intervalo = new \DateInterval('P'.$incrementoFecha);

                                $fechaincrementado = $fechanueva->add($intervalo);
                                $ahorro->setFechaaux($fechaincrementado);
                                $ahorro->setFechafinal($fechaincrementado);
                                $em->persist($ahorro);
                                $em->flush();

                            }
                        }


                    } else {
                        if ($tipoTransaccion->getId() == 5 || $tipoTransaccion->getId() == 4) {

                            $em = $this->getDoctrine()->getManager();

                            $idCredito = $entity->getInfo();
                            //echo $entity->getInfo();
                            $entity->setEstadosLibro($estadoLibro);
                            $entity->setNumeroRecibo($entity->getNumeroRecibo());

                            $credito = $em->getRepository(
                                'ModulosPersonasBundle:Creditos'
                            )->find($idCredito);

                            $estadoCredito = $em->getRepository('ModulosPersonasBundle:EstadoCreditos')->find(6);

                            $credito->setEstadocreditos($estadoCredito);

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

                            //$this->actualizarSaldo($entity);


                        } else {
                            if ($tipoTransaccion->getId() == 10) {//PAGO INTERES

                                $idCredito = $entity->getInfo();

                                $amortizaciones = $em->getRepository(
                                    'ModulosPersonasBundle:TablaAmortizacion'
                                )->findTablasAmortizacionPorCreditos($idCredito);
                                /*for ($i = 0; $i < count($amortizaciones); $i++) {
                    $amortizaciones[$i]->setValorCuota(
                        round($amortizaciones[$i]->getValorcuota(), 2)
                    );
                    $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                    $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                    $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
                }*/

                                $credito = $em->getRepository('ModulosPersonasBundle:Creditos')->find($idCredito);

                                $pagosrealizados = $em->getRepository(
                                    'ModulosPersonasBundle:PagoCuotaCredito'
                                )->findPagosCuotasCreditos($idCredito);

                                $entity->setEstadosLibro($estadoLibro);
                                $entity->setNumeroRecibo($entity->getNumeroRecibo());
                                $entity->setPersona($credito->getPersona());

                                $VCHRPagoInteres = new VCHR();
                                $VCHRPagoInteres->setFecha($entity->getFecha());
                                $VCHRPagoInteres->setMes($entity->getFecha()->format('m'));
                                $VCHRPagoInteres->setLibroId($entity);

                                $DTVCPagoInteres = new DTVC();
                                $DTVCPagoInteres->setCuentaDeudoraId(
                                    $entity->getProductoContableId()->getCuentaHaber()
                                );
                                $DTVCPagoInteres->setCuentaAcreedoraId(
                                    $entity->getProductoContableId()->getCuentaDebe()
                                );
                                $DTVCPagoInteres->setValor($entity->getDebe() + $entity->getHaber());
                                $DTVCPagoInteres->setIdVchr($VCHRPagoInteres);
                                $DTVCPagoInteres->setEsDebe($entity->getDebe() > 0);

                                $credito->setSininteres(0);

                                $em->persist($credito);
                                $em->persist($entity);
                                $em->persist($VCHRPagoInteres);
                                $em->persist($DTVCPagoInteres);

                                $em->flush();

                                for ($i = 0; $i < count($pagosrealizados); $i++) {
                                    if ($pagosrealizados[$i]->getSininteres() == 1) {
                                        $pagosrealizados[$i]->setValorIngresado(
                                            $amortizaciones[$i + 1]->getValorcuota()
                                        );
                                        $pagosrealizados[$i]->setSininteres(0);
                                        $em->persist($pagosrealizados[$i]);
                                        $em->flush();
                                    }
                                }


                            } else {
                                if ($tipoTransaccion->getId() == 19) { //LIQUIDACIÓN

                                    //           SI LA LIQUIDACION ES NEGATIVA RECHAZA LA TRANSACCION

                                    if ($entity->getHaber() < 0) {
                                        $this->get('session')->getFlashBag()->add(
                                            'danger',
                                            'No se puede realizar la Liquidación, existen cuentas pendientes que no pueden ser cubiertas por su saldo a favor.'
                                        );

                                        return $this->redirect($this->generateUrl('libro_create'));
                                    }

                                    $em = $this->getDoctrine()->getManager();

                                    $liquidar = $entity->getInfo();

                                    //          PAGO DE CREDITOS

                                    $creditos = $em->getRepository(
                                        'ModulosPersonasBundle:Creditos'
                                    )->findCreditosOtrogadosByPersonaOrdDescPorFecha($entity->getPersona()->getId());

                                    $valorCreditos = 0;
                                    $valorInteresCreditos = 0;
                                    $valorDesgraCreditos = 0;

                                    for ($j = 0; $j < count($creditos); $j++) {
                                        $amortizaciones = $em->getRepository(
                                            'ModulosPersonasBundle:TablaAmortizacion'
                                        )->findTablasAmortizacionPorCreditos($creditos[$j]->getId());

                                        $pagosrealizados = $em->getRepository(
                                            'ModulosPersonasBundle:PagoCuotaCredito'
                                        )->findPagosCuotasCreditos($creditos[$j]->getId());

                                        $valorCapitalAmort = 0;
                                        $valorInteresAmort = 0;
                                        $valorDesgraAmort = 0;

                                        for ($i = (count($pagosrealizados) + 1); $i < count($amortizaciones); $i++) {
                                            $valorCapitalAmort += $amortizaciones[$i]->getCapital();
                                            //$valorInteresAmort += $amortizaciones[$i]->getInteres();
                                            $valorDesgraAmort += $amortizaciones[$i]->getDesgravamen();

                                            //if($amortizaciones[$i]->getFechaDePago()->format('d-m-Y') >= $amortizaciones[$i]->getFechaDePago()->format('d').'-'.$mes.'-'.$ano){
                                            if ($entity->getFecha()->format(
                                                    'Y-m'
                                                ) < $amortizaciones[$i]->getFechaDePago()->format('Y-m')
                                            ) {
                                                $interes = 0;
                                            } else {
                                                $interes = $amortizaciones[$i]->getInteres();
                                            }

                                            $valorInteresAmort += $interes;
                                        }

                                        $valorCreditos += $valorCapitalAmort;
                                        $valorInteresCreditos += $valorInteresAmort;
                                        $valorDesgraCreditos += $valorDesgraAmort;
                                    }

                                    $totalValorCreditos = $valorCreditos;
                                    $totalValorInteresCreditos = $valorInteresCreditos;
                                    $totalValorDesgraCreditos = $valorDesgraCreditos;

                                    $transaccionPagoCredito = $em->getRepository(
                                        'ModulosPersonasBundle:TipoProductoContable'
                                    )->findOneById(9);

                                    $transaccionPagoDesgravamen = $em->getRepository(
                                        'ModulosPersonasBundle:TipoProductoContable'
                                    )->findOneById(3);

                                    if ($totalValorCreditos > 0) {
                                        if ($totalValorDesgraCreditos > 0) {
                                            $libroPagoDesgravamen = new Libro();
                                            $libroPagoDesgravamen->setFecha($entity->getFecha());
                                            $libroPagoDesgravamen->setPersona($entity->getPersona());
                                            $libroPagoDesgravamen->setDebe($totalValorDesgraCreditos);
                                            $libroPagoDesgravamen->setHaber(0);
                                            $libroPagoDesgravamen->setProductoContableId($transaccionPagoDesgravamen);
                                            $libroPagoDesgravamen->setCuentaid(
                                                $transaccionPagoDesgravamen->getCuentaHaber()
                                            );
                                            $libroPagoDesgravamen->setInfo($liquidar);
                                            $libroPagoDesgravamen->setNumeroRecibo($entity->getNumeroRecibo());
                                            $libroPagoDesgravamen->setEstadosLibro($estadoLibro);

                                            $VCHRPagoDesgravamen = new VCHR();
                                            $VCHRPagoDesgravamen->setFecha($libroPagoDesgravamen->getFecha());
                                            $VCHRPagoDesgravamen->setMes(
                                                $libroPagoDesgravamen->getFecha()->format('m')
                                            );
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
                                        $libroPagoDeuda->setFecha($entity->getFecha());
                                        $libroPagoDeuda->setPersona($entity->getPersona());
                                        $libroPagoDeuda->setDebe($totalValorCreditos);
                                        $libroPagoDeuda->setHaber(0);
                                        $libroPagoDeuda->setProductoContableId($transaccionPagoCredito);
                                        $libroPagoDeuda->setCuentaid($transaccionPagoCredito->getCuentaHaber());
                                        $libroPagoDeuda->setInfo($liquidar);
                                        $libroPagoDeuda->setSaldo($saldo);

                                        if ($totalValorDesgraCreditos > 0) {
                                            $libroPagoDeuda->setNumeroRecibo($entity->getNumeroRecibo() + 1);
                                        } else {
                                            $libroPagoDeuda->setNumeroRecibo($entity->getNumeroRecibo());
                                        }

                                        $libroPagoDeuda->setEstadosLibro($estadoLibro);

                                        $VCHRPagoDeuda = new VCHR();
                                        $VCHRPagoDeuda->setFecha($libroPagoDeuda->getFecha());
                                        $VCHRPagoDeuda->setMes($libroPagoDeuda->getFecha()->format('m'));
                                        $VCHRPagoDeuda->setLibroId($libroPagoDeuda);


                                        $DTVCPagoDeuda = new DTVC();

                                        $DTVCPagoDeuda->setCuentaDeudoraId(
                                            $libroPagoDeuda->getProductoContableId()->getCuentaHaber()
                                        );
                                        $DTVCPagoDeuda->setCuentaAcreedoraId(
                                            $libroPagoDeuda->getProductoContableId()->getCuentaDebe()
                                        );
                                        $DTVCPagoDeuda->setValor(
                                            $libroPagoDeuda->getDebe() + $libroPagoDeuda->getHaber()
                                        );
                                        $DTVCPagoDeuda->setIdVchr($VCHRPagoDeuda);
                                        $DTVCPagoDeuda->setEsDebe($libroPagoDeuda->getDebe() > 0);

                                        $em->persist($libroPagoDeuda);
                                        $em->persist($VCHRPagoDeuda);
                                        $em->persist($DTVCPagoDeuda);

                                        $em->flush();

                                        $transaccionPagoInteres = $em->getRepository(
                                            'ModulosPersonasBundle:TipoProductoContable'
                                        )->findOneByTipo("Pago Interés");

                                        $libroPagoInteres = new Libro();
                                        $libroPagoInteres->setFecha($entity->getFecha());
                                        $libroPagoInteres->setPersona($entity->getPersona());
                                        $libroPagoInteres->setDebe($totalValorInteresCreditos);
                                        $libroPagoInteres->setHaber(0);
                                        $libroPagoInteres->setProductoContableId($transaccionPagoInteres);
                                        $libroPagoInteres->setCuentaid($transaccionPagoInteres->getCuentaHaber());
                                        $libroPagoInteres->setInfo($liquidar);
                                        $libroPagoInteres->setNumeroRecibo($libroPagoDeuda->getNumeroRecibo() + 1);
                                        $libroPagoInteres->setEstadosLibro($estadoLibro);

                                        $VCHRPagoInteres = new VCHR();
                                        $VCHRPagoInteres->setFecha($libroPagoInteres->getFecha());
                                        $VCHRPagoInteres->setMes($libroPagoInteres->getFecha()->format('m'));
                                        $VCHRPagoInteres->setLibroId($libroPagoInteres);


                                        $DTVCPagoInteres = new DTVC();
                                        $DTVCPagoInteres->setCuentaDeudoraId(
                                            $libroPagoInteres->getProductoContableId()->getCuentaHaber()
                                        );
                                        $DTVCPagoInteres->setCuentaAcreedoraId(
                                            $libroPagoInteres->getProductoContableId()->getCuentaDebe()
                                        );
                                        $DTVCPagoInteres->setValor(
                                            $libroPagoInteres->getDebe() + $libroPagoInteres->getHaber()
                                        );
                                        $DTVCPagoInteres->setIdVchr($VCHRPagoInteres);
                                        $DTVCPagoInteres->setEsDebe($libroPagoInteres->getDebe() > 0);

                                        $em->persist($libroPagoInteres);
                                        $em->persist($VCHRPagoInteres);
                                        $em->persist($DTVCPagoInteres);

                                        $em->flush();

                                        for ($j = 0; $j < count($creditos); $j++) {
                                            $amortizaciones = $em->getRepository(
                                                'ModulosPersonasBundle:TablaAmortizacion'
                                            )->findTablasAmortizacionPorCreditos($creditos[$j]->getId());

                                            $pagosrealizados = $em->getRepository(
                                                'ModulosPersonasBundle:PagoCuotaCredito'
                                            )->findPagosCuotasCreditos($creditos[$j]->getId());
                                            /*for ($i = 0; $i < count($amortizaciones); $i++) {
                            $amortizaciones[$i]->setValorCuota(
                                round($amortizaciones[$i]->getValorcuota(), 2)
                            );
                            $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                            $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                            $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
                        }*/

                                            $credito = $em->getRepository('ModulosPersonasBundle:Creditos')->find(
                                                $creditos[$j]->getId()
                                            );


                                            for ($i = (count($pagosrealizados) + 1); $i < count(
                                                $amortizaciones
                                            ); $i++) {
                                                $pagoCredito = new PagoCuotaCredito();
                                                $pagoCredito->setValorIngresado($amortizaciones[$i]->getValorcuota());
                                                $pagoCredito->setFechaDePago($entity->getFecha());
                                                $pagoCredito->setCreditoId($credito);
                                                $em->persist($pagoCredito);
                                                $em->flush();
                                            }

                                            $estadoCerrado = $em->getRepository(
                                                'ModulosPersonasBundle:EstadoCreditos'
                                            )->findOneByTipo("PAGADO");
                                            $credito->setEstadocreditos($estadoCerrado);
                                            $em->persist($credito);
                                            $em->flush();
                                        }

                                        //$this->actualizarSaldo($libroPagoDeuda);
                                        //$this->actualizarSaldo($libroPagoInteres);

                                    }

                                    //          DEVOLUCIÓN DE AHORROS

                                    $ahorrosVista = $em->getRepository(
                                        'ModulosPersonasBundle:Ahorro'
                                    )->findAhorrosVistaByTipoDepositadoPorPersona($entity->getPersona()->getId());
                                    $ahorrosPlazo = $em->getRepository(
                                        'ModulosPersonasBundle:Ahorro'
                                    )->findAhorrosPlazByTipoDepositadoPorPersona($entity->getPersona()->getId());
                                    $ahorrosRes = $em->getRepository(
                                        'ModulosPersonasBundle:Ahorro'
                                    )->findAhorrosResByTipoDepositadoPorPersona($entity->getPersona()->getId());

                                    $estadoAhorro = $em->getRepository('ModulosPersonasBundle:EstadoAhorro')->find(5);

                                    $valorAhorroVista = 0;
                                    $valorInteresAhorroVista = 0;
                                    $valorAhorroPlazoFijo = 0;
                                    $valorInteresAhorroPlazoFijo = 0;
                                    $valorAhorroRestringido = 0;
                                    $valorInteresAhorroRestringido = 0;

                                    for ($j = 0; $j < count($ahorrosVista); $j++) {
                                        $ahorrosVista[$j]->setEstadoAhorro($estadoAhorro);
                                        $valorAhorroVista += $ahorrosVista[$j]->getValorEnCaja();
                                        $difAno = $entity->getFecha()->format(
                                                'Y'
                                            ) - $ahorrosVista[$j]->getFechaSolicitud()->format('Y');
                                        $difMes = (($difAno * 12) + $entity->getFecha()->format(
                                                    'm'
                                                )) - $ahorrosVista[$j]->getFechaSolicitud()->format('m');

                                        if ($ahorrosVista[$j]->getFechaSolicitud()->format('Y-m') >= $entity->getFecha(
                                            )->format('Y-m')
                                        ) {
                                            $interes = 0;
                                        } else {
                                            $interes = $ahorrosVista[$j]->getValorEnCaja(
                                                ) * ($ahorrosVista[$j]->getTipoAhorro()->getTasaInteres(
                                                    ) / 12) * ($difMes);
                                        }
                                        $valorInteresAhorroVista += $interes;

                                        $cuotaAhorro = new PagoCuotaAhorro();
                                        $cuotaAhorro->setCuota($ahorrosVista[$j]->getValorEnCaja());
                                        $cuotaAhorro->setFechaDeEntrada($entity->getFecha());
                                        $cuotaAhorro->setInteres($interes);
                                        $cuotaAhorro->setTipo(-1);
                                        $cuotaAhorro->setIdAhorro($ahorrosVista[$j]);
                                        $cuotaAhorro->setCuotaAcumulada(
                                            $saldoAnterior - $ahorrosVista[$j]->getValorEnCaja()
                                        );

                                        $em->persist($cuotaAhorro);
                                        $em->flush();
                                    }

                                    $totalAhorroVista = $valorAhorroVista;
                                    $totalInteresAhorroVista = $valorInteresAhorroVista;

                                    for ($j = 0; $j < count($ahorrosPlazo); $j++) {
                                        $ahorrosPlazo[$j]->setEstadoAhorro($estadoAhorro);
                                        $valorAhorroPlazoFijo += $ahorrosPlazo[$j]->getValorEnCaja();

                                        if ($ahorrosPlazo[$j]->getFechaRenovacion()) {
                                            $difAno = $entity->getFecha()->format(
                                                    'Y'
                                                ) - $ahorrosPlazo[$j]->getFechaRenovacion()->format('Y');
                                            $difMes = (($difAno * 12) + $entity->getFecha()->format(
                                                        'm'
                                                    )) - $ahorrosPlazo[$j]->getFechaRenovacion()->format('m');

                                            if ($ahorrosPlazo[$j]->getFechaRenovacion()->format(
                                                    'Y-m'
                                                ) >= $entity->getFecha()->format('Y-m')
                                            ) {
                                                $interes = 0;
                                            } else {
                                                $interes = $ahorrosPlazo[$j]->getValorEnCaja(
                                                    ) * ($ahorrosPlazo[$j]->getTipoAhorro()->getTasaInteres(
                                                        ) / 12) * ($difMes);
                                            }
                                        } else {
                                            $difAno = $entity->getFecha()->format(
                                                    'Y'
                                                ) - $ahorrosPlazo[$j]->getFechaSolicitud()->format('Y');
                                            $difMes = (($difAno * 12) + $entity->getFecha()->format(
                                                        'm'
                                                    )) - $ahorrosPlazo[$j]->getFechaSolicitud()->format('m');

                                            if ($ahorrosPlazo[$j]->getFechaSolicitud()->format(
                                                    'Y-m'
                                                ) >= $entity->getFecha()->format('Y-m')
                                            ) {
                                                $interes = 0;
                                            } else {
                                                $interes = $ahorrosPlazo[$j]->getValorEnCaja(
                                                    ) * ($ahorrosPlazo[$j]->getTipoAhorro()->getTasaInteres(
                                                        ) / 12) * ($difMes);
                                            }
                                        }

                                        $valorInteresAhorroPlazoFijo += $interes;

                                        $cuotaAhorro = new PagoCuotaAhorro();
                                        $cuotaAhorro->setCuota($ahorrosPlazo[$j]->getValorEnCaja());
                                        $cuotaAhorro->setFechaDeEntrada($entity->getFecha());
                                        $cuotaAhorro->setInteres($interes);
                                        $cuotaAhorro->setTipo(-1);
                                        $cuotaAhorro->setIdAhorro($ahorrosPlazo[$j]);
                                        $cuotaAhorro->setCuotaAcumulada(
                                            $saldoAnterior - $ahorrosPlazo[$j]->getValorEnCaja()
                                        );

                                        $em->persist($cuotaAhorro);
                                        $em->flush();
                                    }

                                    $totalAhorroPlazoFijo = $valorAhorroPlazoFijo;
                                    $totalInteresAhorroPlazoFijo = $valorInteresAhorroPlazoFijo;

                                    for ($j = 0; $j < count($ahorrosRes); $j++) {
                                        $ahorrosRes[$j]->setEstadoAhorro($estadoAhorro);
                                        $valorAhorroRestringido += $ahorrosRes[$j]->getValorEnCaja();
                                        $difAno = $entity->getFecha()->format('Y') - $ahorrosRes[$j]->getFechaSolicitud(
                                            )->format('Y');
                                        $difMes = (($difAno * 12) + $entity->getFecha()->format(
                                                    'm'
                                                )) - $ahorrosRes[$j]->getFechaSolicitud()->format('m');
                                        if ($ahorrosRes[$j]->getFechaSolicitud()->format('Y-m') >= $entity->getFecha(
                                            )->format('Y-m')
                                        ) {
                                            $interes = 0;
                                        } else {
                                            $interes = $ahorrosRes[$j]->getValorEnCaja(
                                                ) * ($ahorrosRes[$j]->getTipoAhorro()->getTasaInteres(
                                                    ) / 12) * ($difMes);
                                        }
                                        $valorInteresAhorroRestringido += $interes;

                                        $cuotaAhorro = new PagoCuotaAhorro();
                                        $cuotaAhorro->setCuota($ahorrosRes[$j]->getValorEnCaja());
                                        $cuotaAhorro->setFechaDeEntrada($entity->getFecha());
                                        $cuotaAhorro->setInteres($interes);
                                        $cuotaAhorro->setTipo(-1);
                                        $cuotaAhorro->setIdAhorro($ahorrosRes[$j]);
                                        $cuotaAhorro->setCuotaAcumulada(
                                            $saldoAnterior - $ahorrosRes[$j]->getValorEnCaja()
                                        );

                                        $em->persist($cuotaAhorro);
                                        $em->flush();
                                    }

                                    $totalAhorroRestringido = $valorAhorroRestringido;
                                    $totalInteresAhorroRestringido = $valorInteresAhorroRestringido;


//                          Ahorro a la Vista
                                    if ($totalAhorroVista > 0) {
                                        $transaccionAhorroVista = $em->getRepository(
                                            'ModulosPersonasBundle:TipoProductoContable'
                                        )->findOneByTipo("Retiro de Ahorro a la Vista");

                                        $libroAhoVis = new Libro();
                                        $libroAhoVis->setFecha($entity->getFecha());
                                        $libroAhoVis->setPersona($entity->getPersona());
                                        $libroAhoVis->setDebe(0);
                                        $libroAhoVis->setHaber(round($totalAhorroVista, 2));
                                        $libroAhoVis->setProductoContableId($transaccionAhorroVista);
                                        $libroAhoVis->setCuentaid($transaccionAhorroVista->getCuentaDebe());
                                        $libroAhoVis->setInfo($liquidar);
                                        $libroAhoVis->setEstadosLibro($estadoLibro);

                                        if ($totalValorCreditos > 0) {
                                            $libroAhoVis->setNumeroRecibo($libroPagoInteres->getNumeroRecibo() + 1);
                                        } else {
                                            $libroAhoVis->setNumeroRecibo($entity->getNumeroRecibo());
                                        }

                                        //creando DTVC y VCHR
                                        $VCHRAhoVis = new VCHR();
                                        $VCHRAhoVis->setFecha($entity->getFecha());
                                        $VCHRAhoVis->setMes($entity->getFecha()->format('m'));
                                        $VCHRAhoVis->setLibroId($libroAhoVis);

                                        $DTVCAhoVis = new DTVC();
                                        $DTVCAhoVis->setCuentaDeudoraId(
                                            $libroAhoVis->getProductoContableId()->getCuentaHaber()
                                        );
                                        $DTVCAhoVis->setCuentaAcreedoraId(
                                            $libroAhoVis->getProductoContableId()->getCuentaDebe()
                                        );
                                        $DTVCAhoVis->setValor(
                                            round($libroAhoVis->getDebe() + $libroAhoVis->getHaber(), 2)
                                        );
                                        $DTVCAhoVis->setIdVchr($VCHRAhoVis);
                                        $DTVCAhoVis->setEsDebe($libroAhoVis->getDebe() > 0);

                                        $em->persist($libroAhoVis);
                                        $em->persist($VCHRAhoVis);
                                        $em->persist($DTVCAhoVis);

                                        $em->flush();

                                        //$this->actualizarSaldo($libroAhoVis);

                                        //Interes a pagar

                                        $transaccionPagoInteres = $em->getRepository(
                                            'ModulosPersonasBundle:TipoProductoContable'
                                        )->findOneByTipo("Pago Intereses Ahorros");

                                        $libroPagoInteresAhoVis = new Libro();
                                        $libroPagoInteresAhoVis->setFecha($entity->getFecha());
                                        $libroPagoInteresAhoVis->setPersona($entity->getPersona());
                                        $libroPagoInteresAhoVis->setDebe(0);
                                        $libroPagoInteresAhoVis->setHaber($totalInteresAhorroVista);
                                        $libroPagoInteresAhoVis->setProductoContableId($transaccionPagoInteres);
                                        $libroPagoInteresAhoVis->setCuentaid($transaccionPagoInteres->getCuentaDebe());
                                        $libroPagoInteresAhoVis->setInfo($liquidar);
                                        $libroPagoInteresAhoVis->setNumeroRecibo($libroAhoVis->getNumeroRecibo() + 1);
                                        $libroPagoInteresAhoVis->setEstadosLibro($estadoLibro);

                                        $VCHRPagoInteresAhoVis = new VCHR();
                                        $VCHRPagoInteresAhoVis->setFecha($libroPagoInteresAhoVis->getFecha());
                                        $VCHRPagoInteresAhoVis->setMes(
                                            $libroPagoInteresAhoVis->getFecha()->format('m')
                                        );
                                        $VCHRPagoInteresAhoVis->setLibroId($libroPagoInteresAhoVis);

                                        $DTVCPagoInteresAhoVis = new DTVC();
                                        $DTVCPagoInteresAhoVis->setCuentaDeudoraId(
                                            $libroPagoInteresAhoVis->getProductoContableId()->getCuentaHaber()
                                        );
                                        $DTVCPagoInteresAhoVis->setCuentaAcreedoraId(
                                            $libroPagoInteresAhoVis->getProductoContableId()->getCuentaDebe()
                                        );
                                        $DTVCPagoInteresAhoVis->setValor(
                                            $libroPagoInteresAhoVis->getDebe() + $libroPagoInteresAhoVis->getHaber()
                                        );
                                        $DTVCPagoInteresAhoVis->setIdVchr($VCHRPagoInteresAhoVis);
                                        $DTVCPagoInteresAhoVis->setEsDebe($libroPagoInteresAhoVis->getDebe() > 0);

                                        $em->persist($libroPagoInteresAhoVis);
                                        $em->persist($VCHRPagoInteresAhoVis);
                                        $em->persist($DTVCPagoInteresAhoVis);

                                        $em->flush();

                                        //$this->actualizarSaldo($libroPagoInteresAhoVis);
                                    }

                                    //                          Ahorro a Plazo Fijo
                                    if ($totalAhorroPlazoFijo > 0) {
                                        $transaccionAhorroPlazo = $em->getRepository(
                                            'ModulosPersonasBundle:TipoProductoContable'
                                        )->findOneByTipo("Retiro de Ahorro Plazo Fijo");

                                        $libroAhoPla = new Libro();
                                        $libroAhoPla->setFecha($entity->getFecha());
                                        $libroAhoPla->setPersona($entity->getPersona());
                                        $libroAhoPla->setDebe(0);
                                        $libroAhoPla->setHaber(round($totalAhorroPlazoFijo, 2));
                                        $libroAhoPla->setProductoContableId($transaccionAhorroPlazo);
                                        $libroAhoPla->setCuentaid($transaccionAhorroPlazo->getCuentaDebe());
                                        $libroAhoPla->setInfo($liquidar);
                                        $libroAhoPla->setEstadosLibro($estadoLibro);

                                        if ($totalValorCreditos > 0 && $totalAhorroVista == 0) {
                                            $libroAhoPla->setNumeroRecibo($libroPagoInteres->getNumeroRecibo() + 1);
                                        } else {
                                            if ($totalAhorroVista > 0) {
                                                $libroAhoPla->setNumeroRecibo(
                                                    $libroPagoInteresAhoVis->getNumeroRecibo() + 1
                                                );
                                            } else {
                                                $libroAhoPla->setNumeroRecibo($entity->getNumeroRecibo());
                                            }
                                        }

                                        //creando DTVC y VCHR
                                        $VCHRAhoPla = new VCHR();
                                        $VCHRAhoPla->setFecha($entity->getFecha());
                                        $VCHRAhoPla->setMes($entity->getFecha()->format('m'));
                                        $VCHRAhoPla->setLibroId($libroAhoPla);

                                        $DTVCAhoPla = new DTVC();
                                        $DTVCAhoPla->setCuentaDeudoraId(
                                            $libroAhoPla->getProductoContableId()->getCuentaHaber()
                                        );
                                        $DTVCAhoPla->setCuentaAcreedoraId(
                                            $libroAhoPla->getProductoContableId()->getCuentaDebe()
                                        );
                                        $DTVCAhoPla->setValor(
                                            round($libroAhoPla->getDebe() + $libroAhoPla->getHaber(), 2)
                                        );
                                        $DTVCAhoPla->setIdVchr($VCHRAhoPla);
                                        $DTVCAhoPla->setEsDebe($libroAhoPla->getDebe() > 0);

                                        $em->persist($libroAhoPla);
                                        $em->persist($VCHRAhoPla);
                                        $em->persist($DTVCAhoPla);

                                        $em->flush();

                                        //$this->actualizarSaldo($libroAhoPla);

                                        //Interes a pagar

                                        $transaccionPagoInteres = $em->getRepository(
                                            'ModulosPersonasBundle:TipoProductoContable'
                                        )->findOneByTipo("Pago Intereses Ahorros");

                                        $libroPagoInteresAhoPla = new Libro();
                                        $libroPagoInteresAhoPla->setFecha($entity->getFecha());
                                        $libroPagoInteresAhoPla->setPersona($entity->getPersona());
                                        $libroPagoInteresAhoPla->setDebe(0);
                                        $libroPagoInteresAhoPla->setHaber($totalInteresAhorroPlazoFijo);
                                        $libroPagoInteresAhoPla->setProductoContableId($transaccionPagoInteres);
                                        $libroPagoInteresAhoPla->setCuentaid($transaccionPagoInteres->getCuentaDebe());
                                        $libroPagoInteresAhoPla->setInfo($liquidar);
                                        $libroPagoInteresAhoPla->setNumeroRecibo($libroAhoPla->getNumeroRecibo() + 1);
                                        $libroPagoInteresAhoPla->setEstadosLibro($estadoLibro);

                                        $VCHRPagoInteresAhoPla = new VCHR();
                                        $VCHRPagoInteresAhoPla->setFecha($libroPagoInteresAhoPla->getFecha());
                                        $VCHRPagoInteresAhoPla->setMes(
                                            $libroPagoInteresAhoPla->getFecha()->format('m')
                                        );
                                        $VCHRPagoInteresAhoPla->setLibroId($libroPagoInteresAhoPla);

                                        $DTVCPagoInteresAhoPla = new DTVC();
                                        $DTVCPagoInteresAhoPla->setCuentaDeudoraId(
                                            $libroPagoInteresAhoPla->getProductoContableId()->getCuentaHaber()
                                        );
                                        $DTVCPagoInteresAhoPla->setCuentaAcreedoraId(
                                            $libroPagoInteresAhoPla->getProductoContableId()->getCuentaDebe()
                                        );
                                        $DTVCPagoInteresAhoPla->setValor(
                                            $libroPagoInteresAhoPla->getDebe() + $libroPagoInteresAhoPla->getHaber()
                                        );
                                        $DTVCPagoInteresAhoPla->setIdVchr($VCHRPagoInteresAhoPla);
                                        $DTVCPagoInteresAhoPla->setEsDebe($libroPagoInteresAhoPla->getDebe() > 0);

                                        $em->persist($libroPagoInteresAhoPla);
                                        $em->persist($VCHRPagoInteresAhoPla);
                                        $em->persist($DTVCPagoInteresAhoPla);

                                        $em->flush();

                                        //$this->actualizarSaldo($libroPagoInteresAhoPla);
                                    }

                                    //                          Ahorro Restringido
                                    if ($totalAhorroRestringido > 0) {
                                        $transaccionAhorroRes = $em->getRepository(
                                            'ModulosPersonasBundle:TipoProductoContable'
                                        )->findOneByTipo("Retiro Ahorro Restringido");

                                        $libroAhoRes = new Libro();
                                        $libroAhoRes->setFecha($entity->getFecha());
                                        $libroAhoRes->setPersona($entity->getPersona());
                                        $libroAhoRes->setDebe(0);
                                        $libroAhoRes->setHaber($totalAhorroRestringido);
                                        $libroAhoRes->setProductoContableId($transaccionAhorroRes);
                                        $libroAhoRes->setCuentaid($transaccionAhorroRes->getCuentaDebe());
                                        $libroAhoRes->setInfo($liquidar);
                                        $libroAhoRes->setEstadosLibro($estadoLibro);

                                        if ($totalValorCreditos > 0 && $totalAhorroVista == 0 && $totalAhorroPlazoFijo == 0) {
                                            $libroAhoRes->setNumeroRecibo($libroPagoInteres->getNumeroRecibo() + 1);
                                        } else {
                                            if ($totalAhorroVista > 0 && $totalAhorroPlazoFijo == 0) {
                                                $libroAhoRes->setNumeroRecibo(
                                                    $libroPagoInteresAhoVis->getNumeroRecibo() + 1
                                                );
                                            } else {
                                                if ($totalAhorroPlazoFijo > 0) {
                                                    $libroAhoRes->setNumeroRecibo(
                                                        $libroPagoInteresAhoPla->getNumeroRecibo() + 1
                                                    );
                                                } else {
                                                    $libroAhoRes->setNumeroRecibo($entity->getNumeroRecibo());
                                                }
                                            }
                                        }

                                        //creando DTVC y VCHR
                                        $VCHRAhoRes = new VCHR();
                                        $VCHRAhoRes->setFecha($entity->getFecha());
                                        $VCHRAhoRes->setMes($entity->getFecha()->format('m'));
                                        $VCHRAhoRes->setLibroId($libroAhoRes);

                                        $DTVCAhoRes = new DTVC();
                                        $DTVCAhoRes->setCuentaDeudoraId(
                                            $libroAhoRes->getProductoContableId()->getCuentaHaber()
                                        );
                                        $DTVCAhoRes->setCuentaAcreedoraId(
                                            $libroAhoRes->getProductoContableId()->getCuentaDebe()
                                        );
                                        $DTVCAhoRes->setValor($libroAhoRes->getDebe() + $libroAhoRes->getHaber());
                                        $DTVCAhoRes->setIdVchr($VCHRAhoRes);
                                        $DTVCAhoRes->setEsDebe($libroAhoRes->getDebe() > 0);

                                        $em->persist($libroAhoRes);
                                        $em->persist($VCHRAhoRes);
                                        $em->persist($DTVCAhoRes);

                                        $em->flush();

                                        //$this->actualizarSaldo($libroAhoRes);

                                        //Interes a pagar

                                        $transaccionPagoInteres = $em->getRepository(
                                            'ModulosPersonasBundle:TipoProductoContable'
                                        )->findOneByTipo("Pago Intereses Ahorros");

                                        $libroPagoInteresAhoRes = new Libro();
                                        $libroPagoInteresAhoRes->setFecha($entity->getFecha());
                                        $libroPagoInteresAhoRes->setPersona($entity->getPersona());
                                        $libroPagoInteresAhoRes->setDebe(0);
                                        $libroPagoInteresAhoRes->setHaber(round($totalInteresAhorroRestringido, 2));
                                        $libroPagoInteresAhoRes->setProductoContableId($transaccionPagoInteres);
                                        $libroPagoInteresAhoRes->setCuentaid($transaccionPagoInteres->getCuentaDebe());
                                        $libroPagoInteresAhoRes->setInfo($liquidar);
                                        $libroPagoInteresAhoRes->setNumeroRecibo($libroAhoRes->getNumeroRecibo() + 1);
                                        $libroPagoInteresAhoRes->setEstadosLibro($estadoLibro);

                                        $VCHRPagoInteresAhoRes = new VCHR();
                                        $VCHRPagoInteresAhoRes->setFecha($libroPagoInteresAhoRes->getFecha());
                                        $VCHRPagoInteresAhoRes->setMes(
                                            $libroPagoInteresAhoRes->getFecha()->format('m')
                                        );
                                        $VCHRPagoInteresAhoRes->setLibroId($libroPagoInteresAhoRes);

                                        $DTVCPagoInteresAhoRes = new DTVC();
                                        $DTVCPagoInteresAhoRes->setCuentaDeudoraId(
                                            $libroPagoInteresAhoRes->getProductoContableId()->getCuentaHaber()
                                        );
                                        $DTVCPagoInteresAhoRes->setCuentaAcreedoraId(
                                            $libroPagoInteresAhoRes->getProductoContableId()->getCuentaDebe()
                                        );
                                        $DTVCPagoInteresAhoRes->setValor(
                                            round(
                                                $libroPagoInteresAhoRes->getDebe() + $libroPagoInteresAhoRes->getHaber(
                                                ),
                                                2
                                            )
                                        );
                                        $DTVCPagoInteresAhoRes->setIdVchr($VCHRPagoInteresAhoRes);
                                        $DTVCPagoInteresAhoRes->setEsDebe($libroPagoInteresAhoRes->getDebe() > 0);

                                        $em->persist($libroPagoInteresAhoRes);
                                        $em->persist($VCHRPagoInteresAhoRes);
                                        $em->persist($DTVCPagoInteresAhoRes);

                                        $em->flush();

                                        //$this->actualizarSaldo($libroPagoInteresAhoRes);
                                    }

                                    for ($j = 0; $j < count($ahorrosVista); $j++) {
                                        $ahorrosVista[$j]->setValorEnCaja(0);
                                        $em->persist($ahorrosVista[$j]);
                                    }

                                    for ($j = 0; $j < count($ahorrosPlazo); $j++) {
                                        $ahorrosPlazo[$j]->setValorEnCaja(0);
                                        $em->persist($ahorrosPlazo[$j]);
                                    }

                                    for ($j = 0; $j < count($ahorrosRes); $j++) {
                                        $ahorrosRes[$j]->setValorEnCaja(0);
                                        $em->persist($ahorrosRes[$j]);
                                    }

                                    $aportes = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersona(
                                        $entity->getPersona()->getId()
                                    );
                                    $retiro = $em->getRepository(
                                        'ModulosPersonasBundle:Libro'
                                    )->findRetiroAportePorPersona($entity->getPersona()->getId());

                                    $personaAportes = 0;
                                    $personaRetiroAportes = 0;

                                    for ($j = 0; $j < count($aportes); $j++) {
                                        $personaAportes += $aportes[$j]->getDebe();
                                    }

                                    for ($j = 0; $j < count($retiro); $j++) {
                                        $personaRetiroAportes += $retiro[$j]->getHaber();
                                    }

                                    $saldoAporte = $personaAportes;
                                    $saldoRetiro = $personaRetiroAportes;
                                    $saldoFinal = $personaAportes - $personaRetiroAportes;


                                    $entity->setHaber($saldoFinal);
                                    $entity->setInfo($liquidar);
                                    $entity->setEstadosLibro($estadoLibro);

                                    if ($totalValorCreditos > 0 && $totalAhorroVista == 0 && $totalAhorroPlazoFijo == 0 && $totalAhorroRestringido == 0) {
                                        $entity->setNumeroRecibo($libroPagoInteres->getNumeroRecibo() + 1);
                                    } else {
                                        if ($totalAhorroVista > 0 && $totalAhorroPlazoFijo == 0 && $totalAhorroRestringido == 0) {
                                            $entity->setNumeroRecibo($libroPagoInteresAhoVis->getNumeroRecibo() + 1);
                                        } else {
                                            if ($totalAhorroPlazoFijo > 0 && $totalAhorroRestringido == 0) {
                                                $entity->setNumeroRecibo(
                                                    $libroPagoInteresAhoPla->getNumeroRecibo() + 1
                                                );
                                            } else {
                                                if ($totalAhorroRestringido > 0) {
                                                    $entity->setNumeroRecibo(
                                                        $libroPagoInteresAhoRes->getNumeroRecibo() + 1
                                                    );
                                                } else {
                                                    $entity->setNumeroRecibo($entity->getNumeroRecibo());
                                                }
                                            }
                                        }
                                    }

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
                                    //$this->actualizarSaldo($entity);

                                    $personaInactiva = $em->getRepository('ModulosPersonasBundle:Persona')->find(
                                        $entity->getPersona()->getId()
                                    );
                                    $personaInactiva->setEstadoPersona(1);
                                    $em->persist($personaInactiva);
                                    $em->flush();

                                } else {

                                    $em = $this->getDoctrine()->getManager();
                                    $entity->setEstadosLibro($estadoLibro);
                                    $entity->setNumeroRecibo($entity->getNumeroRecibo());

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

                                    //$this->actualizarSaldo($entity);

                                }
                            }
                        }
                    }
                }
            }


            /*$fecha1 = $entity->getFecha();
            $fecha2 = new \DateTime();
            $fecha1->setDate($fecha1->format('Y'), $fecha1->format('m'), 1);
            $aux1 = $fecha1->format('t');
            if(count($libros)==0){
                $fecha2->setDate($fecha1->format('Y'), $fecha1->format('m'), $aux1);
            }else{
                $fecha2->setDate($ano, $mes, $aux1);
            }


            $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenados(
                $fecha1,
                $fecha2
            );
            $array = array();
            if (count($librosOrdenados) > 0) {
                $array [] = $fecha1->format('Y-m');
            }

            return $this->render(
                'ModulosPersonasBundle:Libro:index.html.twig',
                array(
                    'listado' => $librosOrdenados,
                    'array' => $array,
                )
            );*/
            //echo $entity->getFecha();
            $fechaingreso = $entity->getFecha();
            //echo $fechaingreso->format('dd-MM-yyyy HH:mm:ss');
            //return $this->redirect($this->generateUrl('actualizarSaldo',array('fecha' => $fechaingreso->format('d-M-y H:m:s'), 'id' =>$entity->getId())));
            if ($tipoTransaccion->getId() != 9 && $tipoTransaccion->getId() != 15 && $tipoTransaccion->getId(
                ) != 16 && $tipoTransaccion->getId() != 17 && $tipoTransaccion->getId() != 19
            ) {
                //$this->actualizarSaldo($entity);
            }

            //$this->actualizarSaldo($libros[count($libros)-1]);

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado correctamente.'
            );

            return $this->redirect($this->generateUrl('libro'));

        }

        return $this->render(
            'ModulosPersonasBundle:Libro:new.html.twig',
            array(
                'entity' => $entity,
                //'liquidacion'=>$personaMes,
                'form' => $form->createView(),
                'listado' => $librosOrdenados,
                'array' => $array,
                'fecha1' => $fecha1,
                'fecha2' => $fecha2,
                'estadoslibro' => $estadoslibro,
                'saldo' => $saldo,
                'consec' => $consec,
                'tiposProductosContables' => $tiposProductosContables,
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

        $librosAnteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaDescId(
            $fechaInicial,
            $fechaIngreso
        );


        if (count($librosAnteriores) > 0) {
            $saldoAnterior = $librosAnteriores[1]->getSaldo();
            $entity->setSaldo($saldoAnterior + $debe - $haber);
            //$entity->setSaldo(round($saldoAnterior + $debe- $haber,2));
        } else {
            if ($debe > 0) {
                $entity->setSaldo($debe);
                //$entity->setSaldo(round($debe,2));
            }//elseif($haber>0){
            // $entity->setSaldo(0-$haber);
            //}

        }

        $librosPosteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaAscId(
            $fechaIngreso,
            $fechaReciente
        );
        if (count($librosPosteriores) > 0) {
            $librosPosteriores[count($librosPosteriores) - 1]->getSaldo();
            for ($i = 0; $i < count($librosPosteriores); $i++) {
                if ($i > 0) {
                    $id = $librosPosteriores[$i]->getId();
                    $libroActualizar = $em->getRepository('ModulosPersonasBundle:Libro')->find($id);
                    $libroActualizar->setSaldo(
                        $librosPosteriores[$i - 1]->getSaldo() + $libroActualizar->getDebe(
                        ) - $libroActualizar->getHaber()
                    );
                    //$libroActualizar->setSaldo(round($librosPosteriores[$i-1]->getSaldo()+$libroActualizar->getDebe()-$libroActualizar->getHaber(),2));
                    $em->persist($libroActualizar);
                }
            }
        }

        $em->persist($entity);

        $em->flush();

        //return $this->redirect($this->generateUrl('libro'));

    }

    public
    function cambiarEstadoAction(
        $mes,
        $ano
    ) {

        $array = array();
        $em = $this->getDoctrine()->getManager();
        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findAll();

        $estado = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneById(2);

        $cantlibrosmes = 0;
        foreach ($libros as $libro) {

            if ($libro->getFecha()->format('m') == $mes && $libro->getFecha()->format('Y') == $ano) {
                if ($libro->getEstadosLibro()->getId() == 2) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        'El mes que está cerrando ya estaba cerrado.'
                    );

                    return $this->redirect($this->generateUrl('libro'));
                }
                $libro->setEstadosLibro($estado);
                $em->persist($libro);
                $cantlibrosmes++;

            }

        }
        $em->flush();
        $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();
        $fechaUltima = $librosOrdenados[0]->getFecha();
        $mesCom = strlen("0".$mes) == 2 ? "0".$mes : $mes;
        $mesAno = $fechaUltima->format("Y-m");
        if ($mesAno == ($ano."-".$mesCom)) {
            $libroNuevo = new Libro();
            $libroNuevo->setSaldo(count($librosOrdenados) > 0 ? $librosOrdenados[0]->getSaldo() : 0);
            $estadoAbierto = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneById(1);
            $libroNuevo->setEstadosLibro($estadoAbierto);

            $libroMayMen = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

            if (count($libroMayMen) > 0) {
                $f = $libroMayMen[0]->getFecha();
                $intervalo = new \DateInterval('P1M');
                $f->setDate($f->format('Y'), $f->format('m'), 1);
                $f->add($intervalo);
            } else {
                $f = new \DateTime();
                $f->setDate($f->format('Y'), ($mes + 1), 1);
            }

            $libroNuevo->setFecha($f);
            $em->persist($libroNuevo);
        }

        $em->flush();

        return $this->redirect($this->generateUrl('libro'));

    }

    public
    function abrirMesAction(
        $mes,
        $ano
    ) {

        $array = array();
        $em = $this->getDoctrine()->getManager();
        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findAll();

        $estado = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneById(1);

        $cantlibrosmes = 0;
        foreach ($libros as $libro) {

            if ($libro->getFecha()->format('m') == $mes && $libro->getFecha()->format('Y') == $ano) {
                if ($libro->getEstadosLibro()->getId() == 1) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        'El mes que está abriendo ya estaba abierto.'
                    );

                    return $this->redirect($this->generateUrl('libro'));
                }
                $libro->setEstadosLibro($estado);
                $em->persist($libro);
                $cantlibrosmes++;

            }

        }
        $em->flush();

        /*$librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();
        $fechaUltima=$librosOrdenados[0]->getFecha();
        $mesCom=strlen("0".$mes)==2? "0".$mes:$mes;
        $mesAno=$fechaUltima->format("Y-m");
        if($mesAno == ($ano."-".$mesCom)){
            $libroNuevo = new Libro();
            $libroNuevo->setSaldo(count($librosOrdenados) > 0 ? $librosOrdenados[0]->getSaldo() : 0);
            $estadoAbierto = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneById(2);
            $libroNuevo->setEstadosLibro($estadoAbierto);

            $libroMayMen = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

            if (count($libroMayMen) > 0) {
                $f = $libroMayMen[0]->getFecha();
                $intervalo = new \DateInterval('P1M');
                $f->setDate($f->format('Y'), $f->format('m'), 1);
                $f->add($intervalo);
            } else {
                $f = new \DateTime();
                $f->setDate($f->format('Y'), ($mes + 1), 1);
            }

            $libroNuevo->setFecha($f);
            $em->persist($libroNuevo);
        }

        $em->flush();
        */

        return $this->redirect($this->generateUrl('libro'));

    }

    /*public
    function abrirMesAction(
        $mes,
        $ano
    ) {

        $array = array();
        $em = $this->getDoctrine()->getManager();
        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findAll();

        $estado = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneById(1);

        $cantlibrosmes = 0;

        $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();
        if (count($librosOrdenados) > 0) {
            $fechaUltima=$librosOrdenados[0]->getFecha();
            $intervalo = new \DateInterval('P1M');
            $fechaUltima->sub($intervalo);
            $mesCom=strlen("0".$mes)==2? "0".$mes:$mes;

            $mesAno=$fechaUltima->format("Y-m");
//            echo($mesAno ." ". ($ano."-".$mesCom));
//            die();
            if($mesAno == ($ano."-".$mesCom)){
                $fecha1 = new \DateTime();
                $fecha2 = new \DateTime();
                $aux = "0:0:0";
                $fecha1 = $fecha1->format('Y-m-d');
                $fechaaux = $fecha1." ".$aux;
                $fecha1 = new \DateTime($fechaaux);

                $aux2 = "23:59:59";
                $fecha2 = $fecha2->format('Y-m-d');
                $fechaaux2 = $fecha2." ".$aux2;
                $fecha2 = new \DateTime($fechaaux2);

                $fecha1->setDate($ano, $mesCom, 1);
                $aux1 = $fecha1->format('t');
                $fecha2->setDate($ano, $mes, $aux1);

                $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenados($fecha1, $fecha2);
                foreach ($librosOrdenados as $libro) {

                        if ($libro->getEstadosLibro()->getId() == 1) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                'El mes que está abriendo ya estaba abierto.'
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libro->setEstadosLibro($estado);
                        $em->persist($libro);
                    }

            }else{
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Solo se puede abrir un mes anterior al último'
                );

                return $this->redirect($this->generateUrl('libro'));
            }



        }else{
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'No hay transacciones en el sistema'
                );

                return $this->redirect($this->generateUrl('libro'));

        }

        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'info',
            'Mes abierto correctamente'
        );
        return $this->redirect($this->generateUrl('libro'));

    }*/

    public
    function filtrarAction(
        $mes,
        $ano
    ) {
        $em = $this->getDoctrine()->getManager();

        $fecha1 = new \DateTime();
        $fecha2 = new \DateTime();
        $aux = "0:0:0";
        $fecha1 = $fecha1->format('Y-m-d');
        $fechaaux = $fecha1." ".$aux;
        $fecha1 = new \DateTime($fechaaux);

        $aux2 = "23:59:59";
        $fecha2 = $fecha2->format('Y-m-d');
        $fechaaux2 = $fecha2." ".$aux2;
        $fecha2 = new \DateTime($fechaaux2);

        $fecha1->setDate($ano, $mes, 1);
        $aux1 = $fecha1->format('t');
        $fecha2->setDate($ano, $mes, $aux1);

        $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaDescId(
            $fecha1,
            $fecha2
        );
        $array = array();
        $estado = '';
        if (count($librosOrdenados) > 0) {
            $array [] = $fecha1->format('Y-m');
            $this->actualizarSaldo($librosOrdenados[count($librosOrdenados) - 1]);
            $estado = $librosOrdenados[0]->getEstadosLibro();
        }

        return $this->render(
            'ModulosPersonasBundle:Libro:index.html.twig',
            array(
                'listado' => $librosOrdenados,
                'array' => $array,
                'estado' => $estado,

            )
        );

    }

    /**
     * Finds and displays a Libro entity.
     *
     */
    public
    function showAction(
        $id
    ) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Libro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Libro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'ModulosPersonasBundle:Libro:show.html.twig',
            array(
                'entity' => $entity,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing Libro entity.
     *
     */
    public
    function editAction(
        $id
    ) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Libro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Libro entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'ModulosPersonasBundle:Libro:edit.html.twig',
            array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Creates a form to edit a Libro entity.
     *
     * @param Libro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createEditForm(
        Libro $entity
    ) {
        $form = $this->createForm(
            new LibroType(),
            $entity,
            array(
                'action' => $this->generateUrl('libro_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Libro entity.
     *
     */
    public
    function updateAction(
        Request $request,
        $id
    ) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:Libro')->find($id);

        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();
        if (count($libros) > 0) {
            $this->actualizarSaldo($libros[count($libros) - 1]);
            $saldoActual = $libros[0]->getSaldo();

        } else {
            $saldoActual = 0;
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Libro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        //$editForm = $this->createEditForm($entity);
        $editForm = $this->createForm(new LibroType(), $entity);
        $editForm->handleRequest($request);
        $tiposProductosContables = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findAll();


        if ($editForm->isValid()) {
            $entityVieja = $em->getRepository('ModulosPersonasBundle:Libro')->findDebe($id);
            $debeViejo = $entityVieja["debe"];
            $haberViejo = $entityVieja["haber"];
            $saldoViejo = $entityVieja["saldo"];
            $infoViejo = $entityVieja["info"];


            $debedif = $debeViejo - $entity->getDebe();
            $haberdif = $haberViejo - $entity->getHaber();
            $diferencia = $debedif + $haberdif;

            $librosPosteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosMayoresNumeroRecibo(
                $entity->getNumeroRecibo()
            );

            $saldo = $saldoViejo - $diferencia;
//            echo $diferencia;
//            die(0);
//            echo
//
//            echo $diferencia."    ";
//            echo ($saldoViejo-$diferencia)."".$entity->getSaldo();
//            die();
            $librosGuardar = array();
            switch ($entity->getProductoContableId()->getId()) {
                case 1: {//Aporte Monetario
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    $entity->setSaldo($saldoViejo - $diferencia);


                }
                    break;

                case 2: {//Pago Solicitud
                    //chequear q no haya un credito de esa persona q haya consumido esa solicitud
                    if ($entity->getInfo() == 1) {//hay un credito q usa esta solicitud
                        $this->get('session')->getFlashBag()->add(
                            'error',
                            "No se puede eliminar porque hay un crédito que utliliza esta solcitud"
                        );

                        return $this->redirect($this->generateUrl('libro'));
                    } else {
                        foreach ($librosPosteriores as $libroPost) {
                            $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                            if ($saldo < 0) {
                                $this->get('session')->getFlashBag()->add(
                                    'error',
                                    "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                    ).". No se puede realizar la acción"
                                );

                                return $this->redirect($this->generateUrl('libro'));
                            }
                            $libroPost->setSaldo($saldo);
                            $librosGuardar[] = $libroPost;
                        }
                    }

                }
                    break;
                case 3: {//Pago Desgravamen
                    $idCredito = $entity->getInfo();
                    //busco en el libro si ya esta este credito
                    foreach ($librosPosteriores as $libroPost) {
                        if (($libroPost->getProductoContableId()->getId() == 4 || $libroPost->getProductoContableId(
                                )->getId() == 5) && $libroPost->getInfo() == $idCredito
                        ) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque está asociado al crédito ".$idCredito
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                    }

                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());
                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    $entity->setSaldo($saldoViejo - $diferencia);

                }
                    break;
                case 4: {//Crédito Otorgado
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    $entity->setSaldo($saldoViejo - $diferencia);
                }
                    break;
                case 5: {//Crédito Emergente
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    $entity->setSaldo($saldoViejo - $diferencia);
                }
                    break;
                case 6: {//Préstamo
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    $entity->setSaldo($saldoViejo - $diferencia);
                }
                    break;
                case 7: {//Gastos Papelería
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    $entity->setSaldo($saldoViejo - $diferencia);
                }
                    break;
                case 8: {//Otros Gastos
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    $entity->setSaldo($saldoViejo - $diferencia);
                }
                    break;
                case 9: {//Pago Crédito
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    $entity->setSaldo($saldoViejo - $diferencia);

                }
                    break;
                /*case 10:{//Pago Interés
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        "No se puede eliminar el interés intente eliminar el pago de crédito asociado"
                    );
                    return $this->redirect($this->generateUrl('libro'));

                }break;*/
                case 11: {//Multa
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    $entity->setSaldo($saldoViejo - $diferencia);
                }
                    break;
                case 12: {//Ahorro a Vista
//                    echo $entity->getInfo();
//                    echo " ";
//                    echo $infoViejo;
                    $entity->setInfo($infoViejo);
//                    die();
                    //cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $idPagoAhorro = $entity->getInfo();
                    $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($idPagoAhorro);
                    $pagoAhorro->setCuota($entity->getDebe());
                    $ahorro = $pagoAhorro->getIdAhorro();

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findDepositosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $cuotaMenoresList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $depositosList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findRetirosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $pagosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMayoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $pagosListMen = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );


                    $saldoAnterior = 0;
                    $fechaAnterior = new \DateTime();
                    if (count($cuotaMenoresList) > 0) {
                        $saldoAnterior = $cuotaMenoresList[(count($cuotaMenoresList) - 1)]->getCuotaAcumulada();
                    }
                    if (count($depositosList) > 0) {
                        $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                    }
                    $difAno = $pagoAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                    $difMes = (($difAno * 12) + $pagoAhorro->getFechaDeEntrada()->format('m')) - $fechaAnterior->format(
                            'm'
                        );
                    if ($fechaAnterior->format('m-Y') == $pagoAhorro->getFechaDeEntrada()->format('m-Y')) {
                        $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                    } else {
                        $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro()->getTasaInteres(
                                ) / 12) * ($difMes);
                    }

                    $pagoAhorro->setInteres($interes);
                    $pagoAhorro->setCuotaAcumulada($interes + $saldoAnterior + $entity->getDebe());

                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() - $diferencia);
//                    echo $ahorro->getValorEnCaja()." ";
//                    die();

//                    echo "<pre>";
//                    echo "pagosListMen ".count($pagosListMen);
//                    var_dump($pagosListMen);
                    $pagosGuardar = array();
                    $saldoDisponible = $pagoAhorro->getCuotaAcumulada();
//                    echo $saldoDisponible;
//                    die();
                    $pagoCuotaAnt = $pagoAhorro;
                    foreach ($pagosList as $cuotaAhorro) {
                        $valorNetoCuota = $cuotaAhorro->getCuota() * $cuotaAhorro->getTipo();
                        if (($valorNetoCuota + $saldoDisponible) < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar hay movimientos posteriores en el ahorro que no se pueden realizar"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $saldoAnterior = $saldoDisponible;
                        $fechaAnterior = $pagoCuotaAnt->getFechaDeEntrada();
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                        $difAno = $cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                        $difMes = (($difAno * 12) + $cuotaAhorro->getFechaDeEntrada()->format(
                                    'm'
                                )) - $fechaAnterior->format('m');
                        if ($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')) {
                            $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                        } else {
                            $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro(
                                    )->getTasaInteres() / 12) * ($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }
                        if ($cuotaAhorro->getTipo() == 1) {
                            $cuotaAhorro->setInteres($interes);
                        }
                        $cuotaAhorro->setCuotaAcumulada(
                            $interes + $saldoAnterior + ($cuotaAhorro->getCuota() * $cuotaAhorro->getTipo())
                        );
                        $pagosGuardar[] = $cuotaAhorro;
                        $saldoDisponible = $cuotaAhorro->getCuotaAcumulada();
                        $pagoCuotaAnt = $cuotaAhorro;
                    }
//                    foreach($pagosGuardar as $pagoGuardar){
//                        echo "<pre>";
//                        echo $pagoGuardar->getFechaDeEntrada()->format("d/m/Y")." ";
//                        echo $pagoGuardar->getTipo()." ";
//                        echo $pagoGuardar->getInteres()." ";
//                        echo $pagoGuardar->getCuota()." ";
//                        echo $pagoGuardar->getCuotaAcumulada();
//                    }
//                    die();
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    foreach ($pagosGuardar as $pagoGuardar) {
                        $em->persist($pagoGuardar);
                    }
                    $em->persist($pagoAhorro);
                    $em->persist($ahorro);

                }
                    break;
                case 13: {//Ahorro Plazo Fijo
//                    echo " ";
//                    echo $infoViejo;
                    $entity->setInfo($infoViejo);
//                    die();
                    //cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $idPagoAhorro = $entity->getInfo();
                    $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($idPagoAhorro);
                    $pagoAhorro->setCuota($entity->getDebe());
                    $ahorro = $pagoAhorro->getIdAhorro();

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findDepositosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $cuotaMenoresList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $depositosList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findRetirosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $pagosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMayoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $pagosListMen = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );


                    $saldoAnterior = 0;
                    $fechaAnterior = new \DateTime();
                    if (count($cuotaMenoresList) > 0) {
                        $saldoAnterior = $cuotaMenoresList[(count($cuotaMenoresList) - 1)]->getCuotaAcumulada();
                    }
                    if (count($depositosList) > 0) {
                        $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                    }
                    $difAno = $pagoAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                    $difMes = (($difAno * 12) + $pagoAhorro->getFechaDeEntrada()->format('m')) - $fechaAnterior->format(
                            'm'
                        );
                    if ($fechaAnterior->format('m-Y') == $pagoAhorro->getFechaDeEntrada()->format('m-Y')) {
                        $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                    } else {
                        $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro()->getTasaInteres(
                                ) / 12) * ($difMes);
                    }

                    $pagoAhorro->setInteres($interes);
                    $pagoAhorro->setCuotaAcumulada($interes + $saldoAnterior + $entity->getDebe());

                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() - $diferencia);
//                    echo $ahorro->getValorEnCaja()." ";
//                    die();

//                    echo "<pre>";
//                    echo "pagosListMen ".count($pagosListMen);
//                    var_dump($pagosListMen);
                    $pagosGuardar = array();
                    $saldoDisponible = $pagoAhorro->getCuotaAcumulada();
//                    echo $saldoDisponible;
//                    die();
                    $pagoCuotaAnt = $pagoAhorro;
                    foreach ($pagosList as $cuotaAhorro) {
                        $valorNetoCuota = $cuotaAhorro->getCuota() * $cuotaAhorro->getTipo();
                        if (($valorNetoCuota + $saldoDisponible) < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar hay movimientos posteriores en el ahorro que no se pueden realizar"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $saldoAnterior = $saldoDisponible;
                        $fechaAnterior = $pagoCuotaAnt->getFechaDeEntrada();
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                        $difAno = $cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                        $difMes = (($difAno * 12) + $cuotaAhorro->getFechaDeEntrada()->format(
                                    'm'
                                )) - $fechaAnterior->format('m');
                        if ($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')) {
                            $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                        } else {
                            $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro(
                                    )->getTasaInteres() / 12) * ($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }
                        if ($cuotaAhorro->getTipo() == 1) {
                            $cuotaAhorro->setInteres($interes);
                        }
                        $cuotaAhorro->setCuotaAcumulada(
                            $interes + $saldoAnterior + ($cuotaAhorro->getCuota() * $cuotaAhorro->getTipo())
                        );
                        $pagosGuardar[] = $cuotaAhorro;
                        $saldoDisponible = $cuotaAhorro->getCuotaAcumulada();
                        $pagoCuotaAnt = $cuotaAhorro;
                    }
//                    foreach($pagosGuardar as $pagoGuardar){
//                        echo "<pre>";
//                        echo $pagoGuardar->getFechaDeEntrada()->format("d/m/Y")." ";
//                        echo $pagoGuardar->getTipo()." ";
//                        echo $pagoGuardar->getInteres()." ";
//                        echo $pagoGuardar->getCuota()." ";
//                        echo $pagoGuardar->getCuotaAcumulada();
//                    }
//                    die();
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    foreach ($pagosGuardar as $pagoGuardar) {
                        $em->persist($pagoGuardar);
                    }
                    $em->persist($pagoAhorro);
                    $em->persist($ahorro);


                }
                    break;
                case 14: {//Ahorro Restringido/Encaje
//                    echo " ";
//                    echo $infoViejo;
                    $entity->setInfo($infoViejo);
//                    die();
                    //cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $idPagoAhorro = $entity->getInfo();
                    $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($idPagoAhorro);
                    $pagoAhorro->setCuota($entity->getDebe());
                    $ahorro = $pagoAhorro->getIdAhorro();

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findDepositosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $cuotaMenoresList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $depositosList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findRetirosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $pagosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMayoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $pagosListMen = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );


                    $saldoAnterior = 0;
                    $fechaAnterior = new \DateTime();
                    if (count($cuotaMenoresList) > 0) {
                        $saldoAnterior = $cuotaMenoresList[(count($cuotaMenoresList) - 1)]->getCuotaAcumulada();
                    }
                    if (count($depositosList) > 0) {
                        $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                    }
                    $difAno = $pagoAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                    $difMes = (($difAno * 12) + $pagoAhorro->getFechaDeEntrada()->format('m')) - $fechaAnterior->format(
                            'm'
                        );
                    if ($fechaAnterior->format('m-Y') == $pagoAhorro->getFechaDeEntrada()->format('m-Y')) {
                        $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                    } else {
                        $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro()->getTasaInteres(
                                ) / 12) * ($difMes);
                    }

                    $pagoAhorro->setInteres($interes);
                    $pagoAhorro->setCuotaAcumulada($interes + $saldoAnterior + $entity->getDebe());

                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() - $diferencia);
//                    echo $ahorro->getValorEnCaja()." ";
//                    die();

//                    echo "<pre>";
//                    echo "pagosListMen ".count($pagosListMen);
//                    var_dump($pagosListMen);
                    $pagosGuardar = array();
                    $saldoDisponible = $pagoAhorro->getCuotaAcumulada();
//                    echo $saldoDisponible;
//                    die();
                    $pagoCuotaAnt = $pagoAhorro;
                    foreach ($pagosList as $cuotaAhorro) {
                        $valorNetoCuota = $cuotaAhorro->getCuota() * $cuotaAhorro->getTipo();
                        if (($valorNetoCuota + $saldoDisponible) < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar hay movimientos posteriores en el ahorro que no se pueden realizar"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $saldoAnterior = $saldoDisponible;
                        $fechaAnterior = $pagoCuotaAnt->getFechaDeEntrada();
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                        $difAno = $cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                        $difMes = (($difAno * 12) + $cuotaAhorro->getFechaDeEntrada()->format(
                                    'm'
                                )) - $fechaAnterior->format('m');
                        if ($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')) {
                            $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                        } else {
                            $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro(
                                    )->getTasaInteres() / 12) * ($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }
                        if ($cuotaAhorro->getTipo() == 1) {
                            $cuotaAhorro->setInteres($interes);
                        }
                        $cuotaAhorro->setCuotaAcumulada(
                            $interes + $saldoAnterior + ($cuotaAhorro->getCuota() * $cuotaAhorro->getTipo())
                        );
                        $pagosGuardar[] = $cuotaAhorro;
                        $saldoDisponible = $cuotaAhorro->getCuotaAcumulada();
                        $pagoCuotaAnt = $cuotaAhorro;
                    }
//                    foreach($pagosGuardar as $pagoGuardar){
//                        echo "<pre>";
//                        echo $pagoGuardar->getFechaDeEntrada()->format("d/m/Y")." ";
//                        echo $pagoGuardar->getTipo()." ";
//                        echo $pagoGuardar->getInteres()." ";
//                        echo $pagoGuardar->getCuota()." ";
//                        echo $pagoGuardar->getCuotaAcumulada();
//                    }
//                    die();
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    foreach ($pagosGuardar as $pagoGuardar) {
                        $em->persist($pagoGuardar);
                    }
                    $em->persist($pagoAhorro);
                    $em->persist($ahorro);

                }
                    break;
                case 15: {//Retiro de Ahorro a la Vista
                    $entity->setInfo($infoViejo);
//                    die();
                    //cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $idPagoAhorro = $entity->getInfo();
                    $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($idPagoAhorro);
                    $pagoAhorro->setCuota($entity->getHaber());
                    $ahorro = $pagoAhorro->getIdAhorro();

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findDepositosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $cuotaMenoresList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $depositosList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findRetirosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $pagosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMayoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $pagosListMen = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );


                    $saldoAnterior = 0;
                    $fechaAnterior = new \DateTime();
                    if (count($cuotaMenoresList) > 0) {
                        $saldoAnterior = $cuotaMenoresList[(count($cuotaMenoresList) - 1)]->getCuotaAcumulada();
                    }
////                    echo $cuotaMenoresList[1]->getCuotaAcumulada();
//                    echo (count($cuotaMenoresList));
//                    die();
                    if (count($depositosList) > 0) {
                        $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                    }
                    $difAno = $pagoAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                    $difMes = (($difAno * 12) + $pagoAhorro->getFechaDeEntrada()->format('m')) - $fechaAnterior->format(
                            'm'
                        );

                    if ($fechaAnterior->format('m-Y') == $pagoAhorro->getFechaDeEntrada()->format('m-Y')) {
                        $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                    } else {
                        $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro()->getTasaInteres(
                                ) / 12) * ($difMes);
                    }

                    $pagoAhorro->setCuotaAcumulada($interes + $saldoAnterior - $entity->getHaber());

                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() - $diferencia);
//                    echo $ahorro->getValorEnCaja()." ";
//                    die();

//                    echo "<pre>";
//                    echo "pagosListMen ".count($pagosListMen);
//                    var_dump($pagosListMen);
                    $pagosGuardar = array();
                    $saldoDisponible = $pagoAhorro->getCuotaAcumulada();
//                    echo $saldoDisponible;
//                    die();
                    $pagoCuotaAnt = $pagoAhorro;
                    foreach ($pagosList as $cuotaAhorro) {
                        $valorNetoCuota = $cuotaAhorro->getCuota() * $cuotaAhorro->getTipo();
                        if (($valorNetoCuota + $saldoDisponible) < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar hay movimientos posteriores en el ahorro que no se pueden realizar"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $saldoAnterior = $saldoDisponible;
                        $fechaAnterior = $pagoCuotaAnt->getFechaDeEntrada();
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                        $difAno = $cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                        $difMes = (($difAno * 12) + $cuotaAhorro->getFechaDeEntrada()->format(
                                    'm'
                                )) - $fechaAnterior->format('m');
                        if ($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')) {
                            $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                        } else {
                            $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro(
                                    )->getTasaInteres() / 12) * ($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }
                        if ($cuotaAhorro->getTipo() == 1) {
                            $cuotaAhorro->setInteres($interes);
                        }
                        $cuotaAhorro->setCuotaAcumulada(
                            $interes + $saldoAnterior + ($cuotaAhorro->getCuota() * $cuotaAhorro->getTipo())
                        );
                        $pagosGuardar[] = $cuotaAhorro;
                        $saldoDisponible = $cuotaAhorro->getCuotaAcumulada();
                        $pagoCuotaAnt = $cuotaAhorro;
                    }
//                    foreach($pagosGuardar as $pagoGuardar){
//                        echo "<pre>";
//                        echo $pagoGuardar->getFechaDeEntrada()->format("d/m/Y")." ";
//                        echo $pagoGuardar->getTipo()." ";
//                        echo $pagoGuardar->getInteres()." ";
//                        echo $pagoGuardar->getCuota()." ";
//                        echo $pagoGuardar->getCuotaAcumulada();
//                    }
//                    die();
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    foreach ($pagosGuardar as $pagoGuardar) {
                        $em->persist($pagoGuardar);
                    }
                    $em->persist($pagoAhorro);
                    $em->persist($ahorro);

                }
                    break;

                case 16: {//Retiro Ahorro Restringido
                    $entity->setInfo($infoViejo);
//                    die();
                    //cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $idPagoAhorro = $entity->getInfo();
                    $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($idPagoAhorro);
                    $pagoAhorro->setCuota($entity->getHaber());
                    $ahorro = $pagoAhorro->getIdAhorro();

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findDepositosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $cuotaMenoresList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $depositosList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findRetirosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $pagosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMayoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $pagosListMen = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );


                    $saldoAnterior = 0;
                    $fechaAnterior = new \DateTime();
                    if (count($cuotaMenoresList) > 0) {
                        $saldoAnterior = $cuotaMenoresList[(count($cuotaMenoresList) - 1)]->getCuotaAcumulada();
                    }
                    if (count($depositosList) > 0) {
                        $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                    }
                    $difAno = $pagoAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                    $difMes = (($difAno * 12) + $pagoAhorro->getFechaDeEntrada()->format('m')) - $fechaAnterior->format(
                            'm'
                        );
                    if ($fechaAnterior->format('m-Y') == $pagoAhorro->getFechaDeEntrada()->format('m-Y')) {
                        $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                    } else {
                        $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro()->getTasaInteres(
                                ) / 12) * ($difMes);
                    }

//                    $pagoAhorro->setCuotaAcumulada($interes + $saldoAnterior + $entity->getDebe());
                    $pagoAhorro->setCuotaAcumulada($interes + $saldoAnterior - $entity->getHaber());

                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() - $diferencia);
//                    echo $ahorro->getValorEnCaja()." ";
//                    die();

//                    echo "<pre>";
//                    echo "pagosListMen ".count($pagosListMen);
//                    var_dump($pagosListMen);
                    $pagosGuardar = array();
                    $saldoDisponible = $pagoAhorro->getCuotaAcumulada();
//                    echo $saldoDisponible;
//                    die();
                    $pagoCuotaAnt = $pagoAhorro;
                    foreach ($pagosList as $cuotaAhorro) {
                        $valorNetoCuota = $cuotaAhorro->getCuota() * $cuotaAhorro->getTipo();
                        if (($valorNetoCuota + $saldoDisponible) < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar hay movimientos posteriores en el ahorro que no se pueden realizar"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $saldoAnterior = $saldoDisponible;
                        $fechaAnterior = $pagoCuotaAnt->getFechaDeEntrada();
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                        $difAno = $cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                        $difMes = (($difAno * 12) + $cuotaAhorro->getFechaDeEntrada()->format(
                                    'm'
                                )) - $fechaAnterior->format('m');
                        if ($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')) {
                            $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                        } else {
                            $interes = ($saldoAnterior + $entity->getHaber()) * ($ahorro->getTipoAhorro(
                                    )->getTasaInteres() / 12) * ($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }
                        if ($cuotaAhorro->getTipo() == 1) {
                            $cuotaAhorro->setInteres($interes);
                        }
                        $cuotaAhorro->setCuotaAcumulada(
                            $interes + $saldoAnterior + ($cuotaAhorro->getCuota() * $cuotaAhorro->getTipo())
                        );
                        $pagosGuardar[] = $cuotaAhorro;
                        $saldoDisponible = $cuotaAhorro->getCuotaAcumulada();
                        $pagoCuotaAnt = $cuotaAhorro;
                    }
//                    foreach($pagosGuardar as $pagoGuardar){
//                        echo "<pre>";
//                        echo $pagoGuardar->getFechaDeEntrada()->format("d/m/Y")." ";
//                        echo $pagoGuardar->getTipo()." ";
//                        echo $pagoGuardar->getInteres()." ";
//                        echo $pagoGuardar->getCuota()." ";
//                        echo $pagoGuardar->getCuotaAcumulada();
//                    }
//                    die();
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    foreach ($pagosGuardar as $pagoGuardar) {
                        $em->persist($pagoGuardar);
                    }
                    $em->persist($pagoAhorro);
                    $em->persist($ahorro);


                }
                    break;

                case 17: {//Retiro de Ahorro Plazo Fijo
//cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $entity->setInfo($infoViejo);
//                    die();
                    //cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $idPagoAhorro = $entity->getInfo();
                    $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($idPagoAhorro);
                    $pagoAhorro->setCuota($entity->getHaber());
                    $ahorro = $pagoAhorro->getIdAhorro();

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findDepositosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $cuotaMenoresList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $depositosList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findRetirosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $pagosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMayoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $pagosListMen = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );


                    $saldoAnterior = 0;
                    $fechaAnterior = new \DateTime();
                    if (count($cuotaMenoresList) > 0) {
                        $saldoAnterior = $cuotaMenoresList[(count($cuotaMenoresList) - 1)]->getCuotaAcumulada();
                    }
                    if (count($depositosList) > 0) {
                        $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                    }
                    $difAno = $pagoAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                    $difMes = (($difAno * 12) + $pagoAhorro->getFechaDeEntrada()->format('m')) - $fechaAnterior->format(
                            'm'
                        );
                    if ($fechaAnterior->format('m-Y') == $pagoAhorro->getFechaDeEntrada()->format('m-Y')) {
                        $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                    } else {
                        $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro()->getTasaInteres(
                                ) / 12) * ($difMes);
                    }

//                    $pagoAhorro->setCuotaAcumulada($interes + $saldoAnterior + $entity->getDebe());
                    $pagoAhorro->setCuotaAcumulada($interes + $saldoAnterior - $entity->getHaber());

                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() - $diferencia);
//                    echo $ahorro->getValorEnCaja()." ";
//                    die();

//                    echo "<pre>";
//                    echo "pagosListMen ".count($pagosListMen);
//                    var_dump($pagosListMen);
                    $pagosGuardar = array();
                    $saldoDisponible = $pagoAhorro->getCuotaAcumulada();
//                    echo $saldoDisponible;
//                    die();
                    $pagoCuotaAnt = $pagoAhorro;
                    foreach ($pagosList as $cuotaAhorro) {
                        $valorNetoCuota = $cuotaAhorro->getCuota() * $cuotaAhorro->getTipo();
                        if (($valorNetoCuota + $saldoDisponible) < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar hay movimientos posteriores en el ahorro que no se pueden realizar"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $saldoAnterior = $saldoDisponible;
                        $fechaAnterior = $pagoCuotaAnt->getFechaDeEntrada();
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                        $difAno = $cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                        $difMes = (($difAno * 12) + $cuotaAhorro->getFechaDeEntrada()->format(
                                    'm'
                                )) - $fechaAnterior->format('m');
                        if ($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')) {
                            $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                        } else {
                            $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro(
                                    )->getTasaInteres() / 12) * ($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }
                        if ($cuotaAhorro->getTipo() == 1) {
                            $cuotaAhorro->setInteres($interes);
                        }
                        $cuotaAhorro->setCuotaAcumulada(
                            $interes + $saldoAnterior + ($cuotaAhorro->getCuota() * $cuotaAhorro->getTipo())
                        );
                        $pagosGuardar[] = $cuotaAhorro;
                        $saldoDisponible = $cuotaAhorro->getCuotaAcumulada();
                        $pagoCuotaAnt = $cuotaAhorro;
                    }
//                    foreach($pagosGuardar as $pagoGuardar){
//                        echo "<pre>";
//                        echo $pagoGuardar->getFechaDeEntrada()->format("d/m/Y")." ";
//                        echo $pagoGuardar->getTipo()." ";
//                        echo $pagoGuardar->getInteres()." ";
//                        echo $pagoGuardar->getCuota()." ";
//                        echo $pagoGuardar->getCuotaAcumulada();
//                    }
//                    die();
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    foreach ($pagosGuardar as $pagoGuardar) {
                        $em->persist($pagoGuardar);
                    }
                    $em->persist($pagoAhorro);
                    $em->persist($ahorro);


                }
                    break;
                case 18: {//Aporte Monetario Inicial
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    $entity->setSaldo($saldoViejo - $diferencia);


                }
                    break;

            }
            foreach ($librosGuardar as $libroGuardar) {
                $em->persist($libroGuardar);
            }

            $vchr = $em->getRepository('ModulosPersonasBundle:VCHR')->findVCHRByLibroId($id);
            $dtvc = $em->getRepository('ModulosPersonasBundle:DTVC')->findAllDTVCByVchr($vchr->getId());

            //$VCHRPagoDeuda = new VCHR();
            $vchr->setFecha($entity->getFecha());
            $vchr->setMes($entity->getFecha()->format('m'));
            $vchr->setLibroId($entity);


            //$DTVCPagoDeuda = new DTVC();

            $dtvc->setCuentaDeudoraId($entity->getProductoContableId()->getCuentaHaber());
            $dtvc->setCuentaAcreedoraId($entity->getProductoContableId()->getCuentaDebe());
            $dtvc->setValor($entity->getDebe() + $entity->getHaber());
            $dtvc->setIdVchr($vchr);
            $dtvc->setEsDebe($entity->getDebe() > 0);


            $em->persist($dtvc);
            $em->persist($vchr);
            $em->persist($entity);
            $em->flush();
            //$this->actualizarSaldo($entity);
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el libro'
            );

            return $this->redirect($this->generateUrl('libro', array('id' => $id)));
        }
//        if($entity->getProductoContableId()->getId()==12 ||$entity->getProductoContableId()->getId()==13 ||$entity->getProductoContableId()->getId()==14
//            ||$entity->getProductoContableId()->getId()==15 ||$entity->getProductoContableId()->getId()==16 ||$entity->getProductoContableId()->getId()==17){
//            $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($entity->getInfo());
//            $entity->setInfo($pagoAhorro->getIdAhorro()->getId());
//        }
        return $this->render(
            'ModulosPersonasBundle:Libro:edit.html.twig',
            array(
                'entity' => $entity,
                'saldoActual' => $saldoActual,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'tiposProductosContables' => $tiposProductosContables,
            )
        );
    }

    /**
     * Deletes a Libro entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:Libro')->find($id);
        try {
            $librosPosteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosMayoresNumeroRecibo(
                $entity->getNumeroRecibo()
            );

            $saldo = $entity->getSaldo();
            $valor = $entity->getHaber() - $entity->getDebe();

            $saldo += $valor;
            $librosGuardar = array();
            switch ($entity->getProductoContableId()->getId()) {
                case 1: {//Aporte Monetario Mensual
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        //$this->actualizarSaldo($entity);
                        $librosGuardar[] = $libroPost;
                    }

                }
                    break;
                case 2: {//Pago Solicitud
                    //chequear q no haya un credito de esa persona q haya consumido esa solicitud
                    if ($entity->getInfo() == 1) {//hay un credito q usa esta solicitud
                        $this->get('session')->getFlashBag()->add(
                            'error',
                            "No se puede eliminar porque hay un crédito que utliliza esta solcitud"
                        );

                        return $this->redirect($this->generateUrl('libro'));
                    } else {
                        foreach ($librosPosteriores as $libroPost) {
                            $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                            if ($saldo < 0) {
                                $this->get('session')->getFlashBag()->add(
                                    'error',
                                    "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                    ).". No se puede realizar la acción"
                                );

                                return $this->redirect($this->generateUrl('libro'));
                            }
                            $libroPost->setSaldo($saldo);
                            //$this->actualizarSaldo($entity);
                            $librosGuardar[] = $libroPost;
                        }
                    }

                }
                    break;
                case 3: {//Pago Desgravamen
                    $idCredito = $entity->getInfo();
                    //busco en el libro si ya esta este credito
                    foreach ($librosPosteriores as $libroPost) {
                        if (($libroPost->getProductoContableId()->getId() == 4 || $libroPost->getProductoContableId(
                                )->getId() == 5) && $libroPost->getInfo() == $idCredito
                        ) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque está asociado al crédito ".$idCredito
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                    }

                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());
                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        //$this->actualizarSaldo($entity);
                        $librosGuardar[] = $libroPost;
                    }

                }
                    break;
                case 4: {//Crédito Otorgado
                    $idCredito = $entity->getInfo();
                    //busco en el libro si ya esta este credito
                    foreach ($librosPosteriores as $libroPost) {
                        if (($libroPost->getProductoContableId()->getId() == 9 || $libroPost->getProductoContableId(
                                )->getId() == 10) && $libroPost->getInfo() == $idCredito
                        ) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque hay pagos asociados al crédito ".$idCredito
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                    }

                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());
                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        //$this->actualizarSaldo($entity);
                        $librosGuardar[] = $libroPost;
                    }
                }
                    break;
                case 5: {//Crédito Emergente
                    $idCredito = $entity->getInfo();
                    //busco en el libro si ya esta este credito
                    foreach ($librosPosteriores as $libroPost) {
                        if (($libroPost->getProductoContableId()->getId() == 9 || $libroPost->getProductoContableId(
                                )->getId() == 10) && $libroPost->getInfo() == $idCredito
                        ) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque hay pagos asociados al crédito ".$idCredito
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                    }

                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());
                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        //$this->actualizarSaldo($entity);
                        $librosGuardar[] = $libroPost;
                    }
                }
                    break;
                case 6: {//Préstamo
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        //$this->actualizarSaldo($entity);
                        $librosGuardar[] = $libroPost;
                    }
                }
                    break;
                case 7: {//Gastos Papelería
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        //$this->actualizarSaldo($entity);
                        $librosGuardar[] = $libroPost;
                    }
                }
                    break;
                case 8: {//Otros Gastos
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        //$this->actualizarSaldo($entity);
                        $librosGuardar[] = $libroPost;
                    }
                }
                    break;
                case 9: {//Pago Crédito


                    $idCredito = $entity->getInfo();
                    $idSolicitud = $entity->getNumeroRecibo() + 1;
                    $solicitud = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosByRecibo($idSolicitud);
                    $saldo = $solicitud->getSaldo();
                    $valor += $solicitud->getHaber() - $solicitud->getDebe();
                    $saldo += $valor;
                    $librosPosteriores = $em->getRepository(
                        'ModulosPersonasBundle:Libro'
                    )->findLibrosMayoresNumeroRecibo($idSolicitud);


                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        //$this->actualizarSaldo($entity);
                        $librosGuardar[] = $libroPost;

                    }
                    $vchrSol = $em->getRepository('ModulosPersonasBundle:VCHR')->findVCHRByLibroId($solicitud->getId());
                    $dtvcSol = $em->getRepository('ModulosPersonasBundle:DTVC')->findAllDTVCByVchr($vchrSol->getId());

                    $em->remove($dtvcSol);
                    $em->remove($vchrSol);
                    $em->remove($solicitud);

                    $pagosCredito = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaCredito'
                    )->findPagosCuotasCreditos($idCredito);
                    $tipoproductocntable = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->find(9);
                    $librospagoCredito = $em->getRepository(
                        'ModulosPersonasBundle:Libro'
                    )->findLibrosByTipoTransaccionAndInfo($tipoproductocntable, $idCredito);
                    $cont = 0;
                    foreach ($librospagoCredito as $libroPagoCred) {
//                        echo "<pre>";
//                        echo "libro pago ".$libroPagoCred->getProductoContableId()->getId();
//                        echo "<pre>";
//                        echo "libro credito ".$libroPagoCred->getInfo();
//                        echo "<pre>";
//                        echo "libro id ".$libroPagoCred->getId();
                        if ($libroPagoCred->getNumeroRecibo() == $entity->getNumeroRecibo()) {
                            $em->remove($pagosCredito[$cont]);
                            break;
                        } else {
                            $cont++;
                        }
                    }

                }
                    break;
                /*case 10:{//Pago Interés
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        "No se puede eliminar el interés intente eliminar el pago de crédito asociado"
                    );
                    return $this->redirect($this->generateUrl('libro'));

                }break;*/
                case 11: {//Multa
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        //$this->actualizarSaldo($entity);
                        $librosGuardar[] = $libroPost;
                    }
                }
                    break;
                case 12: {//Ahorro a Vista
                    //cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $idPagoAhorro = $entity->getInfo();
                    $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($idPagoAhorro);
                    $rebajarAhorro = $pagoAhorro->getCuota() * $pagoAhorro->getTipo();
                    $ahorro = $pagoAhorro->getIdAhorro();
                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() + $rebajarAhorro);

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findDepositosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $depositosList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findRetirosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $pagosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMayoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $pagosListMen = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
//                    echo "<pre>";
//                    echo "pagosListMen ".count($pagosListMen);
//                    var_dump($pagosListMen);
                    $pagosGuardar = array();
                    $saldoDisponible = count($pagosListMen) > 0 ? $pagosListMen[count(
                        $pagosListMen
                    ) - 1]->getCuotaAcumulada() : 0;
//                    echo "<pre>";
//                    echo "saldoDisponible ".$saldoDisponible;
                    foreach ($pagosList as $cuotaAhorro) {
                        $valorNetoCuota = $cuotaAhorro->getCuota() * $cuotaAhorro->getTipo();
//                        echo "<pre>";
//                        echo "valor neto ".$valorNetoCuota;
                        if (($valorNetoCuota + $saldoDisponible) < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar hay movimientos posteriores en el ahorro que no se pueden realizar"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $saldoAnterior = 0;
                        $fechaAnterior = new \DateTime();
                        if (count($cuotaAhorroList) > 0) {
                            $saldoAnterior = $cuotaAhorroList[count($cuotaAhorroList) - 1]->getCuotaAcumulada();
                        }
                        if (count($depositosList) > 0) {
                            $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                        }
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                        $cuotaAhorro->setFechaDeEntrada($entity->getFecha());
                        //si son del mismo año
//                $difAno=$datetime2->format('Y') - $datetime1->format('Y');
//                $difAno=(($difAno*12)+$datetime2->format('m')) - $datetime1->format('m');
                        $difAno = $cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                        $difMes = (($difAno * 12) + $cuotaAhorro->getFechaDeEntrada()->format(
                                    'm'
                                )) - $fechaAnterior->format('m');
                        if ($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')) {
                            $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                        } else {
                            $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro(
                                    )->getTasaInteres() / 12) * ($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }

                        $cuotaAhorro->setInteres($interes);
                        $cuotaAhorro->setCuotaAcumulada($interes + $saldoAnterior + $cuotaAhorro->getCuota());
                        $pagosGuardar[] = $cuotaAhorro;
                        $saldoDisponible = $cuotaAhorro->getCuotaAcumulada();
                    }
//                    foreach($pagosGuardar as $pagoGuardar){
//                        echo "<pre>";
//                        echo $pagoGuardar->getFechaDeEntrada()->format("d/m/Y")." ";
//                        echo $pagoGuardar->getTipo()." ";
//                        echo $pagoGuardar->getInteres()." ";
//                        echo $pagoGuardar->getCuota()." ";
//                        echo $pagoGuardar->getCuotaAcumulada();
//                    }
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    foreach ($pagosGuardar as $pagoGuardar) {
                        $em->persist($pagoGuardar);
                    }
                    $em->remove($pagoAhorro);
                    $em->persist($ahorro);

                }
                    break;
                case 13: {//Ahorro Plazo Fijo
//cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $idPagoAhorro = $entity->getInfo();
                    $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($idPagoAhorro);
                    $rebajarAhorro = $pagoAhorro->getCuota() * $pagoAhorro->getTipo();
                    $ahorro = $pagoAhorro->getIdAhorro();
                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() + $rebajarAhorro);

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findDepositosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $depositosList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findRetirosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $pagosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMayoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $pagosListMen = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
//                    echo "<pre>";
//                    echo "pagosListMen ".count($pagosListMen);
//                    var_dump($pagosListMen);
                    $pagosGuardar = array();
                    $saldoDisponible = count($pagosListMen) > 0 ? $pagosListMen[count(
                        $pagosListMen
                    ) - 1]->getCuotaAcumulada() : 0;
//                    echo "<pre>";
//                    echo "saldoDisponible ".$saldoDisponible;
                    foreach ($pagosList as $cuotaAhorro) {
                        $valorNetoCuota = $cuotaAhorro->getCuota() * $cuotaAhorro->getTipo();
//                        echo "<pre>";
//                        echo "valor neto ".$valorNetoCuota;
                        if (($valorNetoCuota + $saldoDisponible) < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar hay movimientos posteriores en el ahorro que no se pueden realizar"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $saldoAnterior = 0;
                        $fechaAnterior = new \DateTime();
                        if (count($cuotaAhorroList) > 0) {
                            $saldoAnterior = $cuotaAhorroList[count($cuotaAhorroList) - 1]->getCuotaAcumulada();
                        }
                        if (count($depositosList) > 0) {
                            $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                        }
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                        $cuotaAhorro->setFechaDeEntrada($entity->getFecha());
                        //si son del mismo año
//                $difAno=$datetime2->format('Y') - $datetime1->format('Y');
//                $difAno=(($difAno*12)+$datetime2->format('m')) - $datetime1->format('m');
                        $difAno = $cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                        $difMes = (($difAno * 12) + $cuotaAhorro->getFechaDeEntrada()->format(
                                    'm'
                                )) - $fechaAnterior->format('m');
                        if ($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')) {
                            $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                        } else {
                            $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro(
                                    )->getTasaInteres() / 12) * ($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }

                        $cuotaAhorro->setInteres($interes);
                        $cuotaAhorro->setCuotaAcumulada($interes + $saldoAnterior + $cuotaAhorro->getCuota());
                        $pagosGuardar[] = $cuotaAhorro;
                        $saldoDisponible = $cuotaAhorro->getCuotaAcumulada();
                    }
//                    foreach($pagosGuardar as $pagoGuardar){
//                        echo "<pre>";
//                        echo $pagoGuardar->getFechaDeEntrada()->format("d/m/Y")." ";
//                        echo $pagoGuardar->getTipo()." ";
//                        echo $pagoGuardar->getInteres()." ";
//                        echo $pagoGuardar->getCuota()." ";
//                        echo $pagoGuardar->getCuotaAcumulada();
//                    }
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    foreach ($pagosGuardar as $pagoGuardar) {
                        $em->persist($pagoGuardar);
                    }
                    $em->persist($ahorro);

                }
                    break;
                case 14: {//Ahorro Restringido/Encaje
//cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $idPagoAhorro = $entity->getInfo();
                    $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($idPagoAhorro);
                    $rebajarAhorro = $pagoAhorro->getCuota() * $pagoAhorro->getTipo();
                    $ahorro = $pagoAhorro->getIdAhorro();
                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() + $rebajarAhorro);

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findDepositosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $depositosList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findRetirosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $pagosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMayoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $pagosListMen = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
//                    echo "<pre>";
//                    echo "pagosListMen ".count($pagosListMen);
//                    var_dump($pagosListMen);
                    $pagosGuardar = array();
                    $saldoDisponible = count($pagosListMen) > 0 ? $pagosListMen[count(
                        $pagosListMen
                    ) - 1]->getCuotaAcumulada() : 0;
//                    echo "<pre>";
//                    echo "saldoDisponible ".$saldoDisponible;
                    foreach ($pagosList as $cuotaAhorro) {
                        $valorNetoCuota = $cuotaAhorro->getCuota() * $cuotaAhorro->getTipo();
//                        echo "<pre>";
//                        echo "valor neto ".$valorNetoCuota;
                        if (($valorNetoCuota + $saldoDisponible) < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar hay movimientos posteriores en el ahorro que no se pueden realizar"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $saldoAnterior = 0;
                        $fechaAnterior = new \DateTime();
                        if (count($cuotaAhorroList) > 0) {
                            $saldoAnterior = $cuotaAhorroList[count($cuotaAhorroList) - 1]->getCuotaAcumulada();
                        }
                        if (count($depositosList) > 0) {
                            $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                        }
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                        $cuotaAhorro->setFechaDeEntrada($entity->getFecha());
                        //si son del mismo año
//                $difAno=$datetime2->format('Y') - $datetime1->format('Y');
//                $difAno=(($difAno*12)+$datetime2->format('m')) - $datetime1->format('m');
                        $difAno = $cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                        $difMes = (($difAno * 12) + $cuotaAhorro->getFechaDeEntrada()->format(
                                    'm'
                                )) - $fechaAnterior->format('m');
                        if ($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')) {
                            $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                        } else {
                            $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro(
                                    )->getTasaInteres() / 12) * ($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }

                        $cuotaAhorro->setInteres($interes);
                        $cuotaAhorro->setCuotaAcumulada($interes + $saldoAnterior + $cuotaAhorro->getCuota());
                        $pagosGuardar[] = $cuotaAhorro;
                        $saldoDisponible = $cuotaAhorro->getCuotaAcumulada();
                    }
//                    foreach($pagosGuardar as $pagoGuardar){
//                        echo "<pre>";
//                        echo $pagoGuardar->getFechaDeEntrada()->format("d/m/Y")." ";
//                        echo $pagoGuardar->getTipo()." ";
//                        echo $pagoGuardar->getInteres()." ";
//                        echo $pagoGuardar->getCuota()." ";
//                        echo $pagoGuardar->getCuotaAcumulada();
//                    }
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    foreach ($pagosGuardar as $pagoGuardar) {
                        $em->persist($pagoGuardar);
                    }
                    $em->persist($ahorro);

                }
                    break;
                case 15: {//Retiro de Ahorro a la Vista
//cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $idPagoAhorro = $entity->getInfo();
                    $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($idPagoAhorro);
                    $rebajarAhorro = $pagoAhorro->getCuota() * $pagoAhorro->getTipo();
                    $ahorro = $pagoAhorro->getIdAhorro();
                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() + $rebajarAhorro);

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findDepositosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $depositosList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findRetirosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $pagosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMayoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $pagosListMen = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
//                    echo "<pre>";
//                    echo "pagosListMen ".count($pagosListMen);
//                    var_dump($pagosListMen);
                    $pagosGuardar = array();
                    $saldoDisponible = count($pagosListMen) > 0 ? $pagosListMen[count(
                        $pagosListMen
                    ) - 1]->getCuotaAcumulada() : 0;
//                    echo "<pre>";
//                    echo "saldoDisponible ".$saldoDisponible;
                    foreach ($pagosList as $cuotaAhorro) {
                        $valorNetoCuota = $cuotaAhorro->getCuota() * $cuotaAhorro->getTipo();
//                        echo "<pre>";
//                        echo "valor neto ".$valorNetoCuota;
                        if (($valorNetoCuota + $saldoDisponible) < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar hay movimientos posteriores en el ahorro que no se pueden realizar"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $saldoAnterior = 0;
                        $fechaAnterior = new \DateTime();
                        if (count($cuotaAhorroList) > 0) {
                            $saldoAnterior = $cuotaAhorroList[count($cuotaAhorroList) - 1]->getCuotaAcumulada();
                        }
                        if (count($depositosList) > 0) {
                            $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                        }
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                        $cuotaAhorro->setFechaDeEntrada($entity->getFecha());
                        //si son del mismo año
//                $difAno=$datetime2->format('Y') - $datetime1->format('Y');
//                $difAno=(($difAno*12)+$datetime2->format('m')) - $datetime1->format('m');
                        $difAno = $cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                        $difMes = (($difAno * 12) + $cuotaAhorro->getFechaDeEntrada()->format(
                                    'm'
                                )) - $fechaAnterior->format('m');
                        if ($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')) {
                            $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                        } else {
                            $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro(
                                    )->getTasaInteres() / 12) * ($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }

                        $cuotaAhorro->setInteres($interes);
                        $cuotaAhorro->setCuotaAcumulada($interes + $saldoAnterior + $cuotaAhorro->getCuota());
                        $pagosGuardar[] = $cuotaAhorro;
                        $saldoDisponible = $cuotaAhorro->getCuotaAcumulada();
                    }
//                    foreach($pagosGuardar as $pagoGuardar){
//                        echo "<pre>";
//                        echo $pagoGuardar->getFechaDeEntrada()->format("d/m/Y")." ";
//                        echo $pagoGuardar->getTipo()." ";
//                        echo $pagoGuardar->getInteres()." ";
//                        echo $pagoGuardar->getCuota()." ";
//                        echo $pagoGuardar->getCuotaAcumulada();
//                    }
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    foreach ($pagosGuardar as $pagoGuardar) {
                        $em->persist($pagoGuardar);
                    }
                    $em->persist($ahorro);

                }
                    break;

                case 16: {//Retiro Ahorro Restringido
//cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $idPagoAhorro = $entity->getInfo();
                    $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($idPagoAhorro);
                    $rebajarAhorro = $pagoAhorro->getCuota() * $pagoAhorro->getTipo();
                    $ahorro = $pagoAhorro->getIdAhorro();
                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() + $rebajarAhorro);

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findDepositosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $depositosList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findRetirosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $pagosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMayoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $pagosListMen = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
//                    echo "<pre>";
//                    echo "pagosListMen ".count($pagosListMen);
//                    var_dump($pagosListMen);
                    $pagosGuardar = array();
                    $saldoDisponible = count($pagosListMen) > 0 ? $pagosListMen[count(
                        $pagosListMen
                    ) - 1]->getCuotaAcumulada() : 0;
//                    echo "<pre>";
//                    echo "saldoDisponible ".$saldoDisponible;
                    foreach ($pagosList as $cuotaAhorro) {
                        $valorNetoCuota = $cuotaAhorro->getCuota() * $cuotaAhorro->getTipo();
//                        echo "<pre>";
//                        echo "valor neto ".$valorNetoCuota;
                        if (($valorNetoCuota + $saldoDisponible) < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar hay movimientos posteriores en el ahorro que no se pueden realizar"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $saldoAnterior = 0;
                        $fechaAnterior = new \DateTime();
                        if (count($cuotaAhorroList) > 0) {
                            $saldoAnterior = $cuotaAhorroList[count($cuotaAhorroList) - 1]->getCuotaAcumulada();
                        }
                        if (count($depositosList) > 0) {
                            $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                        }
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                        $cuotaAhorro->setFechaDeEntrada($entity->getFecha());
                        //si son del mismo año
//                $difAno=$datetime2->format('Y') - $datetime1->format('Y');
//                $difAno=(($difAno*12)+$datetime2->format('m')) - $datetime1->format('m');
                        $difAno = $cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                        $difMes = (($difAno * 12) + $cuotaAhorro->getFechaDeEntrada()->format(
                                    'm'
                                )) - $fechaAnterior->format('m');
                        if ($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')) {
                            $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                        } else {
                            $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro(
                                    )->getTasaInteres() / 12) * ($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }

                        $cuotaAhorro->setInteres($interes);
                        $cuotaAhorro->setCuotaAcumulada($interes + $saldoAnterior + $cuotaAhorro->getCuota());
                        $pagosGuardar[] = $cuotaAhorro;
                        $saldoDisponible = $cuotaAhorro->getCuotaAcumulada();
                    }
//                    foreach($pagosGuardar as $pagoGuardar){
//                        echo "<pre>";
//                        echo $pagoGuardar->getFechaDeEntrada()->format("d/m/Y")." ";
//                        echo $pagoGuardar->getTipo()." ";
//                        echo $pagoGuardar->getInteres()." ";
//                        echo $pagoGuardar->getCuota()." ";
//                        echo $pagoGuardar->getCuotaAcumulada();
//                    }
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    foreach ($pagosGuardar as $pagoGuardar) {
                        $em->persist($pagoGuardar);
                    }
                    $em->persist($ahorro);

                }
                    break;

                case 17: {//Retiro de Ahorro Plazo Fijo
//cuando se elimine el ahorro hay q actualizar la tabla pago ahorro y la entidad ahorro
                    $idPagoAhorro = $entity->getInfo();
                    $pagoAhorro = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->find($idPagoAhorro);
                    $rebajarAhorro = $pagoAhorro->getCuota() * $pagoAhorro->getTipo();
                    $ahorro = $pagoAhorro->getIdAhorro();
                    $ahorro->setValorEnCaja($ahorro->getValorEnCaja() + $rebajarAhorro);

                    $cuotaAhorroList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findDepositosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $depositosList = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaAhorro'
                    )->findRetirosAhorrosMenoresQ($ahorro->getId(), $idPagoAhorro);
                    $pagosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMayoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
                    $pagosListMen = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findMenoresQ(
                        $ahorro->getId(),
                        $idPagoAhorro
                    );
//                    echo "<pre>";
//                    echo "pagosListMen ".count($pagosListMen);
//                    var_dump($pagosListMen);
                    $pagosGuardar = array();
                    $saldoDisponible = count($pagosListMen) > 0 ? $pagosListMen[count(
                        $pagosListMen
                    ) - 1]->getCuotaAcumulada() : 0;
//                    echo "<pre>";
//                    echo "saldoDisponible ".$saldoDisponible;
                    foreach ($pagosList as $cuotaAhorro) {
                        $valorNetoCuota = $cuotaAhorro->getCuota() * $cuotaAhorro->getTipo();
//                        echo "<pre>";
//                        echo "valor neto ".$valorNetoCuota;
                        if (($valorNetoCuota + $saldoDisponible) < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar hay movimientos posteriores en el ahorro que no se pueden realizar"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $saldoAnterior = 0;
                        $fechaAnterior = new \DateTime();
                        if (count($cuotaAhorroList) > 0) {
                            $saldoAnterior = $cuotaAhorroList[count($cuotaAhorroList) - 1]->getCuotaAcumulada();
                        }
                        if (count($depositosList) > 0) {
                            $fechaAnterior = $depositosList[count($depositosList) - 1]->getFechaDeEntrada();
                        }
//                $inicio="2015-1-31 00:00:00";
//                $fin="2015-02-01 23:59:59";
////                $datetime1=$fechaAnterior;
//                $datetime1=new \DateTime($inicio);
////                $datetime2=$entity->getFecha();
//                $datetime2=new \DateTime($fin);
//                # obtenemos la diferencia entre las dos fechas
//                $interval=$datetime2->diff($datetime1);
//                # obtenemos la diferencia en meses
//                $intervalMeses=$interval->format("%m");
//                # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
//                $intervalAnos = $interval->format("%y")*12;
//                $diferenciaMeses=$intervalMeses+$intervalAnos;
                        $cuotaAhorro->setFechaDeEntrada($entity->getFecha());
                        //si son del mismo año
//                $difAno=$datetime2->format('Y') - $datetime1->format('Y');
//                $difAno=(($difAno*12)+$datetime2->format('m')) - $datetime1->format('m');
                        $difAno = $cuotaAhorro->getFechaDeEntrada()->format('Y') - $fechaAnterior->format('Y');
                        $difMes = (($difAno * 12) + $cuotaAhorro->getFechaDeEntrada()->format(
                                    'm'
                                )) - $fechaAnterior->format('m');
                        if ($fechaAnterior->format('m-Y') == $cuotaAhorro->getFechaDeEntrada()->format('m-Y')) {
                            $interes = $entity->getDebe() * ($ahorro->getTipoAhorro()->getTasaInteres() / 12);
                        } else {
                            $interes = ($saldoAnterior + $entity->getDebe()) * ($ahorro->getTipoAhorro(
                                    )->getTasaInteres() / 12) * ($difMes);
//                    echo ($saldoAnterior + $entity->getDebe())." ".($ahorro->getTipoAhorro()->getTasaInteres()/12)."  ".($difMes);
//                    die();
                        }

                        $cuotaAhorro->setInteres($interes);
                        $cuotaAhorro->setCuotaAcumulada($interes + $saldoAnterior + $cuotaAhorro->getCuota());
                        $pagosGuardar[] = $cuotaAhorro;
                        $saldoDisponible = $cuotaAhorro->getCuotaAcumulada();
                    }
//                    foreach($pagosGuardar as $pagoGuardar){
//                        echo "<pre>";
//                        echo $pagoGuardar->getFechaDeEntrada()->format("d/m/Y")." ";
//                        echo $pagoGuardar->getTipo()." ";
//                        echo $pagoGuardar->getInteres()." ";
//                        echo $pagoGuardar->getCuota()." ";
//                        echo $pagoGuardar->getCuotaAcumulada();
//                    }
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }
                    foreach ($pagosGuardar as $pagoGuardar) {
                        $em->persist($pagoGuardar);
                    }
                    $em->persist($ahorro);

                }
                    break;
                case 18: {//Aporte Monetario Inicial
                    //chequear q luego no se quede sin dinero la caja
                    foreach ($librosPosteriores as $libroPost) {
                        $saldo += ($libroPost->getDebe() - $libroPost->getHaber());

                        if ($saldo < 0) {
                            $this->get('session')->getFlashBag()->add(
                                'error',
                                "No se puede eliminar porque se tiene que realizar la transaccion ".$libroPost->getNumeroRecibo(
                                ).". No se puede realizar la acción"
                            );

                            return $this->redirect($this->generateUrl('libro'));
                        }
                        $libroPost->setSaldo($saldo);
                        $librosGuardar[] = $libroPost;
                    }

                }
                    break;

            }
            foreach ($librosGuardar as $libroGuardar) {
                $em->persist($libroGuardar);
            }
            $vchr = $em->getRepository('ModulosPersonasBundle:VCHR')->findVCHRByLibroId($id);
            $dtvc = $em->getRepository('ModulosPersonasBundle:DTVC')->findAllDTVCByVchr($vchr->getId());

            // ACTUALIZAR SALDO
            $fechaIngreso = $entity->getFecha();

            $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

            if (count($libros) > 0) {
                $fechaInicial = $libros[count($libros) - 1]->getFecha();
            }

            $librosAnteriores = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaDescId(
                $fechaInicial,
                $fechaIngreso
            );

            $em->remove($dtvc);
            $em->remove($vchr);
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el libro'
            );

            if (count($librosAnteriores) != 1 && count($librosAnteriores) > 0) {
                //$saldoAnterior=$librosAnteriores[1]->getSaldo();
                //$entity->setSaldo($saldoAnterior + $debe- $haber);
                //$this->actualizarSaldo($librosAnteriores[1]);
            }
            //$this->actualizarSaldo();
        } catch (\Doctrine\DBAL\DBALException $e) {
            if ($e->getCode() == 0) {
                if ($e->getPrevious()->getCode() == 23000) {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        "No se puede eliminar porque tiene registros relacionados."
                    );

                    return $this->redirect($this->generateUrl('libro'));
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }

        return $this->redirect($this->generateUrl('libro'));
    }

    /**
     * Creates a form to delete a Libro entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private
    function createDeleteForm(
        $id
    ) {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('libro_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function exportarLibroAction()
    {
        $array = array();
        $em = $this->getDoctrine()->getManager();
        $cajahorro = $em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja = $cajahorro->getRazonSocial();//'nombrecaja'=>$nombrecaja,

        $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosASCEND();
        if (count($librosOrdenados) > 0) {
            $aux = $librosOrdenados[0]->getFecha()->format('Y-m');
            $array [] = $aux;
            for ($i = 1; $i < count($librosOrdenados); $i++) {
                if ($librosOrdenados[$i]->getFecha()->format('Y-m') != $aux) {
                    $aux = $librosOrdenados[$i]->getFecha()->format('Y-m');
                    $array [] = $aux;
                }
            }
//            echo var_dump($array);
//            die();

        }
        $mesesMap = [
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
//        $cantDias=$fecha1->format('t');
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Giulio De Donato")
            ->setTitle("Libro de Caja")
            ->setSubject("Libro de Caja")
            ->setDescription("Libro de Caja")
            ->setKeywords("Libro de Caja")
            ->setCategory("Reporte excel");

        //$tituloReporte1 = "Listado de libros de cajas por meses de:".$fecha1->format('d-m-Y').' a '.$fecha2->format('d-m-Y');
        $tituloReporte = "LIBRO DIARIO DE CAJA";
        $tituloReporte1 = $nombrecaja;

        //  $phpExcelObject->setActiveSheetIndex(0)
        //   ->setCellValue('A1:H1',"Libro de caja");

        $estiloDivisor = array(

            'borders' => array(
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),

            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloTituloCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 12,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
        );
        $estiloSubCabCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 12,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );
        $estiloTituloColumnasCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'size' => 9,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloCeldaCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => false,
                'size' => 11,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloCeldaDerecha = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloCeldaIzquierda = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', $tituloReporte);
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A2', $tituloReporte1);


        $titulosColumnas = array(
            'FECHA',
            'TIPO TRANSACCIÓN',
            'PERSONA',
            'CUENTA CONTABLE',
            'CÓDIGO',
            'DEBE',
            'HABER',
            'SALDO EN CAJA',
        );
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A1:H1');
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A2:H2');

        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('B4:C4');

        $i = 4;
        foreach ($array as $a) {
            $fechaArray = explode('-', $a);
            $date = new \DateTime();
            $date->setDate($fechaArray[0], $fechaArray[1], 1);
            $cantDias = $date->format('t');
            $tituloReporte2 = "1 al ".$cantDias." de ".$mesesMap[$fechaArray[1]]." de ".$fechaArray[0];

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $tituloReporte2);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($estiloTituloCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':H'.$i);
            $i++;
            // Se agregan los titulos del reporte
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "")
                ->setCellValue('B'.$i, "DETALLES")
                ->setCellValue('F'.$i, "INGRESO")
                ->setCellValue('G'.$i, "EGRESO");

            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':C'.$i);
            $phpExcelObject->setActiveSheetIndex(0)->getStyle('B'.$i.':C'.$i)->applyFromArray(
                $estiloTituloColumnasCartera
            );
            $phpExcelObject->setActiveSheetIndex(0)->getStyle('F'.$i)->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->setActiveSheetIndex(0)->getStyle('G'.$i)->applyFromArray($estiloTituloColumnasCartera);

            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray(
                $estiloTituloColumnasCartera
            );
            $i++;
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $titulosColumnas[0]);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloTituloColumnasCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, $titulosColumnas[1])->getStyle('B'.$i)->applyFromArray(
                    $estiloTituloColumnasCartera
                );
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, $titulosColumnas[2])->getStyle('C'.$i)->applyFromArray(
                    $estiloTituloColumnasCartera
                );
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D'.$i, $titulosColumnas[3])->getStyle('D'.$i)->applyFromArray(
                    $estiloTituloColumnasCartera
                );
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E'.$i, $titulosColumnas[4])->getStyle('E'.$i)->applyFromArray(
                    $estiloTituloColumnasCartera
                );
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, $titulosColumnas[5])->getStyle('F'.$i)->applyFromArray(
                    $estiloTituloColumnasCartera
                );
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, $titulosColumnas[6])->getStyle('G'.$i)->applyFromArray(
                    $estiloTituloColumnasCartera
                );
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('H'.$i, $titulosColumnas[7])->getStyle('H'.$i)->applyFromArray(
                    $estiloTituloColumnasCartera
                );

            $i++;
            $posMes = $i;
            foreach ($librosOrdenados as $libro) {

                if ($libro->getFecha()->format('Y-m') == $a) {
                    $fecha = "";
                    $productocontable = "";
                    $persona = "";
                    $cuenta = "";
                    $numeroRecibo = "";
                    $debe = "";
                    $haber = "";
                    $saldo = "";

                    if ($libro->getFecha() != null) {
                        $fecha = $libro->getFecha()->format('Y-m-d H:i');

                    }
                    if ($libro->getProductoContableId() != null) {
                        $productocontable = $libro->getProductoContableId()->getTipo();
                    }
                    if ($libro->getPersona() != null) {
                        $persona = $libro->getpersona()->getNombre().' '.$libro->getPersona()->getPrimerApellido(
                            ).' '.$libro->getPersona()->getSegundoApellido();
                    }
                    if ($libro->getCuentaid() != null) {
                        $cuenta = $libro->getCuentaid()->getCodigo();
                    }
                    if ($libro->getNumeroRecibo() != null) {
                        $numeroRecibo = $libro->getNumeroRecibo();

                    }
                    if
                    ($libro->getDebe() != null
                    ) {
                        $debe = $libro->getDebe();
                    }
                    if
                    ($libro->getHaber() != null
                    ) {
                        $haber = $libro->getHaber();
                    }
                    if
                    ($libro->getSaldo() != null
                    ) {
                        $saldo = $libro->getSaldo();
                    }

                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, $fecha)->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);

                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('B'.$i, $productocontable)->getStyle('B'.$i)->applyFromArray(
                            $estiloCeldaCartera
                        );
                    $phpExcelObject->setActiveSheetIndex(0)->getStyle('B'.$i)->applyFromArray(
                        $estiloCeldaIzquierda
                    );

                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('C'.$i, $persona)->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
                    $phpExcelObject->setActiveSheetIndex(0)->getStyle('C'.$i)->applyFromArray(
                        $estiloCeldaIzquierda
                    );

                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('D'.$i, $cuenta);
                    $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);

                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('E'.$i, $numeroRecibo);
                    $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);

                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('F'.$i, '$ '.round($debe, 2))->getStyle('F'.$i)->applyFromArray(
                            $estiloCeldaCartera
                        );
                    $phpExcelObject->setActiveSheetIndex(0)->getStyle('F'.$i)->applyFromArray($estiloCeldaDerecha);

                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('G'.$i, '$ '.round($haber, 2))->getStyle('G'.$i)->applyFromArray(
                            $estiloCeldaCartera
                        );
                    $phpExcelObject->setActiveSheetIndex(0)->getStyle('G'.$i)->applyFromArray($estiloCeldaDerecha);

                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('H'.$i, '$ '.round($saldo, 2))->getStyle('H'.$i)->applyFromArray(
                            $estiloCeldaCartera
                        );
                    $phpExcelObject->setActiveSheetIndex(0)->getStyle('H'.$i)->applyFromArray($estiloCeldaDerecha);


                    $i++;
                }

            }


        }


        $phpExcelObject->getActiveSheet()->getStyle('A1:H1')->applyFromArray($estiloTituloCartera);
        $phpExcelObject->getActiveSheet()->getStyle('A2:H2')->applyFromArray($estiloTituloCartera);
        //$phpExcelObject->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:W".($i-1));
        for ($i = 'A'; $i <= 'H'; $i++) {
            $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension($i)->setAutoSize(true);
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
            'Libro de caja.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /*public function carteraAction()
    {
        $carteraMeses = array();
        $totalesmeses = array();
        $em = $this->getDoctrine()->getManager();

        $cajahorro=$em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $personas=$em->getRepository('ModulosPersonasBundle:Persona')->findAll();

        $nombrecaja=$cajahorro->getRazonSocial();//'nombrecaja'=>$nombrecaja,

        $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosSinFechaTipoCartera();
        //$librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findAll();
        if (count($librosOrdenados) > 0) {

            $mes = $librosOrdenados[0]->getFecha()->format('Y-m');
            $carteraMes = new CarteraMes();
            $carteraMes->setMes($mes);

            $arrayValoresMes = array();
            $arrayMeses = array();

            for ($i = 0; $i < count($librosOrdenados); $i++) {
                if ($librosOrdenados[$i]->getFecha()->format('Y-m') != $mes) {
                    $arrayMeses[] = array($mes, $arrayValoresMes);
                    $mes = $librosOrdenados[$i]->getFecha()->format('Y-m');
                    $arrayValoresMes = array();
                    $arrayValoresMes[] = $librosOrdenados[$i];
                } else {
                    $arrayValoresMes[] = $librosOrdenados[$i];
                }

            }
            $arrayMeses[] = array($mes, $arrayValoresMes);

            //ordenar por persona
            for ($i = 0; $i < count($arrayMeses); $i++) {
                //*
               $arregloOrdenado = $em->getRepository(
                    'ModulosPersonasBundle:Libro'
                )->findLibrosOrdenadosPorPersonaTipoCartera(
                    $arrayMeses[$i][1][0]->getFecha(),
                    end($arrayMeses[$i][1])->getFecha()
                );

                $arrayMeses[$i][1] = $arregloOrdenado;
            }

            $cartera = new Cartera();
            $carteraMeses = array();
            for ($i = 0; $i < count($arrayMeses); $i++) {

                if($arrayMeses[$i][1]) {
                    $carteraMes = new CarteraMes();
                    $carteraMes->setMes($arrayMeses[$i][0]);
                    $personasMes = array();
                    $persona = $arrayMeses[$i][1][0]->getPersona();
                    $carteraPersonaMes = new CarteraPersonaMes();
                    $carteraPersonaMes->setPersona($persona);
                    $carteraPersonaMes->setCuenta($arrayMeses[$i][1][0]->getCuentaid());
                    $carteraPersonaMes->setHombre(
                        $arrayMeses[$i][1][0]->getPersona()->getGenero()->getGenero() == "MASCULINO" ? 1 : 0
                    );
                    $carteraPersonaMes->setMujer(
                        $arrayMeses[$i][1][0]->getPersona()->getGenero()->getGenero() == "FEMENINO" ? 1 : 0
                    );

                    for ($j = 0; $j < count($arrayMeses[$i][1]); $j++) {

                        $libro = $arrayMeses[$i][1][$j];
                        if ($persona->getId() == $libro->getPersona()->getId()) {
                            if ($libro->getProductoContableId()->getId() == 9) {//Pago crédito
                                $carteraPersonaMes->setCapitalPagado(
                                    $carteraPersonaMes->getCapitalPagado() + $libro->getDebe()
                                );
                            } else {
                                if ($libro->getProductoContableId()->getId() == 10) {//Pago Interés
                                    $carteraPersonaMes->setInteresGanado(
                                        $carteraPersonaMes->getInteresGanado() + $libro->getDebe()
                                    );
                                } else {
                                    if ($libro->getProductoContableId()->getId() == 3) {//Pago Desgravamen
                                        $carteraPersonaMes->setPagoDegravamen(
                                            $carteraPersonaMes->getPagoDegravamen() + $libro->getDebe()
                                        );
                                    } else {
                                        if ($libro->getProductoContableId()->getId(
                                            ) == 4 || $libro->getProductoContableId()->getId() == 5
                                        ) {//Credito Otorgado o Credito Emergente
                                            $carteraPersonaMes->setCreditoMes(
                                                $carteraPersonaMes->getCreditoMes() + $libro->getHaber()
                                            );
                                            $carteraPersonaMes->setCreditoCant(
                                                $carteraPersonaMes->getCreditoCant() + 1
                                            );
                                        }
                                    }
                                }
                            }

                        } else {
//                        echo "<pre>";
//                        print_r($cartera->getSaldoAnterior($carteraMeses, $carteraMes, $carteraPersonaMes->getPersona()));
                            $carteraPersonaMes->setSaldoAnterior(
                                $cartera->getSaldoAnterior($carteraMeses, $carteraMes, $carteraPersonaMes->getPersona())
                            );
                            $carteraPersonaMes->setCreditoCant(
                                $cartera->getNumeroCreditos(
                                    $carteraMeses,
                                    $carteraMes,
                                    $carteraPersonaMes->getPersona()
                                ) + $carteraPersonaMes->getCreditoCant()
                            );

                            //saldo_anterior - capital_pagado + creditos_del_mes

                            $carteraPersonaMes->setCreditoMicroEmpPorVencerSaldoCap(
                                $carteraPersonaMes->getSaldoAnterior() - $carteraPersonaMes->getCapitalPagado(
                                ) + $carteraPersonaMes->getCreditoMes()
                            );


                            $carteraPersonaMes->updateTotalPagado();

                            $personasMes[] = $carteraPersonaMes;

                            $persona = $libro->getPersona();

                            $carteraPersonaMes = new CarteraPersonaMes();
                            $carteraPersonaMes->setPersona($libro->getPersona());
                            $carteraPersonaMes->setHombre(
                                $libro->getPersona()->getGenero()->getGenero() == "MASCULINO" ? 1 : 0
                            );
                            $carteraPersonaMes->setMujer(
                                $libro->getPersona()->getGenero()->getGenero() == "FEMENINO" ? 1 : 0
                            );
                            $carteraPersonaMes->setCuenta($libro->getCuentaid());

                            if ($libro->getProductoContableId()->getId() == 9) {//Pago crédito
                                $carteraPersonaMes->setCapitalPagado(
                                    $carteraPersonaMes->getCapitalPagado() + $libro->getDebe()
                                );
                            } else {
                                if ($libro->getProductoContableId()->getId() == 10) {//Pago Interés
                                    $carteraPersonaMes->setInteresGanado(
                                        $carteraPersonaMes->getInteresGanado() + $libro->getDebe()
                                    );
                                } else {
                                    if ($libro->getProductoContableId()->getId() == 3) {//Pago Desgravamen
                                        $carteraPersonaMes->setPagoDegravamen(
                                            $carteraPersonaMes->getPagoDegravamen() + $libro->getDebe()
                                        );
                                    } else {
                                        if ($libro->getProductoContableId()->getId(
                                            ) == 4 || $libro->getProductoContableId()->getId(
                                            ) == 5//Credito Otorgado o Credito Emergente
                                        ) {
                                            $carteraPersonaMes->setCreditoMes(
                                                $carteraPersonaMes->getCreditoMes() + $libro->getHaber()
                                            );
                                            $carteraPersonaMes->setCreditoCant(
                                                $carteraPersonaMes->getCreditoCant() + 1
                                            );
                                        }
                                    }
                                }
                            }

                        }

                    }

                    $carteraPersonaMes->setSaldoAnterior(
                        $cartera->getSaldoAnterior($carteraMeses, $carteraMes, $carteraPersonaMes->getPersona())
                    );
                    $carteraPersonaMes->setCreditoCant(
                        $cartera->getNumeroCreditos(
                            $carteraMeses,
                            $carteraMes,
                            $carteraPersonaMes->getPersona()
                        ) + $carteraPersonaMes->getCreditoCant()
                    );

                    //saldo_anterior - capital_pagado + creditos_del_mes
                    $carteraPersonaMes->setCreditoMicroEmpPorVencerSaldoCap(
                        $carteraPersonaMes->getSaldoAnterior() - $carteraPersonaMes->getCapitalPagado(
                        ) + $carteraPersonaMes->getCreditoMes()
                    );

                    $carteraPersonaMes->updateTotalPagado();
                    $personasMes[] = $carteraPersonaMes;

                    $carteraMes->setPersonaMes($personasMes);
                    $carteraMeses[] = $carteraMes;
                }
            }


        }

        //calcular totales

        foreach ($carteraMeses as $carteraMes) {
            $personaTotal = new CarteraPersonaMes();
            foreach ($carteraMes->getPersonasMes() as $carteraPersonaMes) {

                $personaTotal->setSaldoAnterior(
                    round($personaTotal->getSaldoAnterior() + $carteraPersonaMes->getSaldoAnterior(),2)
                );
                $personaTotal->setCapitalPagado(
                    round($personaTotal->getCapitalPagado() + $carteraPersonaMes->getCapitalPagado(),2)
                );
                $personaTotal->setInteresGanado(
                    round($personaTotal->getInteresGanado() + $carteraPersonaMes->getInteresGanado(),2)
                );
                $personaTotal->setMoraPagada(round($personaTotal->getMoraPagada() + $carteraPersonaMes->getMoraPagada(),2));
                $personaTotal->setPagoDegravamen(
                    round($personaTotal->getPagoDegravamen() + $carteraPersonaMes->getPagoDegravamen(),2)
                );
                $personaTotal->setTotalPagado(
                    round($personaTotal->getTotalPagado() + $carteraPersonaMes->getTotalPagado(),2)
                );
                $personaTotal->setCreditoMes(round($personaTotal->getCreditoMes() + $carteraPersonaMes->getCreditoMes(),2));
                $personaTotal->setCreditoCant(
                    $personaTotal->getCreditoCant() + $carteraPersonaMes->getCreditoCant()
                );
                $personaTotal->setCreditoMicroEmpPorVencerSaldoCap(
                    round($personaTotal->getCreditoMicroEmpPorVencerSaldoCap(
                    ) + $carteraPersonaMes->getCreditoMicroEmpPorVencerSaldoCap(),2)
                );
                $personaTotal->setCreditoMicroEmpVencidaCapMora(
                    round($personaTotal->getCreditoMicroEmpVencidaCapMora(
                    ) + $carteraPersonaMes->getCreditoMicroEmpVencidaCapMora(),2)
                );
                $personaTotal->setCreditoConsumoPorVencerSaldoCap(
                    round($personaTotal->getCreditoConsumoPorVencerSaldoCap(
                    ) + $carteraPersonaMes->getCreditoConsumoPorVencerSaldoCap(),2)
                );
                $personaTotal->setCreditoConsumoVencidaCapMora(
                    round($personaTotal->getCreditoConsumoVencidaCapMora(
                    ) + $carteraPersonaMes->getCreditoConsumoVencidaCapMora(),2)
                );
                $personaTotal->setDiasAtrazo($personaTotal->getDiasAtrazo() + $carteraPersonaMes->getDiasAtrazo());
                $personaTotal->setHombre($personaTotal->getHombre() + $carteraPersonaMes->getHombre());
                $personaTotal->setMujer($personaTotal->getMujer() + $carteraPersonaMes->getMujer());
            }
            $carteraMes->setTotalesMes($personaTotal);
        }

        return $this->render(
            'ModulosPersonasBundle:Libro:cartera.html.twig',
            array(
                'carterameses' => $carteraMeses,
                'nombrecaja'=>$nombrecaja,
            )
        );

    }*/

    public function carteraAction()
    {
        $arrayMeses = array();
        $arrayMesesTexto = array();
        $salidaCreditos = array();

        $em = $this->getDoctrine()->getManager();

        $cajahorro = $em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja = $cajahorro->getRazonSocial();

        $codPersonas = $em->getRepository('ModulosPersonasBundle:Creditos')->findCreditosOrdenadosPorFecha();

        $cantPersonas = count($codPersonas);
        if ($cantPersonas == 0) {
            return $this->render(
                'ModulosPersonasBundle:Libro:cartera2.html.twig',
                array(
                    'meses' => $arrayMeses,
                    'mesesTexto' => $arrayMesesTexto,
                    'creditos' => $salidaCreditos,
                    'nombrecaja' => $nombrecaja,
                )
            );
        }

        $mesesMap = [
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
        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosASCEND();
        //$libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosSinFechaTipoCartera();
        if (count($libros) > 0) {
            $fecha1 = $libros[0]->getFecha();
            $fecha2 = $libros[count($libros) - 1]->getFecha();;

            $aux = "0:0:0";
            $fecha1 = $fecha1->format('Y-m-d');
            $fechaaux = $fecha1." ".$aux;
            $fecha1 = new \DateTime($fechaaux);
            $fecha1->setDate($fecha1->format('Y'), $fecha1->format('m'), 1);

            $aux2 = "23:59:59";
            $fecha2 = $fecha2->format('Y-m-d');
            $fechaaux2 = $fecha2." ".$aux2;
            $fecha2 = new \DateTime($fechaaux2);
            $aux1 = $fecha2->format('t');
            $fecha2->setDate($fecha2->format('Y'), $fecha2->format('m'), $aux1);

        } else {
            return $this->render(
                'ModulosPersonasBundle:Libro:cartera2.html.twig',
                array(
                    'meses' => $arrayMeses,
                    'mesesTexto' => $arrayMesesTexto,
                    'creditos' => $salidaCreditos,
                    'nombrecaja' => $nombrecaja,

                )
            );
        }

        $intervalo = new \DateInterval('P1M');
        $fecha1Iterante = new \DateTime($fecha1->format('Y-m-d'));
        $valores = array();

        while ($fecha1Iterante <= $fecha2) {
            $arrayMeses[] = $fecha1Iterante->format('Y-m');
            $arrayMesesTexto[$fecha1Iterante->format('Y-m')] = "Del 1 al ".$fecha1Iterante->format(
                    't'
                )." de ".$mesesMap[$fecha1Iterante->format('m')]." del ".$fecha1Iterante->format('Y');
            $valores[$fecha1Iterante->format('Y-m')] = 0;
            //$valoresS[$fecha1Iterante->format('Y-m')]="";
            $fecha1Iterante->add($intervalo);
        }

        $resumenCreditos = array();

        for ($i = 0; $i < count($codPersonas); $i++) {

            $personas=$em->getRepository('ModulosPersonasBundle:Persona')->find($codPersonas[$i][1]);

            $personaCartera = array(
                "persona" => $personas,
                "socio" => $valores,
                "capital" => $valores,
                "saldoAnterior" => $valores,
                "interes" => $valores,
                "mora" => $valores,
                "desgravamen" => $valores,
                "totalpagado" => $valores,
                "creditomes" => $valores,
                "creditono" => $valores,
                "ccmxv" => $valores,
                "ccmv" => $valores,
                "ccxv" => $valores,
                "cccv" => $valores,
                "atraso" => $valores,
                "hombre" => $valores,
                "mujer" => $valores,
                "saldoFinal" => $valores,
            );

            $carteras = $em->getRepository('ModulosPersonasBundle:Libro')->findCarteraPorPersona($personas);
            $creditosPorPersona = $em->getRepository(
                'ModulosPersonasBundle:Creditos'
            )->findCreditosOtrogadosByPersona($personas);

            $diasAtraso = 0;
            if (count($creditosPorPersona) > 0) {
                for ($k = 0; $k < count($creditosPorPersona); $k++) {

                    $amortizaciones = $em->getRepository(
                        'ModulosPersonasBundle:TablaAmortizacion'
                    )->findTablasAmortizacionPorCreditos($creditosPorPersona[$k]->getId());

                    $pagosrealizados = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaCredito'
                    )->findPagosCuotasCreditos($creditosPorPersona[$k]->getId());

                    $dias = 0;
                    if (count($pagosrealizados) > 0) {
                        foreach ($pagosrealizados as $key => $pagorealizado) {
                            if ($pagorealizado->getFechaDePago()->format(
                                    'Ymd'
                                ) > $amortizaciones[$key + 1]->getFechaDePago()->format('Ymd')
                            ) {
                                $dias += $pagorealizado->getFechaDePago()->format(
                                        'd'
                                    ) - $amortizaciones[$key + 1]->getFechaDePago()->format('d');
                            }
                        }
                    }
                    $diasAtraso += $dias;

                }
            }

            $personaCartera["atraso"] = $diasAtraso;

            $personaCartera["hombre"] = $personas->getGenero()->getGenero() == "MASCULINO" ? 1 : 0;
            $personaCartera["mujer"] = $personas->getGenero()->getGenero() == "FEMENINO" ? 1 : 0;
            $personaCartera["socio"] = $personas->getTipo_persona() == "SOCIOS" ? 'S' : '-';

            $cant = 0;
            $ccmxv = 0;
            $saldoa = 0;
            for ($j = 0; $j < count($carteras); $j++) {

                if ($personas->getId() == $carteras[$j]->getPersona()->getId()) {
                    if ($carteras[$j]->getProductoContableId()->getId() == 9) {//Pago crédito
                        $personaCartera["capital"][$carteras[$j]->getFecha()->format(
                            'Y-m'
                        )] += $carteras[$j]->getDebe();
                    } else {
                        if ($carteras[$j]->getProductoContableId()->getId() == 10) {//Pago Interés
                            $personaCartera["interes"][$carteras[$j]->getFecha()->format(
                                'Y-m'
                            )] += $carteras[$j]->getDebe();
                        } else {
                            if ($carteras[$j]->getProductoContableId()->getId() == 3) {//Pago Desgravamen
                                $personaCartera["desgravamen"][$carteras[$j]->getFecha()->format(
                                    'Y-m'
                                )] += $carteras[$j]->getDebe();
                            } else {
                                if ($carteras[$j]->getProductoContableId()->getId(
                                    ) == 4 || $carteras[$j]->getProductoContableId()->getId() == 5
                                ) {//Credito Otorgado o Credito Emergente
                                    $personaCartera["creditomes"][$carteras[$j]->getFecha()->format(
                                        'Y-m'
                                    )] += $carteras[$j]->getHaber();
                                    $cant += 1;
                                } else {
                                    if ($carteras[$j]->getProductoContableId()->getId() == 11) {//Mora
                                        $personaCartera["mora"][$carteras[$j]->getFecha()->format(
                                            'Y-m'
                                        )] += $carteras[$j]->getDebe();
                                    }
                                }
                            }
                        }
                    }

                    $personaCartera["totalpagado"][$carteras[$j]->getFecha()->format(
                        'Y-m'
                    )] = $personaCartera["interes"][$carteras[$j]->getFecha()->format(
                            'Y-m'
                        )] + $personaCartera["capital"][$carteras[$j]->getFecha()->format(
                            'Y-m'
                        )] + $personaCartera["desgravamen"][$carteras[$j]->getFecha()->format('Y-m')];
                }
            }
            $personaCartera["creditono"] = $cant;


            $resumenCreditos[] = $personaCartera;
        }
        for ($i = 0; $i < count($resumenCreditos); $i++) {
            for ($j = 0; $j < count($arrayMeses); $j++) {

                $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]] = (($j > 0) ? ($resumenCreditos[$i]["ccmxv"][$arrayMeses[$j - 1]] + $resumenCreditos[$i]["ccmv"][$arrayMeses[$j - 1]]) : 0);

                if ($resumenCreditos[$i]["mora"][$arrayMeses[$j]] > 0) {
                    $resumenCreditos[$i]["ccmxv"][$arrayMeses[$j]] = 0;
                    $resumenCreditos[$i]["ccmv"][$arrayMeses[$j]] = $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]] - $resumenCreditos[$i]["capital"][$arrayMeses[$j]] + $resumenCreditos[$i]["creditomes"][$arrayMeses[$j]] + $resumenCreditos[$i]["mora"][$arrayMeses[$j]];
                } else {
                    $resumenCreditos[$i]["ccmxv"][$arrayMeses[$j]] = $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]] - $resumenCreditos[$i]["capital"][$arrayMeses[$j]] + $resumenCreditos[$i]["creditomes"][$arrayMeses[$j]];
                    $resumenCreditos[$i]["ccmv"][$arrayMeses[$j]] = 0;
                }


            }
        }


        for ($j = 0; $j < count($arrayMeses); $j++) {
            $salidaCreditos[$arrayMeses[$j]] = array();
            for ($i = 0; $i < count($resumenCreditos); $i++) {

                if( ($resumenCreditos[$i]["desgravamen"][$arrayMeses[$j]]!=0||
                    $resumenCreditos[$i]["capital"][$arrayMeses[$j]]!=0||
                    $resumenCreditos[$i]["mora"][$arrayMeses[$j]]!=0||
                    $resumenCreditos[$i]["interes"][$arrayMeses[$j]]!=0||
                    $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]]!=0)){

                    $personaMes = array();
                    $personaMes["persona"] = $resumenCreditos[$i]["persona"];
                    $personaMes["socio"] = $resumenCreditos[$i]["socio"];
                    $personaMes["atraso"] = $resumenCreditos[$i]["atraso"];
                    $personaMes["saldoAnterior"] = $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]];
                    $personaMes["capital"] = $resumenCreditos[$i]["capital"][$arrayMeses[$j]];
                    $personaMes["interes"] = $resumenCreditos[$i]["interes"][$arrayMeses[$j]];
                    $personaMes["mora"] = $resumenCreditos[$i]["mora"][$arrayMeses[$j]];
                    $personaMes["desgravamen"] = $resumenCreditos[$i]["desgravamen"][$arrayMeses[$j]];
                    $personaMes["totalpagado"] = $resumenCreditos[$i]["totalpagado"][$arrayMeses[$j]];
                    $personaMes["creditomes"] = $resumenCreditos[$i]["creditomes"][$arrayMeses[$j]];
                    $personaMes["creditono"] = $resumenCreditos[$i]["creditono"];
                    $personaMes["ccmxv"] = $resumenCreditos[$i]["ccmxv"][$arrayMeses[$j]];
                    $personaMes["ccmv"] = $resumenCreditos[$i]["ccmv"][$arrayMeses[$j]];
                    $personaMes["hombre"] = $resumenCreditos[$i]["hombre"];
                    $personaMes["mujer"] = $resumenCreditos[$i]["mujer"];
                    $personaMes["saldoFinal"] = $resumenCreditos[$i]["saldoFinal"][$arrayMeses[$j]];
                    $salidaCreditos[$arrayMeses[$j]][] = $personaMes;
                }
            }

        }

        return $this->render(
            'ModulosPersonasBundle:Libro:cartera2.html.twig',
            array(
                'meses' => $arrayMeses,
                'mesesTexto' => $arrayMesesTexto,
                'creditos' => $salidaCreditos,
                'nombrecaja' => $nombrecaja,
            )
        );

    }

    public
    function exportarCarteraAction()
    {
        $arrayMeses = array();
        $arrayMesesTexto = array();
        $salidaCreditos = array();

        $em = $this->getDoctrine()->getManager();

        $cajahorro = $em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja = $cajahorro->getRazonSocial();

        $codPersonas = $em->getRepository('ModulosPersonasBundle:Creditos')->findCreditosOrdenadosPorFecha();

        $cantPersonas = count($codPersonas);
        if ($cantPersonas == 0) {
            return $this->render(
                'ModulosPersonasBundle:Libro:cartera2.html.twig',
                array(
                    'meses' => $arrayMeses,
                    'mesesTexto' => $arrayMesesTexto,
                    'creditos' => $salidaCreditos,
                    'nombrecaja' => $nombrecaja,
                )
            );
        }

        $mesesMap = [
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
        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosASCEND();
        //$libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosSinFechaTipoCartera();
        if (count($libros) > 0) {
            $fecha1 = $libros[0]->getFecha();
            $fecha2 = $libros[count($libros) - 1]->getFecha();;

            $aux = "0:0:0";
            $fecha1 = $fecha1->format('Y-m-d');
            $fechaaux = $fecha1." ".$aux;
            $fecha1 = new \DateTime($fechaaux);
            $fecha1->setDate($fecha1->format('Y'), $fecha1->format('m'), 1);

            $aux2 = "23:59:59";
            $fecha2 = $fecha2->format('Y-m-d');
            $fechaaux2 = $fecha2." ".$aux2;
            $fecha2 = new \DateTime($fechaaux2);
            $aux1 = $fecha2->format('t');
            $fecha2->setDate($fecha2->format('Y'), $fecha2->format('m'), $aux1);

        } else {
            return $this->render(
                'ModulosPersonasBundle:Libro:cartera2.html.twig',
                array(
                    'meses' => $arrayMeses,
                    'mesesTexto' => $arrayMesesTexto,
                    'creditos' => $salidaCreditos,
                    'nombrecaja' => $nombrecaja,

                )
            );
        }

        $intervalo = new \DateInterval('P1M');
        $fecha1Iterante = new \DateTime($fecha1->format('Y-m-d'));
        $valores = array();

        while ($fecha1Iterante <= $fecha2) {
            $arrayMeses[] = $fecha1Iterante->format('Y-m');
            $arrayMesesTexto[$fecha1Iterante->format('Y-m')] = "Del 1 al ".$fecha1Iterante->format(
                    't'
                )." de ".$mesesMap[$fecha1Iterante->format('m')]." del ".$fecha1Iterante->format('Y');
            $valores[$fecha1Iterante->format('Y-m')] = 0;
            //$valoresS[$fecha1Iterante->format('Y-m')]="";
            $fecha1Iterante->add($intervalo);
        }

        $resumenCreditos = array();

        for ($i = 0; $i < count($codPersonas); $i++) {

            $personas=$em->getRepository('ModulosPersonasBundle:Persona')->find($codPersonas[$i][1]);

            $personaCartera = array(
                "persona" => $personas,
                "socio" => $valores,
                "capital" => $valores,
                "saldoAnterior" => $valores,
                "interes" => $valores,
                "mora" => $valores,
                "desgravamen" => $valores,
                "totalpagado" => $valores,
                "creditomes" => $valores,
                "creditono" => $valores,
                "ccmxv" => $valores,
                "ccmv" => $valores,
                "ccxv" => $valores,
                "cccv" => $valores,
                "atraso" => $valores,
                "hombre" => $valores,
                "mujer" => $valores,
                "saldoFinal" => $valores,
            );

            $carteras = $em->getRepository('ModulosPersonasBundle:Libro')->findCarteraPorPersona($personas);
            $creditosPorPersona = $em->getRepository(
                'ModulosPersonasBundle:Creditos'
            )->findCreditosOtrogadosByPersona($personas);

            $diasAtraso = 0;
            if (count($creditosPorPersona) > 0) {
                for ($k = 0; $k < count($creditosPorPersona); $k++) {

                    $amortizaciones = $em->getRepository(
                        'ModulosPersonasBundle:TablaAmortizacion'
                    )->findTablasAmortizacionPorCreditos($creditosPorPersona[$k]->getId());

                    $pagosrealizados = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaCredito'
                    )->findPagosCuotasCreditos($creditosPorPersona[$k]->getId());

                    $dias = 0;
                    if (count($pagosrealizados) > 0) {
                        foreach ($pagosrealizados as $key => $pagorealizado) {
                            if ($pagorealizado->getFechaDePago()->format(
                                    'Ymd'
                                ) > $amortizaciones[$key + 1]->getFechaDePago()->format('Ymd')
                            ) {
                                $dias += $pagorealizado->getFechaDePago()->format(
                                        'd'
                                    ) - $amortizaciones[$key + 1]->getFechaDePago()->format('d');
                            }
                        }
                    }
                    $diasAtraso += $dias;

                }
            }

            $personaCartera["atraso"] = $diasAtraso;

            $personaCartera["hombre"] = $personas->getGenero()->getGenero() == "MASCULINO" ? 1 : 0;
            $personaCartera["mujer"] = $personas->getGenero()->getGenero() == "FEMENINO" ? 1 : 0;
            $personaCartera["socio"] = $personas->getTipo_persona() == "SOCIOS" ? 'S' : '-';

            $cant = 0;
            $ccmxv = 0;
            $saldoa = 0;
            for ($j = 0; $j < count($carteras); $j++) {

                if ($personas->getId() == $carteras[$j]->getPersona()->getId()) {
                    if ($carteras[$j]->getProductoContableId()->getId() == 9) {//Pago crédito
                        $personaCartera["capital"][$carteras[$j]->getFecha()->format(
                            'Y-m'
                        )] += $carteras[$j]->getDebe();
                    } else {
                        if ($carteras[$j]->getProductoContableId()->getId() == 10) {//Pago Interés
                            $personaCartera["interes"][$carteras[$j]->getFecha()->format(
                                'Y-m'
                            )] += $carteras[$j]->getDebe();
                        } else {
                            if ($carteras[$j]->getProductoContableId()->getId() == 3) {//Pago Desgravamen
                                $personaCartera["desgravamen"][$carteras[$j]->getFecha()->format(
                                    'Y-m'
                                )] += $carteras[$j]->getDebe();
                            } else {
                                if ($carteras[$j]->getProductoContableId()->getId(
                                    ) == 4 || $carteras[$j]->getProductoContableId()->getId() == 5
                                ) {//Credito Otorgado o Credito Emergente
                                    $personaCartera["creditomes"][$carteras[$j]->getFecha()->format(
                                        'Y-m'
                                    )] += $carteras[$j]->getHaber();
                                    $cant += 1;
                                } else {
                                    if ($carteras[$j]->getProductoContableId()->getId() == 11) {//Mora
                                        $personaCartera["mora"][$carteras[$j]->getFecha()->format(
                                            'Y-m'
                                        )] += $carteras[$j]->getDebe();
                                    }
                                }
                            }
                        }
                    }

                    $personaCartera["totalpagado"][$carteras[$j]->getFecha()->format(
                        'Y-m'
                    )] = $personaCartera["interes"][$carteras[$j]->getFecha()->format(
                            'Y-m'
                        )] + $personaCartera["capital"][$carteras[$j]->getFecha()->format(
                            'Y-m'
                        )] + $personaCartera["desgravamen"][$carteras[$j]->getFecha()->format('Y-m')];
                }
            }
            $personaCartera["creditono"] = $cant;


            $resumenCreditos[] = $personaCartera;
        }
        for ($i = 0; $i < count($resumenCreditos); $i++) {
            for ($j = 0; $j < count($arrayMeses); $j++) {

                $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]] = (($j > 0) ? ($resumenCreditos[$i]["ccmxv"][$arrayMeses[$j - 1]] + $resumenCreditos[$i]["ccmv"][$arrayMeses[$j - 1]]) : 0);

                if ($resumenCreditos[$i]["mora"][$arrayMeses[$j]] > 0) {
                    $resumenCreditos[$i]["ccmxv"][$arrayMeses[$j]] = 0;
                    $resumenCreditos[$i]["ccmv"][$arrayMeses[$j]] = $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]] - $resumenCreditos[$i]["capital"][$arrayMeses[$j]] + $resumenCreditos[$i]["creditomes"][$arrayMeses[$j]] + $resumenCreditos[$i]["mora"][$arrayMeses[$j]];
                } else {
                    $resumenCreditos[$i]["ccmxv"][$arrayMeses[$j]] = $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]] - $resumenCreditos[$i]["capital"][$arrayMeses[$j]] + $resumenCreditos[$i]["creditomes"][$arrayMeses[$j]];
                    $resumenCreditos[$i]["ccmv"][$arrayMeses[$j]] = 0;
                }


            }
        }


        for ($j = 0; $j < count($arrayMeses); $j++) {
            $salidaCreditos[$arrayMeses[$j]] = array();
            for ($i = 0; $i < count($resumenCreditos); $i++) {

                if( ($resumenCreditos[$i]["desgravamen"][$arrayMeses[$j]]!=0||
                    $resumenCreditos[$i]["capital"][$arrayMeses[$j]]!=0||
                    $resumenCreditos[$i]["mora"][$arrayMeses[$j]]!=0||
                    $resumenCreditos[$i]["interes"][$arrayMeses[$j]]!=0||
                    $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]]!=0)){

                    $personaMes = array();
                    $personaMes["persona"] = $resumenCreditos[$i]["persona"];
                    $personaMes["socio"] = $resumenCreditos[$i]["socio"];
                    $personaMes["atraso"] = $resumenCreditos[$i]["atraso"];
                    $personaMes["saldoAnterior"] = $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]];
                    $personaMes["capital"] = $resumenCreditos[$i]["capital"][$arrayMeses[$j]];
                    $personaMes["interes"] = $resumenCreditos[$i]["interes"][$arrayMeses[$j]];
                    $personaMes["mora"] = $resumenCreditos[$i]["mora"][$arrayMeses[$j]];
                    $personaMes["desgravamen"] = $resumenCreditos[$i]["desgravamen"][$arrayMeses[$j]];
                    $personaMes["totalpagado"] = $resumenCreditos[$i]["totalpagado"][$arrayMeses[$j]];
                    $personaMes["creditomes"] = $resumenCreditos[$i]["creditomes"][$arrayMeses[$j]];
                    $personaMes["creditono"] = $resumenCreditos[$i]["creditono"];
                    $personaMes["ccmxv"] = $resumenCreditos[$i]["ccmxv"][$arrayMeses[$j]];
                    $personaMes["ccmv"] = $resumenCreditos[$i]["ccmv"][$arrayMeses[$j]];
                    $personaMes["hombre"] = $resumenCreditos[$i]["hombre"];
                    $personaMes["mujer"] = $resumenCreditos[$i]["mujer"];
                    $personaMes["saldoFinal"] = $resumenCreditos[$i]["saldoFinal"][$arrayMeses[$j]];
                    $salidaCreditos[$arrayMeses[$j]][] = $personaMes;
                }
            }

        }

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Conquito")
            ->setLastModifiedBy("Conquito")
            ->setTitle("Cartera de Caja")
            ->setSubject("Cartera de Caja")
            ->setDescription("Cartera de Caja")
            ->setKeywords("Cartera de Caja")
            ->setCategory("Reporte excel");

        //$tituloReporte1 = "Listado de libros de cajas por meses de:".$fecha1->format('d-m-Y').' a '.$fecha2->format('d-m-Y');
        $tituloReporte = "LIBRO GENERAL DE CARTERA DE CRÉDITO";
        $tituloHoja = "Cartera de Caja";

        $estiloTituloCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 14,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );

        $estiloCeldaDerecha = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloCeldaIzq = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );

        $estiloSubCabCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 12,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );
        $estiloTituloColumnasCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'size' => 9,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloCeldaCartera = array(
            'font' => array(
                'name' => 'Arial',
                'bold' => false,
                'size' => 10,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloDivisor = array(

            'borders' => array(
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
                    'color' => array(
                        'rgb' => 'FFFFFF',
                    ),
                ),

            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', $tituloReporte);
        $phpExcelObject->getActiveSheet()->getStyle('A1:R1')->applyFromArray($estiloTituloCartera);

        $titulosColumnas = array(
            'No ',
            'Nombre y Apellidos',
            'Socio',
            'Saldo Anterior',
            'Capital Pagado',
            'Interés Ganado',
            'Mora Pagada',
            'Pago de Desgrav.',
            'Total Pagado',
            'Crédito del Mes',
            'Crédito No',
            'Cartera de crédito Microempresa x vencer',
            'Cartera de crédito Microempresa vencida',
            'Cartera de crédito Consumo x vencer',
            'Cartera de crédito Consumo vencida',
            'Dias Atrazo',
            'Hombres',
            'Mujeres',
        );
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A1:R1');


        $i = 4;
        $mesIndex = 0;
        foreach ($arrayMeses as $mes) {

            // Se agregan los titulos del reporte
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $nombrecaja);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray($estiloSubCabCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':R'.$i);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $arrayMesesTexto[$mes]);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray($estiloTituloCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':R'.$i);
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $titulosColumnas[0]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':A'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':A'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, $titulosColumnas[1]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':B'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':B'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, $titulosColumnas[2]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('C'.$i.':C'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i.':C'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D'.$i, $titulosColumnas[3]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('D'.$i.':D'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('D'.$i.':D'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E'.$i, $titulosColumnas[4]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('E'.$i.':E'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('E'.$i.':E'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, "Interesés y descuentos de cartera de créditos");
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('F'.$i.':G'.($i));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i.':G'.$i)->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.($i + 1), $titulosColumnas[5]);
            $phpExcelObject->getActiveSheet()->getStyle('F'.($i + 1))->applyFromArray($estiloTituloColumnasCartera);
//
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.($i + 1), $titulosColumnas[6]);
            $phpExcelObject->getActiveSheet()->getStyle('G'.($i + 1))->applyFromArray($estiloTituloColumnasCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('H'.$i, $titulosColumnas[7]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('H'.$i.':H'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('H'.$i.':H'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, $titulosColumnas[8]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('I'.$i.':I'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('I'.$i.':I'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, $titulosColumnas[9]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':J'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i.':J'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, $titulosColumnas[10]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('K'.$i.':K'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('K'.$i.':K'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('L'.$i, $titulosColumnas[11]);
            $phpExcelObject->getActiveSheet()->getStyle('L'.$i)->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('L'.($i + 1), 'Saldo de capital');
            $phpExcelObject->getActiveSheet()->getStyle('L'.($i + 1))->applyFromArray($estiloTituloColumnasCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, $titulosColumnas[12]);
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.($i + 1), 'Capital en mora');
            $phpExcelObject->getActiveSheet()->getStyle('M'.($i + 1))->applyFromArray($estiloTituloColumnasCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, $titulosColumnas[13]);
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.($i + 1), 'Saldo de capital');
            $phpExcelObject->getActiveSheet()->getStyle('N'.($i + 1))->applyFromArray($estiloTituloColumnasCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('O'.$i, $titulosColumnas[14]);
            $phpExcelObject->getActiveSheet()->getStyle('O'.$i)->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('O'.($i + 1), 'Capital en mora');
            $phpExcelObject->getActiveSheet()->getStyle('O'.($i + 1))->applyFromArray($estiloTituloColumnasCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('P'.$i, $titulosColumnas[15]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('P'.$i.':P'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('P'.$i.':P'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('Q'.$i, $titulosColumnas[16]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('Q'.$i.':Q'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('Q'.$i.':Q'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('R'.$i, $titulosColumnas[17]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('R'.$i.':R'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('R'.$i.':R'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $i++;
            $i++;
            $iIndice = $i;
            $cont = 1;
            foreach ($salidaCreditos[$mes] as $personaM) {
                $personaNombre = $personaM["persona"]->__toString();
                $socio = $personaM["socio"];
                $saldoAnterior = $personaM["saldoAnterior"];
                $capitalPagado = $personaM["capital"];
                $interesGanado = $personaM["interes"];
                $moraPagada = $personaM["mora"];
                $pagoDegravamen = $personaM["desgravamen"];
                $totalPagado = $personaM["totalpagado"];
                $creditoMes = $personaM["creditomes"];
                $creditoCant = $personaM["creditono"];
                $creditoMicroEmpPorVencerSaldoCap = $personaM["ccmxv"];
                $creditoMicroEmpVencidaCapMora = $personaM["ccmv"];
                $creditoConsumoPorVencerSaldoCap = 0;
                $creditoConsumoVencidaCapMora = 0;
                $diasAtrazo = $personaM["atraso"];
                $hombre = $personaM["hombre"];
                $mujer = $personaM["mujer"];

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $cont++);
                $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('B'.$i, $personaNombre);
                $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C'.$i, $socio);
                $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('D'.$i, number_format($saldoAnterior, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('E'.$i, number_format($capitalPagado, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('F'.$i, number_format($interesGanado, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('G'.$i, number_format($moraPagada, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('H'.$i, number_format($pagoDegravamen, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('H'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('H'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('I'.$i, number_format($totalPagado, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('I'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('I'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('J'.$i, number_format($creditoMes, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('J'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('J'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('K'.$i, $creditoCant);
                $phpExcelObject->getActiveSheet()->getStyle('K'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('L'.$i, number_format($creditoMicroEmpPorVencerSaldoCap, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('L'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('L'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('M'.$i, number_format($creditoMicroEmpVencidaCapMora, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('N'.$i, number_format($creditoConsumoPorVencerSaldoCap, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('O'.$i, number_format($creditoConsumoVencidaCapMora, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('O'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('O'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('P'.$i, $diasAtrazo);
                $phpExcelObject->getActiveSheet()->getStyle('P'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('Q'.$i, $hombre);
                $phpExcelObject->getActiveSheet()->getStyle('Q'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('R'.$i, $mujer);
                $phpExcelObject->getActiveSheet()->getStyle('R'.$i)->applyFromArray($estiloCeldaCartera);

                $i++;
            }
            //totales
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, " ");
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "TOTAL");
            $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, " ");
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D'.$i, '=SUM(D'.$iIndice.':D'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E'.$i, '=SUM(E'.$iIndice.':E'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, '=SUM(F'.$iIndice.':F'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaDerecha);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, '=SUM(G'.$iIndice.':G'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaDerecha);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('H'.$i, '=SUM(H'.$iIndice.':H'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('H'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('H'.$i)->applyFromArray($estiloCeldaDerecha);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '=SUM(I'.$iIndice.':I'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('I'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('I'.$i)->applyFromArray($estiloCeldaDerecha);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, '=SUM(J'.$iIndice.':J'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i)->applyFromArray($estiloCeldaDerecha);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, '=SUM(K'.$iIndice.':K'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('K'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('L'.$i, '=SUM(L'.$iIndice.':L'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('L'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('L'.$i)->applyFromArray($estiloCeldaDerecha);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, '=SUM(M'.$iIndice.':M'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaDerecha);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, '=SUM(N'.$iIndice.':N'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaDerecha);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('O'.$i, '=SUM(O'.$iIndice.':O'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('O'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('O'.$i)->applyFromArray($estiloCeldaDerecha);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('P'.$i, '=SUM(P'.$iIndice.':P'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('P'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('P'.$i)->applyFromArray($estiloCeldaDerecha);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('Q'.$i, '=SUM(Q'.$iIndice.':Q'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('Q'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('R'.$i, '=SUM(R'.$iIndice.':R'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('R'.$i)->applyFromArray($estiloCeldaCartera);

            $i++;


            $phpExcelObject->getActiveSheet()->getStyle('A'.$i++.':R'.$i)->applyFromArray($estiloDivisor);
            $i++;
            $i++;
            $i++;


        }
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('A')
            ->setWidth(5);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(23);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(8);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(10);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(10);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('F')
            ->setWidth(7);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('G')
            ->setWidth(7);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('H')
            ->setWidth(13);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('I')
            ->setWidth(10);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('J')
            ->setWidth(10);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('K')
            ->setWidth(12);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('L')
            ->setWidth(14);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('M')
            ->setWidth(14);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('N')
            ->setWidth(14);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('O')
            ->setWidth(14);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('P')
            ->setWidth(7);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('Q')
            ->setWidth(8);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('R')
            ->setWidth(8);
        //$phpExcelObject->getActiveSheet()->getStyle('A3:H3')->applyFromArray($estiloTituloColumnas);
        //$phpExcelObject->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:W".($i-1));
//        for ($i = 'A'; $i <= 'H'; $i++) {
//            $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension($i)->setAutoSize(true);
//        }

        $phpExcelObject->getActiveSheet()->setTitle($tituloHoja);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Cartera General.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;

    }

    public
    function pygAction()
    {
        $em = $this->getDoctrine()->getManager();
        $pygMeses = array();

        $cajahorro = $em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja = $cajahorro->getRazonSocial();

        $dtvcAll = $em->getRepository('ModulosPersonasBundle:DTVC')->findAllDTVCAporFechasAsc();

        //$dtvcAll[0]->getIdVchr()->getFecha()->format('Y-m') ;//print_r($dtvcAll[0]->getIdVchr()->getFecha()->format('Y'));
        $meses = array();
        if (count($dtvcAll) > 0) {
            $aux = $dtvcAll[0]->getIdVchr()->getFecha()->format('Y-m');
            $meses [] = $aux;
            for ($i = 1; $i < count($dtvcAll); $i++) {
                if ($dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m') != $aux) {
                    $aux = $dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m');
                    $meses [] = $aux;
                }
            }
        }


        for ($i = 0; $i < count($meses); $i++) {
            $fechaArray = explode('-', $meses[$i]);
            $fechaInicial = new \DateTime();
            $fechaInicial->setDate($fechaArray[0], $fechaArray[1], 1);
            $cantDias = $fechaInicial->format('t');

            $fechaFinal = new \DateTime();
            $fechaFinal->setDate($fechaArray[0], $fechaArray[1], $cantDias);

            $pyg = new PYG();
            $dtvcMes = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAporFechasAsc(
                $fechaInicial,
                $fechaFinal
            );
//            for($j=0;$i<count($dtvcMes);$j++){

            $ingresosOutput = array(
                "51" => "",
                "5101" => "",
                "5104" => "",
                "5190" => "",
                "54" => "",
                "5490" => "",
                "56" => "",
                "5690" => "",
                "3603" => "",
            );
            $gastosOutput = array(
                "41" => "",
                "4101" => "",
                "4103" => "",
                "4105" => "",
                "44" => "",
                "4402" => "",
                "45" => "",
                "4501" => "",
                "4502" => "",
                "4503" => "",
                "4504" => "",
                "4507" => "",
            );
            $ingresos = array(0 => "51", "5101", "5104", "5190", "54", "5490", "56", "5690", "3603");
            $gastos = array(
                0 => "41",
                "4101",
                "4103",
                "4105",
                "44",
                "4402",
                "45",
                "4501",
                "4502",
                "4503",
                "4504",
                "4507",
            );
            $totales = array("ingresos" => "", "gastos" => "");

            $totalIngreso = 0;
            for ($k = 0; $k < count($ingresos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $ingresos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $ingresos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );

                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];
                $ingresosOutput[$ingresos[$k]] = $deudoraValor - $acreedoraValor;
                $totalIngreso += $ingresosOutput[$ingresos[$k]];
            }
            $totales["ingresos"] = $totalIngreso;

            $totalGasto = 0;
            for ($k = 0; $k < count($gastos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $gastos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $gastos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );


//
//                    $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora($gastos[$k],$fechaArray[1]);
//                    $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora($gastos[$k],$fechaArray[1]);
//                    echo "<pre>";
//                    print_r($fechaArray[1]);
//                    print_r("&nbsp;");
//                    print_r($gastos[$k]);
//                    print_r("&nbsp;");
//                    print_r($acreedora[0][1]);
//                    print_r("&nbsp;");
//                    print_r($deudora[0][1]);
//                    die();
                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

//                    $acreedoraValor= ($acreedora[0][1]==null)? 0: $acreedora[0][1];
//                    $deudoraValor= ($deudoraValor[0][1]==null)? 0: $deudora[0][1];
                $totalGasto += ($acreedoraValor - $deudoraValor);
                $gastosOutput[$gastos[$k]] = $acreedoraValor - $deudoraValor;
            }
            $totales["gastos"] = $totalGasto;

            $pyg->setMes($meses[$i]);
            $pyg->setIngresos($ingresosOutput);
            $pyg->setGastos($gastosOutput);
            $pyg->setTotales($totales);

//            echo count($pyg->getTotales());
//            die();
            $pygMeses[] = $pyg;
        }

        return $this->render(
            'ModulosPersonasBundle:Libro:pyg.html.twig',
            array(
                'pygMeses' => $pygMeses,
                'nombrecaja' => $nombrecaja,
            )
        );
//        echo count($pygMeses);
//        die();

    }

    public function balancegeneralAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cajahorro = $em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja = $cajahorro->getRazonSocial();
        $balanceGralMeses = array();

        $dtvcAll = $em->getRepository('ModulosPersonasBundle:DTVC')->findAllDTVCAporFechasAsc();

        $meses = array();
        if (count($dtvcAll) > 0) {
            $aux = $dtvcAll[0]->getIdVchr()->getFecha()->format('Y-m');
            $meses [] = $aux;
            for ($i = 1; $i < count($dtvcAll); $i++) {
                if ($dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m') != $aux) {
                    $aux = $dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m');
                    $meses [] = $aux;
                }
            }
        }


        //for

        $activosSaldoAnterior = array(
            "11" => 0,
            "1101" => 0,
            "1103" => 0,
            "13" => 0,
            "1301" => 0,
            "14" => 0,
            "1402" => 0,
            "1404" => 0,
            "1422" => 0,
            "1424" => 0,
            "1499" => 0,
            "16" => 0,
            "1602" => 0,
            "1603" => 0,
            "1690" => 0,
            "18" => 0,
            "1801" => 0,
            "1802" => 0,
            "1805" => 0,
            "1806" => 0,
            "1807" => 0,
            "1808" => 0,
            "1890" => 0,
            "19" => 0,
            "1905" => 0,
            "1990" => 0,
        );
        $pasivosSaldoAnterior = array(
            "21" => 0,
            "2101" => 0,
            "2103" => 0,
            "2105" => 0,
            "25" => 0,
            "2503" => 0,
            "2504" => 0,
            "2505" => 0,
            "2590" => 0,
            "26" => 0,
            "2602" => 0,
            "2606" => 0,
            "29" => 0,
            "2990" => 0,
        );
        $patrimonioSaldoAnterior = array(
            "31" => 0,
            "3103" => 0,
            "34" => 0,
            "3402" => 0,
            "3490" => 0,
            "36" => 0,
            "3601" => 0,
            "3602" => 0,
            "3603" => 0,
            "3604" => 0,
        );

        for ($i = 0; $i < count($meses); $i++) {

            //echo $meses[$i].'<BR>';

            $fechaArray = explode('-', $meses[$i]);
            $fechaInicial = new \DateTime();
            $fechaInicial->setDate($fechaArray[0], $fechaArray[1], 1);
            $cantDias = $fechaInicial->format('t');

            $fechaFinal = new \DateTime();
            $fechaFinal->setDate($fechaArray[0], $fechaArray[1], $cantDias);

            $balanceGral = new BalanceGral();

            $activosOutput = array(
                "11" => "",
                "1101" => "",
                "1103" => "",
                "13" => "",
                "1301" => "",
                "14" => "",
                "1402" => "",
                "1404" => "",
                "1422" => "",
                "1424" => "",
                "1499" => "",
                "16" => "",
                "1602" => "",
                "1603" => "",
                "1690" => "",
                "18" => "",
                "1801" => "",
                "1802" => "",
                "1805" => "",
                "1806" => "",
                "1807" => "",
                "1808" => "",
                "1890" => "",
                "19" => "",
                "1905" => "",
                "1990" => "",
            );
            $pasivosOutput = array(
                "21" => "",
                "2101" => "",
                "2103" => "",
                "2105" => "",
                "25" => "",
                "2503" => "",
                "2504" => "",
                "2505" => "",
                "2590" => "",
                "26" => "",
                "2602" => "",
                "2606" => "",
                "29" => "",
                "2990" => "",
            );
            $patrimonioOutput = array(
                "31" => "",
                "3103" => "",
                "34" => "",
                "3402" => "",
                "3490" => "",
                "36" => "",
                "3601" => "",
                "3602" => "",
                "3603" => "",
                "3604" => "",
            );


            $activos = array(
                0 => "11",
                "1101",
                "1103",
                "13",
                "1301",
                "14",
                "1402",
                "1404",
                "1422",
                "1424",
                "1499",
                "16",
                "1602",
                "1603",
                "1690",
                "18",
                "1801",
                "1802",
                "1805",
                "1806",
                "1807",
                "1808",
                "1890",
                "19",
                "1905",
                "1990",
            );
            $pasivos = array(
                0 => "21",
                "2101",
                "2103",
                "2105",
                "25",
                "2503",
                "2504",
                "2505",
                "2590",
                "26",
                "2602",
                "2606",
                "29",
                "2990",
            );
            $patrimonio = array(0 => "31", "3103", "34", "3402", "3490", "36", "3601", "3602", "3603", "3604");
            $totales = array("activos" => "", "pasivos" => "", "patrimonio" => "");


            $totalActivos = 0;
            for ($k = 0; $k < count($activos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $activos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $activos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

                $activosOutput[$activos[$k]] = $acreedoraValor - $deudoraValor + $activosSaldoAnterior[$activos[$k]];
                $totalActivos += $activosOutput[$activos[$k]];
                $activosSaldoAnterior[$activos[$k]] = $activosOutput[$activos[$k]];

            }
            $totales["activos"] = $totalActivos;

            $totalPasivos = 0;
            for ($k = 0; $k < count($pasivos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $pasivos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $pasivos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

                $pasivosOutput[$pasivos[$k]] = $deudoraValor - $acreedoraValor + $pasivosSaldoAnterior[$pasivos[$k]];
                $totalPasivos += $pasivosOutput[$pasivos[$k]];
                $pasivosSaldoAnterior[$pasivos[$k]] = $pasivosOutput[$pasivos[$k]];

            }
            $totales["pasivos"] = $totalPasivos;


            $totalPatrimonio = 0;
            for ($k = 0; $k < count($patrimonio); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $patrimonio[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $patrimonio[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );

                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

                if ($patrimonio[$k] == "3601") {//utilidades del mes anterior + utilidades o excedentes acumulados del ems anteior
                    $patrimonioOutput[$patrimonio[$k]] = $patrimonioSaldoAnterior[$patrimonio[$k]] + $patrimonioSaldoAnterior["3603"];
                    $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                    $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                } else {
                    if ($patrimonio[$k] == "3602") {

                        $patrimonioOutput[$patrimonio[$k]] = $patrimonioSaldoAnterior[$patrimonio[$k]] + $patrimonioSaldoAnterior["3604"];
                        $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                        $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                    } else {
                        if ($patrimonio[$k] == "3603") {//calcular de P y G la 3603 pygMes.totales['ingresos'] - pygMes.totales['gastos']
                            $pygMeses = $this->pyg();
                            $totalIngreso = $pygMeses[count($meses) - 1 - $i]->getTotales()["ingresos"];
                            $totalGastos = $pygMeses[count($meses) - 1 - $i]->getTotales()["gastos"];
                            $patrimonioOutput[$patrimonio[$k]] = $totalIngreso - $totalGastos > 0 ? $totalIngreso - $totalGastos : 0;

                            $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                            $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                        } else {
                            if ($patrimonio[$k] == "3604") {
                                $pygMeses = $this->pyg();
                                $totalIngreso = $pygMeses[count($meses) - 1 - $i]->getTotales()["ingresos"];
                                $totalGastos = $pygMeses[count($meses) - 1 - $i]->getTotales()["gastos"];
//                    print_r($totalIngreso - $totalGastos);


                                $patrimonioOutput[$patrimonio[$k]] = $totalIngreso - $totalGastos < 0 ? $totalIngreso - $totalGastos : 0;
                                $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                                $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                            } else {
                                $patrimonioOutput[$patrimonio[$k]] = $deudoraValor - $acreedoraValor + $patrimonioSaldoAnterior[$patrimonio[$k]];
                                $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                                $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                            }
                        }
                    }
                }


            }
            $totales["patrimonio"] = $totalPatrimonio;
//            $activosSaldoOutput["patrimonio"]=$totalPatrimonio;


            $balanceGral->setMes($meses[$i]);
            $balanceGral->setActivos($activosOutput);
            $balanceGral->setPasivos($pasivosOutput);
            $balanceGral->setPatrimonio($patrimonioOutput);
            $balanceGral->setTotales($totales);

//            echo count($pyg->getTotales());
//            die();
            $balanceGralMeses[] = $balanceGral;
        }

//            echo count($balanceGralMeses);
//            die();

        return $this->render(
            'ModulosPersonasBundle:Libro:balancegeneral.html.twig',
            array(
                'balanceGralMeses' => $balanceGralMeses,
                'nombrecaja' => $nombrecaja,
            )
        );

    }

    public
    function exportarpygAction()
    {
        $em = $this->getDoctrine()->getManager();
        $pygMeses = array();

        $cajahorro = $em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja = $cajahorro->getRazonSocial();//'nombrecaja'=>$nombrecaja,

        $dtvcAll = $em->getRepository('ModulosPersonasBundle:DTVC')->findAllDTVCAporFechasAsc();

        //$dtvcAll[0]->getIdVchr()->getFecha()->format('Y-m') ;//print_r($dtvcAll[0]->getIdVchr()->getFecha()->format('Y'));
        $meses = array();
        if (count($dtvcAll) > 0) {
            $aux = $dtvcAll[0]->getIdVchr()->getFecha()->format('Y-m');
            $meses [] = $aux;
            for ($i = 1; $i < count($dtvcAll); $i++) {
                if ($dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m') != $aux) {
                    $aux = $dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m');
                    $meses [] = $aux;
                }
            }
        }


        for ($i = 0; $i < count($meses); $i++) {
            $fechaArray = explode('-', $meses[$i]);
            $fechaInicial = new \DateTime();
            $fechaInicial->setDate($fechaArray[0], $fechaArray[1], 1);
            $cantDias = $fechaInicial->format('t');

            $fechaFinal = new \DateTime();
            $fechaFinal->setDate($fechaArray[0], $fechaArray[1], $cantDias);

            $pyg = new PYG();
            $dtvcMes = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAporFechasAsc(
                $fechaInicial,
                $fechaFinal
            );
//            for($j=0;$i<count($dtvcMes);$j++){

            $ingresosOutput = array(
                "51" => "",
                "5101" => "",
                "5104" => "",
                "5190" => "",
                "54" => "",
                "5490" => "",
                "56" => "",
                "5690" => "",
                "3603" => "",
            );
            $gastosOutput = array(
                "41" => "",
                "4101" => "",
                "4103" => "",
                "4105" => "",
                "44" => "",
                "4402" => "",
                "45" => "",
                "4501" => "",
                "4502" => "",
                "4503" => "",
                "4504" => "",
                "4507" => "",
            );
            $ingresos = array(0 => "51", "5101", "5104", "5190", "54", "5490", "56", "5690", "3603");
            $gastos = array(
                0 => "41",
                "4101",
                "4103",
                "4105",
                "44",
                "4402",
                "45",
                "4501",
                "4502",
                "4503",
                "4504",
                "4507",
            );
            $totales = array("ingresos" => "", "gastos" => "");

            $totalIngreso = 0;
            for ($k = 0; $k < count($ingresos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $ingresos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $ingresos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );

                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];
                $ingresosOutput[$ingresos[$k]] = $deudoraValor - $acreedoraValor;
                $totalIngreso += $ingresosOutput[$ingresos[$k]];
            }
            $totales["ingresos"] = $totalIngreso;

            $totalGasto = 0;
            for ($k = 0; $k < count($gastos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $gastos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $gastos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );


//
//                    $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora($gastos[$k],$fechaArray[1]);
//                    $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora($gastos[$k],$fechaArray[1]);
//                    echo "<pre>";
//                    print_r($fechaArray[1]);
//                    print_r("&nbsp;");
//                    print_r($gastos[$k]);
//                    print_r("&nbsp;");
//                    print_r($acreedora[0][1]);
//                    print_r("&nbsp;");
//                    print_r($deudora[0][1]);
//                    die();
                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

//                    $acreedoraValor= ($acreedora[0][1]==null)? 0: $acreedora[0][1];
//                    $deudoraValor= ($deudoraValor[0][1]==null)? 0: $deudora[0][1];
                $totalGasto += ($acreedoraValor - $deudoraValor);
                $gastosOutput[$gastos[$k]] = $acreedoraValor - $deudoraValor;
            }
            $totales["gastos"] = $totalGasto;

            $pyg->setMes($meses[$i]);
            $pyg->setIngresos($ingresosOutput);
            $pyg->setGastos($gastosOutput);
            $pyg->setTotales($totales);

//            echo count($pyg->getTotales());
//            die();
            $pygMeses[] = $pyg;
        }


        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Conquito")
            ->setLastModifiedBy("Conquito")
            ->setTitle("Cartera de Caja")
            ->setSubject("Cartera de Caja")
            ->setDescription("Cartera de Caja")
            ->setKeywords("Cartera de Caja")
            ->setCategory("Reporte excel");

        //$tituloReporte1 = "Listado de libros de cajas por meses de:".$fecha1->format('d-m-Y').' a '.$fecha2->format('d-m-Y');
        $tituloReporte = "BALANCE DE RESULTADOS";
        $tituloHoja = "P y G";

        $estiloTituloCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 14,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );
        $estiloSubCabCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 12,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );
        $estiloTituloColumnasCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'size' => 9,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloCeldaCartera = array(
            'font' => array(
                'name' => 'Arial',
                'bold' => false,
                'size' => 10,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloCeldaCarteraBorder = array(
            'font' => array(
                'name' => 'Arial',
                'bold' => false,
                'size' => 10,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),

        );
        $estiloDivisor = array(

            'borders' => array(
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
                    'color' => array(
                        'rgb' => 'FFFFFF',
                    ),
                ),

            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloMarcosBalance = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'size' => 9,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
        );
        $estiloCeldaDerecha = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );

        $estiloCeldaIzq = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', $tituloReporte);
        $phpExcelObject->getActiveSheet()->getStyle('A1:N1')->applyFromArray($estiloTituloCartera);

        $titulosColumnas = array(
            '5 ',
            'INGRESOS',
            '4',
            'GASTOS',
        );
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A1:N1');


        $i = 2;
        $border1 = 2;
        $border2 = 1;

        foreach ($pygMeses as $pygMes) {

            // Se agregan los titulos del reporte
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $nombrecaja);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':N'.$i)->applyFromArray($estiloSubCabCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':N'.$i);
            $i++;
            $border2++;
            $border1 += $border2;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "Del ".$pygMes->getIntervaloFecha());
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':N'.$i)->applyFromArray($estiloTituloCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':N'.$i);
            $i++;
            $border2++;

            $i++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $titulosColumnas[0]);

            $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCarteraBorder);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaIzq);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, $titulosColumnas[1]);

            $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCarteraBorder);
            $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, '');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':C'.$i);

            $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':C'.$i)->applyFromArray($estiloCeldaCarteraBorder);


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, $titulosColumnas[2]);

            $phpExcelObject->getActiveSheet()->getStyle('I'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, $titulosColumnas[3]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':K'.$i);
            $phpExcelObject->getActiveSheet()->getStyle('j'.$i.':K'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, '');

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, '');

            $i++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, 51);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, 'INTERESES Y DESCUENTOS GANADOS');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':E'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, 'INGRESOS');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue(
                    'F'.$i,
                    number_format($pygMes->getIngresos()['51'], 2, '.', '')
                );//number_format($número, 2, '.', '')
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '41');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, 'INTERESES CAUSADOS');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':L'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getGastos()['41'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, 5101);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, 'Depósitos');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':E'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, 'INGRESOS');

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, number_format($pygMes->getIngresos()['5101'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '4101');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, 'Obligaciones con el público');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':L'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getGastos()['4101'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, 5104);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, 'Intereses y descuentos de cartera de créditos ( interés más la mora)');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':E'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, 'INGRESOS');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, number_format($pygMes->getIngresos()['5104'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':L'.$i);
            $i++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, 5190);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, 'Otros Intereses y descuentos');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':E'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, number_format($pygMes->getIngresos()['5190'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '4103');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, 'Obligaciones financieras');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':L'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getGastos()['4103'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, 54);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, 'INGRESOS POR SERVICIOS');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':E'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, number_format($pygMes->getIngresos()['54'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '4105');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, 'Otros Intereses');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':L'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getGastos()['4105'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, 5490);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, 'Otros servicios');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':E'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, number_format($pygMes->getIngresos()['5490'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':L'.$i);
            $i++;
            $border2++;
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, 56);
//                $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, 'OTROS INGRESOS');
//                $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':E'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, number_format($pygMes->getIngresos()['56'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '44');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, 'PROVISIONES');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':L'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getGastos()['44'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;
            $border2++;
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, 5690);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, 'Otros');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, 'SOLICITUD');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, number_format($pygMes->getIngresos()['5690'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '4402');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, 'Cartera de crédito');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':L'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getGastos()['4402'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;
            $i++;
            $border2++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '45');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, 'GASTOS DE OPERACIÓN');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':L'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getGastos()['45'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;
            $border2++;
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '4501');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, 'Gastos de personal');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':L'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getGastos()['4501'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '4502');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, 'Honorarios');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getGastos()['4502'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '4503');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, 'Servicios varios');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getGastos()['4503'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '4504');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, 'Impuestos, contribuciones y multas');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':L'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getGastos()['4504'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, '4507');
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, 'Otros gastos');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('J'.$i.':L'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getGastos()['4507'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;
            $i++;
            $border2++;
            $border2++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, 'TOTAL INGRESOS');

            $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCarteraBorder);


            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':E'.$i);

            $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCarteraBorder);


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, number_format($pygMes->getTotales()['ingresos'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('I'.$i, 'TOTAL GASTOS');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('I'.$i.':L'.$i);

            $phpExcelObject->getActiveSheet()->getStyle('I'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, $titulosColumnas[3]);


            $phpExcelObject->getActiveSheet()->getStyle('J'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->getActiveSheet()->getStyle('K'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->getActiveSheet()->getStyle('L'.$i)->applyFromArray($estiloCeldaCarteraBorder);


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('M'.$i, number_format($pygMes->getTotales()['gastos'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('M'.$i)->applyFromArray($estiloCeldaCarteraBorder);


            $i++;
            $i++;
            $border2++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, '3603');

            $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, 'Entrga de Utilidad o excedente a Socio');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':E'.$i);

            $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCarteraBorder);
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCarteraBorder);
            $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCarteraBorder);


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, number_format($pygMes->getIngresos()['3603'], 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('I'.$i.':L'.$i);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, $titulosColumnas[3]);


            $i++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, '3603');

            $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, 'Utilidad o excedente del ejercicio');
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':E'.$i);

            $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCarteraBorder);
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCarteraBorder);
            $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCarteraBorder);


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue(
                    'F'.$i,
                    number_format(($pygMes->getTotales()['ingresos'] - $pygMes->getTotales()['gastos']), 2, '.', '')
                );
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('I'.$i.':L'.$i);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, $titulosColumnas[3]);


            $i++;
            $border2++;

            $i++;
            $i++;
            $i++;
            $border2++;
            $border2++;
            $border2++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "---------------------------");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "---------------------------");
            $i++;
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "FIRMA DEL TESORERO");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "FIRMA DEL GERENTE");

            $i++;
            $i++;

            $phpExcelObject->getActiveSheet()->getStyle('A'.($i - 27).':N'.$i)->applyFromArray(
                $estiloMarcosBalance
            );
            $phpExcelObject->getActiveSheet()->getStyle('H'.($i - 27).':N'.$i)->applyFromArray(
                $estiloMarcosBalance
            );


            $phpExcelObject->getActiveSheet()->getStyle('F'.($i - 25).':F'.($i - 3))->applyFromArray(
                $estiloCeldaDerecha
            );
            $phpExcelObject->getActiveSheet()->getStyle('M'.($i - 25).':M'.($i - 3))->applyFromArray(
                $estiloCeldaDerecha
            );

            $phpExcelObject->getActiveSheet()->getStyle('A'.($i - 26).':A'.($i - 3))->applyFromArray(
                $estiloCeldaIzq
            );
            $phpExcelObject->getActiveSheet()->getStyle('I'.($i - 26).':I'.($i - 3))->applyFromArray(
                $estiloCeldaIzq
            );
            $i++;

        }
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('A')
            ->setWidth(9);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(11);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(11);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(12);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(9);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('F')
            ->setWidth(13);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('G')
            ->setWidth(9);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('H')
            ->setWidth(9);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('I')
            ->setWidth(10);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('J')
            ->setWidth(11);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('K')
            ->setWidth(12);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('L')
            ->setWidth(10);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('M')
            ->setWidth(13);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('N')
            ->setWidth(11);


        $phpExcelObject->getActiveSheet()->setTitle($tituloHoja);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Perdidas y Ganancias (PYG).xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;

    }

    public
    function exportarbalancegralAction()
    {
        $em = $this->getDoctrine()->getManager();
        $balanceGralMeses = array();

        $cajahorro = $em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja = $cajahorro->getRazonSocial();//'nombrecaja'=>$nombrecaja,

        $dtvcAll = $em->getRepository('ModulosPersonasBundle:DTVC')->findAllDTVCAporFechasAsc();

        $meses = array();
        if (count($dtvcAll) > 0) {
            $aux = $dtvcAll[0]->getIdVchr()->getFecha()->format('Y-m');
            $meses [] = $aux;
            for ($i = 1; $i < count($dtvcAll); $i++) {
                if ($dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m') != $aux) {
                    $aux = $dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m');
                    $meses [] = $aux;
                }
            }
        }

        $activosSaldoAnterior = array(
            "11" => 0,
            "1101" => 0,
            "1103" => 0,
            "13" => 0,
            "1301" => 0,
            "14" => 0,
            "1402" => 0,
            "1404" => 0,
            "1422" => 0,
            "1424" => 0,
            "1499" => 0,
            "16" => 0,
            "1602" => 0,
            "1603" => 0,
            "1690" => 0,
            "18" => 0,
            "1801" => 0,
            "1802" => 0,
            "1805" => 0,
            "1806" => 0,
            "1807" => 0,
            "1808" => 0,
            "1890" => 0,
            "19" => 0,
            "1905" => 0,
            "1990" => 0,
        );
        $pasivosSaldoAnterior = array(
            "21" => 0,
            "2101" => 0,
            "2103" => 0,
            "2105" => 0,
            "25" => 0,
            "2503" => 0,
            "2504" => 0,
            "2505" => 0,
            "2590" => 0,
            "26" => 0,
            "2602" => 0,
            "2606" => 0,
            "29" => 0,
            "2990" => 0,
        );
        $patrimonioSaldoAnterior = array(
            "31" => 0,
            "3103" => 0,
            "34" => 0,
            "3402" => 0,
            "3490" => 0,
            "36" => 0,
            "3601" => 0,
            "3602" => 0,
            "3603" => 0,
            "3604" => 0,
        );

        for ($i = 0; $i < count($meses); $i++) {
            $fechaArray = explode('-', $meses[$i]);
            $fechaInicial = new \DateTime();
            $fechaInicial->setDate($fechaArray[0], $fechaArray[1], 1);
            $cantDias = $fechaInicial->format('t');

            $fechaFinal = new \DateTime();
            $fechaFinal->setDate($fechaArray[0], $fechaArray[1], $cantDias);

            $balanceGral = new BalanceGral();

            $activosOutput = array(
                "11" => "",
                "1101" => "",
                "1103" => "",
                "13" => "",
                "1301" => "",
                "14" => "",
                "1402" => "",
                "1404" => "",
                "1422" => "",
                "1424" => "",
                "1499" => "",
                "16" => "",
                "1602" => "",
                "1603" => "",
                "1690" => "",
                "18" => "",
                "1801" => "",
                "1802" => "",
                "1805" => "",
                "1806" => "",
                "1807" => "",
                "1808" => "",
                "1890" => "",
                "19" => "",
                "1905" => "",
                "1990" => "",
            );
            $pasivosOutput = array(
                "21" => "",
                "2101" => "",
                "2103" => "",
                "2105" => "",
                "25" => "",
                "2503" => "",
                "2504" => "",
                "2505" => "",
                "2590" => "",
                "26" => "",
                "2602" => "",
                "2606" => "",
                "29" => "",
                "2990" => "",
            );
            $patrimonioOutput = array(
                "31" => "",
                "3103" => "",
                "34" => "",
                "3402" => "",
                "3490" => "",
                "36" => "",
                "3601" => "",
                "3602" => "",
                "3603" => "",
                "3604" => "",
            );


            $activos = array(
                0 => "11",
                "1101",
                "1103",
                "13",
                "1301",
                "14",
                "1402",
                "1404",
                "1422",
                "1424",
                "1499",
                "16",
                "1602",
                "1603",
                "1690",
                "18",
                "1801",
                "1802",
                "1805",
                "1806",
                "1807",
                "1808",
                "1890",
                "19",
                "1905",
                "1990",
            );
            $pasivos = array(
                0 => "21",
                "2101",
                "2103",
                "2105",
                "25",
                "2503",
                "2504",
                "2505",
                "2590",
                "26",
                "2602",
                "2606",
                "29",
                "2990",
            );
            $patrimonio = array(0 => "31", "3103", "34", "3402", "3490", "36", "3601", "3602", "3603", "3604");
            $totales = array("activos" => "", "pasivos" => "", "patrimonio" => "");


            $totalActivos = 0;
            for ($k = 0; $k < count($activos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $activos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $activos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

                $activosOutput[$activos[$k]] = $acreedoraValor - $deudoraValor + $activosSaldoAnterior[$activos[$k]];
                $totalActivos += $activosOutput[$activos[$k]];
                $activosSaldoAnterior[$activos[$k]] = $activosOutput[$activos[$k]];

            }
            $totales["activos"] = $totalActivos;

            $totalPasivos = 0;
            for ($k = 0; $k < count($pasivos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $pasivos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $pasivos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

                $pasivosOutput[$pasivos[$k]] = $deudoraValor - $acreedoraValor + $pasivosSaldoAnterior[$pasivos[$k]];
                $totalPasivos += $pasivosOutput[$pasivos[$k]];
                $pasivosSaldoAnterior[$pasivos[$k]] = $pasivosOutput[$pasivos[$k]];

            }
            $totales["pasivos"] = $totalPasivos;


            $totalPatrimonio = 0;
            for ($k = 0; $k < count($patrimonio); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $patrimonio[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $patrimonio[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );

                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

                if ($patrimonio[$k] == "3601") {//utilidades del mes anterior + utilidades o excedentes acumulados del ems anteior
                    $patrimonioOutput[$patrimonio[$k]] = $patrimonioSaldoAnterior[$patrimonio[$k]] + $patrimonioSaldoAnterior["3603"];
                    $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                    $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                } else {
                    if ($patrimonio[$k] == "3602") {

                        $patrimonioOutput[$patrimonio[$k]] = $patrimonioSaldoAnterior[$patrimonio[$k]] + $patrimonioSaldoAnterior["3604"];
                        $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                        $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                    } else {
                        if ($patrimonio[$k] == "3603") {//calcular de P y G la 3603 pygMes.totales['ingresos'] - pygMes.totales['gastos']
                            $pygMeses = $this->pyg();
                            $totalIngreso = $pygMeses[count($meses) - 1 - $i]->getTotales()["ingresos"];
                            $totalGastos = $pygMeses[count($meses) - 1 - $i]->getTotales()["gastos"];

                            $patrimonioOutput[$patrimonio[$k]] = $totalIngreso - $totalGastos > 0 ? $totalIngreso - $totalGastos : 0;

                            $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                            $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                        } else {
                            if ($patrimonio[$k] == "3604") {
                                $pygMeses = $this->pyg();
                                $totalIngreso = $pygMeses[count($meses) - 1 - $i]->getTotales()["ingresos"];
                                $totalGastos = $pygMeses[count($meses) - 1 - $i]->getTotales()["gastos"];
//                    print_r($totalIngreso - $totalGastos);


                                $patrimonioOutput[$patrimonio[$k]] = $totalIngreso - $totalGastos < 0 ? $totalIngreso - $totalGastos : 0;
                                $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                                $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                            } else {
                                $patrimonioOutput[$patrimonio[$k]] = $deudoraValor - $acreedoraValor + $patrimonioSaldoAnterior[$patrimonio[$k]];
                                $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                                $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                            }
                        }
                    }
                }


            }
            $totales["patrimonio"] = $totalPatrimonio;
//            $activosSaldoOutput["patrimonio"]=$totalPatrimonio;


            $balanceGral->setMes($meses[$i]);
            $balanceGral->setActivos($activosOutput);
            $balanceGral->setPasivos($pasivosOutput);
            $balanceGral->setPatrimonio($patrimonioOutput);
            $balanceGral->setTotales($totales);

//            echo count($pyg->getTotales());
//            die();
            $balanceGralMeses[] = $balanceGral;
        }
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Conquito")
            ->setLastModifiedBy("Conquito")
            ->setTitle("Blance General")
            ->setSubject("Blance General")
            ->setDescription("Blance General")
            ->setKeywords("Blance General")
            ->setCategory("Reporte excel");

        //$tituloReporte1 = "Listado de libros de cajas por meses de:".$fecha1->format('d-m-Y').' a '.$fecha2->format('d-m-Y');
        $tituloReporte = "BALANCE GENERAL";
        $subTituloReporte = $nombrecaja;
        $tituloHoja = "BALANCE GENERAL";

        $estiloCeldaFuente = array(
            'font' => array(
                'name' => 'Calibri',
                'size' => 9,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
        );
        $estiloCeldaFuenteBold = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'size' => 9,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
        );
        $estiloTituloCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 14,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );
        $estiloSubCabCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 12,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );
        $estiloTituloColumnasCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'size' => 9,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloMarcosBalance = array(
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
        );
        $estiloCeldaCartera = array(
            'font' => array(
                'name' => 'Arial',
                'bold' => false,
                'size' => 10,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloColumnaCuenta = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => false,
                'size' => 9,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloDivisor = array(

            'borders' => array(
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
                    'color' => array(
                        'rgb' => 'FFFFFF',
                    ),
                ),

            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );

        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', $tituloReporte);
        $phpExcelObject->getActiveSheet()->getStyle('A1:N1')->applyFromArray($estiloTituloCartera);


        $titulosColumnas = array(
            '1 ',
            'ACTIVOS',
            '2',
            'PASIVOS',
            '3',
            'PATRIMONIO',
        );
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A1:N1');


        $i = 2;

        foreach ($balanceGralMeses as $balanceGralMes) {
            $marcoMes = $i;
            // Se agregan los titulos del reporte
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $nombrecaja);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':N'.$i)->applyFromArray($estiloTituloCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':N'.$i);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "Del ".$balanceGralMes->getIntervaloFecha());
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':N'.$i)->applyFromArray($estiloTituloCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':O'.$i);

            $phpExcelObject->getActiveSheet()->getStyle(
                'A'.($i == 3 ? ($i - 2) : ($i - 1)).':O'.$i
            )->applyFromArray($estiloMarcosBalance);
            $i++;
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $titulosColumnas[0]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, $titulosColumnas[1]);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($estiloMarcosBalance);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($estiloCeldaFuenteBold);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, $titulosColumnas[2]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, $titulosColumnas[3]);
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i.':M'.$i)->applyFromArray($estiloMarcosBalance);
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i.':M'.$i)->applyFromArray($estiloCeldaFuenteBold);


            $i++;
            $iIndice = $i;
            $cont = 1;
            //$c11 = $balanceGralMes->getActivos()["11"];
            $c1101 = $balanceGralMes->getActivos()["1101"];
            $c1103 = $balanceGralMes->getActivos()["1103"];
            $c11 = $c1101 + $c1103;

            $c1301 = $balanceGralMes->getActivos()["1301"];
            $c13 = $c1301;

            //$c14 = $balanceGralMes->getActivos()["14"];
            $c1402 = $balanceGralMes->getActivos()["1402"];
            $c1404 = $balanceGralMes->getActivos()["1404"];
            $c1422 = $balanceGralMes->getActivos()["1422"];
            $c1424 = $balanceGralMes->getActivos()["1424"];
            $c1499 = $balanceGralMes->getActivos()["1499"];
            $c14 = $c1402 + $c1404 + $c1422 + $c1424 + $c1499;

            //$c16 = $balanceGralMes->getActivos()["16"];
            $c1602 = $balanceGralMes->getActivos()["1602"];
            $c1603 = $balanceGralMes->getActivos()["1603"];
            $c1690 = $balanceGralMes->getActivos()["1690"];
            $c16 = $c1602 + $c1603 + $c1690;

            //$c18 = $balanceGralMes->getActivos()["18"];
            $c1801 = $balanceGralMes->getActivos()["1801"];
            $c1802 = $balanceGralMes->getActivos()["1802"];
            $c1805 = $balanceGralMes->getActivos()["1805"];
            $c1806 = $balanceGralMes->getActivos()["1806"];
            $c1807 = $balanceGralMes->getActivos()["1807"];
            $c1808 = $balanceGralMes->getActivos()["1808"];
            $c1890 = $balanceGralMes->getActivos()["1890"];
            $c18 = $c1801 + $c1802 + $c1805 + $c1806 + $c1807 + $c1808 + $c1890;

            //$c19 = $balanceGralMes->getActivos()["19"];
            $c1905 = $balanceGralMes->getActivos()["1905"];
            $c1990 = $balanceGralMes->getActivos()["1990"];
            $c19 = $c1905 + $c1990;

            $totalActivosExcel = $balanceGralMes->getTotales()['activos'];


            //$c21 = $balanceGralMes->getPasivos()["21"];
            $c2101 = $balanceGralMes->getPasivos()["2101"];
            $c2103 = $balanceGralMes->getPasivos()["2103"];
            $c2105 = $balanceGralMes->getPasivos()["2105"];
            $c21 = $c2101 + $c2103 + $c2105;

            //$c25 = $balanceGralMes->getPasivos()["25"];
            $c2503 = $balanceGralMes->getPasivos()["2503"];
            $c2504 = $balanceGralMes->getPasivos()["2504"];
            $c2505 = $balanceGralMes->getPasivos()["2505"];
            $c2590 = $balanceGralMes->getPasivos()["2590"];
            $c25 = $c2503 + $c2504 + $c2505 + $c2590;

            //$c26 = $balanceGralMes->getPasivos()["26"];
            $c2602 = $balanceGralMes->getPasivos()["2602"];
            $c2606 = $balanceGralMes->getPasivos()["2606"];
            $c26 = $c2602 + $c2606;

            $c29 = $balanceGralMes->getPasivos()["2990"];
            $c2990 = $balanceGralMes->getPasivos()["2990"];

            $totalPasivosExcel = $balanceGralMes->getTotales()['pasivos'];


            $c31 = $balanceGralMes->getPatrimonio()["3103"];
            $c3103 = $balanceGralMes->getPatrimonio()["3103"];

            //$c34 = $balanceGralMes->getPatrimonio()["34"];
            $c3402 = $balanceGralMes->getPatrimonio()["3402"];
            $c3490 = $balanceGralMes->getPatrimonio()["3490"];
            $c34 = $c3402 + $c3490;

            //$c36 = $balanceGralMes->getPatrimonio()["36"];
            $c3601 = $balanceGralMes->getPatrimonio()["3601"];
            $c3602 = $balanceGralMes->getPatrimonio()["3602"];
            $c3603 = $balanceGralMes->getPatrimonio()["3603"];
            $c3604 = $balanceGralMes->getPatrimonio()["3604"];
            $c36 = $c3601 + $c3602 + $c3603 + $c3604;

            $totalPatrimonioExcel = $balanceGralMes->getTotales()['patrimonio'];


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "11");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Bancos y otras Instituciones Financieras");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c11, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloCeldaFuenteBold);


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "21");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "OBLIGACIONES");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c21, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i.':M'.$i)->applyFromArray($estiloCeldaFuenteBold);
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1101");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Caja");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1101, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "2101");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Depósitos a la vista");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c2101, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1103");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Bancos y otras Instituciones Financieras");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1103, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "2103");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Depósitos a plazo fijo");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c2103, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "13");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "INVERSIONES");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c13, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloCeldaFuenteBold);


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "2105");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Depósitos restringidos");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c2105, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1301");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Para negociar de entidades del sector privado");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1301, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "25");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "CUENTAS POR PAGAR");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c25, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i.':M'.$i)->applyFromArray($estiloCeldaFuenteBold);
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "14");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "CARTERA DE CRÉDITO");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c14, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloCeldaFuenteBold);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "2503");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Obligaciones patronales");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c2503, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1402");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Cartera de créditos de consumo por vencer ");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1402, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "2504");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Retenciones");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c2504, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1404");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Cartera de crédito para la microempresa por vencer");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1404, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "2505");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "OBLIGACIONES");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c2505, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1422");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Cartera de crédito de consumo vencida");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1422, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "2590");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Cuentas por pagar varias");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c2590, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1424");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Cartera de crédito para la microempresa vencida");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1424, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "26");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "OBLIGACIONES FINANCIERAS");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c26, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i.':M'.$i)->applyFromArray($estiloCeldaFuenteBold);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1499");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "(Provisiones para créditos incobrables)");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1499, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "2602");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Obligaciones con Instituciones financieras del país");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c2602, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "16");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "CUENTAS POR COBRAR");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c16, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloCeldaFuenteBold);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "2606");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Obligaciones con entidades financieras del sector privado");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c2606, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1602");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Intereses por cobrar de inversiones");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1602, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "29");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "OTROS PASIVOS");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c29, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i.':M'.$i)->applyFromArray($estiloCeldaFuenteBold);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1603");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Intereses por cobrar de cartera de créditos");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1603, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "2990");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Otros");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c2990, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1690");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Cuentas por cobrar varias");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1690, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Total pasivos");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($totalPasivosExcel, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('K'.$i.':M'.$i)->applyFromArray($estiloMarcosBalance);
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloMarcosBalance);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('K'.$i.':M'.$i);
            $phpExcelObject->getActiveSheet()->getStyle('K'.$i.':N'.$i)->applyFromArray($estiloCeldaFuenteBold);

            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "18");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "PROPIEDAD Y EQUIPOS");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c18, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloCeldaFuenteBold);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, " ");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, " ");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, " ");

            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1801");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Terrenos");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1801, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "3");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "PATRIMONIO");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, " ");
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i.':M'.$i)->applyFromArray($estiloMarcosBalance);
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i.':M'.$i)->applyFromArray($estiloCeldaFuenteBold);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1802");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Edificios");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1802, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "31");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "CAPITAL SOCIAL");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c31, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i.':M'.$i)->applyFromArray($estiloCeldaFuenteBold);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1805");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Muebles, enseres y equipos de oficina");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1805, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "3103");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Aportes de socios");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c3103, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1806");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Equipos de computación");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1806, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "34");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "OTROS APORTES PATRIMONIALES");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c34, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i.':M'.$i)->applyFromArray($estiloCeldaFuenteBold);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1807");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Unidades de Transporte");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1807, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "3402");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Donaciones");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c3402, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1808");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Equipos de construcción");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1808, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "3490");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Otros (Cap. De Utilidades)");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c3490, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1890");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Otros");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1890, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "36");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "RESULTADOS");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c36, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('J'.$i.':M'.$i)->applyFromArray($estiloCeldaFuenteBold);
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "19");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "OTROS ACTIVOS");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c19, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloCeldaFuenteBold);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "3601");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Utilidades o excedentes acumuladas");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c3601, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1905");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Gastos diferidos");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1905, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "3602");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Perdidas acumuladas");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c3602, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "1990");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Otros");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($c1990, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "3603");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Utilidad o excedente del ejercicio");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c3603, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, " ");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, " ");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, " ");

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('J'.$i, "3604");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "(Perdidas del ejercicio)");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($c3604, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloCeldaCartera);
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, " ");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, " ");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, " ");

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Total Patrimonio");
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('K'.$i.':M'.$i);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format($totalPatrimonioExcel, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('K'.$i.':M'.$i)->applyFromArray($estiloMarcosBalance);
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloMarcosBalance);
            $phpExcelObject->getActiveSheet()->getStyle('K'.$i.':M'.$i)->applyFromArray($estiloCeldaFuenteBold);

            $i++;
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, " ");
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "Total Activos");
            $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':F'.$i)->applyFromArray($estiloMarcosBalance);
            $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':G'.$i)->applyFromArray($estiloCeldaFuenteBold);

            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':F'.$i);


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, number_format($totalActivosExcel, 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloMarcosBalance);


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "Total Pasivo + Patrimonio");
            $phpExcelObject->getActiveSheet()->getStyle('K'.$i.':M'.$i)->applyFromArray($estiloMarcosBalance);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('K'.$i.':M'.$i);
            $phpExcelObject->getActiveSheet()->getStyle('K'.$i.':M'.$i)->applyFromArray($estiloCeldaFuenteBold);


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('N'.$i, number_format(($totalPasivosExcel + $totalPatrimonioExcel), 2, '.', ''));
            $phpExcelObject->getActiveSheet()->getStyle('N'.$i)->applyFromArray($estiloMarcosBalance);
            $phpExcelObject->getActiveSheet()->getStyle('K'.$i.':N'.$i)->applyFromArray($estiloCeldaFuenteBold);


            $i++;
            $i++;
            $i++;
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, "---------------------------");
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('C'.$i.':D'.$i);
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i.':D'.$i)->applyFromArray($estiloCeldaFuenteBold);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "---------------------------");
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('K'.$i.':L'.$i);
            $phpExcelObject->getActiveSheet()->getStyle('K'.$i.':L'.$i)->applyFromArray($estiloCeldaFuenteBold);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, "FIRMA DEL TESORERO");
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('C'.$i.':D'.$i);
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i.':D'.$i)->applyFromArray($estiloCeldaFuenteBold);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('K'.$i, "FIRMA DEL GERENTE");
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('K'.$i.':L'.$i);
            $phpExcelObject->getActiveSheet()->getStyle('K'.$i.':L'.$i)->applyFromArray($estiloCeldaFuenteBold);


            $i++;

//            $phpExcelObject->getActiveSheet()->getStyle('A1:I'.$marcoMes)->applyFromArray($estiloMarcosBalance);
            $phpExcelObject->getActiveSheet()->getStyle('A'.($i - 37).':H'.$i)->applyFromArray(
                $estiloMarcosBalance
            );
            $phpExcelObject->getActiveSheet()->getStyle('I'.($i - 37).':O'.$i)->applyFromArray(
                $estiloMarcosBalance
            );
            $phpExcelObject->getActiveSheet()->getStyle('A'.($i - 37).':O'.$i)->applyFromArray($estiloCeldaFuente);


            $phpExcelObject->getActiveSheet()->getStyle('A'.($i - 37).':A'.($i - 2))->applyFromArray(
                $estiloColumnaCuenta
            );
            $phpExcelObject->getActiveSheet()->getStyle('J'.($i - 37).':J'.($i - 2))->applyFromArray(
                $estiloColumnaCuenta
            );

            $i++;

        }


        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('A')
            ->setWidth(7);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(15);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(15);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(15);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(6);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('F')
            ->setWidth(6);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('G')
            ->setWidth(6);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('H')
            ->setWidth(6);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('I')
            ->setWidth(8);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('J')
            ->setWidth(8);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('K')
            ->setWidth(15);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('L')
            ->setWidth(14);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('M')
            ->setWidth(17);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('N')
            ->setWidth(8);


        $phpExcelObject->getActiveSheet()->setTitle($tituloHoja);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Balance General.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;

    }

    public
    function pyg()
    {
        $em = $this->getDoctrine()->getManager();
        $pygMeses = array();

        $dtvcAll = $em->getRepository('ModulosPersonasBundle:DTVC')->findAllDTVCAporFechasDesc();

        //$dtvcAll[0]->getIdVchr()->getFecha()->format('Y-m') ;//print_r($dtvcAll[0]->getIdVchr()->getFecha()->format('Y'));
        $meses = array();
        if (count($dtvcAll) > 0) {
            $aux = $dtvcAll[0]->getIdVchr()->getFecha()->format('Y-m');
            $meses [] = $aux;
            for ($i = 1; $i < count($dtvcAll); $i++) {
                if ($dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m') != $aux) {
                    $aux = $dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m');
                    $meses [] = $aux;
                }
            }
        }


        for ($i = 0; $i < count($meses); $i++) {
            $fechaArray = explode('-', $meses[$i]);
            $fechaInicial = new \DateTime();
            $fechaInicial->setDate($fechaArray[0], $fechaArray[1], 1);
            $cantDias = $fechaInicial->format('t');

            $fechaFinal = new \DateTime();
            $fechaFinal->setDate($fechaArray[0], $fechaArray[1], $cantDias);

            $pyg = new PYG();
            $dtvcMes = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAporFechasDesc(
                $fechaInicial,
                $fechaFinal
            );
//            for($j=0;$i<count($dtvcMes);$j++){

            $ingresosOutput = array(
                "51" => "",
                "5101" => "",
                "5104" => "",
                "5190" => "",
                "54" => "",
                "5490" => "",
                "56" => "",
                "5690" => "",
                "3603" => "",
            );
            $gastosOutput = array(
                "41" => "",
                "4101" => "",
                "4103" => "",
                "4105" => "",
                "44" => "",
                "4402" => "",
                "45" => "",
                "4501" => "",
                "4502" => "",
                "4503" => "",
                "4504" => "",
                "4507" => "",
            );
            $ingresos = array(0 => "51", "5101", "5104", "5190", "54", "5490", "56", "5690", "3603");
            $gastos = array(
                0 => "41",
                "4101",
                "4103",
                "4105",
                "44",
                "4402",
                "45",
                "4501",
                "4502",
                "4503",
                "4504",
                "4507",
            );
            $totales = array("ingresos" => "", "gastos" => "");

            $totalIngreso = 0;
            for ($k = 0; $k < count($ingresos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $ingresos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $ingresos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );

                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];
                $ingresosOutput[$ingresos[$k]] = $deudoraValor - $acreedoraValor;
                $totalIngreso += $ingresosOutput[$ingresos[$k]];
            }
            $totales["ingresos"] = $totalIngreso;

            $totalGasto = 0;
            for ($k = 0; $k < count($gastos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $gastos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $gastos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );


//
//                    $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora($gastos[$k],$fechaArray[1]);
//                    $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora($gastos[$k],$fechaArray[1]);
//                    echo "<pre>";
//                    print_r($fechaArray[1]);
//                    print_r("&nbsp;");
//                    print_r($gastos[$k]);
//                    print_r("&nbsp;");
//                    print_r($acreedora[0][1]);
//                    print_r("&nbsp;");
//                    print_r($deudora[0][1]);
//                    die();
                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

//                    $acreedoraValor= ($acreedora[0][1]==null)? 0: $acreedora[0][1];
//                    $deudoraValor= ($deudoraValor[0][1]==null)? 0: $deudora[0][1];
                $totalGasto += ($acreedoraValor - $deudoraValor);
                $gastosOutput[$gastos[$k]] = $acreedoraValor - $deudoraValor;
            }
            $totales["gastos"] = $totalGasto;

            $pyg->setMes($meses[$i]);
            $pyg->setIngresos($ingresosOutput);
            $pyg->setGastos($gastosOutput);
            $pyg->setTotales($totales);

//            echo count($pyg->getTotales());
//            die();
            $pygMeses[] = $pyg;
        }

        return $pygMeses;
    }

    public function aportesocioAction()
    {
        $aporteMeses = array();
        $totalesmeses = array();
        $em = $this->getDoctrine()->getManager();

        $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findBy(
            array("productocontableid" => '1')
        );
        if (count($librosOrdenados) > 0) {

            $mes = $librosOrdenados[0]->getFecha()->format('Y-m');
            $aporteMes = new AporteMes();
            $aporteMes->setMes($mes);

            $arrayValoresMes = array();
            $arrayMeses = array();

            for ($i = 0; $i < count($librosOrdenados); $i++) {
                if ($librosOrdenados[$i]->getFecha()->format('Y-m') != $mes) {
                    $arrayMeses[] = array($mes, $arrayValoresMes);
                    $mes = $librosOrdenados[$i]->getFecha()->format('Y-m');
                    $arrayValoresMes = array();
                    $arrayValoresMes[] = $librosOrdenados[$i];
                } else {
                    $arrayValoresMes[] = $librosOrdenados[$i];
                }

            }
            $arrayMeses[] = array($mes, $arrayValoresMes);


            $aporte = new Aporte();
            $aporteMeses = array();
            for ($i = 0; $i < count($arrayMeses); $i++) {
                $aporteMes = new AporteMes();
                $aporteMes->setMes($arrayMeses[$i][0]);
                $personasMes = array();
                $persona = $arrayMeses[$i][1][0]->getPersona();
                $aportePersonaMes = new AportePersonaMes();
                $aportePersonaMes->setPersona($persona);

                for ($j = 0; $j < count($arrayMeses[$i][1]); $j++) {

                    $libro = $arrayMeses[$i][1][$j];
                    if ($persona->getId() == $libro->getPersona()->getId()) {
                        if ($libro->getProductoContableId()->getTipo() == "Aporte Monetario Mensual") {
                            $aportePersonaMes->setCapitalPagado(
                                $aportePersonaMes->getCapitalPagado() + $libro->getDebe()
                            );
                        }
                    } else {
                        $aportePersonaMes->setSaldoAnterior(
                            $aporte->getSaldoAnterior($aporteMeses, $aporteMes, $aportePersonaMes->getPersona())
                        );

                        $aportePersonaMes->setCreditoMicroEmpPorVencerSaldoCap(
                            $aportePersonaMes->getCapitalPagado() + $aportePersonaMes->getSaldoAnterior()
                        );

                        $aportePersonaMes->updateTotalPagado();

                        $personasMes[] = $aportePersonaMes;

                        $persona = $libro->getPersona();

                        $aportePersonaMes = new AportePersonaMes();
                        $aportePersonaMes->setPersona($libro->getPersona());

                        if ($libro->getProductoContableId()->getTipo() == "Aporte Monetario Mensual") {
                            $aportePersonaMes->setCapitalPagado(
                                $aportePersonaMes->getCapitalPagado() + $libro->getDebe()
                            );
                        }

                    }

                }

                $aportePersonaMes->setSaldoAnterior(
                    $aporte->getSaldoAnterior($aporteMeses, $aporteMes, $aportePersonaMes->getPersona())
                );
                //saldo_anterior - capital_pagado + creditos_del_mes

                $aportePersonaMes->setCreditoMicroEmpPorVencerSaldoCap(
                    $aportePersonaMes->getCapitalPagado() + $aportePersonaMes->getSaldoAnterior()
                );

                $aportePersonaMes->updateTotalPagado();
                $personasMes[] = $aportePersonaMes;

                $aporteMes->setPersonaMes($personasMes);
                $aporteMeses[] = $aporteMes;
            }


        }

        //calcular totales

        foreach ($aporteMeses as $aporteMes) {
            $personaTotal = new AportePersonaMes();
            foreach ($aporteMes->getPersonasMes() as $aportePersonaMes) {

                $personaTotal->setSaldoAnterior(
                    $personaTotal->getSaldoAnterior() + $aportePersonaMes->getSaldoAnterior()
                );
                $personaTotal->setCapitalPagado(
                    $personaTotal->getCapitalPagado() + $aportePersonaMes->getCapitalPagado()
                );
                $personaTotal->setTotalPagado($personaTotal->getTotalPagado() + $aportePersonaMes->getTotalPagado());
            }
            $aporteMes->setTotalesMes($personaTotal);
        }

        return $this->render(
            'ModulosPersonasBundle:Libro:aportesocio.html.twig',
            array(
                'carterameses' => $aporteMeses,
            )
        );
    }


    public function exportarAporteAction()
    {
        $aporteMeses = array();
        $totalesmeses = array();
        $em = $this->getDoctrine()->getManager();

        $cajahorro = $em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja = $cajahorro->getRazonSocial();//'nombrecaja'=>$nombrecaja,

        $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findBy(
            array("productocontableid" => '1')
        );
        if (count($librosOrdenados) > 0) {

            $mes = $librosOrdenados[0]->getFecha()->format('Y-m');
            $aporteMes = new AporteMes();
            $aporteMes->setMes($mes);

            $arrayValoresMes = array();
            $arrayMeses = array();

            for ($i = 0; $i < count($librosOrdenados); $i++) {
                if ($librosOrdenados[$i]->getFecha()->format('Y-m') != $mes) {
                    $arrayMeses[] = array($mes, $arrayValoresMes);
                    $mes = $librosOrdenados[$i]->getFecha()->format('Y-m');
                    $arrayValoresMes = array();
                    $arrayValoresMes[] = $librosOrdenados[$i];
                } else {
                    $arrayValoresMes[] = $librosOrdenados[$i];
                }

            }
            $arrayMeses[] = array($mes, $arrayValoresMes);


            $aporte = new Aporte();
            $aporteMeses = array();
            for ($i = 0; $i < count($arrayMeses); $i++) {
                $aporteMes = new AporteMes();
                $aporteMes->setMes($arrayMeses[$i][0]);
                $personasMes = array();
                $persona = $arrayMeses[$i][1][0]->getPersona();
                $aportePersonaMes = new AportePersonaMes();
                $aportePersonaMes->setPersona($persona);

                for ($j = 0; $j < count($arrayMeses[$i][1]); $j++) {

                    $libro = $arrayMeses[$i][1][$j];
                    if ($persona->getId() == $libro->getPersona()->getId()) {
                        if ($libro->getProductoContableId()->getTipo() == "Aporte Monetario Mensual") {
                            $aportePersonaMes->setCapitalPagado(
                                $aportePersonaMes->getCapitalPagado() + $libro->getDebe()
                            );
                        }
                    } else {
                        $aportePersonaMes->setSaldoAnterior(
                            $aporte->getSaldoAnterior($aporteMeses, $aporteMes, $aportePersonaMes->getPersona())
                        );

                        $aportePersonaMes->setCreditoMicroEmpPorVencerSaldoCap(
                            $aportePersonaMes->getCapitalPagado() + $aportePersonaMes->getSaldoAnterior()
                        );

                        $aportePersonaMes->updateTotalPagado();

                        $personasMes[] = $aportePersonaMes;

                        $persona = $libro->getPersona();

                        $aportePersonaMes = new AportePersonaMes();
                        $aportePersonaMes->setPersona($libro->getPersona());

                        if ($libro->getProductoContableId()->getTipo() == "Aporte Monetario Mensual") {
                            $aportePersonaMes->setCapitalPagado(
                                $aportePersonaMes->getCapitalPagado() + $libro->getDebe()
                            );
                        }

                    }

                }

                $aportePersonaMes->setSaldoAnterior(
                    $aporte->getSaldoAnterior($aporteMeses, $aporteMes, $aportePersonaMes->getPersona())
                );

                $aportePersonaMes->setCreditoMicroEmpPorVencerSaldoCap(
                    $aportePersonaMes->getCapitalPagado() + $aportePersonaMes->getSaldoAnterior()
                );

                $aportePersonaMes->updateTotalPagado();
                $personasMes[] = $aportePersonaMes;

                $aporteMes->setPersonaMes($personasMes);
                $aporteMeses[] = $aporteMes;
            }


        }


        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Conquito")
            ->setLastModifiedBy("Conquito")
            ->setTitle("Aportes de Socios")
            ->setSubject("Aportes de Socios")
            ->setDescription("Aportes de Socios")
            ->setKeywords("Aportes de Socios")
            ->setCategory("Reporte excel");

        //$tituloReporte1 = "Listado de libros de cajas por meses de:".$fecha1->format('d-m-Y').' a '.$fecha2->format('d-m-Y');
        $tituloReporte = "LIBRO GENERAL DE APORTES DE SOCIOS";
        $tituloHoja = "Aportes de Socios";

        $estiloTituloCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 14,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );
        $estiloSubCabCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 12,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );
        $estiloTituloColumnasCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'size' => 9,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloCeldaCartera = array(
            'font' => array(
                'name' => 'Arial',
                'bold' => false,
                'size' => 10,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloDivisor = array(

            'borders' => array(
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
                    'color' => array(
                        'rgb' => 'FFFFFF',
                    ),
                ),

            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', $tituloReporte);
        $phpExcelObject->getActiveSheet()->getStyle('A1:F1')->applyFromArray($estiloTituloCartera);

        $titulosColumnas = array(
            'No ',
            'Nombre y Apellidos',
            'Saldo Anterior',
            'Aporte',
            'Retiro de Aportes',
            'Saldo Final',
        );
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A1:F1');


        $i = 4;

        foreach ($aporteMeses as $carteraM) {

            // Se agregan los titulos del reporte
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $nombrecaja);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($estiloSubCabCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':F'.$i);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "Del ".$carteraM->getIntervaloFecha());
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($estiloTituloCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':F'.$i);
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $titulosColumnas[0]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':A'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':A'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, $titulosColumnas[1]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':B'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':B'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, $titulosColumnas[2]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('C'.$i.':C'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i.':C'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D'.$i, $titulosColumnas[3]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('D'.$i.':D'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('D'.$i.':D'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E'.$i, $titulosColumnas[4]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('E'.$i.':E'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('E'.$i.':E'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, "Aportes de Capital");
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('F'.$i.':F'.($i));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i.':F'.$i)->applyFromArray($estiloTituloColumnasCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.($i + 1), $titulosColumnas[5]);
            $phpExcelObject->getActiveSheet()->getStyle('F'.($i + 1))->applyFromArray($estiloTituloColumnasCartera);
//
            $i++;
            $i++;
            $iIndice = $i;
            $cont = 1;
            foreach ($carteraM->getPersonasMes() as $personaM) {
                $personaNombre = $personaM->getNombreCompleto();
                $saldoAnterior = $personaM->getSaldoAnterior();
                $capitalPagado = $personaM->getCapitalPagado();
                $retiro = $personaM->getRetiroAportes();
                $totalPagado = $personaM->getTotalPagado();

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $cont++);
                $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('B'.$i, $personaNombre);
                $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C'.$i, $saldoAnterior);
                $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('D'.$i, $capitalPagado);
                $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('E'.$i, $retiro);
                $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('F'.$i, $totalPagado);
                $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);

                $i++;
            }
            //totales
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, " ");
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, "TOTAL");
            $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, '=SUM(C'.$iIndice.':C'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D'.$i, '=SUM(D'.$iIndice.':D'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E'.$i, '=SUM(E'.$iIndice.':E'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, '=SUM(F'.$iIndice.':F'.($i - 1).')');
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);


            $i++;


            $phpExcelObject->getActiveSheet()->getStyle('A'.$i++.':F'.$i)->applyFromArray($estiloDivisor);
            $i++;


        }
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('A')
            ->setWidth(5);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(40);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(20);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(20);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(20);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('F')
            ->setWidth(20);

        //$phpExcelObject->getActiveSheet()->getStyle('A3:H3')->applyFromArray($estiloTituloColumnas);
        //$phpExcelObject->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:W".($i-1));
//        for ($i = 'A'; $i <= 'H'; $i++) {
//            $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension($i)->setAutoSize(true);
//        }

        $phpExcelObject->getActiveSheet()->setTitle($tituloHoja);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Libro de aportes.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;

    }

    public function utilidadAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cajahorro = $em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja = $cajahorro->getRazonSocial();//'nombrecaja'=>$nombrecaja,

        $pygMeses = array();
        $totalIngreso = array();
        $totalGasto = array();
        $ingresoFinal = 0;
        $gastoFinal = 0;

        $dtvcAll = $em->getRepository('ModulosPersonasBundle:DTVC')->findAllDTVCAporFechasDesc();

        $meses = array();
        if (count($dtvcAll) > 0) {
            $aux = $dtvcAll[0]->getIdVchr()->getFecha()->format('Y-m');
            $meses [] = $aux;
            for ($i = 1; $i < count($dtvcAll); $i++) {
                if ($dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m') != $aux) {
                    $aux = $dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m');
                    $meses [] = $aux;
                }
            }
        }


        for ($i = 0; $i < count($meses); $i++) {
            $fechaArray = explode('-', $meses[$i]);
            $fechaInicial = new \DateTime();
            $fechaInicial->setDate($fechaArray[0], $fechaArray[1], 1);
            $cantDias = $fechaInicial->format('t');

            $fechaFinal = new \DateTime();
            $fechaFinal->setDate($fechaArray[0], $fechaArray[1], $cantDias);

            $pyg = new PYG();
            $dtvcMes = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAporFechasDesc(
                $fechaInicial,
                $fechaFinal
            );

            $ingresosOutput = array(
                "51" => "",
                "5101" => "",
                "5104" => "",
                "5190" => "",
                "54" => "",
                "5490" => "",
                "56" => "",
                "5690" => "",
                "3603" => "",
            );
            $gastosOutput = array(
                "41" => "",
                "4101" => "",
                "4103" => "",
                "4105" => "",
                "44" => "",
                "4402" => "",
                "45" => "",
                "4501" => "",
                "4502" => "",
                "4503" => "",
                "4504" => "",
                "4507" => "",
            );
            $ingresos = array(0 => "51", "5101", "5104", "5190", "54", "5490", "56", "5690", "3603");
            $gastos = array(
                0 => "41",
                "4101",
                "4103",
                "4105",
                "44",
                "4402",
                "45",
                "4501",
                "4502",
                "4503",
                "4504",
                "4507",
            );
            $totales = array("ingresos" => "", "gastos" => "");

            $totalIngreso[$i] = 0;
            for ($k = 0; $k < count($ingresos); $k++) {

                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $ingresos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $ingresos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );

                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];
                $totalIngreso[$i] += ($deudoraValor - $acreedoraValor);
                $ingresosOutput[$ingresos[$k]] = $deudoraValor - $acreedoraValor;

            }
            $totales["ingresos"] = $totalIngreso[$i];

            $ingresoFinal += $totalIngreso[$i];

            $totalGasto[$i] = 0;
            for ($k = 0; $k < count($gastos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $gastos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $gastos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );

                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

                $totalGasto[$i] = ($acreedoraValor - $deudoraValor);
                $gastosOutput[$gastos[$k]] = $acreedoraValor - $deudoraValor;
            }
            $totales["gastos"] = $totalGasto[$i];

            $gastoFinal += $totalGasto[$i];

            $pyg->setMes($meses[$i]);
            $pyg->setIngresos($ingresosOutput);
            $pyg->setGastos($gastosOutput);
            $pyg->setTotales($totales);
            //$pyg->setTotalUtil($totalUtilidad);

            $pygMeses[] = $pyg;

        }

        return $this->render(
            'ModulosPersonasBundle:Libro:utilidad.html.twig',
            array(
                'pygMeses' => $pygMeses,
                'totalIngreso' => $ingresoFinal,
                'totalGasto' => $gastoFinal,
                'nombrecaja' => $nombrecaja,
            )
        );

    }

    public function exportarUtilidadAction()
    {
        $em = $this->getDoctrine()->getManager();
        $pygMeses = array();
        $totalIngreso = array();
        $totalGasto = array();
        $ingresoFinal = 0;
        $gastoFinal = 0;

        $cajahorro = $em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja = $cajahorro->getRazonSocial();//'nombrecaja'=>$nombrecaja,

        $dtvcAll = $em->getRepository('ModulosPersonasBundle:DTVC')->findAllDTVCAporFechasDesc();

        $meses = array();
        if (count($dtvcAll) > 0) {
            $aux = $dtvcAll[0]->getIdVchr()->getFecha()->format('Y-m');
            $meses [] = $aux;
            for ($i = 1; $i < count($dtvcAll); $i++) {
                if ($dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m') != $aux) {
                    $aux = $dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m');
                    $meses [] = $aux;
                }
            }
        }


        for ($i = 0; $i < count($meses); $i++) {
            $fechaArray = explode('-', $meses[$i]);
            $fechaInicial = new \DateTime();
            $fechaInicial->setDate($fechaArray[0], $fechaArray[1], 1);
            $cantDias = $fechaInicial->format('t');

            $fechaFinal = new \DateTime();
            $fechaFinal->setDate($fechaArray[0], $fechaArray[1], $cantDias);

            $pyg = new PYG();
            $dtvcMes = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAporFechasDesc(
                $fechaInicial,
                $fechaFinal
            );

            $ingresosOutput = array(
                "51" => "",
                "5101" => "",
                "5104" => "",
                "5190" => "",
                "54" => "",
                "5490" => "",
                "56" => "",
                "5690" => "",
            );
            $gastosOutput = array(
                "41" => "",
                "4101" => "",
                "4103" => "",
                "4105" => "",
                "44" => "",
                "4402" => "",
                "45" => "",
                "4501" => "",
                "4502" => "",
                "4503" => "",
                "4504" => "",
                "4507" => "",
            );
            $ingresos = array(0 => "51", "5101", "5104", "5190", "54", "5490", "56", "5690");
            $gastos = array(
                0 => "41",
                "4101",
                "4103",
                "4105",
                "44",
                "4402",
                "45",
                "4501",
                "4502",
                "4503",
                "4504",
                "4507",
            );
            $totales = array("ingresos" => "", "gastos" => "");

            $totalIngreso[$i] = 0;
            for ($k = 0; $k < count($ingresos); $k++) {

                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $ingresos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $ingresos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );

                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];
                $totalIngreso[$i] += ($deudoraValor - $acreedoraValor);
                $ingresosOutput[$ingresos[$k]] = $deudoraValor - $acreedoraValor;

            }
            $totales["ingresos"] = $totalIngreso[$i];

            $ingresoFinal += $totalIngreso[$i];

            $totalGasto[$i] = 0;
            for ($k = 0; $k < count($gastos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $gastos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $gastos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );

                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

                $totalGasto[$i] = ($acreedoraValor - $deudoraValor);
                $gastosOutput[$gastos[$k]] = $acreedoraValor - $deudoraValor;
            }
            $totales["gastos"] = $totalGasto[$i];

            $gastoFinal += $totalGasto[$i];

            $pyg->setMes($meses[$i]);
            $pyg->setIngresos($ingresosOutput);
            $pyg->setGastos($gastosOutput);
            $pyg->setTotales($totales);
            //$pyg->setTotalUtil($totalUtilidad);

            $pygMeses[] = $pyg;

        }


        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Conquito")
            ->setLastModifiedBy("Conquito")
            ->setTitle("Utilidades P y G")
            ->setSubject("Utilidades P y G")
            ->setDescription("Utilidades P y G")
            ->setKeywords("Utilidades P y G")
            ->setCategory("Reporte excel");

        //$tituloReporte1 = "Listado de libros de cajas por meses de:".$fecha1->format('d-m-Y').' a '.$fecha2->format('d-m-Y');
        $tituloReporte = "ESTADO DE PERDIDAS Y GANANCIAS ANUALES (P Y G)";
        $tituloHoja = "Utilidades P y G";

        $estiloTituloCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 14,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );
        $estiloSubCabCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'italic' => false,
                'strike' => false,
                'size' => 12,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => true,
            ),
        );
        $estiloTituloColumnasCartera = array(
            'font' => array(
                'name' => 'Calibri',
                'bold' => true,
                'size' => 9,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloCeldaCartera = array(
            'font' => array(
                'name' => 'Arial',
                'bold' => false,
                'size' => 10,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'left' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
                'right' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000',
                    ),
                ),
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );
        $estiloDivisor = array(

            'borders' => array(
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
                    'color' => array(
                        'rgb' => 'FFFFFF',
                    ),
                ),

            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap' => true,
            ),
        );

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', $tituloReporte);
        $phpExcelObject->getActiveSheet()->getStyle('A1:E1')->applyFromArray($estiloTituloCartera);

        $titulosColumnas = array(
            'No ',
            'FECHA',
            'INGRESOS',
            'GASTO',
            'UTILIDADES',

        );
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A1:E1');


        $i = 4;


        // Se agregan los titulos del reporte
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $nombrecaja);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($estiloSubCabCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A'.$i.':E'.$i);
        $i++;

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $titulosColumnas[0]);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A'.$i.':A'.($i + 1));
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':A'.($i + 1))->applyFromArray($estiloTituloColumnasCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, $titulosColumnas[1]);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('B'.$i.':B'.($i + 1));
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':B'.($i + 1))->applyFromArray($estiloTituloColumnasCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, $titulosColumnas[2]);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('C'.$i.':C'.($i + 1));
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i.':C'.($i + 1))->applyFromArray($estiloTituloColumnasCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, $titulosColumnas[3]);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('D'.$i.':D'.($i + 1));
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i.':D'.($i + 1))->applyFromArray($estiloTituloColumnasCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('E'.$i, $titulosColumnas[4]);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('E'.$i.':E'.($i + 1));
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i.':E'.($i + 1))->applyFromArray($estiloTituloColumnasCartera);

        //
        $i++;
        $i++;
        $iIndice = $i;
        $cont = 1;
        foreach ($pygMeses as $pyg) {
            $fecha = $pyg->getIntervaloFecha();
            $ingresos = $totales["ingresos"];
            $gastos = $totales["gastos"];
            $utilidad = $totales["ingresos"] - $totales["gastos"];

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $cont++);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, $fecha);
            $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':B'.($i + 1))->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, $ingresos);
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i.':C'.($i + 1))->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D'.$i, $gastos);
            $phpExcelObject->getActiveSheet()->getStyle('D'.$i.':D'.($i + 1))->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E'.$i, $utilidad);
            $phpExcelObject->getActiveSheet()->getStyle('E'.$i.':E'.($i + 1))->applyFromArray($estiloCeldaCartera);

            $i++;
        }
        //totales
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, " ");
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B'.$i, "TOTAL");
        $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':E'.$i)->applyFromArray($estiloTituloColumnasCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C'.$i, '=SUM(C'.$iIndice.':C'.($i - 1).')');
        $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('D'.$i, '=SUM(D'.$iIndice.':D'.($i - 1).')');
        $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('E'.$i, '=SUM(E'.$iIndice.':E'.($i - 1).')');
        $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);


        $i++;


        $phpExcelObject->getActiveSheet()->getStyle('A'.$i++.':E'.$i)->applyFromArray($estiloDivisor);
        $i++;


        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('A')
            ->setWidth(5);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(40);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(20);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(20);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(20);

        $phpExcelObject->getActiveSheet()->setTitle($tituloHoja);

        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Libro de Utilidades P y G.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;

    }

    /*public function estadosfinancierosAction()
    {

///////////////////////////////////////////////////////CARTERA GENERAL//////////////////////////////////////////////////////////////////
        $arrayMeses = array();
        $arrayMesesTexto = array();
        $salidaCreditos = array();

        $em = $this->getDoctrine()->getManager();

        $cajahorro = $em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja = $cajahorro->getRazonSocial();

        $personas = $em->getRepository('ModulosPersonasBundle:Persona')->findOrdenados();

        $cantPersonas = count($personas);

        $mesesMap = [
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
        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosASCEND();
        //$libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosSinFechaTipoCartera();
        if (count($libros) > 0) {
            $fecha1 = $libros[0]->getFecha();
            $fecha2 = $libros[count($libros) - 1]->getFecha();;

            $aux = "0:0:0";
            $fecha1 = $fecha1->format('Y-m-d');
            $fechaaux = $fecha1." ".$aux;
            $fecha1 = new \DateTime($fechaaux);
            $fecha1->setDate($fecha1->format('Y'), $fecha1->format('m'), 1);

            $aux2 = "23:59:59";
            $fecha2 = $fecha2->format('Y-m-d');
            $fechaaux2 = $fecha2." ".$aux2;
            $fecha2 = new \DateTime($fechaaux2);
            $aux1 = $fecha2->format('t');
            $fecha2->setDate($fecha2->format('Y'), $fecha2->format('m'), $aux1);

        } else {
            return $this->render(
                'ModulosPersonasBundle:Libro:estadosfinancieros.html.twig',
                array(
                    'meses' => $arrayMeses,
                    'mesesTexto' => $arrayMesesTexto,
                    'creditos' => $salidaCreditos,
                    'nombrecaja' => $nombrecaja,

                )
            );
        }

        $intervalo = new \DateInterval('P1M');
        $fecha1Iterante = new \DateTime($fecha1->format('Y-m-d'));
        $valores = array();

        while ($fecha1Iterante <= $fecha2) {
            $arrayMeses[] = $fecha1Iterante->format('Y-m');
            $arrayMesesTexto[$fecha1Iterante->format('Y-m')] = $mesesMap[$fecha1Iterante->format(
                    'm'
                )]."-".$fecha1Iterante->format('Y');
            $valores[$fecha1Iterante->format('Y-m')] = 0;
            //$valoresS[$fecha1Iterante->format('Y-m')]="";
            $fecha1Iterante->add($intervalo);
        }

        $resumenCreditos = array();

        for ($i = 0; $i < $cantPersonas; $i++) {

            $personaCartera = array(
                "persona" => $personas[$i],
                "socio" => $valores,
                "capital" => $valores,
                "saldoAnterior" => $valores,
                "interes" => $valores,
                "mora" => $valores,
                "desgravamen" => $valores,
                "totalpagado" => $valores,
                "creditomes" => $valores,
                "creditono" => $valores,
                "ccmxv" => $valores,
                "ccmv" => $valores,
                "ccxv" => $valores,
                "cccv" => $valores,
                "atraso" => $valores,
                "hombre" => $valores,
                "mujer" => $valores,
                "saldoFinal" => $valores,
            );

            $carteras = $em->getRepository('ModulosPersonasBundle:Libro')->findCarteraPorPersona($personas[$i]);
            $creditosPorPersona = $em->getRepository('ModulosPersonasBundle:Creditos')->findCreditosOtrogadosByPersona(
                $personas[$i]
            );

            $diasAtraso = 0;
            if (count($creditosPorPersona) > 0) {
                for ($k = 0; $k < count($creditosPorPersona); $k++) {

                    $amortizaciones = $em->getRepository(
                        'ModulosPersonasBundle:TablaAmortizacion'
                    )->findTablasAmortizacionPorCreditos($creditosPorPersona[$k]->getId());

                    $pagosrealizados = $em->getRepository(
                        'ModulosPersonasBundle:PagoCuotaCredito'
                    )->findPagosCuotasCreditos($creditosPorPersona[$k]->getId());

                    $dias = 0;
                    if (count($pagosrealizados) > 0) {
                        foreach ($pagosrealizados as $key => $pagorealizado) {
                            if ($pagorealizado->getFechaDePago()->format(
                                    'Ymd'
                                ) > $amortizaciones[$key + 1]->getFechaDePago()->format('Ymd')
                            ) {
                                $dias += $pagorealizado->getFechaDePago()->format(
                                        'd'
                                    ) - $amortizaciones[$key + 1]->getFechaDePago()->format('d');
                            }
                        }
                    }
                    $diasAtraso += $dias;

                }
            }

            $personaCartera["atraso"] = $diasAtraso;

            $personaCartera["hombre"] = $personas[$i]->getGenero()->getGenero() == "MASCULINO" ? 1 : 0;
            $personaCartera["mujer"] = $personas[$i]->getGenero()->getGenero() == "FEMENINO" ? 1 : 0;
            $personaCartera["socio"] = $personas[$i]->getTipo_persona() == "SOCIOS" ? 'S' : '-';

            $cant = 0;
            $ccmxv = 0;
            $saldoa = 0;
            for ($j = 0; $j < count($carteras); $j++) {

                if ($personas[$i]->getId() == $carteras[$j]->getPersona()->getId()) {
                    if ($carteras[$j]->getProductoContableId()->getId() == 9) {//Pago crédito
                        $personaCartera["capital"][$carteras[$j]->getFecha()->format('Y-m')] += $carteras[$j]->getDebe(
                        );
                    } else {
                        if ($carteras[$j]->getProductoContableId()->getId() == 10) {//Pago Interés
                            $personaCartera["interes"][$carteras[$j]->getFecha()->format(
                                'Y-m'
                            )] += $carteras[$j]->getDebe();
                        } else {
                            if ($carteras[$j]->getProductoContableId()->getId() == 3) {//Pago Desgravamen
                                $personaCartera["desgravamen"][$carteras[$j]->getFecha()->format(
                                    'Y-m'
                                )] += $carteras[$j]->getDebe();
                            } else {
                                if ($carteras[$j]->getProductoContableId()->getId(
                                    ) == 4 || $carteras[$j]->getProductoContableId()->getId() == 5
                                ) {//Credito Otorgado o Credito Emergente
                                    $personaCartera["creditomes"][$carteras[$j]->getFecha()->format(
                                        'Y-m'
                                    )] += $carteras[$j]->getHaber();
                                    $cant += 1;
                                } else {
                                    if ($carteras[$j]->getProductoContableId()->getId() == 11) {//Mora
                                        $personaCartera["mora"][$carteras[$j]->getFecha()->format(
                                            'Y-m'
                                        )] += $carteras[$j]->getDebe();
                                    }
                                }
                            }
                        }
                    }

                    $personaCartera["totalpagado"][$carteras[$j]->getFecha()->format(
                        'Y-m'
                    )] = $personaCartera["interes"][$carteras[$j]->getFecha()->format(
                            'Y-m'
                        )] + $personaCartera["capital"][$carteras[$j]->getFecha()->format(
                            'Y-m'
                        )] + $personaCartera["desgravamen"][$carteras[$j]->getFecha()->format('Y-m')];
                }
            }
            $personaCartera["creditono"] = $cant;


            $resumenCreditos[] = $personaCartera;
        }
        for ($i = 0; $i < count($resumenCreditos); $i++) {
            for ($j = 0; $j < count($arrayMeses); $j++) {

                $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]] = (($j > 0) ? ($resumenCreditos[$i]["ccmxv"][$arrayMeses[$j - 1]] + $resumenCreditos[$i]["ccmv"][$arrayMeses[$j - 1]]) : 0);

                if ($resumenCreditos[$i]["mora"][$arrayMeses[$j]] > 0) {
                    $resumenCreditos[$i]["ccmxv"][$arrayMeses[$j]] = 0;
                    $resumenCreditos[$i]["ccmv"][$arrayMeses[$j]] = $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]] - $resumenCreditos[$i]["capital"][$arrayMeses[$j]] + $resumenCreditos[$i]["creditomes"][$arrayMeses[$j]] + $resumenCreditos[$i]["mora"][$arrayMeses[$j]];
                } else {
                    $resumenCreditos[$i]["ccmxv"][$arrayMeses[$j]] = $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]] - $resumenCreditos[$i]["capital"][$arrayMeses[$j]] + $resumenCreditos[$i]["creditomes"][$arrayMeses[$j]];
                    $resumenCreditos[$i]["ccmv"][$arrayMeses[$j]] = 0;
                }


            }
        }

        for ($j = 0; $j < count($arrayMeses); $j++) {
            $salidaCreditos[$arrayMeses[$j]] = array();
            for ($i = 0; $i < count($resumenCreditos); $i++) {

                $personaMes = array();
                $personaMes["persona"] = $resumenCreditos[$i]["persona"];
                $personaMes["socio"] = $resumenCreditos[$i]["socio"];
                $personaMes["atraso"] = $resumenCreditos[$i]["atraso"];
                $personaMes["saldoAnterior"] = $resumenCreditos[$i]["saldoAnterior"][$arrayMeses[$j]];
                $personaMes["capital"] = $resumenCreditos[$i]["capital"][$arrayMeses[$j]];
                $personaMes["interes"] = $resumenCreditos[$i]["interes"][$arrayMeses[$j]];
                $personaMes["mora"] = $resumenCreditos[$i]["mora"][$arrayMeses[$j]];
                $personaMes["desgravamen"] = $resumenCreditos[$i]["desgravamen"][$arrayMeses[$j]];
                $personaMes["totalpagado"] = $resumenCreditos[$i]["totalpagado"][$arrayMeses[$j]];
                $personaMes["creditomes"] = $resumenCreditos[$i]["creditomes"][$arrayMeses[$j]];
                $personaMes["creditono"] = $resumenCreditos[$i]["creditono"];
                $personaMes["ccmxv"] = $resumenCreditos[$i]["ccmxv"][$arrayMeses[$j]];
                $personaMes["ccmv"] = $resumenCreditos[$i]["ccmv"][$arrayMeses[$j]];
                $personaMes["hombre"] = $resumenCreditos[$i]["hombre"];
                $personaMes["mujer"] = $resumenCreditos[$i]["mujer"];
                $personaMes["saldoFinal"] = $resumenCreditos[$i]["saldoFinal"][$arrayMeses[$j]];
                $salidaCreditos[$arrayMeses[$j]][] = $personaMes;
            }

        }

        /*return $this->render(
            'ModulosPersonasBundle:Libro:cartera2.html.twig',
            array(
                'meses'=>$arrayMeses,
                'mesesTexto'=>$arrayMesesTexto,
                'creditos'=>$salidaCreditos,
                'nombrecaja'=>$nombrecaja,
            )
        );/

////////////////////////////////////////////////////////////////BALANCE GENERAL/////////////////////////////////////////////////////////////

        $balanceGralMeses = array();

        $dtvcAll = $em->getRepository('ModulosPersonasBundle:DTVC')->findAllDTVCAporFechasAsc();

        $meses = array();
        if (count($dtvcAll) > 0) {
            $aux = $dtvcAll[0]->getIdVchr()->getFecha()->format('Y-m');
            $meses [] = $aux;
            for ($i = 1; $i < count($dtvcAll); $i++) {
                if ($dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m') != $aux) {
                    $aux = $dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m');
                    $meses [] = $aux;
                }
            }
        }

        $activosSaldoAnterior = array(
            "11" => 0,
            "1101" => 0,
            "1103" => 0,
            "13" => 0,
            "1301" => 0,
            "14" => 0,
            "1402" => 0,
            "1404" => 0,
            "1422" => 0,
            "1424" => 0,
            "1499" => 0,
            "16" => 0,
            "1602" => 0,
            "1603" => 0,
            "1690" => 0,
            "18" => 0,
            "1801" => 0,
            "1802" => 0,
            "1805" => 0,
            "1806" => 0,
            "1807" => 0,
            "1808" => 0,
            "1890" => 0,
            "19" => 0,
            "1905" => 0,
            "1990" => 0,
        );
        $pasivosSaldoAnterior = array(
            "21" => 0,
            "2101" => 0,
            "2103" => 0,
            "2105" => 0,
            "25" => 0,
            "2503" => 0,
            "2504" => 0,
            "2505" => 0,
            "2590" => 0,
            "26" => 0,
            "2602" => 0,
            "2606" => 0,
            "29" => 0,
            "2990" => 0,
        );
        $patrimonioSaldoAnterior = array(
            "31" => 0,
            "3103" => 0,
            "34" => 0,
            "3402" => 0,
            "3490" => 0,
            "36" => 0,
            "3601" => 0,
            "3602" => 0,
            "3603" => 0,
            "3604" => 0,
        );

        for ($i = 0; $i < count($meses); $i++) {
            $fechaArray = explode('-', $meses[$i]);
            $fechaInicial = new \DateTime();
            $fechaInicial->setDate($fechaArray[0], $fechaArray[1], 1);
            $cantDias = $fechaInicial->format('t');

            $fechaFinal = new \DateTime();
            $fechaFinal->setDate($fechaArray[0], $fechaArray[1], $cantDias);

            $balanceGral = new BalanceGral();

            $activosOutput = array(
                "11" => "",
                "1101" => "",
                "1103" => "",
                "13" => "",
                "1301" => "",
                "14" => "",
                "1402" => "",
                "1404" => "",
                "1422" => "",
                "1424" => "",
                "1499" => "",
                "16" => "",
                "1602" => "",
                "1603" => "",
                "1690" => "",
                "18" => "",
                "1801" => "",
                "1802" => "",
                "1805" => "",
                "1806" => "",
                "1807" => "",
                "1808" => "",
                "1890" => "",
                "19" => "",
                "1905" => "",
                "1990" => "",
            );
            $pasivosOutput = array(
                "21" => "",
                "2101" => "",
                "2103" => "",
                "2105" => "",
                "25" => "",
                "2503" => "",
                "2504" => "",
                "2505" => "",
                "2590" => "",
                "26" => "",
                "2602" => "",
                "2606" => "",
                "29" => "",
                "2990" => "",
            );
            $patrimonioOutput = array(
                "31" => "",
                "3103" => "",
                "34" => "",
                "3402" => "",
                "3490" => "",
                "36" => "",
                "3601" => "",
                "3602" => "",
                "3603" => "",
                "3604" => "",
            );


            $activos = array(
                0 => "11",
                "1101",
                "1103",
                "13",
                "1301",
                "14",
                "1402",
                "1404",
                "1422",
                "1424",
                "1499",
                "16",
                "1602",
                "1603",
                "1690",
                "18",
                "1801",
                "1802",
                "1805",
                "1806",
                "1807",
                "1808",
                "1890",
                "19",
                "1905",
                "1990",
            );
            $pasivos = array(
                0 => "21",
                "2101",
                "2103",
                "2105",
                "25",
                "2503",
                "2504",
                "2505",
                "2590",
                "26",
                "2602",
                "2606",
                "29",
                "2990",
            );
            $patrimonio = array(0 => "31", "3103", "34", "3402", "3490", "36", "3601", "3602", "3603", "3604");
            $totales = array("activos" => "", "pasivos" => "", "patrimonio" => "");


            $totalActivos = 0;
            for ($k = 0; $k < count($activos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $activos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $activos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

                $activosOutput[$activos[$k]] = $acreedoraValor - $deudoraValor + $activosSaldoAnterior[$activos[$k]];
                $totalActivos += $activosOutput[$activos[$k]];
                $activosSaldoAnterior[$activos[$k]] = $activosOutput[$activos[$k]];

            }
            $totales["activos"] = $totalActivos;

            $totalPasivos = 0;
            for ($k = 0; $k < count($pasivos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $pasivos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $pasivos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

                $pasivosOutput[$pasivos[$k]] = $deudoraValor - $acreedoraValor + $pasivosSaldoAnterior[$pasivos[$k]];
                $totalPasivos += $pasivosOutput[$pasivos[$k]];
                $pasivosSaldoAnterior[$pasivos[$k]] = $pasivosOutput[$pasivos[$k]];

            }
            $totales["pasivos"] = $totalPasivos;


            $totalPatrimonio = 0;
            for ($k = 0; $k < count($patrimonio); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $patrimonio[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $patrimonio[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );

                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

                if ($patrimonio[$k] == "3601") {//utilidades del mes anterior + utilidades o excedentes acumulados del ems anteior
                    $patrimonioOutput[$patrimonio[$k]] = $patrimonioSaldoAnterior[$patrimonio[$k]] + $patrimonioSaldoAnterior["3603"];
                    $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                    $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                } else {
                    if ($patrimonio[$k] == "3602") {

                        $patrimonioOutput[$patrimonio[$k]] = $patrimonioSaldoAnterior[$patrimonio[$k]] + $patrimonioSaldoAnterior["3604"];
                        $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                        $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                    } else {
                        if ($patrimonio[$k] == "3603") {//calcular de P y G la 3603 pygMes.totales['ingresos'] - pygMes.totales['gastos']
                            $pygMeses = $this->pyg();
                            $totalIngreso = $pygMeses[count($meses) - 1 - $i]->getTotales()["ingresos"];
                            $totalGastos = $pygMeses[count($meses) - 1 - $i]->getTotales()["gastos"];

                            $patrimonioOutput[$patrimonio[$k]] = $totalIngreso - $totalGastos > 0 ? $totalIngreso - $totalGastos : 0;

                            $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                            $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                        } else {
                            if ($patrimonio[$k] == "3604") {
                                $pygMeses = $this->pyg();
                                $totalIngreso = $pygMeses[count($meses) - 1 - $i]->getTotales()["ingresos"];
                                $totalGastos = $pygMeses[count($meses) - 1 - $i]->getTotales()["gastos"];

                                $patrimonioOutput[$patrimonio[$k]] = $totalIngreso - $totalGastos < 0 ? $totalIngreso - $totalGastos : 0;
                                $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                                $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                            } else {
                                $patrimonioOutput[$patrimonio[$k]] = $deudoraValor - $acreedoraValor + $patrimonioSaldoAnterior[$patrimonio[$k]];
                                $totalPatrimonio += $patrimonioOutput[$patrimonio[$k]];
                                $patrimonioSaldoAnterior[$patrimonio[$k]] = $patrimonioOutput[$patrimonio[$k]];
                            }
                        }
                    }
                }
            }
            $totales["patrimonio"] = $totalPatrimonio;

            $balanceGral->setMes($meses[$i]);
            $balanceGral->setActivos($activosOutput);
            $balanceGral->setPasivos($pasivosOutput);
            $balanceGral->setPatrimonio($patrimonioOutput);
            $balanceGral->setTotales($totales);
            $balanceGralMeses[] = $balanceGral;
        }

///////////////////////////////////////////////////////////////////// P Y G ///////////////////////////////////////////////////////////////

        $pygMeses = array();

        $dtvcAll = $em->getRepository('ModulosPersonasBundle:DTVC')->findAllDTVCAporFechasAsc();

        $meses = array();
        if (count($dtvcAll) > 0) {
            $aux = $dtvcAll[0]->getIdVchr()->getFecha()->format('Y-m');
            $meses [] = $aux;
            for ($i = 1; $i < count($dtvcAll); $i++) {
                if ($dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m') != $aux) {
                    $aux = $dtvcAll[$i]->getIdVchr()->getFecha()->format('Y-m');
                    $meses [] = $aux;
                }
            }
        }


        for ($i = 0; $i < count($meses); $i++) {
            $fechaArray = explode('-', $meses[$i]);
            $fechaInicial = new \DateTime();
            $fechaInicial->setDate($fechaArray[0], $fechaArray[1], 1);
            $cantDias = $fechaInicial->format('t');

            $fechaFinal = new \DateTime();
            $fechaFinal->setDate($fechaArray[0], $fechaArray[1], $cantDias);

            $pyg = new PYG();
            $dtvcMes = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAporFechasAsc(
                $fechaInicial,
                $fechaFinal
            );

            $ingresosOutput = array(
                "51" => "",
                "5101" => "",
                "5104" => "",
                "5190" => "",
                "54" => "",
                "5490" => "",
                "56" => "",
                "5690" => "",
                "3603" => "",
            );
            $gastosOutput = array(
                "41" => "",
                "4101" => "",
                "4103" => "",
                "4105" => "",
                "44" => "",
                "4402" => "",
                "45" => "",
                "4501" => "",
                "4502" => "",
                "4503" => "",
                "4504" => "",
                "4507" => "",
            );
            $ingresos = array(0 => "51", "5101", "5104", "5190", "54", "5490", "56", "5690", "3603");
            $gastos = array(
                0 => "41",
                "4101",
                "4103",
                "4105",
                "44",
                "4402",
                "45",
                "4501",
                "4502",
                "4503",
                "4504",
                "4507",
            );
            $totales = array("ingresos" => "", "gastos" => "");

            $totalIngreso = 0;
            for ($k = 0; $k < count($ingresos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $ingresos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $ingresos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );

                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];
                $ingresosOutput[$ingresos[$k]] = $deudoraValor - $acreedoraValor;
                $totalIngreso += $ingresosOutput[$ingresos[$k]];
            }
            $totales["ingresos"] = $totalIngreso;

            $totalGasto = 0;
            for ($k = 0; $k < count($gastos); $k++) {
                $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedora(
                    $gastos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );
                $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudora(
                    $gastos[$k],
                    $fechaArray[1],
                    $fechaInicial->format('Y-m-d H:i:s'),
                    $fechaFinal->format('Y-m-d H:i:s')
                );

                $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
                $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];
                $totalGasto += ($acreedoraValor - $deudoraValor);
                $gastosOutput[$gastos[$k]] = $acreedoraValor - $deudoraValor;
            }
            $totales["gastos"] = $totalGasto;

            $pyg->setMes($meses[$i]);
            $pyg->setIngresos($ingresosOutput);
            $pyg->setGastos($gastosOutput);
            $pyg->setTotales($totales);

            $pygMeses[] = $pyg;
        }

        return $this->render(
            'ModulosPersonasBundle:Libro:estadosfinancieros.html.twig',
            array(
                'meses' => $arrayMeses,
                'mesesTexto' => $arrayMesesTexto,
                'creditos' => $salidaCreditos,
                'pygMeses' => $pygMeses,
                'balanceGralMeses' => $balanceGralMeses,
                'nombrecaja' => $nombrecaja,
            )
        );

    }*/

    public function resumenTransaccionesAction($mes, $ano)
    {
        $em = $this->getDoctrine()->getManager();

        $mesesMap = [
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
        $mesesMapAbrev = [
            "01" => "Ene",
            "02" => "Feb",
            "03" => "Mar",
            "04" => "Abr",
            "05" => "May",
            "06" => "Jun",
            "07" => "Jul",
            "08" => "Ago",
            "09" => "Sep",
            "10" => "Oct",
            "11" => "Nov",
            "12" => "Dic",
        ];

        $fecha1 = new \DateTime();
        $fecha2 = new \DateTime();

        $aux = "0:0:0";
        $fecha1 = $fecha1->format('Y-m-d');
        $fechaaux = $fecha1." ".$aux;
        $fecha1 = new \DateTime($fechaaux);

        $aux2 = "23:59:59";
        $fecha2 = $fecha2->format('Y-m-d');
        $fechaaux2 = $fecha2." ".$aux2;
        $fecha2 = new \DateTime($fechaaux2);

        $fecha1->setDate($ano,$mes,1);

        if($fecha1->format('m')=='01')
        {
            $fecha2->setDate($ano, 12, 1);
            $aux1 = $fecha2->format('t');
            $fecha2->setDate($ano, 12, $aux1);

        }else {
            $mesF2 = (($mes - 1) == 0 ? 12 : ($mes - 1));
            $anoF2 = $ano + 1;
            $fecha2->setDate($anoF2, $mesF2, 1);
            $aux1 = $fecha2->format('t');
            $fecha2->setDate($anoF2, $mesF2, $aux1);
        }

        $intervalo = new \DateInterval('P1M');
        $fecha1Iterante=new \DateTime($fecha1->format('Y-m-d'));
        $arrayMeses=array();
        $arrayMesesTexto=array();
        for($m=0; $m<12; $m++){
            $arrayMeses[]=$fecha1Iterante->format('Y-m');
            //$arrayMesesTexto[]=$mesesMapAbrev[$fecha1Iterante->format('m')]."-".$fecha1Iterante->format('Y');
            $arrayMesesTexto[]=$mesesMapAbrev[$fecha1Iterante->format('m')];
            $fecha1Iterante->add($intervalo);
        }
        $mesesTexto=array($arrayMeses[0]=>$arrayMesesTexto[0],$arrayMeses[1]=>$arrayMesesTexto[1],$arrayMeses[2]=>$arrayMesesTexto[2],$arrayMeses[3]=>$arrayMesesTexto[3],$arrayMeses[4]=>$arrayMesesTexto[4],$arrayMeses[5]=>$arrayMesesTexto[5],$arrayMeses[6]=>$arrayMesesTexto[6],$arrayMeses[7]=>$arrayMesesTexto[7],$arrayMeses[8]=>$arrayMesesTexto[8],$arrayMeses[9]=>$arrayMesesTexto[9],$arrayMeses[10]=>$arrayMesesTexto[10],$arrayMeses[11]=>$arrayMesesTexto[11]);

        $fechaIntervaloLabel="De ".$mesesMap[$fecha1->format('m')]." del ".$fecha1->format('Y')
            ." a ".$mesesMap[$fecha2->format('m')]." del ".$fecha2->format('Y');


        $resumenTransaccionesArray = array();

        $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();
        if(count($librosOrdenados)>0)
            $saldoLibro= $librosOrdenados[0]->getSaldo();
        else
            $saldoLibro= 0;

        //echo $saldoLibro;

        $transacciones = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findTipoProductoContableOrdenado();

        if(count($transacciones)>0){
            for ($i = 0; $i < count($transacciones); $i++) {

                $total = 0;

                $resumenTransacciones = array("transacciones" => $transacciones[$i]->getTipo(),
                    "meses" => array($arrayMeses[0] => 0, $arrayMeses[1] => 0, $arrayMeses[2] => 0, $arrayMeses[3] => 0, $arrayMeses[4] => 0, $arrayMeses[5] => 0, $arrayMeses[6] => 0, $arrayMeses[7] => 0, $arrayMeses[8] => 0, $arrayMeses[9] => 0, $arrayMeses[10] => 0, $arrayMeses[11] => 0,),
                    "total" => 0);

                //$librosOrdenados[$i]
                $librosXtransaccion = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosPorfechaXtransaccion($transacciones[$i]->getId(),$fecha1,$fecha2);

                for($j=0; $j<count($librosXtransaccion); $j++) {
                    $total += $librosXtransaccion[$j]->getDebe() - $librosXtransaccion[$j]->getHaber();
                    $resumenTransacciones["meses"][$librosXtransaccion[$j]->getFecha()->format('Y-m')]+=$librosXtransaccion[$j]->getDebe()-$librosXtransaccion[$j]->getHaber();
                }

                $resumenTransacciones["total"]=$total;
                $resumenTransaccionesArray[]=$resumenTransacciones;
            }
        }

        $totalesMeses=array($arrayMeses[0]=>0,$arrayMeses[1]=>0,$arrayMeses[2]=>0,$arrayMeses[3]=>0,$arrayMeses[4]=>0,$arrayMeses[5]=>0,$arrayMeses[6]=>0,$arrayMeses[7]=>0,$arrayMeses[8]=>0,$arrayMeses[9]=>0,$arrayMeses[10]=>0,$arrayMeses[11]=>0,);
        for($i=0; $i<count($resumenTransaccionesArray); $i++){
            for($j=0; $j<count($arrayMeses); $j++) {
                $totalesMeses[$arrayMeses[$j]]+=$resumenTransaccionesArray[$i]["meses"][$arrayMeses[$j]];
            }
        }

        $personas = $em->getRepository('ModulosPersonasBundle:Persona')->findAll();
        $socios =0;
        $personal = 0;
        $hombres = 0;
        $mujeres = 0;
        $numPersonas = count($personas);


        if(count($personas)>0) {
            for ($i = 0; $i < count($personas); $i++) {
                if ($personas[$i]->getTipo_persona()->getId()==3){
                    $personal+=1;
                }elseif($personas[$i]->getTipo_persona()->getId()==2 || $personas[$i]->getTipo_persona()->getId()==1) {
                    $socios+=1;
                    if ($personas[$i]->getGenero()->getId() == 1){
                        $hombres+=1;
                    }else{
                        $mujeres+=1;
                    }
                }
            }
        }

        $hoy = getdate();

        $fechaHoy = new \DateTime();
        $fechaHoy->setDate($hoy['year'], $hoy['mon'], $hoy['mday']);
        $fechaActual = $fechaHoy->format('Y-m-d');


        return $this->render(
            'ModulosPersonasBundle:Libro:resumenTransacciones.html.twig',
            array(
                'fechainicial' => $fecha1,
                'fechafinal' => $fecha2,
                'meses'=>$arrayMeses,
                'mesesTotales'=>$totalesMeses,
                'mesesTexto'=>$mesesTexto,
                'fechaLabel'=>$fechaIntervaloLabel,
                'resumen'=>$resumenTransaccionesArray,
                'saldoCaja' =>$saldoLibro,
                'socios' => $socios,
                'personal' => $personal,
                'hombres' => $hombres,
                'mujeres' => $mujeres,
                'fechaHoy' =>$fechaActual,
                'numeroPersonas' => $numPersonas,
            )
        );
    }

    public function impresionResumenTransaccionesAction($mes, $ano)
    {
        $em = $this->getDoctrine()->getManager();

        $mesesMap = [
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
        $mesesMapAbrev = [
            "01" => "Ene",
            "02" => "Feb",
            "03" => "Mar",
            "04" => "Abr",
            "05" => "May",
            "06" => "Jun",
            "07" => "Jul",
            "08" => "Ago",
            "09" => "Sep",
            "10" => "Oct",
            "11" => "Nov",
            "12" => "Dic",
        ];

        $fecha1 = new \DateTime();
        $fecha2 = new \DateTime();

        $aux = "0:0:0";
        $fecha1 = $fecha1->format('Y-m-d');
        $fechaaux = $fecha1." ".$aux;
        $fecha1 = new \DateTime($fechaaux);

        $aux2 = "23:59:59";
        $fecha2 = $fecha2->format('Y-m-d');
        $fechaaux2 = $fecha2." ".$aux2;
        $fecha2 = new \DateTime($fechaaux2);

        $fecha1->setDate($ano,$mes,1);

        if($fecha1->format('m')=='01')
        {
            $fecha2->setDate($ano, 12, 1);
            $aux1 = $fecha2->format('t');
            $fecha2->setDate($ano, 12, $aux1);

        }else {
            $mesF2 = (($mes - 1) == 0 ? 12 : ($mes - 1));
            $anoF2 = $ano + 1;
            $fecha2->setDate($anoF2, $mesF2, 1);
            $aux1 = $fecha2->format('t');
            $fecha2->setDate($anoF2, $mesF2, $aux1);
        }

        $intervalo = new \DateInterval('P1M');
        $fecha1Iterante=new \DateTime($fecha1->format('Y-m-d'));
        $arrayMeses=array();
        $arrayMesesTexto=array();
        for($m=0; $m<12; $m++){
            $arrayMeses[]=$fecha1Iterante->format('Y-m');
            //$arrayMesesTexto[]=$mesesMapAbrev[$fecha1Iterante->format('m')]."-".$fecha1Iterante->format('Y');
            $arrayMesesTexto[]=$mesesMapAbrev[$fecha1Iterante->format('m')];
            $fecha1Iterante->add($intervalo);
        }
        $mesesTexto=array($arrayMeses[0]=>$arrayMesesTexto[0],$arrayMeses[1]=>$arrayMesesTexto[1],$arrayMeses[2]=>$arrayMesesTexto[2],$arrayMeses[3]=>$arrayMesesTexto[3],$arrayMeses[4]=>$arrayMesesTexto[4],$arrayMeses[5]=>$arrayMesesTexto[5],$arrayMeses[6]=>$arrayMesesTexto[6],$arrayMeses[7]=>$arrayMesesTexto[7],$arrayMeses[8]=>$arrayMesesTexto[8],$arrayMeses[9]=>$arrayMesesTexto[9],$arrayMeses[10]=>$arrayMesesTexto[10],$arrayMeses[11]=>$arrayMesesTexto[11]);

        $fechaIntervaloLabel="De ".$mesesMap[$fecha1->format('m')]." del ".$fecha1->format('Y')
            ." a ".$mesesMap[$fecha2->format('m')]." del ".$fecha2->format('Y');


        $resumenTransaccionesArray = array();

        $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();
        if(count($librosOrdenados)>0)
            $saldoLibro= $librosOrdenados[0]->getSaldo();
        else
            $saldoLibro= 0;
        //echo $saldoLibro;

        $transacciones = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findTipoProductoContableOrdenado();

        if(count($transacciones)>0){
            for ($i = 0; $i < count($transacciones); $i++) {

                $total = 0;

                $resumenTransacciones = array("transacciones" => $transacciones[$i]->getTipo(),
                    "meses" => array($arrayMeses[0] => 0, $arrayMeses[1] => 0, $arrayMeses[2] => 0, $arrayMeses[3] => 0, $arrayMeses[4] => 0, $arrayMeses[5] => 0, $arrayMeses[6] => 0, $arrayMeses[7] => 0, $arrayMeses[8] => 0, $arrayMeses[9] => 0, $arrayMeses[10] => 0, $arrayMeses[11] => 0,),
                    "total" => 0);

                //$librosOrdenados[$i]
                $librosXtransaccion = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosPorfechaXtransaccion($transacciones[$i]->getId(),$fecha1,$fecha2);

                for($j=0; $j<count($librosXtransaccion); $j++) {
                    $total += $librosXtransaccion[$j]->getDebe() - $librosXtransaccion[$j]->getHaber();
                    $resumenTransacciones["meses"][$librosXtransaccion[$j]->getFecha()->format('Y-m')]+=$librosXtransaccion[$j]->getDebe()-$librosXtransaccion[$j]->getHaber();
                }

                $resumenTransacciones["total"]=$total;
                $resumenTransaccionesArray[]=$resumenTransacciones;
            }
        }

        $totalesMeses=array($arrayMeses[0]=>0,$arrayMeses[1]=>0,$arrayMeses[2]=>0,$arrayMeses[3]=>0,$arrayMeses[4]=>0,$arrayMeses[5]=>0,$arrayMeses[6]=>0,$arrayMeses[7]=>0,$arrayMeses[8]=>0,$arrayMeses[9]=>0,$arrayMeses[10]=>0,$arrayMeses[11]=>0,);
        for($i=0; $i<count($resumenTransaccionesArray); $i++){
            for($j=0; $j<count($arrayMeses); $j++) {
                $totalesMeses[$arrayMeses[$j]]+=$resumenTransaccionesArray[$i]["meses"][$arrayMeses[$j]];
            }
        }

        $personas = $em->getRepository('ModulosPersonasBundle:Persona')->findAll();
        $socios =0;
        $personal = 0;
        $hombres = 0;
        $mujeres = 0;
        $numPersonas = count($personas);


        if(count($personas)>0) {
            for ($i = 0; $i < count($personas); $i++) {
                if ($personas[$i]->getTipo_persona()->getId()==3){
                    $personal+=1;
                }elseif($personas[$i]->getTipo_persona()->getId()==2 || $personas[$i]->getTipo_persona()->getId()==1) {
                    $socios+=1;
                    if ($personas[$i]->getGenero()->getId() == 1){
                        $hombres+=1;
                    }else{
                        $mujeres+=1;
                    }
                }
            }
        }

        $hoy = getdate();

        $fechaHoy = new \DateTime();
        $fechaHoy->setDate($hoy['year'], $hoy['mon'], $hoy['mday']);
        $fechaActual = $fechaHoy->format('Y-m-d');


        $html= $this->renderView(
            'ModulosPersonasBundle:Libro:FormatoResumenTransacciones.html.twig',
            array(
                'someDataToView' => 'Something',
                'fechainicial' => $fecha1,
                'fechafinal' => $fecha2,
                'meses'=>$arrayMeses,
                'mesesTotales'=>$totalesMeses,
                'mesesTexto'=>$mesesTexto,
                'fechaLabel'=>$fechaIntervaloLabel,
                'resumen'=>$resumenTransaccionesArray,
                'saldoCaja' =>$saldoLibro,
                'socios' => $socios,
                'personal' => $personal,
                'hombres' => $hombres,
                'mujeres' => $mujeres,
                'fechaHoy' =>$fechaActual,
                'numeroPersonas' => $numPersonas,
            )
        );

        $this->returnPDFResponseFromHTML($html, $ano);
    }

    public function returnPDFResponseFromHTML($html,$anio)
    {
        //set_time_limit(30); uncomment this line according to your needs
        // If you are not in a controller, retrieve of some way the service container and then retrieve it
        //$pdf = $this->container->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //if you are in a controlller use :
        $pdf = $this->get("white_october.tcpdf")->create();// create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('Conquito');
        $pdf->SetTitle(('Resumen de Transacciones'));
        $pdf->SetSubject('Resumen de Transacciones');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 8, '', true);
        //$pdf->SetMargins(20,20,40, true);
        //$pdf->AddPage();
        $pdf->AddPage('L');
        //$pdf->AddPage('P', 'Letter');



        $filename = 'Resumen Transacciones '.$anio;

        //$pdf->AddPage('L');
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

        $pdf->Output($filename . ".pdf", 'D'); // This will output the PDF as a response directly
    }
}
