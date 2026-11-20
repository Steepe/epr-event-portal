<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmergenceStore extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'product_id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 120],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'access_type' => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'external'],
            'access_url' => ['type' => 'VARCHAR', 'constraint' => 2048, 'null' => true],
            'price_amount' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'currency' => ['type' => 'VARCHAR', 'constraint' => 3, 'default' => 'NGN'],
            'button_label' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('product_id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('tbl_store_products', true);

        $this->forge->addField([
            'coupon_id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'product_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 80],
            'discount_type' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'fixed'],
            'discount_amount' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'currency' => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => true],
            'starts_at' => ['type' => 'DATETIME', 'null' => true],
            'expires_at' => ['type' => 'DATETIME', 'null' => true],
            'max_redemptions' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'redemption_count' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('coupon_id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->addKey('product_id');
        $this->forge->createTable('tbl_store_coupons', true);

        $this->forge->addField([
            'order_id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'order_ref' => ['type' => 'VARCHAR', 'constraint' => 100],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'product_id' => ['type' => 'INT', 'unsigned' => true],
            'coupon_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'currency' => ['type' => 'VARCHAR', 'constraint' => 3],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending'],
            'stripe_session_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'stripe_payment_intent_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'provider_payload' => ['type' => 'TEXT', 'null' => true],
            'paid_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('order_id', true);
        $this->forge->addUniqueKey('order_ref');
        $this->forge->addUniqueKey('stripe_session_id');
        $this->forge->addKey(['user_id', 'product_id']);
        $this->forge->createTable('tbl_store_orders', true);

        $this->forge->addField([
            'entitlement_id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'product_id' => ['type' => 'INT', 'unsigned' => true],
            'order_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
            'granted_by_admin_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'notes' => ['type' => 'VARCHAR', 'constraint' => 1000, 'null' => true],
            'granted_at' => ['type' => 'DATETIME'],
            'revoked_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('entitlement_id', true);
        $this->forge->addUniqueKey(['user_id', 'product_id']);
        $this->forge->addKey('order_id');
        $this->forge->createTable('tbl_store_entitlements', true);

        $this->forge->addField([
            'video_id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'product_id' => ['type' => 'INT', 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'bunny_embed_url' => ['type' => 'VARCHAR', 'constraint' => 2048],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('video_id', true);
        $this->forge->addKey('product_id');
        $this->forge->createTable('tbl_store_product_videos', true);

        $now = date('Y-m-d H:i:s');
        $this->db->table('tbl_store_products')->insertBatch([
            [
                'slug' => 'accelerate-2026-replays',
                'name' => 'Accelerate 2026 Replays',
                'description' => 'Watch the full Accelerate 2026 replay library on demand.',
                'access_type' => 'replays',
                'price_amount' => 2999,
                'currency' => 'NGN',
                'button_label' => 'Buy replay access',
                'is_active' => 1,
                'sort_order' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => '2027-coaching-interest',
                'name' => '2027 Coaching Sessions',
                'description' => 'Register your interest in the January coaching sessions and receive the attendee offer when it opens.',
                'access_type' => 'coaching_interest',
                'price_amount' => 0,
                'currency' => 'NGN',
                'button_label' => 'Register interest',
                'is_active' => 1,
                'sort_order' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'execution-engine',
                'name' => 'Execution Engine',
                'description' => 'Early access to the EPR execution and dashboard experience.',
                'access_type' => 'external',
                'price_amount' => 99,
                'currency' => 'USD',
                'button_label' => 'Buy early access',
                'is_active' => 1,
                'sort_order' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down()
    {
        foreach (['tbl_store_product_videos', 'tbl_store_entitlements', 'tbl_store_orders', 'tbl_store_coupons', 'tbl_store_products'] as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
