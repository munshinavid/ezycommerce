<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class DummyDataSeeder extends AbstractSeed
{
    private function resetSeedTables(): void
    {
        //$this->execute('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'order_items',
            'returns',
            'shipping',
            'payments',
            'wishlist',
            'cart_items',
            'orders',
            'cart',
            'products',
            'vendors',
            'categories',
            'users',
            'roles',
            'customerdetails',
        ] as $tableName) {
            $this->table($tableName)->truncate();
        }

        //$this->execute('SET FOREIGN_KEY_CHECKS=1');
    }

    public function run(): void
    {
        $this->resetSeedTables();

        // ---------- 1. Roles ----------
        $this->table('roles')->insert([
            ['role_name' => 'Admin'],
            ['role_name' => 'Customer'],
            ['role_name' => 'Vendor']
        ])->save();

        // ---------- 2. Users ----------
        $usersData = [];
        $usersData[] = [
            'username' => 'admin',
            'email' => 'admin@ezycommerce.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role_id' => 1
        ];

        // 8 customers
        for ($i = 1; $i <= 8; $i++) {
            $usersData[] = [
                'username' => "customer$i",
                'email' => "customer$i@test.com",
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role_id' => 2
            ];
        }

        // 2 vendors
        for ($i = 1; $i <= 2; $i++) {
            $usersData[] = [
                'username' => "vendor$i",
                'email' => "vendor$i@test.com",
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role_id' => 3
            ];
        }

        $this->table('users')->insert($usersData)->save();

        // ---------- 3. Vendors ----------
        $this->table('vendors')->insert([
            [
                'user_id' => 10,
                'vendor_name' => 'TechWorld',
                'contact_email' => 'tech@shop.com'
            ],
            [
                'user_id' => 11,
                'vendor_name' => 'FashionHub',
                'contact_email' => 'fashion@shop.com'
            ]
        ])->save();

        // ---------- 4. Categories ----------
        $this->table('categories')->insert([
            ['category_name' => 'Electronics'],
            ['category_name' => 'Clothing'],
            ['category_name' => 'Home'],
        ])->save();

        // ---------- 5. Products ----------
        $productNames = [
            'Bluetooth Speaker','Smart Watch','Gaming Mouse','Mechanical Keyboard',
            'Denim Jacket','Casual Shirt','Sneakers','Hoodie',
            'Blender','Rice Cooker','Table Lamp','Vacuum Cleaner'
        ];

        $productsData = [];
        $imgIndex = 1;

        foreach ($productNames as $i => $name) {
            $productsData[] = [
                'name' => $name,
                'description' => "High quality $name for everyday use.",
                'price' => rand(10, 300),
                'stock' => rand(10, 150),
                'image_url' => "/ezycommerce/uploads/images/product{$imgIndex}.jpg",
                'category_id' => ($i % 3) + 1,
                'vendor_id' => ($i % 2) + 1
            ];

            $imgIndex++;
            if ($imgIndex > 6) $imgIndex = 1; // rotate images
        }

        $this->table('products')->insert($productsData)->save();

        // ---------- 6. Cart ----------
        $this->table('cart')->insert([
            ['customer_id' => 2],
            ['customer_id' => 3],
            ['customer_id' => 4],
        ])->save();

        // ---------- 7. Cart Items ----------
        $this->table('cart_items')->insert([
            ['cart_id' => 1, 'product_id' => 1, 'quantity' => 2],
            ['cart_id' => 1, 'product_id' => 3, 'quantity' => 1],
            ['cart_id' => 2, 'product_id' => 5, 'quantity' => 1],
            ['cart_id' => 3, 'product_id' => 2, 'quantity' => 4],
        ])->save();

        // ---------- 8. Wishlist ----------
        $this->table('wishlist')->insert([
            ['user_id' => 2, 'product_id' => 4],
            ['user_id' => 2, 'product_id' => 6],
            ['user_id' => 3, 'product_id' => 1],
        ])->save();

        // ---------- 9. Orders ----------
        $this->table('orders')->insert([
            [
                'customer_id' => 2,
                'total_amount' => 120.50,
                'order_status' => 'Pending',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'customer_id' => 3,
                'total_amount' => 75.00,
                'order_status' => 'Delivered',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ])->save();

        // ---------- 10. Order Items ----------
        $this->table('order_items')->insert([
            [
                'order_id' => 1,
                'product_id' => 1,
                'quantity' => 1,
                'price_at_purchase' => 60
            ],
            [
                'order_id' => 1,
                'product_id' => 2,
                'quantity' => 1,
                'price_at_purchase' => 60.5
            ],
            [
                'order_id' => 2,
                'product_id' => 3,
                'quantity' => 1,
                'price_at_purchase' => 75
            ]
        ])->save();
    }
}