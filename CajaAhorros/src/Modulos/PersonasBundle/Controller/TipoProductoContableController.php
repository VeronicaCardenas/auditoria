<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\TipoProductoContable;
use Modulos\PersonasBundle\Form\TipoProductoContableType;

/**
 * TipoProductoContable controller.
 *
 */
class TipoProductoContableController extends Controller
{

    /**
     * Lists all TipoProductoContable entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->findAll();

        return $this->render('ModulosPersonasBundle:TipoProductoContable:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TipoProductoContable entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TipoProductoContable();
        $form = $this->createForm(new TipoProductoContableType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el tipo de producto contable'
            );
            return $this->redirect($this->generateUrl('tipoproductocontable', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:TipoProductoContable:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TipoProductoContable entity.
     *
     * @param TipoProductoContable $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /*private function createCreateForm(TipoProductoContable $entity)
    {
        $form = $this->createForm(new TipoProductoContableType(), $entity, array(
            'action' => $this->generateUrl('tipoproductocontable_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new TipoProductoContable entity.
     *
     */
   /* public function newAction()
    {
        $entity = new TipoProductoContable();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:TipoProductoContable:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a TipoProductoContable entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoProductoContable entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoProductoContable:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TipoProductoContable entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoProductoContable entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoProductoContable:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a TipoProductoContable entity.
    *
    * @param TipoProductoContable $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoProductoContable $entity)
    {
        $form = $this->createForm(new TipoProductoContableType(), $entity, array(
            'action' => $this->generateUrl('tipoproductocontable_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TipoProductoContable entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoProductoContable entity.');
        }

        $editForm = $this->createForm(new TipoProductoContableType(),$entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el tipo de producto contable'
            );
            return $this->redirect($this->generateUrl('tipoproductocontable', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:TipoProductoContable:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a TipoProductoContable entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:TipoProductoContable')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('tipoproductocontable'));
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
        return $this->redirect($this->generateUrl('tipoproductocontable'));
    }

    
    /**
     * Creates a form to delete a TipoProductoContable entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipoproductocontable_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
