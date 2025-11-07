<?php
/**
 * Created by PhpStorm.
 * User: Oluwamayowa Steepe
 * Project: eprglobal
 * Date: 29/08/2021
 * Time: 02:42
 */
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Emergence Conference Global 2023</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Global EPR Emergence Conference" name="description" />
    <meta content="Creyatif - 08033868618 business@creyatif.com" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo asset_url('images/favicon.png')?>">

    <!-- App css -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo asset_url('css/bootstrap.css')?>" type="text/css">
    <link href="<?php echo asset_url('css/eprglobal.css')?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo asset_url('font-awesome/css/font-awesome.min.css')?>" rel="stylesheet" type="text/css" />


    <script>
        /* Storing user's device details in a variable*/
        let details = navigator.userAgent;
        //console.log(details);
        //alert(details);

        /* Creating a regular expression
        containing some mobile devices keywords
        to search it in details string*/
        let regexp = /android|iphone|ipod|kindle/i;

        /* Using test() method to search regexp in details
        it returns boolean value*/
        let isMobileDevice = regexp.test(details);

        if (isMobileDevice) {
            location.href = "<?php echo base_url('mobile/login')?>";
            //document.write("You are using a Mobile Device");
        }
    </script>

</head>
