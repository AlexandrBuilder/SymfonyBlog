<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\User;
use App\Form\FilterAdminType;
use App\Form\PostAdminType;
use App\Helpers\FormHelper;
use App\Helpers\Paginator;
use App\Repository\UserRepository;
use App\Security\Voter\PostVoter;
use App\Services\UserAdminFilterService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $paginator;
    private $formHelper;
    private $userAdminFilterService;
    private $userService;

    public function __construct(
        Paginator $paginator,
        FormHelper $formHelper,
        UserAdminFilterService $userAdminFilterService,
        UserService $userService
    ) {
        $this->paginator = $paginator;
        $this->formHelper = $formHelper;
        $this->userAdminFilterService = $userAdminFilterService;
        $this->userService = $userService;
    }

    /**
     * @Route("/admin/user", name="admin_user")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->formHelper
            ->addElementsName('email');

        $this->userAdminFilterService->loadDataFilter($request);

        $paginator = $this
            ->paginator
            ->paginate(
                $this->userAdminFilterService->getQuery(),
                $this->userAdminFilterService->countItemsQuery()
            );

        return $this->render('admin/user/index.html.twig', [
            'users' => $this->paginator->getItems(),
            'paginator' => $this->paginator->getPaginator(),
            'old_fields' => $this->formHelper->getParamertsQuery()
        ]);
    }

    /**
     * @Route("/admin/user/activate/{id}", name="admin_user_activate")
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function activateAction(Request $request, User $user)
    {

        $this->userService->setActiveStatusUser($user);

        $this->addFlash(
            'success_user',
            'You activate user "'.$user->getEmail().'"'
        );

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/admin/user/block/{id}", name="admin_user_block")
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function blockAction(Request $request, User $user)
    {
        $this->userService->setBlockStatusUser($user);

        $this->addFlash(
            'success_user',
            'You blocked user "'.$user->getEmail().'"'
        );

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
