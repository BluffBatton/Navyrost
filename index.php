<?php
if(!class_exists('BasePage'))
{
    require_once 'View/pages.php';
}
$main = new MainPage(4);
$main->render();
