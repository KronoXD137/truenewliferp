<?php
// login.php
session_start();
require_once 'config.php';

// simples proteção contra bruteforce: conte tentativas na sessão
if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
if ($_SESSION['login_attempts'] > 10) {
    exit('Muitas tentativas. Aguarde alguns minutos.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$login = isset($_POST['login']) ? trim($_POST['login']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($login === '' || $password === '') {
    $_SESSION['login_attempts']++;
    $_SESSION['error'] = 'Preencha usuário e senha.';
    header('Location: login.html');
    exit;
}

try {
    // buscar conta pelo login (case-sensitive normalmente)
    $stmt = $pdo->prepare('SELECT * FROM TC_accounts WHERE login = :login LIMIT 1');
    $stmt->execute([':login' => $login]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['login_attempts']++;
        $_SESSION['error'] = 'Usuário ou senha inválidos.';
        header('Location: login.html');
        exit;
    }

    // Verificação flexível de senha - adapte se souber o método exato
    if (!verify_password_flexible($password, $user['password'])) {
        $_SESSION['login_attempts']++;
        $_SESSION['error'] = 'Usuário ou senha inválidos.';
        header('Location: login.html');
        exit;
    }

    // sucesso: gravar sessão e atualizar lastOnline / isOnline
    session_regenerate_id(true);
    $_SESSION['user_uid'] = $user['UID'];
    $_SESSION['user_login'] = $user['login'];
    $_SESSION['user_email'] = $user['email'] ?? null;

    // Atualiza lastOnline e isOnline no BD (opcional)
    $upd = $pdo->prepare('UPDATE TC_accounts SET lastOnline = NOW(), isOnline = 1, ip = :ip WHERE UID = :uid');
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? null;
    $upd->execute([':ip' => $clientIp, ':uid' => $user['UID']]);

    // reset attempts
    $_SESSION['login_attempts'] = 0;

    // redireciona para dashboard
    header('Location: dashboard.php');
    exit;

} catch (Exception $e) {
    // Em produção, logue o erro e mostre mensagem genérica
    exit('Erro interno: ' . $e->getMessage());
}
