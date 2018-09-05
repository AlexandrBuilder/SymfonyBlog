<?php
/**
 * Created by PhpStorm.
 * User: akiselev
 * Date: 29.08.18
 * Time: 15:22
 */

namespace App\Services;

use App\Repository\TagRepository;
use Doctrine\ORM\EntityManager;

class TagService
{
    private $repositoryTag;

    public function __construct(TagRepository $repositoryTag)
    {
        $this->repositoryTag = $repositoryTag;
    }

    public function getArrayTags()
    {
        $tags = $this->repositoryTag->findAll();
        $tagsArray = [];
        foreach ($tags as $tag) {
            $tagsArray[]=$tag->getArrayTag();
        }
        return $tagsArray;
    }
}
