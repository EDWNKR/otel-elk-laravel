@if(config('activity_log.elastic_apm_rum.enabled', false) && config('activity_log.elastic_apm_rum.server_url'))
{{-- Elastic APM RUM Agent --}}
<script src="{{ config('activity_log.elastic_apm_rum.cdn_url') }}" crossorigin></script>
<script>
    elasticApm.init({
        serviceName: '{{ config("activity_log.elastic_apm_rum.service_name") }}',
        serverUrl: '{{ config("activity_log.elastic_apm_rum.server_url") }}',
        serviceVersion: '{{ config("activity_log.elastic_apm_rum.service_version") }}',
        environment: '{{ config("activity_log.elastic_apm_rum.environment") }}',
        pageLoadTransactionName: '{{ config("activity_log.elastic_apm_rum.page_load_transaction_name", "Page Load") }}',
        transactionSampleRate: {{ config("activity_log.elastic_apm_rum.transaction_sample_rate", 1.0) }},
        pageLoadSpanId: {{ config("activity_log.elastic_apm_rum.page_load_span_id", true) ? 'true' : 'false' }},
        @if(config('activity_log.elastic_apm_rum.secret_token'))
        secretToken: '{{ config("activity_log.elastic_apm_rum.secret_token") }}',
        @endif
        @if(config('activity_log.elastic_apm_rum.api_key'))
        apiKey: '{{ config("activity_log.elastic_apm_rum.api_key") }}',
        @endif
        @if(count(config('activity_log.elastic_apm_rum.distributed_tracing_origins', [])) > 0)
        distributedTracingOrigins: {!! json_encode(config('activity_log.elastic_apm_rum.distributed_tracing_origins')) !!},
        @endif
    });
</script>
@endif
