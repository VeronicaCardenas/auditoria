<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\TipoDeCobro;
use Modulos\PersonasBundle\Form\TipoDeCobroType;

/**
 * TipoDeCobro controller.
 *
 */
class TipoDeCobroController extends Controller
{

    /**
     * Lists all TipoDeCobro entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:TipoDeCobro')->findAll();

        return $this->render('ModulosPersonasBundle:TipoDeCobro:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TipoDeCobro entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TipoDeCobro();
        $form = $this->createForm(new TipoDeCobroType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el tipo de cobro'
            ); 
            return $this->redirect($this->generateUrl('tipodecobro', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:TipoDeCobro:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TipoDeCobro entity.
     *
     * @param TipoDeCobro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /*private function createCreateForm(TipoDeCobro $entity)
    {
        $form = $this->createForm(new TipoDeCobroType(), $entity, array(
            'action' => $this->generateUrl('tipodecobro_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new TipoDeCobro entity.
     *
     */
    /*public function newAction()
    {
        $entity = new TipoDeCobro();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:TipoDeCobro:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a TipoDeCobro entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoDeCobro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDeCobro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoDeCobro:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TipoDeCobro entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoDeCobro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDeCobro entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoDeCobro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a TipoDeCobro entity.
    *
    * @param TipoDeCobro $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoDeCobro $entity)
    {
        $form = $this->createForm(new TipoDeCobroType(), $entity, array(
            'action' => $this->generateUrl('tipodecobro_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TipoDeCobro entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoDeCobro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDeCobro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
             $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el tipo de cobro'
            );
            return $this->redirect($this->generateUrl('tipodecobro', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:TipoDeCobro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a TipoDeCobro entity.
     *
     */
    
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:TipoDeCobro')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el tipo de cobro'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('tipodecobro'));
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
        return $this->redirect($this->generateUrl('tipodecobro'));
    }

     /**
     * Creates a form to delete a TipoDeCobro entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipodecobro_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
