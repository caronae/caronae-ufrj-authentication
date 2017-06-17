<?php

require '../.env';
require __DIR__ . '/../vendor/autoload.php';

session_start();

phpCAS::client(CAS_VERSION_2_0, 'cas.ufrj.br', 443, '');
phpCAS::setNoCasServerValidation();
phpCAS::forceAuthentication();

$user_id = phpCAS::getUser();
$token = strtoupper(substr(base_convert(sha1(uniqid() . rand()), 16, 36), 0, 6));
$db = new PDO('pgsql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';', DB_USER, DB_PASSWORD);
$db_query = $db->query("SELECT token FROM users WHERE id_ufrj = '$user_id' ");
$app_token = ($db_query->rowCount()) ? $db_query->fetchColumn() : null;
$error = null;

if (@$_POST['token'] && @$_SESSION['token'] == $_POST['token']) {
    if (!isset($app_token)) {
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $resposta = json_decode(file_get_contents(API_URL . "/user/signup/intranet/$user_id/$token", false, $context), true);
        $error = $resposta['error'] ?: '';
    } else {
        $_POST['cmd'] == 'Gerar' or $token = '';
        $error = $db->exec("UPDATE users SET token = '$token' WHERE id_ufrj = '$user_id'") ? '' : 'Não foi possível atualizar a chave do usuário.';
        if (!$error) {
            header('Location: /chave');
        }
    }
}

$_SESSION['token'] = $token;

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
                                <input type="hidden" name="token" value="<?= $token ?>">
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
