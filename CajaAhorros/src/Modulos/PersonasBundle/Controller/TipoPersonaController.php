<?php

namespace Modulos\PersonasBundle\Controller;

use Modulos\PersonasBundle\Form\EntidadType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\TipoPersona;
use Modulos\PersonasBundle\Form\TipoPersonaType;

/**
 * TipoPersona controller.
 *
 */
class TipoPersonaController extends Controller
{

    /**
     * Lists all TipoPersona entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:TipoPersona')->findAll();

        return $this->render('ModulosPersonasBundle:TipoPersona:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TipoPersona entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TipoPersona();
        $form = $this->createForm(new TipoPersonaType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado el tipo de persona'
            );

            return $this->redirect($this->generateUrl('tipopersona'));
        }

        return $this->render('ModulosPersonasBundle:TipoPersona:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TipoPersona entity.
     *
     * @param TipoPersona $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
   /* private function createCreateForm(TipoPersona $entity)
    {
        $form = $this->createForm(new TipoPersonaType(), $entity, array(
            'action' => $this->generateUrl('tipopersona_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }*/

    /**
     * Displays a form to create a new TipoPersona entity.
     *
     */
   /* public function newAction()
    {
        $entity = new TipoPersona();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:TipoPersona:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }*/

    /**
     * Finds and displays a TipoPersona entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoPersona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoPersona entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoPersona:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TipoPersona entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoPersona')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoPersona entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:TipoPersona:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a TipoPersona entity.
    *
    * @param TipoPersona $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoPersona $entity)
    {
        $form = $this->createForm(new TipoPersonaType(), $entity, array(
            'action' => $this->generateUrl('tipopersona_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TipoPersona entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:TipoPersona')->find($id);


        $form = $this->createForm(new TipoPersonaType(),$entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado el tipo de persona'
            );

            return $this->redirect($this->generateUrl('tipopersona'));
        }

        return $this->render('ModulosPersonasBundle:TipoPersona:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $form->createView(),

        ));
    }
    /**
     * Deletes a TipoPersona entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:TipoPersona')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado el tipo de persona'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('tipopersona'));
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
        return $this->redirect($this->generateUrl('tipopersona'));
    }

    /**
     * Creates a form to delete a TipoPersona entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipopersona_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
