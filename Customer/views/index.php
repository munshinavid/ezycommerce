<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase - Your Online Shopping Destination</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --secondary: #7c3aed;
            --accent: #dc2626;
            --accent-light: #ef4444;
            --light: #f8fafc;
            --dark: #1e293b;
            --success: #10b981;
            --warning: #f59e0b;
            --gray: #64748b;
            --light-gray: #e2e8f0;
            --border-radius: 8px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            background-color: #f8fafc;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Header Styles */
        header {
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .top-header {
            background-color: var(--dark);
            color: white;
            padding: 8px 0;
            font-size: 0.9rem;
        }

        .top-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-header-links a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            transition: color 0.3s;
        }

        .top-header-links a:hover {
            color: var(--primary-light);
        }

        .main-header {
            padding: 15px 0;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 8px;
            color: var(--secondary);
        }

        .search-bar {
            flex: 1;
            max-width: 500px;
            margin: 0 20px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid var(--light-gray);
            border-radius: 30px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
        }

        .search-bar input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }

        .search-bar button {
            position: absolute;
            right: 5px;
            top: 5px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 7px 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-bar button:hover {
            background: var(--primary-dark);
        }

        .header-actions {
            display: flex;
            align-items: center;
        }

        .header-action {
            margin-left: 20px;
            position: relative;
            cursor: pointer;
        }

        .header-action i {
            font-size: 1.3rem;
            color: var(--dark);
            transition: color 0.3s;
        }

        .header-action:hover i {
            color: var(--primary);
        }

        .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-menu {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            font-weight: bold;
        }

        /* Navigation */
        nav {
            background-color: white;
            border-top: 1px solid var(--light-gray);
        }

        .nav-links {
            display: flex;
            list-style: none;
            padding: 12px 0;
            overflow-x: auto;
        }

        .nav-links li {
            margin-right: 25px;
            flex-shrink: 0;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-links i {
            margin-right: 5px;
        }

        /* Breadcrumbs */
        .breadcrumbs {
            padding: 15px 0;
            font-size: 0.9rem;
            color: var(--gray);
        }

        .breadcrumbs a {
            color: var(--primary);
            text-decoration: none;
        }

        .breadcrumbs a:hover {
            text-decoration: underline;
        }

        /* Hero Section with Discount Banners */
        .hero {
            margin-bottom: 40px;
        }

        .discount-banners {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .main-banner {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: var(--border-radius);
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .banner-text h1 {
            font-size: 2.2rem;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .banner-text p {
            font-size: 1.1rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .countdown-timer {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .countdown-unit {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 12px;
            border-radius: 5px;
            text-align: center;
            min-width: 50px;
        }

        .countdown-value {
            font-size: 1.5rem;
            font-weight: 700;
            display: block;
        }

        .countdown-label {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .cta-button {
            display: inline-block;
            background: var(--accent);
            color: white;
            padding: 14px 32px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            box-shadow: 0 4px 6px rgba(220, 38, 38, 0.3);
        }

        .cta-button:hover {
            background: var(--accent-light);
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(220, 38, 38, 0.4);
        }

        .banner-image img {
            max-width: 100%;
            border-radius: var(--border-radius);
        }

        .side-banners {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .side-banner {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .side-banner h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .side-banner p {
            color: var(--gray);
            margin-bottom: 15px;
        }

        .banner-discount {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 10px;
        }

        /* Trust Badges */
        .trust-badges {
            display: flex;
            justify-content: center;
            gap: 30px;
            padding: 20px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 40px;
        }

        .trust-badge {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .trust-badge i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .trust-badge span {
            font-size: 0.9rem;
            color: var(--gray);
        }

        /* Category Cards */
        .categories-section {
            padding: 40px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .section-title h2 {
            font-size: 2rem;
            color: var(--dark);
            display: inline-block;
            padding-bottom: 10px;
        }

        .section-title h2:after {
            content: '';
            position: absolute;
            width: 80px;
            height: 3px;
            background: var(--primary);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .category-card {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
            padding: 25px 15px;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .category-icon {
            width: 70px;
            height: 70px;
            background: var(--light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.8rem;
            color: var(--primary);
        }

        .category-card h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .category-card p {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Products Section */
        .products-section {
            padding: 30px 0;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 40px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding: 0 20px;
        }

        .product-filters {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: var(--light);
            border: 1px solid var(--light-gray);
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .filter-btn:hover:not(.active) {
            background: var(--light-gray);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            padding: 0 20px;
        }

        .product-card {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--accent);
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 1;
        }

        .discount-badge {
            background: var(--success);
        }

        .stock-badge {
            background: var(--warning);
        }

        .product-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .product-card:hover .product-actions {
            opacity: 1;
        }

        .product-action {
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .product-action:hover {
            background: var(--primary);
            color: white;
        }

        .product-info {
            padding: 15px;
            display: flex;
            flex-direction: column;
            height: 180px;
        }

        .product-category {
            color: var(--gray);
            font-size: 0.8rem;
            margin-bottom: 5px;
        }

        .product-title {
            font-size: 1.1rem;
            margin-bottom: 8px;
            font-weight: 600;
            height: 2.6rem;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.3;
        }

        .product-price {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .current-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            margin-right: 10px;
        }

        .original-price {
            font-size: 1rem;
            color: var(--gray);
            text-decoration: line-through;
        }

        .discount-percent {
            background: var(--success);
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .product-rating {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .stars {
            color: var(--warning);
            margin-right: 5px;
        }

        .rating-count {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .add-to-cart {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: auto;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
        }

        .add-to-cart:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.4);
        }

        .add-to-cart i {
            margin-right: 8px;
        }

        .load-more {
            text-align: center;
            margin-top: 30px;
        }

        /* Recommendations Section */
        .recommendations-section {
            padding: 40px 0;
        }

        .recommendations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        /* Testimonials Section */
        .testimonials-section {
            padding: 40px 0;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 40px;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .testimonial-card {
            background: var(--light);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
            color: var(--gray);
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
        }

        .author-info h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .author-info p {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Newsletter */
        .newsletter {
            padding: 60px 0;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            text-align: center;
            margin-bottom: 40px;
            border-radius: var(--border-radius);
        }

        .newsletter h2 {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .newsletter p {
            font-size: 1.1rem;
            margin-bottom: 25px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            opacity: 0.9;
        }

        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
        }

        .newsletter-form input {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 30px 0 0 30px;
            font-size: 1rem;
            outline: none;
        }

        .newsletter-form button {
            background: var(--accent);
            color: white;
            border: none;
            padding: 0 25px;
            border-radius: 0 30px 30px 0;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .newsletter-form button:hover {
            background: var(--accent-light);
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 60px 0 20px;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-column h3:after {
            content: '';
            position: absolute;
            width: 40px;
            height: 2px;
            background: var(--primary);
            bottom: 0;
            left: 0;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        .social-links {
            display: flex;
            margin-top: 15px;
        }

        .social-links a {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            transition: background 0.3s;
        }

        .social-links a:hover {
            background: var(--primary);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #adb5bd;
            font-size: 0.9rem;
        }

        /* Loading Spinner */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            grid-column: 1 / -1;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Quick View Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: white;
            border-radius: var(--border-radius);
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalFadeIn 0.3s;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            color: var(--gray);
            cursor: pointer;
            z-index: 10;
            background: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow);
        }

        .close-modal:hover {
            color: var(--dark);
        }

        .quick-view-content {
            display: flex;
            flex-wrap: wrap;
        }

        .quick-view-image {
            flex: 1;
            min-width: 300px;
            padding: 20px;
        }

        .quick-view-image img {
            width: 100%;
            border-radius: var(--border-radius);
        }

        .quick-view-details {
            flex: 1;
            min-width: 300px;
            padding: 30px;
        }

        .quick-view-details h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .quick-view-category {
            color: var(--gray);
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .quick-view-price {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .quick-view-rating {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .quick-view-description {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .quick-view-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            overflow: hidden;
        }

        .quantity-btn {
            background: var(--light);
            border: none;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-input {
            width: 50px;
            height: 40px;
            border: none;
            text-align: center;
            font-size: 1rem;
        }

        .quick-view-features {
            margin-top: 20px;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .feature-list i {
            color: var(--success);
            margin-right: 10px;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .discount-banners {
                grid-template-columns: 1fr;
            }
            
            .search-bar {
                margin: 15px 0;
                max-width: 100%;
            }
            
            .header-content {
                flex-wrap: wrap;
            }
            
            .quick-view-content {
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                padding-bottom: 10px;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .product-filters {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 10px;
            }
            
            .newsletter-form {
                flex-direction: column;
            }
            
            .newsletter-form input {
                border-radius: 30px;
                margin-bottom: 10px;
            }
            
            .newsletter-form button {
                border-radius: 30px;
                padding: 12px;
            }
            
            .quick-view-actions {
                flex-direction: column;
            }
            
            .trust-badges {
                flex-wrap: wrap;
                gap: 20px;
            }
        }

        @media (max-width: 576px) {
            .top-header .container {
                flex-direction: column;
                text-align: center;
            }
            
            .top-header-links {
                margin-top: 5px;
            }
            
            .banner-text h1 {
                font-size: 1.8rem;
            }
            
            .product-grid {
                grid-template-columns: 1fr;
            }
            
            .main-banner {
                padding: 25px;
                flex-direction: column;
                text-align: center;
            }
            
            .banner-image {
                margin-top: 20px;
            }
            
            .category-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="container">
            <div class="top-header-text">
                <span>Free shipping on orders over $50 | 30-day money-back guarantee</span>
            </div>
            <div class="top-header-links">
                <a href="#"><i class="fas fa-phone"></i> +1 (555) 123-4567</a>
                <a href="#"><i class="fas fa-envelope"></i> support@shopease.com</a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header>
        <div class="main-header">
            <div class="container">
                <div class="header-content">
                    <a href="#" class="logo">
                        <i class="fas fa-shopping-bag"></i>
                        ShopEase
                    </a>
                    
                    <div class="search-bar">
                        <input type="text" placeholder="Search for products...">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                    
                    <div class="header-actions">
                        <div class="header-action user-menu">
                            <div class="user-avatar">JS</div>
                            <span>John Smith</span>
                        </div>
                        <div class="header-action">
                            <i class="far fa-heart"></i>
                            <span class="badge">3</span>
                        </div>
                        <div class="header-action">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge">2</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation with Categories -->
        <nav>
            <div class="container">
                <ul class="nav-links" id="categories-nav">
                    <!-- Categories will be loaded here via AJAX -->
                    <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                    <div class="loading">
                        <div class="spinner"></div>
                    </div>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Breadcrumbs -->
    <div class="container">
        <div class="breadcrumbs">
            <a href="#">Home</a> / <span>All Products</span>
        </div>
    </div>

    <!-- Hero Section with Discount Banners -->
    <section class="hero">
        <div class="container">
            <div class="discount-banners" id="discount-banners">
                <!-- Discount banners will be loaded here via AJAX -->
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Badges -->
    <div class="container">
        <div class="trust-badges">
            <div class="trust-badge">
                <i class="fas fa-shield-alt"></i>
                <span>Secure Payment</span>
            </div>
            <div class="trust-badge">
                <i class="fas fa-truck"></i>
                <span>Free Shipping</span>
            </div>
            <div class="trust-badge">
                <i class="fas fa-undo-alt"></i>
                <span>30-Day Returns</span>
            </div>
            <div class="trust-badge">
                <i class="fas fa-headset"></i>
                <span>24/7 Support</span>
            </div>
        </div>
    </div>

    <!-- Category Cards -->
    <section class="categories-section">
        <div class="container">
            <div class="section-title">
                <h2>Shop By Category</h2>
            </div>
            <div class="category-grid" id="categories-container">
                <!-- Categories will be loaded here via AJAX -->
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Featured Products</h2>
                <div class="product-filters" id="product-filters">
                    <!-- Filter buttons will be loaded here via AJAX -->
                </div>
            </div>
            <div class="product-grid" id="products-container">
                <!-- Products will be loaded here via AJAX -->
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
            <div class="load-more">
                <button class="cta-button" id="load-more-btn">Load More Products</button>
            </div>
        </div>
    </section>

    <!-- Recommendations Section -->
    <section class="recommendations-section">
        <div class="container">
            <div class="section-title">
                <h2>You May Also Like</h2>
            </div>
            <div class="recommendations-grid" id="recommendations-container">
                <!-- Recommended products will be loaded here via AJAX -->
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-title">
                <h2>What Our Customers Say</h2>
            </div>
            <div class="testimonials-grid" id="testimonials-container">
                <!-- Testimonials will be loaded here via AJAX -->
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter">
        <div class="container">
            <h2>Subscribe to Our Newsletter</h2>
            <p>Get the latest updates on new products and upcoming sales</p>
            <form class="newsletter-form" id="newsletter-form">
                <input type="email" placeholder="Your email address" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>ShopEase</h3>
                    <p>Your one-stop destination for all your shopping needs. Quality products at affordable prices.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Products</a></li>
                        <li><a href="#">Deals</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Customer Service</h3>
                    <ul class="footer-links">
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Shipping Policy</a></li>
                        <li><a href="#">Returns & Refunds</a></li>
                        <li><a href="#">Track Order</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Info</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Commerce St, City, State 12345</li>
                        <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope"></i> support@shopease.com</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 ShopEase. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Quick View Modal -->
    <div class="modal" id="quick-view-modal">
        <div class="modal-content">
            <span class="close-modal" id="close-modal">&times;</span>
            <div class="quick-view-content" id="quick-view-content">
                <!-- Quick view content will be loaded here via AJAX -->
            </div>
        </div>
    </div>

    <script>
        // DOM Content Loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Load categories, discounts, products, recommendations, and testimonials
            loadCategories();
            loadDiscountBanners();
            loadProducts();
            loadRecommendations();
            loadTestimonials();
            
            // Newsletter form submission
            document.getElementById('newsletter-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const email = this.querySelector('input[type="email"]').value;
                subscribeNewsletter(email);
            });
            
            // Search functionality
            document.querySelector('.search-bar button').addEventListener('click', function() {
                const searchTerm = document.querySelector('.search-bar input').value;
                if (searchTerm.trim() !== '') {
                    performSearch(searchTerm);
                }
            });
            
            // Load more products
            document.getElementById('load-more-btn').addEventListener('click', loadMoreProducts);
            
            // Quick view modal
            document.getElementById('close-modal').addEventListener('click', closeModal);
            
            // Close modal when clicking outside
            document.getElementById('quick-view-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
            
            // Add to cart and wishlist functionality
            document.addEventListener('click', function(e) {
                if (e.target.closest('.add-to-cart')) {
                    const productId = e.target.closest('.product-card').dataset.productId;
                    addToCart(productId);
                }
                
                if (e.target.closest('.product-action.wishlist')) {
                    const productId = e.target.closest('.product-card').dataset.productId;
                    addToWishlist(productId);
                }
                
                if (e.target.closest('.quick-view-btn')) {
                    const productId = e.target.closest('.product-card').dataset.productId;
                    openQuickView(productId);
                }
            });
        });

        // Global variables
        let currentPage = 1;
        let currentCategory = 'all';
        let allProducts = [];

        // Load Categories via AJAX
        function loadCategories() {
            // In a real implementation, this would be an AJAX call to your PHP backend
            // For now, we'll simulate the data
            setTimeout(() => {
                const categories = [
                    { id: 1, name: "Electronics", icon: "fas fa-laptop", productCount: 125 },
                    { id: 2, name: "Fashion", icon: "fas fa-tshirt", productCount: 89 },
                    { id: 3, name: "Home & Garden", icon: "fas fa-home", productCount: 76 },
                    { id: 4, name: "Beauty", icon: "fas fa-spa", productCount: 54 },
                    { id: 5, name: "Sports", icon: "fas fa-basketball-ball", productCount: 42 },
                    { id: 6, name: "Toys", icon: "fas fa-gamepad", productCount: 38 },
                    { id: 7, name: "Books", icon: "fas fa-book", productCount: 67 },
                    { id: 8, name: "Automotive", icon: "fas fa-car", productCount: 31 }
                ];
                
                // Load categories in navigation
                const navContainer = document.getElementById('categories-nav');
                navContainer.innerHTML = '<li><a href="#"><i class="fas fa-home"></i> Home</a></li>';
                
                categories.forEach(category => {
                    const categoryItem = document.createElement('li');
                    categoryItem.innerHTML = `
                        <a href="#" data-category-id="${category.id}">
                            <i class="${category.icon}"></i> ${category.name}
                        </a>
                    `;
                    navContainer.appendChild(categoryItem);
                });
                
                // Load categories in category grid
                const gridContainer = document.getElementById('categories-container');
                gridContainer.innerHTML = '';
                
                categories.forEach(category => {
                    const categoryCard = document.createElement('a');
                    categoryCard.className = 'category-card';
                    categoryCard.href = '#';
                    categoryCard.dataset.categoryId = category.id;
                    categoryCard.innerHTML = `
                        <div class="category-icon">
                            <i class="${category.icon}"></i>
                        </div>
                        <h3>${category.name}</h3>
                        <p>${category.productCount} Products</p>
                    `;
                    gridContainer.appendChild(categoryCard);
                });
                
                // Add event listeners to category links
                document.querySelectorAll('a[data-category-id]').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const categoryId = this.dataset.categoryId;
                        filterProductsByCategory(categoryId);
                    });
                });
            }, 1000);
        }

        // Load Discount Banners via AJAX
        function loadDiscountBanners() {
            // In a real implementation, this would be an AJAX call to your PHP backend
            // For now, we'll simulate the data
            setTimeout(() => {
                const discounts = [
                    {
                        id: 1,
                        name: "Summer Sale",
                        discountValue: 50,
                        discountType: "percentage",
                        description: "Get up to 50% off on all summer items",
                        image: "https://images.unsplash.com/photo-1607082350899-7e105aa886ae?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                        endDate: "2023-08-31T23:59:59"
                    },
                    {
                        id: 2,
                        name: "Flash Deal",
                        discountValue: 30,
                        discountType: "percentage",
                        description: "Limited time offer - 30% off electronics",
                        image: null,
                        endDate: "2023-07-20T23:59:59"
                    },
                    {
                        id: 3,
                        name: "Free Shipping",
                        discountValue: 0,
                        discountType: "fixed",
                        description: "Free shipping on all orders over $50",
                        image: null,
                        endDate: null
                    }
                ];
                
                const container = document.getElementById('discount-banners');
                container.innerHTML = '';
                
                // Main banner (first discount)
                const mainBanner = document.createElement('div');
                mainBanner.className = 'main-banner';
                
                // Countdown timer for the main banner
                let countdownHTML = '';
                if (discounts[0].endDate) {
                    countdownHTML = `
                        <div class="countdown-timer">
                            <div class="countdown-unit">
                                <span class="countdown-value" id="countdown-days">00</span>
                                <span class="countdown-label">Days</span>
                            </div>
                            <div class="countdown-unit">
                                <span class="countdown-value" id="countdown-hours">00</span>
                                <span class="countdown-label">Hours</span>
                            </div>
                            <div class="countdown-unit">
                                <span class="countdown-value" id="countdown-minutes">00</span>
                                <span class="countdown-label">Minutes</span>
                            </div>
                            <div class="countdown-unit">
                                <span class="countdown-value" id="countdown-seconds">00</span>
                                <span class="countdown-label">Seconds</span>
                            </div>
                        </div>
                    `;
                }
                
                mainBanner.innerHTML = `
                    <div class="banner-text">
                        <h1>${discounts[0].name} - Up to ${discounts[0].discountValue}% Off</h1>
                        <p>${discounts[0].description}</p>
                        ${countdownHTML}
                        <button class="cta-button">Shop Now</button>
                    </div>
                    <div class="banner-image">
                        <img src="${discounts[0].image}" alt="${discounts[0].name}">
                    </div>
                `;
                container.appendChild(mainBanner);
                
                // Start countdown if applicable
                if (discounts[0].endDate) {
                    startCountdown(discounts[0].endDate);
                }
                
                // Side banners (other discounts)
                const sideBanners = document.createElement('div');
                sideBanners.className = 'side-banners';
                
                for (let i = 1; i < discounts.length; i++) {
                    const discount = discounts[i];
                    const sideBanner = document.createElement('div');
                    sideBanner.className = 'side-banner';
                    
                    let discountText = '';
                    if (discount.discountType === 'percentage') {
                        discountText = `${discount.discountValue}% OFF`;
                    } else {
                        discountText = discount.name;
                    }
                    
                    sideBanner.innerHTML = `
                        <div class="banner-discount">${discountText}</div>
                        <h3>${discount.name}</h3>
                        <p>${discount.description}</p>
                        <button class="cta-button" style="padding: 10px 20px; font-size: 0.9rem;">Shop Now</button>
                    `;
                    sideBanners.appendChild(sideBanner);
                }
                
                container.appendChild(sideBanners);
            }, 1200);
        }

        // Start Countdown Timer
        function startCountdown(endDate) {
            const countdownDate = new Date(endDate).getTime();
            
            const timer = setInterval(function() {
                const now = new Date().getTime();
                const distance = countdownDate - now;
                
                if (distance < 0) {
                    clearInterval(timer);
                    document.querySelector('.countdown-timer').innerHTML = "Offer Expired";
                    return;
                }
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                document.getElementById('countdown-days').textContent = days.toString().padStart(2, '0');
                document.getElementById('countdown-hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('countdown-minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('countdown-seconds').textContent = seconds.toString().padStart(2, '0');
            }, 1000);
        }

        // Load Products via AJAX
        function loadProducts() {
            // In a real implementation, this would be an AJAX call to your PHP backend
            // For now, we'll simulate the data
            setTimeout(() => {
                const products = [
                    { 
                        id: 1, 
                        name: "Wireless Bluetooth Headphones with Noise Cancellation", 
                        category: "Electronics", 
                        categoryId: 1,
                        price: 79.99, 
                        originalPrice: 99.99,
                        discountId: 1,
                        image: "https://images.unslib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80",
                        rating: 4.5,
                        reviewCount: 128,
                        badge: "Sale",
                        stock: 3,
                        description: "High-quality wireless headphones with active noise cancellation for immersive audio experience.",
                        features: ["Active Noise Cancellation", "30-hour battery life", "Bluetooth 5.0", "Fast charging"]
                    },
                    { 
                        id: 2, 
                        name: "Smart Fitness Watch with Heart Rate Monitor", 
                        category: "Electronics", 
                        categoryId: 1,
                        price: 149.99, 
                        originalPrice: 199.99,
                        discountId: 2,
                        image: "https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80",
                        rating: 4.7,
                        reviewCount: 89,
                        badge: "Hot",
                        stock: 12,
                        description: "Advanced fitness tracker with heart rate monitoring, GPS, and smartphone notifications.",
                        features: ["Heart rate monitor", "GPS tracking", "Water resistant", "7-day battery"]
                    },
                    { 
                        id: 3, 
                        name: "Modern Coffee Maker with Programmable Timer", 
                        category: "Home & Garden", 
                        categoryId: 3,
                        price: 89.99, 
                        originalPrice: 119.99,
                        discountId: null,
                        image: "https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80",
                        rating: 4.3,
                        reviewCount: 64,
                        badge: "New",
                        stock: 25,
                        description: "Programmable coffee maker with thermal carafe to keep your coffee hot for hours.",
                        features: ["Programmable timer", "Thermal carafe", "24-hour digital clock", "Auto shut-off"]
                    },
                    { 
                        id: 4, 
                        name: "Men's Casual Shirt - Premium Cotton", 
                        category: "Fashion", 
                        categoryId: 2,
                        price: 39.99, 
                        originalPrice: 49.99,
                        discountId: 1,
                        image: "https://images.unsplash.com/photo-1596755094514-f87e34085b2c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80",
                        rating: 4.2,
                        reviewCount: 42,
                        badge: null,
                        stock: 8,
                        description: "Comfortable casual shirt made from premium cotton with a modern fit.",
                        features: ["100% premium cotton", "Modern fit", "Machine washable", "Wrinkle-resistant"]
                    },
                    { 
                        id: 5, 
                        name: "Wireless Gaming Mouse with RGB Lighting", 
                        category: "Electronics", 
                        categoryId: 1,
                        price: 59.99, 
                        originalPrice: 79.99,
                        discountId: 2,
                        image: "https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80",
                        rating: 4.6,
                        reviewCount: 156,
                        badge: "Sale",
                        stock: 15,
                        description: "High-precision wireless gaming mouse with customizable RGB lighting.",
                        features: ["16000 DPI sensor", "RGB lighting", "Programmable buttons", "50-hour battery"]
                    },
                    { 
                        id: 6, 
                        name: "Yoga Mat with Carrying Strap", 
                        category: "Sports", 
                        categoryId: 5,
                        price: 29.99, 
                        originalPrice: 39.99,
                        discountId: 1,
                        image: "https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80",
                        rating: 4.4,
                        reviewCount: 78,
                        badge: null,
                        stock: 32,
                        description: "Non-slip yoga mat with carrying strap for your fitness routine.",
                        features: ["Non-slip surface", "Eco-friendly material", "Comes with carrying strap", "Easy to clean"]
                    },
                    { 
                        id: 7, 
                        name: "Ceramic Cookware Set - 10 Pieces", 
                        category: "Home & Garden", 
                        categoryId: 3,
                        price: 129.99, 
                        originalPrice: 179.99,
                        discountId: null,
                        image: "https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80",
                        rating: 4.5,
                        reviewCount: 93,
                        badge: "New",
                        stock: 7,
                        description: "Non-stick ceramic cookware set for healthy and easy cooking.",
                        features: ["Non-stick ceramic coating", "Oven safe to 450°F", "Dishwasher safe", "10-piece set"]
                    },
                    { 
                        id: 8, 
                        name: "Women's Running Shoes - Lightweight", 
                        category: "Fashion", 
                        categoryId: 2,
                        price: 79.99, 
                        originalPrice: 99.99,
                        discountId: 1,
                        image: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80",
                        rating: 4.3,
                        reviewCount: 67,
                        badge: "Sale",
                        stock: 4,
                        description: "Lightweight running shoes with cushioning for maximum comfort.",
                        features: ["Lightweight design", "Cushioned insole", "Breathable mesh", "Durable rubber sole"]
                    }
                ];
                
                allProducts = products;
                displayProducts(products);
                setupFilters();
            }, 1500);
        }

        // Display Products
        function displayProducts(products) {
            const container = document.getElementById('products-container');
            container.innerHTML = '';
            
            if (products.length === 0) {
                container.innerHTML = '<div class="no-products">No products found matching your criteria.</div>';
                return;
            }
            
            products.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';
                productCard.dataset.productId = product.id;
                productCard.dataset.categoryId = product.categoryId;
                
                let badgeHTML = '';
                if (product.badge) {
                    badgeHTML = `<div class="product-badge ${product.discountId ? 'discount-badge' : ''}">${product.badge}</div>`;
                }
                
                // Add stock badge if low stock
                let stockBadgeHTML = '';
                if (product.stock < 5) {
                    stockBadgeHTML = `<div class="product-badge stock-badge">Only ${product.stock} left!</div>`;
                }
                
                const starsHTML = generateStars(product.rating);
                
                let discountHTML = '';
                if (product.originalPrice && product.originalPrice > product.price) {
                    const discountPercent = Math.round((1 - product.price / product.originalPrice) * 100);
                    discountHTML = `<span class="discount-percent">-${discountPercent}%</span>`;
                }
                
                let originalPriceHTML = '';
                if (product.originalPrice && product.originalPrice > product.price) {
                    originalPriceHTML = `<span class="original-price">$${product.originalPrice.toFixed(2)}</span>`;
                }
                
                productCard.innerHTML = `
                    ${badgeHTML}
                    ${stockBadgeHTML}
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.name}">
                        <div class="product-actions">
                            <div class="product-action wishlist">
                                <i class="far fa-heart"></i>
                            </div>
                            <div class="product-action quick-view-btn">
                                <i class="far fa-eye"></i>
                            </div>
                            <div class="product-action compare">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category">${product.category}</div>
                        <h3 class="product-title">${product.name}</h3>
                        <div class="product-price">
                            <span class="current-price">$${product.price.toFixed(2)}</span>
                            ${originalPriceHTML}
                            ${discountHTML}
                        </div>
                        <div class="product-rating">
                            <div class="stars">
                                ${starsHTML}
                            </div>
                            <span class="rating-count">(${product.reviewCount})</span>
                        </div>
                        <button class="add-to-cart">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                `;
                container.appendChild(productCard);
            });
        }

        // Setup Filters
        function setupFilters() {
            const filtersContainer = document.getElementById('product-filters');
            
            const filters = [
                { id: 'all', name: 'All Products' },
                { id: 'discount', name: 'On Sale' },
                { id: 'new', name: 'New Arrivals' },
                { id: 'bestseller', name: 'Bestsellers' }
            ];
            
            filters.forEach(filter => {
                const filterBtn = document.createElement('button');
                filterBtn.className = 'filter-btn';
                if (filter.id === 'all') filterBtn.classList.add('active');
                filterBtn.dataset.filter = filter.id;
                filterBtn.textContent = filter.name;
                filtersContainer.appendChild(filterBtn);
            });
            
            // Add event listeners to filter buttons
            filtersContainer.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Update active button
                    filtersContainer.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter products
                    const filterType = this.dataset.filter;
                    filterProducts(filterType);
                });
            });
        }

        // Filter Products
        function filterProducts(filterType) {
            let filteredProducts = [...allProducts];
            
            switch(filterType) {
                case 'discount':
                    filteredProducts = allProducts.filter(p => p.discountId !== null);
                    break;
                case 'new':
                    // In a real implementation, you would check a "isNew" flag or date
                    filteredProducts = allProducts.filter(p => p.badge === 'New');
                    break;
                case 'bestseller':
                    // In a real implementation, you would check sales data
                    filteredProducts = allProducts.filter(p => p.reviewCount > 100);
                    break;
                default:
                    // 'all' - show all products
                    break;
            }
            
            // Also apply category filter if active
            if (currentCategory !== 'all') {
                filteredProducts = filteredProducts.filter(p => p.categoryId.toString() === currentCategory);
            }
            
            displayProducts(filteredProducts);
        }

        // Filter Products by Category
        function filterProductsByCategory(categoryId) {
            currentCategory = categoryId;
            
            let filteredProducts = [...allProducts];
            
            if (categoryId !== 'all') {
                filteredProducts = allProducts.filter(p => p.categoryId.toString() === categoryId);
            }
            
            // Also apply type filter if active
            const activeFilter = document.querySelector('.filter-btn.active');
            if (activeFilter && activeFilter.dataset.filter !== 'all') {
                const filterType = activeFilter.dataset.filter;
                switch(filterType) {
                    case 'discount':
                        filteredProducts = filteredProducts.filter(p => p.discountId !== null);
                        break;
                    case 'new':
                        filteredProducts = filteredProducts.filter(p => p.badge === 'New');
                        break;
                    case 'bestseller':
                        filteredProducts = filteredProducts.filter(p => p.reviewCount > 100);
                        break;
                }
            }
            
            displayProducts(filteredProducts);
        }

        // Load Recommendations
        function loadRecommendations() {
            // In a real implementation, this would be an AJAX call to your PHP backend
            setTimeout(() => {
                // Use the last 4 products as recommendations for demo purposes
                const recommendations = allProducts.slice(-4);
                const container = document.getElementById('recommendations-container');
                container.innerHTML = '';
                
                recommendations.forEach(product => {
                    const productCard = document.createElement('div');
                    productCard.className = 'product-card';
                    productCard.dataset.productId = product.id;
                    
                    let badgeHTML = '';
                    if (product.badge) {
                        badgeHTML = `<div class="product-badge ${product.discountId ? 'discount-badge' : ''}">${product.badge}</div>`;
                    }
                    
                    const starsHTML = generateStars(product.rating);
                    
                    let discountHTML = '';
                    if (product.originalPrice && product.originalPrice > product.price) {
                        const discountPercent = Math.round((1 - product.price / product.originalPrice) * 100);
                        discountHTML = `<span class="discount-percent">-${discountPercent}%</span>`;
                    }
                    
                    let originalPriceHTML = '';
                    if (product.originalPrice && product.originalPrice > product.price) {
                        originalPriceHTML = `<span class="original-price">$${product.originalPrice.toFixed(2)}</span>`;
                    }
                    
                    productCard.innerHTML = `
                        ${badgeHTML}
                        <div class="product-image">
                            <img src="${product.image}" alt="${product.name}">
                            <div class="product-actions">
                                <div class="product-action wishlist">
                                    <i class="far fa-heart"></i>
                                </div>
                                <div class="product-action quick-view-btn">
                                    <i class="far fa-eye"></i>
                                </div>
                            </div>
                        </div>
                        <div class="product-info">
                            <div class="product-category">${product.category}</div>
                            <h3 class="product-title">${product.name}</h3>
                            <div class="product-price">
                                <span class="current-price">$${product.price.toFixed(2)}</span>
                                ${originalPriceHTML}
                                ${discountHTML}
                            </div>
                            <div class="product-rating">
                                <div class="stars">
                                    ${starsHTML}
                                </div>
                                <span class="rating-count">(${product.reviewCount})</span>
                            </div>
                            <button class="add-to-cart">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    `;
                    container.appendChild(productCard);
                });
            }, 1000);
        }

        // Load Testimonials
        function loadTestimonials() {
            // In a real implementation, this would be an AJAX call to your PHP backend
            setTimeout(() => {
                const testimonials = [
                    {
                        id: 1,
                        text: "I've been shopping with ShopEase for over a year now and I'm always impressed with their product quality and customer service. Highly recommended!",
                        author: "Sarah Johnson",
                        role: "Regular Customer",
                        avatar: "SJ"
                    },
                    {
                        id: 2,
                        text: "The return policy is fantastic! I had to return a product once and the process was so smooth. This is why I keep coming back to ShopEase.",
                        author: "Michael Chen",
                        role: "Satisfied Customer",
                        avatar: "MC"
                    },
                    {
                        id: 3,
                        text: "Fast shipping and great prices. I compared several online stores and ShopEase consistently offers the best value for money.",
                        author: "Emily Rodriguez",
                        role: "First-time Buyer",
                        avatar: "ER"
                    }
                ];
                
                const container = document.getElementById('testimonials-container');
                container.innerHTML = '';
                
                testimonials.forEach(testimonial => {
                    const testimonialCard = document.createElement('div');
                    testimonialCard.className = 'testimonial-card';
                    testimonialCard.innerHTML = `
                        <div class="testimonial-text">
                            "${testimonial.text}"
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">${testimonial.avatar}</div>
                            <div class="author-info">
                                <h4>${testimonial.author}</h4>
                                <p>${testimonial.role}</p>
                            </div>
                        </div>
                    `;
                    container.appendChild(testimonialCard);
                });
            }, 1200);
        }

        // Load More Products
        function loadMoreProducts() {
            // In a real implementation, this would fetch the next page of products from the backend
            // For now, we'll just show a message
            showNotification('Loading more products...');
            
            // Simulate API call delay
            setTimeout(() => {
                showNotification('More products loaded successfully!');
            }, 1000);
        }

        // Open Quick View Modal
        function openQuickView(productId) {
            // Find the product
            const product = allProducts.find(p => p.id == productId);
            if (!product) return;
            
            const modal = document.getElementById('quick-view-modal');
            const content = document.getElementById('quick-view-content');
            
            const starsHTML = generateStars(product.rating);
            
            let originalPriceHTML = '';
            if (product.originalPrice && product.originalPrice > product.price) {
                originalPriceHTML = `<span class="original-price">$${product.originalPrice.toFixed(2)}</span>`;
            }
            
            let featuresHTML = '';
            if (product.features) {
                featuresHTML = `
                    <div class="quick-view-features">
                        <h3>Features</h3>
                        <ul class="feature-list">
                            ${product.features.map(feature => `<li><i class="fas fa-check"></i> ${feature}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }
            
            content.innerHTML = `
                <div class="quick-view-image">
                    <img src="${product.image}" alt="${product.name}">
                </div>
                <div class="quick-view-details">
                    <h2>${product.name}</h2>
                    <div class="quick-view-category">${product.category}</div>
                    <div class="quick-view-price">
                        <span class="current-price">$${product.price.toFixed(2)}</span>
                        ${originalPriceHTML}
                    </div>
                    <div class="quick-view-rating">
                        <div class="stars">${starsHTML}</div>
                        <span class="rating-count">(${product.reviewCount} reviews)</span>
                    </div>
                    <div class="quick-view-description">
                        ${product.description}
                    </div>
                    <div class="quick-view-actions">
                        <div class="quantity-selector">
                            <button class="quantity-btn minus"><i class="fas fa-minus"></i></button>
                            <input type="text" class="quantity-input" value="1" readonly>
                            <button class="quantity-btn plus"><i class="fas fa-plus"></i></button>
                        </div>
                        <button class="cta-button add-to-cart-quick">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="product-action wishlist">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                    ${featuresHTML}
                </div>
            `;
            
            // Add event listeners for quantity buttons
            content.querySelector('.quantity-btn.minus').addEventListener('click', function() {
                const input = content.querySelector('.quantity-input');
                let value = parseInt(input.value);
                if (value > 1) {
                    input.value = value - 1;
                }
            });
            
            content.querySelector('.quantity-btn.plus').addEventListener('click', function() {
                const input = content.querySelector('.quantity-input');
                let value = parseInt(input.value);
                input.value = value + 1;
            });
            
            // Add to cart from quick view
            content.querySelector('.add-to-cart-quick').addEventListener('click', function() {
                const quantity = parseInt(content.querySelector('.quantity-input').value);
                addToCart(productId, quantity);
                closeModal();
            });
            
            // Show modal
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Close Modal
        function closeModal() {
            const modal = document.getElementById('quick-view-modal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Generate Star Rating
        function generateStars(rating) {
            let stars = '';
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 !== 0;
            
            for (let i = 0; i < fullStars; i++) {
                stars += '<i class="fas fa-star"></i>';
            }
            
            if (hasHalfStar) {
                stars += '<i class="fas fa-star-half-alt"></i>';
            }
            
            const emptyStars = 5 - Math.ceil(rating);
            for (let i = 0; i < emptyStars; i++) {
                stars += '<i class="far fa-star"></i>';
            }
            
            return stars;
        }

        // Newsletter Subscription
        function subscribeNewsletter(email) {
            // In a real implementation, this would be an AJAX call to your PHP backend
            console.log(`Subscribing email: ${email}`);
            
            // Show success message
            showNotification('Thank you for subscribing to our newsletter!');
            document.getElementById('newsletter-form').reset();
        }

        // Search Functionality
        function performSearch(searchTerm) {
            // In a real implementation, this would be an AJAX call to your PHP backend
            console.log(`Searching for: ${searchTerm}`);
            
            // For demo purposes, we'll filter existing products
            const filteredProducts = allProducts.filter(p => 
                p.name.toLowerCase().includes(searchTerm.toLowerCase()) || 
                p.category.toLowerCase().includes(searchTerm.toLowerCase())
            );
            
            displayProducts(filteredProducts);
            showNotification(`Found ${filteredProducts.length} products for "${searchTerm}"`);
        }

        // Add to Cart
        function addToCart(productId, quantity = 1) {
            // In a real implementation, this would be an AJAX call to your PHP backend
            console.log(`Adding product ${productId} to cart with quantity ${quantity}`);
            
            // Update cart badge
            const cartBadge = document.querySelector('.header-action .badge');
            let count = parseInt(cartBadge.textContent);
            cartBadge.textContent = count + quantity;
            
            // Show confirmation
            showNotification('Product added to cart!');
        }

        // Add to Wishlist
        function addToWishlist(productId) {
            // In a real implementation, this would be an AJAX call to your PHP backend
            console.log(`Adding product ${productId} to wishlist`);
            
            // Update wishlist badge
            const wishlistBadge = document.querySelectorAll('.header-action .badge')[0];
            let count = parseInt(wishlistBadge.textContent);
            wishlistBadge.textContent = count + 1;
            
            // Show confirmation
            showNotification('Product added to wishlist!');
        }

        // Show Notification
        function showNotification(message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background: var(--success);
                color: white;
                padding: 15px 20px;
                border-radius: 5px;
                box-shadow: var(--shadow-lg);
                z-index: 10000;
                transition: all 0.3s;
                transform: translateX(100%);
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    </script>
</body>
</html>