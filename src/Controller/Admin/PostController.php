<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\Admin\PostAdminType;
use App\Helpers\FormHelper;
use App\Helpers\Paginator;
use App\Repository\PostRepository;
use App\Security\Voter\PostVoter;
use App\Services\PostAdminFilterService;
use App\Services\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private $paginator;
    private $formHelper;
    private $adminFilterService;

    public function __construct(
        Paginator $paginator,
        FormHelper $formHelper,
        PostAdminFilterService $adminFilterService
    ) {
        $this->paginator = $paginator;
        $this->formHelper = $formHelper;
        $this->adminFilterService = $adminFilterService;
    }

    /**
     * @Route("/admin", name="admin_post_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->formHelper
            ->addElementsName('email')
            ->addElementsName('status')
            ->addElementsName('date_from')
            ->addElementsName('date_to');

        $this->adminFilterService->loadDataFilter($request);

        $paginator = $this
            ->paginator
            ->paginate(
                $this->adminFilterService->getQuery(),
                $this->adminFilterService->countItemsQuery()
            );

        return $this->render('admin/post/index.html.twig', [
            'posts' => $this->paginator->getItems(),
            'paginator' => $this->paginator->getPaginator(),
            'post_statuses' => Post::getPostStatus(),
            'old_fields' => $this->formHelper->getParamertsQuery()
        ]);
    }

    /**
     * @Route("/admin/post/{id}/edit", name="admin_post_edit")
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function edit(Request $request, Post $post): Response
    {
        $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

        $form = $this->createForm(PostAdminType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()
                ->getManager()
                ->flush();

            $this->addFlash(
                'success_post',
                'Post status changed successfully'
            );

            return $this->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('admin/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
}
