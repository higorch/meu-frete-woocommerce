<div class="wrap-timeline-order">

    <div class="title">
        <h2>
            Pedido em andamento
            <span class="order_number">#<?php echo $order_number; ?></span>
        </h2>
    </div>

    <div class="timeline">
        <ul>
            <li class="stage placed active">
                <span class="icon">
                    <?php include plugin_dir_path(__DIR__) . '/assets/images/shopping-bag.svg'; ?>
                </span>
                <span class="details">Pedido realizado <br> <?php echo date('d/m/Y', strtotime($date_created)); ?></span>
            </li>
            <li class="stage payment-confirm <?php echo $status == 'processing' || $status == 'completed' ? 'active' : ''; ?>">
                <span class="icon">
                    <?php include plugin_dir_path(__DIR__) . '/assets/images/money.svg'; ?>
                </span>
                <?php if ($status == 'processing' || $status == 'completed') : ?>
                    <span class="details">Pagamento <br>confirmado</span>
                <?php else : ?>
                    <span class="details">Aguardando <br>pagamento</span>
                <?php endif; ?>
            </li>
            <li class="stage delivery <?php echo !empty($code_correios) ? 'active' : ''; ?>">
                <span class="icon">
                    <?php include plugin_dir_path(__DIR__) . '/assets/images/delivery-truck.svg'; ?>
                </span>
                <?php if (!empty($code_correios)) : ?>
                    <span class="details">Pedido enviado <br><a href="https://www2.correios.com.br/sistemas/rastreamento/default.cfm" title="Código rastreio" target="_blank"><?php echo $code_correios; ?></a></span>
                <?php else : ?>
                    <span class="details">COD. rastreio <br>XXXXXXXXX</span>
                <?php endif; ?>
            </li>
            <li class="stage payment-confirm <?php echo $status == 'completed' ? 'active' : ''; ?>">
                <span class="icon">
                    <?php include plugin_dir_path(__DIR__) . '/assets/images/correct.svg'; ?>
                </span>
                <span class="details">Pedido <br>finalizado</span>
            </li>
        </ul>
    </div>

</div>