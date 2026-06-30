<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCouponsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['percentage', 'fixed', 'free_shipping'],
                'default' => 'percentage',
            ],
            'value' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'min_order_value' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0.00,
            ],
            'max_discount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'usage_limit' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'usage_limit_per_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'usage_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'applies_to' => [
                'type' => 'ENUM',
                'constraint' => ['all', 'products', 'categories', 'brands'],
                'default' => 'all',
            ],
            'product_ids' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'category_ids' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'brand_ids' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'exclude_sale_items' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'first_purchase_only' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'starts_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive', 'expired'],
                'default' => 'active',
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->addKey('status');
        $this->forge->addKey('starts_at');
        $this->forge->addKey('expires_at');

        $this->forge->createTable('coupons', true);

        // Coupon usage tracking
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'coupon_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'discount_applied' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('coupon_id');
        $this->forge->addKey('customer_id');
        $this->forge->addKey('order_id');
        $this->forge->addForeignKey('coupon_id', 'coupons', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('coupon_usage', true);
    }

    public function down()
    {
        $this->forge->dropTable('coupon_usage', true);
        $this->forge->dropTable('coupons', true);
    }
}
