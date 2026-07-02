<style>
    .dashboard-layout { display: flex; gap: 25px; align-items: flex-start; width: 100%; }
    .productos-panel  { flex: 2; min-width: 0; }
    .ticket-panel     { flex: 0 0 320px; min-width: 280px; background: var(--blanco); padding: 20px; border-radius: 8px; border: 1px solid var(--gris-borde); position: sticky; top: 0; box-shadow: 0 4px 6px rgba(0,0,0,0.05); max-height: calc(100vh - 100px); overflow-y: auto; }

    /* Widget de alertas de stock */
    .stock-alert-widget { background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 8px; padding: 12px 15px; margin-bottom: 15px; }
    .stock-alert-widget h4 { margin: 0 0 8px 0; color: #92400E; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; }
    .stock-alert-counts { display: flex; gap: 8px; flex-wrap: wrap; }
    .stock-cnt { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; text-decoration: none; }
    .stock-cnt-critico  { background:#FEE2E2; color:#991B1B; }
    .stock-cnt-bajo     { background:#FEF3C7; color:#92400E; }
    .stock-cnt-sin      { background:#1E293B; color:white; }

    /* Selector de método de pago */
    .pago-selector { display: flex; gap: 8px; margin-bottom: 12px; }
    .pago-option { flex: 1; }
    .pago-option input[type="radio"] { display: none; }
    .pago-option label {
        display: flex; flex-direction: column; align-items: center; gap: 3px;
        padding: 10px; border-radius: 6px; border: 2px solid var(--gris-borde);
        cursor: pointer; transition: 0.2s; font-size: 0.8rem; font-weight: bold;
        background: var(--blanco-puro); color: var(--gris-texto); text-align: center;
    }
    .pago-option label .pago-icon { font-size: 1.4rem; }
    .pago-option input:checked + label { border-color: var(--azul-fuerte); background: rgba(13,71,161,0.08); color: var(--azul-fuerte); }
    .pago-option.tarjeta input:checked + label { border-color: #7C3AED; background: rgba(124,58,237,0.08); color: #7C3AED; }

    /* Desglose de comisión */
    .pago-desglose { background: #F8FAFC; border: 1px solid var(--gris-borde); border-radius: 6px; padding: 10px 12px; margin-bottom: 10px; font-size: 0.8rem; }
    .pago-desglose .linea { display: flex; justify-content: space-between; padding: 2px 0; color: var(--gris-texto); }
    .pago-desglose .linea.comision { color: #DC2626; }
    .pago-desglose .linea.neto { color: var(--verde, #059669); font-weight: bold; border-top: 1px solid var(--gris-borde); margin-top: 4px; padding-top: 6px; }
</style>

<!-- TICKET FLASH -->
<?php if (isset($_SESSION['ultimo_ticket'])): 
    $ut = $_SESSION['ultimo_ticket'];
    unset($_SESSION['ultimo_ticket']);
?>
    <div style="background:var(--blanco);border:3px solid var(--azul-fuerte);border-radius:12px;padding:25px;margin-bottom:25px;box-shadow:0 8px 20px rgba(13,71,161,0.15);display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h3 style="margin:0;color:var(--azul-fuerte);font-size:1.6rem;display:flex;align-items:center;gap:10px;">✅ Venta Procesada con Éxito</h3>
            <p style="margin:8px 0 0;color:var(--gris-oscuro);font-size:1.1rem;">
                Se cobraron <strong><?php echo (int)$ut['articulos']; ?> artículos</strong> usando <strong><?php echo $ut['metodo']==='tarjeta' ? '💳 Tarjeta' : '💵 Efectivo'; ?></strong>.
            </p>
            <p style="margin:5px 0 0;color:#64748B;font-size:0.9rem;">
                Si necesitas devolver esto después, busca el ticket por este folio.
            </p>
        </div>
        <div style="text-align:right;">
            <div style="font-size:0.9rem;text-transform:uppercase;color:#64748B;font-weight:bold;letter-spacing:1px;margin-bottom:5px;">Folio del Ticket</div>
            <div style="font-size:3.5rem;font-weight:900;color:var(--gris-oscuro);line-height:1;font-family:monospace;">
                #<?php echo str_pad($ut['id'], 5, '0', STR_PAD_LEFT); ?>
            </div>
            <div style="font-size:1.2rem;font-weight:bold;color:#059669;margin-top:8px;">
                Total: $<?php echo number_format($ut['total'], 2); ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="dashboard-layout">

    <!-- PANEL DE PRODUCTOS -->
    <div class="productos-panel">

        <?php
            // Widget de alertas de stock en dashboard
            $sin   = (int)($resumen_stock['sin_stock'] ?? 0);
            $crit  = (int)($resumen_stock['critico']   ?? 0);
            $bajo  = (int)($resumen_stock['bajo']      ?? 0);
        ?>
        <?php if ($sin + $crit + $bajo > 0): ?>
        <div class="stock-alert-widget">
            <h4>⚠️ Productos que necesitan reposición</h4>
            <div class="stock-alert-counts">
                <?php if ($sin > 0): ?>
                    <a href="index.php?ruta=inventario&amp;stock=sin_stock" class="stock-cnt stock-cnt-sin">
                        🚫 <?php echo $sin; ?> sin stock
                    </a>
                <?php endif; ?>
                <?php if ($crit > 0): ?>
                    <a href="index.php?ruta=inventario&amp;stock=critico" class="stock-cnt stock-cnt-critico">
                        🔴 <?php echo $crit; ?> críticos
                    </a>
                <?php endif; ?>
                <?php if ($bajo > 0): ?>
                    <a href="index.php?ruta=inventario&amp;stock=bajo" class="stock-cnt stock-cnt-bajo">
                        🟡 <?php echo $bajo; ?> bajos
                    </a>
                <?php endif; ?>
                <a href="index.php?ruta=inventario&amp;stock=surtir" class="stock-cnt" style="background:#EDE9FE;color:#5B21B6;">
                    🛒 Ver todos a surtir
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pestañas de departamento -->
        <div style="margin-bottom:15px;display:flex;gap:8px;overflow-x:auto;padding-bottom:4px;">
            <?php foreach ($departamentos as $depto): ?>
                <?php $es_activo = (($id_dept??1) == $depto['id_departamento']); ?>
                <a href="index.php?ruta=dashboard&amp;dept=<?php echo (int)$depto['id_departamento']; ?>"
                   style="flex-shrink:0;min-width:110px;text-align:center;padding:9px;border-radius:4px;text-decoration:none;font-weight:bold;
                   <?php echo $es_activo ? 'background-color:var(--azul-fuerte);color:var(--blanco);' : 'background-color:var(--blanco);color:var(--gris-texto);border:1px solid var(--gris-borde);'; ?>">
                    <?php echo htmlspecialchars($depto['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Buscador -->
        <form method="GET" action="index.php" style="display:flex;gap:8px;margin-bottom:18px;">
            <input type="hidden" name="ruta" value="dashboard">
            <input type="hidden" name="dept" value="<?php echo (int)($id_dept??1); ?>">
            <input type="text" name="buscar" placeholder="Buscar producto..." style="flex:1;"
                   value="<?php echo htmlspecialchars($_GET['buscar'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn-secundario">Buscar</button>
        </form>

        <!-- Tabla de productos -->
        <table>
            <thead>
                <tr><th>Producto</th><th>Precio</th><th>Stock</th><th>Añadir</th></tr>
            </thead>
            <tbody>
                <?php if (empty($productos)): ?>
                    <tr><td colspan="4" style="text-align:center;padding:20px;color:#94A3B8;">Sin resultados.</td></tr>
                <?php else: ?>
                    <?php
                        $umbral_c = defined('STOCK_UMBRAL_CRITICO') ? STOCK_UMBRAL_CRITICO : 5;
                        $umbral_b = defined('STOCK_UMBRAL_BAJO')    ? STOCK_UMBRAL_BAJO    : 15;
                        foreach ($productos as $producto):
                            $stock = (float)$producto['stock'];
                    ?>
                        <tr>
                            <td>
                                <strong style="color:var(--azul-fuerte);">
                                    <?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                </strong><br>
                                <small style="color:#94A3B8;"><?php echo htmlspecialchars($producto['marca'] ?? '', ENT_QUOTES, 'UTF-8'); ?></small>
                            </td>
                            <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                            <td class="<?php echo ($stock <= $umbral_b) ? 'stock-bajo' : ''; ?>">
                                <?php
                                    if ($stock == 0)           echo '<span class="badge-sin_stock">0</span>';
                                    elseif ($stock <= $umbral_c) echo '<span class="badge-critico">' . round($stock,2) . '</span>';
                                    elseif ($stock <= $umbral_b) echo '<span class="badge-bajo">'    . round($stock,2) . '</span>';
                                    else                         echo round($stock, 2);
                                ?>
                            </td>
                            <td>
                                <?php if ($stock > 0): ?>
                                <!-- SEC-09: precio viene de la BD en el controlador, no del form -->
                                <form action="index.php?ruta=agregar_carrito" method="POST" style="display:flex;gap:5px;">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id_producto"     value="<?php echo (int)$producto['id_producto']; ?>">
                                    <input type="hidden" name="id_departamento" value="<?php echo (int)($id_dept??1); ?>">
                                    <input type="number" step="0.01" min="0.01" max="<?php echo $stock; ?>"
                                           name="cantidad" value="1" style="width:65px;">
                                    <button type="submit" class="btn-primario" style="padding:6px 10px;">＋</button>
                                </form>
                                <?php else: ?>
                                    <span style="font-size:11px;color:#94A3B8;font-style:italic;">Sin existencia</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (isset($total_paginas) && $total_paginas > 1): ?>
            <div class="paginacion">
                <?php for($i=1; $i<=$total_paginas; $i++): ?>
                    <a href="index.php?ruta=dashboard&amp;dept=<?php echo $id_dept; ?>&amp;buscar=<?php echo urlencode($busqueda??''); ?>&amp;pagina=<?php echo $i; ?>"
                       class="<?php echo ($i==$pagina_actual)?'activo':''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- PANEL DEL TICKET -->
    <div class="ticket-panel">
        <h2 style="margin-top:0;border-bottom:1px solid var(--gris-borde);padding-bottom:10px;color:var(--gris-oscuro);font-size:1.1rem;">
            🧾 Ticket Actual
        </h2>

        <?php
            $total = 0.0;
            if (!empty($_SESSION['carrito'])):
                foreach ($_SESSION['carrito'] as $item) {
                    $total += $item['precio'] * $item['cantidad'];
                }
        ?>
            <ul style="list-style:none;padding:0;margin:0 0 15px 0;">
                <?php foreach ($_SESSION['carrito'] as $id => $item): ?>
                    <?php $subtotal = $item['precio'] * $item['cantidad']; ?>
                    <li style="border-bottom:1px solid var(--gris-borde);padding:10px 0;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:3px;">
                            <strong style="font-size:0.85rem;color:var(--gris-oscuro);">
                                <?php echo htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                            </strong>
                            <form class="form-eliminar" action="index.php?ruta=eliminar_item" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id"   value="<?php echo (int)$id; ?>">
                                <input type="hidden" name="dept" value="<?php echo (int)($id_dept??1); ?>">
                                <button type="submit" style="background:none;border:none;color:var(--rojo-alerta);font-size:11px;cursor:pointer;font-weight:bold;">✕</button>
                            </form>
                        </div>
                        <div style="font-size:12px;color:#64748B;">
                            <?php echo round($item['cantidad'],2); ?> × $<?php echo number_format($item['precio'],2); ?>
                            <strong style="float:right;color:var(--azul-fuerte);">$<?php echo number_format($subtotal,2); ?></strong>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- NUEVO: Método de Pago + Cálculo de Comisión -->
            <p style="margin:0 0 8px;font-size:0.8rem;font-weight:bold;color:var(--gris-texto);text-transform:uppercase;letter-spacing:0.5px;">Método de Pago</p>
            <div class="pago-selector" id="pagoSelector">
                <div class="pago-option">
                    <input type="radio" name="metodo_pago_display" id="pago_efectivo" value="efectivo" checked>
                    <label for="pago_efectivo">
                        <span class="pago-icon">💵</span>
                        Efectivo
                    </label>
                </div>
                <div class="pago-option tarjeta">
                    <input type="radio" name="metodo_pago_display" id="pago_tarjeta" value="tarjeta">
                    <label for="pago_tarjeta">
                        <span class="pago-icon">💳</span>
                        Tarjeta
                        <small style="font-size:10px;opacity:0.8;">(−<?php echo (defined('COMISION_TARJETA') ? COMISION_TARJETA*100 : 2.5); ?>%)</small>
                    </label>
                </div>
            </div>

            <!-- Desglose dinámico (actualizado por JS) -->
            <div class="pago-desglose" id="pagoDesglose">
                <div class="linea">
                    <span>Total bruto:</span>
                    <span id="totalBruto">$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="linea comision" id="lineaComision" style="display:none;">
                    <span>Comisión (<span id="tasaLabel"><?php echo (defined('COMISION_TARJETA') ? COMISION_TARJETA*100 : 2.5); ?>%</span>):</span>
                    <span id="montoComision">$0.00</span>
                </div>
                <div class="linea neto" id="lineaNeto">
                    <span>Ingreso neto:</span>
                    <span id="totalNeto">$<?php echo number_format($total, 2); ?></span>
                </div>
            </div>

            <!-- Formulario de cobro -->
            <form action="index.php?ruta=cobrar" method="POST" id="formCobrar">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="metodo_pago" id="metodo_pago_input" value="efectivo">
                <button type="submit" class="btn-primario" id="btnCobrar"
                        style="display:block;width:100%;font-size:1rem;padding:14px;">
                    💵 COBRAR $<?php echo number_format($total, 2); ?>
                </button>
            </form>

            <form action="index.php?ruta=vaciar_carrito" method="POST" style="margin-top:8px;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-outline" style="width:100%;padding:7px;text-align:center;"
                        onclick="return confirm('¿Vaciar el ticket actual?');">✕ Vaciar todo</button>
            </form>

        <?php else: ?>
            <p style="color:#94A3B8;text-align:center;margin-top:40px;font-size:0.9rem;">El ticket está vacío.</p>
        <?php endif; ?>
    </div>

</div>

<script>
(function() {
    const totalBruto = <?php echo $total; ?>;
    const tasaComision = <?php echo defined('COMISION_TARJETA') ? COMISION_TARJETA : 0.025; ?>;

    const radioEfectivo = document.getElementById('pago_efectivo');
    const radioTarjeta  = document.getElementById('pago_tarjeta');
    const hiddenInput   = document.getElementById('metodo_pago_input');
    const lineaComision = document.getElementById('lineaComision');
    const btnCobrar     = document.getElementById('btnCobrar');

    function actualizarDesglose() {
        const esTarjeta = radioTarjeta && radioTarjeta.checked;
        hiddenInput.value = esTarjeta ? 'tarjeta' : 'efectivo';

        if (esTarjeta) {
            const comision  = totalBruto * tasaComision;
            const neto      = totalBruto - comision;
            document.getElementById('montoComision').textContent = '-$' + comision.toFixed(2);
            document.getElementById('totalNeto').textContent     = '$' + neto.toFixed(2);
            lineaComision.style.display = 'flex';
            btnCobrar.textContent = '💳 COBRAR $' + totalBruto.toFixed(2);
            btnCobrar.style.background = '#7C3AED';
        } else {
            document.getElementById('totalNeto').textContent = '$' + totalBruto.toFixed(2);
            lineaComision.style.display = 'none';
            btnCobrar.textContent = '💵 COBRAR $' + totalBruto.toFixed(2);
            btnCobrar.style.background = '';
        }
    }

    if (radioEfectivo) radioEfectivo.addEventListener('change', actualizarDesglose);
    if (radioTarjeta)  radioTarjeta.addEventListener('change', actualizarDesglose);
})();
</script>