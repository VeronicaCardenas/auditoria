<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\Operador;
use Modulos\PersonasBundle\Form\OperadorType;

/**
 * Operador controller.
 *
 */
class OperadorController extends Controller
{

    /**
     * Lists all Operador entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:Operador')->findAll();

        return $this->render('ModulosPersonasBundle:Operador:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Operador entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Operador();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('operador_show', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:Operador:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Operador entity.
     *
     * @param Operador $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Operador $entity)
    {
        $form = $this->createForm(new OperadorType(), $entity, array(
            'action' => $this->generateUrl('operador_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Operador entity.
     *
     */
    public function newAction()
    {
        $entity = new Operador();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:Operador:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Operador entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Operador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Operador entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Operador:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Operador entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Operador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Operador entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Operador:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Operador entity.
    *
    * @param Operador $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Operador $entity)
    {
        $form = $this->createForm(new OperadorType(), $entity, array(
            'action' => $this->generateUrl('operador_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Operador entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Operador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Operador entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('operador_edit', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:Operador:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Operador entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ModulosPersonasBundle:Operador')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Operador entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('operador'));
    }

    /**
     * Creates a form to delete a Operador entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('operador_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
