<?php

// Local/CI bootstrap only. Administrative credentials are never application credentials.
require __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;

$api = dirname(__DIR__);
$source = file_get_contents($api.'/.env');
$values = Dotenv::parse($source);
$initial = in_array('--from-current-env', $argv, true);
$admin = getenv('BOOTSTRAP_DB_USERNAME') ?: ($initial ? $values['DB_USERNAME'] : null);
$password = getenv('BOOTSTRAP_DB_PASSWORD') ?: ($initial ? $values['DB_PASSWORD'] : null);
if (! $admin || ! $password) {
    throw new RuntimeException('Supply bootstrap credentials through the environment.');
}
$host = $values['DB_HOST'] ?? '127.0.0.1';
$port = $values['DB_PORT'] ?? '5432';
if (! in_array($host, ['127.0.0.1', 'localhost', '::1'], true)) {
    throw new RuntimeException('Provisioning is restricted to local PostgreSQL.');
}
$connect = static fn (string $database): PDO => new PDO("pgsql:host=$host;port=$port;dbname=$database", $admin, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$db = $connect('postgres');
$runtimePassword = bin2hex(random_bytes(32));
$migrationPassword = bin2hex(random_bytes(32));
$key = bin2hex(random_bytes(32));
foreach (['jsdesign_runtime' => $runtimePassword, 'jsdesign_migrator' => $migrationPassword] as $role => $secret) {
    if (! $db->query('SELECT 1 FROM pg_roles WHERE rolname = '.$db->quote($role))->fetchColumn()) {
        $db->exec('CREATE ROLE '.$role.' LOGIN');
    }
    $db->exec('ALTER ROLE '.$role.' NOSUPERUSER NOCREATEDB NOCREATEROLE NOREPLICATION NOBYPASSRLS PASSWORD '.$db->quote($secret));
}
$databases = $db->query("SELECT datname FROM pg_database WHERE datname IN ('jsdesign', 'jsdesign_test')")->fetchAll(PDO::FETCH_COLUMN);
if (! in_array('jsdesign_test', $databases, true)) {
    throw new RuntimeException('Create the isolated jsdesign_test database before provisioning.');
}
foreach ($databases as $database) {
    $connection = $connect($database);
    $connection->exec('REVOKE CREATE, TEMPORARY ON DATABASE '.$database.' FROM PUBLIC');
    $connection->exec('GRANT CONNECT, CREATE ON DATABASE '.$database.' TO jsdesign_migrator');
    $connection->exec('REVOKE ALL ON DATABASE '.$database.' FROM jsdesign_runtime');
    $connection->exec('GRANT CONNECT ON DATABASE '.$database.' TO jsdesign_runtime');
    $connection->exec('ALTER SCHEMA public OWNER TO jsdesign_migrator');
    $connection->exec('REVOKE CREATE ON SCHEMA public FROM PUBLIC');
    // Preserve data; transfer only application objects, excluding extension-owned objects.
    $objects = $connection->query(<<<'SQL'
        SELECT CASE c.relkind WHEN 'S' THEN 'SEQUENCE' ELSE 'TABLE' END AS kind, quote_ident(c.relname) AS name
        FROM pg_class c JOIN pg_namespace n ON n.oid=c.relnamespace
        WHERE n.nspname='public' AND c.relkind IN ('r','p','S')
          AND NOT EXISTS (SELECT 1 FROM pg_depend d WHERE d.objid=c.oid AND d.classid='pg_class'::regclass AND d.deptype='e')
        ORDER BY (c.relkind='S')
    SQL)->fetchAll(PDO::FETCH_ASSOC);
    foreach ($objects as $object) {
        $connection->exec('ALTER '.$object['kind'].' public.'.$object['name'].' OWNER TO jsdesign_migrator');
    }
    $functions = $connection->query(<<<'SQL'
        SELECT p.oid::regprocedure::text FROM pg_proc p JOIN pg_namespace n ON n.oid=p.pronamespace
        WHERE n.nspname='public' AND NOT EXISTS (SELECT 1 FROM pg_depend d WHERE d.objid=p.oid AND d.classid='pg_proc'::regclass AND d.deptype='e')
    SQL)->fetchAll(PDO::FETCH_COLUMN);
    foreach ($functions as $function) {
        $connection->exec('ALTER FUNCTION '.$function.' OWNER TO jsdesign_migrator');
    }
    $connection->exec('REVOKE ALL ON SCHEMA public FROM jsdesign_runtime');
    $connection->exec('GRANT USAGE ON SCHEMA public TO jsdesign_runtime');
    $connection->exec('REVOKE ALL ON ALL TABLES IN SCHEMA public FROM jsdesign_runtime');
    $connection->exec('GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO jsdesign_runtime');
    $connection->exec('GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO jsdesign_runtime');
    $connection->exec('ALTER DEFAULT PRIVILEGES FOR ROLE jsdesign_migrator IN SCHEMA public GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO jsdesign_runtime');
    $connection->exec('ALTER DEFAULT PRIVILEGES FOR ROLE jsdesign_migrator IN SCHEMA public GRANT USAGE, SELECT ON SEQUENCES TO jsdesign_runtime');
}
$replace = static function (string $text, array $entries): string {
    foreach ($entries as $name => $value) {
        $text = preg_match('/^'.preg_quote($name, '/').'=/m', $text)
            ? preg_replace('/^'.preg_quote($name, '/').'=.*$/m', $name.'='.$value, $text)
            : $text."\n".$name.'='.$value."\n";
    }

    return $text;
};
$mainDatabase = in_array('jsdesign', $databases, true) ? 'jsdesign' : 'jsdesign_test';
file_put_contents($api.'/.env', $replace($source, ['DB_DATABASE' => $mainDatabase, 'DB_USERNAME' => 'jsdesign_runtime', 'DB_PASSWORD' => $runtimePassword, 'SITEMAP_CLIENT_KEY' => $key]));
file_put_contents($api.'/.env.testing', $replace($source, ['APP_ENV' => 'testing', 'DB_DATABASE' => 'jsdesign_test', 'DB_USERNAME' => 'jsdesign_migrator', 'DB_PASSWORD' => $migrationPassword, 'SITEMAP_CLIENT_KEY' => $key]));
file_put_contents($api.'/.env.migrations', $replace($source, ['DB_DATABASE' => $mainDatabase, 'DB_USERNAME' => 'jsdesign_migrator', 'DB_PASSWORD' => $migrationPassword, 'SITEMAP_CLIENT_KEY' => $key]));
$webPath = $api.'/../web/.env.local';
$webSource = is_file($webPath) ? file_get_contents($webPath) : file_get_contents($api.'/../web/.env.example');
file_put_contents($webPath, $replace($webSource, ['SITEMAP_CLIENT_KEY' => $key]));
if (PHP_OS_FAMILY !== 'Windows') {
    foreach ([$api.'/.env', $api.'/.env.testing', $api.'/.env.migrations', $webPath] as $path) {
        chmod($path, 0600);
    }
}
echo 'Provisioned runtime and migrator roles; secrets written only to ignored environment files.'.PHP_EOL;
