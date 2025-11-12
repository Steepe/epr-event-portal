<?php
/**
 * Created by PhpStorm.
 * User: Oluwamayowa Steepe
 * Project: eprglobal
 * Date: 17/08/2021
 * Time: 04:44
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>EPR Global Conference Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="EPR Global Conference Portal" name="description" />
    <meta content="Creyatif Solutions - +2348033868618" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo asset_url('images/favicon.png')?>">

    <!-- App css -->
    <link rel="stylesheet" href="<?php echo asset_url('css/bootstrap.min.css')?>" type="text/css">
    <link href="<?php echo asset_url('css/eprglobal.css')?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo asset_url('font-awesome/css/font-awesome.min.css')?>" rel="stylesheet" type="text/css" />

    <!-- Matomo -->
    <script>
        var _paq = window._paq = window._paq || [];
        /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u="https://eprglobal.matomo.cloud/";
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            _paq.push(['setSiteId', '1']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src='https://cdn.matomo.cloud/eprglobal.matomo.cloud/matomo.js'; s.parentNode.insertBefore(g,s);
        })();
    </script>
    <!-- End Matomo Code -->
</head>
