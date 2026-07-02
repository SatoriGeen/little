<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" defer></script>

<style>
    .kpi-grid   { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 25px; }
    .kpi-card   { background: var(--blanco); padding: 18px; border-radius: 8px; border: 1px solid var(--gris-borde); box-shadow: 0 2px 8px rgba(0,0,0,0.04); border-left: 5px solid var(--azul-fuerte); }
    .kpi-card.efectivo  { border-left-color: #059669; }
    .kpi-card.tarjeta   { border-left-color: #7C3AED; }
    .kpi-card.comision  { border-left-color: #DC2626; }
    .kpi-card.devolucion{ border-left-color: #D97706; }
    .kpi-title  { font-size: 0.75rem; color: #64748B; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 5px; }
    .kpi-value  { font-size: 1.6rem; color: var(--gris-oscuro); font-weight: 800; margin: 0; }
    .kpi-sub    { font-size: 0.75rem; color: #94A3B8; margin-top: 3px; }

    .charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 25px; }
    @media(max-width:900px) { .charts-grid { grid-template-columns: 1fr; } }
    .chart-box  { background: var(--blanco); padding: 20px; border-radius: 8px; border: 1px solid var(--gris-borde); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .chart-box h3 { margin-top:0; color: var(--gris-oscuro); font-size:1rem; border-bottom: 1px solid var(--fondo); padding-bottom:10px; }

    /* Pagos breakdown */
    .pagos-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 25px; }
    .pago-card  { background: var(--blanco); padding: 18px; border-radius: 8px; border: 1px solid var(--gris-borde); }
    .pago-card h4 { margin: 0 0 12px; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .pago-row   { display: flex; justify-content: space-between; font-size: 0.85rem; padding: 5px 0; border-bottom: 1px solid var(--fondo); }
    .pago-row:last-child { border-bottom: none; font-weight: bold; }

    .filtro-form { background:var(--blanco); padding:15px; border-radius:8px; border:1px solid var(--gris-borde); margin-bottom:25px; display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap; }
</style>

<div style="margin-bottom:20px;">
    <h2 style="margin:0;color:var(--gris-oscuro);">📊 Panel Analítico</h2>
    <p style="color:#64748B;margin-top:5px;font-size:0.9rem;">Métricas de ventas, desglose por método de pago y análisis de devoluciones.</p>
</div>

<!-- Filtro por fechas -->
<form class="filtro-form" method="GET" action="index.php">
    <input type="hidden" name="ruta" value="reportes">
    <div>
        <label style="display:block;font-size:11px;font-weight:bold;margin-bottom:4px;">Desde:</label>
        <input type="date" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div>
        <label style="display:block;font-size:11px;font-weight:bold;margin-bottom:4px;">Hasta:</label>
        <input type="date" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <button type="submit" class="btn-primario">Filtrar</button>
    <?php if(isset($fecha_inicio)): ?>
        <a href="index.php?ruta=reportes" class="btn-outline" style="padding:10px 15px;">✖ Todo el período</a>
    <?php endif; ?>
</form>

<!-- KPIs principales -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-title">Ingresos Hoy (Neto)</div>
        <div class="kpi-value">$<?php echo number_format($hoy, 2); ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-title">Ingresos Brutos (Periodo)</div>
        <div class="kpi-value">$<?php echo number_format($kpis['ingresos'] ?? 0, 2); ?></div>
        <div class="kpi-sub"><?php echo (int)($kpis['total_ventas']??0); ?> transacciones</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-title">Ingresos Netos (Periodo)</div>
        <div class="kpi-value">$<?php echo number_format($kpis['ingresos_netos'] ?? 0, 2); ?></div>
        <div class="kpi-sub">Después de comisiones</div>
    </div>
    <div class="kpi-card comision">
        <div class="kpi-title">💳 Comisiones Pagadas</div>
        <div class="kpi-value" style="color:#DC2626;">$<?php echo number_format($kpis['total_comisiones'] ?? 0, 2); ?></div>
        <div class="kpi-sub">Costo bancario / tarjeta</div>
    </div>
    <div class="kpi-card devolucion">
        <div class="kpi-title">↩️ Total Devoluciones</div>
        <div class="kpi-value" style="color:#D97706;">$<?php echo number_format($total_devoluciones ?? 0, 2); ?></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-title">Ticket Promedio</div>
        <div class="kpi-value">$<?php echo number_format($kpis['ticket_promedio'] ?? 0, 2); ?></div>
    </div>
</div>

<!-- Desglose por método de pago -->
<div class="pagos-grid">
    <?php
        $ef = $resumen_pagos['efectivo'] ?? null;
        $tj = $resumen_pagos['tarjeta']  ?? null;
    ?>
    <div class="pago-card" style="border-top:4px solid #059669;">
        <h4 style="color:#059669;">💵 Efectivo</h4>
        <?php if ($ef): ?>
            <div class="pago-row"><span>Transacciones:</span><span><?php echo (int)$ef['cantidad_transacciones']; ?></span></div>
            <div class="pago-row"><span>Total recibido:</span><span>$<?php echo number_format($ef['total_bruto'],2); ?></span></div>
            <div class="pago-row"><span>Sin comisión:</span><span style="color:#059669;">$0.00</span></div>
            <div class="pago-row"><span>Ingreso neto:</span><span style="color:#059669;">$<?php echo number_format($ef['total_neto'],2); ?></span></div>
        <?php else: ?>
            <p style="color:#94A3B8;font-size:0.85rem;">Sin ventas en efectivo en este periodo.</p>
        <?php endif; ?>
    </div>

    <div class="pago-card" style="border-top:4px solid #7C3AED;">
        <h4 style="color:#7C3AED;">💳 Tarjeta</h4>
        <?php if ($tj): ?>
            <div class="pago-row"><span>Transacciones:</span><span><?php echo (int)$tj['cantidad_transacciones']; ?></span></div>
            <div class="pago-row"><span>Total cobrado:</span><span>$<?php echo number_format($tj['total_bruto'],2); ?></span></div>
            <div class="pago-row"><span>Comisión bancaria:</span><span style="color:#DC2626;">-$<?php echo number_format($tj['total_comisiones'],2); ?></span></div>
            <div class="pago-row"><span>Ingreso neto:</span><span style="color:#7C3AED;">$<?php echo number_format($tj['total_neto'],2); ?></span></div>
        <?php else: ?>
            <p style="color:#94A3B8;font-size:0.85rem;">Sin ventas con tarjeta en este periodo.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Gráficas -->
<div class="charts-grid">
    <div class="chart-box">
        <h3>📈 Tendencia de Ventas (Últimos 7 días)</h3>
        <canvas id="lineChart" height="90"></canvas>
    </div>
    <div class="chart-box">
        <h3>⭐ Top 5 Productos</h3>
        <canvas id="doughnutChart" height="190"></canvas>
    </div>
</div>

<!-- Historial de ventas -->
<h3 style="color:var(--gris-oscuro);margin-bottom:10px;">📋 Historial de Ventas</h3>
<table>
    <thead>
        <tr>
            <th>Folio</th>
            <th>Fecha</th>
            <th>Cajero</th>
            <th>Método</th>
            <th>Total</th>
            <th>Comisión</th>
            <th>Neto</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($historial)): ?>
            <tr><td colspan="8" style="text-align:center;padding:30px;color:#94A3B8;">Sin ventas en este periodo.</td></tr>
        <?php else: ?>
            <?php foreach($historial as $venta): ?>
                <tr>
                    <td style="font-size:12px;"><strong>#<?php echo str_pad((int)$venta['id_venta'],5,'0',STR_PAD_LEFT); ?></strong></td>
                    <td style="font-size:12px;"><?php echo date('d/m/Y h:i A', strtotime($venta['fecha'])); ?></td>
                    <td><?php echo htmlspecialchars($venta['cajero'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <?php $mp = $venta['metodo_pago'] ?? 'efectivo'; ?>
                        <span style="font-size:11px;padding:3px 8px;border-radius:10px;font-weight:bold;
                            <?php echo $mp==='tarjeta' ? 'background:#EDE9FE;color:#5B21B6;' : 'background:#D1FAE5;color:#065F46;'; ?>">
                            <?php echo $mp==='tarjeta' ? '💳 Tarjeta' : '💵 Efectivo'; ?>
                        </span>
                    </td>
                    <td>$<?php echo number_format($venta['total'],2); ?></td>
                    <td style="color:#DC2626;font-size:12px;">
                        <?php echo ($venta['comision']>0) ? '-$'.number_format($venta['comision'],2) : '-'; ?>
                    </td>
                    <td><strong style="color:var(--azul-fuerte);">$<?php echo number_format($venta['total_neto']??$venta['total'],2); ?></strong></td>
                    <td>
                        <?php 
                        $estado = $venta['estado'] ?? 'Normal';
                        if ($estado === 'Devuelta'): ?>
                            <span style="font-size:11px;padding:3px 8px;border-radius:10px;font-weight:bold;background:#FEE2E2;color:#991B1B;">Devuelta</span>
                        <?php elseif ($estado === 'Parcial'): ?>
                            <span style="font-size:11px;padding:3px 8px;border-radius:10px;font-weight:bold;background:#FEF3C7;color:#92400E;">Parcial</span>
                        <?php else: ?>
                            <span style="font-size:11px;padding:3px 8px;border-radius:10px;font-weight:bold;background:#F1F5F9;color:#64748B;">Completada</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (isset($total_paginas) && $total_paginas > 1): ?>
    <div class="paginacion" style="margin-top:20px;">
        <?php for($i=1;$i<=$total_paginas;$i++): ?>
            <a href="index.php?ruta=reportes&amp;fecha_inicio=<?php echo urlencode($fecha_inicio??''); ?>&amp;fecha_fin=<?php echo urlencode($fecha_fin??''); ?>&amp;pagina=<?php echo $i; ?>"
               class="<?php echo ($i==$pagina_actual)?'activo':''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ventasDias  = <?php echo json_encode($ventas7Dias,  JSON_HEX_TAG | JSON_HEX_AMP); ?>;
    const topProds    = <?php echo json_encode($topProductos, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

    // Gráfico de líneas (bruto vs neto)
    new Chart(document.getElementById('lineChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ventasDias.map(d => d.dia),
            datasets: [
                {
                    label: 'Total Bruto ($)',
                    data: ventasDias.map(d => d.total_dia),
                    borderColor: '#0D47A1', backgroundColor: 'rgba(13,71,161,0.1)',
                    borderWidth: 2, tension: 0.3, fill: true
                },
                {
                    label: 'Ingreso Neto ($)',
                    data: ventasDias.map(d => d.neto_dia),
                    borderColor: '#059669', backgroundColor: 'rgba(5,150,105,0.05)',
                    borderWidth: 2, tension: 0.3, fill: true, borderDash: [5,3]
                }
            ]
        },
        options: { responsive:true, plugins: { legend: { position:'top', labels:{font:{size:11}} } } }
    });

    // Dona - top productos
    new Chart(document.getElementById('doughnutChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: topProds.map(p => p.nombre),
            datasets: [{
                data: topProds.map(p => p.total_vendido),
                backgroundColor: ['#0D47A1','#2563EB','#7C3AED','#059669','#D97706'],
                borderWidth: 0
            }]
        },
        options: { responsive:true, cutout:'65%', plugins:{legend:{position:'bottom',labels:{font:{size:10}}}} }
    });
});
</script>