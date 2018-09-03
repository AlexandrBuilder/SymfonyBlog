<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 03.09.18
 * Time: 15:09
 */

namespace App\Helpers;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RequestStack;

class FormHelper
{
    private $elements;
    private $elementsName;
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->elementsName = new ArrayCollection();
        $this->elements = new ArrayCollection();
        $this->requestStack = $requestStack;
    }

    public function addElementsName(string $elementName)
    {
        if (!$this->elementsName->contains($elementName)) {
            $this->elementsName[] = $elementName;
        }

        return $this;
    }

    public function getParamertsQuery()
    {
        $arrayResult = [];

        foreach ($this->elementsName as $elementName) {
            $getElement = $this->requestStack->getCurrentRequest()->query->get($elementName);
            if (isset($getElement)) {
                $arrayResult[$elementName] = $getElement;
            } else {
                $arrayResult[$elementName] = '';
            }
        }

        return $arrayResult;
    }
}