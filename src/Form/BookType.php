<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['placeholder' => 'Название книги']
            ])
            ->add('publishing', IntegerType::class, [
                'attr' => ['placeholder' => 'Год издания','min' => 0, 'max' => date('Y')]
            ])
            ->add('isbn', TextType::class, [
                'attr' => ['placeholder' => '978-1-56619-909-4 или 1-56619-909-3 или 1566199093']
            ])
            ->add('pages_count', IntegerType::class, [
                'required' => false
            ])
            ->add('add_author', ButtonType::class, [
                'attr' => ['class' => 'add_item_link', '']
            ])
            ->add('authors', CollectionType::class, [
                'entry_type' => AuthorType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'label' => 'Авторы',
                'row_attr' => ['class' => 'authors', 'id' => 'authors'],
                'entry_options' => ['attr' => ['placeholder' => 'ФИО']]
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}