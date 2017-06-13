<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\NivelDeCuenta;
use Modulos\PersonasBundle\Form\NivelDeCuentaType;

/**
 * NivelDeCuenta controller.
 *
 */
class NivelDeCuentaController extends Controller
{

    /**
     * Lists all NivelDeCuenta entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:NivelDeCuenta')->findAll();

        return $this->render('ModulosPersonasBundle:NivelDeCuenta:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new NivelDeCuenta entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new NivelDeCuenta();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('niveldecuenta_show', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:NivelDeCuenta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a NivelDeCuenta entity.
     *
     * @param NivelDeCuenta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(NivelDeCuenta $entity)
    {
        $form = $this->createForm(new NivelDeCuentaType(), $entity, array(
            'action' => $this->generateUrl('niveldecuenta_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new NivelDeCuenta entity.
     *
     */
    public function newAction()
    {
        $entity = new NivelDeCuenta();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:NivelDeCuenta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a NivelDeCuenta entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:NivelDeCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NivelDeCuenta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:NivelDeCuenta:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NivelDeCuenta entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:NivelDeCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NivelDeCuenta entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:NivelDeCuenta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a NivelDeCuenta entity.
    *
    * @param NivelDeCuenta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(NivelDeCuenta $entity)
    {
        $form = $this->createForm(new NivelDeCuentaType(), $entity, array(
            'action' => $this->generateUrl('niveldecuenta_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing NivelDeCuenta entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:NivelDeCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NivelDeCuenta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('niveldecuenta_edit', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:NivelDeCuenta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a NivelDeCuenta entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ModulosPersonasBundle:NivelDeCuenta')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find NivelDeCuenta entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('niveldecuenta'));
    }

    /**
     * Creates a form to delete a NivelDeCuenta entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('niveldecuenta_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
