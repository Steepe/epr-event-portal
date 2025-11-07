<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 13:55
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTblPointsGuideAndEarned extends Migration
{
    public function up()
    {
        // tbl_points_guide
        $this->forge->addField([
            'guide_id'   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'activity'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'short_name' => ['type' => 'VARCHAR', 'constraint' => 50],
            'image'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'points'     => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('guide_id', true);
        $this->forge->createTable('tbl_points_guide', true);

        // tbl_points_earned
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true],
            'short_name'  => ['type' => 'VARCHAR', 'constraint' => 50],
            'activity_id' => ['type' => 'INT', 'unsigned' => true],
            'points'      => ['type' => 'INT', 'default' => 0],
            'created_at'  => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'tbl_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('activity_id', 'tbl_points_guide', 'guide_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tbl_points_earned', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_points_earned', true);
        $this->forge->dropTable('tbl_points_guide', true);
    }
}
