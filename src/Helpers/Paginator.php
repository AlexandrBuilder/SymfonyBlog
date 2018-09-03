<?php
/**
 * Created by PhpStorm.
 * User: akiselev
 * Date: 30.08.18
 * Time: 13:08
 */

namespace App\Helpers;


use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Paginator
{
    private $items;
    private $paginator;
    private $query;
    private $itemCount;
    private $url;
    private $maxItems;
    private $maxPages;
    private $currPage;
    private $router;
    private $queryParametrs;

    public function __construct(UrlGeneratorInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->queryParametrs = $requestStack->getCurrentRequest()->query->all();

        foreach ($requestStack->getCurrentRequest()->attributes->all() as $key=>$attribute)
        {
            if (!(preg_match('/^_.+$/', $key, $matches))) {
                $this->queryParametrs[$key] = $attribute;
            }
        }

        $this->url = $requestStack->getCurrentRequest()->attributes->get('_route');
        $currPage=$queryParametrs = $requestStack->getCurrentRequest()->query->get('page');
        $this->currPage = $currPage ? $currPage : 1;
    }

    public function paginate($query, $itemCount, $maxItems = 1, $maxPages = 5)
    {
        $this->query = $query;
        $this->itemCount = $itemCount;
        $this->maxItems = $maxItems;
        $this->maxPages = $maxPages;
        $this->paginateConstructor();
    }

    private function paginateConstructor()
    {
        if($this->itemCount) {
            $paginatorArray = $this->paginateBuilder();
            $this->paginator = '<nav aria-label="Page navigation example"> <ul class="pagination justify-content-center">';
            $this->paginator .= $this->setPrevPage();

            foreach ($paginatorArray as $paginatorItem) {
                $this->paginator .= $paginatorItem;
            }

            $this->paginator .= $this->setNextPage();
            $this->paginator .= '</ul> </nav>';
        } else {
            $this->paginator = '';
        }

        return $this->paginator;
    }

    private function paginateBuilder()
    {
        $this->query->setMaxResults($this->maxItems)
            ->setFirstResult(($this->currPage - 1) * $this->maxItems);
        $this->items = $this->query->getResult();

        $paginatorArray = [];
        $pageCount = ceil($this->itemCount / $this->maxItems);

        $ltlVar = $this->currPage - (ceil($this->maxPages/2) - 1);
        $lft = $ltlVar > 0 ? $ltlVar : 1;

        $rgtVar = $this->currPage + (ceil($this->maxPages/2) - 1) ;
        $rgt = $rgtVar < $pageCount ? $rgtVar : $pageCount;

        if($lft > 1) {
            $paginatorArray[] = $this->setTagPagonator(1);
        }

        if($lft > 2)
            $paginatorArray['leftPoints'] = '...';

        for($i = $lft; $i <= $rgt; $i++) {
            $paginatorArray[] = $this->setTagPagonator($i);
        }

        if($rgt < $pageCount - 1)
            $paginatorArray['rightPoints'] = '...';

        if($rgt < $pageCount)
            $paginatorArray[] = $this->setTagPagonator((int)$pageCount);

        return $paginatorArray;
    }

    public function setPrevPage()
    {
        $addClass = 'disabled';
        $url = '';

        if($this->currPage-1 >= 1) {
            $addClass = '';
            $url = $this->getPageUrl($this->currPage - 1);
        }

        return '<li class="page-item '.$addClass.'"> <a class="page-link" href="'. $url .'" aria-label="Previous"> <span aria-hidden="true">&laquo;</span> <span class="sr-only">Previous</span> </a> </li>';
    }
    public function setNextPage()
    {
        $addClass = 'disabled';
        $url = '';

        if( $this->currPage + 1 <= $this->itemCount) {
            $addClass = '';
            $url = $this->getPageUrl($this->currPage + 1);
        }

        return '<li class="page-item '.$addClass.'"> <a class="page-link" href="'. $url .'" aria-label="Next"> <span aria-hidden="true">&raquo;</span> <span class="sr-only">Next</span> </a> </li>';
    }

    public function setTagPagonator($num)
    {
        $addClass = $num == $this->currPage ? 'active' : '';

        return '<li class="page-item '.$addClass.'"><a class="page-link" href="'.$this->getPageUrl($num).'">'.$num.'</a></li>';
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getPaginator()
    {
        if ($this->maxItems < $this->maxPages) {
            return $this->paginator;
        } else {
            return '';
        }
    }

    public function getPageUrl($numPage) {
        $arrayPage = array_merge($this->queryParametrs, ['page' => $numPage]);
        return $this->router->generate($this->url, $arrayPage);
    }

}
