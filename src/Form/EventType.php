<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\FormatEvent;
use App\Entity\Society;
use App\Entity\User;
use App\Entity\StatusEvent;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', TextType::class)
            ->add('statusEvent', EntityType::class, [
                'class' => StatusEvent::class,
                'choice_label' => 'name',
                'choice_attr' => function ($key) use ($options) {
                    $disabled = true;

                    if ($key->getState() == $options['status'] || ($options['status'] < $options['statusFullState']
                            && $key->getState() < $options['statusFullState'] && $options['nbPlayers'] == 0)) {
                        $disabled = false;
                    }

                    return $disabled ? ['disabled' => 'disabled'] : [];
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.state', 'ASC');
                }
            ])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('date', DateTimeType::class)
            ->add('formatEvent', EntityType::class, [
                'class' => FormatEvent::class,
                'choice_label' => 'name',
                'choice_attr' => function ($key) use ($options) {
                    $disabled = true;

                    if ($key->getNumberOfPlayers() >= $options['nbPlayers']) {
                        $disabled = false;
                    }

                    return $disabled ? ['disabled' => 'disabled'] : [];
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('f')
                        ->orderBy('f.numberOfPlayers', 'ASC');
                }
            ])
            ->add('logoFile', FileType::class, [
                'required' => false
            ])
            ->add('roundMinutes', IntegerType::class)
            ->add('roundSeconds', IntegerType::class)
            ->add('pauseMinutes', IntegerType::class)
            ->add('pauseSeconds', IntegerType::class)
            ->add('society', EntityType::class, [
                'class' => Society::class,
                'choice_label' => 'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'status' => null,
            'statusFullState' => null,
            'nbPlayers' => 0
        ]);
    }
}
