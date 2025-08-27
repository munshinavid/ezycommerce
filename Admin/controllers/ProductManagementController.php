<?php
require_once __DIR__ . '/../models/Database.php';

class ProductManagementController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get all products with category, vendor, discount info
    public function getAllProducts() {
        return $this->db->select("
            SELECT p.*, c.category_name, v.vendor_name, d.discount_type, d.discount_value
            FROM Products p
            LEFT JOIN Categories c ON p.category_id = c.category_id
            LEFT JOIN Vendors v ON p.vendor_id = v.vendor_id
            LEFT JOIN Discounts d ON p.discount_id = d.discount_id
            ORDER BY p.product_id DESC
        ");
    }
    //get product by id
    public function getProductById($id) {
        $products = $this->db->select("
            SELECT p.*, c.category_name, v.vendor_name, d.discount_type, d.discount_value
            FROM Products p
            LEFT JOIN Categories c ON p.category_id = c.category_id
            LEFT JOIN Vendors v ON p.vendor_id = v.vendor_id
            LEFT JOIN Discounts d ON p.discount_id = d.discount_id
            WHERE p.product_id=?
        ", [$id]);
        return $products ? $products[0] : null;
    }

    // Get all categories
    public function getAllCategories() {
        return $this->db->select("SELECT * FROM Categories ORDER BY category_name ASC");
    }

    // Get all vendors
    public function getAllVendors() {
        return $this->db->select("SELECT * FROM Vendors ORDER BY vendor_name ASC");
    }

    // Get all discounts
    public function getAllDiscounts() {
        return $this->db->select("SELECT * FROM Discounts ORDER BY discount_value DESC");
    }

    // Add product
    public function addProduct($data) {
        return $this->db->execute("
            INSERT INTO Products (name, description, price, stock, category_id, vendor_id, discount_id, image_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $data['name'], $data['description'], $data['price'], $data['stock'],
            $data['category_id'], $data['vendor_id'] ?? null,
            $data['discount_id'] ?? null, $data['image_url'] ?? null
        ]);
    }

    // Update product
    public function updateProduct($id, $data) {
        return $this->db->execute("
            UPDATE Products 
            SET name=?, description=?, price=?, stock=?, category_id=?, vendor_id=?, discount_id=?, image_url=?
            WHERE product_id=?
        ", [
            $data['name'], $data['description'], $data['price'], $data['stock'],
            $data['category_id'], $data['vendor_id'] ?? null,
            $data['discount_id'] ?? null, $data['image_url'] ?? null,
            $id
        ]);
    }

    // Delete product
    public function deleteProduct($id) {
        return $this->db->execute("DELETE FROM Products WHERE product_id=?", [$id]);
    }
}
