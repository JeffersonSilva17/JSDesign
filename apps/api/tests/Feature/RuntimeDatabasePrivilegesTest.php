<?php

namespace Tests\Feature;

use Dotenv\Dotenv;
use PDO;
use PDOException;
use Tests\TestCase;

final class RuntimeDatabasePrivilegesTest extends TestCase
{
    public function test_runtime_can_read_but_cannot_create_schema_objects_or_escalate(): void
    {
        $env = Dotenv::parse(file_get_contents(base_path('.env')));
        self::assertSame('jsdesign_runtime', $env['DB_USERNAME']);
        $db = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=jsdesign_test', $env['DB_USERNAME'], $env['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $role = $db->query('SELECT rolsuper, rolcreatedb, rolcreaterole, rolreplication, rolbypassrls FROM pg_roles WHERE rolname=current_user')->fetch(PDO::FETCH_ASSOC);
        foreach ($role as $privilege) {
            self::assertFalse($privilege);
        }
        self::assertFalse($db->query("SELECT has_schema_privilege(current_user, 'public', 'CREATE')")->fetchColumn());
        self::assertFalse($db->query("SELECT has_database_privilege(current_user, current_database(), 'CREATE')")->fetchColumn());
        self::assertFalse($db->query("SELECT has_database_privilege(current_user, current_database(), 'TEMPORARY')")->fetchColumn());
        self::assertIsInt($db->query('SELECT count(*) FROM catalog_products')->fetchColumn());
        $db->beginTransaction();
        try {
            $db->exec('CREATE TABLE public.runtime_privilege_probe (id integer)');
            self::fail('Runtime unexpectedly has DDL privileges.');
        } catch (PDOException $exception) {
            self::assertSame('42501', $exception->getCode());
        } finally {
            $db->rollBack();
        }
    }
}
