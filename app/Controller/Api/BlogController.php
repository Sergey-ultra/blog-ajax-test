<?php

declare(strict_types=1);

namespace App\Controller\Api;


use App\Model\Post;

header('Content-type: application/json; charset=UTF-8');

class BlogController
{
    public function sendPost()
    {
        try {
            $request =   json_decode(file_get_contents('php://input'), true);
            $name = trim($request['name']);
            $message = trim($request['message']);
            $replyId = $request['reply_id'];


            if (!strlen($name) || !strlen($message)) {
                http_response_code(422);
                $errors = [];
                if (!strlen($name)) {
                    $errors['name'] = ['Поле имя пустое'];
                }
                if (!strlen($message)) {
                    $errors['message'] = ['Поле сообщение пустое'];
                }

                echo json_encode(['errors' => $errors]);
                die;
            }

            $id = (new Post())->create([
                'name' => $name,
                'message' => $message,
                'reply_id' => $replyId
            ]);
            $newPost = (new Post())->byId($id);

            http_response_code(201);
            echo json_encode([
                    'data' => [
                        'message' =>  'Сообщение сохранено',
                        'post' => $newPost
                    ]

                ]);

        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'data' =>
                [
                    'error' =>  $e->getMessage()
                ]
            ]);
        }
    }
}