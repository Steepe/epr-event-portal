<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 01/11/2025
 * Time: 10:06
 */


namespace App\Modules\Api\Controllers;

use App\Controllers\BaseController;
use App\Modules\Api\Models\TblSponsorsModel;
use CodeIgniter\API\ResponseTrait;

class SponsorsController extends BaseController
{
    use ResponseTrait;

    protected TblSponsorsModel $sponsors;

    public function __construct()
    {
        $this->sponsors = new TblSponsorsModel();
    }

    public function index()
    {
        try {
            $data = $this->sponsors->findAll();
            return $this->respond([
                'status' => 'success',
                'count'  => count($data),
                'data'   => $data
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Failed to load sponsors: ' . $e->getMessage());
            return $this->failServerError('Error fetching sponsors.');
        }
    }
}
