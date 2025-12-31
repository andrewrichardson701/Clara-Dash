<?php
// lib/DB.php

class DB {
    private $pdo;

    public function __construct($config) {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
        $this->pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    /** Generic query helper */
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Convenience function to fetch a single row */
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /** Convenience function to fetch all rows */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /** Fetch all rows where... */
    public function fetchAllWhere($sql, $params = [], $whereConditions = []) {
        // If there are WHERE conditions, add them to the SQL query
        if (!empty($whereConditions)) {
            // Build the WHERE clause dynamically based on the conditions
            $whereSql = ' WHERE ';
            $whereClauses = [];
            foreach ($whereConditions as $column => $value) {
                // Assuming simple equality condition for now (could be extended)
                $whereClauses[] = "$column = :$column";
                $params[":$column"] = $value;  // Add the condition values to the parameters
            }
            $whereSql .= implode(' AND ', $whereClauses);
            $sql .= $whereSql; // Append the WHERE clause to the SQL query
        }
        
        // Prepare and execute the query with the merged parameters
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /** Insert helper (returns last insert ID) */
    public function insert($table, array $data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT INTO `$table` (`" . implode('`,`', $columns) . "`) VALUES (" . implode(',', $placeholders) . ")";
        $this->query($sql, array_values($data));
        return $this->pdo->lastInsertId();
    }

    /** Update helper */
    public function update($table, array $data, $where, array $whereParams = []) {
        $set = [];
        foreach ($data as $col => $val) {
            $set[] = "`$col` = ?";
        }
        $sql = "UPDATE `$table` SET " . implode(',', $set) . " WHERE $where";
        $params = array_merge(array_values($data), $whereParams);
        return $this->query($sql, $params);
    }

    /** Transactions */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }

}
