<?php
$tawkPropertyId = trim((string) env('tawk.propertyId'));
$tawkWidgetId = trim((string) env('tawk.widgetId'));
$isValidTawkId = static fn (string $value): bool => preg_match('/\A[a-zA-Z0-9_-]{3,120}\z/', $value) === 1;
?>
<?php if ($isValidTawkId($tawkPropertyId) && $isValidTawkId($tawkWidgetId)): ?>
<!-- Tawk.to live support -->
<script>
    window.Tawk_API = window.Tawk_API || {};
    window.Tawk_LoadStart = new Date();
    (function () {
        var script = document.createElement('script');
        var firstScript = document.getElementsByTagName('script')[0];
        script.async = true;
        script.src = 'https://embed.tawk.to/<?php echo esc($tawkPropertyId, 'attr'); ?>/<?php echo esc($tawkWidgetId, 'attr'); ?>';
        script.charset = 'UTF-8';
        script.setAttribute('crossorigin', '*');
        firstScript.parentNode.insertBefore(script, firstScript);
    })();
</script>
<?php endif; ?>
