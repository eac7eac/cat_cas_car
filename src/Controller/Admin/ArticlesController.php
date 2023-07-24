<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleFormType;
use App\Homework\ArticleContentProvider;
use App\Homework\ArticleWordsFilter;
use App\Repository\ArticleRepository;
use App\Service\ArticleService;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User|null getUser()
 */
class ArticlesController extends AbstractController
{
    const DEFAULT_PAGE_SIZE = 20;

    private $wordsArticleContentProvider = [
        'ПРИВЕТ!',
        'balalaika',
        'вариативность',
        'коммуникабельность',
        'John Smith',
        'bananas',
        'Excepteur',
        'symfony',
        'qwerty@mail.com',
        'Акрон'
    ];

    /**
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     * @Route("/admin/articles", name="app_admin_articles")
     */
    public function index(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator)
    {
        $pagination = $paginator->paginate(
            $articleRepository->findAllWithSearchQuery($request->query->get('q')),
            $request->query->getInt('page', 1),
            $request->query->get('count') ?: self::DEFAULT_PAGE_SIZE
        );

        return $this->render('admin/articles/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/admin/articles/create", name="app_admin_articles_create")
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     */
    public function create(
        EntityManagerInterface $em,
        ArticleContentProvider $articleContentProvider,
        Request $request,
        ArticleWordsFilter $filter,
        FileUploader $articleFileUploader,
        ArticleService $articleService
    ) {
        $form = $this->createForm(ArticleFormType::class, new Article());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();

            if (rand(1, 3) != 1) {
                $article
                    ->setBody($article->getBody() . '<br>' . $articleContentProvider->get(rand(1, 3), $this->wordsArticleContentProvider[random_int(0, 9)], rand(5, 10)));
            } else {
                $article
                    ->setBody($article->getBody() . '<br>' . $articleContentProvider->get(rand(1, 3)));
            }

            /** @var UploadedFile|null $image */
            $image = $form->get('image')->getData();

            if ($image) {
                $article->setImageFilename($articleFileUploader->uploadFile($image, $article->getImageFilename()));
            }

            $article
                ->setTitle($filter->filter($article->getTitle(), ['стакан', 'Наташ', 'dolor']))
                ->setBody($filter->filter($article->getBody(), ['стакан', 'Наташ', 'dolor']))
            ;

            $em->persist($article);
            $em->flush();

            $articleService->articleCreate($article);

            $this->addFlash('flash_message', 'Статья успешно создана');

            return $this->redirectToRoute('app_admin_articles');
        }

        return $this->render('admin/articles/create.html.twig', [
            'articleForm' => $form->createView(),
            'showError' => $form->isSubmitted(),
        ]);
    }

    /**
     * @Route("/admin/articles/{id}/edit", name="app_admin_article_edit")
     * @IsGranted("MANAGE", subject="article")
     */
    public function edit(Article $article, EntityManagerInterface $em, Request $request, FileUploader $articleFileUploader)
    {
        $form = $this->createForm(ArticleFormType::class, $article, [
            'enable_published_at' => true,
        ]);

        if ($article = $this->handleFormRequest($form, $em, $request, $articleFileUploader)) {
            $this->addFlash('flash_message', 'Статья успешно изменена');

            return $this->redirectToRoute('app_admin_article_edit', ['id' => $article->getId()]);
        }

        return $this->render('admin/articles/edit.html.twig', [
            'articleForm' => $form->createView(),
            'showError' => $form->isSubmitted(),
        ]);
    }

    private function handleFormRequest(FormInterface $form, EntityManagerInterface $em, Request $request, FileUploader $articleFileUploader)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();

            /** @var UploadedFile|null $image */
            $image = $form->get('image')->getData();

            if ($image) {
                $article->setImageFilename($articleFileUploader->uploadFile($image, $article->getImageFilename()));
            }

            $em->persist($article);
            $em->flush();

            return $article;
        }

        return null;
    }
}
