<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\State;
use App\Entity\Trip;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('dateAndTime', null, [
                'widget' => 'single_text',
            ])
            ->add('duration')
            ->add('seats')
            ->add('textNote')
            ->add('registrationDeadline', null, [
                'widget' => 'single_text',
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'location',
            ])
            ->add('state', EntityType::class, [
                'class' => State::class,
                'choice_label' => 'label',
                'data' => $options['state'],
                'disabled' => true,
            ])
            ->add('organizer', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'username',
                'data' => $options['organizer'],
                'disabled' => true,
            ])
 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
            'organizer' => null,
            'state' => null,
        ]);
    }
}
