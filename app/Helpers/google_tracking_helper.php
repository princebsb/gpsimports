<?php

/**
 * Google Tracking Helper
 *
 * Funcoes auxiliares para tracking do Google
 *
 * @package GPS Imports
 */

use App\Libraries\GoogleTracking;

if (!function_exists('google_tracking')) {
    /**
     * Retorna instancia do GoogleTracking
     */
    function google_tracking(): GoogleTracking
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new GoogleTracking();
        }

        return $instance;
    }
}

if (!function_exists('gtag_head')) {
    /**
     * Retorna script do gtag para o head
     */
    function gtag_head(): string
    {
        return google_tracking()->getGtagScript();
    }
}

if (!function_exists('gtm_head')) {
    /**
     * Retorna script do GTM para o head
     */
    function gtm_head(): string
    {
        return google_tracking()->getGTMHeadScript();
    }
}

if (!function_exists('gtm_body')) {
    /**
     * Retorna noscript do GTM para o body
     */
    function gtm_body(): string
    {
        return google_tracking()->getGTMBodyScript();
    }
}

if (!function_exists('track_view_item')) {
    /**
     * Gera script de view_item
     */
    function track_view_item(array $product): string
    {
        return google_tracking()->viewItemScript($product);
    }
}

if (!function_exists('track_add_to_cart')) {
    /**
     * Gera script de add_to_cart (inline para usar em JS)
     */
    function track_add_to_cart(array $product, int $quantity = 1): string
    {
        return google_tracking()->addToCartScript($product, $quantity);
    }
}

if (!function_exists('track_begin_checkout')) {
    /**
     * Gera script de begin_checkout
     */
    function track_begin_checkout(array $cart): string
    {
        return google_tracking()->beginCheckoutScript($cart);
    }
}

if (!function_exists('track_purchase')) {
    /**
     * Gera script de purchase com conversao
     */
    function track_purchase(array $order, array $customer = []): string
    {
        $tracking = google_tracking();

        // Verificar se ja foi registrado
        $orderId = (int) ($order['id'] ?? 0);
        if ($orderId > 0 && $tracking->isConversionLogged($orderId)) {
            return '<!-- Conversion already logged for order ' . $orderId . ' -->';
        }

        // Registrar no banco
        if ($orderId > 0) {
            $tracking->logConversion(
                $orderId,
                $order['order_number'] ?? (string) $orderId
            );
        }

        return $tracking->purchaseScript($order, $customer);
    }
}

if (!function_exists('google_config')) {
    /**
     * Retorna configuracao do Google
     */
    function google_config(): \Config\Google
    {
        return google_tracking()->getConfig();
    }
}
