<?php

if (!defined('ABSPATH')) {
    exit;
}

class AGP_Totem_Settings {
    const OPTION_KEY = 'agp_totem_settings';
    const FLUSH_REWRITE_OPTION = 'agp_totem_flush_rewrite';

    public static function defaults() {
        return array(
            'frontend_route' => '/totem-agrocampo/',
            'welcome_message' => 'Bienvenido a Tótem Agrocampo',
            'closing_message' => 'Tu solicitud fue recibida correctamente.',
            'inactivity_timeout' => 90,
            'whatsapp_main' => '',
            'internal_emails' => '',
            'branches' => array(
                'talca' => 'Talca',
                'linares' => 'Linares',
                'parral' => 'Parral',
            ),
        );
    }

    public static function get_all() {
        return wp_parse_args(get_option(self::OPTION_KEY, array()), self::defaults());
    }

    public static function update($input) {
        $settings = self::get_all();

        $old_route = $settings['frontend_route'];
        $settings['frontend_route'] = self::sanitize_route(isset($input['frontend_route']) ? $input['frontend_route'] : '');
        $settings['welcome_message'] = sanitize_textarea_field(isset($input['welcome_message']) ? $input['welcome_message'] : '');
        $settings['closing_message'] = sanitize_textarea_field(isset($input['closing_message']) ? $input['closing_message'] : '');

        $timeout = isset($input['inactivity_timeout']) ? absint($input['inactivity_timeout']) : 90;
        $settings['inactivity_timeout'] = $timeout > 0 ? $timeout : 90;

        $settings['whatsapp_main'] = preg_replace('/[^0-9+]/', '', (string) (isset($input['whatsapp_main']) ? $input['whatsapp_main'] : ''));

        $emails = isset($input['internal_emails']) ? (string) $input['internal_emails'] : '';
        $settings['internal_emails'] = self::sanitize_email_list($emails);

        $settings['branches'] = array(
            'talca' => sanitize_text_field(isset($input['branch_talca']) ? $input['branch_talca'] : 'Talca'),
            'linares' => sanitize_text_field(isset($input['branch_linares']) ? $input['branch_linares'] : 'Linares'),
            'parral' => sanitize_text_field(isset($input['branch_parral']) ? $input['branch_parral'] : 'Parral'),
        );

        update_option(self::OPTION_KEY, $settings);

        if ($old_route !== $settings['frontend_route']) {
            update_option(self::FLUSH_REWRITE_OPTION, 1);
        }

        return $settings;
    }

    private static function sanitize_route($route) {
        $route = trim((string) $route);
        if ($route === '') {
            return '/totem-agrocampo/';
        }

        $route = '/' . trim($route, '/') . '/';
        return sanitize_text_field($route);
    }

    private static function sanitize_email_list($list) {
        $items = array_filter(array_map('trim', explode(',', $list)));
        $valid = array();
        foreach ($items as $item) {
            $email = sanitize_email($item);
            if (!empty($email) && is_email($email)) {
                $valid[] = $email;
            }
        }
        return implode(', ', $valid);
    }
}
