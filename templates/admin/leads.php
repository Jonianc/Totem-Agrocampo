<?php if (!defined('ABSPATH')) { exit; } ?>
<div class="wrap">
    <h1>Atenciones recibidas</h1>

    <?php if (!empty($action_notice)) : ?>
        <div class="notice notice-<?php echo esc_attr($action_notice['type']); ?> is-dismissible"><p><?php echo esc_html($action_notice['message']); ?></p></div>
    <?php endif; ?>

    <?php if ('detail' === $view && !empty($lead_detail)) : ?>
        <h2>Detalle de atención</h2>
        <table class="widefat striped">
            <tbody>
                <tr><th>Ticket</th><td><?php echo esc_html($lead_detail['ticket_number']); ?></td></tr>
                <tr><th>Fecha</th><td><?php echo esc_html($lead_detail['created_at']); ?></td></tr>
                <tr><th>Sucursal</th><td><?php echo esc_html($lead_detail['branch']); ?></td></tr>
                <tr><th>Tipo de consulta</th><td><?php echo esc_html($lead_detail['inquiry_type']); ?></td></tr>
                <tr><th>Producto seleccionado</th><td><?php echo esc_html($lead_detail['product_title'] ? $lead_detail['product_title'] : '—'); ?></td></tr>
                <tr><th>Nombre cliente</th><td><?php echo esc_html($lead_detail['customer_name']); ?></td></tr>
                <tr><th>WhatsApp</th><td><?php echo esc_html($lead_detail['customer_whatsapp']); ?></td></tr>
                <tr><th>Correo</th><td><?php echo esc_html($lead_detail['customer_email']); ?></td></tr>
                <tr><th>Comuna/sector</th><td><?php echo esc_html($lead_detail['customer_location']); ?></td></tr>
                <tr><th>Comentario</th><td><?php echo esc_html($lead_detail['comment']); ?></td></tr>
                <tr><th>Estado</th><td><?php echo esc_html($lead_detail['status']); ?></td></tr>
                <tr><th>Usuario asignado</th><td><?php echo esc_html($lead_detail['assigned_user_name'] ? $lead_detail['assigned_user_name'] : 'Sin asignar'); ?></td></tr>
            </tbody>
        </table>

        <form method="post" style="margin-top:16px;">
            <?php wp_nonce_field('agp_totem_lead_update', 'agp_totem_lead_nonce'); ?>
            <input type="hidden" name="agp_totem_lead_action" value="update_detail" />
            <input type="hidden" name="lead_id" value="<?php echo (int) $lead_detail['id']; ?>" />
            <table class="form-table">
                <tr>
                    <th><label for="assigned_user_id">Asignado a</label></th>
                    <td>
                        <select name="assigned_user_id" id="assigned_user_id">
                            <option value="0">Sin asignar</option>
                            <?php foreach ($assignable_users as $admin_user) : ?>
                                <option value="<?php echo (int) $admin_user->ID; ?>" <?php selected((int) $lead_detail['assigned_user_id'], (int) $admin_user->ID); ?>><?php echo esc_html($admin_user->display_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="internal_notes">Notas internas</label></th>
                    <td><textarea name="internal_notes" id="internal_notes" rows="6" cols="60"><?php echo esc_textarea($lead_detail['internal_notes']); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button('Guardar detalle'); ?>
            <a class="button" href="<?php echo esc_url(add_query_arg(array('page' => 'agp-totem-leads'), admin_url('admin.php'))); ?>">Volver al listado</a>
        </form>
        <hr />
    <?php endif; ?>

    <form method="get">
        <input type="hidden" name="page" value="agp-totem-leads" />
        <p>
            <label>Fecha desde <input type="date" name="date_from" value="<?php echo esc_attr($filters['date_from']); ?>" /></label>
            <label>Fecha hasta <input type="date" name="date_to" value="<?php echo esc_attr($filters['date_to']); ?>" /></label>
            <label>Sucursal
                <select name="branch"><option value="">Todas</option>
                    <?php foreach ($filter_options['branches'] as $branch) : ?><option value="<?php echo esc_attr($branch); ?>" <?php selected($filters['branch'], $branch); ?>><?php echo esc_html($branch); ?></option><?php endforeach; ?>
                </select>
            </label>
            <label>Estado
                <select name="status"><option value="">Todos</option>
                    <?php foreach ($statuses as $status_option) : ?><option value="<?php echo esc_attr($status_option); ?>" <?php selected($filters['status'], $status_option); ?>><?php echo esc_html($status_option); ?></option><?php endforeach; ?>
                </select>
            </label>
            <label>Tipo de consulta
                <select name="inquiry_type"><option value="">Todos</option>
                    <?php foreach ($filter_options['inquiry_types'] as $type) : ?><option value="<?php echo esc_attr($type); ?>" <?php selected($filters['inquiry_type'], $type); ?>><?php echo esc_html($type); ?></option><?php endforeach; ?>
                </select>
            </label>
            <label>Búsqueda <input type="search" name="s" value="<?php echo esc_attr($filters['search']); ?>" /></label>
            <button class="button button-primary" type="submit">Filtrar</button>
        </p>
    </form>

    <table class="widefat striped">
        <thead><tr><th>Ticket</th><th>Fecha/hora</th><th>Cliente</th><th>WhatsApp</th><th>Sucursal</th><th>Tipo de consulta</th><th>Estado</th><th>Asignado a</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php if (empty($leads)) : ?>
            <tr><td colspan="9">No hay atenciones para los filtros seleccionados.</td></tr>
        <?php else : foreach ($leads as $lead) : ?>
            <tr>
                <td><?php echo esc_html($lead['ticket_number']); ?></td>
                <td><?php echo esc_html($lead['created_at']); ?></td>
                <td><?php echo esc_html($lead['customer_name']); ?></td>
                <td><?php echo esc_html($lead['customer_whatsapp']); ?></td>
                <td><?php echo esc_html($lead['branch']); ?></td>
                <td><?php echo esc_html($lead['inquiry_type']); ?></td>
                <td>
                    <form method="post">
                        <?php wp_nonce_field('agp_totem_lead_update', 'agp_totem_lead_nonce'); ?>
                        <input type="hidden" name="agp_totem_lead_action" value="update_status" />
                        <input type="hidden" name="lead_id" value="<?php echo (int) $lead['id']; ?>" />
                        <select name="status">
                            <?php foreach ($statuses as $status_option) : ?><option value="<?php echo esc_attr($status_option); ?>" <?php selected($lead['status'], $status_option); ?>><?php echo esc_html($status_option); ?></option><?php endforeach; ?>
                        </select>
                        <button type="submit" class="button">Guardar</button>
                    </form>
                </td>
                <td><?php echo esc_html($lead['assigned_user_name'] ? $lead['assigned_user_name'] : 'Sin asignar'); ?></td>
                <td><a class="button" href="<?php echo esc_url(add_query_arg(array('page' => 'agp-totem-leads', 'view' => 'detail', 'lead_id' => (int) $lead['id']), admin_url('admin.php'))); ?>">Ver detalle</a></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>

    <?php
    $pagination_base_args = array_merge(array('page' => 'agp-totem-leads'), array_filter(array(
        'date_from' => $filters['date_from'], 'date_to' => $filters['date_to'], 'branch' => $filters['branch'], 'status' => $filters['status'], 'inquiry_type' => $filters['inquiry_type'], 's' => $filters['search'],
    )));
    echo wp_kses_post(paginate_links(array(
        'base' => add_query_arg('paged', '%#%', add_query_arg($pagination_base_args, admin_url('admin.php'))),
        'format' => '',
        'current' => $current_page,
        'total' => $total_pages,
        'type' => 'plain',
    )));
    ?>
</div>
