<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\PagoCuotaCredito;
use Modulos\PersonasBundle\Form\PagoCuotaCreditoType;

/**
 * PagoCuotaCredito controller.
 *
 */
class PagoCuotaCreditoController extends Controller
{

    /**
     * Lists all PagoCuotaCredito entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->findAll();

        return $this->render('ModulosPersonasBundle:PagoCuotaCredito:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new PagoCuotaCredito entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new PagoCuotaCredito();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

        if ($form->isValid()) {
            if(count($libros)){
                foreach ($libros as $libro) {
                    if ($libro->getEstadosLibro()->getEstado() == 'CERRADO' && $libro->getFecha()->format('m-Y') == $entity->getFechaDePago()->format('m-Y')) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'No se puede crear un libro en un mes cerrado.'
                        );
                        return $this->redirect($this->generateUrl('creditos_pago', array('id' => $entity->getCreditoId()->getId())));
                    }
                }
            }
            $em->persist($entity);
            $em->flush();

            if($entity->getSininteres()==1){
                $si=$entity->getSininteres();
            }else{
                $si=0;
            }


            //return $this->redirect($this->generateUrl('creditos_pago', array('id' => $entity->getCreditoId()->getId())));
            return $this->redirect($this->generateUrl('creditos_pagoLibro', array('id' => $entity->getCreditoId()->getId(),'sininteres'=>$si)));
            //return $this->redirect($this->generateUrl('creditos'));
        }

        return $this->render('ModulosPersonasBundle:PagoCuotaCredito:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function LiqParcialAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = new PagoCuotaCredito();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $libros = $em->getRepository('ModulosPersonasBundle:Libro')->findLibrosOrdenadosDESC();

        if ($form->isValid()) {
            if(count($libros)){
                foreach ($libros as $libro) {
                    if ($libro->getEstadosLibro()->getEstado() == 'CERRADO' && $libro->getFecha()->format('m-Y') == $entity->getFechaDePago()->format('m-Y')) {
                        $this->get('session')->getFlashBag()->add(
                            'danger',
                            'No se puede crear un libro en un mes cerrado.'
                        );
                        return $this->redirect($this->generateUrl('creditos_pago', array('id' => $entity->getCreditoId()->getId())));
                    }
                }
            }

            return $this->redirect($this->generateUrl('creditos_liquidacion_parcial', array('id' => $entity->getCreditoId()->getId(), 'cuotas'=>$entity->getCoutas(), 'ano'=>$entity->getFechaDePago()->format('Y'), 'mes'=>$entity->getFechaDePago()->format('m'), 'dia'=>$entity->getFechaDePago()->format('d'),'h'=>$entity->getFechaDePago()->format('H'), 'm'=>$entity->getFechaDePago()->format('i'), 's'=>$entity->getFechaDePago()->format('s'))));
            //return $this->redirect($this->generateUrl('creditos_liquidacion_parcial', array('id' => $entity->getCreditoId()->getId(), 'cuotas'=>$entity->getCoutas())));
        }
    }

    /**
     * Creates a form to create a PagoCuotaCredito entity.
     *
     * @param PagoCuotaCredito $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PagoCuotaCredito $entity)
    {
        $form = $this->createForm(new PagoCuotaCreditoType(), $entity, array(
            'action' => $this->generateUrl('pagocuotacredito_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new PagoCuotaCredito entity.
     *
     */
    public function newAction()
    {
        $entity = new PagoCuotaCredito();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:PagoCuotaCredito:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PagoCuotaCredito entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PagoCuotaCredito entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:PagoCuotaCredito:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PagoCuotaCredito entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PagoCuotaCredito entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:PagoCuotaCredito:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a PagoCuotaCredito entity.
    *
    * @param PagoCuotaCredito $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PagoCuotaCredito $entity)
    {
        $form = $this->createForm(new PagoCuotaCreditoType(), $entity, array(
            'action' => $this->generateUrl('pagocuotacredito_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing PagoCuotaCredito entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PagoCuotaCredito entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('pagocuotacredito_edit', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:PagoCuotaCredito:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a PagoCuotaCredito entity.
     *
     */
    public function deleteAction($id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ModulosPersonasBundle:PagoCuotaCredito')->find($id);
            $id_credito=    $entity->getCreditoId();
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PagoCuotaCredito entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('creditos_pago',array('id' => $id_credito)));
    }

    /**
     * Creates a form to delete a PagoCuotaCredito entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pagocuotacredito_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
