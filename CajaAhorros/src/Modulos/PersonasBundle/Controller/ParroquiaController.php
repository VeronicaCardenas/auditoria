<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\Parroquia;
use Modulos\PersonasBundle\Form\ParroquiaType;

/**
 * Parroquia controller.
 *
 */
class ParroquiaController extends Controller
{

    /**
     * Lists all Parroquia entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:Parroquia')->findAll();

        return $this->render('ModulosPersonasBundle:Parroquia:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Parroquia entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Parroquia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('parroquia_show', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:Parroquia:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Parroquia entity.
     *
     * @param Parroquia $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Parroquia $entity)
    {
        $form = $this->createForm(new ParroquiaType(), $entity, array(
            'action' => $this->generateUrl('parroquia_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Parroquia entity.
     *
     */
    public function newAction()
    {
        $entity = new Parroquia();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:Parroquia:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Parroquia entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Parroquia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Parroquia entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Parroquia:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Parroquia entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Parroquia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Parroquia entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:Parroquia:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Parroquia entity.
    *
    * @param Parroquia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Parroquia $entity)
    {
        $form = $this->createForm(new ParroquiaType(), $entity, array(
            'action' => $this->generateUrl('parroquia_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Parroquia entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:Parroquia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Parroquia entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('parroquia_edit', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:Parroquia:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Parroquia entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ModulosPersonasBundle:Parroquia')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Parroquia entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('parroquia'));
    }

    /**
     * Creates a form to delete a Parroquia entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parroquia_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
