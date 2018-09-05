<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 03.09.18
 * Time: 20:43
 */

namespace App\Services;

use App\Repository\PostRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

class PostAdminFilterService
{
    private $postRepository;
    private $options;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
        $this->options = [];
    }

    public function loadDataFilter(Request $request)
    {
        $email = $request->query->get('email');

        if (strlen($email) > 0) {
            $this->options['email'] = $email;
        }

        $status = $request->query->get('status');

        if (strlen($status) > 0) {
            $this->options['status'] = $status;
        }

        $dateFrom = DateTime::createFromFormat('Y-m-d', $request->query->get('date_from'));

        if ($dateFrom) {
            $this->options['date_from'] = $dateFrom;
        }

        $dateTo = DateTime::createFromFormat('Y-m-d', $request->query->get('date_to'));

        if ($dateTo) {
            $this->options['date_to'] = $dateTo;
        }
    }

    public function getQuery()
    {
        return $this->postRepository->findByFilterParametrs($this->options);
    }

    public function countItemsQuery()
    {
        return $this->postRepository->countItemsByFilterParametrs($this->options)[1];
    }
}
