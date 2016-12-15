<?php

namespace MusicShareBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SoundType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, array('data_class' => null))
            ->add('coverFile', FileType::class, array('data_class' => null, 'required' => false))
            ->add('songName', TextType::class)
            ->add('songAuthor', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MusicShareBundle\Entity\Sound',
        ));
    }

    public function getName()
    {
        return 'music_share_bundle_sound_type';
    }
}
