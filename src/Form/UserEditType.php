<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name',TextType::class, ['label'=>'First Name (*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2']])
            ->add('last_name',TextType::class, ['label'=>'Last Name (*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('email', EmailType::class, ['label'=>'Email (*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('user_roles', EntityType::class ,
                ['expanded'=>false,'multiple'=>true,'class' => Role::class,'choice_label' => function($object){
                    return $object->getDisplayName();
                }, 'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'ASC');
                },
                    'attr'=>['class'=>'form-select', 'data-control'=>'select2'] ,'label'=>'Roles (*):',
                    'label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']]
            )
            ->add('enabled',ChoiceType::class,[
                'attr'=>['class'=>'form-select','data-control'=>'select2'],
                'label'=>'Enabled?:',
                'label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5'],
                'choices'=>[
                    'Yes'=>true,
                    'No'=>false
                ],
                'expanded'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
