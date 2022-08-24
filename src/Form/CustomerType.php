<?php

namespace App\Form;

use App\Entity\Agency;
use App\Entity\Agent;
use App\Entity\Customer;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\UsState;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name',TextType::class, ['label'=>'First Name (*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2']],)
            ->add('last_name',TextType::class, ['label'=>'Last Name (*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('email', EmailType::class, ['label'=>'Email (*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('birth_date', TextType::class, ['label'=>'Birth Date',
                'attr' => ['placeholder' => 'dd/mm/yyyy','data-mask'=>'99/99/9999'],'label_attr'=>['class'=>'col-form-label']])
            ->add('phone',TextType::class, ['label'=>'Phone','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('mobile',TextType::class, ['label'=>'Mobil','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('social',TextType::class, ['label'=>'Social','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('street',TextType::class, ['label'=>'Street','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('city',TextType::class, ['label'=>'City','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('county',TextType::class, ['label'=>'County','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('zip_code',TextType::class, ['label'=>'Zip Code','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('annual_income',TextType::class, ['label'=>'Annual Income','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])

            ->add('agent', EntityType::class ,
                ['expanded'=>false,'multiple'=>false,'class' => Agent::class,'choice_label' => function($object){
                    return $object->getDisplayName();
                }, 'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'ASC');
                },
                    'attr'=>['class'=>'form-select', 'data-control'=>'select2'] ,'label'=>'Agent',
                    'label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']]
            )

            ->add('state', EntityType::class ,
                ['expanded'=>false,'multiple'=>false,'class' => UsState::class,'choice_label' => function($object){
                    return $object->getName();
                }, 'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'ASC');
                },
                    'attr'=>['class'=>'form-select', 'data-control'=>'select2'] ,'label'=>'State',
                    'label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']]
            )

            ->add('birth_state', EntityType::class ,
                ['expanded'=>false,'multiple'=>false,'class' => UsState::class,'choice_label' => function($object){
                    return $object->getName();
                }, 'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'ASC');
                },
                    'attr'=>['class'=>'form-select', 'data-control'=>'select2'] ,'label'=>'Birth State',
                    'label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']]
            )
            //TODO relation fields
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
