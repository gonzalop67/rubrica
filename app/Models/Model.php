<?php

namespace App\Models;

use mysqli;

class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected static ?mysqli $sharedConnection = null;
    protected mysqli $connection;
    protected mixed $query = null;
    protected string $select = "*";
    public string $where = "";
    public array $values = [];
    protected string $orderBy = "";
    public array $errors = [];

    // SOFT DELETE: Propiedades de control de estado
    protected bool $useSoftDeletes = false; // Activa/desactiva softdelete globalmente en este modelo
    protected bool $withTrashed = false;   // Bandera para incluir eliminados
    protected bool $onlyTrashed = false;   // Bandera para mostrar SOLO eliminados

    public function __construct()
    {
        $this->connection();
    }

    public function connection()
    {
        // Desactiva el reporte de errores interno de Mysqli para controlarlo tú mismo
        mysqli_report(MYSQLI_REPORT_OFF);

        // Si la conexión ya fue creada por otro modelo, la reutilizamos
        if (self::$sharedConnection === null) {
            // CORRECCIÓN CLAVE: Agregamos la barra invertida "\" para forzar el alcance global
            $conn = new \mysqli(\DB_HOST, \DB_USER, \DB_PASS, \DB_NAME);

            if ($conn->connect_error) {
                die('Error de conexión: ' . $conn->connect_error);
            }
            $conn->set_charset('utf8mb4');
            self::$sharedConnection = $conn;
        }

        // Asignamos la conexión compartida a la propiedad del modelo actual
        $this->connection = self::$sharedConnection;
    }

    public function getTable()
    {
        return $this->table;
    }

    /**
     * Obtiene el último ID autoincremental generado en la base de datos.
     * 
     * @return int
     */
    public function getInsertId(): int
    {
        return (int)($this->connection->insert_id ?? 0);
    }

    // SOFT DELETE: Métodos modificadores de flujo estilo Laravel
    public function withTrashed(): self
    {
        $this->withTrashed = true;
        return $this;
    }

    public function onlyTrashed(): self
    {
        $this->onlyTrashed = true;
        return $this;
    }

    // Restauración lógica
    public function restore(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET deleted_at = NULL WHERE {$this->primaryKey} = ?";
        $this->query($sql, [$id], 'i');
        return $this->query > 0;
    }

    // Eliminación física definitiva de la Base de Datos
    public function forceDelete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $this->query($sql, [$id], 'i');
        return $this->query > 0;
    }

    public function query(string $sql, array $data = [], ?string $params = null)
    {
        if ($data) {
            if ($params == null) {
                $params = '';
                foreach ($data as $val) {
                    if (is_int($val)) $params .= 'i';
                    elseif (is_double($val)) $params .= 'd';
                    else $params .= 's';
                }
            }

            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                // CAMBIO: Lanzar excepción en lugar de matar el script con die()
                throw new \mysqli_sql_exception('Error en la preparación SQL: ' . $this->connection->error . ' | SQL: ' . $sql, $this->connection->errno);
            }

            $stmt->bind_param($params, ...$data);

            if (!$stmt->execute()) {
                // CORRECCIÓN CLAVE: Lanzar excepción nativa con el código de error (ej: 1451)
                throw new \mysqli_sql_exception($stmt->error, $stmt->errno);
            }

            if ($stmt->field_count > 0) {
                $this->query = $stmt->get_result();
            } else {
                $this->query = $stmt->affected_rows;
            }

            $stmt->close();
        } else {
            $this->query = $this->connection->query($sql);
            if (!$this->query) {
                throw new \mysqli_sql_exception('Error en consulta directa: ' . $this->connection->error, $this->connection->errno);
            }
        }
        return $this;
    }

    public function select(...$columns)
    {
        // Seguridad básica ante inyecciones en los nombres de columnas
        $sanitized = array_map(fn($col) => trim(str_replace('`', '', $col)), $columns);
        $this->select = implode(', ', $sanitized);
        return $this;
    }

    public function orderBy(string $column, $order = 'ASC')
    {
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
        $column = trim(str_replace('`', '', $column));

        if (empty($this->orderBy)) {
            $this->orderBy = "{$column} {$order}";
        } else {
            $this->orderBy .= ", {$column} {$order}";
        }
        return $this;
    }

    // SOFT DELETE: Modificación crucial para inyectar las cláusulas automáticamente
    protected function buildSelectSql(): string
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        // Generamos el filtro dinámico de SoftDelete
        $softDeleteWhere = "";
        if ($this->useSoftDeletes) {
            if ($this->onlyTrashed) {
                $softDeleteWhere = "{$this->table}.deleted_at IS NOT NULL";
            } elseif (!$this->withTrashed) {
                $softDeleteWhere = "{$this->table}.deleted_at IS NULL";
            }
        }

        // Combinamos el softdelete con el $this->where del usuario de forma segura
        if (!empty($softDeleteWhere)) {
            if (!empty($this->where)) {
                $sql .= " WHERE ({$this->where}) AND {$softDeleteWhere}";
            } else {
                $sql .= " WHERE {$softDeleteWhere}";
            }
        } elseif (!empty($this->where)) {
            $sql .= " WHERE {$this->where}";
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY {$this->orderBy}";
        }
        return $sql;
    }

    // SOFT DELETE: Limpiar los estados para no afectar a la siguiente consulta del script
    protected function resetQuery()
    {
        $this->select = "*";
        $this->where = "";
        $this->values = [];
        $this->orderBy = "";
        $this->query = null;
        $this->withTrashed = false; // Reset de bandera
        $this->onlyTrashed = false; // Reset de bandera
    }

    public function first()
    {
        if (empty($this->query)) {
            $sql = $this->buildSelectSql();
            $this->query($sql, $this->values);
        }

        $result = null;
        if ($this->query instanceof \mysqli_result) {
            $result = $this->query->fetch_assoc();
        }

        $this->resetQuery();
        return $result;
    }

    public function get()
    {
        if (empty($this->query)) {
            $sql = $this->buildSelectSql();
            $this->query($sql, $this->values);
        }

        $result = [];
        if ($this->query instanceof \mysqli_result) {
            $result = $this->query->fetch_all(MYSQLI_ASSOC);
        }

        $this->resetQuery();
        return $result;
    }

    // SOFT DELETE: Adaptado para heredar la lógica de conteo limpia de buildSelectSql()
    public function paginate($cant = 15)
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        // MEJORA: El conteo ahora usa la misma estructura de buildSelectSql para respetar SoftDeletes
        $softDeleteWhere = "";
        if ($this->useSoftDeletes) {
            if ($this->onlyTrashed) {
                $softDeleteWhere = "deleted_at IS NOT NULL";
            } elseif (!$this->withTrashed) {
                $softDeleteWhere = "deleted_at IS NULL";
            }
        }

        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($softDeleteWhere)) {
            $countSql .= !empty($this->where) ? " WHERE ({$this->where}) AND {$softDeleteWhere}" : " WHERE {$softDeleteWhere}";
        } elseif (!empty($this->where)) {
            $countSql .= " WHERE {$this->where}";
        }

        $countQuery = $this->connection->prepare($countSql);

        // ¡BLINDAJE CRÍTICO AQUÍ! Si la consulta falla, detenemos con el error real de MySQL
        if (!$countQuery) {
            die("Error preparando el conteo de paginación: " . $this->connection->error . " | SQL generado: " . $countSql);
        }

        // CORRECCIÓN CLAVE: Detección dinámica de tipos idéntica a tu método query()
        if ($this->values) {
            $params = '';
            foreach ($this->values as $val) {
                if (is_int($val)) $params .= 'i';
                elseif (is_double($val)) $params .= 'd';
                else $params .= 's';
            }
            $countQuery->bind_param($params, ...$this->values);
        }

        $countQuery->execute();
        $total = $countQuery->get_result()->fetch_assoc()['total'] ?? 0;

        // IMPORTANTE: Cerrar el countQuery para liberar los hilos de Mysqli
        $countQuery->close();

        $sql = $this->buildSelectSql();
        $offset = ($page - 1) * $cant;

        // Mantenemos tu asignación directa pero segura para el LIMIT
        $sql .= " LIMIT {$offset}, {$cant}";

        $this->query($sql, $this->values);
        $data = ($this->query instanceof \mysqli_result) ? $this->query->fetch_all(MYSQLI_ASSOC) : [];

        // Conservamos temporalmente el URI antes del resetQuery
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = str_replace(['\\', '/public'], ['/', ''], dirname($scriptName));
        $uri = trim(str_replace($basePath, '', $uri), '/');

        // Recuperamos los parámetros GET actuales (como 'search') para mantenerlos en la URL de paginación
        $queryParams = $_GET;
        unset($queryParams['page']); // Eliminamos la página actual para sobrescribirla después

        $this->resetQuery();

        $last_page = (int)ceil($total / $cant);
        if ($last_page < 1) $last_page = 1;

        // Construcción de strings de consulta para preservar el buscador en los enlaces
        $queryString = count($queryParams) > 0 ? '&' . http_build_query($queryParams) : '';

        return [
            'total'        => $total,
            'from'         => $total > 0 ? $offset + 1 : 0,
            'to'           => $offset + count($data),
            'current_page' => $page,
            'last_page'    => $last_page,
            'next_page_url' => $page < $last_page ? "/{$uri}?page=" . ($page + 1) . $queryString : null,
            'prev_page_url' => $page > 1 ? "/{$uri}?page=" . ($page - 1) . $queryString : null,
            'data'         => $data,
        ];
    }

    // SOFT DELETE: Ahora utiliza buildSelectSql() en vez de SQL duro para respetar los filtros
    public function all()
    {
        $sql = $this->buildSelectSql();
        return $this->query($sql, $this->values)->get();
    }

    public function pluck($value, $key = null)
    {
        $columns = $key ? "{$key}, {$value}" : $value;
        // SOFT DELETE: Cambiado para usar buildSelectSql heredando los filtros activos de columnas
        $this->select = $columns;
        $sql = $this->buildSelectSql();
        $data = $this->query($sql, $this->values)->get();
        if (empty($data)) return [];
        return is_null($key) ? array_column($data, $value) : array_column($data, $value, $key);
    }

    // SOFT DELETE: Usa el motor buildSelectSql() nativo añadiendo el ID al stack de valores
    public function find(int $id)
    {
        $this->where($this->primaryKey, '=', $id);
        return $this->first();
    }

    public function where(string $column, string $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = "=";
        }

        // Seguridad contra manipulación de columnas en el WHERE
        $column = trim(str_replace('`', '', $column));

        if (!empty($this->where)) {
            $this->where .= " AND {$column} {$operator} ?";
        } else {
            $this->where = "{$column} {$operator} ?";
        }
        $this->values[] = $value;
        return $this;
    }

    public function orWhere(string $column, string $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = "=";
        }

        // Seguridad contra manipulación de nombres de columnas
        $column = trim(str_replace('`', '', $column));

        if (!empty($this->where)) {
            $this->where .= " OR {$column} {$operator} ?";
        } else {
            // Si es la primera condición, actúa como un WHERE normal
            $this->where = "{$column} {$operator} ?";
        }

        $this->values[] = $value;
        return $this;
    }

    public function exists(string $column, string $value, ?int $id = null): bool
    {
        // 1. Construimos la consulta base
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$column} = ?";
        $params = [$value];

        // 2. Si pasan un ID (caso Update), lo excluimos de la búsqueda
        if ($id !== null) {
            $sql .= " AND {$this->primaryKey} != ?";
            $params[] = $id;
        }

        // 3. Soporte para Soft Deletes
        if ($this->useSoftDeletes && !$this->withTrashed) {
            $sql .= " AND deleted_at IS NULL";
        }

        // Ejecutamos la consulta en el modelo
        $this->query($sql, $params);

        $result = 0;

        // CORREGIDO: Evaluamos la propiedad interna $this->query, no el retorno del método
        if ($this->query instanceof \mysqli_result) {
            // Obtenemos la fila de la propiedad interna
            $row = $this->query->fetch_assoc();
            $result = (int)($row['total'] ?? 0);

            // Liberamos la memoria del resultado interno
            $this->query->free();
        }

        // 4. Resetear el estado de consultas del modelo
        $this->resetQuery();

        return $result > 0;
    }


    public function create(array $data)
    {
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        // return array_values($data);

        $this->query($sql, array_values($data));
        return $this->find($this->connection->insert_id);
    }

    public function update(int $id, array $data)
    {
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        $fields = [];
        foreach (array_keys($data) as $key) {
            $fields[] = "{$key} = ?";
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = ?";
        $values = array_values($data);
        $values[] = $id;

        // Ejecutamos la consulta preparada
        $this->query($sql, $values);

        // CORRECCIÓN CRÍTICA: Si no hay código de error (errno === 0), la consulta fue exitosa
        // independientemente de si se modificaron filas en los textos o no.
        return $this->connection->errno === 0;
    }

    // Eliminación inteligente adaptada al estado del modelo
    public function delete(int $id): bool
    {
        if ($this->useSoftDeletes) {
            // Si SoftDelete está activo: Eliminación lógica (UPDATE)
            $now = date('Y-m-d H:i:s');
            $sql = "UPDATE {$this->table} SET deleted_at = ? WHERE {$this->primaryKey} = ?";
            $this->query($sql, [$now, $id], 'si');
        } else {
            // Si SoftDelete está inactivo: Eliminación física tradicional (DELETE)
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $this->query($sql, [$id], 'i');
        }

        return $this->query > 0; // Verifica filas afectadas
    }

    // Inicia la transacción desactivando el autocommit
    public function beginTransaction(): bool
    {
        return $this->connection->begin_transaction();
    }

    // Confirma todos los cambios realizados en la transacción
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    // Revierte todos los cambios si algo falló
    public function rollBack(): bool
    {
        return $this->connection->rollback();
    }
}
