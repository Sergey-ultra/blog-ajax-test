<?php

if (!isset($_SESSION)) {
    session_start();
}

function flash($name, $message)
{
    if (!empty($name) && !empty($message)) {
        $_SESSION[$name] = $message;
    }
}

function message($name, $class)
{
    if (isset($_SESSION[$name])) {
        echo '<div class="' . $class . '" ><p>' . $_SESSION[$name] . '</p></div>';
        unset($_SESSION[$name]);
    }
}

function redirect($location)
{
    header("location: " . $location);
    exit();
}

function render($path, $args = [])
{
    foreach ($args as $key => $arg){
        $$key = $arg;
    }


    ob_start();
    require __DIR__ . "/../view/" . $path . ".php";
    $content = ob_get_clean();
    require "../view/layout/" . $layout . ".php";

}

function viewTree(array $tree): void
{
    echo '<div class="posts">';
    foreach ($tree as $key => $item) {

        echo "<div class='post'>
                <div class='post__title'>
                    <span class='post__name'>{$item['name']}</span>
                    <span class='post__date'>{$item['created_at']}</span>
                </div>
                <div class='post__message'>{$item['message']}</div>
                <div id='{$item['id']}' class='post__reply'>
                        <span>+</span>
                        <span class='post__reply-dashed'>Ответить</span>
                </div>
                ";
        echo "<div class='post__nested'>";
        if (isset($item['children'])) {
                viewTree($item['children']);
        }
        echo "</div>";



        echo "</div>";

    }
    echo '</div>';
}
