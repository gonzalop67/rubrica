<table>
        <thead>
            <!-- FILA 1: Bimestres / Periodos Grandes -->
            <tr>
                <th scope="col" rowspan="3">Nro.</th>
                <th scope="col" rowspan="3">Nómina</th>
                <?php foreach ($estructura as $p): ?>
                    <th scope="col" colspan="<?php echo $p['total_columnas']; ?>"><?php echo strtoupper($p['nombre']); ?></th>
                <?php endforeach; ?>
                <th scope="col" rowspan="3">PROMEDIO FINAL</th>
            </tr>

            <!-- FILA 2: Aportes (Parciales, Quimestres, Exámenes) -->
            <tr>
                <?php foreach ($estructura as $p): ?>
                    <?php foreach ($p['aportes'] as $a): ?>
                        <?php
                        // Calcular el colspan de este aporte
                        $colspan_aporte = count($a['insumos']);
                        if ($a['tipo'] == 1) $colspan_aporte++; // Más su promedio
                        ?>
                        <th colspan="<?php echo $colspan_aporte; ?>"><?php echo strtoupper($a['nombre']); ?></th>
                    <?php endforeach; ?>
                    <th rowspan="2">PROMEDIO</th> <!-- Celda de promedio del periodo -->
                <?php endforeach; ?>
            </tr>

            <!-- FILA 3: Insumos directos (Tareas, Lecciones, etc.) -->
            <tr>
                <?php foreach ($estructura as $p): ?>
                    <?php foreach ($p['aportes'] as $a): ?>
                        <?php foreach ($a['insumos'] as $insumo): ?>
                            <th><?php echo strtoupper($insumo['nombre']); ?></th>
                        <?php endforeach; ?>
                        <?php if ($a['tipo'] == 1): ?>
                            <th>PROM.</th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php
            $contador_nro = 1;
            while ($estudiante = $db->fetch_object($res_estudiantes)):
                $id_est_actual = $estudiante->id_estudiante;
            ?>
                <tr>
                    <td><?php echo $contador_nro++; ?></td>
                    <th scope="row" class="text-left"><?php echo $estudiante->nombre; ?></th>

                    <?php
                    // Recorremos la misma estructura para pintar las notas de este estudiante
                    foreach ($estructura as $p):
                        foreach ($p['aportes'] as $a):
                            foreach ($a['insumos'] as $insumo):
                                // Aquí debes hacer tu consulta para obtener la nota real del insumo
                                // Ejemplo ficticio: $nota = obtenerNotaInsumo($id_est_actual, $insumo['id']);

                                // $nota = obtenerNotaInsumo($id_est_actual, );

                                $nota = rand(5, 10); // Simulación de nota para el ejemplo

                                $clase_rojo = ($nota < 7) ? 'class="rojo"' : '';
                                echo "<td $clase_rojo>$nota</td>";
                            endforeach;

                            if ($a['tipo'] == 1):
                                // Celda para el promedio del parcial
                                echo "<td><b>9.0</b></td>";
                            endif;
                        endforeach;

                        // Celda para el promedio del periodo completo
                        echo "<td><b>9.5</b></td>";
                    endforeach;
                    ?>

                    <!-- Celda del promedio final general -->
                    <td><b>9.3</b></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>