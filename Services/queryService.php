<?php
require_once "pdoConnection.php";
/**
 * Выполняет запрос к базе данных и возвращает результат.
 *
 * @param string $query SQL-запрос.
 * @param array $params Параметры для привязки.
 * @param bool $fetchAll Вернуть все строки или одну.
 * @param int $fetchMode Режим извлечения (PDO::FETCH_ASSOC по умолчанию).
 * @return array|string|null
 */
function executeQuery(string $query, array $params = [], bool $fetchAll = true, int $fetchMode = PDO::FETCH_ASSOC): array|string|null
{
    global $dbh;
    try {
        $stmt = $dbh->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $fetchAll ? $stmt->fetchAll($fetchMode) : $stmt->fetch($fetchMode);
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return null;
    }
}