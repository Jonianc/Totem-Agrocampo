<?php

if (!defined('ABSPATH')) {
    exit;
}

class AGP_Totem_Leads_Repository {
    const PER_PAGE_DEFAULT = 20;

    private $wpdb;
    private $table_name;

    public function __construct($wpdb_instance = null) {
        global $wpdb;
        $this->wpdb = $wpdb_instance ?: $wpdb;
        $this->table_name = $this->wpdb->prefix . 'agp_totem_leads';
    }

    public static function allowed_statuses() {
        return array('nueva', 'contactado', 'cotizado', 'cerrado', 'descartado');
    }

    public function create(array $data) {
        $ticket_number = $this->generate_unique_ticket_number();
        $created_at = current_time('mysql');

        $inserted = $this->wpdb->insert(
            $this->table_name,
            array(
                'ticket_number' => $ticket_number,
                'created_at' => $created_at,
                'branch' => $data['branch'],
                'category_id' => null,
                'product_id' => $data['product_id'],
                'customer_name' => $data['customer_name'],
                'customer_whatsapp' => $data['customer_whatsapp'],
                'customer_email' => $data['customer_email'],
                'customer_location' => $data['customer_location'],
                'inquiry_type' => $data['inquiry_type'],
                'comment' => $data['comment'],
                'status' => 'nueva',
                'assigned_user_id' => null,
                'internal_notes' => null,
                'source' => 'totem',
                'user_agent' => $data['user_agent'],
                'ip_partial' => $data['ip_partial'],
            ),
            array(
                '%s','%s','%s','%d','%d','%s','%s','%s','%s','%s','%s','%s','%d','%s','%s','%s','%s'
            )
        );

        if ($inserted === false) {
            return new WP_Error('agp_totem_insert_failed', __('No pudimos registrar la atención. Por favor solicita ayuda a un vendedor.', 'totem-agrocampo'));
        }

        return array(
            'ticket_number' => $ticket_number,
            'created_at' => $created_at,
            'lead_id' => (int) $this->wpdb->insert_id,
        );
    }

    public function get_leads_paginated(array $filters, $page = 1, $per_page = self::PER_PAGE_DEFAULT) {
        $page = max(1, (int) $page);
        $per_page = max(1, (int) $per_page);
        $offset = ($page - 1) * $per_page;

        $where = $this->build_where_clause($filters);
        $sql = "SELECT l.*,
            u.display_name AS assigned_user_name,
            p.title AS product_title
            FROM {$this->table_name} l
            LEFT JOIN {$this->wpdb->users} u ON u.ID = l.assigned_user_id
            LEFT JOIN {$this->wpdb->prefix}agp_totem_products p ON p.id = l.product_id
            {$where['sql']}
            ORDER BY l.created_at DESC, l.id DESC
            LIMIT %d OFFSET %d";

        $params = array_merge($where['params'], array($per_page, $offset));
        return $this->wpdb->get_results($this->wpdb->prepare($sql, $params), ARRAY_A);
    }

    public function count_leads(array $filters) {
        $where = $this->build_where_clause($filters);
        $sql = "SELECT COUNT(*) FROM {$this->table_name} l {$where['sql']}";
        return (int) $this->wpdb->get_var($this->wpdb->prepare($sql, $where['params']));
    }

    public function get_filter_options() {
        return array(
            'branches' => $this->wpdb->get_col("SELECT DISTINCT branch FROM {$this->table_name} ORDER BY branch ASC"),
            'inquiry_types' => $this->wpdb->get_col("SELECT DISTINCT inquiry_type FROM {$this->table_name} ORDER BY inquiry_type ASC"),
        );
    }

    public function get_lead_by_id($lead_id) {
        $lead_id = (int) $lead_id;
        if ($lead_id <= 0) {
            return null;
        }

        $sql = "SELECT l.*,
            u.display_name AS assigned_user_name,
            p.title AS product_title
            FROM {$this->table_name} l
            LEFT JOIN {$this->wpdb->users} u ON u.ID = l.assigned_user_id
            LEFT JOIN {$this->wpdb->prefix}agp_totem_products p ON p.id = l.product_id
            WHERE l.id = %d LIMIT 1";

        return $this->wpdb->get_row($this->wpdb->prepare($sql, $lead_id), ARRAY_A);
    }

    public function update_status($lead_id, $status) {
        $lead_id = (int) $lead_id;
        $status = sanitize_key($status);

        if ($lead_id <= 0 || !in_array($status, self::allowed_statuses(), true)) {
            return false;
        }

        return false !== $this->wpdb->update(
            $this->table_name,
            array('status' => $status),
            array('id' => $lead_id),
            array('%s'),
            array('%d')
        );
    }

    public function update_notes_and_assignment($lead_id, $notes, $assigned_user_id) {
        $lead_id = (int) $lead_id;
        $assigned_user_id = (int) $assigned_user_id;

        if ($lead_id <= 0) {
            return false;
        }

        if ($assigned_user_id > 0 && !user_can($assigned_user_id, 'manage_options')) {
            $assigned_user_id = 0;
        }

        return false !== $this->wpdb->update(
            $this->table_name,
            array(
                'internal_notes' => sanitize_textarea_field($notes),
                'assigned_user_id' => $assigned_user_id > 0 ? $assigned_user_id : null,
            ),
            array('id' => $lead_id),
            array('%s', '%d'),
            array('%d')
        );
    }

    private function build_where_clause(array $filters) {
        $where = array('1=1');
        $params = array();

        if (!empty($filters['date_from'])) {
            $where[] = 'l.created_at >= %s';
            $params[] = $filters['date_from'] . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $where[] = 'l.created_at <= %s';
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        if (!empty($filters['branch'])) {
            $where[] = 'l.branch = %s';
            $params[] = $filters['branch'];
        }

        if (!empty($filters['status']) && in_array($filters['status'], self::allowed_statuses(), true)) {
            $where[] = 'l.status = %s';
            $params[] = $filters['status'];
        }

        if (!empty($filters['inquiry_type'])) {
            $where[] = 'l.inquiry_type = %s';
            $params[] = $filters['inquiry_type'];
        }

        if (!empty($filters['search'])) {
            $like = '%' . $this->wpdb->esc_like($filters['search']) . '%';
            $where[] = '(l.ticket_number LIKE %s OR l.customer_name LIKE %s OR l.customer_whatsapp LIKE %s OR l.customer_location LIKE %s)';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        return array(
            'sql' => 'WHERE ' . implode(' AND ', $where),
            'params' => $params,
        );
    }

    private function generate_unique_ticket_number() {
        $date_prefix = current_time('Ymd');
        $prefix = 'AGP-' . $date_prefix . '-';

        $last_ticket = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT ticket_number FROM {$this->table_name} WHERE ticket_number LIKE %s ORDER BY id DESC LIMIT 1",
                $this->wpdb->esc_like($prefix) . '%'
            )
        );

        $next_sequence = 1;
        if (is_string($last_ticket) && preg_match('/^(?:AGP-\d{8}-)(\d{4})$/', $last_ticket, $matches)) {
            $next_sequence = ((int) $matches[1]) + 1;
        }

        for ($attempt = 0; $attempt < 50; $attempt++) {
            $candidate = $prefix . str_pad((string) ($next_sequence + $attempt), 4, '0', STR_PAD_LEFT);
            $exists = $this->wpdb->get_var(
                $this->wpdb->prepare("SELECT id FROM {$this->table_name} WHERE ticket_number = %s LIMIT 1", $candidate)
            );
            if (!$exists) {
                return $candidate;
            }
        }

        return $prefix . strtoupper(wp_generate_password(4, false, false));
    }
}
