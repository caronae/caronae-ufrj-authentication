<?php

require '.env';
require __DIR__ . '/vendor/autoload.php';

use Caronae\CaronaeUFRJAgent;

phpCAS::client(CAS_VERSION_2_0, 'cas.ufrj.br', 443, '');
phpCAS::setNoCasServerValidation();
phpCAS::forceAuthentication();

$id_ufrj = phpCAS::getUser();
$app_token = null;
$error = null;
$agent = new CaronaeUFRJAgent();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $agent->createOrUpdateUserWithUfrjId($id_ufrj);
    } catch (Exception $exception) {
        $error = $exception->getMessage();
    }
}

?>
<html>
<head>
    <title>Obter Chave | Caronaê</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>

<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-2 header-line brown"></div>
        <div class="col-xs-2 header-line blue"></div>
        <div class="col-xs-2 header-line pink"></div>
        <div class="col-xs-2 header-line green"></div>
        <div class="col-xs-2 header-line orange"></div>
        <div class="col-xs-2 header-line red"></div>
    </div>
</div>

<div class="top-content">

    <div class="inner-bg">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 logo">
                    <img src="images/logo.png">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 form-box">
                    <?php if ($app_token) : ?>
                        <div class="form-top">
                            <div class="form-top-left">
                                    <h3>Você já tem uma chave Caronaê!</h3>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="form-bottom">
                        <?php if ($error): ?>
                            <div class="alert alert-danger error">
                                <div class="title">Ops! Algo deu errado. Por favor, tente novamente.</div>
                                <div class="message">Erro: <?= $error ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($app_token) : ?>
                            <p class="text-center">Sua chave de acesso ao Caronaê é:</p>
                            <h2 class="text-center token" data-clipboard-text="<?= $app_token ?>"><?= $app_token ?></h2>
                            <p class="text-center copy-text">Basta clicar para copiar a chave.</p>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-group">
                                <input type="hidden" name="user" id="user" value="<?= $id_ufrj ?>">
                                <input type="hidden" name="app_token" id="app_token" value="<?= $app_token ?>">

                                <?php if (!$app_token) : ?>
                                    <div class="terms-alert">
                                        <span class="icon glyphicon glyphicon-warning-sign"></span>
                                        <h2>Você já leu nossos termos e condições de uso?</h2>
                                        <p>
                                            Para obter sua chave de acesso, você deve ler e concordar com nossos termos e condições de uso.
                                        </p>

                                        <button type="submit" class="button btn btn-block btn-primary" onclick="return openTermsOfUse()">
                                            <span class="glyphicon glyphicon-list-alt"></span>
                                            <span>Ler termos de uso</span>
                                        </button>

                                        <button type="submit" class="button btn btn-block btn-success">
                                            <span class="glyphicon glyphicon-ok"></span>
                                            <span>Li e aceito os termos</span>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="js/clipboard.min.js"></script>
<script src="js/chave.js"></script>

</body>
</html>
