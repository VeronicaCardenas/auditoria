<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\ProductoContableCuenta;
use Modulos\PersonasBundle\Form\ProductoContableCuentaType;

/**
 * ProductoContableCuenta controller.
 *
 */
class ProductoContableCuentaController extends Controller
{

    /**
     * Lists all ProductoContableCuenta entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:ProductoContableCuenta')->findAll();

        return $this->render('ModulosPersonasBundle:ProductoContableCuenta:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ProductoContableCuenta entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ProductoContableCuenta();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('productocontablecuenta_show', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:ProductoContableCuenta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ProductoContableCuenta entity.
     *
     * @param ProductoContableCuenta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ProductoContableCuenta $entity)
    {
        $form = $this->createForm(new ProductoContableCuentaType(), $entity, array(
            'action' => $this->generateUrl('productocontablecuenta_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ProductoContableCuenta entity.
     *
     */
    public function newAction()
    {
        $entity = new ProductoContableCuenta();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:ProductoContableCuenta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ProductoContableCuenta entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ProductoContableCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoContableCuenta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:ProductoContableCuenta:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ProductoContableCuenta entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ProductoContableCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoContableCuenta entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:ProductoContableCuenta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ProductoContableCuenta entity.
    *
    * @param ProductoContableCuenta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ProductoContableCuenta $entity)
    {
        $form = $this->createForm(new ProductoContableCuentaType(), $entity, array(
            'action' => $this->generateUrl('productocontablecuenta_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ProductoContableCuenta entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:ProductoContableCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductoContableCuenta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('productocontablecuenta_edit', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:ProductoContableCuenta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ProductoContableCuenta entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ModulosPersonasBundle:ProductoContableCuenta')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProductoContableCuenta entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('productocontablecuenta'));
    }

    /**
     * Creates a form to delete a ProductoContableCuenta entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productocontablecuenta_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
