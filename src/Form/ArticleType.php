<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\User;
use App\Repository\CategorieRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre de l\'article'
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => true,
                'by_reference' => false,
                'autocomplete' => true,
                'query_builder' => function (CategorieRepository $repo): QueryBuilder {
                    return $repo->createQueryBuilder('c')
                        ->andWhere('c.enable = :enable')
                        ->setParameter('enable', true)
                        ->orderBy('c.name', 'ASC');
                },
            ]);

        if ($options['isEdit']) {
            $builder
                ->add('user', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'fullName',
                    'expanded' => false,
                    'multiple' => false,
                ]);
        }

        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Contenu de l\'article',
                    'rows' => 10,
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer l\'image',
                'download_uri' => false,
                'asset_helper' => false,
                'image_uri' => true,
            ])
            ->add('enable', CheckboxType::class, [
                'label' => 'Activer',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'sanitize_html' => true,
            'isEdit' => false,
        ]);
    }
}
