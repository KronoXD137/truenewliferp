<?php
// dashboard.php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_uid'])) {
    header('Location: login.html');
    exit;
}

// Puxe informações do usuário (opcional)
$stmt = $pdo->prepare('SELECT UID, login, email, rPoints, created, lastOnline FROM TC_accounts WHERE UID = :uid LIMIT 1');
$stmt->execute([':uid' => $_SESSION['user_uid']]);
$user = $stmt->fetch();

?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <title>Dashboard - <?= htmlspecialchars($user['login'] ?? 'Usuário') ?></title>
  <style>
    body { font-family: Arial, sans-serif; background:#071022; color:#eaf4ff; padding:28px; }
    .box { background:#07172b; padding:20px; border-radius:10px; width:480px; }
    a.logout { color:#ff7b7b; text-decoration:none; float:right; }
  </style>
</head>
<body>
  <div class="box">
    <a class="logout" href="logout.php">Sair</a>
    <h1>Olá, <?= htmlspecialchars($user['login'] ?? $_SESSION['user_login']) ?></h1>
    <p>Email: <?= htmlspecialchars($user['email'] ?? '-') ?></p>
    <p>rPoints: <?= htmlspecialchars($user['rPoints'] ?? '0') ?></p>
    <p>Criado em: <?= htmlspecialchars($user['created'] ?? '-') ?></p>
    <p>Último online: <?= htmlspecialchars($user['lastOnline'] ?? '-') ?></p>
  </div>
</body>
</html>
