<?php
/**
 * Created by PhpStorm.
 * User: akiselev
 * Date: 30.08.18
 * Time: 13:08
 */

namespace App\Helpers;

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

    public function __construct(UrlGeneratorInterface $router, $query, $itemCount, $url, $currPage = 1, $maxItems = 1, $maxPages = 5)
    {
        $this->query = $query;
        $this->itemCount = $itemCount;
        $this->url = $url;
        $this->currPage = $currPage;
        $this->maxItems = $maxItems;
        $this->maxPages = $maxPages;
        $this->router = $router;
        $this->paginateConstructor();
    }

    private function paginateConstructor()
    {
        if($this->itemCount) {
            $paginatorArray = $this->paginate();
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

    private function paginate()
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
            $url = $this->router->generate('homepage', ['page'=> $this->currPage-1]);
        }
        return '<li class="page-item '.$addClass.'"> <a class="page-link" href="'. $url .'" aria-label="Previous"> <span aria-hidden="true">&laquo;</span> <span class="sr-only">Previous</span> </a> </li>';
    }
    public function setNextPage()
    {
        $addClass = 'disabled';
        $url = '';
        if( $this->currPage + 1 <= $this->itemCount) {
            $addClass = '';
            $url = $this->router->generate('homepage', ['page'=> $this->currPage+1]);
        }
        return '<li class="page-item '.$addClass.'"> <a class="page-link" href="'. $url .'" aria-label="Next"> <span aria-hidden="true">&raquo;</span> <span class="sr-only">Next</span> </a> </li>';
    }

    public function setTagPagonator($num)
    {
        $addClass = $num == $this->currPage ? 'active' : '';
        return '<li class="page-item '.$addClass.'"><a class="page-link" href="'.$this->router->generate('homepage', ['page'=>$num]).'">'.$num.'</a></li>';
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getPaginator()
    {
        return $this->paginator;
    }

    public function __toString()
    {
        $result = '';
        $result .= "Страницы: ";
        foreach($this->paginator as $page => $value )
        {
            if(is_integer($value) and $value != $this->currPage)
            {
                if($value != 1)
                    $result .= "{$page}";
                else
                    $result .= "1";
            }
            elseif($value != $this->currPage)
                $result .= "{$value}";
            else
                $result .= "{$value}";
        }

        return $result;
    }

}
