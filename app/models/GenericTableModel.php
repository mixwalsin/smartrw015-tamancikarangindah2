<?php

declare(strict_types=1);

require_once APP_PATH . '/core/Model.php';

class GenericTableModel extends Model
{
    public function __construct(string $table)
    {
        $this->table = $table;
        parent::__construct();
    }

    public function paginateQuery(
        string $selectSql,
        string $countSql,
        int $page,
        string $keyword = '',
        array $searchColumns = [],
        int $perPage = PAGINATION_LIMIT
    ): array {
        $offset = ($page - 1) * $perPage;
        $conditions = [];
        $params = [];

        if ($keyword !== '' && $searchColumns !== []) {
            foreach ($searchColumns as $index => $column) {
                $param = 'search_' . $index;
                $conditions[] = $column . ' LIKE :' . $param;
                $params[$param] = '%' . $keyword . '%';
            }
        }

        $whereSql = $conditions !== [] ? ' WHERE ' . implode(' OR ', $conditions) : '';

        $countStmt = $this->db->prepare($countSql . $whereSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare($selectSql . $whereSql . ' LIMIT :limit OFFSET :offset');
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'         => $stmt->fetchAll(),
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
            'keyword'      => $keyword,
        ];
    }

    public function fetchOneQuery(string $sql, array $bindings = []): array|false
    {
        $stmt = $this->db->prepare($sql);
        foreach ($bindings as $key => $value) {
            $param = is_string($key) ? ':' . ltrim($key, ':') : $key + 1;
            $stmt->bindValue($param, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetch();
    }

    public function fetchPairs(string $sql, array $bindings = []): array
    {
        $stmt = $this->db->prepare($sql);
        foreach ($bindings as $key => $value) {
            $param = is_string($key) ? ':' . ltrim($key, ':') : $key + 1;
            $stmt->bindValue($param, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $result = [];
        foreach ($rows as $row) {
            $values = array_values($row);
            $result[(string) $values[0]] = (string) $values[1];
        }
        return $result;
    }
}
