<?php

namespace App\Libraries;

use Config\Google;

/**
 * Google Tracking Library
 *
 * Responsavel por gerar eventos de tracking para:
 * - Google Analytics 4 (GA4)
 * - Google Ads Conversion
 * - Enhanced Conversions
 *
 * @package GPS Imports
 */
class GoogleTracking
{
    protected Google $config;
    protected $db;

    public function __construct()
    {
        $this->config = config('Google');
        $this->db = \Config\Database::connect();
    }

    /**
     * Gera hash SHA-256 para Enhanced Conversions
     */
    public function hashData(string $data): string
    {
        // Normalizar: lowercase, trim, remover espacos extras
        $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $data)));
        return hash('sha256', $normalized);
    }

    /**
     * Normaliza telefone para formato E.164
     */
    public function normalizePhone(string $phone): string
    {
        // Remove tudo que nao for numero
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Adiciona codigo do pais se nao tiver
        if (strlen($phone) === 11 || strlen($phone) === 10) {
            $phone = '55' . $phone;
        }

        return '+' . $phone;
    }

    /**
     * Prepara dados do usuario para Enhanced Conversions (com hash)
     */
    public function prepareUserData(array $customer): array
    {
        $userData = [];

        if (!empty($customer['email'])) {
            $userData['sha256_email_address'] = $this->hashData($customer['email']);
        }

        if (!empty($customer['phone'])) {
            $normalizedPhone = $this->normalizePhone($customer['phone']);
            $userData['sha256_phone_number'] = $this->hashData($normalizedPhone);
        }

        // Nome
        if (!empty($customer['name'])) {
            $nameParts = explode(' ', trim($customer['name']), 2);
            $userData['address'] = [
                'sha256_first_name' => $this->hashData($nameParts[0] ?? ''),
                'sha256_last_name' => $this->hashData($nameParts[1] ?? ''),
            ];
        }

        // Endereco
        if (!empty($customer['city'])) {
            $userData['address']['city'] = $customer['city'];
        }
        if (!empty($customer['state'])) {
            $userData['address']['region'] = $customer['state'];
        }
        if (!empty($customer['zipcode'])) {
            $userData['address']['postal_code'] = preg_replace('/[^0-9]/', '', $customer['zipcode']);
        }
        if (!empty($customer['country'])) {
            $userData['address']['country'] = $customer['country'];
        } else {
            $userData['address']['country'] = 'BR';
        }

        return $userData;
    }

    /**
     * Formata item do produto para eventos GA4/Ads
     */
    public function formatItem(array $product, int $index = 0): array
    {
        return [
            'item_id' => (string) ($product['sku'] ?? $product['id'] ?? ''),
            'item_name' => $this->escapeString($product['name'] ?? ''),
            'item_brand' => $this->escapeString($product['brand'] ?? $product['brand_name'] ?? 'GPS Imports'),
            'item_category' => $this->escapeString($product['category'] ?? $product['category_name'] ?? ''),
            'price' => (float) ($product['price'] ?? 0),
            'quantity' => (int) ($product['quantity'] ?? 1),
            'index' => $index,
        ];
    }

    /**
     * Formata lista de itens do pedido
     */
    public function formatOrderItems(array $items): array
    {
        $formattedItems = [];

        foreach ($items as $index => $item) {
            $formattedItems[] = $this->formatItem($item, $index);
        }

        return $formattedItems;
    }

    /**
     * Escapa string para uso em JavaScript
     */
    public function escapeString(?string $str): string
    {
        if ($str === null) {
            return '';
        }
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Verifica se conversao ja foi registrada para evitar duplicidade
     */
    public function isConversionLogged(int $orderId): bool
    {
        $result = $this->db->table('google_conversion_log')
            ->where('order_id', $orderId)
            ->where('status', 'sent')
            ->countAllResults();

        return $result > 0;
    }

    /**
     * Registra conversao no banco
     */
    public function logConversion(int $orderId, string $transactionId, string $status = 'sent', ?string $response = null): bool
    {
        return $this->db->table('google_conversion_log')->insert([
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'event_type' => 'purchase',
            'sent_at' => date('Y-m-d H:i:s'),
            'status' => $status,
            'response' => $response,
        ]);
    }

    /**
     * Gera script de view_item (visualizacao de produto)
     */
    public function viewItemScript(array $product): string
    {
        $item = $this->formatItem($product);
        $value = (float) ($product['price'] ?? 0);

        $itemsJson = json_encode([$item], JSON_UNESCAPED_UNICODE);

        return <<<SCRIPT
<script>
// GA4 - View Item Event
gtag('event', 'view_item', {
    currency: '{$this->config->currency}',
    value: {$value},
    items: {$itemsJson}
});
</script>
SCRIPT;
    }

    /**
     * Gera script de add_to_cart
     */
    public function addToCartScript(array $product, int $quantity = 1): string
    {
        $product['quantity'] = $quantity;
        $item = $this->formatItem($product);
        $value = (float) ($product['price'] ?? 0) * $quantity;

        $itemsJson = json_encode([$item], JSON_UNESCAPED_UNICODE);

        return <<<SCRIPT
// GA4 - Add to Cart Event
gtag('event', 'add_to_cart', {
    currency: '{$this->config->currency}',
    value: {$value},
    items: {$itemsJson}
});
SCRIPT;
    }

    /**
     * Gera script de begin_checkout
     */
    public function beginCheckoutScript(array $cart): string
    {
        $items = $this->formatOrderItems($cart['items'] ?? []);
        $value = (float) ($cart['subtotal'] ?? 0);
        $coupon = $this->escapeString($cart['coupon_code'] ?? '');

        $itemsJson = json_encode($items, JSON_UNESCAPED_UNICODE);

        return <<<SCRIPT
<script>
// GA4 - Begin Checkout Event
gtag('event', 'begin_checkout', {
    currency: '{$this->config->currency}',
    value: {$value},
    coupon: '{$coupon}',
    items: {$itemsJson}
});
</script>
SCRIPT;
    }

    /**
     * Gera script completo de purchase (GA4 + Google Ads + Enhanced Conversions)
     */
    public function purchaseScript(array $order, array $customer = []): string
    {
        $orderId = (int) ($order['id'] ?? 0);
        $transactionId = $this->escapeString($order['order_number'] ?? $order['id'] ?? '');
        $value = (float) ($order['total'] ?? 0);
        $shipping = (float) ($order['shipping_cost'] ?? 0);
        $tax = (float) ($order['tax'] ?? 0);
        $discount = (float) ($order['discount'] ?? 0);
        $coupon = $this->escapeString($order['coupon_code'] ?? '');

        $items = $this->formatOrderItems($order['items'] ?? []);
        $itemsJson = json_encode($items, JSON_UNESCAPED_UNICODE);

        // Enhanced Conversions user data
        $userData = [];
        $userDataScript = '';

        if ($this->config->enhancedConversions && !empty($customer)) {
            $userData = $this->prepareUserData($customer);
            $userDataJson = json_encode($userData, JSON_UNESCAPED_UNICODE);
            $userDataScript = "gtag('set', 'user_data', {$userDataJson});";
        }

        // Google Ads conversion
        $adsConversionScript = '';
        if ($this->config->hasGoogleAds() && !empty($this->config->adsConversionLabel)) {
            $sendTo = $this->config->adsConversionId . '/' . $this->config->adsConversionLabel;
            $adsConversionScript = <<<ADS

// Google Ads - Conversion Event
gtag('event', 'conversion', {
    'send_to': '{$sendTo}',
    'value': {$value},
    'currency': '{$this->config->currency}',
    'transaction_id': '{$transactionId}'
});
ADS;
        }

        return <<<SCRIPT
<script>
(function() {
    // Verificar se ja foi enviado (cookie de protecao)
    var cookieName = 'gps_conversion_{$orderId}';
    if (document.cookie.indexOf(cookieName + '=1') !== -1) {
        console.log('Conversion already sent for order {$orderId}');
        return;
    }

    // Marcar como enviado (cookie expira em 1 dia)
    document.cookie = cookieName + '=1; max-age=86400; path=/; SameSite=Strict';

    {$userDataScript}

    // GA4 - Purchase Event
    gtag('event', 'purchase', {
        transaction_id: '{$transactionId}',
        value: {$value},
        currency: '{$this->config->currency}',
        tax: {$tax},
        shipping: {$shipping},
        coupon: '{$coupon}',
        items: {$itemsJson}
    });
    {$adsConversionScript}

    console.log('GPS Imports: Conversion sent for order {$transactionId}');
})();
</script>
SCRIPT;
    }

    /**
     * Gera o script base do gtag (para o head)
     */
    public function getGtagScript(): string
    {
        $trackingIds = $this->config->getTrackingIds();

        if (empty($trackingIds)) {
            return '';
        }

        $primaryId = $trackingIds[0];
        $configLines = [];

        foreach ($trackingIds as $id) {
            $debugParam = $this->config->debugMode ? ", { 'debug_mode': true }" : '';
            $configLines[] = "gtag('config', '{$id}'{$debugParam});";
        }

        $configs = implode("\n    ", $configLines);

        return <<<SCRIPT
<!-- Google tag (gtag.js) - GA4 & Google Ads -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$primaryId}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    {$configs}
</script>
SCRIPT;
    }

    /**
     * Gera script do Google Tag Manager (head)
     */
    public function getGTMHeadScript(): string
    {
        if (!$this->config->hasGTM()) {
            return '';
        }

        $containerId = $this->config->gtmContainerId;

        return <<<SCRIPT
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$containerId}');</script>
<!-- End Google Tag Manager -->
SCRIPT;
    }

    /**
     * Gera noscript do Google Tag Manager (body)
     */
    public function getGTMBodyScript(): string
    {
        if (!$this->config->hasGTM()) {
            return '';
        }

        $containerId = $this->config->gtmContainerId;

        return <<<SCRIPT
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$containerId}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
SCRIPT;
    }

    /**
     * Gera dataLayer push para GTM
     */
    public function dataLayerPush(string $event, array $data = []): string
    {
        $data['event'] = $event;
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        return "<script>dataLayer.push({$json});</script>";
    }

    /**
     * Retorna configuracao
     */
    public function getConfig(): Google
    {
        return $this->config;
    }
}
