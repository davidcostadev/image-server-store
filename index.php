<?php

/**
 * @author      David Santana Costa
 * @email       davidcosta@csthost.com.br
 * @url         davidcosta.com.br
 *
 * @copyright   Copyright (C) August 2014 - CSTHost WEB Builder, Inc. All rights reserved.
 * @license     MIT
 */


//Configurações padroes--------------------------------------------------------
define('SYS_IMG_WIDTHDEFAULT', 250);

define('SYS_IMG_PATH_CACHE', '/../');

define('SYS_IMG_PATH_SOURCE', '/../source/');

define('SYS_IMG_SHOW_ERROR', false);

define('SYS_IMG_CACHE_NAVEGADOR', true);



// Função para imagem padrao, quando o Objeto não tiver imagem------------------
function imagemPadrao($width = null) {

    if ($width == null) {
        $width = SYS_IMG_WIDTHDEFAULT;
    }

    header('Content-Type: image/jpeg');

    $imagem = file_get_contents(__DIR__.'/source/'. 'image-vazio.jpg');
    die($imagem);

}

imagemPadrao();




