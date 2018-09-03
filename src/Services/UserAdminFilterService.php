<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 03.09.18
 * Time: 22:15
 */

namespace App\Services;


use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class UserAdminFilterService
{
    private $userRepository;
    private $options;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->options = [];
    }

    public function loadDataFilter(Request $request)
    {
        $email = $request->query->get('email');

        if (strlen($email) > 0) {
            $this->options['email'] = $email;
        }

    }

    public function getQuery()
    {
        return $this->userRepository->findByFilterParametrs($this->options);
    }

    public function countItemsQuery()
    {
        return $this->userRepository->countItemsByFilterParametrs($this->options)[1];
    }
}