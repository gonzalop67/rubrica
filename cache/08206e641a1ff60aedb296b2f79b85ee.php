<?php
// 1. Buscamos automáticamente cuál es el arreglo de la paginación activa
$source = null;
foreach (get_defined_vars() as $key => $value) {
    if (is_array($value) && isset($value['current_page']) && isset($value['total']) && isset($value['last_page'])) {
        $source = $value;
        break;
    }
}

// 2. Si se encontró la paginación, limpiamos el entorno y generamos las URLs de forma segura
if ($source): 
    // Obtenemos la URL actual limpia del navegador de forma aislada
    $paginationUrlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Capturamos los parámetros de búsqueda actuales (como ?search=)
    $cleanParams = $_GET;
    unset($cleanParams['page']); // Quitamos la página para que no se duplique
    $paginationQueryString = count($cleanParams) > 0 ? '&' . http_build_query($cleanParams) : '';

    // Re-calculamos los enlaces de forma segura e independiente en la vista
    $currentPage = (int)$source['current_page'];
    $lastPage = (int)$source['last_page'];

    $prevLink = $currentPage > 1 ? $paginationUrlPath . "?page=" . ($currentPage - 1) . $paginationQueryString : '#';
    $nextLink = $currentPage < $lastPage ? $paginationUrlPath . "?page=" . ($currentPage + 1) . $paginationQueryString : '#';
?>
<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        Mostrando de <?= (int)$source['from'] ?> a <?= (int)$source['to'] ?> de <?= (int)$source['total'] ?> registros
    </div>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-end mb-0">
            
            <!-- Botón Anterior -->
            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $prevLink ?>" aria-label="Previous">
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>

            <!-- Números de Página -->
            <?php for ($i = 1; $i <= $lastPage; $i++): ?>
                <li class="page-item <?= $currentPage === $i ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $paginationUrlPath ?>?page=<?= $i ?><?= $paginationQueryString ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- Botón Siguiente -->
            <li class="page-item <?= $currentPage >= $lastPage ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $nextLink ?>" aria-label="Next">
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
            
        </ul>
    </nav>
</div>
<?php endif; ?>
