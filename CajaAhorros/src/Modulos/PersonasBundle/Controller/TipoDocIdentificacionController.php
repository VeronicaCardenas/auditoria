<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\TipoDocIdentificacion;
use Modulos\PersonasBundle\Form\TipoDocIdentificacionType;

/**
 * TipoDocIdentificacion controller.
 *
 */
class TipoDocIdentificacionController extends Controller
{

    /**
     * Lists all TipoDocIdentificacion entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:TipoDocIdentificacion')->findAll();

        return $this->render('ModulosPersonasBundle:TipoDocIdentificacion:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TipoDocIdentificacion entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TipoDocIdentificacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipodocidentificacion_show', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:TipoDocIdentificacion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TipoDocIdentificacion entity.
     *
     * @param TipoDocIdentificacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TipoDocIdentificacion $entity)
    {
        $form = $this->createForm(new TipoDocIdentificacionType(), $entity, array(
            'action' => $this->generateUrl('tipodocidentificacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoDocIdentificacion entity.
     *
     */
    public function newAction()
    {
        $entity = new TipoDocIdentificacion();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:TipoDocIdentificacion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TipoDocIdentificacion entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoDocIdentificacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDocIdentificacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoDocIdentificacion:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TipoDocIdentificacion entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoDocIdentificacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDocIdentificacion entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoDocIdentificacion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a TipoDocIdentificacion entity.
    *
    * @param TipoDocIdentificacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoDocIdentificacion $entity)
    {
        $form = $this->createForm(new TipoDocIdentificacionType(), $entity, array(
            'action' => $this->generateUrl('tipodocidentificacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TipoDocIdentificacion entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoDocIdentificacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDocIdentificacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipodocidentificacion_edit', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:TipoDocIdentificacion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a TipoDocIdentificacion entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ModulosPersonasBundle:TipoDocIdentificacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TipoDocIdentificacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tipodocidentificacion'));
    }

    /**
     * Creates a form to delete a TipoDocIdentificacion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipodocidentificacion_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
