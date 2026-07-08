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

    // 🔥 NUEVA PROPIEDAD: Guarda el último ID de inserción real
    protected int $lastInsertedId = 0;

    protected string $select = "*";
    public string $where = "";
    public array $values = [];
    protected string $orderBy = "";
    protected string $joins = ""; // ◄ NUEVA: Almacena los strings de los JOINs
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

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Obtiene el último ID autoincremental generado en la base de datos.
     * 
     * @return int
     */
    public function getInsertId(): int
    {
        if (isset($this->lastInsertedId) && $this->lastInsertedId > 0) {
            $id = $this->lastInsertedId;
            $this->lastInsertedId = 0; // Consumimos el ID y lo limpiamos
            return $id;
        }
        return (int)($this->connection->insert_id ?? 0);
    }

    // Agrega este método dentro de tu clase Model.php:
    /**
     * Obtiene el resultado de la última consulta ejecutada
     * @return mixed
     */
    public function getQueryResult()
    {
        return $this->query;
    }

    // SOFT DELETE: Métodos modificadores de flujo estilo Laravel
    public function withTrashed()
    {
        $this->resetQuery();
        $this->useSoftDeletes = true;
        $this->withTrashed = true;
        $this->onlyTrashed = false;
        return $this;
    }

    public function onlyTrashed()
    {
        $this->resetQuery(); // Limpia campos structurales (where, values, orderBy)
        $this->useSoftDeletes = true;
        $this->onlyTrashed = true;
        $this->withTrashed = false;
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

            // 🔥 RESPALDO CRÍTICO: Capturamos el ID inmediatamente después del execute
            if ($this->connection->insert_id > 0) {
                $this->lastInsertedId = (int)$this->connection->insert_id;
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

    public function join(string $table, string $first, string $operator, string $second)
    {
        // Sanitizar nombres de tablas y columnas básicos omitiendo acentos graves accidentales
        $table  = trim(str_replace('`', '', $table));
        $first  = trim(str_replace('`', '', $first));
        $second = trim(str_replace('`', '', $second));

        // Estructura nativa SQL para un INNER JOIN estándar
        $joinSql = " INNER JOIN {$table} ON {$first} {$operator} {$second}";

        // Permite acumular múltiples joins si se encadenan de forma consecutiva
        $this->joins .= $joinSql;

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
        // 1. Añadimos de forma nativa la variable $this->joins justo después del FROM
        $sql = "SELECT {$this->select} FROM {$this->table}{$this->joins}";

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

    // SOFT DELETE: Limpiar solo las cláusulas estructurales de la consulta actual
    protected function resetQuery()
    {
        $this->select = "*";
        $this->where = "";
        $this->values = [];
        $this->orderBy = "";
        $this->joins = ""; // ◄ NUEVA: Resetea el búfer de joins para la siguiente ejecución
        $this->query = null;
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

        // MEJORA CRÍTICA: Se califica 'deleted_at' con el nombre de la tabla para evitar ambigüedades en JOINs
        $softDeleteWhere = "";
        if ($this->useSoftDeletes) {
            if ($this->onlyTrashed) {
                $softDeleteWhere = "{$this->table}.deleted_at IS NOT NULL";
            } elseif (!$this->withTrashed) {
                $softDeleteWhere = "{$this->table}.deleted_at IS NULL";
            }
        }

        // =====================================================================
        // 🔥 CORRECCIÓN MASTER: Añadida la propiedad $this->joins al conteo total
        // =====================================================================
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}{$this->joins}";

        if (!empty($softDeleteWhere)) {
            $countSql .= !empty($this->where) ? " WHERE ({$this->where}) AND {$softDeleteWhere}" : " WHERE {$softDeleteWhere}";
        } elseif (!empty($this->where)) {
            $countSql .= " WHERE {$this->where}";
        }

        $countQuery = $this->connection->prepare($countSql);

        // ¡BLINDAJE CRÍTICO! Si la consulta tiene fallos de sintaxis o ambigüedad, el CLI/Navegador te lo dirá al instante
        if (!$countQuery) {
            throw new \mysqli_sql_exception("Error preparando el conteo de paginación: " . $this->connection->error . " | SQL generado: " . $countSql);
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

        $last_page = (int)ceil($total / $cant);
        if ($last_page < 1) $last_page = 1;

        // Construcción de strings de consulta para preservar el buscador en los enlaces
        $queryString = count($queryParams) > 0 ? '&' . http_build_query($queryParams) : '';

        // Primero limpiamos la estructura de la consulta
        $this->resetQuery();

        // Y finalmente apagamos las banderas para que el modelo quede limpio para el siguiente flujo del script
        $this->onlyTrashed = false;
        $this->withTrashed = false;

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
        $this->resetQuery();
        $this->useSoftDeletes = true;
        $this->withTrashed = false;
        $this->onlyTrashed = false;
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

    /**
     * Verifica si un valor ya existe en una columna específica de una tabla.
     * 
     * @param string $column Nombre de la columna a evaluar (ej: 'username', 'dni')
     * @param string $value Valor a buscar en la base de datos
     * @param int|null $id ID a excluir de la búsqueda en caso de actualizaciones
     * @param string|null $tablaOpcional Permite forzar una tabla externa (ej: 'personas')
     * @return bool
     */
    public function exists(string $column, string $value, ?int $id = null, ?string $tablaOpcional = null): bool
    {
        // 🔥 DETECCIÓN DINÁMICA: Si pasan una tabla, la usa. Si no, usa la del modelo actual.
        $tablaActual = (!empty($tablaOpcional)) ? trim($tablaOpcional) : $this->table;

        $sql = "SELECT COUNT(*) as total FROM {$tablaActual} WHERE {$column} = ?";
        $params = [$value];
        $types = 's';

        // Si pasan un ID (caso Update), lo excluimos de la búsqueda de duplicados
        if ($id !== null) {
            $sql .= " AND {$this->primaryKey} != ?";
            $params[] = $id;
            $types .= 'i';
        }

        // Soporte estricto para Soft Deletes calificado por la tabla correspondiente
        if ($this->useSoftDeletes && !$this->withTrashed) {
            $sql .= " AND {$tablaActual}.deleted_at IS NULL";
        }

        // Aislamiento total mediante sentencia preparada local directa
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception('Error en preparación local exists: ' . $this->connection->error);
        }

        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            throw new \mysqli_sql_exception($stmt->error, $stmt->errno);
        }

        $resultado = $stmt->get_result();
        $total = 0;

        if ($resultado) {
            $row = $resultado->fetch_assoc();
            $total = (int)($row['total'] ?? 0);
            $resultado->free();
        }

        $stmt->close();

        return $total > 0;
    }

    /**
     * Verifica la existencia de un registro de forma aislada y estática.
     * No utiliza $this para evitar quiebres en bloques transaccionales.
     * 
     * @param \mysqli $connection Conexión activa a la base de datos
     * @param string $table Nombre de la tabla (ej: 'usuarios', 'personas')
     * @param string $column Nombre de la columna (ej: 'username', 'email')
     * @param string $value Valor a buscar
     * @param int|null $excludeId ID a excluir en caso de actualizaciones
     * @param string $primaryKeyOpcional Nombre físico de la PK en la tabla (por defecto 'id')
     * @param bool $aplicarSoftDeletes Indica si debe filtrar deleted_at IS NULL (por defecto false)
     * @return bool
     */
    public static function checkExists(
        \mysqli $connection,
        string $table,
        string $column,
        string $value,
        ?int $excludeId = null,
        string $primaryKeyOpcional = 'id',
        bool $aplicarSoftDeletes = false // ◄ NUEVO PARAMETRO CONTROLADO
    ): bool {

        $sql = "SELECT COUNT(*) as total FROM `{$table}` WHERE `{$column}` = ?";
        $params = [$value];
        $types = 's';

        // Si hay un ID de exclusión válido, lo concatenamos usando la PK correspondiente
        if ($excludeId !== null && $excludeId > 0) {
            $sql .= " AND `{$primaryKeyOpcional}` != ?";
            $params[] = (int)$excludeId;
            $types .= 'i';
        }

        // 🔥 CORRECCIÓN: Filtro de Soft Deletes utilizando la bandera local pura
        if ($aplicarSoftDeletes) {
            $sql .= " AND `{$table}`.`deleted_at` IS NULL";
        }

        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Error en checkExists local: " . $connection->error);
        }

        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            $stmt->close();
            throw new \mysqli_sql_exception($stmt->error, $stmt->errno);
        }

        $resultado = $stmt->get_result();
        $total = 0;

        if ($resultado) {
            $row = $resultado->fetch_assoc();
            $total = (int)($row['total'] ?? 0);
            $resultado->free();
        }

        $stmt->close();

        return $total > 0;
    }

    public function create(array $data)
    {
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $this->query($sql, array_values($data));

        // 🔥 EL BLINDAJE: Guardamos el ID en la propiedad antes de que find() lo destruya
        $idInsertado = (int)$this->connection->insert_id;
        $this->lastInsertedId = $idInsertado;

        $this->resetQuery();

        $registro = $this->find($idInsertado);

        // Aseguramos compatibilidad total de arreglos para tu controlador
        if (is_array($registro)) {
            $registro[$this->primaryKey] = $idInsertado;
            $registro['id'] = $idInsertado;
        }

        return $registro;
    }

    public function update(int $id, array $data)
    {
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        // Salvaguarda: Si no quedan campos válidos por actualizar, evita ejecutar SQL
        if (empty($data)) {
            return true;
        }

        $fields = [];
        foreach (array_keys($data) as $key) {
            $fields[] = "{$key} = ?";
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE {$this->primaryKey} = ?";
        $values = array_values($data);
        $values[] = $id;

        $this->query($sql, $values);

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

        // CORRECCIÓN CRÍTICA: Evaluamos que la consulta se haya ejecutado sin errores del motor de base de datos
        return $this->connection->errno === 0;
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
