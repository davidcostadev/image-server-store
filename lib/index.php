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

define('SYS_IMG_PATH_CACHE', dirname(__DIR__)  .'/'. $path);

define('SYS_IMG_PATH_SOURCE', dirname(__DIR__) . '/source/' );

define('SYS_IMG_SHOW_ERROR', true);

define('SYS_IMG_CACHE_NAVEGADOR', true);



// Função para fazer resize, criar o cache e exibir
function resize($fileSource, $fileCache, $width, $height = null) {

    require __DIR__.'/WideImage/WideImage.php';

    $widimage = WideImage::load($fileSource); 



    if($width === $height) {
        $widimage = $widimage->resize($width, $height);
    } else if($width > $height) {
        $widimage = $widimage->resize($width, $width);

        $widimage = $widimage->crop('center', 'center', $width, $height);

    } else {
        $widimage = $widimage->resize($height, $height);

        $widimage = $widimage->crop('center', 'center',  $width, $height);

    }





    

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

    if(file_exists(SYS_IMG_PATH_CACHE . 'image-vazio-'.$width.'.jpg')) {
        $imagem = file_get_contents(SYS_IMG_PATH_CACHE . 'image-vazio-'.$width.'.jpg');
        die($imagem);
    } else {
        resize(SYS_IMG_PATH_SOURCE. 'image-vazio.jpg', SYS_IMG_PATH_CACHE . 'image-vazio-'.$width.'.jpg', $width);
    }
}





if (empty($imgQuery)) {
    if (SYS_IMG_SHOW_ERROR){
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
        die('<h1>Requisicao invalida</h1>');
    } else {
        imagemPadrao();
    }
}





// Validação da extenção--------------------------------------------------------
$path_parts = pathinfo($imgQuery);


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

// $nomeEDados = explode('---', $path_parts['filename']);

// if (count($nomeEDados) == 2) {
//     $nome = $nomeEDados[0];
//     $dados = $nomeEDados[1];

// } elseif(count($nomeEDados) == 1) {
//     $nome = 'empty';
//     $dados = $nomeEDados[0];
// } else {
//     if (SYS_IMG_SHOW_ERROR) {
//         header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
//         die('<h1>Dados Invalido</h1>');
//     } else {
//         imagemPadrao();
//     }
// }




// Idendificando Valores--------------------------------------------------------
// $dadosArray = explode('-', $dados);


// if (count($dadosArray) == 3) {
//     $id    = $dadosArray[0];
//     $width = $dadosArray[1];

// } elseif(count($dadosArray) == 2) {
//     $id    = $dadosArray[0];
//     $width = $dadosArray[1];

// } elseif(count($dadosArray) == 1) {
//     $id    = $dadosArray[0];
//     $width = SYS_IMG_WIDTHDEFAULT;
    
// } else {
//     if (SYS_IMG_SHOW_ERROR) {
//         header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
//         die('<h1>Valores Invalido</h1>');
//     } else {
//         imagemPadrao();
//     }
// }


//pegando os tamanhos -------------------------------------------------------------------

$dimentions = explode('/', str_replace('.', '', $path_parts['dirname']));

$width  = isset($dimentions[0]) && is_numeric($dimentions[0]) ? $dimentions[0] : SYS_IMG_WIDTHDEFAULT;
$heigth = isset($dimentions[1]) && is_numeric($dimentions[1]) ? $dimentions[1] : $width;


$width  = $width > 2000 ? 2000 : $width;
$heigth = $heigth > 2000 ? 2000 : $heigth;


$pathDimentions = implode($dimentions, '/');




$pathWithDimentions = !empty($pathDimentions) ? $pathDimentions . '/' : '';


//Verificando se a imagem original existe------------------------------------------------

$filename = "{$path_parts['filename']}.{$extencao}";
 
$fileSource  = SYS_IMG_PATH_SOURCE . $path . $filename;
$fileCache   = SYS_IMG_PATH_CACHE . $pathWithDimentions . $filename;



$filename = preg_replace('/[^A-Za-z0-9 _ .-]/', '', $path_parts['filename']);

//$fileCache    = SYS_IMG_PATH_CACHE  . $filename. '.' . $path_parts['extension'];


// print_r([
//         'path' => $path,
//         'pathDimentions' => $pathDimentions,
//         'path_parts' => $path_parts,
//         'width'      => $width,
//         'heigth'     => $heigth,
//         'filename'   => $filename,
//         'fileSource' => $fileSource,
//         'fileCache'  => $fileCache

// ]);


if (!file_exists($fileSource) && @getimagesize($fileSource) === false) {
    if (SYS_IMG_SHOW_ERROR) {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
        die('<h1>1 Imagem nao encontrada</h1>');
    } else {
        imagemPadrao($width);
    }
}





// Valores padroes -------------------------------------------------------------
header('Content-Type:image/jpeg');

if (SYS_IMG_CACHE_NAVEGADOR) {
    header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 7776000));
    header('Accept-Ranges: bytes');
}




// Verificado se o cache foi criado---------------------------------------------
if(file_exists($fileCache)) {
    if (SYS_IMG_CACHE_NAVEGADOR) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($fileCache)));
        header('Content-Length: ' . filesize($fileCache));
    }
    //header('Gerado: true');
    
    $imagem = file_get_contents($fileCache);
    die($imagem);

} else {


    // var_dump($dimentions);

    if(count($dimentions) < 3 && count($dimentions) >= 0) {

 
       
        if(count($dimentions) === 1 && is_dir(SYS_IMG_PATH_CACHE . $dimentions[0]) !== true) {
            mkdir(SYS_IMG_PATH_CACHE . $dimentions[0]);
        }
        if(count($dimentions) === 2 && is_dir(SYS_IMG_PATH_CACHE . $dimentions[0]. '/' . $dimentions[1]) !== true) {
            if(is_dir(SYS_IMG_PATH_CACHE . $dimentions[0]) !== true) {
                mkdir(SYS_IMG_PATH_CACHE . $dimentions[0]);
            }


            mkdir(SYS_IMG_PATH_CACHE . $dimentions[0]. '/' . $dimentions[1]);
        }



        resize($fileSource, $fileCache, $width, $heigth);
        

    } else {
        if (SYS_IMG_SHOW_ERROR) {
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
            die('<h1>2 Imagem nao encontrada</h1>');
        } else {
            imagemPadrao($width);
        }
    }

}

