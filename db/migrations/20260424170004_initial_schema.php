<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialSchema extends AbstractMigration
{
    public function change(): void
    {
        // Roles Table
        $roles = $this->table('roles', ['id' => 'role_id']);
        $roles->addColumn('role_name', 'string', ['limit' => 50])
              ->addIndex(['role_name'], ['unique' => true])
              ->create();

        // Users Table
        $users = $this->table('users', ['id' => 'user_id']);
        $users->addColumn('username', 'string', ['limit' => 50])
              ->addColumn('email', 'string', ['limit' => 100])
              ->addColumn('password', 'string', ['limit' => 255])
              ->addColumn('role_id', 'integer') 
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addIndex(['email'], ['unique' => true])
              ->addForeignKey('role_id', 'roles', 'role_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->create();

        // Vendors Table
        $vendors = $this->table('vendors', ['id' => 'vendor_id']);
        $vendors->addColumn('user_id', 'integer')
                ->addColumn('vendor_name', 'string', ['limit' => 100])
                ->addColumn('contact_email', 'string', ['limit' => 100, 'null' => true])
                ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('user_id', 'users', 'user_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                ->create();

        // Discounts Table
        $discounts = $this->table('discounts', ['id' => 'discount_id']);
        $discounts->addColumn('discount_name', 'string', ['limit' => 100, 'null' => true])
                  ->addColumn('discount_type', 'string', ['limit' => 20]) // PostgreSQL standard
                  ->addColumn('discount_value', 'decimal', ['precision' => 10, 'scale' => 2])
                  ->addColumn('start_date', 'date')
                  ->addColumn('end_date', 'date')
                  ->addColumn('is_active', 'boolean', ['default' => true])
                  ->create();

        // Categories Table
        $categories = $this->table('categories', ['id' => 'category_id']);
        $categories->addColumn('category_name', 'string', ['limit' => 100])
                   ->addColumn('discount_id', 'integer', ['null' => true])
                   ->addIndex(['category_name'], ['unique' => true])
                   ->addForeignKey('discount_id', 'discounts', 'discount_id', ['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                   ->create();

        // Products Table
        $products = $this->table('products', ['id' => 'product_id']);
        $products->addColumn('name', 'string', ['limit' => 100])
                 ->addColumn('description', 'text', ['null' => true])
                 ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2])
                 ->addColumn('stock', 'integer')
                 ->addColumn('image_url', 'string', ['limit' => 255, 'null' => true])
                 ->addColumn('category_id', 'integer', ['null' => true])
                 ->addColumn('discount_id', 'integer', ['null' => true])
                 ->addColumn('vendor_id', 'integer', ['null' => true])
                 ->addForeignKey('category_id', 'categories', 'category_id', ['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                 ->addForeignKey('discount_id', 'discounts', 'discount_id', ['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                 ->addForeignKey('vendor_id', 'vendors', 'vendor_id', ['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                 ->create();

        // Wishlist Table
        $wishlist = $this->table('wishlist', ['id' => 'wishlist_id']);
        $wishlist->addColumn('user_id', 'integer')
                 ->addColumn('product_id', 'integer')
                 ->addColumn('added_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                 ->addIndex(['user_id', 'product_id'], ['unique' => true, 'name' => 'unique_user_product'])
                 ->addForeignKey('user_id', 'users', 'user_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                 ->addForeignKey('product_id', 'products', 'product_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                 ->create();

        // Orders Table
        $orders = $this->table('orders', ['id' => 'order_id']);
        $orders->addColumn('customer_id', 'integer')
               ->addColumn('order_status', 'string', ['limit' => 20]) // Change from Enum
               ->addColumn('total_amount', 'decimal', ['precision' => 10, 'scale' => 2])
               ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
               ->addForeignKey('customer_id', 'users', 'user_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
               ->create();

        // Order Items Table
        $orderItems = $this->table('order_items', ['id' => 'order_item_id']);
        $orderItems->addColumn('order_id', 'integer')
                   ->addColumn('product_id', 'integer')
                   ->addColumn('quantity', 'integer')
                   ->addColumn('price_at_purchase', 'decimal', ['precision' => 10, 'scale' => 2])
                   ->addForeignKey('order_id', 'orders', 'order_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                   ->addForeignKey('product_id', 'products', 'product_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                   ->create();

        // Shipping Table
        $shipping = $this->table('shipping', ['id' => 'shipping_id']);
        $shipping->addColumn('order_id', 'integer')
                 ->addColumn('shipping_status', 'string', ['limit' => 20]) // Change from Enum
                 ->addColumn('tracking_number', 'string', ['limit' => 100, 'null' => true])
                 ->addColumn('handled_by', 'integer', ['null' => true])
                 ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                 ->addForeignKey('order_id', 'orders', 'order_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                 ->addForeignKey('handled_by', 'users', 'user_id', ['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                 ->create();

        // Returns Table
        $returns = $this->table('returns', ['id' => 'return_id']);
        $returns->addColumn('order_id', 'integer')
                ->addColumn('reason', 'text', ['null' => true])
                ->addColumn('status', 'string', ['limit' => 20]) // Change from Enum
                ->addColumn('processed_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->addColumn('handled_by', 'integer', ['null' => true])
                ->addForeignKey('order_id', 'orders', 'order_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                ->addForeignKey('handled_by', 'users', 'user_id', ['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                ->create();

        // Cart Table
        $cart = $this->table('cart', ['id' => 'cart_id']);
        $cart->addColumn('customer_id', 'integer')
             ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
             ->addForeignKey('customer_id', 'users', 'user_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
             ->create();

        // Cart Items Table
        $cartItems = $this->table('cart_items', ['id' => 'cart_item_id']);
        $cartItems->addColumn('cart_id', 'integer')
                  ->addColumn('product_id', 'integer')
                  ->addColumn('quantity', 'integer')
                  ->addForeignKey('cart_id', 'cart', 'cart_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->addForeignKey('product_id', 'products', 'product_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->create();

        // Customer Details Table
        $customerDetails = $this->table('customerdetails', ['id' => 'detail_id']);
        $customerDetails->addColumn('user_id', 'integer')
                        ->addColumn('full_name', 'string', ['limit' => 100])
                        ->addColumn('billing_address', 'text', ['null' => true])
                        ->addColumn('shipping_address', 'text', ['null' => true])
                        ->addColumn('address', 'text', ['null' => true])
                        ->addColumn('phone', 'string', ['limit' => 20])
                        ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                        ->addForeignKey('user_id', 'users', 'user_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                        ->create();

        // Payments Table
        $payments = $this->table('payments', ['id' => 'payment_id']);
        $payments->addColumn('order_id', 'integer')
                 ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2])
                 ->addColumn('method', 'string', ['limit' => 30]) // Change from Enum
                 ->addColumn('status', 'string', ['limit' => 20, 'default' => 'Pending']) // Change from Enum
                 ->addColumn('transaction_date', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                 ->addForeignKey('order_id', 'orders', 'order_id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                 ->create();
    }
}