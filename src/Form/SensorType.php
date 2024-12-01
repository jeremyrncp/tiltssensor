<?php

namespace App\Form;

use App\Entity\Lift;
use App\Entity\Sensor;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SensorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier')
            ->add('name')
            ->add('description')
            ->add('latitude')
            ->add('longitude')
            ->add('floor')
            ->add('lift', EntityType::class, [
                'class' => Lift::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sensor::class,
        ]);
    }
}
