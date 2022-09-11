<?php

declare(strict_types=1);

namespace App\Service;

class TreeService implements TreeInterface
{
    public  function buildTree(array &$items, string $field, $initField = null): array
    {
        $tree = [];

        foreach ($items as $key => $element) {
            if ($element[$field] === $initField) {
                $children = self::buildTree($items, $field, $element['id']);
                if (count($children)) {
                    $element['children'] = $children;
                }
                $tree[] = $element;
                unset($items[$key]);
            }
        }
        return $tree;
    }

}