<?php

namespace Fabien\EventsEngineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PersonType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('slug',TextType::class, array('required'=>1,'label'=>"Slug","attr"=>array("placeholder"=>"nom-prenom")))
                ->add('prenom',TextType::class, array('required'=>0,'label'=>"Prénom","attr"=>array("placeholder"=>"")))

                ->add('nom',TextType::class, array('required'=>1,'label'=>"Nom","attr"=>array("placeholder"=>"")))
                ->add('surnom',TextType::class, array('required'=>0,'label'=>"Surnom","attr"=>array("placeholder"=>"Chicho")))
                ->add('siteweb',TextType::class, array('required'=>0,'label'=>"Site web","attr"=>array("placeholder"=>"")))
                ->add('facebook',TextType::class, array('required'=>0,'label'=>"Facebook","attr"=>array("placeholder"=>"")))
                ->add('wikipedia',TextType::class, array('required'=>0,'label'=>"Wikipedia","attr"=>array("placeholder"=>"")))
                ->add('type',ChoiceType::class, array(
                  'choices'  => array(
                      'Maestro' => "maestro",
                      'DJ' => "dj",
                      'Autre' => "other",
                  )))
                ->add('role',ChoiceType::class, array('label'=>"Rôle",
                  'choices'  => array(
                      '' => "",
                      'leader' => "Leader",
                      'Follower' => "follower",
                  )))
                  ->add('partner',EntityType::class,array('required'=>0,"label"=>"Partenaire",'class'=>'FabienEventsEngineBundle:Person','placeholder' => 'Choose an option','expanded'  => false,
                      'multiple'  => false, 'query_builder' => function(\Doctrine\ORM\EntityRepository $person) {
                          return $person->getPersons();
                      }))
                ->add('file', FileType::class,array("label"=>"Image de la personne","required"=>false))
                ->add('creditPhoto',TextType::class, array('required'=>0,'label'=>"Crédit Photo","attr"=>array("placeholder"=>"")))
                ->add('description',TextareaType::class, array('required'=>0,'label'=>"Contenu", 'attr' => array('class'=>"editor",'rows' => '15',"placeholder"=>"")))
                ->add('active')
                ->add('homonyme')
                ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fabien\EventsEngineBundle\Entity\Person'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fabien_eventsenginebundle_person';
    }


}
