<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FilterAdminType;
use App\Helpers\Paginator;
use App\Repository\PostRepository;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $paginator;

    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(Request $request, PostRepository $postRepository)
    {


        $paginator = $this
            ->paginator
            ->paginate(
                $postRepository->findAllQuery(),
                $postRepository->countAll()[1]
            );

        return $this->render('admin/show.html.twig', [
            'posts' => $this->paginator->getItems(),
            'paginator' => $this->paginator->getPaginator(),
            'user_statuses' => User::getUserStatuses()
        ]);
    }
}
