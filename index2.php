<?php

require '.env';
require __DIR__ . '/vendor/autoload.php';

use Caronae\CaronaeService;
use Caronae\CaronaeSigaAdaptor;
use Caronae\SigaService;

phpCAS::client(CAS_VERSION_2_0, 'cas.ufrj.br', 443, '');
phpCAS::setNoCasServerValidation();
phpCAS::forceAuthentication();

$siga = new SigaService;
$caronae = new CaronaeService(CARONAE_API_URL);
$caronae->setInstitution(CARONAE_INSTITUTION_ID, CARONAE_INSTITUTION_PASSWORD);
$adaptor = new CaronaeSigaAdaptor;

$app_token = null;
$error = null;

try {
    $user_id = phpCAS::getUser();
    $siga_user = $siga->getProfileById($user_id);
    $user = $adaptor->convertToCaronaeUser($siga_user);

    $user = $caronae->signUp($user);
    $app_token = $user->token;
} catch (Exception $exception) {
    $error = $exception->getMessage();
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
    <link rel="stylesheet" type="text/css" href="css/sweetalert.css">
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
                <div class="col-sm-8 col-sm-offset-2 text">
                    <img src="images/logo.png">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 form-box">
                    <div class="form-top">
                        <div class="form-top-left">
                            <?php if ($app_token) : ?>
                                <h3>Você já tem uma chave Caronaê!</h3>
                                <p>Para criar uma nova basta clicar em "Nova Chave".</p>
                            <?php else : ?>
                                <h3>Obtenha a sua chave do Caronaê!</h3>
                                <p>Basta clicar em "Gerar Chave":</p>
                            <?php endif; ?>
                        </div>
                    </div>
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
                                <input type="hidden" name="user" id="user" value="<?= $user_id ?>">
                                <input type="hidden" name="app_token" id="app_token" value="<?= $app_token ?>">

                                <button type="submit" class="button btn btn-block btn-success" name="cmd" value="Gerar">
                                    <?php if ($app_token) : ?>
                                        <span class="glyphicon glyphicon-refresh"></span>
                                        <span>Nova chave</span>
                                    <?php else : ?>
                                        <span class="glyphicon glyphicon-certificate"></span>
                                        <span>Gerar chave</span>
                                    <?php endif; ?>
                                </button>

                                <?php if ($app_token) : ?>
                                    <button type="submit" class="button btn btn-block remove" name="cmd" value="Invalidar">
                                        Remover chave
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="js/sweetalert.min.js"></script>
<script src="js/clipboard.min.js"></script>
<script src="js/chave.js"></script>

<?php if (!$app_token && !$error) : ?>
    <script>
        displayTermsAlert();
    </script>
<?php endif; ?>

</body>
</html>
