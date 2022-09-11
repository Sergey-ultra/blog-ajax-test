<?php

declare(strict_types=1);


namespace App\Service;


interface TreeInterface
{
    public  function buildTree(array &$items, string $field, $initField = null): array;
}