<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\MetodoDeAmortizacion;
use Modulos\PersonasBundle\Form\MetodoDeAmortizacionType;

/**
 * MetodoDeAmortizacion controller.
 *
 */
class MetodoDeAmortizacionController extends Controller
{

    /**
     * Lists all MetodoDeAmortizacion entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:MetodoDeAmortizacion')->findAll();

        return $this->render('ModulosPersonasBundle:MetodoDeAmortizacion:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new MetodoDeAmortizacion entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MetodoDeAmortizacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('metododeamortizacion_show', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:MetodoDeAmortizacion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MetodoDeAmortizacion entity.
     *
     * @param MetodoDeAmortizacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MetodoDeAmortizacion $entity)
    {
        $form = $this->createForm(new MetodoDeAmortizacionType(), $entity, array(
            'action' => $this->generateUrl('metododeamortizacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new MetodoDeAmortizacion entity.
     *
     */
    public function newAction()
    {
        $entity = new MetodoDeAmortizacion();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:MetodoDeAmortizacion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a MetodoDeAmortizacion entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:MetodoDeAmortizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MetodoDeAmortizacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:MetodoDeAmortizacion:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing MetodoDeAmortizacion entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:MetodoDeAmortizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MetodoDeAmortizacion entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:MetodoDeAmortizacion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a MetodoDeAmortizacion entity.
    *
    * @param MetodoDeAmortizacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MetodoDeAmortizacion $entity)
    {
        $form = $this->createForm(new MetodoDeAmortizacionType(), $entity, array(
            'action' => $this->generateUrl('metododeamortizacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing MetodoDeAmortizacion entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:MetodoDeAmortizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Imposible encontrar la Entidad MetodoDeAmortizacion.');
        }

        $editForm = $this->createForm(new MetodoDeAmortizacionType(),$entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el Metodo de Amortizacion de crédito'
            );

            return $this->redirect($this->generateUrl('metododeamortizacion', array('id' => $id)));
        }
//        echo "valido:  ".$editForm->isValid();
//        var_dump($editForm);
//        die();
        return $this->render('ModulosPersonasBundle:MetodoDeAmortizacion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a MetodoDeAmortizacion entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ModulosPersonasBundle:MetodoDeAmortizacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MetodoDeAmortizacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('metododeamortizacion'));
    }

    /**
     * Creates a form to delete a MetodoDeAmortizacion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('metododeamortizacion_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
