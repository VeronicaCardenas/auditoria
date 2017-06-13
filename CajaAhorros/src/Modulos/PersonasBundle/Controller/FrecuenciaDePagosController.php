<?php

namespace Modulos\PersonasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Modulos\PersonasBundle\Entity\FrecuenciaDePagos;
use Modulos\PersonasBundle\Form\FrecuenciaDePagosType;

/**
 * FrecuenciaDePagos controller.
 *
 */
class FrecuenciaDePagosController extends Controller
{

    /**
     * Lists all FrecuenciaDePagos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ModulosPersonasBundle:FrecuenciaDePagos')->findAll();

        return $this->render('ModulosPersonasBundle:FrecuenciaDePagos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new FrecuenciaDePagos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new FrecuenciaDePagos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha creado la frecuencia de pago'
            ); 
            return $this->redirect($this->generateUrl('frecuenciadepagos', array('id' => $entity->getId())));
        }

        return $this->render('ModulosPersonasBundle:FrecuenciaDePagos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a FrecuenciaDePagos entity.
     *
     * @param FrecuenciaDePagos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FrecuenciaDePagos $entity)
    {
        $form = $this->createForm(new FrecuenciaDePagosType(), $entity, array(
            'action' => $this->generateUrl('frecuenciadepagos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new FrecuenciaDePagos entity.
     *
     */
    public function newAction()
    {
        $entity = new FrecuenciaDePagos();
        $form   = $this->createCreateForm($entity);

        return $this->render('ModulosPersonasBundle:FrecuenciaDePagos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a FrecuenciaDePagos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:FrecuenciaDePagos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FrecuenciaDePagos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:FrecuenciaDePagos:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing FrecuenciaDePagos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:FrecuenciaDePagos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FrecuenciaDePagos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ModulosPersonasBundle:FrecuenciaDePagos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a FrecuenciaDePagos entity.
    *
    * @param FrecuenciaDePagos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(FrecuenciaDePagos $entity)
    {
        $form = $this->createForm(new FrecuenciaDePagosType(), $entity, array(
            'action' => $this->generateUrl('frecuenciadepagos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing FrecuenciaDePagos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ModulosPersonasBundle:FrecuenciaDePagos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FrecuenciaDePagos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha actualizado la frecuencia de pago'
            );

            return $this->redirect($this->generateUrl('frecuenciadepagos', array('id' => $id)));
        }

        return $this->render('ModulosPersonasBundle:FrecuenciaDePagos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a FrecuenciaDePagos entity.
     *
     */
   
     public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ModulosPersonasBundle:FrecuenciaDePagos')->find($id);
        try {
            $em->remove($entity);
            $em->flush();
              $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha eliminado la frecuencia de pago'
            );
        }
        catch (\Doctrine\DBAL\DBALException $e){
            if ($e->getCode() == 0)
            {
                if ($e->getPrevious()->getCode() == 23000)
                {
                    $this->get('session')->getFlashBag()->add('error', "No se puede eliminar porque tiene registros relacionados.");
                    return $this->redirect($this->generateUrl('frecuenciadepagos'));
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
        return $this->redirect($this->generateUrl('frecuenciadepagos'));
    }

    /**
     * Creates a form to delete a FrecuenciaDePagos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('frecuenciadepagos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
