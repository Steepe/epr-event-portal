<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 18:26
 */


namespace App\Modules\Api\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Modules\Api\Models\TblCountriesModel;

class CountriesController extends ResourceController
{
    protected string $modelName = TblCountriesModel::class;
    protected string $format    = 'json';

    /**
     * GET /api/countries
     * Returns a list of all countries
     */
    public function index()
    {
        $countries = $this->model->findAll();

        return $this->respond([
            'status'  => 'success',
            'message' => 'Countries retrieved successfully.',
            'data'    => $countries,
        ]);
    }
}
