<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\MetodoAmortizacionAhorro;
use Modulos\PersonasBundle\Form\MetodoAmortizacionAhorroType;

/**
 * MetodoAmortizacionAhorro controller.
 *
 */
class MetodoAmortizacionAhorroController extends Controller
{

    /**
     * Lists all MetodoAmortizacionAhorro entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:MetodoAmortizacionAhorro')->findAll();

        return $this->render('ModulosPersonasBundle:MetodoAmortizacionAhorro:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new MetodoAmortizacionAhorro entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MetodoAmortizacionAhorro();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('metodoamortizacionahorro_show', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:MetodoAmortizacionAhorro:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MetodoAmortizacionAhorro entity.
     *
     * @param MetodoAmortizacionAhorro $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MetodoAmortizacionAhorro $entity)
    {
        $form = $this->createForm(new MetodoAmortizacionAhorroType(), $entity, array(
            'action' => $this->generateUrl('metodoamortizacionahorro_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new MetodoAmortizacionAhorro entity.
     *
     */
    public function newAction()
    {
        $entity = new MetodoAmortizacionAhorro();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:MetodoAmortizacionAhorro:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a MetodoAmortizacionAhorro entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:MetodoAmortizacionAhorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MetodoAmortizacionAhorro entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:MetodoAmortizacionAhorro:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing MetodoAmortizacionAhorro entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:MetodoAmortizacionAhorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MetodoAmortizacionAhorro entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:MetodoAmortizacionAhorro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a MetodoAmortizacionAhorro entity.
    *
    * @param MetodoAmortizacionAhorro $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MetodoAmortizacionAhorro $entity)
    {
        $form = $this->createForm(new MetodoAmortizacionAhorroType(), $entity, array(
            'action' => $this->generateUrl('metodoamortizacionahorro_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing MetodoAmortizacionAhorro entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:MetodoAmortizacionAhorro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Imposible encontrar la Entidad MetodoAmortizacionAhorro.');
        }

        $editForm = $this->createForm(new MetodoAmortizacionAhorroType(),$entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el Metodo de Amortizacion de ahorro'
            );

            return $this->redirect($this->generateUrl('metodoamortizacionahorro', array('id' => $id)));
        }
//        echo "valido:  ".$editForm->isValid();
//        var_dump($editForm);
//        die();
        return $this->render('ModulosPersonasBundle:MetodoAmortizacionAhorro:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a MetodoAmortizacionAhorro entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ModulosPersonasBundle:MetodoAmortizacionAhorro')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MetodoAmortizacionAhorro entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('metodoamortizacionahorro'));
    }

    /**
     * Creates a form to delete a MetodoAmortizacionAhorro entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('metodoamortizacionahorro_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
