<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\RegimenMatrimonial;
use Modulos\PersonasBundle\Form\RegimenMatrimonialType;

/**
 * RegimenMatrimonial controller.
 *
 */
class RegimenMatrimonialController extends Controller
{

    /**
     * Lists all RegimenMatrimonial entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:RegimenMatrimonial')->findAll();

        return $this->render('ModulosPersonasBundle:RegimenMatrimonial:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new RegimenMatrimonial entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new RegimenMatrimonial();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('regimenmatrimonial_show', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:RegimenMatrimonial:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a RegimenMatrimonial entity.
     *
     * @param RegimenMatrimonial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RegimenMatrimonial $entity)
    {
        $form = $this->createForm(new RegimenMatrimonialType(), $entity, array(
            'action' => $this->generateUrl('regimenmatrimonial_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new RegimenMatrimonial entity.
     *
     */
    public function newAction()
    {
        $entity = new RegimenMatrimonial();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:RegimenMatrimonial:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a RegimenMatrimonial entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:RegimenMatrimonial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegimenMatrimonial entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:RegimenMatrimonial:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing RegimenMatrimonial entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:RegimenMatrimonial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegimenMatrimonial entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:RegimenMatrimonial:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a RegimenMatrimonial entity.
    *
    * @param RegimenMatrimonial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(RegimenMatrimonial $entity)
    {
        $form = $this->createForm(new RegimenMatrimonialType(), $entity, array(
            'action' => $this->generateUrl('regimenmatrimonial_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing RegimenMatrimonial entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:RegimenMatrimonial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegimenMatrimonial entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('regimenmatrimonial_edit', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:RegimenMatrimonial:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a RegimenMatrimonial entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ModulosPersonasBundle:RegimenMatrimonial')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find RegimenMatrimonial entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('regimenmatrimonial'));
    }

    /**
     * Creates a form to delete a RegimenMatrimonial entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('regimenmatrimonial_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
