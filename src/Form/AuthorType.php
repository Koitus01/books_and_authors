<?php

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, ['attr' => ['placeholder' => 'Имя']])
            ->add('second_name', TextType::class, ['attr' => ['placeholder' => 'Фамилия']])
            ->add('third_name', TextType::class, ['attr' => ['placeholder' => 'Отчество']]);
    }
}