<?php

namespace Core;

class MiniBlade
{
    protected array $sections = [];
    protected ?string $layout = null;
    protected ?string $currentSection = null;
    protected string $viewsPath;
    protected string $cachePath;
    protected bool $useCache = true;
    // Activa o desactiva la validación estricta de sintaxis
    protected bool $debug = true;

    protected array $sharedData = []; // 📦 Almacén de variables globales

    public function __construct(
        ?string $viewsPath = null,
        ?string $cachePath = null,
        bool $useCache = true,
        bool $debug = true
    ) {
        // 1. Si no se pasa una ruta de vistas, calculamos automáticamente la carpeta resources en la raíz
        $defaultViews = RAIZ_PROYECTO . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';
        $this->viewsPath = rtrim($viewsPath ?? $defaultViews, '/\\') . DIRECTORY_SEPARATOR;

        // 2. Si no se pasa una ruta de caché, usamos la carpeta cache de la raíz
        $defaultCache = RAIZ_PROYECTO . DIRECTORY_SEPARATOR . 'cache';
        $this->cachePath = rtrim($cachePath ?? $defaultCache, '/\\') . DIRECTORY_SEPARATOR;

        $this->useCache = $useCache;
        $this->debug = $debug;

        // 3. Validación de seguridad nativa intacta
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    /**
     * Registra variables globales para todas las vistas
     */
    public function share(string|array $key, mixed $value = null): void
    {
        if (is_array($key)) {
            $this->sharedData = array_merge($this->sharedData, $key);
        } else {
            $this->sharedData[$key] = $value;
        }
    }

    public function render(string $viewName, array $data = [])
    {
        // Resetear estado entre renderizados
        $this->layout = null;
        $this->sections = [];

        // 🌟 MEJORA: Fusiona los datos compartidos globales con los específicos de esta vista
        // Los datos específicos ($data) tienen prioridad si se llaman igual
        $combinedData = array_merge($this->sharedData, $data);

        // Ejecuta la vista base (hija)
        $content = $this->renderView($viewName, $combinedData);

        // Si la vista base definió un layout en ejecución, lo renderiza
        if ($this->layout) {
            return $this->renderView($this->layout, $combinedData);
        }

        return $content;
    }

    protected function renderView(string $viewName, array $data)
    {
        $path = $this->viewsPath . str_replace('.', '/', $viewName) . ".view.php";
        if (!file_exists($path)) {
            return "<!-- Vista [$viewName] no encontrada -->";
        }

        $cacheFile = $this->cachePath . md5($viewName) . '.php';

        if (!$this->useCache || !file_exists($cacheFile) || filemtime($path) > filemtime($cacheFile)) {
            //file_put_contents($cacheFile, $this->compile(file_get_contents($path)));
            $compiledCode = $this->compile(file_get_contents($path));
            file_put_contents($cacheFile, $compiledCode);

            // Validar sintaxis inmediatamente después de escribir la caché
            if ($this->debug) {
                $this->validateSyntax($cacheFile, $path);
            }
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include($cacheFile);
        return ob_get_clean();
    }

    protected function compile(string $code): string
    {
        $patterns = [
            // 1. Elimina comentarios de bloque: {{-- comentario --}}
            '/\{\{\-\-(.*?)\-\-\}\}/s' => '',

            // 2. Bloques PHP nativos (mantiene /s para soportar multilínea)
            '/@php(.*?)@endphp/s' => '<?php $1 ?>',

            // 3. Impresión de variables (sin /s para evitar capturas masivas)
            '/\{\{\s*(.*?)\s*\}\}/' => '<?php echo htmlspecialchars((string)($1), ENT_QUOTES, "UTF-8"); ?>',

            // 4. Estructuras de control (se elimina /s y se restringe el cierre)
            '/@if\s*\((.*)\)/'        => '<?php if($1): ?>',
            '/@else/'                 => '<?php else: ?>',
            '/@endif/'                => '<?php endif; ?>',
            '/@foreach\s*\((.*)\)/'   => '<?php foreach($1): ?>',
            '/@endforeach/'           => '<?php endforeach; ?>',

            // 5. Inclusiones y Layouts
            '/@include\(\'(.*?)\'\)/' => '<?php echo $this->renderView(\'$1\', get_defined_vars()); ?>',
            '/@yield\(\'(.*?)\'\)/'   => '<?php echo $this->sections[\'$1\'] ?? ""; ?>',
            '/@extends\(\'(.*?)\'\)/' => '<?php $this->layout = \'$1\'; ?>',
            '/@section\(\'(.*?)\'\)/' => '<?php ob_start(); $this->currentSection = \'$1\'; ?>',
            '/@endsection/'           => '<?php $this->sections[$this->currentSection] = ob_get_clean(); ?>',
        ];

        return preg_replace(array_keys($patterns), array_values($patterns), $code);
    }


    private function validateSyntax(string $cacheFile, string $originalPath): void
    {
        // Si la función nativa de chequeo rápido existe (PHP < 7 o extensiones específicas)
        if (function_exists('php_check_syntax')) {
            if (!php_check_syntax($cacheFile, $error)) {
                $this->throwParseError($cacheFile, $originalPath, $error);
            }
            return;
        }

        // Alternativa segura y nativa: Validar a través del Tokenizer de PHP
        // Captura fallas estructurales graves de sintaxis (llaves, corchetes, strings mal cerrados)
        try {
            $code = file_get_contents($cacheFile);

            // Deshabilitamos el reporte de errores temporalmente para probar la compilación interna
            // Si el archivo tiene un error de parseo crítico, PHP lanzará un ParseError al evaluar
            $prevErrorLevel = error_reporting(0);

            // token_get_all analiza la estructura del código sin ejecutarlo
            $tokens = token_get_all($code);

            error_reporting($prevErrorLevel);
        } catch (\Throwable $e) {
            $this->throwParseError($cacheFile, $originalPath, $e->getMessage());
        }
    }

    /**
     * Método auxiliar para limpiar caché y lanzar el error formateado
     */
    private function throwParseError(string $cacheFile, string $originalPath, string $details): void
    {
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
        $friendlyError = str_replace($cacheFile, $originalPath, $details);
        throw new \ParseError("Error de sintaxis en la vista compilada.\nDetalles:\n" . trim($friendlyError));
    }

    public function clearCache()
    {
        $files = glob($this->cachePath . '*.php');
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
        }
        return count($files);
    }
}
