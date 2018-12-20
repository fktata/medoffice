<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class EventController extends Controller
{
    /**
     * @Route("event/", name="view_event_route")
     */
    public function viewEventAction()
    {
        $events = $this->getDoctrine()->getRepository('AppBundle:Event')->findAll();
        return $this->render("pages/event.html.twig", ['events' => $events]);
    }

    /**
     * @Route("event/create", name="create_event_route")
     */
    public function createEventAction(Request $request)
    {
        $event = new Event;
        $form = $this->createFormBuilder($event)
            ->add('date', DateTimeType::class, array('attr' => array('style' => 'display: flex;margin-bottom: 10px;'),
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',
                )
            ))
            ->add('note', TextType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Enter the notes')))
            ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary btn-lg', 'style' => 'margin-top: 20px'), 'label' => 'Create Event'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $event->setDate($form['date']->getData());
            $event->setNote($form['note']->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
            $this->addFlash('message', 'Event saved successfully');
            $this->redirectToRoute('view_event_route');
        }
        return $this->render("pages/create-event.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("event/update/{id}", name="update_event_route")
     */
    public function updateEventAction($id, Request $request)
    {
        $event = $this->getDoctrine()->getRepository('AppBundle:Event')->find($id);
        $form = $this->createFormBuilder($event)
            ->add('date', DateTimeType::class, array('attr' => array('style' => 'display: flex;margin-bottom: 10px;'),
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',
                )
            ))
            ->add('note', TextType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Enter the notes')))
            ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary btn-lg', 'style' => 'margin-top: 20px'), 'label' => 'Update Event'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $event = $em->getRepository('AppBundle:Event')->find($id);

            $event->setDate($form['date']->getData());
            $event->setNote($form['note']->getData());

            $em->flush();
            $this->addFlash('message', 'Event updated successfully');
            $this->redirectToRoute('view_event_route');
        }
        return $this->render("pages/update-event.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("event/delete/{id}", name="delete_event_route")
     */
    public function deleteEventAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('AppBundle:Event')->find($id);
        $em->remove($event);
        $em->flush();
        return $this->redirectToRoute('view_event_route');
    }
}
