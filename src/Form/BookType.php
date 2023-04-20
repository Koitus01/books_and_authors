<?php

namespace App\Form;

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
                'attr' => ['placeholder' => 'Название книги']
            ])
            ->add('publishing', IntegerType::class, [
                'attr' => ['placeholder' => 'Год издания', 'min' => 0, 'max' => date('Y')]
            ])
            ->add('isbn', TextType::class, [
                'attr' => ['placeholder' => '978-1-56619-909-4 или 1-56619-909-3 или 1566199093']
            ])
            ->add('pages_count', IntegerType::class, [
                'required' => false
            ])
            ->add('cover', FileType::class, [
                'required' => false,
                'attr' => ['accept' => 'image/png, image/jpeg']
            ])
            ->add('add_author', ButtonType::class, [
                'attr' => ['class' => 'add_item_link']
            ])
            ->add('authors', CollectionType::class, [
                'entry_type' => AuthorType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'label' => 'Авторы. Создадутся, если их еще нет',
                'row_attr' => ['class' => 'authors', 'id' => 'authors'],
                'entry_options' => ['attr' => ['placeholder' => 'ФИО']]
            ])
            ->add('save', SubmitType::class);

        $builder->get('publishing')->addModelTransformer(new CallbackTransformer(
            function ($publishingAsObject) {
                if (!$publishingAsObject) {
                    return null;
                }
                return (int)$publishingAsObject->value()->format('Y');
            },
            function ($publishingAsInt) {
                return Publishing::fromScalar($publishingAsInt);
            }
        ));
        $builder->get('cover')->addModelTransformer(new CallbackTransformer(
            function ($file) {
                if (!$file) {
                    return null;
                }
                return new File($this->coverPath . $file);
            },
            function ($file) {
                return $file;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        #$resolver->setDefaults();
        /*        $resolver->setNormalizer('publishing', function(Options $options, $states) {
                    dd($options, $states);
                });*/
    }
}