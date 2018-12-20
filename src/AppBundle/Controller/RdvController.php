<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Patient;
use AppBundle\Entity\Rdv;

class RdvController extends Controller
{

    /**
     * @Route("rdv/", name="view_rdv_route")
     */
    public function viewRdvAction()
    {
        $rdvs = $this->getDoctrine()->getRepository('AppBundle:Rdv')->findAll();
        return $this->render("pages/rdv.html.twig", ['rdvs' => $rdvs]);
    }

    /**
     * @Route("rdv/create", name="create_rdv_route")
     */
    public function createRdvAction(Request $request)
    {
        $rdv = new Rdv;
        $form = $this->createFormBuilder($rdv)
            ->add('patient', EntityType::class, array('attr' => array('class' => 'form-control'),
                'class' => Patient::class,
                'choice_label' => 'name',
                'multiple' => false
            ))
            ->add('date', DateTimeType::class, array('attr' => array('style' => 'display: flex;margin-bottom: 10px;'),
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',
                )
            ))
            ->add('type', ChoiceType::class, array('attr' => array('class' => 'form-control'), 'choices' => array('consultation' => 'consultation', 'control' => 'control')))
            ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary btn-lg', 'style' => 'margin-top: 20px'), 'label' => 'Create Rdv'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rdv->setDate($form['date']->getData());
            $rdv->setStatus("outside");
            $rdv->setType($form['type']->getData());
            $rdv->setPatient($form['patient']->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($rdv);
            $em->flush();
            $this->addFlash('message', 'Rdv saved successfully');
            $this->redirectToRoute('view_rdv_route');
        }
        return $this->render("pages/create-rdv.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("rdv/update/{id}", name="update_rdv_route")
     */
    public function updateRdvAction($id, Request $request)
    {
        $rdv = $this->getDoctrine()->getRepository('AppBundle:Rdv')->find($id);
        $form = $this->createFormBuilder($rdv)
            ->add('patient', EntityType::class, array(
                'class' => Patient::class,
                'choice_label' => 'name',
                'multiple' => false
            ))
            ->add('date', DateTimeType::class, array(
                'placeholder' => 'Select a value',
            ))
            ->add('type', ChoiceType::class, array('attr' => array('class' => 'form-control'), 'choices' => array('consultation' => 'consultation', 'control' => 'control')))
            ->add('status', ChoiceType::class, array('attr' => array('class' => 'form-control'), 'choices' => array('completed' => 'completed', 'current' => 'current', 'waiting' => 'waiting', 'outside' => 'outside')))
            ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary btn-lg', 'style' => 'margin-top: 20px'), 'label' => 'Update Rdv'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $rdv = $em->getRepository('AppBundle:Rdv')->find($id);

            $rdv->setDate($form['date']->getData());
            $rdv->setStatus($form['status']->getData());
            $rdv->setType($form['type']->getData());
            $rdv->setPatient($form['patient']->getData());

            $em->flush();
            $this->addFlash('message', 'Rdv updated successfully');
            $this->redirectToRoute('view_rdv_route');
        }
        return $this->render("pages/update-rdv.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("rdv/show/{id}", name="show_rdv_route")
     */
    public function showRdvAction($id)
    {
        $rdv = $this->getDoctrine()->getRepository('AppBundle:Rdv')->find($id);
        return $this->render("pages/show-rdv.html.twig", ['rdv' => $rdv]);
    }

    /**
     * @Route("rdv/delete/{id}", name="delete_rdv_route")
     */
    public function deleteRdvAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $rdv = $em->getRepository('AppBundle:Rdv')->find($id);
        $em->remove($rdv);
        $em->flush();
        return $this->redirectToRoute('view_rdv_route');
    }
}
