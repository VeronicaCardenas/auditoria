<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\TipoDeParcialidad;
use Modulos\PersonasBundle\Form\TipoDeParcialidadType;

/**
 * TipoDeParcialidad controller.
 *
 */
class TipoDeParcialidadController extends Controller
{

    /**
     * Lists all TipoDeParcialidad entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:TipoDeParcialidad')->findAll();

        return $this->render('ModulosPersonasBundle:TipoDeParcialidad:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TipoDeParcialidad entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TipoDeParcialidad();
        $form = $this->createForm(new TipoDeParcialidadType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el tipo de parcialidad'
            ); 
            return $this->redirect($this->generateUrl('tipodeparcialidad', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:TipoDeParcialidad:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TipoDeParcialidad entity.
     *
     * @param TipoDeParcialidad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /*private function createCreateForm(TipoDeParcialidad $entity)
    {
        $form = $this->createForm(new TipoDeParcialidadType(), $entity, array(
            'action' => $this->generateUrl('tipodeparcialidad_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new TipoDeParcialidad entity.
     *
     */
    /*public function newAction()
    {
        $entity = new TipoDeParcialidad();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:TipoDeParcialidad:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a TipoDeParcialidad entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoDeParcialidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDeParcialidad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoDeParcialidad:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TipoDeParcialidad entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoDeParcialidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDeParcialidad entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoDeParcialidad:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a TipoDeParcialidad entity.
    *
    * @param TipoDeParcialidad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoDeParcialidad $entity)
    {
        $form = $this->createForm(new TipoDeParcialidadType(), $entity, array(
            'action' => $this->generateUrl('tipodeparcialidad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TipoDeParcialidad entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoDeParcialidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDeParcialidad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el tipo de parcialidad'
            );
            return $this->redirect($this->generateUrl('tipodeparcialidad', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:TipoDeParcialidad:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a TipoDeParcialidad entity.
     *
     */
     public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:TipoDeParcialidad')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el tipo de parcialidad'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('tipodeparcialidad'));
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
        return $this->redirect($this->generateUrl('tipodeparcialidad'));
    }

     /**
     * Creates a form to delete a TipoDeParcialidad entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipodeparcialidad_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
