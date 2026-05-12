<?php

if (!defined('ABSPATH')) {
    exit;
}

class AGP_Totem_Leads_Repository {
    private $wpdb;
    private $table_name;

    public function __construct($wpdb_instance = null) {
        global $wpdb;
        $this->wpdb = $wpdb_instance ?: $wpdb;
        $this->table_name = $this->wpdb->prefix . 'agp_totem_leads';
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

    private function generate_unique_ticket_number() {
        $date_prefix = gmdate('Ymd');
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
