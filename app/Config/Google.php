<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Configuracoes do Google Analytics, Ads, Tag Manager e Merchant Center
 *
 * @package GPS Imports
 */
class Google extends BaseConfig
{
    /**
     * Google Analytics 4 Measurement ID
     * Formato: G-XXXXXXXXXX
     */
    public string $ga4MeasurementId = 'G-XXXXXXXXXX';

    /**
     * Google Ads Conversion ID
     * Formato: AW-XXXXXXXXXX
     */
    public string $adsConversionId = 'AW-18297254265';

    /**
     * Google Ads Conversion Label para evento de compra
     * Obtido no Google Ads ao criar a acao de conversao
     */
    public string $adsConversionLabel = 'XXXXXXXX';

    /**
     * Google Tag Manager Container ID
     * Formato: GTM-XXXXXXX
     */
    public string $gtmContainerId = '';

    /**
     * Google Merchant Center ID
     */
    public string $merchantCenterId = '';

    /**
     * Moeda padrao
     */
    public string $currency = 'BRL';

    /**
     * Habilitar debug mode (envia para DebugView do GA4)
     */
    public bool $debugMode = false;

    /**
     * Habilitar Enhanced Conversions
     */
    public bool $enhancedConversions = true;

    /**
     * Nome da loja para eventos
     */
    public string $storeName = 'GPS Imports';

    /**
     * Verifica se GA4 esta configurado
     */
    public function hasGA4(): bool
    {
        return !empty($this->ga4MeasurementId) && $this->ga4MeasurementId !== 'G-XXXXXXXXXX';
    }

    /**
     * Verifica se Google Ads esta configurado
     */
    public function hasGoogleAds(): bool
    {
        return !empty($this->adsConversionId) && $this->adsConversionId !== 'AW-XXXXXXXXXX';
    }

    /**
     * Verifica se GTM esta configurado
     */
    public function hasGTM(): bool
    {
        return !empty($this->gtmContainerId) && $this->gtmContainerId !== 'GTM-XXXXXXX';
    }

    /**
     * Retorna todos os IDs de tracking para o gtag
     */
    public function getTrackingIds(): array
    {
        $ids = [];

        if ($this->hasGA4()) {
            $ids[] = $this->ga4MeasurementId;
        }

        if ($this->hasGoogleAds()) {
            $ids[] = $this->adsConversionId;
        }

        return $ids;
    }
}
