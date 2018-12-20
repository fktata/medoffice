<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Rdv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    /**
     * @Route("dashboard/", name="change_type")
     */
    public function viewRdvTodayAction(Request $request)
    {
        $status = $request->request->get('status');
        $rdvId = $request->request->get('rdv');

        if ($status && $rdvId) {
            $rdv = $this->getDoctrine()->getRepository('AppBundle:Rdv')->find($rdvId);
            $rdv->setStatus($status);
            $em = $this->getDoctrine()->getManager();
            $em->persist($rdv);
            $em->flush();
            if ($status == 'current') {
                $now = new \DateTime(date("Y-m-d H:i:s"));
                $startDate = $rdv->getDate();
                if ($now > $startDate) {
                    $diff = $now->getTimestamp() - $startDate->getTimestamp();
                    $delay_type = "delay";
                } else {
                    $diff = $startDate->getTimestamp() - $now->getTimestamp();
                    $delay_type = "win";
                }
                $delay = date('H:i:s', mktime(0, 0, $diff));
                $cookieDelay = new Cookie('delay', $delay, strtotime('today 23:59'), '/');
                $response = new Response();
                $response->headers->setCookie($cookieDelay);
                $response->send();
                $cookieDelayType = new Cookie('delayType', $delay_type, strtotime('today 23:59'), '/');
                $response = new Response();
                $response->headers->setCookie($cookieDelayType);
                $response->send();
                return $this->redirect($request->getUri());
            }
        }
        $rdvs = $this->getDoctrine()->getRepository('AppBundle:Rdv')->getRdvToday();
        $completed = $this->getDoctrine()->getRepository('AppBundle:Rdv')->getRdvByStatus("completed");
        $waiting = $this->getDoctrine()->getRepository('AppBundle:Rdv')->getRdvByStatus("waiting");
        $outside = $this->getDoctrine()->getRepository('AppBundle:Rdv')->getRdvByStatus("outside");
        $waitingRdv = $this->getDoctrine()->getRepository('AppBundle:Rdv')->getCurrentRdv();
        $cookies = $request->cookies;
        $data = array(
            'rdvs' => $rdvs,
            'completed' => $completed,
            'waiting' => $waiting,
            'upcoming' => $outside,
            'waitingRdv' => $waitingRdv,
            'delay' => $cookies->has('delay') ? $cookies->get('delay') : 0,
            'delayType' => $cookies->has('delayType') ? $cookies->get('delayType') : "draw"
        );

        return $this->render("pages/dashboard.html.twig", [
            'data' => $data
        ]);
    }
}
