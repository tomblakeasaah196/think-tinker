<?php
/**
 * includes/db.php
 * PDO database singleton — one connection, reused everywhere.
 * All queries MUST use prepared statements.
 */

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'db.php') {
    http_response_code(403);
    exit('Direct access denied.');
}

/**
 * Get the PDO database connection (singleton).
 * Creates the connection on first call, reuses it on subsequent calls.
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ]);
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                http_response_code(500);
                exit('Database connection failed: ' . $e->getMessage());
            }
            error_log('Database connection failed: ' . $e->getMessage());
            http_response_code(500);
            exit('Service temporarily unavailable. Please try again later.');
        }
    }

    return $pdo;
}

/**
 * Execute a SELECT query and return all rows.
 *
 * @param string $sql    SQL query with ? or :named placeholders
 * @param array  $params Parameters to bind
 * @return array
 */
function dbFetchAll(string $sql, array $params = []): array
{
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Execute a SELECT query and return a single row.
 *
 * @param string $sql    SQL query with ? or :named placeholders
 * @param array  $params Parameters to bind
 * @return array|null
 */
function dbFetchOne(string $sql, array $params = []): ?array
{
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Execute a SELECT query and return a single column value.
 *
 * @param string $sql    SQL query with ? or :named placeholders
 * @param array  $params Parameters to bind
 * @return mixed
 */
function dbFetchColumn(string $sql, array $params = []): mixed
{
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

/**
 * Execute an INSERT/UPDATE/DELETE query.
 *
 * @param string $sql    SQL query with ? or :named placeholders
 * @param array  $params Parameters to bind
 * @return int           Number of affected rows
 */
function dbExecute(string $sql, array $params = []): int
{
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/**
 * Insert a row and return the new auto-increment ID.
 *
 * @param string $table   Table name
 * @param array  $data    Associative array of column => value
 * @return int             Last insert ID
 */
function dbInsert(string $table, array $data): int
{
    $columns = implode(', ', array_map(fn($col) => "`$col`", array_keys($data)));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));

    $sql = "INSERT INTO `$table` ($columns) VALUES ($placeholders)";
    $stmt = getDB()->prepare($sql);
    $stmt->execute(array_values($data));

    return (int) getDB()->lastInsertId();
}

/**
 * Update rows in a table.
 *
 * @param string $table      Table name
 * @param array  $data       Associative array of column => value to SET
 * @param string $where      WHERE clause (e.g., "id = ?")
 * @param array  $whereParams Parameters for the WHERE clause
 * @return int                Number of affected rows
 */
function dbUpdate(string $table, array $data, string $where, array $whereParams = []): int
{
    $setParts = array_map(fn($col) => "`$col` = ?", array_keys($data));
    $setClause = implode(', ', $setParts);

    $sql = "UPDATE `$table` SET $setClause WHERE $where";
    $params = array_merge(array_values($data), $whereParams);

    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);

    return $stmt->rowCount();
}

/**
 * Soft-delete a row by setting deleted_at.
 *
 * @param string $table Table name
 * @param int    $id    Row ID
 * @return int          Number of affected rows
 */
function dbSoftDelete(string $table, int $id): int
{
    return dbExecute(
        "UPDATE `$table` SET `deleted_at` = NOW() WHERE `id` = ? AND `deleted_at` IS NULL",
        [$id]
    );
}

/**
 * Count rows matching a condition.
 *
 * @param string $table Table name
 * @param string $where WHERE clause (e.g., "status = ?")
 * @param array  $params Parameters for the WHERE clause
 * @return int
 */
function dbCount(string $table, string $where = '1=1', array $params = []): int
{
    return (int) dbFetchColumn("SELECT COUNT(*) FROM `$table` WHERE $where", $params);
}
