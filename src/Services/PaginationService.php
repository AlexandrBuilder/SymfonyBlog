<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 30.08.18
 * Time: 9:39
 */

namespace App\Services;


class PaginationService
{

    public function paginate($dql, $page = 1, $limit = 3)
    {
        $paginator = new Paginator($dql);
        $paginator->setUseOutputWalkers(false);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }
}