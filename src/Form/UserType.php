<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name',TextType::class, ['label'=>'First Name (*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2']],)
            ->add('last_name',TextType::class, ['label'=>'Last Name (*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('email', EmailType::class, ['label'=>'Email (*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password(*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']],
                'second_options' => ['label' => 'Repeat Password(*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']],
            ])
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
