<?php

set_time_limit(40000);

$arquivos = array();
$dir      = __DIR__.'/image/';
$handle   = opendir( $dir);



while ($arquivo = readdir($handle)) {
    switch ($arquivo) {
        case '.':
        case '..':
        case '.htaccess':
        case 'cache':
        case 'index.php':
        case 'lib':
        case 'limpar.php':
        case 'source':
            # code...
            break;
        
        default:
            //echo $arquivo."\n";
            $arquivos[] = $arquivo;
            break;
    }
}

$quantArquivos = count($arquivos);




?><!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Limpando o Cache</title>
    
    <!-- csss -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- scripts -->
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <br>
        <div class="jumbotron">
            <h1>Limpando o Cache</h1>
        </div>
        <?php if ($quantArquivos == 0) : ?>

            <div class="alert alert-danger">Todos os arquivos ja foram excluidos</div>
    
        <?php else: ?>
        



            <div class="panel panel-default">
            <div class="panel-heading">Excluindo arquivos do cache</div>
                <div class="panel-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped ative" id="progresso"></div>
                    </div>
                    <div class="text-center"><span id="quant"><?php echo '0/'.$quantArquivos; ?></span> Files Deleted</div>
                    <script>
                        function mais(quant, porcento) {
                            document.getElementById('progresso').style.width = porcento;
                            document.getElementById('quant').innerHTML      = quant;
                        }


                    </script>
                    <?php 
                        $porcendoPorFile = 100 / $quantArquivos;
                        $porcendoPorAgora = 0;
                        $arquivosExcluidos = 0;

                        ob_flush();
                        flush();
                        foreach ($arquivos as $file) {
                            unlink($dir.$file);
                            
                            $arquivosExcluidos++;
                            $porcendoPorAgora = $porcendoPorAgora + $porcendoPorFile;

                            echo "<script>mais('$arquivosExcluidos/$quantArquivos', '".round($porcendoPorAgora, 0)."%');</script>";
                            ob_flush();
                            flush();
                            //usleep(100000);
                        }

                    ?>
                </div>
            </div>

        <?php endif; ?>
    </div>
    
    

</body>
<html>
