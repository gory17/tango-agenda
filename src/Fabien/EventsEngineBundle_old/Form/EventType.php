<?php

namespace Fabien\EventsEngineBundle\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Fabien\EventsEngineBundle\repository\CityRepository;
use Fabien\EventsEngineBundle\repository\CountryRepository;
use Fabien\EventsEngineBundle\Entity\Country;
use Fabien\EventsEngineBundle\Entity\City;
use Fabien\EventsEngineBundle\Entity\Date;

class EventType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title' ,     TextType::class, array('required'=>true,'label'=>"Titre de l'événement","attr"=>array("placeholder"=>"Exemple : Milonga du printemps ")))
        ->add('organizer',TextType::class,array('label'=>"Organisateur","attr"=>array("placeholder"=>"Exemple : Association Tangoloso"),'required' => false))
        ->add('description', TextareaType::class, array('required'=>1,'label'=>"Description", 'attr' => array('rows' => '20')))
        ->add('urlWeb', TextType::class, array('required'=>false,'label'=>"Site web","attr"=>array("placeholder"=>"Exemple : https://www.tangoloso.com")))
        ->add('urlFb',TextType::class, array('required'=>false,'label'=>"Adresse Facebook","attr"=>array("placeholder"=>"Exemple : https://www.facebook.com/events/334047523619810/")))
        ->add('adress',TextType::class, array('required'=>1,'label'=>"Adresse"))
        ->add('country',EntityType::class,array('label'=>"Pays",'class'=>'FabienEventsEngineBundle:Country','choice_label'=>'title','required'=>true))
        ->add('city_other',TextType::class,array('label'=>"Autre ville (si vous n'avez pas trouvez votre ville dans la liste)",'required'=>false))
        ->add('type_event',EntityType::class,array("label"=>"Type d'événement",'class'=>'FabienEventsEngineBundle:TypeEvent','choice_label'=>'title',))
        ->add('inscription', ChoiceType::class, array('label'=>'Evénement sur inscription ?',"choices"=>array("non"=>0,"oui"=>1)))
        ->add('day', ChoiceType::class, array('label'=>'jour',"choices"=>array("chaque lundi"=>1,"chaque mardi"=>2,"chaque mercredi"=>3,"chaque jeudi"=>4,"chaque vendredi"=>5,"chaque samedi"=>6,"chaque dimanche"=>7),"required"=>false))
        ->add('dateInscription', DateTimeType::class, array('required'=>0,'label'=>"Date et heure de l'ouverture des inscriptions",'format' => 'dd/MM/yyyy kk:mm'))
        ->add('genderBalance', ChoiceType::class, array('label'=>'Parité guideur & guidé ?',"choices"=>array("non"=>0,"oui"=>1)))
        ->add('file', FileType::class,array("label"=>"Envoyer une image (taille max 1Mo)","required"=>false))
        ->add('Sauvegarder', SubmitType::class)
        ->add('dates', CollectionType::class, array(
              'entry_type'   => DateType::class,
              'allow_add'    => true,
              'allow_delete' => true,
              //ultra important ... sinon la correspondance entre les deux ne marchera pas
              'by_reference' => false,
              "prototype"=> true,
              'data_class' => null,
              "prototype_data"=>new Date()
            ))
          ;

          //Parties qui se modifient selon les droits
          if($options['userRole']=="admin"){
            $builder->add('publish',CheckBoxType::class, array('required'=>false,'label'=>"Publié ?"))
            ->add('valorisation',TextType::class, array('required'=>false,'label'=>"Valorisation"))
            ;
          }




          $formModifier = function (FormInterface $form, Country $country = null) {

              $cities = null === $country ? array() : $country->getCities();


              $form->add('city',EntityType::class,
                array('label'=>'Ville (ou ville la plus proche présente dans la liste)',
                      'class'=>'FabienEventsEngineBundle:City',
                      'choice_label'=>'title',
                      "required"=>true,
                      'choices'=>$cities
                      )
                    );
          };


          $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $eventForm) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $eventForm->getData();

                $formModifier($eventForm->getForm(), $data->getCountry());

                //parties qui se modifient si on édite
                $object= $eventForm->getData();
                $form = $eventForm->getForm();

                /*
                if (!$object || null === $object->getId()) {
                  $form ->add("mail_creator",EmailType::class,array("label"=>"Votre adresse email","required"=>true,"attr"=>array("placeholder"=>"contact@tangoloso.com")))
                    ->add("password_creator",PasswordType::class,array("label"=>"Votre mot de passe","required"=>true))
                    ;
                }
                */

            }
          );


          $builder->get("country")->addEventListener(
           FormEvents::POST_SUBMIT,
           function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $country = $event->getForm()->getData();

                $form=$event->getForm();


                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($form->getParent(), $country);
            }
          );


    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fabien\EventsEngineBundle\Entity\Event',
            'userRole'=>null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fabien_eventsenginebundle_event';
    }


}
