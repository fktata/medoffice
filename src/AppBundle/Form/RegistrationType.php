<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Enter the username')))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Enter the email')))
            ->add('password', PasswordType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Enter the password')))
            ->add('confirm_password', PasswordType::class, array('attr' => array('class' => 'form-control', 'placeholder' => 'Retape the password')))
            ->add('type', ChoiceType::class, array('attr' => array('class' => 'form-control'), 'choices' => array('Secretary' => 'secretary', 'Doctor' => 'doctor')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}