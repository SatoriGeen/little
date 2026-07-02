<style>
    .dev-search-box   { background: var(--blanco); padding: 25px; border-radius: 8px; border: 1px solid var(--gris-borde); margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .dev-venta-card   { background: var(--blanco); border: 1px solid var(--gris-borde); border-radius: 8px; padding: 20px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .dev-venta-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; padding-bottom: 12px; border-bottom: 1px solid var(--gris-borde); flex-wrap: wrap; gap: 10px; }
    .dev-venta-folio  { font-size: 1.4rem; font-weight: bold; color: var(--gris-oscuro); }
    .dev-venta-meta   { font-size: 0.85rem; color: #64748B; }
    .dev-meta-grid    { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 15px; }
    .dev-meta-item    { background: var(--fondo); padding: 10px; border-radius: 6px; }
    .dev-meta-label   { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748B; font-weight: bold; margin-bottom: 3px; }
    .dev-meta-val     { font-size: 1rem; font-weight: bold; color: var(--gris-oscuro); }

    .dev-items-table th { font-size: 11px; padding: 10px 12px; }
    .dev-items-table td { padding: 10px 12px; font-size: 0.88rem; }
    .qty-input        { width: 75px; text-align: center; }
    .dev-total-box    { background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 6px; padding: 12px 16px; margin-top: 12px; display: flex; justify-content: space-between; align-items: center; }
    .dev-total-lbl    { font-weight: bold; color: #065F46; font-size: 0.9rem; }
    .dev-total-val    { font-size: 1.2rem; font-weight: bold; color: #059669; }

    .historial-dev    { margin-top: 30px; }
    .no-disponible    { color: #94A3B8; font-style: italic; font-size: 12px; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2 style="margin:0;color:var(--gris-oscuro);">↩️ Devoluciones y Cambios</h2>
        <p style="margin:5px 0 0;color:#64748B;font-size:0.9rem;">Busca una venta por folio para procesar una devolución parcial o total.</p>
    </div>
</div>

<!-- BUSCADOR DE FOLIO O PRODUCTO -->
<div class="dev-search-box">
    <form method="GET" action="index.php" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <input type="hidden" name="ruta" value="devoluciones">
        <div style="flex:1;min-width:250px;">
            <label style="display:block;font-size:0.8rem;font-weight:bold;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;color:var(--gris-oscuro);">
                Buscar por Folio o Nombre de Producto
            </label>
            <input type="text" name="buscar" id="buscar" placeholder="Ej. 42, Coca Cola, Sabritas..."
                   value="<?php echo htmlspecialchars($_GET['buscar'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   style="width:100%;font-size:1.1rem;padding:12px;" autofocus>
        </div>
        <button type="submit" class="btn-secundario" style="padding:12px 24px;height:50px;">
            🔍 Buscar
        </button>
        <?php if (!empty($_GET['folio']) || !empty($_GET['buscar'])): ?>
            <a href="index.php?ruta=devoluciones" class="btn-outline" style="padding:12px;height:50px;display:flex;align-items:center;">✖</a>
        <?php endif; ?>
    </form>
</div>

<!-- LISTA DE VENTAS RECIENTES (Si no hay una venta específica seleccionada) -->
<?php if (empty($venta) && isset($ventas_recientes)): ?>
    <h3 style="color:var(--gris-oscuro);margin-bottom:15px;margin-top:0;">🛍️ Últimas Ventas <?php echo !empty($_GET['buscar']) ? '(Resultados de búsqueda)' : ''; ?></h3>
    <?php if (empty($ventas_recientes)): ?>
        <p style="color:#64748B;">No se encontraron ventas recientes.</p>
    <?php else: ?>
        <table style="margin-bottom: 30px;">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha y Hora</th>
                    <th>Cajero</th>
                    <th>Artículos (Resumen)</th>
                    <th>Total</th>
                    <th style="text-align:center;">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas_recientes as $vr): ?>
                    <tr>
                        <td style="font-weight:bold;color:var(--gris-oscuro);">#<?php echo str_pad((int)$vr['id_venta'],5,'0',STR_PAD_LEFT); ?></td>
                        <td style="font-size:12px;"><?php echo date('d/m/Y h:i A', strtotime($vr['fecha'])); ?></td>
                        <td><?php echo htmlspecialchars($vr['cajero'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td style="font-size:12px;color:#64748B;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($vr['resumen_productos'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($vr['resumen_productos'], ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td style="font-weight:bold;">$<?php echo number_format($vr['total'],2); ?></td>
                        <td style="text-align:center;">
                            <a href="index.php?ruta=devoluciones&folio=<?php echo (int)$vr['id_venta']; ?>" class="btn-primario" style="padding:6px 12px;font-size:0.85rem;display:inline-block;text-decoration:none;">
                                ➔ Devolver
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

<!-- MENSAJE DE ERROR EN BÚSQUEDA -->
<?php if (isset($error)): ?>
    <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<!-- RESULTADO DE LA VENTA ENCONTRADA -->
<?php if ($venta && !empty($detalle)): ?>
<div class="dev-venta-card">
    <div class="dev-venta-header">
        <div>
            <div class="dev-venta-folio">
                Folio #<?php echo str_pad((int)$venta['id_venta'], 5, '0', STR_PAD_LEFT); ?>
            </div>
            <div class="dev-venta-meta">
                <?php echo date('d/m/Y h:i A', strtotime($venta['fecha'])); ?> &nbsp;|&nbsp;
                Cajero: <?php echo htmlspecialchars($venta['cajero'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
        <div>
            <?php $mp = $venta['metodo_pago'] ?? 'efectivo'; ?>
            <span style="background:<?php echo $mp==='tarjeta' ? '#EDE9FE' : '#D1FAE5'; ?>;
                         color:<?php echo $mp==='tarjeta' ? '#5B21B6' : '#065F46'; ?>;
                         padding:5px 12px;border-radius:12px;font-size:0.8rem;font-weight:bold;">
                <?php echo $mp === 'tarjeta' ? '💳 Tarjeta' : '💵 Efectivo'; ?>
            </span>
        </div>
    </div>

    <!-- Resumen de la venta -->
    <div class="dev-meta-grid">
        <div class="dev-meta-item">
            <div class="dev-meta-label">Total Venta</div>
            <div class="dev-meta-val">$<?php echo number_format($venta['total'], 2); ?></div>
        </div>
        <?php if (($venta['comision'] ?? 0) > 0): ?>
        <div class="dev-meta-item">
            <div class="dev-meta-label">Comisión</div>
            <div class="dev-meta-val" style="color:#DC2626;">-$<?php echo number_format($venta['comision'], 2); ?></div>
        </div>
        <div class="dev-meta-item">
            <div class="dev-meta-label">Ingreso Neto</div>
            <div class="dev-meta-val" style="color:#059669;">$<?php echo number_format($venta['total_neto'] ?? $venta['total'], 2); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Formulario de devolución -->
    <form action="index.php?ruta=devoluciones_procesar" method="POST" id="formDevolucion">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id_venta" value="<?php echo (int)$venta['id_venta']; ?>">

        <table class="dev-items-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th style="text-align:right;">Vendido</th>
                    <th style="text-align:right;">Ya Devuelto</th>
                    <th style="text-align:right;">Disponible</th>
                    <th style="text-align:right;">P. Unitario</th>
                    <th style="text-align:center;">Cantidad a Devolver</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalle as $linea):
                    $disponible = (float)$linea['cantidad'] - (float)$linea['ya_devuelto'];
                    $ya_dev     = (float)$linea['ya_devuelto'];
                ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($linea['nombre'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                    <td style="text-align:right;"><?php echo round($linea['cantidad'],2); ?></td>
                    <td style="text-align:right;color:<?php echo $ya_dev > 0 ? '#DC2626' : '#94A3B8'; ?>;">
                        <?php echo $ya_dev > 0 ? round($ya_dev,2) : '-'; ?>
                    </td>
                    <td style="text-align:right;font-weight:bold;color:<?php echo $disponible > 0 ? 'var(--gris-oscuro)' : '#94A3B8'; ?>;">
                        <?php echo round($disponible, 2); ?>
                    </td>
                    <td style="text-align:right;">$<?php echo number_format($linea['precio_unitario'], 2); ?></td>
                    <td style="text-align:center;">
                        <?php if ($disponible > 0): ?>
                            <input type="number"
                                   name="cantidades[<?php echo (int)$linea['id_producto']; ?>]"
                                   class="qty-input dev-qty"
                                   min="0" max="<?php echo $disponible; ?>"
                                   step="0.01"
                                   value="0"
                                   data-precio="<?php echo $linea['precio_unitario']; ?>">
                        <?php else: ?>
                            <span class="no-disponible">Agotado</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Cálculo de reembolso en tiempo real -->
        <div class="dev-total-box">
            <div class="dev-total-lbl">💰 Total a Reembolsar:</div>
            <div class="dev-total-val" id="totalReembolso">$0.00</div>
        </div>

        <div style="margin-top:15px;display:flex;flex-direction:column;gap:8px;">
            <label style="font-size:0.8rem;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;color:var(--gris-oscuro);">
                Motivo de la Devolución (opcional)
            </label>
            <input type="text" name="motivo" maxlength="255"
                   placeholder="Ej. Producto defectuoso, cambio de talla, error en pedido..."
                   style="width:100%;padding:12px;">
        </div>

        <div style="margin-top:15px;display:flex;justify-content:flex-end;gap:10px;">
            <a href="index.php?ruta=devoluciones" class="btn-outline" style="padding:10px 18px;">Cancelar</a>
            <button type="submit" class="btn-primario" id="btnProcesar" disabled
                    style="padding:10px 25px;background:#059669;">
                ↩️ Procesar Devolución ($<span id="btnTotal">0.00</span>)
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- HISTORIAL RECIENTE DE DEVOLUCIONES -->
<?php if (!empty($historial)): ?>
<div class="historial-dev">
    <h3 style="color:var(--gris-oscuro);margin-bottom:10px;">📋 Historial Reciente de Devoluciones</h3>
    <table>
        <thead>
            <tr>
                <th>#Dev</th>
                <th>Venta Orig.</th>
                <th>Fecha</th>
                <th>Cajero</th>
                <th>Motivo</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($historial as $dev): ?>
                <tr>
                    <td style="color:#94A3B8;font-size:12px;">#<?php echo str_pad((int)$dev['id_devolucion'],4,'0',STR_PAD_LEFT); ?></td>
                    <td>
                        <a href="index.php?ruta=devoluciones&amp;folio=<?php echo (int)$dev['id_venta']; ?>"
                           style="color:var(--azul-fuerte);font-weight:bold;text-decoration:none;">
                            #<?php echo str_pad((int)$dev['id_venta'],5,'0',STR_PAD_LEFT); ?>
                        </a>
                    </td>
                    <td style="font-size:12px;"><?php echo date('d/m/Y h:i A', strtotime($dev['fecha'])); ?></td>
                    <td><?php echo htmlspecialchars($dev['cajero'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td style="color:#64748B;font-size:12px;">
                        <?php echo $dev['motivo'] ? htmlspecialchars($dev['motivo'], ENT_QUOTES, 'UTF-8') : '<em>Sin motivo</em>'; ?>
                    </td>
                    <td><strong style="color:#DC2626;">-$<?php echo number_format($dev['total_devuelto'], 2); ?></strong></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<script>
// Calcular el total a reembolsar dinámicamente
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.dev-qty');
    const btnProcesar = document.getElementById('btnProcesar');
    const spanTotal   = document.getElementById('btnTotal');
    const divTotal    = document.getElementById('totalReembolso');

    function calcular() {
        let total = 0;
        inputs.forEach(function(input) {
            const cant   = parseFloat(input.value) || 0;
            const precio = parseFloat(input.dataset.precio) || 0;
            total += cant * precio;
        });
        const fmt = total.toFixed(2);
        if (divTotal)    divTotal.textContent = '$' + fmt;
        if (spanTotal)   spanTotal.textContent = fmt;
        if (btnProcesar) btnProcesar.disabled = (total <= 0);
    }

    inputs.forEach(function(input) {
        input.addEventListener('input', calcular);
    });
    calcular(); // Inicializar
});
</script>
