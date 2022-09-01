<?php

namespace App\Form;

use App\Entity\Agent;
use App\Entity\Carrier;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;

class CarrierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('name',TextType::class, ['label'=>'Name (*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('description',TextType::class, ['label'=>'Description','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('agents', EntityType::class, array(
                'class'     => Agent::class,
                'expanded'  => false,
                'multiple'  => true,
                'label'=>'Agents',
                'label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5'],
                'attr'=>['class'=>'form-select', 'data-control'=>'select2'] ,
            ))
            ->add('logo_url', FileType::class, [
                'label' => 'Logo',
                'label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5'],
                'attr'=>['class'=>'form-control'],

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Carrier::class,
        ]);
    }
}
