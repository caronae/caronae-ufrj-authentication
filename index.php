<?php
require '.env';
require __DIR__ . '/vendor/autoload.php';
phpCAS::client(CAS_VERSION_2_0, 'cas.ufrj.br', 443, '');
phpCAS::setNoCasServerValidation();
phpCAS::forceAuthentication();
$usuario = phpCAS::getUser();
$token = strtoupper(substr(base_convert(sha1(uniqid() . rand()), 16, 36), 0, 6));
$db = new PDO('pgsql:dbname=caronae;host='.DB_HOST.';', DB_USER, DB_PASSWORD);
$consulta = $db->query("SELECT token FROM users WHERE id_ufrj = '$usuario' ");
$app_token = ($consulta->rowCount())? $consulta->fetchColumn() : NULL;
if ($_POST['token'] && $_SESSION['token'] == $_POST['token']) {
  if (!isset($app_token)) {
      $resposta = json_decode(file_get_contents("http://caronae.tic.ufrj.br/user/signup/intranet/$usuario/$token"),true );
      header('Location: /token'. (!$resposta || $resposta['erro'] ? '?erro' : '/'));
      exit;
  }
  $_POST['cmd'] == 'Gerar' or $token = '';
  header('Location: /token'. ($db->exec("UPDATE users SET token = '$token' WHERE id_ufrj = '$usuario'") ? '/' : '?erro'));
  exit;
}
$_SESSION['token'] = $token;

function phpAlert() {
    echo '
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/sweetalert2/0.4.5/sweetalert2.css">
<script src="https://cdn.jsdelivr.net/sweetalert2/0.4.5/sweetalert2.min.js"></script>
<script>
    swal({
          title: "Você já leu nossos termos e condições de uso?",
          text: "Para obter sua chave de acesso, você deve ler e concordar com nossos termos e condições de uso.",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Ok, li e concordo!",
          cancelButtonText: "Ler termos de uso",
          confirmButtonClass: "btn btn-success",
          cancelButtonClass: "btn btn-danger",
          buttonsStyling: false,
          closeOnConfirm: true,
          closeOnCancel: false,
          allowOutsideClick: false
          },
function(isConfirm) {
  if (isConfirm === true) {
    // confirmation closes window
  } else if (isConfirm === false) {
    window.open("//docs.google.com/viewerng/viewer?url=https://caronae.ufrj.br/termos_de_uso.pdf");
  } else {
    // outside click, not allowed
  }
})
</script>';
}
?>

<html>
<head>
    
    <title>Obter Chave | Caronaê</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="http://caronae.tic.ufrj.br/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="http://caronae.tic.ufrj.br/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="http://caronae.tic.ufrj.br/favicon-16x16.png" sizes="16x16">

    <link rel="stylesheet" type="text/css" href="http://caronae.tic.ufrj.br/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="http://caronae.tic.ufrj.br/css/token/main.css">
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
                    <img src="http://caronae.tic.ufrj.br/images/logo_caronae_with_text.png">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 form-box">
                    <div class="form-top">
                        <div class="form-top-left">
                            <?php if ($app_token) : ?>
                                <h3>Você já tem uma chave Caronaê!</h3>
                                <p>Para criar uma nova basta clicar em "Nova Chave".</p>
                            <?php else : phpAlert() ?>
                                <h3>Obtenha a sua chave do Caronaê!</h3>
                                <p>Basta clicar em "Gerar Chave":</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-bottom">
                        <?php if ($_SERVER["QUERY_STRING"]): ?>
                            <p class="alert alert-danger">
                                <span style="font-size: 18px">Ocorreu um problema, tente novamente.</span>
                            </p>
                        <?php endif; ?>

                        <?php if ($app_token) : ?>
                            <p class="text-center">Sua chave de acesso ao Caronaê é:</p>
                            <h2 class="text-center token" data-clipboard-text="<?= $app_token ?>"><?= $app_token ?></h2>
                            <p class="text-center copy-text">Basta clicar para copiar a chave.</p>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-group">
                            <input type="hidden" name="token" value="<?= $token ?>"/>
                            <button type="submit" class="button btn btn-block btn-success" name="cmd" value="Gerar">
                                <?php if ($app_token) : ?>
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>Nova Chave</span>
                                <?php else : ?>
                                    <span class="glyphicon glyphicon-certificate"></span>
                                    <span>Gerar Chave</span>
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

<script src="http://caronae.tic.ufrj.br/js/token/clipboard.min.js"></script>
<script>
    var clipboard = new Clipboard('.token');

    clipboard.on('success', function(e) {
        document.querySelector('.copy-text').innerHTML =
            '<span class="text-success" style="font-size:2em">' +
                'Copiado! Agora é só colar no app do Caronaê.' +
            '</span>';

        e.clearSelection();
    });

    clipboard.on('error', function(e) {
        document.querySelector('.copy-text').innerHTML =
            '<span class="text-danger" style="font-size:2em">' +
                'Erro... É preciso copiar manualmente.' +
            '</span>';
    });

</script>


</body>
</html>
