<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns Management - EzyCommerce Logistics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --pending: #ffc107;
            --processing: #17a2b8;
            --approved: #28a745;
            --rejected: #dc3545;
            --completed: #6f42c1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: #333;
            line-height: 1.6;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: white;
            color: var(--dark);
            transition: all 0.3s;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid var(--light-gray);
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .sidebar-header i {
            margin-bottom: 10px;
            display: block;
        }

        .sidebar-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .sidebar-stats {
            padding: 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .stat-icon.pending {
            background-color: var(--pending);
        }

        .stat-icon.processing {
            background-color: var(--processing);
        }

        .stat-icon.approved {
            background-color: var(--approved);
        }

        .stat-icon.rejected {
            background-color: var(--rejected);
        }

        .stat-icon.completed {
            background-color: var(--completed);
        }

        .stat-label {
            font-weight: 500;
        }

        .stat-value {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .sidebar-menu {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .sidebar-menu ul {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover, .sidebar-menu li.active a {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .menu-badge {
            background-color: var(--light-gray);
            color: var(--dark);
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 0.8rem;
            margin-left: auto;
        }

        .sidebar-menu li.active .menu-badge {
            background-color: var(--primary);
            color: white;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
        }

        .header h1 {
            color: var(--dark);
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Tabs Navigation */
        .tabs {
            display: flex;
            background: white;
            border-radius: 10px;
            padding: 5px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .tab {
            flex: 1;
            padding: 12px 20px;
            text-align: center;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .tab.active {
            background-color: var(--primary);
            color: white;
        }

        .tab:hover:not(.active) {
            background-color: var(--light-gray);
        }

        .tab-badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 0.8rem;
            margin-left: 5px;
        }

        .tab:not(.active) .tab-badge {
            background-color: var(--light-gray);
            color: var(--dark);
        }

        /* Filters Section */
        .filters-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .filters-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .toggle-filters {
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .filters-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .filter-group {
            margin-bottom: 10px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--dark);
            font-size: 0.9rem;
        }

        .filter-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .filter-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            grid-column: 1 / -1;
        }

        /* Table Container */
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .table-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
        }

        .table-actions {
            display: flex;
            gap: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }

        th {
            background-color: var(--light);
            font-weight: 600;
            color: var(--dark);
            position: sticky;
            top: 0;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background-color: #cce7ff;
            color: #004085;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-completed {
            background-color: #e2e3ff;
            color: #383d8c;
        }

        /* Buttons */
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary);
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #3aa8d4;
        }

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn-warning:hover {
            background-color: #d41c6c;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--light-gray);
            color: var(--dark);
        }

        .btn-outline:hover {
            background-color: var(--light-gray);
        }

        /* Bulk Actions */
        .bulk-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background-color: var(--light);
            border-bottom: 1px solid var(--light-gray);
        }

        .bulk-checkbox {
            margin-right: 5px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-top: 1px solid var(--light-gray);
        }

        .pagination-info {
            font-size: 0.9rem;
            color: var(--gray);
        }

        .pagination-controls {
            display: flex;
            gap: 5px;
        }

        .page-btn {
            padding: 5px 10px;
            border: 1px solid var(--light-gray);
            background: white;
            border-radius: 3px;
            cursor: pointer;
        }

        .page-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-btn:hover:not(.active) {
            background-color: var(--light-gray);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 10px;
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .modal-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray);
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        /* Return Details */
        .return-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-section {
            margin-bottom: 15px;
        }

        .detail-section h4 {
            margin-bottom: 10px;
            color: var(--dark);
            font-size: 1rem;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: var(--gray);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .filters-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
            }
            
            .sidebar-menu ul {
                display: flex;
                overflow-x: auto;
                padding: 10px;
            }
            
            .sidebar-menu li {
                margin-bottom: 0;
                margin-right: 10px;
                flex-shrink: 0;
            }
            
            .sidebar-menu a {
                padding: 10px 15px;
                border-radius: 5px;
                border-left: none;
                border-bottom: 3px solid transparent;
            }
            
            .sidebar-menu a:hover, .sidebar-menu li.active a {
                border-left: none;
                border-bottom-color: var(--primary);
            }
            
            .tabs {
                flex-direction: column;
            }
            
            .filters-content {
                grid-template-columns: 1fr;
            }
            
            .return-details {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            table {
                min-width: 900px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-truck fa-2x"></i>
                <h2>EzyCommerce Logistics</h2>
            </div>
            
            <div class="sidebar-stats">
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-label">Pending Returns</div>
                    </div>
                    <div class="stat-value" id="sidebar-pending-count">18</div>
                </div>
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon processing">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="stat-label">Processing</div>
                    </div>
                    <div class="stat-value" id="sidebar-processing-count">12</div>
                </div>
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon approved">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-label">Approved</div>
                    </div>
                    <div class="stat-value" id="sidebar-approved-count">25</div>
                </div>
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon rejected">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-label">Rejected</div>
                    </div>
                    <div class="stat-value" id="sidebar-rejected-count">5</div>
                </div>
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon completed">
                            <i class="fas fa-flag-checkered"></i>
                        </div>
                        <div class="stat-label">Completed</div>
                    </div>
                    <div class="stat-value" id="sidebar-completed-count">32</div>
                </div>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li><a href="<?php echo url('/logistics'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="<?php echo url('/logistics/shipping'); ?>"><i class="fas fa-list"></i> All Orders</a></li>
                    <li class="active"><a href="<?php echo url('/logistics/returns'); ?>#all" data-status="refresh-all"><i class="fas fa-undo"></i> Returns <span class="menu-badge" id="menu-all-count">92</span></a></li>
                    <li><a href="<?php echo url('/logistics/returns'); ?>#pending" data-status="pending"><i class="fas fa-clock" style="color: var(--pending);"></i> Pending <span class="menu-badge" id="menu-pending-count">18</span></a></li>
                    <li><a href="<?php echo url('/logistics/returns'); ?>#processing" data-status="processing"><i class="fas fa-cog" style="color: var(--processing);"></i> Processing <span class="menu-badge" id="menu-processing-count">12</span></a></li>
                    <li><a href="<?php echo url('/logistics/returns'); ?>#approved" data-status="approved"><i class="fas fa-check-circle" style="color: var(--approved);"></i> Approved <span class="menu-badge" id="menu-approved-count">25</span></a></li>
                    <li><a href="<?php echo url('/logistics/returns'); ?>#rejected" data-status="rejected"><i class="fas fa-times-circle" style="color: var(--rejected);"></i> Rejected <span class="menu-badge" id="menu-rejected-count">5</span></a></li>
                    <li><a href="<?php echo url('/logistics/returns'); ?>#completed" data-status="completed"><i class="fas fa-flag-checkered" style="color: var(--completed);"></i> Completed <span class="menu-badge" id="menu-completed-count">32</span></a></li>
                    <li><a href="#" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-chart-bar"></i> Analytics</a></li>
                    <li><a href="#" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="<?php echo url('/logistics/logout'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Returns Management</h1>
                <div class="user-info">
                    <div class="user-avatar">LM</div>
                    <span>Logistics Manager</span>
                </div>
            </div>
            
            <!-- Tabs Navigation -->
            <div class="tabs">
                <div class="tab active" data-tab="all">
                    All Returns <span class="tab-badge" id="all-count">92</span>
                </div>
                <div class="tab" data-tab="pending">
                    Pending <span class="tab-badge" id="pending-count">18</span>
                </div>
                <div class="tab" data-tab="processing">
                    Processing <span class="tab-badge" id="processing-count">12</span>
                </div>
                <div class="tab" data-tab="approved">
                    Approved <span class="tab-badge" id="approved-count">25</span>
                </div>
                <div class="tab" data-tab="rejected">
                    Rejected <span class="tab-badge" id="rejected-count">5</span>
                </div>
                <div class="tab" data-tab="completed">
                    Completed <span class="tab-badge" id="completed-count">32</span>
                </div>
            </div>
            
            <!-- Filters Section -->
            <div class="filters-container">
                <div class="filters-header">
                    <h3>Filters</h3>
                    <button class="toggle-filters" id="toggle-filters">
                        <i class="fas fa-sliders-h"></i> Toggle Filters
                    </button>
                </div>
                <div class="filters-content" id="filters-content">
                    <div class="filter-group">
                        <label for="date-range">Date Range</label>
                        <select id="date-range" class="filter-control">
                            <option value="all">All Dates</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="customer-name">Customer Name</label>
                        <input type="text" id="customer-name" class="filter-control" placeholder="Search customer...">
                    </div>
                    <div class="filter-group">
                        <label for="return-id">Return ID</label>
                        <input type="text" id="return-id" class="filter-control" placeholder="Search return ID...">
                    </div>
                    <div class="filter-group">
                        <label for="order-id">Order ID</label>
                        <input type="text" id="order-id" class="filter-control" placeholder="Search order ID...">
                    </div>
                    <div class="filter-group">
                        <label for="return-reason">Return Reason</label>
                        <select id="return-reason" class="filter-control">
                            <option value="all">All Reasons</option>
                            <option value="defective">Defective Product</option>
                            <option value="wrong-item">Wrong Item</option>
                            <option value="damaged">Damaged in Shipping</option>
                            <option value="not-as-described">Not as Described</option>
                            <option value="changed-mind">Changed Mind</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="refund-type">Refund Type</label>
                        <select id="refund-type" class="filter-control">
                            <option value="all">All Types</option>
                            <option value="refund">Refund</option>
                            <option value="exchange">Exchange</option>
                            <option value="store-credit">Store Credit</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button class="btn btn-primary" id="apply-filters">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-outline" id="reset-filters">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Bulk Actions -->
            <div class="bulk-actions" id="bulk-actions" style="display: none;">
                <input type="checkbox" id="select-all" class="bulk-checkbox">
                <span id="selected-count">0 returns selected</span>
                <select id="bulk-action" class="filter-control" style="width: auto;">
                    <option value="">Bulk Actions</option>
                    <option value="process">Mark as Processing</option>
                    <option value="approve">Approve Returns</option>
                    <option value="reject">Reject Returns</option>
                    <option value="complete">Mark as Completed</option>
                </select>
                <button class="btn btn-primary btn-sm" id="apply-bulk-action">Apply</button>
            </div>
            
            <!-- Returns Table -->
            <div class="table-container">
                <div class="table-header">
                    <h2 id="table-title">All Returns</h2>
                    <div class="table-actions">
                        <button class="btn btn-outline" id="export-returns">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button class="btn btn-primary" id="refresh-returns">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th width="30"><input type="checkbox" id="select-all-returns"></th>
                            <th>Return ID</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Refund Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="returns-body">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
                <div class="pagination">
                    <div class="pagination-info" id="pagination-info">Showing 1-10 of 92 returns</div>
                    <div class="pagination-controls">
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn">3</button>
                        <button class="page-btn">4</button>
                        <button class="page-btn">5</button>
                        <button class="page-btn">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Return Details Modal -->
    <div class="modal" id="return-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Return Details - <span id="modal-return-id"></span></h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="return-details">
                    <div>
                        <div class="detail-section">
                            <h4>Customer Information</h4>
                            <div class="detail-item">
                                <span class="detail-label">Name:</span>
                                <span id="customer-name-detail"></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span id="customer-email-detail"></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone:</span>
                                <span id="customer-phone-detail"></span>
                            </div>
                        </div>
                        <div class="detail-section">
                            <h4>Return Information</h4>
                            <div class="detail-item">
                                <span class="detail-label">Date Requested:</span>
                                <span id="return-date-detail"></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span id="return-status-detail"></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Refund Type:</span>
                                <span id="refund-type-detail"></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="detail-section">
                            <h4>Order Information</h4>
                            <div class="detail-item">
                                <span class="detail-label">Order ID:</span>
                                <span id="order-id-detail"></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Order Date:</span>
                                <span id="order-date-detail"></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Order Amount:</span>
                                <span id="order-amount-detail"></span>
                            </div>
                        </div>
                        <div class="detail-section">
                            <h4>Return Address</h4>
                            <div id="return-address-detail"></div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4>Return Reason & Notes</h4>
                    <div class="form-control" style="min-height: 80px; background-color: var(--light);" id="return-reason-detail"></div>
                </div>
                
                <div class="detail-section">
                    <h4>Return Items</h4>
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody id="return-items-detail">
                        </tbody>
                    </table>
                </div>
                
                <div class="form-actions">
                    <button class="btn btn-danger" id="close-return-modal">Close</button>
                    <button class="btn btn-primary" id="process-return">Process Return</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Process Return Modal -->
    <div class="modal" id="process-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Process Return - <span id="process-return-id"></span></h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="process-form">
                    <div class="form-group">
                        <label for="process-return-status">Action</label>
                        <select id="process-return-status" class="form-control">
                            <option value="Pending">Pending</option>
                            <option value="Processing">Mark as Processing</option>
                            <option value="Approved">Approve Returns</option>
                            <option value="Rejected">Reject Returns</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="refund-amount-group">
                        <label for="refund-amount">Refund Amount ($)</label>
                        <input type="number" id="refund-amount" class="form-control" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group" id="rejection-reason-group" style="display: none;">
                        <label for="rejection-reason">Rejection Reason</label>
                        <textarea id="rejection-reason" class="form-control" rows="3" placeholder="Explain why this return is being rejected..."></textarea>
                    </div>
                    
                    <div class="form-group" id="internal-notes-group">
                        <label for="internal-notes">Internal Notes</label>
                        <textarea id="internal-notes" class="form-control" rows="3" placeholder="Add any internal notes about this return..."></textarea>
                    </div>
                    
                    <div class="form-group" id="customer-message-group">
                        <label for="customer-message">Customer Message</label>
                        <textarea id="customer-message" class="form-control" rows="3" placeholder="Message to send to the customer..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-danger" id="cancel-process">Cancel</button>
                        <button type="submit" class="btn btn-success">Process Return</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="<?php echo url('/Logistics/js/return.js'); ?>"></script>
</body>
</html>