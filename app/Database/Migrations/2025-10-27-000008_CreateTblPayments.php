<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 13:52
 */


namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblPayments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'payment_id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'currency' => ['type' => 'VARCHAR', 'constraint' => 5],
            'status' => ['type' => 'ENUM', 'constraint' => ['pending', 'success', 'failed', 'refunded'], 'default' => 'pending'],
            'provider' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'transaction_ref' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'metadata' => ['type' => 'TEXT', 'null' => true],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('payment_id', true);
        $this->forge->addForeignKey('user_id', 'tbl_users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('tbl_payments', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_payments', true);
    }
}
