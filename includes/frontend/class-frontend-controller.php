<?php

if (!defined('ABSPATH')) {
    exit;
}

class AGP_Totem_Frontend_Controller {
    public function render() {
        status_header(200);
        nocache_headers();

        $settings = AGP_Totem_Settings::get_all();
        $submission = $this->handle_submission();

        $view_data = array(
            'welcome_message' => $settings['welcome_message'],
            'closing_message' => $settings['closing_message'],
            'route' => $settings['frontend_route'],
            'errors' => $submission['errors'],
            'form_values' => $submission['form_values'],
            'ticket_number' => $submission['ticket_number'],
            'active_screen' => $submission['active_screen'],
            'branches' => $settings['branches'],
        );

        include AGP_TOTEM_PATH . 'templates/frontend/kiosk-layout.php';
    }

    private function handle_submission() {
        $state = array(
            'errors' => array(),
            'form_values' => array(),
            'ticket_number' => '',
            'active_screen' => 'home',
        );

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['agp_totem_submit'])) {
            return $state;
        }

        $state['active_screen'] = 'form';
        $settings = AGP_Totem_Settings::get_all();
        $allowed_branches = array_map('strval', array_values($settings['branches']));

        if (!isset($_POST['agp_totem_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['agp_totem_nonce'])), 'agp_totem_submit_lead')) {
            $state['errors'][] = __('No pudimos validar la sesión del formulario. Intenta nuevamente.', 'totem-agrocampo');
            return $state;
        }

        $name = sanitize_text_field(wp_strip_all_tags(wp_unslash($_POST['customer_name'] ?? '')));
        $whatsapp = $this->normalize_whatsapp(wp_unslash($_POST['customer_whatsapp'] ?? ''));
        $email_raw = wp_unslash($_POST['customer_email'] ?? '');
        $email = '';
        if ($email_raw !== '') {
            $email = sanitize_email($email_raw);
            if (!is_email($email)) {
                $state['errors'][] = __('El correo no es válido.', 'totem-agrocampo');
            }
        }
        $location = sanitize_text_field(wp_strip_all_tags(wp_unslash($_POST['customer_location'] ?? '')));
        $branch = sanitize_text_field(wp_strip_all_tags(wp_unslash($_POST['branch'] ?? '')));
        $inquiry = sanitize_text_field(wp_strip_all_tags(wp_unslash($_POST['inquiry_type'] ?? '')));
        $product = sanitize_text_field(wp_strip_all_tags(wp_unslash($_POST['selected_product'] ?? '')));
        $comment = sanitize_textarea_field(wp_strip_all_tags(wp_unslash($_POST['comment'] ?? '')));

        $state['form_values'] = array(
            'customer_name' => $name,
            'customer_whatsapp' => $whatsapp,
            'customer_email' => $email,
            'customer_location' => $location,
            'branch' => $branch,
            'inquiry_type' => $inquiry,
            'selected_product' => $product,
            'comment' => $comment,
        );

        if ($name === '') {
            $state['errors'][] = __('El nombre es obligatorio.', 'totem-agrocampo');
        }
        if ($whatsapp === '') {
            $state['errors'][] = __('El WhatsApp es obligatorio.', 'totem-agrocampo');
        } elseif (!$this->is_valid_chile_whatsapp($whatsapp)) {
            $state['errors'][] = __('Ingresa un WhatsApp válido.', 'totem-agrocampo');
        }
        if ($branch === '') {
            $state['errors'][] = __('La sucursal es obligatoria.', 'totem-agrocampo');
        } elseif (!in_array($branch, $allowed_branches, true)) {
            $state['errors'][] = __('La sucursal seleccionada no es válida.', 'totem-agrocampo');
        }
        if ($inquiry === '') {
            $state['errors'][] = __('El tipo de consulta es obligatorio.', 'totem-agrocampo');
        }

        if (!empty($state['errors'])) {
            return $state;
        }

        $repository = new AGP_Totem_Leads_Repository();
        $result = $repository->create(array(
            'branch' => $branch,
            'product_id' => null,
            'customer_name' => $name,
            'customer_whatsapp' => $whatsapp,
            'customer_email' => $email ?: null,
            'customer_location' => $location,
            'inquiry_type' => $inquiry,
            'comment' => $comment ?: null,
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr(sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])), 0, 255) : null,
            'ip_partial' => $this->get_ip_partial(),
        ));

        if (is_wp_error($result)) {
            $state['errors'][] = $result->get_error_message();
            return $state;
        }

        $state['ticket_number'] = $result['ticket_number'];
        $state['active_screen'] = 'success';
        $state['form_values'] = array();
        return $state;
    }

    private function normalize_whatsapp($value) {
        $raw = wp_strip_all_tags((string) $value);
        $raw = preg_replace('/[^\d+]/', '', $raw);
        if ($raw === null) {
            return '';
        }

        if (strpos($raw, '+56') === 0) {
            return '+' . preg_replace('/\D/', '', substr($raw, 1));
        }

        $digits = preg_replace('/\D/', '', $raw);
        if (strpos($digits, '56') === 0) {
            return '+' . $digits;
        }
        if (strlen($digits) === 9 && strpos($digits, '9') === 0) {
            return '+56' . $digits;
        }

        return $digits;
    }


    private function is_valid_chile_whatsapp($value) {
        return (bool) preg_match('/^\+569\d{8}$/', (string) $value);
    }

    private function get_ip_partial() {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
        if ($ip === '' || filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return null;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            if (count($parts) === 4) {
                return $parts[0] . '.' . $parts[1] . '.x.x';
            }
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            return implode(':', array_slice($parts, 0, 3)) . ':xxxx:xxxx';
        }

        return null;
    }
}
