<?php

namespace App\Form;

use App\DTO\AuthorDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AuthorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', AuthorType::class, [
            'label' => false
        ])->add('books_ids', TextType::class, [
            'label' => 'Идентификаторы книг через запятую', // sorry
            'required' => false
        ])->add('save', SubmitType::class, options: [
            'label' => 'Сохранить'
        ]);

        $builder->get('books_ids')->addModelTransformer(new CallbackTransformer(
            function ($ids) {
                if (!$ids) {
                    return $ids;
                }
                return implode(',', $ids);
            },
            function ($ids) {
                if (!$ids) {
                    return $ids;
                }
                return explode(',', $ids);

            }
        ));
    }
}