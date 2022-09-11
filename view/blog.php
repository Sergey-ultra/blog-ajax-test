<?php
require_once '../helpers/helpers.php';

$layout = 'master';
$title = 'Гостевая книга';
?>

    <form id="create" class="form form__create" name="create">
        <input id="create-name" type="text" name="name" class="name input" placeholder="Введите свое имя">
        <textarea id="create-message" type="text" name="message" class="message textarea"
                  placeholder="Введите сообщение"></textarea>
        <button class="btn" type="submit">Запостить</button>
    </form>


<?php
viewTree($tree)
?>