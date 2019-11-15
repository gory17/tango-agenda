<?php

namespace Fabien\EventsEngineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Fabien\EventsEngineBundle\Entity\State;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class CityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
                ->add('urlImage')
                ->add('slug')
                ->add('state',EntityType::class, array(
                'class'        => 'FabienEventsEngineBundle:State',
                'choice_label' => 'title',
               ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fabien\EventsEngineBundle\Entity\City'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fabien_eventsenginebundle_city';
    }


}
