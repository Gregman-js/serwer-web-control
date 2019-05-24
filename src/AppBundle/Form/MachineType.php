<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * Class MachineType
 * @package AppBundle\Form
 */

class MachineType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', null, array('label' => "Name"))
        ->add('ipaddress', null, array('label' => "IP Address"))
        ->add('mac', null, array('label' => "MAC Address"))
        ->add('username', null, array('label' => "SSH username"))
        ->add('pass', PasswordType::class, array('label' => "SSH Password"))
        ->add('submit', SubmitType::class, array('label' => 'Save' , 'attr' => array('class'=>'btn btn-primary ')  ))
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Machine',
            'allow_extra_fields'=>true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_machine';
    }


}
