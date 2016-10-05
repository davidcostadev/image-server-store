<?php

/**
 * @author      David Santana Costa
 * @email       davidcosta@csthost.com.br
 * @url         davidcosta.com.br
 *
 * @copyright   Copyright (C) August 2014 - David Costa Inc. All rights reserved.
 * @license     MIT
 */


//Configurações padroes--------------------------------------------------------
define('SYS_IMG_WIDTHDEFAULT', 250);

define('SYS_IMG_PATH_CACHE', '/../');

define('SYS_IMG_PATH_SOURCE', __DIR__ .'/../source/');




// Função para imagem padrao, quando o Objeto não tiver imagem------------------
function imagemPadrao($width = null) {

    if ($width == null) {
        $width = SYS_IMG_WIDTHDEFAULT;
    }

    header('Content-Type: image/jpeg');

    $imagem = file_get_contents(SYS_IMG_PATH_SOURCE. 'image-vazio.jpg');
    die($imagem);

}

imagemPadrao();




