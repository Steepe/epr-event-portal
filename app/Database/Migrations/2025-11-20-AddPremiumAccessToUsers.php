<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 20/11/2025
 * Time: 21:07
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPremiumAccessToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'premium_access' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'is_verified' // places column after is_verified
            ],
        ];

        $this->forge->addColumn('tbl_users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_users', 'premium_access');
    }
}
