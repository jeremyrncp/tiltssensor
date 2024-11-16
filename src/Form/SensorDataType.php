<?php

namespace App\Form;

use App\Entity\Sensor;
use App\Entity\SensorData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SensorDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timestamp')
            ->add('clientName')
            ->add('messageCounter')
            ->add('payloadType')
            ->add('frameType')
            ->add('temp')
            ->add('battery')
            ->add('sensorPosition')
            ->add('timeSinceLastChange')
            ->add('flapping')
            ->add('acceleratometer1')
            ->add('acceleratometer2')
            ->add('ledStatus')
            ->add('tempLedBlink')
            ->add('keepaliveInterval')
            ->add('lowTempThresold')
            ->add('highTempThresold')
            ->add('sensor', EntityType::class, [
                'class' => Sensor::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SensorData::class,
        ]);
    }
}
