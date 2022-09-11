<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Post;
use App\Service\TreeService;

class BlogController
{
    public function index()
    {
        $posts = (new Post())->index();
        $tree = (new TreeService())->buildTree($posts, 'reply_id');

        render('blog', compact('tree'));
    }



}