<?php

namespace Fabien\EventsEngineBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Fabien\EventsEngineBundle\Entity\CategoryPost;
use Fabien\EventsEngineBundle\Repository\CategoryPostRepository;

class PostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('slug',TextType::class, array('required'=>1,'label'=>"Slug de l'article","attr"=>array("placeholder"=>"Exemple : apprendre-le-tango ")))
                ->add('author',TextType::class, array('required'=>0,'label'=>"Auteur de l'article","attr"=>array("placeholder"=>"Exemple : JojoTanguero ")))
                ->add('mailAuthor',TextType::class, array('required'=>0,'label'=>"Email de l'auteur","attr"=>array("placeholder"=>" Exemple : jean.dupont@gmail.com ")))
                ->add('CategoryPost',EntityType::class, array(
                    'class'        => 'FabienEventsEngineBundle:CategoryPost',
                    'choice_label' => 'title',
                    'query_builder' => function(CategoryPostRepository $repository) {
                       return $repository->getByRank();
                     },
                    'multiple'     => true,
                    'expanded'=>true
                    ))
                ->add('date',DateTimeType::class)
                ->add('publish',CheckboxType::class,array("label"=>"Publié  ?","required"=>false))
                ->add('title',TextType::class, array('required'=>1,'label'=>"Titre de l'article","attr"=>array("placeholder"=>"Exemple : Milonga du printemps ")))
                ->add('header',TextareaType::class, array('required'=>0,'label'=>"Chapeau", 'attr' => array('rows' => '4',"placeholder"=>"")))
                ->add('content',TextareaType::class, array('required'=>0,'label'=>"Contenu", 'attr' => array('class'=>"editor",'rows' => '15',"placeholder"=>"")))
                ->add('file', FileType::class,array("label"=>"Image de l'article","required"=>false))
                ->add('titleTrad',TextType::class, array('required'=>0,'label'=>"Titre de l'article - traduction","attr"=>array("placeholder"=>" ")))
                ->add('headerTrad',TextareaType::class, array('required'=>0,'label'=>"Chapeau - traduction", 'attr' => array('rows' => '4',"placeholder"=>"")))
                ->add('contentTrad',TextareaType::class, array('required'=>0,'label'=>"Contenu - traduction", 'attr' => array('class'=>"editor",'rows' => '15',"placeholder"=>"")))


                //->add('images')
                ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fabien\EventsEngineBundle\Entity\Post'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fabien_eventsenginebundle_post';
    }


}
