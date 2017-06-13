<?php

namespace Modulos\PersonasBundle\Controller;

use Modulos\PersonasBundle\Form\PersonaDocumentoType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


use Modulos\PersonasBundle\Entity\Persona;
use Modulos\PersonasBundle\Entity\PersonaDocumento;
use Modulos\PersonasBundle\Form\PersonaType;

/**
 * Persona controller.
 *
 */
class PersonaController extends Controller
{

    /**
     * Lists all Persona entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:Persona')->findAll();

        return $this->render('ModulosPersonasBundle:Persona:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Persona entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Persona();
        $form = $this->createForm(new PersonaType(),$entity);
        $form->handleRequest($request);

        //$form->get('region')->setData($entity->getRegion(1));
        $em = $this->getDoctrine()->getManager();



        /*for($i=0;$i<count($cedulas);$i++)
        {
            if($entity->getCi()== $cedulas[$i]->getCi()){
                $this->get('session')->getFlashBag()->add(
                    'danger',
                    //'No se puede realizar la transacción, la caja no dispone de dinero suficiente.'
                    'No se puede ingresar la Persona ya que la cédula a sido registrada con anterioridad.'
                );

                return $this->redirect($this->generateUrl('persona_create'));
            }
        }*/



        if ($form->isValid()) {

            $cedulas = $em->getRepository('ModulosPersonasBundle:Persona')->findAll();

            foreach($cedulas as $cedula)
            {
                if($entity->getTipo_persona()== $cedula->getTipo_persona()) {
                    if ($entity->getCi() == $cedula->getCi()) {
                        $this->get('session')->getFlashBag()->add(
                            'error',
                            'No se puede ingresar la Persona ya que la cédula a sido registrada con anterioridad.'
                        );

                        return $this->redirect($this->generateUrl('persona_create'));
                    }
                }
            }

            //echo "<pre>";
            //echo $entity->getRegion()->getNombre();//='SIERRA';
            //echo "<pre>";
            //echo $entity->getProvincia()->getNombre();
            //echo "<pre>";
            //echo $entity->getCiudad()->getNombre();
            //echo "<pre>";
            //echo $entity->getParroquia()->getNombre();
            //die();


            $entity->subirFoto($this->container->getParameter('modulos.directorio.fotos'));
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado la persona'
            ); 
            return $this->redirect($this->generateUrl('persona', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:Persona:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Persona entity.
     *
     * @param Persona $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
   /* private function createCreateForm(Persona $entity)
    {
        $form = $this->createForm(new PersonaType(), $entity, array(
            'action' => $this->generateUrl('persona_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new Persona entity.
     *
     */
   /* public function newAction()
    {
        $entity = new Persona();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:Persona:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a Persona entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Persona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Persona entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Persona:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Persona entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Persona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Persona entity.');
        }
        $documentos = $em->getRepository('ModulosPersonasBundle:PersonaDocumento')->findPersonaDocumentoPorPersona($id);

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Persona:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'documentos'  =>$documentos
        ));
    }

    /**
    * Creates a form to edit a Persona entity.
    *
    * @param Persona $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Persona $entity)
    {
        $form = $this->createForm(new PersonaType(), $entity, array(
            'action' => $this->generateUrl('persona_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Persona entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Persona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Persona entity.');
        }

        $editForm = $this->createForm(new PersonaType(),$entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->subirFoto($this->container->getParameter('modulos.directorio.fotos'));
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado la persona'
            );
            return $this->redirect($this->generateUrl('persona', array('id' => $id)));
        }
        $documentos = $em->getRepository('ModulosPersonasBundle:PersonaDocumento')->findPersonaDocumentoPorPersona($id);
        return $this->render('ModulosPersonasBundle:Persona:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'documentos'  =>$documentos,

        ));
    }
    /**
     * Deletes a Persona entity.
     *
     */
   public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:Persona')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado la persona'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('persona'));
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
        return $this->redirect($this->generateUrl('persona'));
    }
   
    /**
     * Creates a form to delete a Persona entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('persona_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    public function asociarDocumentoAction($id,Request $request){

    $em = $this->getDoctrine()->getManager();

    $documento = new  PersonaDocumento();
    $documentos = $em->getRepository('ModulosPersonasBundle:PersonaDocumento')->findPersonaDocumentoPorPersona($id);
    $form = $this->createForm(new PersonaDocumentoType(),$documento);
    $form->handleRequest($request);
    $persona = $em->getRepository('ModulosPersonasBundle:Persona')->find($id);
    if ($form->isValid()) {

        $documento->subirDocumento($this->container->getParameter('modulos.directorio.documentos'));
        $documento->setPersona($persona);
        $em->persist($documento);
        $em->flush();
        return $this->redirect($this->generateUrl('asociarDocumento', array('id' => $id)));
    }

    return $this->render('ModulosPersonasBundle:Persona:AsociarDocumento.html.twig', array(
        'form'   => $form->createView(),
        'id'    =>$id,
        'documentos'=>$documentos,
        'persona'=>$persona


    ));
    }
    public  function editarasociarDocumentoAction($id,$idPersona,Request $request){


        $em = $this->getDoctrine()->getManager();
        $documento = $em->getRepository('ModulosPersonasBundle:PersonaDocumento')->findOneById($id);
        $form = $this->createForm(new PersonaDocumentoType(),$documento);
        $persona = $em->getRepository('ModulosPersonasBundle:Persona')->findOneById($idPersona);
        $rutaDocumentoOriginal = $form->getData()->getRutaDocumento();
        $form->handleRequest($request);
        if ($form->isValid()) {

            if (null == $documento->getDocumento())
            {
                $documento->setRutaDocumento($rutaDocumentoOriginal);
            }
            else
            {
                if($rutaDocumentoOriginal != null)
                {
                   $documento->subirDocumento($this->container->getParameter('modulos.directorio.documentos'));
                    // Borrar el documento anterior
                    unlink($this->container->getParameter('modulos.directorio.documentos').$rutaDocumentoOriginal);
                }
                else
                {
                    $documento->subirDocumento($this->container->getParameter('modulos.directorio.documentos'));
                }

            }
            $em->persist($documento);
            $em->flush();
            return $this->redirect($this->generateUrl('asociarDocumento', array('id' => $idPersona)));
        }
        return $this->render('ModulosPersonasBundle:Persona:AsociarDocumentoEditar.html.twig', array(
            'documento'   => $documento,
            'idPersona'  =>$idPersona,
            'form'=>$form->createView(),
            'persona'=>$persona
        ));


    }
    public function eliminarasociarDocumentoAction($id,$idPersona){
        $em = $this->getDoctrine()->getManager();
        $documento = $em->getRepository('ModulosPersonasBundle:PersonaDocumento')->findOneById($id);
        if($documento->getRutaDocumento()!=""){
            unlink($this->container->getParameter('modulos.directorio.documentos').$documento->getRutaDocumento());
        }

        $em->remove($documento);
        $em->flush();

        return $this->redirect($this->generateUrl('asociarDocumento', array('id' => $idPersona)));
    }


    public function cargarubicacionAction($idPadre, $tipo)
    {

        $em = $this->getDoctrine()->getManager();

         switch ($tipo) {
            case 1: {
                $lista= $em->getRepository('ModulosPersonasBundle:Provincia')->findByRegionId($idPadre);
            }break;
            case 2: {
                $lista= $em->getRepository('ModulosPersonasBundle:Ciudad')->findByProvinciaId($idPadre);
            }break;
            case 3: {
                $lista= $em->getRepository('ModulosPersonasBundle:Parroquia')->findByCiudadId($idPadre);
            }break;
         }
        return $this->render(
            'ModulosPersonasBundle:Persona:cargarubicacion.html.twig',
            array(
                'flag' => $tipo,
                'lista' => $lista,
            )
        );
    }

    public function aportesAction($personalist)
    {
        $arrayMeses=array();
        $arrayMesesTexto=array();
        $salidaAportes=array();

        $em = $this->getDoctrine()->getManager();

        $cajahorro=$em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja=$cajahorro->getRazonSocial();//'nombrecaja'=>$nombrecaja,

        $personasID = explode(",", $personalist);

        $personas=array();
        if($personalist=="all"){
            $personas=$em->getRepository('ModulosPersonasBundle:Persona')->findOrdenados();
        }else{
            for($i=0; $i<count($personasID); $i++){
                $personas[]=$em->getRepository('ModulosPersonasBundle:Persona')->findOneById($personasID[$i]);
            }
        }

        $cantPersonas=count( $personas );
        if($cantPersonas==0){
            return $this->render(
                'ModulosPersonasBundle:Persona:aportes.html.twig',
                array(
                    'meses'=>$arrayMeses,
                    'mesesTexto'=>$arrayMesesTexto,
                    'aportes'=>$salidaAportes,
                    'personalist'=>$personalist,
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
                'ModulosPersonasBundle:Persona:aportes.html.twig',
                array(
                    'meses'=>$arrayMeses,
                    'mesesTexto'=>$arrayMesesTexto,
                    'aportes'=>$salidaAportes,
                    'personalist'=>$personalist,
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
            $fecha1Iterante->add($intervalo);
        }

        $resumenAportes=array();



        for($i=0; $i<$cantPersonas; $i++){



            $personaAportes=array(  "persona"=>$personas[$i],
                                    "aportes"=>$valores,
                                    "saldoAnterior"=>$valores,
                                    "retiroaportes"=>$valores,
                                    "saldoFinal"=>$valores,);

            $aportes = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersona($personas[$i]);
            $retiro = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersona($personas[$i]);

            for($j=0; $j<count($aportes); $j++) {
                $personaAportes["aportes"][$aportes[$j]->getFecha()->format('Y-m')]+=$aportes[$j]->getDebe();
            }

            for($j=0; $j<count($retiro); $j++) {
                $personaAportes["retiroaportes"][$retiro[$j]->getFecha()->format('Y-m')]+=$retiro[$j]->getHaber();
            }

            $resumenAportes[]=$personaAportes;
        }
        for($i=0; $i<count($resumenAportes); $i++){
            for($j=0; $j<count($arrayMeses); $j++) {
                $resumenAportes[$i]["saldoAnterior"][$arrayMeses[$j]]= (($j>0) ? $resumenAportes[$i]["saldoFinal"][$arrayMeses[$j-1]]: 0);
                $resumenAportes[$i]["saldoFinal"][$arrayMeses[$j]]=$resumenAportes[$i]["aportes"][$arrayMeses[$j]] + $resumenAportes[$i]["saldoAnterior"][$arrayMeses[$j]] - $resumenAportes[$i]["retiroaportes"][$arrayMeses[$j]];
            }
        }
        /*for($i=0; $i<count($resumenAportes); $i++){
            for($j=0; $j<count($arrayMeses); $j++) {
                $resumenAportes[$i]["saldoAnterior"][$arrayMeses[$j]]= (($j>0) ? $resumenAportes[$i]["saldoFinal"][$arrayMeses[$j-1]] : 0);
                $resumenAportes[$i]["saldoFinal"][$arrayMeses[$j]]= $resumenAportes[$i]["aportes"][$arrayMeses[$j]] + $resumenAportes[$i]["saldoAnterior"][$arrayMeses[$j]];

            }
        }*/

        for($j=0; $j<count($arrayMeses); $j++) {
            $salidaAportes[$arrayMeses[$j]]=array();
            for($i=0; $i<count($resumenAportes); $i++){
                $personaMes=array();
                $personaMes["persona"]=$resumenAportes[$i]["persona"];
                $personaMes["saldoAnterior"]=$resumenAportes[$i]["saldoAnterior"][$arrayMeses[$j]];
                $personaMes["aportes"]=$resumenAportes[$i]["aportes"][$arrayMeses[$j]];
                $personaMes["retiroaportes"]=$resumenAportes[$i]["retiroaportes"][$arrayMeses[$j]];
                $personaMes["saldoFinal"]=$resumenAportes[$i]["saldoFinal"][$arrayMeses[$j]];
                $salidaAportes[$arrayMeses[$j]][]=$personaMes;
            }

        }

        return $this->render(
            'ModulosPersonasBundle:Persona:aportes.html.twig',
            array(
                'meses'=>$arrayMeses,
                'mesesTexto'=>$arrayMesesTexto,
                'aportes'=>$salidaAportes,
                'personalist'=>$personalist,
                'nombrecaja'=>$nombrecaja,
            )
        );

    }

    public function aportesExcelAction($personalist)
    {
        $arrayMeses=array();
        $arrayMesesTexto=array();
        $salidaAportes=array();

        $em = $this->getDoctrine()->getManager();

        $cajahorro=$em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja=$cajahorro->getRazonSocial();//'nombrecaja'=>$nombrecaja,

        $personasID = explode(",", $personalist);

        $personas=array();
        if($personalist=="all"){
            $personas=$em->getRepository('ModulosPersonasBundle:Persona')->findOrdenados();
        }else{
            for($i=0; $i<count($personasID); $i++){
                $personas[]=$em->getRepository('ModulosPersonasBundle:Persona')->findOneById($personasID[$i]);
            }
        }

        $cantPersonas=count( $personas );
        if($cantPersonas==0){
            return $this->render(
                'ModulosPersonasBundle:Persona:aportes.html.twig',
                array(
                    'meses'=>$arrayMeses,
                    'mesesTexto'=>$arrayMesesTexto,
                    'aportes'=>$salidaAportes,
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
                'ModulosPersonasBundle:Persona:aportes.html.twig',
                array(
                    'meses'=>$arrayMeses,
                    'mesesTexto'=>$arrayMesesTexto,
                    'aportes'=>$salidaAportes,
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
            $fecha1Iterante->add($intervalo);
        }

        $resumenAportes=array();

        for($i=0; $i<$cantPersonas; $i++){

            $personaAportes=array(  "persona"=>$personas[$i],
                "aportes"=>$valores,
                "saldoAnterior"=>$valores,
                "retiroaportes"=>$valores,
                "saldoFinal"=>$valores,);

            $aportes = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersona($personas[$i]);
            $retiro = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersona($personas[$i]);

            for($j=0; $j<count($aportes); $j++) {
                $personaAportes["aportes"][$aportes[$j]->getFecha()->format('Y-m')]+=$aportes[$j]->getDebe();
            }

            for($j=0; $j<count($retiro); $j++) {
                $personaAportes["retiroaportes"][$retiro[$j]->getFecha()->format('Y-m')]+=$retiro[$j]->getHaber();
            }

            $resumenAportes[]=$personaAportes;
        }
        for($i=0; $i<count($resumenAportes); $i++){
            for($j=0; $j<count($arrayMeses); $j++) {
                $resumenAportes[$i]["saldoAnterior"][$arrayMeses[$j]]= (($j>0) ? $resumenAportes[$i]["saldoFinal"][$arrayMeses[$j-1]]: 0);
                $resumenAportes[$i]["saldoFinal"][$arrayMeses[$j]]=$resumenAportes[$i]["aportes"][$arrayMeses[$j]] + $resumenAportes[$i]["saldoAnterior"][$arrayMeses[$j]] - $resumenAportes[$i]["retiroaportes"][$arrayMeses[$j]];
            }
        }

        for($j=0; $j<count($arrayMeses); $j++) {
            $salidaAportes[$arrayMeses[$j]]=array();
            for($i=0; $i<count($resumenAportes); $i++){
                $personaMes=array();
                $personaMes["persona"]=$resumenAportes[$i]["persona"];
                $personaMes["saldoAnterior"]=$resumenAportes[$i]["saldoAnterior"][$arrayMeses[$j]];
                $personaMes["aportes"]=$resumenAportes[$i]["aportes"][$arrayMeses[$j]];
                $personaMes["retiroaportes"]=$resumenAportes[$i]["retiroaportes"][$arrayMeses[$j]];
                $personaMes["saldoFinal"]=$resumenAportes[$i]["saldoFinal"][$arrayMeses[$j]];
                $salidaAportes[$arrayMeses[$j]][]=$personaMes;
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
        //$phpExcelObject->setActiveSheetIndex(0)
        //   ->setCellValue('A1', $tituloReporte);
        //$phpExcelObject->getActiveSheet()->getStyle('A1:G1')->applyFromArray($estiloTituloCartera);

        $titulosColumnas = array(
            'Nº',
            'Nombre y Apellidos',
            'Usuario Nº',
            'Saldo Anterior',
            'Aportes Mensual',
            'Retiro de Aportes',
            'Saldo final de aportes',
        );

        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A1:R1');

        $i = 2;
        $mesIndex=0;
        foreach($arrayMeses as $mes){
            // Se agregan los titulos del reporte
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, "LIBRO GENERAL DE APORTES DE SOCIOS");
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloSubCabCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':G'.$i);
            $i++;
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $nombrecaja);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloSubCabCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':G'.$i);
            $i++;
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $arrayMesesTexto[$mes]);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloTituloCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':G'.$i);
            $i++;


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $titulosColumnas[0]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('A'.$i.':A'.($i+1));
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':A'.($i+1))->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':A'.($i+1))->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('B'.$i, $titulosColumnas[1]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('B'.$i.':B'.($i+1));
            $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':B'.($i+1))->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':B'.($i+1))->applyFromArray($estiloCeldaCarteraBorder);


            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, $titulosColumnas[2]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('C'.$i.':C'.($i+1));
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i.':C'.($i+1))->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i.':C'.($i+1))->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D'.$i, $titulosColumnas[3]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('D'.$i.':D'.($i+1));
            $phpExcelObject->getActiveSheet()->getStyle('D'.$i.':D'.($i+1))->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->getActiveSheet()->getStyle('D'.$i.':D'.($i+1))->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E'.$i, ($mesIndex++==0?"Aportes Iniciales":$titulosColumnas[4]));
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('E'.$i.':E'.($i+1));
            $phpExcelObject->getActiveSheet()->getStyle('E'.$i.':E'.($i+1))->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->getActiveSheet()->getStyle('E'.$i.':E'.($i+1))->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, $titulosColumnas[5]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->mergeCells('F'.$i.':F'.($i+1));
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i.':F'.($i+1))->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i.':F'.($i+1))->applyFromArray($estiloCeldaCarteraBorder);

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, $titulosColumnas[6]);
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.($i+1), 'Saldo final de aportes');
            $phpExcelObject->getActiveSheet()->getStyle('G'.($i+1))->applyFromArray($estiloTituloColumnasCartera);
            $phpExcelObject->getActiveSheet()->getStyle('G'.($i).':G'.($i+1))->applyFromArray($estiloCeldaCarteraBorder);
            $i++;
            $i++;
            $iIndice=$i;
            $cont = 1;
          //  {% for personaAportes in aportes[mes] %}
            foreach($salidaAportes[$mes] as $aporte){
                $personaNombre = $aporte["persona"]->__toString();
                $personaSaldoAnterior = $aporte["saldoAnterior"];
                $personaAportes = $aporte["aportes"];
                $personaRetiroAportes = $aporte["retiroaportes"];
                $personaSaldoFinal = $aporte["saldoFinal"];

                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, $cont);
                $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);

                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, $personaNombre);
                $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C'.$i, $cont++);
                $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('D'.$i, number_format($personaSaldoAnterior, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('E'.$i, number_format($personaAportes, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('F'.$i, number_format($personaRetiroAportes, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaDerecha);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('G'.$i, number_format($personaSaldoFinal, 2, '.', ''));
                $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);
                $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaDerecha);
                $i++;
            }
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, "Total");
            $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('C'.$i, '=MAX(C'.$iIndice.':C'.($i-1).')');
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('D'.$i, '=SUM(D'.$iIndice.':D'.($i-1).')');
            $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('E'.$i, '=SUM(E'.$iIndice.':E'.($i-1).')');
            $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.$i, '=SUM(F'.$iIndice.':F'.($i-1).')');
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaDerecha);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, '=SUM(G'.$iIndice.':G'.($i-1).')');
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);
            $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaDerecha);
            $phpExcelObject->getActiveSheet()->getStyle('A'.($i).':G'.($i))->applyFromArray($estiloCeldaCarteraBorder);
            $phpExcelObject->getActiveSheet()->getStyle('A'.($iIndice).':G'.($i))->applyFromArray($estiloCeldaCarteraBorder);

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
            ->setWidth(8);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('G')
            ->setWidth(10);

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
            'Aportes Socios.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    public function resumenAportesAction($mes, $ano)
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

        $resumenAportes=array();


        $personas=$em->getRepository('ModulosPersonasBundle:Persona')->findOrdenados();

        $cantPersonas=count( $personas );



        for($i=0; $i<$cantPersonas; $i++){
            $totalAporte=0;
            $totalRetiro=0;
            $aporteIni= $em->getRepository('ModulosPersonasBundle:Libro')->findAporteInicialXpersona($personas[$i]);
            $personaAportes=array(  "persona"=>$personas[$i],
                                    "meses"=>array($arrayMeses[0]=>0,$arrayMeses[1]=>0,$arrayMeses[2]=>0,$arrayMeses[3]=>0,$arrayMeses[4]=>0,$arrayMeses[5]=>0,$arrayMeses[6]=>0,$arrayMeses[7]=>0,$arrayMeses[8]=>0,$arrayMeses[9]=>0,$arrayMeses[10]=>0,$arrayMeses[11]=>0,),
                                    "retiro"=>array($arrayMeses[0]=>0,$arrayMeses[1]=>0,$arrayMeses[2]=>0,$arrayMeses[3]=>0,$arrayMeses[4]=>0,$arrayMeses[5]=>0,$arrayMeses[6]=>0,$arrayMeses[7]=>0,$arrayMeses[8]=>0,$arrayMeses[9]=>0,$arrayMeses[10]=>0,$arrayMeses[11]=>0,),
                                    "total"=>0);

            $aportes = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersonaEntreFechas($personas[$i], $fecha1, $fecha2);
            $retiro = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersonaEntreFechas($personas[$i], $fecha1, $fecha2);

            for($j=0; $j<count($aportes); $j++) {
                $totalAporte += $aportes[$j]->getDebe();
                $personaAportes["meses"][$aportes[$j]->getFecha()->format('Y-m')]+= $aportes[$j]->getDebe();
            }

            for($j=0; $j<count($retiro); $j++) {
                $totalRetiro += $retiro[$j]->getHaber();
                $personaAportes["retiro"][$retiro[$j]->getFecha()->format('Y-m')]+=$retiro[$j]->getHaber();
            }

            $personaAportes["total"]=$totalAporte-$totalRetiro;
            $resumenAportes[]=$personaAportes;
        }

        $totalesMeses=array($arrayMeses[0]=>0,$arrayMeses[1]=>0,$arrayMeses[2]=>0,$arrayMeses[3]=>0,$arrayMeses[4]=>0,$arrayMeses[5]=>0,$arrayMeses[6]=>0,$arrayMeses[7]=>0,$arrayMeses[8]=>0,$arrayMeses[9]=>0,$arrayMeses[10]=>0,$arrayMeses[11]=>0,);
        for($i=0; $i<count($resumenAportes); $i++){
            for($j=0; $j<count($arrayMeses); $j++) {
               $totalesMeses[$arrayMeses[$j]]+=$resumenAportes[$i]["meses"][$arrayMeses[$j]] - $resumenAportes[$i]["retiro"][$arrayMeses[$j]];
            }
        }

        return $this->render(
            'ModulosPersonasBundle:Persona:resumenAportes.html.twig',
            array(
                'fechainicial' => $fecha1,
                'fechafinal' => $fecha2,
                'meses'=>$arrayMeses,
                'mesesTotales'=>$totalesMeses,
                'mesesTexto'=>$mesesTexto,
                'fechaLabel'=>$fechaIntervaloLabel,
                'aportes'=>$resumenAportes,
            )
        );

    }

    public function moraAction($id){
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
        $em = $this->getDoctrine()->getManager();
        $persona =$em->getRepository('ModulosPersonasBundle:Persona')->findOneById($id);
        $creditos=$em->getRepository(
            'ModulosPersonasBundle:Creditos'
        )->findByPersona($persona);
        $creditosAmort=array();

        $fechaActual=new \DateTime();
        $aux = "23:59:59";
        $fecha2 = $fechaActual->format('Y-m-d');
        $fechaaux = $fecha2." ".$aux;
        $fechaActual = new \DateTime($fechaaux);
        foreach ( $creditos as $credito) {
            $creditoEspecif=array();
            $creditoEspecif["credito"]=$credito;
            $creditoEspecif["amortiz"]=array();

            $estadopago="pago";
            $estadocredito=false;
            if($credito->getEstadocreditos()->getTipo()=='APROBADO'){
                $amortizaciones = $em->getRepository('ModulosPersonasBundle:TablaAmortizacion')->findTablasAmortizacionPorCreditos($credito->getId());
                for ($i = 0; $i < count($amortizaciones); $i++) {
                    /*$amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
                    $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                    $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                    $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));*/
                    $creditoEspecif["amortiz"][$i][0]=$amortizaciones[$i];
                    $creditoEspecif["amortiz"][$i][1]=3;
                }

                $pagosrealizados = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->findByCreditoId($credito);
//
                for ($i = 0; $i < count($pagosrealizados); $i++) {
                    if($amortizaciones[$i+1]->getFechaDePago()>=$pagosrealizados[$i]->getFechaDePago()){
                        $creditoEspecif["amortiz"][$i][1]=0;
                    }else{
                        $creditoEspecif["amortiz"][$i][1]=1;
                    }
                }

                for (; $i < count($amortizaciones); $i++) {
                    if($amortizaciones[$i]->getFechaDePago() < $fechaActual){
                        if($i!=0){
                            $creditoEspecif["amortiz"][$i][1]=2;
                            $estadocredito=true;
                        }
                    }else{
                        $creditoEspecif["amortiz"][$i][1]=3;
                    }
                }
//            echo "<pre>";
//            echo $i;
//            die();
//
                if (count($amortizaciones) > 1 && (count($amortizaciones)-1 == count($pagosrealizados))) {
                    $estadopago="completado";
                }

                //0-pagado en tiempo
                //1-pagado pero con retrazo
                //2-moroso
                //3-sin pagar pero en tiempo
                $creditoEspecif["estado"]=$estadopago;
                $creditoEspecif["moroso"]=$estadocredito;
                $creditosAmort[]=$creditoEspecif;
            }
        }

//         foreach($creditosAmort as $ca){
//             if($ca["moroso"]){
//                 echo "<pre>";
//                 echo $ca["credito"]->getId();
//                 echo "<pre>";
//                 foreach($ca["amortiz"] as $amort){
//                     echo "<pre>";
//                     echo $amort[0]->getCreditoId()->getId();
//                     echo "&nbsp;";
//                     echo $amort[0]->getValorcuota();
//                     echo "&nbsp;";
//                     if($amort[1]==0){
//                         echo "Pagado";
//                     }else
//                         if($amort[1]==1){
//                             echo "Retrazado;";
//                         }else{
//                             if($amort[1]==2){
//                                 echo "Moroso";
//                             }else{
//                                 echo "Sin Pagar pero en Tiempo";
//                             }
//                         }
//                 }
//                 echo "<pre>";
//                 echo "Estado Credito: ".$ca["estado"];
//                 echo "<pre>";
//             }
//        }
//        die();

        return $this->render( 'ModulosPersonasBundle:Persona:mora.html.twig', array(
            'creditosMorosos'=>$creditosAmort,
            'persona'=>$persona,
            'fecha'=>$fechaActual->format('d')." de ".$mesesMap[$fechaActual->format('m')]." del ".$fechaActual->format('Y'),
        ));

    }

    public function moraExcelAction($id){
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
        $em = $this->getDoctrine()->getManager();

        $cajahorro=$em->getRepository('ModulosPersonasBundle:Entidad')->find(1);

        $nombrecaja=$cajahorro->getRazonSocial();//'nombrecaja'=>$nombrecaja,

        $persona =$em->getRepository('ModulosPersonasBundle:Persona')->findOneById($id);
        $creditos=$em->getRepository(
            'ModulosPersonasBundle:Creditos'
        )->findByPersona($persona);
        $creditosAmort=array();

        $fechaActual=new \DateTime();
        $aux = "23:59:59";
        $fecha2 = $fechaActual->format('Y-m-d');
        $fechaaux = $fecha2." ".$aux;
        $fechaActual = new \DateTime($fechaaux);
        foreach ( $creditos as $credito) {
            $creditoEspecif=array();
            $creditoEspecif["credito"]=$credito;
            $creditoEspecif["amortiz"]=array();

            $estadopago="pago";
            $estadocredito=false;
            if($credito->getEstadocreditos()->getTipo()=='APROBADO'){
                $amortizaciones = $em->getRepository('ModulosPersonasBundle:TablaAmortizacion')->findTablasAmortizacionPorCreditos($credito->getId());
                for ($i = 0; $i < count($amortizaciones); $i++) {
                    /*$amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
                    $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                    $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                    $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));*/
                    $creditoEspecif["amortiz"][$i][0]=$amortizaciones[$i];
                    $creditoEspecif["amortiz"][$i][1]=3;
                }

                $pagosrealizados = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->findByCreditoId($credito);
//
                for ($i = 0; $i < count($pagosrealizados); $i++) {
                    if($amortizaciones[$i+1]->getFechaDePago()>=$pagosrealizados[$i]->getFechaDePago()){
                        $creditoEspecif["amortiz"][$i][1]=0;
                    }else{
                        $creditoEspecif["amortiz"][$i][1]=1;
                    }
                }

                for (; $i < count($amortizaciones); $i++) {
                    if($amortizaciones[$i]->getFechaDePago() < $fechaActual){
                        if($i!=0){
                            $creditoEspecif["amortiz"][$i][1]=2;
                            $estadocredito=true;
                        }
                    }else{
                        $creditoEspecif["amortiz"][$i][1]=3;
                    }
                }
                if (count($amortizaciones) > 1 && (count($amortizaciones)-1 == count($pagosrealizados))) {
                    $estadopago="completado";
                }

                //0-pagado en tiempo
                //1-pagado pero con retrazo
                //2-moroso
                //3-sin pagar pero en tiempo
                $creditoEspecif["estado"]=$estadopago;
                $creditoEspecif["moroso"]=$estadocredito;
                $creditosAmort[]=$creditoEspecif;
            }
        }


        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Conquito")
            ->setLastModifiedBy("Conquito")
            ->setTitle("Reporte de Morocidad sobre Créditos")
            ->setSubject("Reporte de Morocidad sobre Créditos")
            ->setDescription("Reporte de Morocidad sobre Créditos")
            ->setKeywords("Reporte de Morocidad sobre Créditos")
            ->setCategory("Reporte excel");

        $tituloHoja = "Morocidad sobre Créditos";

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
        $titulosColumnas = array(
            'Cuota',
            'Fecha de Pago',
            'Capital',
            'Interés',
            'Valor Cuota',
            'Saldo',
            'Pagado',
        );
        $estiloCeldaCarteraBorder = array(
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
        $estiloCeldaMorosa=array(
                'fill' => array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'cc9382')
                )
        );

        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A1:R1');

        $i = 2;
        // Se agregan los titulos del reporte

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, "REPORTE DE MOROCIDAD SOBRE CRÉDITOS");
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloTituloCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A'.$i.':G'.$i);
        $i++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $nombrecaja);
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloTituloCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A'.$i.':G'.$i);
        $i++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $fechaActual->format('d')." de ".$mesesMap[$fechaActual->format('m')]." del ".$fechaActual->format('Y'));
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloTituloCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A'.$i.':G'.$i);
        $i++;
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $persona->__toString());
        $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloTituloCartera);
        $phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A'.$i.':G'.$i);
        $i++;
        $i++;
        $i++;


        $mesIndex=0;
        foreach($creditosAmort as $creditoMoroso){
            if ($creditoMoroso['moroso'] ){
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, "ID Crédito: ". $creditoMoroso['credito']->getId());
                $phpExcelObject->setActiveSheetIndex(0)
                    ->mergeCells('A'.$i.':C'.($i+1));
                $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':C'.($i+1))->applyFromArray($estiloSubCabCartera);

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('D'.$i, "Valor Autorizado: ".$creditoMoroso['credito']->getMontoSolicitado());
                $phpExcelObject->setActiveSheetIndex(0)
                    ->mergeCells('D'.$i.':G'.($i+1));
                $phpExcelObject->getActiveSheet()->getStyle('D'.$i.':G'.($i+1))->applyFromArray($estiloSubCabCartera);

                $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.($i+1))->applyFromArray($estiloCeldaCarteraBorder);
//                $phpExcelObject->setActiveSheetIndex(0)
//                    ->mergeCells('A'.$i.':G'.($i+1));
                $i++;
                $i++;

                $iIndice=$i;
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $titulosColumnas[0]);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->mergeCells('A'.$i.':A'.$i);
                $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':A'.$i)->applyFromArray($estiloTituloColumnasCartera);
                $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':A'.$i)->applyFromArray($estiloCeldaCarteraBorder);

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('B'.$i, $titulosColumnas[1]);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->mergeCells('B'.$i.':B'.$i);
                $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':B'.$i)->applyFromArray($estiloTituloColumnasCartera);
                $phpExcelObject->getActiveSheet()->getStyle('B'.$i.':B'.$i)->applyFromArray($estiloCeldaCarteraBorder);


                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C'.$i, $titulosColumnas[2]);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->mergeCells('C'.$i.':C'.$i);
                $phpExcelObject->getActiveSheet()->getStyle('C'.$i.':C'.$i)->applyFromArray($estiloTituloColumnasCartera);
                $phpExcelObject->getActiveSheet()->getStyle('C'.$i.':C'.$i)->applyFromArray($estiloCeldaCarteraBorder);

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('D'.$i, $titulosColumnas[3]);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->mergeCells('D'.$i.':D'.$i);
                $phpExcelObject->getActiveSheet()->getStyle('D'.$i.':D'.$i)->applyFromArray($estiloTituloColumnasCartera);
                $phpExcelObject->getActiveSheet()->getStyle('D'.$i.':D'.$i)->applyFromArray($estiloCeldaCarteraBorder);

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('E'.$i, ($mesIndex++==0?"Aportes Iniciales":$titulosColumnas[4]));
                $phpExcelObject->setActiveSheetIndex(0)
                    ->mergeCells('E'.$i.':E'.$i);
                $phpExcelObject->getActiveSheet()->getStyle('E'.$i.':E'.$i)->applyFromArray($estiloTituloColumnasCartera);
                $phpExcelObject->getActiveSheet()->getStyle('E'.$i.':E'.$i)->applyFromArray($estiloCeldaCarteraBorder);

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('F'.$i, $titulosColumnas[5]);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->mergeCells('F'.$i.':F'.$i);
                $phpExcelObject->getActiveSheet()->getStyle('F'.$i.':F'.$i)->applyFromArray($estiloTituloColumnasCartera);
                $phpExcelObject->getActiveSheet()->getStyle('F'.$i.':F'.$i)->applyFromArray($estiloCeldaCarteraBorder);

                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('G'.$i, $titulosColumnas[6]);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->mergeCells('G'.$i.':G'.$i);
                $phpExcelObject->getActiveSheet()->getStyle('G'.$i.':G'.$i)->applyFromArray($estiloTituloColumnasCartera);
                $phpExcelObject->getActiveSheet()->getStyle('G'.$i.':G'.$i)->applyFromArray($estiloCeldaCarteraBorder);

                $i++;
                $cont = 1;
                $capitalMoroso = 0;
                $interesMoroso = 0;
                $cuotaMoroso = 0;
                foreach($creditoMoroso['amortiz']  as $amort){

                    $cuota = $amort[0]->getCuota();
                    $fechaPago = $amort[0]->getFechaDePago()->format("d/m/Y");
                    $capital = $amort[0]->getCapital();
                    $interes = $amort[0]->getInteres();
                    $valorCuota = $amort[0]->getValorCuota();
                    $saldo = $amort[0]->getSaldo();
                    $pagado = $amort[1] <= 1 ? "Si":"No";

                    $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A'.$i, $cuota);
                    $phpExcelObject->getActiveSheet()->getStyle('A'.$i)->applyFromArray($estiloCeldaCartera);

                    $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, $fechaPago);
                    $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaCartera);
                    $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);
                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('C'.$i, number_format($capital, 2, '.', ''));
                    $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaCartera);
                    $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('D'.$i, number_format($interes, 2, '.', ''));
                    $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaCartera);
                    $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('E'.$i, number_format($valorCuota, 2, '.', ''));
                    $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaCartera);
                    $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);
                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('F'.$i, number_format($saldo, 2, '.', ''));
                    $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaCartera);
                    $phpExcelObject->getActiveSheet()->getStyle('F'.$i)->applyFromArray($estiloCeldaDerecha);
                    $phpExcelObject->setActiveSheetIndex(0)
                        ->setCellValue('G'.$i, number_format($pagado, 2, '.', ''));
                    $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaCartera);
                    $phpExcelObject->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloCeldaDerecha);
                    if($amort[1] == 2){
                        $capitalMoroso += $amort[0]->getCapital();
                        $interesMoroso += $amort[0]->getInteres();
                        $cuotaMoroso += $amort[0]->getValorCuota();
                        $phpExcelObject->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($estiloCeldaMorosa);

                    }

                    $i++;
                }
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B'.$i, "Total Retrasos");
                $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloTituloColumnasCartera);
                $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($estiloCeldaIzq);
//                $phpExcelObject->getActiveSheet()->getStyle('B'.$i)->applyFromArray($negrita);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('C'.$i, $capitalMoroso);
                $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloTituloColumnasCartera);
                $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($estiloCeldaDerecha);
//                $phpExcelObject->getActiveSheet()->getStyle('C'.$i)->applyFromArray($negrita);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('D'.$i, $interesMoroso);
                $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloTituloColumnasCartera);
                $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloCeldaDerecha);
//                $phpExcelObject->getActiveSheet()->getStyle('D'.$i)->applyFromArray($negrita);
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('E'.$i, $cuotaMoroso);
                $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloTituloColumnasCartera);
                $phpExcelObject->getActiveSheet()->getStyle('E'.$i)->applyFromArray($estiloCeldaDerecha);

                $phpExcelObject->getActiveSheet()->getStyle('A'.($i).':G'.($i))->applyFromArray($estiloCeldaCarteraBorder);
                $phpExcelObject->getActiveSheet()->getStyle('A'.($iIndice).':G'.($i))->applyFromArray($estiloCeldaCarteraBorder);

                $i++;
                $i++;

            }

        }

        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('A')
            ->setWidth(10);
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(23);
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
        $phpExcelObject->getActiveSheet()
            ->getColumnDimension('G')
            ->setWidth(10);

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
            'Mora Créditos.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;

    }

    public function carteracreditoAction($parametro,$id){

        if($parametro == 1)
            $forma='::fronted6.html.twig';
        else
            $forma='::fronted3.html.twig';

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
        $em = $this->getDoctrine()->getManager();
        $persona =$em->getRepository('ModulosPersonasBundle:Persona')->findOneById($id);
        $creditos=$em->getRepository(
            'ModulosPersonasBundle:Creditos'
        )->findCreditosByPersonaOrdDescPorFecha($persona);
        //echo count($creditos);
        $creditosAmort=array();

        $fechaActual=new \DateTime();
        $aux = "23:59:59";
        $fecha2 = $fechaActual->format('Y-m-d');
        $fechaaux = $fecha2." ".$aux;
        $fechaActual = new \DateTime($fechaaux);
        foreach ( $creditos as $credito) {
            $creditoEspecif=array();
            $creditoEspecif["credito"]=$credito;
            $creditoEspecif["amortiz"]=array();

            $estadopago="pago";
            $estadocredito=false;
                $amortizaciones = $em->getRepository('ModulosPersonasBundle:TablaAmortizacion')->findTablasAmortizacionPorCreditos($credito->getId());
                for ($i = 0; $i < count($amortizaciones); $i++) {
                    /*$amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
                    $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                    $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                    $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));*/
                    $creditoEspecif["amortiz"][$i][0]=$amortizaciones[$i];
                    $creditoEspecif["amortiz"][$i][1]=3;
                }

                $pagosrealizados = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->findByCreditoId($credito);
//
                for ($i = 0; $i < count($pagosrealizados); $i++) {
                    if($amortizaciones[$i+1]->getFechaDePago()>=$pagosrealizados[$i]->getFechaDePago()){
                        $creditoEspecif["amortiz"][$i][1]=0;
                    }else{
                        $creditoEspecif["amortiz"][$i][1]=1;
                    }
                }
                for (; $i < count($amortizaciones); $i++) {
                    if($amortizaciones[$i]->getFechaDePago() < $fechaActual){
                        if($i!=0){
                            $creditoEspecif["amortiz"][$i][1]=2;
                            $estadocredito=true;
                        }
                    }else{
                        $creditoEspecif["amortiz"][$i][1]=3;
                    }
                }
                if (count($amortizaciones) > 1 && (count($amortizaciones)-1 == count($pagosrealizados))) {
                    $estadopago="completado";
                }
                if(count($amortizaciones) > 1){
                    $creditoEspecif["fechainicio"]=$amortizaciones[0]->getFechaDePago()->format("d/m/Y");
                    $creditoEspecif["fechafin"]=$amortizaciones[count($amortizaciones)-1]->getFechaDePago()->format("d/m/Y");
                }else{
                    $creditoEspecif["fechainicio"]="No Definida";
                    $creditoEspecif["fechafin"]="No Definida";
                }

                //0-pagado en tiempo
                //1-pagado pero con retrazo
                //2-moroso
                //3-sin pagar pero en tiempo
                $creditoEspecif["estado"]=$estadopago;
                $creditoEspecif["moroso"]=$estadocredito;
                $creditosAmort[]=$creditoEspecif;
        }

        return $this->render( 'ModulosPersonasBundle:Persona:carteracredito.html.twig', array(
            'creditosMorosos'=>$creditosAmort,
            'persona'=>$persona,
            'fecha'=>$fechaActual->format('d')." de ".$mesesMap[$fechaActual->format('m')]." del ".$fechaActual->format('Y'),
            'forma'=>$forma
        ));

    }

    public function carteracreditoExcelAction($id){
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
        $em = $this->getDoctrine()->getManager();
        $persona =$em->getRepository('ModulosPersonasBundle:Persona')->findOneById($id);
        $creditos=$em->getRepository(
            'ModulosPersonasBundle:Creditos'
        )->findCreditosByPersonaOrdDescPorFecha($persona);
        $creditosAmort=array();

        $fechaActual=new \DateTime();
        $aux = "23:59:59";
        $fecha2 = $fechaActual->format('Y-m-d');
        $fechaaux = $fecha2." ".$aux;
        $fechaActual = new \DateTime($fechaaux);
        foreach ( $creditos as $credito) {
            $creditoEspecif=array();
            $creditoEspecif["credito"]=$credito;
            $creditoEspecif["amortiz"]=array();

            $estadopago="pago";
            $estadocredito=false;
                $amortizaciones = $em->getRepository('ModulosPersonasBundle:TablaAmortizacion')->findTablasAmortizacionPorCreditos($credito->getId());
                for ($i = 0; $i < count($amortizaciones); $i++) {
                    /*$amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
                    $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                    $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                    $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));*/
                    $creditoEspecif["amortiz"][$i][0]=$amortizaciones[$i];
                    $creditoEspecif["amortiz"][$i][1]=3;
                }

                $pagosrealizados = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->findByCreditoId($credito);
//
                for ($i = 0; $i < count($pagosrealizados); $i++) {
                    if($amortizaciones[$i+1]->getFechaDePago()>=$pagosrealizados[$i]->getFechaDePago()){
                        $creditoEspecif["amortiz"][$i][1]=0;
                    }else{
                        $creditoEspecif["amortiz"][$i][1]=1;
                    }
                }
                for (; $i < count($amortizaciones); $i++) {
                    if($amortizaciones[$i]->getFechaDePago() < $fechaActual){
                        if($i!=0){
                            $creditoEspecif["amortiz"][$i][1]=2;
                            $estadocredito=true;
                        }
                    }else{
                        $creditoEspecif["amortiz"][$i][1]=3;
                    }
                }
                if (count($amortizaciones) > 1 && (count($amortizaciones)-1 == count($pagosrealizados))) {
                    $estadopago="completado";
                }
                if(count($amortizaciones) > 1){
                    $creditoEspecif["fechainicio"]=$amortizaciones[0]->getFechaDePago()->format("d/m/Y");
                    $creditoEspecif["fechafin"]=$amortizaciones[count($amortizaciones)-1]->getFechaDePago()->format("d/m/Y");
//                    echo "<pre>";
//                     echo $creditoEspecif["fechainicio"];
//                    echo "<pre>";
//                     echo $creditoEspecif["fechafin"];
//                    die();
                }else{
                    $creditoEspecif["fechainicio"]="No Definida";
                    $creditoEspecif["fechafin"]="No Definida";
                }

                //0-pagado en tiempo
                //1-pagado pero con retrazo
                //2-moroso
                //3-sin pagar pero en tiempo
                $creditoEspecif["estado"]=$estadopago;
                $creditoEspecif["moroso"]=$estadocredito;
                $creditosAmort[]=$creditoEspecif;
        }

//         foreach($creditosAmort as $ca){
//             if($ca["moroso"]){
//                 echo "<pre>";
//                 echo $ca["credito"]->getId();
//                 echo "<pre>";
//                 foreach($ca["amortiz"] as $amort){
//                     echo "<pre>";
//                     echo $amort[0]->getCreditoId()->getId();
//                     echo "&nbsp;";
//                     echo $amort[0]->getValorcuota();
//                     echo "&nbsp;";
//                     if($amort[1]==0){
//                         echo "Pagado";
//                     }else
//                         if($amort[1]==1){
//                             echo "Retrazado;";
//                         }else{
//                             if($amort[1]==2){
//                                 echo "Moroso";
//                             }else{
//                                 echo "Sin Pagar pero en Tiempo";
//                             }
//                         }
//                 }
//                 echo "<pre>";
//                 echo "Estado Credito: ".$ca["estado"];
//                 echo "<pre>";
//             }
//        }
//        die();

        return $this->render( 'ModulosPersonasBundle:Persona:carteracredito.html.twig', array(
            'creditosMorosos'=>$creditosAmort,
            'persona'=>$persona,
            'fecha'=>$fechaActual->format('d')." de ".$mesesMap[$fechaActual->format('m')]." del ".$fechaActual->format('Y'),
        ));

    }
//carteracreditounico
    public function carteracreditounicoAction($id){


        $em = $this->getDoctrine()->getManager();
        $credito =$em->getRepository('ModulosPersonasBundle:Creditos')->findOneById($id);
        $fechaActual=new \DateTime();
        $aux = "23:59:59";
        $fecha2 = $fechaActual->format('Y-m-d');
        $fechaaux = $fecha2." ".$aux;
        $fechaActual = new \DateTime($fechaaux);
            $creditoEspecif["credito"]=$credito;
            $creditoEspecif["amortiz"]=array();

            $estadopago="pago";
            $estadocredito=false;
                $amortizaciones = $em->getRepository('ModulosPersonasBundle:TablaAmortizacion')->findTablasAmortizacionPorCreditos($credito->getId());
                for ($i = 0; $i < count($amortizaciones); $i++) {
                    /*$amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
                    $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                    $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                    $amortizaciones[$i]->setDesgravamen(round($amortizaciones[$i]->getDesgravamen(), 2));
                    $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));*/
                    $creditoEspecif["amortiz"][$i][0]=$amortizaciones[$i];
                    $creditoEspecif["amortiz"][$i][1]=3;
                }

                $pagosrealizados = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->findByCreditoId($credito);
//
                for ($i = 0; $i < count($pagosrealizados); $i++) {
                    if($amortizaciones[$i+1]->getFechaDePago()>=$pagosrealizados[$i]->getFechaDePago()){
                        $creditoEspecif["amortiz"][$i][1]=0;
                    }else{
                        $creditoEspecif["amortiz"][$i][1]=1;
                    }
                }
                for (; $i < count($amortizaciones); $i++) {
                    if($amortizaciones[$i]->getFechaDePago() < $fechaActual){
                        if($i!=0){
                            $creditoEspecif["amortiz"][$i][1]=2;
                            $estadocredito=true;
                        }
                    }else{
                        $creditoEspecif["amortiz"][$i][1]=3;
                    }
                }
                if (count($amortizaciones) > 1 && (count($amortizaciones)-1 == count($pagosrealizados))) {
                    $estadopago="completado";
                }
                if(count($amortizaciones) > 1){
                    $creditoEspecif["fechainicio"]=$amortizaciones[0]->getFechaDePago()->format("d/m/Y");
                    $creditoEspecif["fechafin"]=$amortizaciones[count($amortizaciones)-1]->getFechaDePago()->format("d/m/Y");
                }else{
                    $creditoEspecif["fechainicio"]="No Definida";
                    $creditoEspecif["fechafin"]="No Definida";
                }

                //0-pagado en tiempo
                //1-pagado pero con retrazo
                //2-moroso
                //3-sin pagar pero en tiempo
                $creditoEspecif["estado"]=$estadopago;
                $creditoEspecif["moroso"]=$estadocredito;


        return $this->render( 'ModulosPersonasBundle:Persona:carteracreditounico.html.twig', array(
            'creditoEspecif'=>$creditoEspecif,
            'pagosrealizados'=>$pagosrealizados,
        ));

    }

    public function carteraahorroAction($parametro,$id){

        if($parametro == 1)
            $forma='::fronted6.html.twig';
        else
            $forma='::fronted4.html.twig';

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
        $em = $this->getDoctrine()->getManager();
        $persona =$em->getRepository('ModulosPersonasBundle:Persona')->findOneById($id);
        $ahorros=$em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosByPersonaOrdDescPorFecha($persona);
        $ahorrosAmort=array();

        $fechaActual=new \DateTime();
        $aux = "23:59:59";
        $fecha2 = $fechaActual->format('Y-m-d');
        $fechaaux = $fecha2." ".$aux;
        $fechaActual = new \DateTime($fechaaux);

        foreach ( $ahorros as $ahorro) {
            $ahorroEspecif=array();
            $ahorroEspecif["ahorro"]=$ahorro;
            $ahorroEspecif["amortiz"]=array();

            $pagosrealizados = $em->getRepository(
                'ModulosPersonasBundle:PagoCuotaAhorro'
            )->findPagosCuotasAhorros($ahorro->getId());

            $estadopago="pago";
            $estadocredito=false;
                $amortizaciones = $em->getRepository('ModulosPersonasBundle:TablaAmortizacionAhorro')->findTablasAmortizacionPorAhorros($ahorro->getId());
                for ($i = 0; $i < count($amortizaciones); $i++) {
                    /*$amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
                    $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                    $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                    $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));*/
                    $ahorroEspecif["amortiz"][$i][0]=$amortizaciones[$i];
                    $ahorroEspecif["amortiz"][$i][1]=3;
                }

//                $pagosrealizados = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->findByCreditoId($ahorro);
////
//                for ($i = 0; $i < count($pagosrealizados); $i++) {
//                    if($amortizaciones[$i+1]->getFechaDePago()>=$pagosrealizados[$i]->getFechaDePago()){
//                        $creditoEspecif["amortiz"][$i][1]=0;
//                    }else{
//                        $creditoEspecif["amortiz"][$i][1]=1;
//                    }
//                }
//                for (; $i < count($amortizaciones); $i++) {
//                    if($amortizaciones[$i]->getFechaDePago() < $fechaActual){
//                        if($i!=0){
//                            $creditoEspecif["amortiz"][$i][1]=2;
//                            $estadocredito=true;
//                        }
//                    }else{
//                        $creditoEspecif["amortiz"][$i][1]=3;
//                    }
//                }
//                if (count($amortizaciones) > 1 && (count($amortizaciones)-1 == count($pagosrealizados))) {
//                    $estadopago="completado";
//                }
                /*if(count($amortizaciones) > 1){
                    $ahorroEspecif["fechainicio"]=$amortizaciones[0]->getFechaDePago()->format("d/m/Y");
                    $ahorroEspecif["fechafin"]=$amortizaciones[count($amortizaciones)-1]->getFechaDePago()->format("d/m/Y");
//                    echo "<pre>";
//                     echo $creditoEspecif["fechainicio"];
//                    echo "<pre>";
//                     echo $creditoEspecif["fechafin"];
//                    die();
                }else{
                    $ahorroEspecif["fechainicio"]="No Definida";
                    $ahorroEspecif["fechafin"]="No Definida";
                }*/

            for($i=0;$i<count($pagosrealizados);$i++)
            {
                $pagosrealizados[$i]->getFechaDeEntrada();
            }

            if(count($pagosrealizados)== 1){
                $ahorroEspecif["fechainicio"]=$pagosrealizados[0]->getFechaDeEntrada()->format("d/m/Y");
                $ahorroEspecif["fechafin"]="No Definida";//$pagosrealizados[1]->getFechaDeEntrada()->format("d/m/Y");
            }elseif(count($pagosrealizados)== 0){
                $ahorroEspecif["fechainicio"]="No Definida";//$pagosrealizados[0]->getFechaDeEntrada()->format("d/m/Y");
                $ahorroEspecif["fechafin"]="No Definida";//$pagosrealizados[1]->getFechaDeEntrada()->format("d/m/Y");
            }elseif(count($pagosrealizados)== 2){
                $ahorroEspecif["fechainicio"]=$pagosrealizados[0]->getFechaDeEntrada()->format("d/m/Y");
                $ahorroEspecif["fechafin"]=$pagosrealizados[1]->getFechaDeEntrada()->format("d/m/Y");
                $ahorroEspecif["interes"]=$pagosrealizados[1]->getInteres();
                $ahorroEspecif["num"]=$pagosrealizados[1]->getFechaDeEntrada()->format("m")-$pagosrealizados[0]->getFechaDeEntrada()->format("m");
            }

                //0-pagado en tiempo
                //1-pagado pero con retrazo
                //2-moroso
                //3-sin pagar pero en tiempo
                $ahorroEspecif["estado"]=$estadopago;
                $ahorroEspecif["moroso"]=$estadocredito;
                $ahorrosAmort[]=$ahorroEspecif;
        }

//         foreach($creditosAmort as $ca){
//             if($ca["moroso"]){
//                 echo "<pre>";
//                 echo $ca["credito"]->getId();
//                 echo "<pre>";
//                 foreach($ca["amortiz"] as $amort){
//                     echo "<pre>";
//                     echo $amort[0]->getCreditoId()->getId();
//                     echo "&nbsp;";
//                     echo $amort[0]->getValorcuota();
//                     echo "&nbsp;";
//                     if($amort[1]==0){
//                         echo "Pagado";
//                     }else
//                         if($amort[1]==1){
//                             echo "Retrazado;";
//                         }else{
//                             if($amort[1]==2){
//                                 echo "Moroso";
//                             }else{
//                                 echo "Sin Pagar pero en Tiempo";
//                             }
//                         }
//                 }
//                 echo "<pre>";
//                 echo "Estado Credito: ".$ca["estado"];
//                 echo "<pre>";
//             }
//        }
//        die();

        return $this->render( 'ModulosPersonasBundle:Persona:carteraahorros.html.twig', array(
            'ahorros'=>$ahorrosAmort,
            'persona'=>$persona,
            'fecha'=>$fechaActual->format('d')." de ".$mesesMap[$fechaActual->format('m')]." del ".$fechaActual->format('Y'),
            'forma'=>$forma
        ));

    }
    public function carteraahorroExcelAction($id){
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
        $em = $this->getDoctrine()->getManager();
        $persona =$em->getRepository('ModulosPersonasBundle:Persona')->findOneById($id);
        $ahorros=$em->getRepository('ModulosPersonasBundle:Ahorro')->findAhorrosByPersonaOrdDescPorFecha($persona);
        $ahorrosAmort=array();

        $fechaActual=new \DateTime();
        $aux = "23:59:59";
        $fecha2 = $fechaActual->format('Y-m-d');
        $fechaaux = $fecha2." ".$aux;
        $fechaActual = new \DateTime($fechaaux);

        foreach ( $ahorros as $ahorro) {
            $ahorroEspecif=array();
            $ahorroEspecif["ahorro"]=$ahorro;
            $ahorroEspecif["amortiz"]=array();

            $estadopago="pago";
            $estadocredito=false;
                $amortizaciones = $em->getRepository('ModulosPersonasBundle:TablaAmortizacionAhorro')->findTablasAmortizacionPorAhorros($ahorro->getId());
                for ($i = 0; $i < count($amortizaciones); $i++) {
                    /*$amortizaciones[$i]->setValorCuota(round($amortizaciones[$i]->getValorcuota(), 2));
                    $amortizaciones[$i]->setCapital(round($amortizaciones[$i]->getCapital(), 2));
                    $amortizaciones[$i]->setInteres(round($amortizaciones[$i]->getInteres(), 2));
                    $amortizaciones[$i]->setSaldo(round($amortizaciones[$i]->getSaldo(), 2));*/
                    $ahorroEspecif["amortiz"][$i][0]=$amortizaciones[$i];
                    $ahorroEspecif["amortiz"][$i][1]=3;
                }

//                $pagosrealizados = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->findByCreditoId($ahorro);
////
//                for ($i = 0; $i < count($pagosrealizados); $i++) {
//                    if($amortizaciones[$i+1]->getFechaDePago()>=$pagosrealizados[$i]->getFechaDePago()){
//                        $creditoEspecif["amortiz"][$i][1]=0;
//                    }else{
//                        $creditoEspecif["amortiz"][$i][1]=1;
//                    }
//                }
//                for (; $i < count($amortizaciones); $i++) {
//                    if($amortizaciones[$i]->getFechaDePago() < $fechaActual){
//                        if($i!=0){
//                            $creditoEspecif["amortiz"][$i][1]=2;
//                            $estadocredito=true;
//                        }
//                    }else{
//                        $creditoEspecif["amortiz"][$i][1]=3;
//                    }
//                }
//                if (count($amortizaciones) > 1 && (count($amortizaciones)-1 == count($pagosrealizados))) {
//                    $estadopago="completado";
//                }
                if(count($amortizaciones) > 1){
                    $ahorroEspecif["fechainicio"]=$amortizaciones[0]->getFechaDePago()->format("d/m/Y");
                    $ahorroEspecif["fechafin"]=$amortizaciones[count($amortizaciones)-1]->getFechaDePago()->format("d/m/Y");
//                    echo "<pre>";
//                     echo $creditoEspecif["fechainicio"];
//                    echo "<pre>";
//                     echo $creditoEspecif["fechafin"];
//                    die();
                }else{
                    $ahorroEspecif["fechainicio"]="No Definida";
                    $ahorroEspecif["fechafin"]="No Definida";
                }

                //0-pagado en tiempo
                //1-pagado pero con retrazo
                //2-moroso
                //3-sin pagar pero en tiempo
                $ahorroEspecif["estado"]=$estadopago;
                $ahorroEspecif["moroso"]=$estadocredito;
                $ahorrosAmort[]=$ahorroEspecif;
        }

//         foreach($creditosAmort as $ca){
//             if($ca["moroso"]){
//                 echo "<pre>";
//                 echo $ca["credito"]->getId();
//                 echo "<pre>";
//                 foreach($ca["amortiz"] as $amort){
//                     echo "<pre>";
//                     echo $amort[0]->getCreditoId()->getId();
//                     echo "&nbsp;";
//                     echo $amort[0]->getValorcuota();
//                     echo "&nbsp;";
//                     if($amort[1]==0){
//                         echo "Pagado";
//                     }else
//                         if($amort[1]==1){
//                             echo "Retrazado;";
//                         }else{
//                             if($amort[1]==2){
//                                 echo "Moroso";
//                             }else{
//                                 echo "Sin Pagar pero en Tiempo";
//                             }
//                         }
//                 }
//                 echo "<pre>";
//                 echo "Estado Credito: ".$ca["estado"];
//                 echo "<pre>";
//             }
//        }
//        die();

        return $this->render( 'ModulosPersonasBundle:Persona:carteraahorros.html.twig', array(
            'ahorros'=>$ahorrosAmort,
            'persona'=>$persona,
            'fecha'=>$fechaActual->format('d')." de ".$mesesMap[$fechaActual->format('m')]." del ".$fechaActual->format('Y'),
        ));

    }
    public function carteraahorrounicoAction($id){
        $em = $this->getDoctrine()->getManager();

        $ahorro = $em->getRepository(
            'ModulosPersonasBundle:Ahorro'
        )->find($id);

        $pagosrealizados = $em->getRepository(
            'ModulosPersonasBundle:PagoCuotaAhorro'
        )->findPagosCuotasAhorros($id);

        $posibleExtraccion=0;


        $pagocuota = 0;
        // valor disponible para ahorro es buscar todos los pagoahorro y comparar con la fecha
        // del mes en curso para el caso de q sea de este mes no sumarle el interes
        $dateActual=new \DateTime();
//        if (count($amortizaciones) > 1 && (count($amortizaciones) >= count($pagosrealizados))) {
//            $pagocuota=$amortizaciones[count($pagosrealizados) + 1]->getValorcuota();
//        }

//        return $this->render(
//            'ModulosPersonasBundle:Libro:tablaahorro.html.twig',
//            array(
//                'pagosrealizados' => $pagosrealizados,
//                'entity' => $ahorro,
//                'pagocuota' => $pagocuota,
//                'posibleExtraccion' => $posibleExtraccion,
//                'pago' => "amort",
//            )
//        );

        return $this->render( 'ModulosPersonasBundle:Persona:carteraahorrounico.html.twig', array(
            'pagosrealizados' => $pagosrealizados,
            'entity' => $ahorro,
            'pagocuota' => $pagocuota,
            'posibleExtraccion' => $posibleExtraccion,
            'pago' => "amort",
        ));

    }
    
    public function excedentesAction($mes,$ano)
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

        $fecha1 = new \DateTime();
        $fecha2 = new \DateTime();
        $fecha3 = new \DateTime();
        $fecha4 = new \DateTime();

        $aux = "0:0:0";
        $fecha1 = $fecha1->format('Y-m-d');
        $fechaaux = $fecha1." ".$aux;
        $fecha1 = new \DateTime($fechaaux);

        $aux2 = "23:59:59";
        $fecha2 = $fecha2->format('Y-m-d');
        $fechaaux2 = $fecha2." ".$aux2;
        $fecha2 = new \DateTime($fechaaux2);

        $fecha3 = $fecha3->format('Y-m-d');
        $fechaaux3 = $fecha3 . " " . $aux2;
        $fecha3 = new \DateTime($fechaaux3);

        $fecha4 = $fecha4->format('Y-m-d');
        $fechaaux4 = $fecha4 . " " . $aux2;
        $fecha4 = new \DateTime($fechaaux4);

        $fecha1->setDate($ano, $mes, 1);

        if($fecha1->format('m')=='01')
        {
            $fecha2->setDate($ano, 12, 1);
            $aux1 = $fecha2->format('t');
            $fecha2->setDate($ano, 12, $aux1);

            $fecha3->setDate($ano, 5, 1);
            $aux6 = $fecha3->format('t');
            $fecha3->setDate($ano, 5, $aux6);

            $fecha4->setDate($ano, 9, 1);
            $aux3 = $fecha4->format('t');
            $fecha4->setDate($ano, 9, $aux3);
        }else {
            $mesF2 = (($mes - 1) == 0 ? 12 : ($mes - 1));
            $anoF2 = $ano + 1;
            $fecha2->setDate($anoF2, $mesF2, 1);
            $aux1 = $fecha2->format('t');
            $fecha2->setDate($anoF2, $mesF2, $aux1);

            $mesf3 = $mesF2 - 7;
            $fecha3->setDate($anoF2, $mesf3, 1);
            $aux6 = $fecha3->format('t');
            $fecha3->setDate($anoF2, $mesf3, $aux6);

            $mesf4 = $mesF2 - 3;
            $fecha4->setDate($anoF2, $mesf4, 1);
            $aux3 = $fecha4->format('t');
            $fecha4->setDate($anoF2, $mesf4, $aux3);
        }

        $intervalo = new \DateInterval('P1M');
        $fecha1Iterante=new \DateTime($fecha1->format('Y-m-d'));
        $arrayMeses=array();
        for($m=0; $m<12; $m++){
            $arrayMeses[]=$fecha1Iterante->format('Y-m');
            $fecha1Iterante->add($intervalo);
        }

        $fechaIntervaloLabel="De ".$mesesMap[$fecha1->format('m')]." del ".$fecha1->format('Y')
                ." a ".$mesesMap[$fecha2->format('m')]." del ".$fecha2->format('Y');

        $resumenAportes=array();

        $personas=$em->getRepository('ModulosPersonasBundle:Persona')->findOrdenadosActivos();
        $cantPersonas=count( $personas );

        $totalAportesIni=0;
        $totalTotalSuma=0;
        $totalTotalOcho=0;
        $totalTotalCuatro=0;
        $totalTotalAnio=0;

        $porcentajeXSocio=0;

        for($i=0; $i<$cantPersonas; $i++){
            $aporteInicial =0;
            $totalAporte=0;
            $totalAporteCuatro=0;
            $totalAporteOcho=0;

            $personaAportes=array(  "persona"=>$personas[$i],
                "aporteInicial"=>0,
                "totalcuatro" =>0,
                "totalocho" =>0,
                "anio" =>array($arrayMeses[0]=>0,$arrayMeses[1]=>0,$arrayMeses[2]=>0,$arrayMeses[3]=>0,$arrayMeses[4]=>0,$arrayMeses[5]=>0,$arrayMeses[6]=>0,$arrayMeses[7]=>0,$arrayMeses[8]=>0,$arrayMeses[9]=>0,$arrayMeses[10]=>0,$arrayMeses[11]=>0),
                "total"=>0,
                "totalSuma"=>0);

            $aporteIni = $em->getRepository('ModulosPersonasBundle:Libro')->findAporteInicialPorPersona($personas[$i]);
            $aportes = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersonaEntreFechasEx($personas[$i], $fecha1, $fecha2);
            $retiro = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersonaEntreFechas($personas[$i], $fecha1, $fecha2);
            $aportesCuatro = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersonaEntreFechasEx($personas[$i], $fecha1, $fecha3);
            $retiroCuatro = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersonaEntreFechas($personas[$i], $fecha1, $fecha3);
            $aportesOcho = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersonaEntreFechasEx($personas[$i], $fecha1, $fecha4);
            $retiroOcho = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersonaEntreFechas($personas[$i], $fecha1, $fecha4);


            for($j=0; $j<count($aporteIni); $j++) {
                $aporteInicial += $aporteIni[$j]->getDebe();
            }

            $personaAportes["aporteInicial"]=$aporteInicial;

            for($j=0; $j<count($aportes); $j++) {
                $totalAporte += $aportes[$j]->getDebe();
                $personaAportes["anio"][$aportes[$j]->getFecha()->format('Y-m')]+=$aportes[$j]->getDebe();
            }

            for($j=0; $j<count($retiro); $j++) {
                $totalAporte -= $retiro[$j]->getHaber();
                $personaAportes["anio"][$retiro[$j]->getFecha()->format('Y-m')]-=$retiro[$j]->getHaber();
            }

            $personaAportes["total"]=$totalAporte+$personaAportes["aporteInicial"];

            for($j=0; $j<count($aportesCuatro); $j++) {
                $totalAporteCuatro += $aportesCuatro[$j]->getDebe();
            }

            for($j=0; $j<count($retiroCuatro); $j++) {
                $totalAporteCuatro -= $retiroCuatro[$j]->getHaber();
            }

            $personaAportes["totalcuatro"]=$totalAporteCuatro+$personaAportes["aporteInicial"];

            for($j=0; $j<count($aportesOcho); $j++) {
                $totalAporteOcho += $aportesOcho[$j]->getDebe();
            }

            for($j=0; $j<count($retiroOcho); $j++) {
                $totalAporteOcho -= $retiroOcho[$j]->getHaber();
            }

            $personaAportes["totalocho"]=$totalAporteOcho+$personaAportes["aporteInicial"];

            $personaAportes["totalSuma"]=$totalAporteCuatro+$totalAporte+$totalAporteOcho+($personaAportes['aporteInicial']*4);

            $totalAportesIni+=$personaAportes['aporteInicial'];
            $totalTotalCuatro+=$personaAportes['totalcuatro'];
            $totalTotalOcho +=$personaAportes['totalocho'];
            $totalTotalAnio +=$personaAportes['total'];
            $totalTotalSuma += $personaAportes["totalSuma"];

            //$porcentajeXSocio = ($personaAportes["totalSuma"] / $totalTotalSuma)*100;

            $resumenAportes[]=$personaAportes;
        }


        //////////////////////////////////////////////////////////VALOR PYG/////////////////////////////////////////////////////////////////////////////////////////
        $totalIngreso=array();
        $totalGasto=array();
        $ingresoFinal=0;
        $gastoFinal=0;


        $ingresosOutput = array("51" => "", "5101" => "", "5104" => "", "5190" => "", "54" => "", "5490" => "", "56" => "", "5690" => "","3603"=>"");
        $gastosOutput = array("41" => "", "4101" => "", "4103" => "", "4105" => "", "44" => "", "4402" => "", "45" => "", "4501" => "", "4502" => "", "4503" => "", "4504" => "", "4507" => "");
        $ingresos = array(0 => "51", "5101", "5104", "5190", "54", "5490", "56", "5690","3603");
        $gastos = array(0 => "41", "4101", "4103", "4105", "44", "4402", "45", "4501", "4502", "4503", "4504", "4507");
        $totales = array("ingresos" => "", "gastos" => "");

        $totalIngreso[$i] = 0;
        for ($k = 0; $k < count($ingresos); $k++) {

            $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedoraAnual($ingresos[$k], $fecha1, $fecha2);
            $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudoraAnual($ingresos[$k], $fecha1, $fecha2);

            $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
            $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];
            $totalIngreso[$i] += ($deudoraValor - $acreedoraValor);
            $ingresosOutput[$ingresos[$k]] = $deudoraValor - $acreedoraValor;

        }
        $totales["ingresos"] = $totalIngreso[$i];

        $ingresoFinal += $totalIngreso[$i];

        $totalGasto[$i] = 0;
        for ($k = 0; $k < count($gastos); $k++) {
            $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedoraAnual($gastos[$k], $fecha1, $fecha2);
            $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudoraAnual($gastos[$k], $fecha1, $fecha2);

            $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
            $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

            $totalGasto[$i] = ($acreedoraValor - $deudoraValor);
            $gastosOutput[$gastos[$k]] = $acreedoraValor - $deudoraValor;
        }
        $totales["gastos"] = $totalGasto[$i];

        $gastoFinal += $totalGasto[$i];

        $pyg = $ingresoFinal - $gastoFinal;


        return $this->render(
            'ModulosPersonasBundle:Persona:excedentes.html.twig',
            array(
                'fechainicial' => $fecha1,
                'fechafinal' => $fecha2,
                'meses'=>$arrayMeses,
                'fechaLabel'=>$fechaIntervaloLabel,
                'aportes'=>$resumenAportes,
                'numsocios'=>$cantPersonas,
                'totalAportesIni'=>$totalAportesIni,
                'totalTotalSuma'=>$totalTotalSuma,
                'totalTotalOcho'=>$totalTotalOcho,
                'totalTotalCuatro'=>$totalTotalCuatro,
                'totalTotalAnio'=>$totalTotalAnio,
                'pyg'=>$pyg,
                //'porcentajeXSocio'=>$porcentajeXSocio,
            )
        );

    }


    public function impresionResumenAportesAction($mes, $ano)
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

        $resumenAportes=array();


        $personas=$em->getRepository('ModulosPersonasBundle:Persona')->findOrdenados();

        $cantPersonas=count( $personas );



        for($i=0; $i<$cantPersonas; $i++){
            $totalAporte=0;
            $totalRetiro=0;
            $aporteIni= $em->getRepository('ModulosPersonasBundle:Libro')->findAporteInicialXpersona($personas[$i]);
            $personaAportes=array(  "persona"=>$personas[$i],
                "meses"=>array($arrayMeses[0]=>0,$arrayMeses[1]=>0,$arrayMeses[2]=>0,$arrayMeses[3]=>0,$arrayMeses[4]=>0,$arrayMeses[5]=>0,$arrayMeses[6]=>0,$arrayMeses[7]=>0,$arrayMeses[8]=>0,$arrayMeses[9]=>0,$arrayMeses[10]=>0,$arrayMeses[11]=>0,),
                "retiro"=>array($arrayMeses[0]=>0,$arrayMeses[1]=>0,$arrayMeses[2]=>0,$arrayMeses[3]=>0,$arrayMeses[4]=>0,$arrayMeses[5]=>0,$arrayMeses[6]=>0,$arrayMeses[7]=>0,$arrayMeses[8]=>0,$arrayMeses[9]=>0,$arrayMeses[10]=>0,$arrayMeses[11]=>0,),
                "total"=>0);

            $aportes = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersonaEntreFechas($personas[$i], $fecha1, $fecha2);
            $retiro = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersonaEntreFechas($personas[$i], $fecha1, $fecha2);

            for($j=0; $j<count($aportes); $j++) {
                $totalAporte += $aportes[$j]->getDebe();
                $personaAportes["meses"][$aportes[$j]->getFecha()->format('Y-m')]+= $aportes[$j]->getDebe();
            }

            for($j=0; $j<count($retiro); $j++) {
                $totalRetiro += $retiro[$j]->getHaber();
                $personaAportes["retiro"][$retiro[$j]->getFecha()->format('Y-m')]+=$retiro[$j]->getHaber();
            }

            $personaAportes["total"]=$totalAporte-$totalRetiro;
            $resumenAportes[]=$personaAportes;
        }

        $totalesMeses=array($arrayMeses[0]=>0,$arrayMeses[1]=>0,$arrayMeses[2]=>0,$arrayMeses[3]=>0,$arrayMeses[4]=>0,$arrayMeses[5]=>0,$arrayMeses[6]=>0,$arrayMeses[7]=>0,$arrayMeses[8]=>0,$arrayMeses[9]=>0,$arrayMeses[10]=>0,$arrayMeses[11]=>0,);
        for($i=0; $i<count($resumenAportes); $i++){
            for($j=0; $j<count($arrayMeses); $j++) {
                $totalesMeses[$arrayMeses[$j]]+=$resumenAportes[$i]["meses"][$arrayMeses[$j]] - $resumenAportes[$i]["retiro"][$arrayMeses[$j]];
            }
        }

        $html= $this->renderView(
            'ModulosPersonasBundle:Persona:FormatoResumenAportes.html.twig',
            array(
                'fechainicial' => $fecha1,
                'fechafinal' => $fecha2,
                'meses'=>$arrayMeses,
                'mesesTotales'=>$totalesMeses,
                'mesesTexto'=>$mesesTexto,
                'fechaLabel'=>$fechaIntervaloLabel,
                'aportes'=>$resumenAportes,
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
        $pdf->SetTitle(('Resumen de Aportes'));
        $pdf->SetSubject('Resumen de Aportes');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 8, '', true);
        //$pdf->SetMargins(20,20,40, true);
        //$pdf->AddPage();
        $pdf->AddPage('L');
        //$pdf->AddPage('P', 'Letter');



        $filename = 'Resumen Aportes '.$anio;

        //$pdf->AddPage('L');
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

        $pdf->Output($filename . ".pdf", 'D'); // This will output the PDF as a response directly
    }

    public function impresionExcedentesAction($mes, $ano)
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

        $fecha1 = new \DateTime();
        $fecha2 = new \DateTime();
        $fecha3 = new \DateTime();
        $fecha4 = new \DateTime();

        $aux = "0:0:0";
        $fecha1 = $fecha1->format('Y-m-d');
        $fechaaux = $fecha1." ".$aux;
        $fecha1 = new \DateTime($fechaaux);

        $aux2 = "23:59:59";
        $fecha2 = $fecha2->format('Y-m-d');
        $fechaaux2 = $fecha2." ".$aux2;
        $fecha2 = new \DateTime($fechaaux2);

        $fecha3 = $fecha3->format('Y-m-d');
        $fechaaux3 = $fecha3 . " " . $aux2;
        $fecha3 = new \DateTime($fechaaux3);

        $fecha4 = $fecha4->format('Y-m-d');
        $fechaaux4 = $fecha4 . " " . $aux2;
        $fecha4 = new \DateTime($fechaaux4);

        $fecha1->setDate($ano, $mes, 1);

        if($fecha1->format('m')=='01')
        {
            $fecha2->setDate($ano, 12, 1);
            $aux1 = $fecha2->format('t');
            $fecha2->setDate($ano, 12, $aux1);

            $fecha3->setDate($ano, 5, 1);
            $aux6 = $fecha3->format('t');
            $fecha3->setDate($ano, 5, $aux6);

            $fecha4->setDate($ano, 9, 1);
            $aux3 = $fecha4->format('t');
            $fecha4->setDate($ano, 9, $aux3);
        }else {
            $mesF2 = (($mes - 1) == 0 ? 12 : ($mes - 1));
            $anoF2 = $ano + 1;
            $fecha2->setDate($anoF2, $mesF2, 1);
            $aux1 = $fecha2->format('t');
            $fecha2->setDate($anoF2, $mesF2, $aux1);

            $mesf3 = $mesF2 - 7;
            $fecha3->setDate($anoF2, $mesf3, 1);
            $aux6 = $fecha3->format('t');
            $fecha3->setDate($anoF2, $mesf3, $aux6);

            $mesf4 = $mesF2 - 3;
            $fecha4->setDate($anoF2, $mesf4, 1);
            $aux3 = $fecha4->format('t');
            $fecha4->setDate($anoF2, $mesf4, $aux3);
        }

        $intervalo = new \DateInterval('P1M');
        $fecha1Iterante=new \DateTime($fecha1->format('Y-m-d'));
        $arrayMeses=array();
        for($m=0; $m<12; $m++){
            $arrayMeses[]=$fecha1Iterante->format('Y-m');
            $fecha1Iterante->add($intervalo);
        }

        $fechaIntervaloLabel="De ".$mesesMap[$fecha1->format('m')]." del ".$fecha1->format('Y')
            ." a ".$mesesMap[$fecha2->format('m')]." del ".$fecha2->format('Y');

        $resumenAportes=array();

        $personas=$em->getRepository('ModulosPersonasBundle:Persona')->findOrdenadosActivos();
        $cantPersonas=count( $personas );

        $totalAportesIni=0;
        $totalTotalSuma=0;
        $totalTotalOcho=0;
        $totalTotalCuatro=0;
        $totalTotalAnio=0;

        $porcentajeXSocio=0;

        for($i=0; $i<$cantPersonas; $i++){
            $aporteInicial =0;
            $totalAporte=0;
            $totalAporteCuatro=0;
            $totalAporteOcho=0;

            $personaAportes=array(  "persona"=>$personas[$i],
                "aporteInicial"=>0,
                "totalcuatro" =>0,
                "totalocho" =>0,
                "anio" =>array($arrayMeses[0]=>0,$arrayMeses[1]=>0,$arrayMeses[2]=>0,$arrayMeses[3]=>0,$arrayMeses[4]=>0,$arrayMeses[5]=>0,$arrayMeses[6]=>0,$arrayMeses[7]=>0,$arrayMeses[8]=>0,$arrayMeses[9]=>0,$arrayMeses[10]=>0,$arrayMeses[11]=>0),
                "total"=>0,
                "totalSuma"=>0);

            $aporteIni = $em->getRepository('ModulosPersonasBundle:Libro')->findAporteInicialPorPersona($personas[$i]);
            $aportes = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersonaEntreFechasEx($personas[$i], $fecha1, $fecha2);
            $retiro = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersonaEntreFechas($personas[$i], $fecha1, $fecha2);
            $aportesCuatro = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersonaEntreFechasEx($personas[$i], $fecha1, $fecha3);
            $retiroCuatro = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersonaEntreFechas($personas[$i], $fecha1, $fecha3);
            $aportesOcho = $em->getRepository('ModulosPersonasBundle:Libro')->findAportePorPersonaEntreFechasEx($personas[$i], $fecha1, $fecha4);
            $retiroOcho = $em->getRepository('ModulosPersonasBundle:Libro')->findRetiroAportePorPersonaEntreFechas($personas[$i], $fecha1, $fecha4);


            for($j=0; $j<count($aporteIni); $j++) {
                $aporteInicial += $aporteIni[$j]->getDebe();
            }

            $personaAportes["aporteInicial"]=$aporteInicial;

            for($j=0; $j<count($aportes); $j++) {
                $totalAporte += $aportes[$j]->getDebe();
                $personaAportes["anio"][$aportes[$j]->getFecha()->format('Y-m')]+=$aportes[$j]->getDebe();
            }

            for($j=0; $j<count($retiro); $j++) {
                $totalAporte -= $retiro[$j]->getHaber();
                $personaAportes["anio"][$retiro[$j]->getFecha()->format('Y-m')]-=$retiro[$j]->getHaber();
            }

            $personaAportes["total"]=$totalAporte+$personaAportes["aporteInicial"];

            for($j=0; $j<count($aportesCuatro); $j++) {
                $totalAporteCuatro += $aportesCuatro[$j]->getDebe();
            }

            for($j=0; $j<count($retiroCuatro); $j++) {
                $totalAporteCuatro -= $retiroCuatro[$j]->getHaber();
            }

            $personaAportes["totalcuatro"]=$totalAporteCuatro+$personaAportes["aporteInicial"];

            for($j=0; $j<count($aportesOcho); $j++) {
                $totalAporteOcho += $aportesOcho[$j]->getDebe();
            }

            for($j=0; $j<count($retiroOcho); $j++) {
                $totalAporteOcho -= $retiroOcho[$j]->getHaber();
            }

            $personaAportes["totalocho"]=$totalAporteOcho+$personaAportes["aporteInicial"];

            $personaAportes["totalSuma"]=$totalAporteCuatro+$totalAporte+$totalAporteOcho+($personaAportes['aporteInicial']*4);

            $totalAportesIni+=$personaAportes['aporteInicial'];
            $totalTotalCuatro+=$personaAportes['totalcuatro'];
            $totalTotalOcho +=$personaAportes['totalocho'];
            $totalTotalAnio +=$personaAportes['total'];
            $totalTotalSuma += $personaAportes["totalSuma"];

            //$porcentajeXSocio = ($personaAportes["totalSuma"] / $totalTotalSuma)*100;

            $resumenAportes[]=$personaAportes;
        }


        //////////////////////////////////////////////////////////VALOR PYG/////////////////////////////////////////////////////////////////////////////////////////
        $totalIngreso=array();
        $totalGasto=array();
        $ingresoFinal=0;
        $gastoFinal=0;


        $ingresosOutput = array("51" => "", "5101" => "", "5104" => "", "5190" => "", "54" => "", "5490" => "", "56" => "", "5690" => "","3603"=>"");
        $gastosOutput = array("41" => "", "4101" => "", "4103" => "", "4105" => "", "44" => "", "4402" => "", "45" => "", "4501" => "", "4502" => "", "4503" => "", "4504" => "", "4507" => "");
        $ingresos = array(0 => "51", "5101", "5104", "5190", "54", "5490", "56", "5690","3603");
        $gastos = array(0 => "41", "4101", "4103", "4105", "44", "4402", "45", "4501", "4502", "4503", "4504", "4507");
        $totales = array("ingresos" => "", "gastos" => "");

        $totalIngreso[$i] = 0;
        for ($k = 0; $k < count($ingresos); $k++) {

            $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedoraAnual($ingresos[$k], $fecha1, $fecha2);
            $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudoraAnual($ingresos[$k], $fecha1, $fecha2);

            $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
            $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];
            $totalIngreso[$i] += ($deudoraValor - $acreedoraValor);
            $ingresosOutput[$ingresos[$k]] = $deudoraValor - $acreedoraValor;

        }
        $totales["ingresos"] = $totalIngreso[$i];

        $ingresoFinal += $totalIngreso[$i];

        $totalGasto[$i] = 0;
        for ($k = 0; $k < count($gastos); $k++) {
            $acreedora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCAcreedoraAnual($gastos[$k], $fecha1, $fecha2);
            $deudora = $em->getRepository('ModulosPersonasBundle:DTVC')->findDTVCDeudoraAnual($gastos[$k], $fecha1, $fecha2);

            $acreedoraValor = ($acreedora[0][1] == null) ? 0 : $acreedora[0][1];
            $deudoraValor = ($deudora[0][1] == null) ? 0 : $deudora[0][1];

            $totalGasto[$i] = ($acreedoraValor - $deudoraValor);
            $gastosOutput[$gastos[$k]] = $acreedoraValor - $deudoraValor;
        }
        $totales["gastos"] = $totalGasto[$i];

        $gastoFinal += $totalGasto[$i];

        $pyg = $ingresoFinal - $gastoFinal;


        $html= $this->renderView(
            'ModulosPersonasBundle:Persona:FormatoExcedentes.html.twig',
            array(
                'fechainicial' => $fecha1,
                'fechafinal' => $fecha2,
                'meses'=>$arrayMeses,
                'fechaLabel'=>$fechaIntervaloLabel,
                'aportes'=>$resumenAportes,
                'numsocios'=>$cantPersonas,
                'totalAportesIni'=>$totalAportesIni,
                'totalTotalSuma'=>$totalTotalSuma,
                'totalTotalOcho'=>$totalTotalOcho,
                'totalTotalCuatro'=>$totalTotalCuatro,
                'totalTotalAnio'=>$totalTotalAnio,
                'pyg'=>$pyg,
                //'porcentajeXSocio'=>$porcentajeXSocio,
            )
        );

        $this->returnPDFResponseFromHTMLExcedentes($html);
    }

    public function returnPDFResponseFromHTMLExcedentes($html)
    {
        //set_time_limit(30); uncomment this line according to your needs
        // If you are not in a controller, retrieve of some way the service container and then retrieve it
        //$pdf = $this->container->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //if you are in a controlller use :
        $pdf = $this->get("white_october.tcpdf")->create();// create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('Conquito');
        $pdf->SetTitle(('Excedentes'));
        $pdf->SetSubject('Excedentes');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 8, '', true);
        //$pdf->SetMargins(20,20,40, true);
        //$pdf->AddPage();
        $pdf->AddPage('L');
        //$pdf->AddPage('P', 'Letter');



        $filename = 'Excedentes';

        //$pdf->AddPage('L');
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

        $pdf->Output($filename . ".pdf", 'D'); // This will output the PDF as a response directly
    }
}
