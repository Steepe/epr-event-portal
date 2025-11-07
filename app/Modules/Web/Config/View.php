<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 19:00
 */


namespace App\Modules\Web\Config;

use CodeIgniter\Config\BaseConfig;

class View extends BaseConfig
{
    /**
     * Tell CI4 to look here for views.
     */
    public $paths = [
        APPPATH . 'Modules/Web/Views',
    ];
}
