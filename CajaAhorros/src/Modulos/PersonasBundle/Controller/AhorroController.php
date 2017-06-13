<?php

namespace Modulos\PersonasBundle\Controller;

use Modulos\PersonasBundle\Entity\DTVC;
use Modulos\PersonasBundle\Entity\Libro;
use Modulos\PersonasBundle\Entity\VCHR;
use Modulos\PersonasBundle\Form\DepositoPlazoFijoType;
use Modulos\PersonasBundle\Form\DepositoRestringidoType;
use Modulos\PersonasBundle\Form\DepositoVistaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\Ahorro;
use Modulos\PersonasBundle\Form\AhorroType;

use Modulos\PersonasBundle\Form\PagoCuotaAhorroType;

use Modulos\PersonasBundle\Entity\TablaAmortizacionAhorro;
use Modulos\PersonasBundle\Entity\PagoCuotaAhorro;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Constraints\DateTime;

use Modulos\PersonasBundle\Entity\AhorroLista;
use Modulos\PersonasBundle\Entity\AhorroPersonaMes;
use Modulos\PersonasBundle\Entity\AhorroMes;

/**
 * Ahorro controller.
 *
 */
class AhorroController extends Controller
{

    /**
     * Lists all Ahorro entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAll();
        

        return $this->render('ModulosPersonasBundle:Ahorro:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Ahorro entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Ahorro();
        $em = $this->getDoctrine()->getManager();

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

        $form = $this->createForm(new AhorroType(),$entity);
        $form->handleRequest($request);

        $tiposahorros = $em->getRepository('ModulosPersonasBundle:TipoAhorro')->findAll();

        if($entity->getTipoAhorro()=="Ahorro Plazo Fijo"){

            $difAno=$entity->getFechafinal()->format('Y')- $entity->getFechaSolicitud()->format('Y');
            $difMes=(($difAno*12)+$entity->getFechafinal()->format('m'))- $entity->getFechaSolicitud()->format('m');
            $difDias =(($difMes*30)+$entity->getFechafinal()->format('d')) - $entity->getFechaSolicitud()->format('d');

            //if($entity->getFechafinal()->format('Y-m')==$entity->getFechaSolicitud()->format('Y-m')||$entity->getFechafinal()->format('Y-m')>$entity->getFechaSolicitud()->format('Y-m')||$entity->getFechafinal()->format('Y-m')<$entity->getFechaSolicitud()->format('Y-m')){
                if($difMes<=1) {
                    if ($difDias < 30) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'El plazo mínimo para crear un Ahorro a Plazo Fijo es de un mes o una diferencia de 30 días superiores a la fecha de ingreso'
                        );
                        return $this->redirect($this->generateUrl('ahorro_create'));
                    }
                }
            //}

        }

        if ($form->isValid()) {



            $em = $this->getDoctrine()->getManager();
            $estadoAhorro = $em->getRepository('ModulosPersonasBundle:EstadoAhorro')->find(4);
            $entity->setEstadoAhorro($estadoAhorro);
            $entity->setFechaaux($entity->getFechafinal());
                $em->persist($entity);
                $em->flush();

            if (!$entity->getFrecuenciaDePago() == "" && !$entity->getFechaSolicitud(
                ) == "" && !$entity->getvalorAhorrar(
                ) == "" && !$entity->getTasaInteres() == ""
            ) {
                if($entity->getTipoAhorro()->getMetodoAmortizacionAhorro()->getMetodo() == "FONDO AHORRO"){//Francés
                    $this->getCuotaAhorroConstante($em, $entity);
                }
//

            }




                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Se ha creado el ahorro'
                );
            return $this->redirect($this->generateUrl(
                //'ahorro_update', array('id' => $entity->getId())));
                'depositoAho', array('persona' => $entity->getPersona()->getId(),'id' => $entity->getId())));


        }

        return $this->render('ModulosPersonasBundle:Ahorro:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'tiposahorros'   => $tiposahorros            
        ));
    }


    /**
     * Finds and displays a Ahorro entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Ahorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ahorro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Ahorro:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Ahorro entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Ahorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ahorro entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Ahorro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Ahorro entity.
    *
    * @param Ahorro $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Ahorro $entity)
    {
        $form = $this->createForm(new AhorroType(), $entity, array(
            'action' => $this->generateUrl('ahorro_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Ahorro entity.
     *
     */
    public function updateAction(Request $request, $id)
    {   
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Ahorro')->find($id);

        $valorCaja= $entity->getValorEnCaja();
        $tipoAhorro= $entity->getTipoAhorro();

        $tiposahorros = $em->getRepository('ModulosPersonasBundle:TipoAhorro')->findAll();
        $editForm = $this->createForm(new AhorroType(),$entity);
        $editForm->handleRequest($request);

        if($entity->getTipoAhorro()=="Ahorro Plazo Fijo"){

            $difAno=$entity->getFechafinal()->format('Y')- $entity->getFechaSolicitud()->format('Y');
            $difMes=(($difAno*12)+$entity->getFechafinal()->format('m'))- $entity->getFechaSolicitud()->format('m');
            $difDias =(($difMes*30)+$entity->getFechafinal()->format('d')) - $entity->getFechaSolicitud()->format('d');

           // if($entity->getFechafinal()->format('Y-m')==$entity->getFechaSolicitud()->format('Y-m')||$entity->getFechafinal()->format('Y-m')>$entity->getFechaSolicitud()->format('Y-m')||$entity->getFechafinal()->format('Y-m')<$entity->getFechaSolicitud()->format('Y-m')){
                if($difMes<=1) {
                    if ($difDias < 30) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'El plazo mínimo para crear un Ahorro a Plazo Fijo es de un mes o una diferencia de 30 días superiores a la fecha de ingreso'
                        );
                        return $this->redirect($this->generateUrl('ahorro_create'));
                    }
                }
           // }

        }

        $pagosrealizados = $em->getRepository(
            'ModulosPersonasBundle:PagoCuotaAhorro'
        )->findPagosCuotasAhorros($id);

        $amortizaciones = $em->getRepository(
            'ModulosPersonasBundle:TablaAmortizacionAhorro'
        )->findTablasAmortizacionPorAhorros($id);
//        echo count($amortizaciones);
//        die();
        /*for ($i = 0; $i < count($amortizaciones); $i++) {
            $amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
            $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
            $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
            $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));
        }*/

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ahorro entity.');
        }

        


        if ($editForm->isValid()) {
            $entity->setValorEnCaja($valorCaja);
            $entity->setTipoAhorro($tipoAhorro);
            $amortizacionesToDelete = $em->getRepository(
                'ModulosPersonasBundle:TablaAmortizacionAhorro'
            )->findTablasAmortizacionPorAhorros($id);

            foreach ($amortizacionesToDelete as $amort) {
                $em->remove($amort);
            }
            $em->flush();
            if (!$entity->getFrecuenciaDePago() == "" && !$entity->getFechaSolicitud(
                ) == "" && !$entity->getvalorAhorrar(
                ) == "" && !$entity->getTasaInteres() == ""
            ) {
                if($entity->getTipoAhorro()->getMetodoAmortizacionAhorro()->getMetodo() == "FONDO AHORRO"){//Francés
                    $this->getCuotaAhorroConstante($em, $entity);
                }
//

            }
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Se ha actualizado el ahorro'
                );
//                return $this->redirect($this->generateUrl('ahorro', array('id' => $id)));
            return $this->redirect($this->generateUrl(
                //'ahorro_update', array('id' => $entity->getId())));
                'depositoAho', array('persona' => $entity->getPersona()->getId(),'id' => $entity->getId())));

            }
        return $this->render('ModulosPersonasBundle:Ahorro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'tiposahorros'=>$tiposahorros,
            'amortizaciones' => $amortizaciones,
            'pagosrealizados'=>$pagosrealizados

        ));

    }

    public function tablaahorromesesAction($idAhorro, $mes, $ano)
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
        $mesF2=(($mes-1)==0? 12: ($mes-1));
        $anoF2=$ano+1;
        $fecha2->setDate($anoF2, $mesF2, 1);
        $aux1 = $fecha2->format('t');
        $fecha2->setDate($anoF2, $mesF2, $aux1);

        $intervalo = new \DateInterval('P1M');
        $fecha1Iterante=new \DateTime($fecha1->format('Y-m-d'));
        $arrayMeses=array();
        $arrayMesesTexto=array();
        for($m=0; $m<12; $m++){
            $arrayMeses[]=$fecha1Iterante->format('Y-m');
            $arrayMesesTexto[]=$mesesMapAbrev[$fecha1Iterante->format('m')]."-".$fecha1Iterante->format('Y');
            $fecha1Iterante->add($intervalo);
        }
        $mesesTexto=array($arrayMeses[0]=>$arrayMesesTexto[0],$arrayMeses[1]=>$arrayMesesTexto[1],$arrayMeses[2]=>$arrayMesesTexto[2],$arrayMeses[3]=>$arrayMesesTexto[3],$arrayMeses[4]=>$arrayMesesTexto[4],$arrayMeses[5]=>$arrayMesesTexto[5],$arrayMeses[6]=>$arrayMesesTexto[6],$arrayMeses[7]=>$arrayMesesTexto[7],$arrayMeses[8]=>$arrayMesesTexto[8],$arrayMeses[9]=>$arrayMesesTexto[9],$arrayMeses[10]=>$arrayMesesTexto[10],$arrayMeses[11]=>$arrayMesesTexto[11]);

        $fechaIntervaloLabel="De ".$mesesMap[$fecha1->format('m')]." del ".$fecha1->format('Y')
            ." a ".$mesesMap[$fecha2->format('m')]." del ".$fecha2->format('Y');

        $resumenAportes=array();

        $ahorro = $em->getRepository(
            'ModulosPersonasBundle:Ahorro'
        )->find($idAhorro);

        $pagosrealizados = $em->getRepository(
            'ModulosPersonasBundle:PagoCuotaAhorro'
        )->findPagosCuotasAhorrosPorFechas($idAhorro , $fecha1, $fecha2);

        $posibleExtraccion=0;
        foreach ($pagosrealizados as $pago){
            $posibleExtraccion+=($pago->getCuota()*$pago->getTipo());
            if($pago->getFechaDeEntrada()->format("d-m") != $fecha1 && $pago->getTipo()==1){
                $posibleExtraccion+=$pago->getInteres();
            }
        }



            $personaAportes=array(  "ahorro"=>$ahorro,
                "meses"=>array($arrayMeses[0]=>array(),$arrayMeses[1]=>array(),$arrayMeses[2]=>array(),$arrayMeses[3]=>array(),$arrayMeses[4]=>array(),$arrayMeses[5]=>array(),$arrayMeses[6]=>array(),$arrayMeses[7]=>array(),$arrayMeses[8]=>array(),$arrayMeses[9]=>array(),$arrayMeses[10]=>array(),$arrayMeses[11]=>array(),),
                "total"=>0);

            for($j=0; $j<count($pagosrealizados); $j++) {
                $personaAportes["meses"][$pagosrealizados[$j]->getFecha()->format('Y-m')]=$pagosrealizados[$j];
            }

//            $personaAportes["total"]=$totalAporte;
//            $resumenAportes[]=$personaAportes;


//        $totalesMeses=array($arrayMeses[0]=>0,$arrayMeses[1]=>0,$arrayMeses[2]=>0,$arrayMeses[3]=>0,$arrayMeses[4]=>0,$arrayMeses[5]=>0,$arrayMeses[6]=>0,$arrayMeses[7]=>0,$arrayMeses[8]=>0,$arrayMeses[9]=>0,$arrayMeses[10]=>0,$arrayMeses[11]=>0,);
//        for($i=0; $i<count($resumenAportes); $i++){
//            for($j=0; $j<count($arrayMeses); $j++) {
//                $totalesMeses[$arrayMeses[$j]]+=$resumenAportes[$i]["meses"][$arrayMeses[$j]];
//            }
//        }

        return $this->render(
            'ModulosPersonasBundle:Persona:resumenAportes.html.twig',
            array(
                'fechainicial' => $fecha1,
                'fechafinal' => $fecha2,
                'meses'=>$arrayMeses,
                'mesesTotales'=>$personaAportes,
                'mesesTexto'=>$mesesTexto,
                'fechaLabel'=>$fechaIntervaloLabel,
                'pagosrealizados'=>$pagosrealizados,
                'tabla'=>$personaAportes,
            )
        );

    }

    public function getCuotaAhorroConstante($em, $entity){
        $RESTO_A_AMORTIZAR = $entity->getvalorAhorrar();
        //$periodos = $entity->getTipoAhorro()->getPlazo();
        $periodos = $entity->getCuotas();
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
        $rowamortizacion = new TablaAmortizacionAhorro();
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
            $rowamortizacionnueva = new TablaAmortizacionAhorro();
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

    /**
     * Deletes a Ahorro entity.
     *
     */
        public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:Ahorro')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el ahorro'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('ahorro'));
                }
                else
                {
                    throw $e;
                }
            }
            else
            {
                throw $e;
            }
        }
        return $this->redirect($this->generateUrl('ahorro'));
    }

     /**
     * Creates a form to delete a Ahorro entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ahorro_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /*public function libroAhorrosAction()
    {
        $ahorroMeses = array();
        $totalesmeses = array();
        $em = $this->getDoctrine()->getManager();

        $cajahorro=$em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja=$cajahorro->getRazonSocial();//'nombrecaja'=>$nombrecaja,

        $librosOrdenados = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosAhorros();
        if (count($librosOrdenados) > 0) {

            $mes = $librosOrdenados[0]->getFecha()->format('Y-m');
            $ahorroMes = new AhorroMes();
            $ahorroMes->setMes($mes);

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
                $arregloOrdenado = $em->getRepository(
                    'ModulosPersonasBundle:Libro'
                )->findLibrosOrdenadosPorPersonaTipoAhorro(
                    $arrayMeses[$i][1][0]->getFecha(),
                    end($arrayMeses[$i][1])->getFecha()
                );
                $arrayMeses[$i][1] = $arregloOrdenado;
            }

            $ahorro = new AhorroLista();
            $ahorroMeses = array();
            for ($i = 0; $i < count($arrayMeses); $i++) {
                $ahorroMes = new AhorroMes();
                $ahorroMes->setMes($arrayMeses[$i][0]);
                $personasMes = array();
                $persona = $arrayMeses[$i][1][0]->getPersona();
                $ahorroPersonaMes = new AhorroPersonaMes();
                $ahorroPersonaMes->setPersona($persona);
                //$ahorroPersonaMes->setCuenta($arrayMeses[$i][1][0]->getCuentaid());

                for ($j = 0; $j < count($arrayMeses[$i][1]); $j++) {

                    $libro = $arrayMeses[$i][1][$j];
                    if ($persona->getId() == $libro->getPersona()->getId()) {
                        if ($libro->getProductoContableId()->getTipo() == "Ahorro a Vista") {
                            $ahorroPersonaMes->setAhorroVista(
                                $ahorroPersonaMes->getAhorroVista() + $libro->getDebe()
                            );
                        } else {
                            if ($libro->getProductoContableId()->getTipo() == "Ahorro Plazo Fijo") {
                                $ahorroPersonaMes->setAhorroPlazoFijo(
                                    $ahorroPersonaMes->getAhorroPlazoFijo() + $libro->getDebe()
                                );
                            } else {
                                if ($libro->getProductoContableId()->getTipo() == "Ahorro Restringido/Encaje") {
                                    $ahorroPersonaMes->setAhorroRestringido(
                                        $ahorroPersonaMes->getAhorroRestringido()+ $libro->getDebe()
                                    );
                                } else {
                                    if ($libro->getProductoContableId()->getTipo() == "Retiro de Ahorro a la Vista") {
                                        $ahorroPersonaMes->setRetiroAhorroVista(
                                            $ahorroPersonaMes->getRetiroAhorroVista() + $libro->getHaber()
                                        );
                                    } else {
                                        if ($libro->getProductoContableId()->getTipo() == "Retiro de Ahorro Plazo Fijo") {
                                            $ahorroPersonaMes->setRetiroAhorroPlazoFijo(
                                            $ahorroPersonaMes->getRetiroAhorroPlazoFijo() + $libro->getHaber()
                                            );
                                        } else {
                                            if ($libro->getProductoContableId()->getTipo() == "Retiro Ahorro Restringido") {
                                                $ahorroPersonaMes->setRetiroAhorroRestringido(
                                                    $ahorroPersonaMes->getRetiroAhorroRestringido() + $libro->getHaber()
                                                );
                                            }else {
                                                if ($libro->getProductoContableId()->getTipo() == "Pago Intereses Ahorros") {
                                                    $ahorroPersonaMes->setInteresPagado(
                                                        $ahorroPersonaMes->getInteresPagado() + $libro->getHaber()
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $ahorroPersonaMes->setSaldoAnterior(
                            $ahorro->getSaldoAnterior($ahorroMeses, $ahorroMes, $ahorroPersonaMes->getPersona())
                        );
                        
                        $ahorroPersonaMes->setCreditoMicroEmpPorVencerSaldoCap(
                            $ahorroPersonaMes->getAhorroVista() + $ahorroPersonaMes->getSaldoAnterior() 
                        );

                        $ahorroPersonaMes->updateTotalPagado();
                        $ahorroPersonaMes->updateSaldoFinalAhorros();

                        $personasMes[] = $ahorroPersonaMes;

                        $persona = $libro->getPersona();

                        $ahorroPersonaMes = new AhorroPersonaMes();
                        $ahorroPersonaMes->setPersona($libro->getPersona());
                        //$ahorroPersonaMes->setCuenta($libro->getCuentaid());

                        if ($libro->getProductoContableId()->getTipo() == "Ahorro a Vista") {
                            $ahorroPersonaMes->setAhorroVista(
                                $ahorroPersonaMes->getAhorroVista() + $libro->getDebe()
                            );
                        } else {
                            if ($libro->getProductoContableId()->getTipo() == "Ahorro Plazo Fijo") {
                                $ahorroPersonaMes->setAhorroPlazoFijo(
                                    $ahorroPersonaMes->getAhorroPlazoFijo() + $libro->getDebe()
                                );
                            } else {
                                if ($libro->getProductoContableId()->getTipo() == "Ahorro Restringido/Encaje") {
                                    $ahorroPersonaMes->setAhorroRestringido(
                                        $ahorroPersonaMes->getAhorroRestringido() + $libro->getDebe()
                                    );
                                } else {
                                    if ($libro->getProductoContableId()->getTipo() == "Retiro de Ahorro a la Vista") {
                                        $ahorroPersonaMes->setRetiroAhorroVista(
                                            $ahorroPersonaMes->getRetiroAhorroVista() + $libro->getHaber()
                                        );
                                    } else {
                                        if ($libro->getProductoContableId()->getTipo() == "Retiro de Ahorro Plazo Fijo") {
                                            $ahorroPersonaMes->setRetiroAhorroPlazoFijo(
                                            $ahorroPersonaMes->getRetiroAhorroPlazoFijo() + $libro->getHaber()
                                            );
                                        } else {
                                            if ($libro->getProductoContableId()->getTipo() == "Retiro Ahorro Restringido") {
                                                $ahorroPersonaMes->setRetiroAhorroRestringido(
                                                    $ahorroPersonaMes->getRetiroAhorroRestringido() + $libro->getHaber()
                                                );
                                            }else {
                                                if ($libro->getProductoContableId()->getTipo() == "Pago Intereses Ahorros") {
                                                    $ahorroPersonaMes->setInteresPagado(
                                                        $ahorroPersonaMes->getInteresPagado() + $libro->getHaber()
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $ahorroPersonaMes->setSaldoAnterior(
                    $ahorro->getSaldoAnterior($ahorroMeses, $ahorroMes, $ahorroPersonaMes->getPersona())
                );
                
                $ahorroPersonaMes->setCreditoMicroEmpPorVencerSaldoCap(
                    $ahorroPersonaMes->getAhorroVista() + $ahorroPersonaMes->getSaldoAnterior() 
                );
                
                $ahorroPersonaMes->updateTotalPagado();
                $ahorroPersonaMes->updateSaldoFinalAhorros();
                $personasMes[] = $ahorroPersonaMes;

                $ahorroMes->setPersonaMes($personasMes);
                $ahorroMeses[] = $ahorroMes;
                
            }
        }
        foreach ($ahorroMeses as $ahorroMes) {
            $personaTotal=new AhorroPersonaMes();
            foreach ($ahorroMes->getPersonasMes() as $ahorroPersonaMes) {

                $personaTotal->setSaldoAnterior($personaTotal->getSaldoAnterior()+$ahorroPersonaMes->getSaldoAnterior());
                $personaTotal->setAhorroVista($personaTotal->getAhorroVista()+$ahorroPersonaMes->getAhorroVista());
                $personaTotal->setAhorroPlazoFijo($personaTotal->getAhorroPlazoFijo()+$ahorroPersonaMes->getAhorroPlazoFijo());
                $personaTotal->setAhorroRestringido($personaTotal->getAhorroRestringido()+$ahorroPersonaMes->getAhorroRestringido());
                $personaTotal->setRetiroAhorroVista($personaTotal->getRetiroAhorroVista()+$ahorroPersonaMes->getRetiroAhorroVista());
                $personaTotal->setRetiroAhorroPlazoFijo($personaTotal->getRetiroAhorroPlazoFijo()+$ahorroPersonaMes->getRetiroAhorroPlazoFijo());
                $personaTotal->setRetiroAhorroRestringido($personaTotal->getRetiroAhorroRestringido()+$ahorroPersonaMes->getRetiroAhorroRestringido());
                $personaTotal->setInteresPagado($personaTotal->getInteresPagado()+$ahorroPersonaMes->getInteresPagado());
                $personaTotal->setTotalPagado($personaTotal->getTotalPagado()+$ahorroPersonaMes->getTotalPagado());
                $personaTotal->setSaldoFinalAhorros($personaTotal->getSaldoFinalAhorros()+$ahorroPersonaMes->getSaldoFinalAhorros());
            }
            $ahorroMes->setTotalesMes($personaTotal);
        }

        return $this->render(
            'ModulosPersonasBundle:Ahorro:libroAhorros.html.twig',
            array(
                'ahorromeses' => $ahorroMeses,
                'nombrecaja'=>$nombrecaja,
            )
        );

    }*/

    public function libroAhorrosAction()
    {
        $arrayMeses=array();
        $arrayMesesTexto=array();
        $salidaAhorro=array();

        $em = $this->getDoctrine()->getManager();

        $cajahorro=$em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja=$cajahorro->getRazonSocial();

        //$personas=$em->getRepository('ModulosPersonasBundle:Persona')->findOrdenados();
        //$personas=$em->getRepository('ModulosPersonasBundle:Libro')->findPersonasAhorros();
        $codPersonas=$em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosOrdenadosPorFecha();

        $cantPersonas=count( $codPersonas );
        if($cantPersonas==0){
            return $this->render(
                'ModulosPersonasBundle:Ahorro:libroAhorros2.html.twig',
                array(
                    'meses'=>$arrayMeses,
                    'mesesTexto'=>$arrayMesesTexto,
                    'ahorros'=>$salidaAhorro,
                    'nombrecaja'=>$nombrecaja,
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
        if(count($libros )>0){
            $fecha1 = $libros[0]->getFecha();
            $fecha2 = $libros[count($libros)-1]->getFecha();;

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

        }else{
            return $this->render(
                'ModulosPersonasBundle:Ahorro:libroAhorros2.html.twig',
                array(
                    'meses'=>$arrayMeses,
                    'mesesTexto'=>$arrayMesesTexto,
                    'ahorros'=>$salidaAhorro,
                    'nombrecaja'=>$nombrecaja,

                )
            );
        }

        $intervalo = new \DateInterval('P1M');
        $fecha1Iterante=new \DateTime($fecha1->format('Y-m-d'));
        $valores=array();

        while($fecha1Iterante<=$fecha2){
            $arrayMeses[]=$fecha1Iterante->format('Y-m');
            $arrayMesesTexto[$fecha1Iterante->format('Y-m')]="Del 1 al ".$fecha1Iterante->format('t')." de ".$mesesMap[$fecha1Iterante->format('m')]." del ".$fecha1Iterante->format('Y');
            $valores[$fecha1Iterante->format('Y-m')]=0;
            //$valoresS[$fecha1Iterante->format('Y-m')]="";
            $fecha1Iterante->add($intervalo);
        }

        $resumenAhorros=array();

        for($i=0; $i<$cantPersonas; $i++){

            $personas=$em->getRepository('ModulosPersonasBundle:Persona')->find($codPersonas[$i][1]);

            $personaCartera = array(
                "persona" => $personas,
                "depoVista"=>$valores,
                "depoPlazoFijo"=>$valores,
                "depoRestringido"=>$valores,
                "retiroAhorrosVista"=>$valores,
                "retiroAhorrosPlazoFijo"=>$valores,
                "retiroAhorrosRestringidos"=>$valores,
                "interes"=>$valores,
                "saldoAnterior"=>$valores,
                "saldoAhorros"=>$valores,
                "saldoFinalAhorros"=>$valores,
            );

            $ahorros = $em->getRepository('ModulosPersonasBundle:Libro')->findAhorrosPorPersona($personas);

            for($j=0; $j<count($ahorros); $j++) {

                if ($personas->getId() == $ahorros[$j]->getPersona()->getId()) {
                    if ($ahorros[$j]->getProductoContableId()->getId() == 12) {
                        $personaCartera["depoVista"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getDebe();
                    } else {
                        if ($ahorros[$j]->getProductoContableId()->getId() == 13) {
                            $personaCartera["depoPlazoFijo"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getDebe();
                        } else {
                            if ($ahorros[$j]->getProductoContableId()->getId() == 14) {
                                $personaCartera["depoRestringido"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getDebe();
                            } else {
                                if ($ahorros[$j]->getProductoContableId()->getId() == 15 ) {
                                    $personaCartera["retiroAhorrosVista"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getHaber();
                                }else {
                                    if ($ahorros[$j]->getProductoContableId()->getId() == 17) {
                                        $personaCartera["retiroAhorrosPlazoFijo"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getHaber();
                                    }else {
                                        if ($ahorros[$j]->getProductoContableId()->getId() == 16) {
                                            $personaCartera["retiroAhorrosRestringidos"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getHaber();
                                        }else {
                                            if ($ahorros[$j]->getProductoContableId()->getId() == 20) {
                                                $personaCartera["interes"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getHaber();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //$personaCartera["saldoAhorros"][$ahorros[$j]->getFecha()->format('Y-m')]=$personaCartera["saldoAnterior"][$ahorros[$j]->getFecha()->format('Y-m')] + $personaCartera["depoPlazoFijo"][$ahorros[$j]->getFecha()->format('Y-m')] - $personaCartera["retiroAhorrosPlazoFijo"][$ahorros[$j]->getFecha()->format('Y-m')];
                    //$personaCartera["saldoFinalAhorros"][$ahorros[$j]->getFecha()->format('Y-m')]=$personaCartera["saldoAnterior"][$ahorros[$j]->getFecha()->format('Y-m')] + $personaCartera["saldoAhorros"][$ahorros[$j]->getFecha()->format('Y-m')] + ($personaCartera["depoVista"][$ahorros[$j]->getFecha()->format('Y-m')]- $personaCartera["retiroAhorrosVista"][$ahorros[$j]->getFecha()->format('Y-m')])+ ($personaCartera["depoRestringido"][$ahorros[$j]->getFecha()->format('Y-m')]- $personaCartera["retiroAhorrosRestringidos"][$ahorros[$j]->getFecha()->format('Y-m')]);
                }
            }


            $resumenAhorros[]=$personaCartera;
        }
        for($i=0; $i<count($resumenAhorros); $i++){
            for($j=0; $j<count($arrayMeses); $j++) {
                $resumenAhorros[$i]["saldoAnterior"][$arrayMeses[$j]]= (($j>0) ? $resumenAhorros[$i]["saldoFinalAhorros"][$arrayMeses[$j-1]]: 0);
                $resumenAhorros[$i]["saldoAhorros"][$arrayMeses[$j]]= $resumenAhorros[$i]["saldoAnterior"][$arrayMeses[$j]] + $resumenAhorros[$i]["depoPlazoFijo"][$arrayMeses[$j]]-$resumenAhorros[$i]["retiroAhorrosPlazoFijo"][$arrayMeses[$j]];
                $resumenAhorros[$i]["saldoFinalAhorros"][$arrayMeses[$j]]= $resumenAhorros[$i]["saldoAhorros"][$arrayMeses[$j]]+($resumenAhorros[$i]["depoVista"][$arrayMeses[$j]]-$resumenAhorros[$i]["retiroAhorrosVista"][$arrayMeses[$j]])+($resumenAhorros[$i]["depoRestringido"][$arrayMeses[$j]]-$resumenAhorros[$i]["retiroAhorrosRestringidos"][$arrayMeses[$j]]);
            }
        }

        for($j=0; $j<count($arrayMeses); $j++) {
            $salidaAhorro[$arrayMeses[$j]]=array();
            for($i=0; $i<count($resumenAhorros); $i++){

                if( $resumenAhorros[$i]["saldoAnterior"][$arrayMeses[$j]]!=0||
                    $resumenAhorros[$i]["depoVista"][$arrayMeses[$j]]!=0||
                    $resumenAhorros[$i]["depoPlazoFijo"][$arrayMeses[$j]]!=0||
                    $resumenAhorros[$i]["depoRestringido"][$arrayMeses[$j]]!=0||
                    $resumenAhorros[$i]["retiroAhorrosVista"][$arrayMeses[$j]]!=0||
                    $resumenAhorros[$i]["retiroAhorrosPlazoFijo"][$arrayMeses[$j]]!=0||
                    $resumenAhorros[$i]["interes"][$arrayMeses[$j]]!=0
                ) {
                    $personaMes = array();
                    $personaMes["persona"] = $resumenAhorros[$i]["persona"];
                    $personaMes["saldoAnterior"] = $resumenAhorros[$i]["saldoAnterior"][$arrayMeses[$j]];
                    $personaMes["depoVista"] = $resumenAhorros[$i]["depoVista"][$arrayMeses[$j]];
                    $personaMes["depoPlazoFijo"] = $resumenAhorros[$i]["depoPlazoFijo"][$arrayMeses[$j]];
                    $personaMes["depoRestringido"] = $resumenAhorros[$i]["depoRestringido"][$arrayMeses[$j]];
                    $personaMes["retiroAhorrosVista"] = $resumenAhorros[$i]["retiroAhorrosVista"][$arrayMeses[$j]];
                    $personaMes["retiroAhorrosPlazoFijo"] = $resumenAhorros[$i]["retiroAhorrosPlazoFijo"][$arrayMeses[$j]];
                    $personaMes["retiroAhorrosRestringidos"] = $resumenAhorros[$i]["retiroAhorrosRestringidos"][$arrayMeses[$j]];
                    $personaMes["interes"] = $resumenAhorros[$i]["interes"][$arrayMeses[$j]];
                    $personaMes["saldoAhorros"] = $resumenAhorros[$i]["saldoAhorros"][$arrayMeses[$j]];
                    $personaMes["saldoFinalAhorros"] = $resumenAhorros[$i]["saldoFinalAhorros"][$arrayMeses[$j]];
                    $salidaAhorro[$arrayMeses[$j]][] = $personaMes;
                }
            }

        }

        return $this->render(
            'ModulosPersonasBundle:Ahorro:libroAhorros2.html.twig',
            array(
                'meses'=>$arrayMeses,
                'mesesTexto'=>$arrayMesesTexto,
                'ahorros'=>$salidaAhorro,
                'nombrecaja'=>$nombrecaja,
            )
        );

    }

    public
    function exportarAhorroLibroAction()
    {
        $arrayMeses=array();
        $arrayMesesTexto=array();
        $salidaAhorro=array();

        $em = $this->getDoctrine()->getManager();

        $cajahorro=$em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja=$cajahorro->getRazonSocial();

        //$personas=$em->getRepository('ModulosPersonasBundle:Persona')->findOrdenados();
        //$personas=$em->getRepository('ModulosPersonasBundle:Libro')->findPersonasAhorros();
        $codPersonas=$em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosOrdenadosPorFecha();

        $cantPersonas=count( $codPersonas );
        if($cantPersonas==0){
            return $this->render(
                'ModulosPersonasBundle:Ahorro:libroAhorros2.html.twig',
                array(
                    'meses'=>$arrayMeses,
                    'mesesTexto'=>$arrayMesesTexto,
                    'ahorros'=>$salidaAhorro,
                    'nombrecaja'=>$nombrecaja,
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
        if(count($libros )>0){
            $fecha1 = $libros[0]->getFecha();
            $fecha2 = $libros[count($libros)-1]->getFecha();;

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

        }else{
            return $this->render(
                'ModulosPersonasBundle:Ahorro:libroAhorros2.html.twig',
                array(
                    'meses'=>$arrayMeses,
                    'mesesTexto'=>$arrayMesesTexto,
                    'ahorros'=>$salidaAhorro,
                    'nombrecaja'=>$nombrecaja,

                )
            );
        }

        $intervalo = new \DateInterval('P1M');
        $fecha1Iterante=new \DateTime($fecha1->format('Y-m-d'));
        $valores=array();

        while($fecha1Iterante<=$fecha2){
            $arrayMeses[]=$fecha1Iterante->format('Y-m');
            $arrayMesesTexto[$fecha1Iterante->format('Y-m')]="Del 1 al ".$fecha1Iterante->format('t')." de ".$mesesMap[$fecha1Iterante->format('m')]." del ".$fecha1Iterante->format('Y');
            $valores[$fecha1Iterante->format('Y-m')]=0;
            //$valoresS[$fecha1Iterante->format('Y-m')]="";
            $fecha1Iterante->add($intervalo);
        }

        $resumenAhorros=array();

        for($i=0; $i<$cantPersonas; $i++){

            $personas=$em->getRepository('ModulosPersonasBundle:Persona')->find($codPersonas[$i][1]);

            $personaCartera = array(
                "persona" => $personas,
                "depoVista"=>$valores,
                "depoPlazoFijo"=>$valores,
                "depoRestringido"=>$valores,
                "retiroAhorrosVista"=>$valores,
                "retiroAhorrosPlazoFijo"=>$valores,
                "retiroAhorrosRestringidos"=>$valores,
                "interes"=>$valores,
                "saldoAnterior"=>$valores,
                "saldoAhorros"=>$valores,
                "saldoFinalAhorros"=>$valores,
            );

            $ahorros = $em->getRepository('ModulosPersonasBundle:Libro')->findAhorrosPorPersona($personas);

            for($j=0; $j<count($ahorros); $j++) {

                if ($personas->getId() == $ahorros[$j]->getPersona()->getId()) {
                    if ($ahorros[$j]->getProductoContableId()->getId() == 12) {
                        $personaCartera["depoVista"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getDebe();
                    } else {
                        if ($ahorros[$j]->getProductoContableId()->getId() == 13) {
                            $personaCartera["depoPlazoFijo"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getDebe();
                        } else {
                            if ($ahorros[$j]->getProductoContableId()->getId() == 14) {
                                $personaCartera["depoRestringido"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getDebe();
                            } else {
                                if ($ahorros[$j]->getProductoContableId()->getId() == 15 ) {
                                    $personaCartera["retiroAhorrosVista"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getHaber();
                                }else {
                                    if ($ahorros[$j]->getProductoContableId()->getId() == 17) {
                                        $personaCartera["retiroAhorrosPlazoFijo"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getHaber();
                                    }else {
                                        if ($ahorros[$j]->getProductoContableId()->getId() == 16) {
                                            $personaCartera["retiroAhorrosRestringidos"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getHaber();
                                        }else {
                                            if ($ahorros[$j]->getProductoContableId()->getId() == 20) {
                                                $personaCartera["interes"][$ahorros[$j]->getFecha()->format('Y-m')]+=$ahorros[$j]->getHaber();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //$personaCartera["saldoAhorros"][$ahorros[$j]->getFecha()->format('Y-m')]=$personaCartera["saldoAnterior"][$ahorros[$j]->getFecha()->format('Y-m')] + $personaCartera["depoPlazoFijo"][$ahorros[$j]->getFecha()->format('Y-m')] - $personaCartera["retiroAhorrosPlazoFijo"][$ahorros[$j]->getFecha()->format('Y-m')];
                    //$personaCartera["saldoFinalAhorros"][$ahorros[$j]->getFecha()->format('Y-m')]=$personaCartera["saldoAnterior"][$ahorros[$j]->getFecha()->format('Y-m')] + $personaCartera["saldoAhorros"][$ahorros[$j]->getFecha()->format('Y-m')] + ($personaCartera["depoVista"][$ahorros[$j]->getFecha()->format('Y-m')]- $personaCartera["retiroAhorrosVista"][$ahorros[$j]->getFecha()->format('Y-m')])+ ($personaCartera["depoRestringido"][$ahorros[$j]->getFecha()->format('Y-m')]- $personaCartera["retiroAhorrosRestringidos"][$ahorros[$j]->getFecha()->format('Y-m')]);
                }
            }


            $resumenAhorros[]=$personaCartera;
        }
        for($i=0; $i<count($resumenAhorros); $i++){
            for($j=0; $j<count($arrayMeses); $j++) {
                $resumenAhorros[$i]["saldoAnterior"][$arrayMeses[$j]]= (($j>0) ? $resumenAhorros[$i]["saldoFinalAhorros"][$arrayMeses[$j-1]]: 0);
                $resumenAhorros[$i]["saldoAhorros"][$arrayMeses[$j]]= $resumenAhorros[$i]["saldoAnterior"][$arrayMeses[$j]] + $resumenAhorros[$i]["depoPlazoFijo"][$arrayMeses[$j]]-$resumenAhorros[$i]["retiroAhorrosPlazoFijo"][$arrayMeses[$j]];
                $resumenAhorros[$i]["saldoFinalAhorros"][$arrayMeses[$j]]= $resumenAhorros[$i]["saldoAhorros"][$arrayMeses[$j]]+($resumenAhorros[$i]["depoVista"][$arrayMeses[$j]]-$resumenAhorros[$i]["retiroAhorrosVista"][$arrayMeses[$j]])+($resumenAhorros[$i]["depoRestringido"][$arrayMeses[$j]]-$resumenAhorros[$i]["retiroAhorrosRestringidos"][$arrayMeses[$j]]);
            }
        }

        for($j=0; $j<count($arrayMeses); $j++) {
            $salidaAhorro[$arrayMeses[$j]]=array();
            for($i=0; $i<count($resumenAhorros); $i++){

                if( $resumenAhorros[$i]["saldoAnterior"][$arrayMeses[$j]]!=0||
                    $resumenAhorros[$i]["depoVista"][$arrayMeses[$j]]!=0||
                    $resumenAhorros[$i]["depoPlazoFijo"][$arrayMeses[$j]]!=0||
                    $resumenAhorros[$i]["depoRestringido"][$arrayMeses[$j]]!=0||
                    $resumenAhorros[$i]["retiroAhorrosVista"][$arrayMeses[$j]]!=0||
                    $resumenAhorros[$i]["retiroAhorrosPlazoFijo"][$arrayMeses[$j]]!=0||
                    $resumenAhorros[$i]["interes"][$arrayMeses[$j]]!=0
                ) {
                    $personaMes = array();
                    $personaMes["persona"] = $resumenAhorros[$i]["persona"];
                    $personaMes["saldoAnterior"] = $resumenAhorros[$i]["saldoAnterior"][$arrayMeses[$j]];
                    $personaMes["depoVista"] = $resumenAhorros[$i]["depoVista"][$arrayMeses[$j]];
                    $personaMes["depoPlazoFijo"] = $resumenAhorros[$i]["depoPlazoFijo"][$arrayMeses[$j]];
                    $personaMes["depoRestringido"] = $resumenAhorros[$i]["depoRestringido"][$arrayMeses[$j]];
                    $personaMes["retiroAhorrosVista"] = $resumenAhorros[$i]["retiroAhorrosVista"][$arrayMeses[$j]];
                    $personaMes["retiroAhorrosPlazoFijo"] = $resumenAhorros[$i]["retiroAhorrosPlazoFijo"][$arrayMeses[$j]];
                    $personaMes["retiroAhorrosRestringidos"] = $resumenAhorros[$i]["retiroAhorrosRestringidos"][$arrayMeses[$j]];
                    $personaMes["interes"] = $resumenAhorros[$i]["interes"][$arrayMeses[$j]];
                    $personaMes["saldoAhorros"] = $resumenAhorros[$i]["saldoAhorros"][$arrayMeses[$j]];
                    $personaMes["saldoFinalAhorros"] = $resumenAhorros[$i]["saldoFinalAhorros"][$arrayMeses[$j]];
                    $salidaAhorro[$arrayMeses[$j]][] = $personaMes;
                }
            }

        }

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Conquito")
            ->setLastModifiedBy("Conquito")
            ->setTitle("Libro de Ahorros")
            ->setSubject("Libro de Ahorros")
            ->setDescription("Libro de Ahorros")
            ->setKeywords("Libro de Ahorros")
            ->setCategory("Reporte excel");

        //$tituloReporte1 = "Listado de libros de cajas por meses de:".$fecha1->format('d-m-Y').' a '.$fecha2->format('d-m-Y');
        $tituloReporte = "LIBRO GENERAL DE AHORROS";
        $tituloHoja = "Libro de Ahorros";

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
        $estiloCeldaIzq= array(
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
        $phpExcelObject->getActiveSheet()->getStyle('A1:J1')->applyFromArray($estiloTituloCartera);

        $titulosColumnas = array(
            'No ',
            'Nombre y Apellidos',
            'Saldo Anterior',
            'Depósito a la Vista',
            'Depósito a Plazo Fijo',
            'Depósito Restringido',
            'Retiro de Ahorros',
            'Interés Pagado',
            'Saldo de Ahorros a Plazo Fijo',
            'Saldo Final de Ahorros',
        );
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A1:J1');


        $i = 4;
        $mesIndex=0;
        foreach($arrayMeses as $mes){

            // Se agregan los titulos del reporte
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $nombrecaja);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($estiloSubCabCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':J'.$i);
            $i++;

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $arrayMesesTexto[$mes]);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($estiloTituloCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':J'.$i);
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
            )

            ;$phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, $titulosColumnas[5]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('F'.$i.':F'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i.':F'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            )

            ;$phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, $titulosColumnas[6]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('G'.$i.':G'.($i + 1));
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i.':G'.($i + 1))->applyFromArray(
                $estiloTituloColumnasCartera
            );

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


            $i++;
            $i++;
            $iIndice = $i;
            $cont = 1;
            foreach ($salidaAhorro[$mes] as $personaM) {
                $personaNombre = $personaM["persona"]->__toString();
                $saldoAnterior = $personaM["saldoAnterior"];
                $depoVista = $personaM["depoVista"];
                $depoPlazoFijo = $personaM["depoPlazoFijo"];
                $depoRestringido = $personaM["depoRestringido"];
                $retiroAhorros = $personaM["retiroAhorrosVista"]+$personaM["retiroAhorrosPlazoFijo"]+$personaM["retiroAhorrosRestringidos"];
                $interes = $personaM["interes"];
                $saldoAhorros = $personaM["saldoAhorros"];
                $saldoFinalAhorros = $personaM["saldoFinalAhorros"];

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $cont++);
                $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('B'.$i, $personaNombre);
                $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C'.$i, number_format($saldoAnterior, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('D'.$i, number_format($depoVista, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('E'.$i, number_format($depoPlazoFijo, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('F'.$i, number_format($depoRestringido, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('G'.$i, number_format($retiroAhorros, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('H'.$i, number_format($interes, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('H'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('H'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('I'.$i, number_format($saldoAhorros, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('I'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('I'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('J'.$i, number_format($saldoFinalAhorros, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('J'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('J'.$i)->applyFromArray($estiloCeldaDerecha);

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
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);

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
            ->setWidth(16);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(16);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(16);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('F')
            ->setWidth(16);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('G')
            ->setWidth(16);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('H')
            ->setWidth(16);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('I')
            ->setWidth(16);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('J')
            ->setWidth(16);

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
            'Libro Ahorros.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;

    }

    public
    function depositoAction(Request $request, $persona, $id) {
        $em = $this->getDoctrine()->getManager();

        $idAhorros = $em->getRepository('ModulosPersonasBundle:Ahorro')->find($id);

        $valorAhorrar = $idAhorros->getvalorAhorrar();

        //echo $idAhorros->getTipoAhorro();

        if($idAhorros->getTipoAhorro()=='Ahorro a Vista')
        {
            $formulario = new DepositoVistaType();
        }elseif($idAhorros->getTipoAhorro()=='Ahorro Plazo Fijo')
        {
            $formulario = new DepositoPlazoFijoType();
        }elseif($idAhorros->getTipoAhorro()=='Ahorro Restringido/Encaje')
        {
            $formulario = new DepositoRestringidoType();
        }

        $entity = new Libro();
        $form = $this->createForm($formulario, $entity);
        $form->handleRequest($request);
        $idpersona = $em->getRepository('ModulosPersonasBundle:Persona')->find($persona);
        $entity->setPersona($idpersona);
        $entity->setHaber(0);
        $entity->setFecha($idAhorros->getFechaSolicitud());
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
            $form = $this->createForm($formulario, $entity);
            $form->handleRequest($request);
        } else {
            $consec = 1;
        }

        if (count($libros) > 0) {
            $this->actualizarSaldo($libros[count($libros)-1]);
            $saldo = $libros[0]->getSaldo();

        } else {
            $saldo = 0;
        }



        $tmp = '';

        if ($form->isValid()) {
            $tipoTransaccion=$entity->getProductoContableId();
            if (count($libros) > 0) {
                $ano = $libros[0]->getFecha()->format('Y');
                $anoParametro = $entity->getFecha()->format('Y');
                $mes = $libros[0]->getFecha()->format('m');
                $mesParametro = $entity->getFecha()->format('m');

                $fechaInicial = $libros[count($libros)-1]->getFecha();
                $fechaIngreso = $entity->getFecha();


                $librosAnteriores=$em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaDescId($fechaInicial,$fechaIngreso);

                if(count($librosAnteriores)!=1 && count($librosAnteriores)>0){
                    $saldoAnterior=$librosAnteriores[0]->getSaldo();
                }

                $fechaUltima = $libros[0]->getFecha();
                $librosPosteriores=$em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaAscId($fechaIngreso,$fechaUltima);
                $evalua=0;
                if(count($librosPosteriores)>0){
                    $librosPosteriores[count($librosPosteriores)-1]->getSaldo();
                    for($i=1;$i<count($librosPosteriores);){
                        if(($librosPosteriores[$i]->getSaldo()-$entity->getHaber())<0){
                            $evalua=1;
                            break;
                        }else{
                            $i++;
                        }
                    }
                }

                //if ($entity->getSaldo() < 0) {
                if (($saldoAnterior - $entity->getHaber()) < 0 || $evalua==1) {
                    $this->get('session')->getFlashBag()->add(
                        'danger',
                        //'No se puede realizar la transacción, la caja no dispone de dinero suficiente.'
                        'No se puede realizar la transacción, la caja no dispone de dinero suficiente en la fecha solicitada.'
                    );

                    return $this->redirect($this->generateUrl('depositoAho', array('persona' => $entity->getPersona()->getId(),'id' => $entity->getId())));
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

                    if ($libro->getEstadosLibro()->getEstado() == 'CERRADO' && $libro->getFecha()->format('m-Y') == $entity->getFecha()->format('m-Y')
                    ) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'No se puede crear un libro en un mes cerrado.'
                        );

                        return $this->redirect($this->generateUrl('depositoAho', array('persona' => $entity->getPersona()->getId(),'id' => $entity->getId())));
                    }


                }

            }
            $estadoLibro = $em->getRepository('ModulosPersonasBundle:EstadosLibro')->findOneByEstado("ABIERTO");
            if ($tipoTransaccion->getId() == 12 || $tipoTransaccion->getId() == 13 || $tipoTransaccion->getId() == 14 ){//Ahorros Depositos

                        $estadoAhorro = $em->getRepository('ModulosPersonasBundle:EstadoAhorro')->find(6);
                        $idAhorro = $entity->getInfo();
                        $ahorro = $em->getRepository(
                            'ModulosPersonasBundle:Ahorro'
                        )->find($idAhorro);



                        $ahorro->setEstadoAhorro($estadoAhorro);

                        $cuotaAhorroList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findPagosCuotasAhorros($idAhorro);
                        $depositosList = $em->getRepository('ModulosPersonasBundle:PagoCuotaAhorro')->findDepositosAhorros($idAhorro);
                        $saldoAnterior=0;
                        $fechaAnterior=new \DateTime();
                        if(count($cuotaAhorroList)>0){
                            $saldoAnterior=$cuotaAhorroList[count($cuotaAhorroList)-1]->getCuotaAcumulada();
                        }
                        if(count($depositosList)>0){
                            $fechaAnterior=$depositosList[count($depositosList)-1]->getFechaDeEntrada();
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
                        $interes=0;
                        $cuotaAhorro->setInteres($interes);
                        $cuotaAhorro->setCuotaAcumulada($interes + $saldoAnterior + $entity->getDebe());

                        $ahorro->setValorEnCaja($ahorro->getValorEnCaja() + $entity->getDebe());

                        switch ($tipoTransaccion->getId()){
                            case 12:{

                            }break;
                            case 13:{

                            }break;
                            case 14:{

                            }break;
                        }


                        $em = $this->getDoctrine()->getManager();
                        $entity->setEstadosLibro($estadoLibro);
                        $entity->setNumeroRecibo($entity->getNumeroRecibo());

                $this->actualizarSaldo($libros[count($libros)-1]);

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



            }
            else{

                        $em = $this->getDoctrine()->getManager();
                        $entity->setEstadosLibro($estadoLibro);
                        $entity->setNumeroRecibo($entity->getNumeroRecibo());

                $this->actualizarSaldo($libros[count($libros)-1]);

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
            if($tipoTransaccion->getId() != 9){
                //$this->actualizarSaldo($entity);
            }

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado correctamente.'
            );

            //return $this->redirect($this->generateUrl('ahorro_update', array('id' => $ahorro->getId())));
            return $this->redirect($this->generateUrl('ahorro'));

        }

        return $this->render(
            'ModulosPersonasBundle:Ahorro:depositoIni.html.twig',
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
                'id'=>$id,
                'persona'=>$persona,
                'ahorro'=>$idAhorros,
                'valorAhorrar'=>$valorAhorrar,
            )
        );
    }

    public function actualizarSaldo($entity){

        $fechaIngreso =$entity->getFecha();
        $debe = $entity->getDebe();
        $haber = $entity->getHaber();

        $em = $this->getDoctrine()->getManager();
        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

        if (count($libros) > 0) {
            $fechaReciente= $libros[0]->getFecha();
            $fechaInicial = $libros[count($libros)-1]->getFecha();
        } else {
            $saldo = 0;
        }
        $saldoAnterior=0;
        $librosAnteriores=$em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaDescId($fechaInicial,$fechaIngreso);
        if(count($librosAnteriores)>0){
            $saldoAnterior=$librosAnteriores[1]->getSaldo();
            $entity->setSaldo($saldoAnterior + $debe- $haber);
        }else{
            if($debe>0){
                $entity->setSaldo($debe);
            }//elseif($haber>0){
            // $entity->setSaldo(0-$haber);
            //}

        }

        $librosPosteriores=$em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosEntreFechaAscId($fechaIngreso,$fechaReciente);
        if(count($librosPosteriores)>0){
            $librosPosteriores[count($librosPosteriores)-1]->getSaldo();
            for($i=0;$i<count($librosPosteriores);$i++){
                if($i>0){
                    $id=$librosPosteriores[$i]->getId();
                    $libroActualizar=$em->getRepository('ModulosPersonasBundle:Libro')->find($id);
                    $libroActualizar->setSaldo($librosPosteriores[$i-1]->getSaldo()+$libroActualizar->getDebe()-$libroActualizar->getHaber());
                    $em->persist($libroActualizar);
                }
            }
        }

        $em->persist($entity);

        $em->flush();

        return $this->redirect($this->generateUrl('ahorro'));

    }

    public function cargarPersonasAhoAction($idTransaccion,$persona,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $flag = 1;
        if ($idTransaccion == 12 || $idTransaccion == 13 || $idTransaccion == 14 ||$idTransaccion == 15 || $idTransaccion == 16 || $idTransaccion == 17 ) {//Ahorros depositos
            switch ($idTransaccion) {
                case 12: {
                    $listaAhorros = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosByTipoAprobadoPorId3(1, $id);
                }break;
                case 13: {
                    $listaAhorros = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosByTipoAprobadoPorId1(2, $id);
                }break;
                case 14: {
                    $listaAhorros = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosByTipoAprobadoPorId3(3, $id);
                }break;
                case 15: {
                    $listaAhorros = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosByTipoAprobadoPorId2(1, $id);
                }break;
                case 16: {
                    $listaAhorros = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosByTipoAprobadoPorId2(3, $id);
                }break;
                case 17: {
                    $listaAhorros = $em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosByTipoAprobadoPorId4(2,$id);
                }break;
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
        }

        return $this->render(
            'ModulosPersonasBundle:Ahorro:cargarAho.html.twig',
            array(
                'flag' => $flag,
                'lista' => $lista,
            )
        );
    }
}