<?php

namespace App\Form;

use App\Entity\Agency;
use App\Entity\Agent;
use App\Entity\Ancillary;
use App\Entity\Carrier;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;

class AgencyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, ['label'=>'Name (*)','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
            ->add('description',TextType::class, ['label'=>'Description','label_attr'=>['class'=>'form-label fw-bolder text-dark fs-6 mb-2 mt-5']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Agency::class,
        ]);
    }
}
