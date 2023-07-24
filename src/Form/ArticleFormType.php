<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class ArticleFormType extends AbstractType
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Article|null $article */
        $article = $options['data'] ?? null;

        $cannotEditAuthor = $article && $article->getId() && $article->isPublished();

        $imageConstrains = [
            new Image([
                'maxSize' => '2M',
                'minHeight' => '300',
                'minHeightMessage' => 'Минимальная высота изображения должна быть 300рх',
                'minWidth' => '480',
                'minWidthMessage' => 'Минимальная ширина изображения должна быть 480рх',
                'allowSquare' => false,
                'allowSquareMessage' => 'Изображение не может быть квадратным',
                'allowPortrait' => false,
                'allowPortraitMessage' => 'Изображение должно быть горизонтальным',
            ]),
        ];

        if (! $article || ! $article->getImageFilename()) {
            $imageConstrains[] = new NotNull([
                'message' => 'Не выбрано изображение статьи',
            ]);
        }

        $builder
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => $imageConstrains,
            ])
            ->add('title', TextType::class, [
                'label' => 'Название статьи',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Описание статьи',
                'rows' => 3,
            ])
            ->add('body', TextareaType::class, [
                'label'=> 'Содержимое статьи',
                'rows' => 10,
            ])
            ->add('keywords', TextType::class, [
                'label' => 'Ключевые слова статьи',
                'required' => false,
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choices' => $this->userRepository->findAllSortedByName(),
                'choice_label' => function (User $user) {
                    return sprintf('%s (id: %d)', $user->getFirstName(), $user->getId());
                },
                'label' => 'Автор статьи',
                'placeholder' => 'Выберите автора статьи',
                'invalid_message' => 'Такого автора не существует',
                'disabled' => $cannotEditAuthor,
            ])
        ;

        if ($options['enable_published_at']) {
            $builder
                ->add('publishedAt', null, [
                    'label' => 'Дата публикации статьи',
                    'widget' => 'single_text',
                ])
            ;
        }

        $builder->get('body')
            ->addModelTransformer(new CallbackTransformer(
                function ($bodyFromDatabase) {
                    return str_replace('**собака**', 'собака', $bodyFromDatabase);
                },
                function ($bodyFromInput) {
                    return str_replace('собака', '**собака**', $bodyFromInput);
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'enable_published_at' => false,
        ]);
    }
}
