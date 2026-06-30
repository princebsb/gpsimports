<?php

/**
 * Get setting value by key
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
if (!function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        static $settings = null;

        if ($settings === null) {
            $cache = \Config\Services::cache();
            $settings = $cache->get('settings');

            if ($settings === null) {
                $db = \Config\Database::connect();

                try {
                    $result = $db->table('settings')->get()->getResultArray();
                    $settings = [];

                    foreach ($result as $row) {
                        $value = $row['value'];

                        // Type casting
                        switch ($row['type'] ?? 'text') {
                            case 'boolean':
                                $value = (bool) $value;
                                break;
                            case 'number':
                                $value = is_numeric($value) ? (float) $value : 0;
                                break;
                            case 'json':
                                $value = json_decode($value, true);
                                break;
                        }

                        $settings[$row['key']] = $value;
                    }

                    $cache->save('settings', $settings, 3600);
                } catch (\Exception $e) {
                    $settings = [];
                }
            }
        }

        return $settings[$key] ?? $default;
    }
}

/**
 * Set setting value
 *
 * @param string $key
 * @param mixed $value
 * @return bool
 */
if (!function_exists('set_setting')) {
    function set_setting(string $key, $value): bool
    {
        $db = \Config\Database::connect();

        $existing = $db->table('settings')->where('key', $key)->get()->getRow();

        if ($existing) {
            $db->table('settings')->where('key', $key)->update(['value' => $value]);
        } else {
            $db->table('settings')->insert(['key' => $key, 'value' => $value]);
        }

        // Clear cache
        \Config\Services::cache()->delete('settings');

        return true;
    }
}

/**
 * Format price in BRL
 *
 * @param float $value
 * @return string
 */
if (!function_exists('format_price')) {
    function format_price(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}

/**
 * Format date in Brazilian format
 *
 * @param string $date
 * @param bool $withTime
 * @return string
 */
if (!function_exists('format_date')) {
    function format_date(string $date, bool $withTime = false): string
    {
        $format = $withTime ? 'd/m/Y H:i' : 'd/m/Y';
        return date($format, strtotime($date));
    }
}

/**
 * Generate slug from string
 *
 * @param string $string
 * @return string
 */
if (!function_exists('generate_slug')) {
    function generate_slug(string $string): string
    {
        $slug = mb_strtolower($string);
        $slug = preg_replace('/[áàãâä]/u', 'a', $slug);
        $slug = preg_replace('/[éèêë]/u', 'e', $slug);
        $slug = preg_replace('/[íìîï]/u', 'i', $slug);
        $slug = preg_replace('/[óòõôö]/u', 'o', $slug);
        $slug = preg_replace('/[úùûü]/u', 'u', $slug);
        $slug = preg_replace('/[ç]/u', 'c', $slug);
        $slug = preg_replace('/[ñ]/u', 'n', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }
}

/**
 * Format CPF
 *
 * @param string $cpf
 * @return string
 */
if (!function_exists('format_cpf')) {
    function format_cpf(string $cpf): string
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11) {
            return $cpf;
        }

        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
}

/**
 * Format phone number
 *
 * @param string $phone
 * @return string
 */
if (!function_exists('format_phone')) {
    function format_phone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) === 11) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7);
        } elseif (strlen($phone) === 10) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6);
        }

        return $phone;
    }
}

/**
 * Format CEP
 *
 * @param string $cep
 * @return string
 */
if (!function_exists('format_cep')) {
    function format_cep(string $cep): string
    {
        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) !== 8) {
            return $cep;
        }

        return substr($cep, 0, 5) . '-' . substr($cep, 5);
    }
}

/**
 * Generate order number
 *
 * @return string
 */
if (!function_exists('generate_order_number')) {
    function generate_order_number(): string
    {
        return date('Ymd') . strtoupper(substr(uniqid(), -6));
    }
}

/**
 * Get order status label
 *
 * @param string $status
 * @return string
 */
if (!function_exists('order_status_label')) {
    function order_status_label(string $status): string
    {
        return match ($status) {
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
            default => ucfirst($status)
        };
    }
}

/**
 * Get payment status label
 *
 * @param string $status
 * @return string
 */
if (!function_exists('payment_status_label')) {
    function payment_status_label(string $status): string
    {
        return match ($status) {
            'pending' => 'Aguardando',
            'paid' => 'Pago',
            'failed' => 'Falhou',
            'refunded' => 'Reembolsado',
            'cancelled' => 'Cancelado',
            default => ucfirst($status)
        };
    }
}
