<?php

$content = file_get_contents('resources/views/frontend/tours/checkout.blade.php');
if (preg_match_all('/.{0,30}\{\s+\{.{0,30}/s', $content, $matches)) {
    print_r($matches[0]);
}
