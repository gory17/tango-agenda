<?php

namespace Fabien\EventsEngineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Fabien\EventsEngineBundle\Entity\Date;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('start',DateTimeType::class, array('required'=>0,
                      'label'=>"Date et heure début",
                      'format' => 'dd/MM/yyyy kk:mm',
                      'date_widget'=>'single_text',
                      'date_format'=>'dd/MM/yyyy',
                      'time_widget' => 'choice',

                    ))
                ->add('end',DateTimeType::class, array('required'=>0,
                      'label'=>"Date et heure de fin",
                      'format' => 'dd/MM/yyyy kk:mm',
                      'date_widget'=>'single_text',
                      'date_format'=>'dd/MM/yyyy',
                      'time_widget' => 'choice',

                    ))
                ->add('description',TextareaType::class, array('required'=>0,'label'=>"Infos supplémentaires", 'attr' => array('rows' => '6',"placeholder"=>"Exemple : Dj invité, démo etc... ")))
                ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fabien\EventsEngineBundle\Entity\Date'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fabien_eventsenginebundle_date';
    }


}
