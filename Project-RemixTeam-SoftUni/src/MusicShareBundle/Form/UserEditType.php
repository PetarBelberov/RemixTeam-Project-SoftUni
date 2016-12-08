<?php
/**
 * Created by IntelliJ IDEA.
 * User: Johny_Domino
 * Date: 8.12.2016 г.
 * Time: 14:25
 */

namespace MusicShareBundle\Form;


use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends UserType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MusicShareBundle\Entity\User',
        ));
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('roles', ChoiceType::class, array(
            'choices' => [
                'Admin' => "ROLE_ADMIN",
                'User' => "ROLE_USER",
            ],
            'expanded' => true,
            'multiple' => true,
        ));
    }
}