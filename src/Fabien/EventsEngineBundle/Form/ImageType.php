<?php

namespace Fabien\EventsEngineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;



use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ImageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

                ->add('alt',TextType::class,array('label'=>"Description de l'image","attr"=>array("placeholder"=>"Exemple : Salle de bal de la milonga"),'required' => true))
               ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fabien\EventsEngineBundle\Entity\Image'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fabien_eventsenginebundle_image';
    }


}
