<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\Cuenta;

use Modulos\PersonasBundle\Form\CuentaType;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Cuenta controller.
 *
 */
class CuentaController extends Controller
{

    /**
     * Lists all Cuenta entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:Cuenta')->findAll();

        return $this->render('ModulosPersonasBundle:Cuenta:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Cuenta entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Cuenta();
        $form = $this->createForm(new CuentaType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
             $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado la cuenta'
            );
            return $this->redirect($this->generateUrl('cuenta', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:Cuenta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Cuenta entity.
     *
     * @param Cuenta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /*private function createCreateForm(Cuenta $entity)
    {
        $form = $this->createForm(new CuentaType(), $entity, array(
            'action' => $this->generateUrl('cuenta_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new Cuenta entity.
     *
     */
    /*public function newAction()
    {
        $entity = new Cuenta();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:Cuenta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a Cuenta entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Cuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cuenta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Cuenta:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Cuenta entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Cuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cuenta entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Cuenta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Cuenta entity.
    *
    * @param Cuenta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Cuenta $entity)
    {
        $form = $this->createForm(new CuentaType(), $entity, array(
            'action' => $this->generateUrl('cuenta_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Cuenta entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Cuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cuenta entity.');
        }

        $editForm = $this->createForm(new CuentaType(),$entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
             $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado la cuenta'
            );
            return $this->redirect($this->generateUrl('cuenta', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:Cuenta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Cuenta entity.
     *
     */
   public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:Cuenta')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado la cuenta'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('cuenta'));
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
        return $this->redirect($this->generateUrl('cuenta'));
    }

    /**
     * Creates a form to delete a Cuenta entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cuenta_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function exportarCuentasAction(){
        $em=  $this->getDoctrine()->getManager();
        $cuentas = $em->getRepository('ModulosPersonasBundle:Cuenta')->findAll();
        

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Giulio De Donato")
            ->setTitle("Listado de Cuentas")
            ->setSubject("Listado de Cuentas")
            ->setDescription("Listado de Cuentas")
            ->setKeywords("Listado de Cuentas")
            ->setCategory("Reporte excel");

        $tituloReporte = "Reporte de Cuentas";
        $titulosColumnas = array('CODIGO','NOMBRE DE LA CUENTA','SIGLAS');
        /*$phpExcelObject->setActiveSheetIndex(0)
            ->mergeCells('A1:D1')
        ;*/

        // Se agregan los titulos del reporte
        $phpExcelObject->setActiveSheetIndex(0)
            //->setCellValue('A1',"Reporte de Cuentas")
            ->setCellValue('A1',$titulosColumnas[0])
            ->setCellValue('B1',$titulosColumnas[1])
            ->setCellValue('C1',$titulosColumnas[2])
        ;
        $i = 2;
        for($j=0;$j<count($cuentas);$j++){
            $codigo = "";
            $nombre="";
            $siglas = "";
            
            if($cuentas[$j]->getCodigo()!=null){
                $codigo = $cuentas[$j]->getCodigo();

            }            
            if($cuentas[$j]->getNombre()!=null){
                $nombre = $cuentas[$j]->getNombre();
            }
            if($cuentas[$j]->getSiglas()!=null){
                $siglas = $cuentas[$j]->getSiglas();            }
            
            $phpExcelObject->setActiveSheetIndex(0)
                // ->setCellValue('A'.$i, $factura)
                ->setCellValue('A'.$i, $codigo)
                ->setCellValue('B'.$i, $nombre)
                ->setCellValue('C'.$i, $siglas)
                ;
            $i++;
        }
        $estiloTituloReporte = array(
            'font' => array(
                'name'      => 'Verdana',
                'bold'      => true,
                'italic'    => false,
                'strike'    => false,
                'size' =>16,
                'color'     => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'fill' => array(
                'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
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
                'name'  => 'Arial',
                'bold'  => true,
                'color' => array(
                    'rgb' => 'blue'
                )
            ),
            'fill' => array(
                'type'       => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                'rotation'   => 90,
                'startcolor' => array(
                    'rgb' => 'c47cf2'
                ),
                'endcolor' => array(
                    'argb' => 'FF431a5d'
                )
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                ),
                'bottom' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                )
            ),
            'alignment' =>  array(
                'horizontal'=> \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'      => TRUE
            )
        );

        
        //$phpExcelObject->getActiveSheet()->getStyle('A1:D1')->applyFromArray($estiloTituloReporte);
        $phpExcelObject->getActiveSheet()->getStyle('A1:C1')->applyFromArray($estiloTituloColumnas);
        //$phpExcelObject->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:W".($i-1));
        for($i = 'A'; $i <= 'C'; $i++){
            $phpExcelObject->setActiveSheetIndex(0)->getColumnDimension($i)->setAutoSize(TRUE);
        }

        //$phpExcelObject->getActiveSheet()->setTitle($tituloReporte);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Cuentas.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }


}
