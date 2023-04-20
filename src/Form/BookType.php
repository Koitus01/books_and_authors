<?php

namespace App\Form;

use App\DTO\AuthorDTO;
use App\Entity\Author;
use App\ValueObject\Publishing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    protected string $coverPath;

    public function __construct(string $coverPath)
    {
        $this->coverPath = $coverPath;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['placeholder' => 'Отверженные'],
                'label' => 'Название книги'
            ])
            ->add('publishing', IntegerType::class, [
                'attr' => ['placeholder' => '1862', 'min' => 1000, 'max' => date('Y')],
                'label' => 'Год издания'
            ])
            ->add('isbn', TextType::class, [
                'attr' => ['placeholder' => '978-1-56619-909-4 или 1-56619-909-3 или 1566199093'],
                'label' => 'ISBN'
            ])
            ->add('pages_count', IntegerType::class, [
                'required' => false,
                'label' => 'Количество страниц'
            ])
            ->add('cover', FileType::class, [
                'required' => false,
                'attr' => ['accept' => 'image/png, image/jpeg'],
                'label' => 'Обложка'
            ])
            ->add('add_author', ButtonType::class, [
                'attr' => ['class' => 'add_item_link'],
                'label' => 'Добавить автора'
            ])
            ->add('authors', CollectionType::class, [
                'entry_type' => AuthorType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'label' => false,
                'row_attr' => ['class' => 'authors', 'id' => 'authors'],
                'entry_options' => [
                    'attr' => ['placeholder' => 'ФИО'],
                    'label' => false
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить'
            ]);

        $builder->get('authors')->addModelTransformer(new CallbackTransformer(
            function ($authors) {
                return $authors;
            },
            function ($authors) {
                return array_map(function ($author) {
                    return new AuthorDTO(
                        $author['first_name'],
                        $author['second_name'],
                        $author['third_name']
                    );
                }, array_filter($authors));
            }
        ));

        $builder->get('publishing')->addModelTransformer(new CallbackTransformer(
            function ($publishingAsObject) {
                return $publishingAsObject;
            },
            function ($publishingAsInt) {
                if (!$publishingAsInt) {
                    return $publishingAsInt;
                }
                return Publishing::fromScalar($publishingAsInt);
            }
        ));
    }
}