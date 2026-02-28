<?php

namespace Edwinekr\OtelElkLaravel\Helpers;

class RumHelper
{
    /**
     * Check if RUM is enabled.
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return config('activity_log.elastic_apm_rum.enabled', false)
            && !empty(config('activity_log.elastic_apm_rum.server_url'));
    }

    /**
     * Get the RUM configuration.
     *
     * @return array
     */
    public static function getConfig(): array
    {
        return config('activity_log.elastic_apm_rum', []);
    }

    /**
     * Get the RUM initialization script as a string.
     * Useful for APIs or custom implementations.
     *
     * @return string|null
     */
    public static function getInitScript(): ?string
    {
        if (!self::isEnabled()) {
            return null;
        }

        $config = self::getConfig();
        
        $initConfig = [
            'serviceName' => $config['service_name'] ?? config('app.name', 'laravel'),
            'serverUrl' => $config['server_url'],
            'serviceVersion' => $config['service_version'] ?? '1.0.0',
            'environment' => $config['environment'] ?? config('app.env', 'production'),
            'pageLoadTransactionName' => $config['page_load_transaction_name'] ?? 'Page Load',
            'transactionSampleRate' => (float) ($config['transaction_sample_rate'] ?? 1.0),
            'pageLoadSpanId' => (bool) ($config['page_load_span_id'] ?? true),
        ];

        if (!empty($config['secret_token'])) {
            $initConfig['secretToken'] = $config['secret_token'];
        }

        if (!empty($config['api_key'])) {
            $initConfig['apiKey'] = $config['api_key'];
        }

        if (!empty($config['distributed_tracing_origins'])) {
            $initConfig['distributedTracingOrigins'] = $config['distributed_tracing_origins'];
        }

        return json_encode($initConfig, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get the CDN URL for the RUM agent.
     *
     * @return string
     */
    public static function getCdnUrl(): string
    {
        return config(
            'activity_log.elastic_apm_rum.cdn_url',
            'https://unpkg.com/@elastic/apm-rum@5.16.0/dist/bundles/elastic-apm-rum.umd.min.js'
        );
    }

    /**
     * Generate the full RUM script HTML.
     *
     * @return string|null
     */
    public static function renderScript(): ?string
    {
        if (!self::isEnabled()) {
            return null;
        }

        $cdnUrl = self::getCdnUrl();
        $initScript = self::getInitScript();

        return <<<HTML
<script src="{$cdnUrl}" crossorigin></script>
<script>
    elasticApm.init({$initScript});
</script>
HTML;
    }
}
