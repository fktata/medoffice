<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AgendaController extends Controller
{
    /**
     * @Route("agenda/", name="view_agenda_route")
     */
    public function viewPatientAction(Request $request)
    {
        $form = $this->createFormBuilder(null)
            ->add('startDate', DateType::class, array('attr' => array('class' => 'form-control'), 'widget' => 'single_text'))
            ->add('endDate', DateType::class, array('attr' => array('class' => 'form-control'), 'widget' => 'single_text'))
            ->add('search', SubmitType::class, array('attr' => array('class' => 'btn btn-info', 'style' => 'margin-top: 30px'), 'label' => 'search'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()){
                $startDate = $form['startDate']->getData();
                $endDate = $form['endDate']->getData();
                $rdvs = $this->getDoctrine()->getRepository('AppBundle:Rdv')->getFiltredRdvAgenda($startDate,$endDate);
                $events = $this->getDoctrine()->getRepository('AppBundle:Event')->getFiltredEventAgenda($startDate,$endDate);
                $tasks = array_merge($rdvs,$events);
                usort($tasks, function ($a, $b) {
                    return strnatcmp($a['date']->format('Y-m-d H:i:s'), $b['date']->format('Y-m-d H:i:s'));
                });
            }
        }
        else {
            $rdvs = $this->getDoctrine()->getRepository('AppBundle:Rdv')->getRdvAgenda();
            $events = $this->getDoctrine()->getRepository('AppBundle:Event')->getEventAgenda();
            $tasks = array_merge($rdvs,$events);
            usort($tasks, function ($a, $b) {
                return strnatcmp($a['date']->format('Y-m-d H:i:s'), $b['date']->format('Y-m-d H:i:s'));
            });
        }
        return $this->render("pages/agenda.html.twig", ["tasks" => $tasks, "form" => $form->createView()]);
    }
}
