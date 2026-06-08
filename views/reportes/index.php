<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Estilos específicos del Dashboard Analítico */
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px; }
    .kpi-card { background: var(--blanco); padding: 20px; border-radius: 8px; border: 1px solid var(--gris-borde); box-shadow: 0 2px 8px rgba(0,0,0,0.04); border-left: 5px solid var(--azul-fuerte); }
    .kpi-card.success { border-left-color: var(--success, #10B981); }
    .kpi-card.warning { border-left-color: #F59E0B; }
    
    .kpi-title { font-size: 0.85rem; color: var(--gris-texto); text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 5px; }
    .kpi-value { font-size: 2rem; color: var(--gris-oscuro); font-weight: 800; margin: 0; }
    
    .charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 25px; }
    @media(max-width: 900px) { .charts-grid { grid-template-columns: 1fr; } }
    .chart-container { background: var(--blanco); padding: 20px; border-radius: 8px; border: 1px solid var(--gris-borde); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .chart-container h3 { margin-top: 0; color: var(--gris-oscuro); font-size: 1.1rem; border-bottom: 1px solid var(--fondo); padding-bottom: 10px; }
    
    .filtro-form { background: var(--blanco); padding: 15px; border-radius: 8px; border: 1px solid var(--gris-borde); margin-bottom: 25px; display: flex; gap: 15px; align-items: flex-end; }
</style>

<div style="margin-bottom: 20px;">
    <h2 style="margin: 0; color: var(--gris-oscuro);">Panel Analítico</h2>
    <p style="color: var(--gris-texto); margin-top: 5px;">Métricas de rendimiento y registro de ventas.</p>
</div>

<form class="filtro-form" method="GET" action="index.php">
    <input type="hidden" name="ruta" value="reportes">
    <div>
        <label style="display:block; font-size:12px; font-weight:bold; color:var(--gris-texto); margin-bottom:5px;">Desde:</label>
        <input type="date" name="fecha_inicio" class="form-input" value="<?php echo $_GET['fecha_inicio'] ?? ''; ?>" required>
    </div>
    <div>
        <label style="display:block; font-size:12px; font-weight:bold; color:var(--gris-texto); margin-bottom:5px;">Hasta:</label>
        <input type="date" name="fecha_fin" class="form-input" value="<?php echo $_GET['fecha_fin'] ?? ''; ?>" required>
    </div>
    <button type="submit" class="btn-primario">Filtrar Ventas</button>
    <?php if(isset($_GET['fecha_inicio'])): ?>
        <a href="index.php?ruta=reportes" class="btn-outline" style="padding: 10px 15px;">Limpiar Filtro</a>
    <?php endif; ?>
</form>

<div class="kpi-grid">
    <div class="kpi-card success">
        <div class="kpi-title">Ingresos Hoy</div>
        <div class="kpi-value">$<?php echo number_format($hoy, 2); ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-title">Ingresos (Periodo Seleccionado)</div>
        <div class="kpi-value">$<?php echo number_format($kpis['ingresos'], 2); ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-title">Transacciones</div>
        <div class="kpi-value"><?php echo $kpis['total_ventas']; ?></div>
    </div>
    <div class="kpi-card warning">
        <div class="kpi-title">Ticket Promedio</div>
        <div class="kpi-value">$<?php echo number_format($kpis['ticket_promedio'], 2); ?></div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-container">
        <h3>📈 Tendencia de Ventas (Últimos 7 días)</h3>
        <canvas id="lineChart" height="100"></canvas>
    </div>
    <div class="chart-container">
        <h3>⭐ Top 5 Productos Estrella</h3>
        <canvas id="doughnutChart" height="200"></canvas>
    </div>
</div>

<h3 style="color: var(--gris-oscuro); margin-bottom: 10px;">Historial Detallado</h3>
<table>
    <thead>
        <tr>
            <th>Folio / ID</th>
            <th>Fecha y Hora</th>
            <th>Cajero</th>
            <th>Total Venta</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($historial)): ?>
            <tr><td colspan="4" style="text-align: center;">No hay ventas registradas en este periodo.</td></tr>
        <?php else: ?>
            <?php foreach($historial as $venta): ?>
                <tr>
                    <td><strong>#<?php echo str_pad($venta['id_venta'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                    <td><?php echo date('d/m/Y h:i A', strtotime($venta['fecha'])); ?></td>
                    <td><?php echo htmlspecialchars($venta['cajero']); ?></td>
                    <td><strong style="color: var(--azul-fuerte);">$<?php echo number_format($venta['total'], 2); ?></strong></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (isset($total_paginas) && $total_paginas > 1): ?>
    <div class="paginacion" style="margin-top: 20px;">
        <?php for($i = 1; $i <= $total_paginas; $i++): ?>
            <a href="index.php?ruta=reportes&fecha_inicio=<?php echo urlencode($fecha_inicio ?? ''); ?>&fecha_fin=<?php echo urlencode($fecha_fin ?? ''); ?>&pagina=<?php echo $i; ?>" 
               class="<?php echo ($i == $pagina_actual) ? 'activo' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<script>
    // Extraemos datos de PHP a Javascript
    const ventasDiasData = <?php echo json_encode($ventas7Dias); ?>;
    const topProductosData = <?php echo json_encode($topProductos); ?>;

    // --- GRÁFICO DE LÍNEAS (TENDENCIA) ---
    const ctxLine = document.getElementById('lineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ventasDiasData.map(d => d.dia), // Fechas en el eje X
            datasets: [{
                label: 'Ingresos por Día ($)',
                data: ventasDiasData.map(d => d.total_dia), // Totales en el eje Y
                borderColor: '#0D47A1',
                backgroundColor: 'rgba(13, 71, 161, 0.1)',
                borderWidth: 3,
                tension: 0.3, // Curvas suaves
                fill: true
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // --- GRÁFICO DE DONA (TOP PRODUCTOS) ---
    const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: topProductosData.map(p => p.nombre),
            datasets: [{
                data: topProductosData.map(p => p.total_vendido),
                backgroundColor: [ '#0D47A1', '#2563EB', '#60A5FA', '#93C5FD', '#CBD5E1' ],
                borderWidth: 0
            }]
        },
        options: { responsive: true, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
    });
</script>