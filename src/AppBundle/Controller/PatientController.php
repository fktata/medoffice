<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Patient;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PatientController extends Controller
{

    /**
     * @Route("patient/", name="view_patient_route")
     */
    public function viewPatientAction()
    {
        $patients = $this->getDoctrine()->getRepository('AppBundle:Patient')->findAll();
        return $this->render("pages/index.html.twig", ['patients' => $patients]);
    }

    /**
     * @Route("patient/create", name="create_patient_route")
     */
    public function createPatientAction(Request $request)
    {
        $patient = new Patient;
        $form = $this->createFormBuilder($patient)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Enter the name')))
            ->add('civility', ChoiceType::class, array('attr' => array('class' => 'form-control'), 'choices' => array('Male' => 'Male', 'Female' => 'Female')))
            ->add('phone', TextType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Enter the phone')))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Enter the email')))
            ->add('birth', DateType::class, array('attr' => array('class' => 'form-control'), 'widget' => 'single_text'))
            ->add('adress', TextType::class, array('required' => false, 'attr' => array('class' => 'form-control', 'placeholder' => 'Enter the adress')))
            ->add('profession', TextType::class, array('required' => false, 'attr' => array('class' => 'form-control', 'placeholder' => 'Enter the profession')))
            ->add('notes', TextareaType::class, array('required' => false, 'attr' => array('class' => 'form-control', 'placeholder' => 'Enter the notes')))
            ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary btn-lg', 'style' => 'margin-top: 20px'), 'label' => 'Create Patient'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $patient->setName($form['name']->getData());
            $patient->setCivility($form['civility']->getData());
            $patient->setPhone($form['phone']->getData());
            $patient->setEmail($form['email']->getData());
            $patient->setBirth($form['birth']->getData());
            $patient->setAdress($form['adress']->getData());
            $patient->setProfession($form['profession']->getData());
            $patient->setNotes($form['notes']->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($patient);
            $em->flush();
            $this->addFlash('message', 'Patient saved successfully');
            $this->redirectToRoute('view_patient_route');
        }
        return $this->render("pages/create-patient.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("patient/update/{id}", name="update_patient_route")
     */
    public function updatePatientAction($id, Request $request)
    {
        $patient = $this->getDoctrine()->getRepository('AppBundle:Patient')->find($id);
        $form = $this->createFormBuilder($patient)
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Enter the name')))
            ->add('civility', ChoiceType::class, array('attr' => array('class' => 'form-control'), 'choices' => array('Male' => 'Male', 'Female' => 'Female')))
            ->add('phone', TextType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Enter the phone')))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Enter the email')))
            ->add('birth', DateType::class, array('attr' => array('class' => 'form-control'), 'widget' => 'single_text'))
            ->add('adress', TextType::class, array('attr' => array('required' => false, 'class' => 'form-control', 'placeholder' => 'Enter the adress')))
            ->add('profession', TextType::class, array('required' => false, 'attr' => array('class' => 'form-control', 'placeholder' => 'Enter the profession')))
            ->add('notes', TextareaType::class, array('required' => false, 'attr' => array('class' => 'form-control', 'placeholder' => 'Enter the notes')))
            ->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary btn-lg', 'style' => 'margin-top: 20px'), 'label' => 'Update Patient'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $patient = $em->getRepository('AppBundle:Patient')->find($id);
            $patient->setName($form['name']->getData());
            $patient->setCivility($form['civility']->getData());
            $patient->setPhone($form['phone']->getData());
            $patient->setEmail($form['email']->getData());
            $patient->setBirth($form['birth']->getData());
            $patient->setAdress($form['adress']->getData());
            $patient->setProfession($form['profession']->getData());
            $patient->setNotes($form['notes']->getData());

            $em->flush();
            $this->addFlash('message', 'Patient updated successfully');
            $this->redirectToRoute('view_patient_route');
        }
        return $this->render("pages/update-patient.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("patient/show/{id}", name="show_patient_route")
     */
    public function showPatientAction($id)
    {
        $patient = $this->getDoctrine()->getRepository('AppBundle:Patient')->find($id);
        return $this->render("pages/show-patient.html.twig", ['patient' => $patient]);
    }

    /**
     * @Route("patient/delete/{id}", name="delete_patient_route")
     */
    public function deletePatientAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $patient = $em->getRepository('AppBundle:Patient')->find($id);
        $em->remove($patient);
        $em->flush();
        return $this->redirectToRoute('view_patient_route');
    }
}
