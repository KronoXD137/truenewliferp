<?php
// config.php
// Ajuste aqui suas credenciais de DB
$db_host = '177.54.147.212';
$db_name = 'maykonm_4363';
$db_user = 'maykonm_4363';
$db_pass = 'qCI22B0hok';
$dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Em produção mostre mensagem genérica
    exit('Erro na conexão com o banco: ' . $e->getMessage());
}

/**
 * Função flexível para verificar senha.
 * Modos comuns:
 * - 'password_hash' usa password_verify (recomendado se você usou password_hash).
 * - 'md5' compara md5($plain).
 * - 'base64' compara base64_encode($plain).
 * - 'plain' compara texto puro.
 *
 * A função tenta várias estratégias na ordem mais segura.
 *
 * Se você souber exatamente qual método foi usado, altere a lógica para forçar somente aquele.
 */
function verify_password_flexible(string $plain, string $stored): bool {
    // 1) Se stored parece ser um password_hash (começa por $2y$ ou $argon2)
    if (password_needs_rehash($stored, PASSWORD_DEFAULT) || password_verify($plain, $stored)) {
        // Se password_verify gerar aviso por stored inválido, password_verify retornará false
        if (@password_verify($plain, $stored)) return true;
    }

    // 2) Try password_verify (if stored is a valid hash)
    if (@password_verify($plain, $stored)) return true;

    // 3) md5 (legacy)
    if (md5($plain) === $stored) return true;

    // 4) base64 (alguns sistemas salvam base64 de algo)
    if (base64_encode($plain) === $stored) return true;

    // 5) plain equality (não ideal)
    if ($plain === $stored) return true;

    // não bateu
    return false;
}
