<?php

/**
 * @author      David Santana Costa
 * @email       davidcosta@csthost.com.br
 * @url         davidcosta.com.br
 *
 * @copyright   Copyright (C) August 2014 - David Costa Inc. All rights reserved.
 * @license     MIT
 */


ini_set('memory_limit','256M');

//Configurações padroes--------------------------------------------------------
define('SYS_IMG_WIDTHDEFAULT', 250);

define('SYS_IMG_PATH_CACHE', '/../image/');

define('SYS_IMG_PATH_SOURCE', '/../source/');

define('SYS_IMG_SHOW_ERROR', true);

define('SYS_IMG_CACHE_NAVEGADOR', true);



// Função para fazer resize, criar o cache e exibir
function resize($fileOriginal, $fileCache, $width) {

    require __DIR__.'/WideImage/WideImage.php';

    $widimage = WideImage::load($fileOriginal); 
    $widimage = $widimage->resizeDown($width, $width, 'fill', 'down');

    $widimage->saveToFile($fileCache, 85);
    echo $widimage->output('jpg', 85);

    die();


    


    if (SYS_IMG_CACHE_NAVEGADOR) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($fileCache)));
        header('Content-Length: ' . filesize($fileCache));
    }
}


// Função para imagem padrao, quando o Objeto não tiver imagem------------------
function imagemPadrao($width = null) {

    if ($width == null) {
        $width = SYS_IMG_WIDTHDEFAULT;
    }

    header('Content-Type: image/jpeg');

    if(file_exists(__DIR__.SYS_IMG_PATH_CACHE . 'image-vazio-'.$width.'.jpg')) {
        $imagem = file_get_contents(__DIR__.SYS_IMG_PATH_CACHE . 'image-vazio-'.$width.'.jpg');
        die($imagem);
    } else {
        resize(__DIR__.SYS_IMG_PATH_SOURCE. 'image-vazio.jpg', __DIR__.SYS_IMG_PATH_CACHE . 'image-vazio-'.$width.'.jpg', $width);
    }
}



// Validação da requisição------------------------------------------------------
$img = !empty($_GET['img']) ? $_GET['img'] : null;

if (empty($img)) {
    if (SYS_IMG_SHOW_ERROR){
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
        die('<h1>Requisicao invalida</h1>');
    } else {
        imagemPadrao();
    }
}





// Validação da extenção--------------------------------------------------------
$path_parts = pathinfo($img);

$extencoesPermitidas = array(
    'jpg', 'png', 'gif', 'bmp', 'jpeg'
);

if (isset($path_parts['extension']) && in_array(strtolower($path_parts['extension']), $extencoesPermitidas)) {
    $extencao = $path_parts['extension'];
} else {
    if (SYS_IMG_SHOW_ERROR) {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
        die('<h1>Extension not allowed</h1>'); 
    } else {
        imagemPadrao();
    }
}



//Pegando os dados da imagem----------------------------------------------------

$nomeEDados = explode('---', $path_parts['filename']);

if (count($nomeEDados) == 2) {
    $nome = $nomeEDados[0];
    $dados = $nomeEDados[1];

} elseif(count($nomeEDados) == 1) {
    $nome = 'empty';
    $dados = $nomeEDados[0];
} else {
    if (SYS_IMG_SHOW_ERROR) {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
        die('<h1>Dados Invalido</h1>');
    } else {
        imagemPadrao();
    }
}




// Idendificando Valores--------------------------------------------------------
$dadosArray = explode('-', $dados);


if (count($dadosArray) == 3) {
    $id    = $dadosArray[0];
    $width = $dadosArray[1];
    $child = $dadosArray[2];

} elseif(count($dadosArray) == 2) {
    $id    = $dadosArray[0];
    $width = $dadosArray[1];
    $child = 1;

} elseif(count($dadosArray) == 1) {
    $id    = $dadosArray[0];
    $width = SYS_IMG_WIDTHDEFAULT;
    $child = 1;
    
} else {
    if (SYS_IMG_SHOW_ERROR) {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
        die('<h1>Valores Invalido</h1>');
    } else {
        imagemPadrao();
    }
}




//Verificando se a imagem original existe------------------------------------------------

$nomeArquivoOriginal = "{$id}-{$child}.{$extencao}";
$nomeArquivoCache    = "{$id}-{$width}-{$child}.{$extencao}";
 
$fileOriginal      = __DIR__.SYS_IMG_PATH_SOURCE . $nomeArquivoOriginal;
$fileCacheFiltrado = __DIR__.SYS_IMG_PATH_CACHE. $nomeArquivoCache;


$filename = preg_replace('/[^A-Za-z0-9 _ .-]/', '', $path_parts['filename']);

$fileCache    = __DIR__.SYS_IMG_PATH_CACHE  . $filename. '.' . $path_parts['extension'];




if (!file_exists($fileOriginal) && @getimagesize($fileOriginal) === false) {
    if (SYS_IMG_SHOW_ERROR) {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
        die('<h1>Imagem nao encontrada</h1>');
    } else {
        imagemPadrao($width);
    }
}





// Valores padroes -------------------------------------------------------------
header('Content-Type: image/jpeg');

if (SYS_IMG_CACHE_NAVEGADOR) {
    header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 7776000));
    header('Accept-Ranges: bytes');
}




// Verificado se o cache foi criado---------------------------------------------
if(file_exists($fileCacheFiltrado)) {
    if (SYS_IMG_CACHE_NAVEGADOR) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($fileCacheFiltrado)));
        header('Content-Length: ' . filesize($fileCacheFiltrado));
    }
    //header('Gerado: true');
    
    $imagem = file_get_contents($fileCacheFiltrado);
    die($imagem);

} else {


    if($filename === $path_parts['filename']) {
        resize($fileOriginal, $fileCache, $width);
    } else {
        resize($fileOriginal, $fileCacheFiltrado, $width);
    }

}

