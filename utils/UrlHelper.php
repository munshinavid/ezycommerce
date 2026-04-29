<?php

class UrlHelper {
    /**
     * Normalize image URL to be absolute from application root
     * Handles relative paths and converts them to application URLs
     */
    public static function normalizeImageUrl(?string $imageUrl): string {
        if (empty($imageUrl)) {
            return '/uploads/images/placeholder.jpg';
        }

        // If already absolute HTTP(S), return as-is
        if (strpos($imageUrl, 'http://') === 0 || strpos($imageUrl, 'https://') === 0) {
            return $imageUrl;
        }

        // If already starts with /, return as-is
        if (strpos($imageUrl, '/') === 0) {
            return $imageUrl;
        }

        // Relative path - prepend /
        return '/' . ltrim($imageUrl, '/');
    }

    /**
     * Get full URL based on current environment (XAMPP or Docker)
     */
    public static function getBaseUrl(): string {
        // Check if running in XAMPP (request URI contains /ezycommerce)
        if (strpos($_SERVER['REQUEST_URI'] ?? '', '/ezycommerce') === 0) {
            return '/ezycommerce';
        }
        return '';
    }

    /**
     * Normalize product data to include proper image URLs
     */
    public static function normalizeProductData(array $product): array {
        if (isset($product['image_url'])) {
            $product['image_url'] = self::normalizeImageUrl($product['image_url']);
        }
        return $product;
    }

    /**
     * Normalize array of products
     */
    public static function normalizeProductsArray(array $products): array {
        return array_map([self::class, 'normalizeProductData'], $products);
    }
}
