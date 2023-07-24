<?php

namespace App\Controller\Admin;

use App\Repository\TagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN_TAG")
 */
class TagsController extends AbstractController
{
    const DEFAULT_PAGE_SIZE = 20;
    const PAGE_SIZES = [10, 20, 50];

    /**
     * @param Request $request
     * @param TagRepository $tagRepository
     * @param PaginatorInterface $paginator
     * @return Response
     *
     * @Route("/admin/tags", name="app_admin_tags")
     */
    public function index(
        Request $request,
        TagRepository $tagRepository,
        PaginatorInterface $paginator
    ): Response
    {
        $q = trim($request->get('q'));
        $pageSize = (int)$request->get('count');
        $withSoftDeletes = $request->query->has('showDeleted');

        $pagination = $paginator->paginate(
            $tagRepository->findAllWithSearchQuery($q, $withSoftDeletes),
            $request->query->getInt('page', 1),
            $pageSize ?: self::DEFAULT_PAGE_SIZE
        );

        return $this->render('admin/tags/index.html.twig', [
                'pagination' => $pagination,
            ]
        );

    }
}
