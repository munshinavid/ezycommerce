<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EzyCommerce Logistics Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            width: 250px;
            background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            transition: all 0.3s;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header i {
            margin-bottom: 10px;
            display: block;
        }

        .sidebar-header h2 {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .sidebar-menu ul {
            list-style: none;
            padding: 20px 0;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover, .sidebar-menu li.active a {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 4px solid white;
        }

        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar-stats {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
            background-color: #f0ad4e;
        }

        .stat-icon.processing {
            background-color: #5bc0de;
        }

        .stat-icon.shipped {
            background-color: #0275d8;
        }

        .stat-icon.delivered {
            background-color: #5cb85c;
        }

        .stat-label {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
        }

        .stat-value {
            font-weight: 700;
            font-size: 1.1rem;
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

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 0.9rem;
            color: var(--gray);
            font-weight: 500;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .card-footer {
            font-size: 0.8rem;
            color: var(--gray);
        }

        /* Charts Section */
        .charts-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .chart-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        /* Tables Section */
        .tables-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
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

        .status-shipped {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-return-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-return-processing {
            background-color: #cce7ff;
            color: #004085;
        }

        .status-return-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-return-rejected {
            background-color: #f8d7da;
            color: #721c24;
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
            max-width: 500px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            overflow: hidden;
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

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .charts-container, .tables-container {
                grid-template-columns: 1fr;
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
            }
            
            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-value" id="sidebar-pending-count">0</div>
                </div>
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon processing">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="stat-label">Processing</div>
                    </div>
                    <div class="stat-value" id="sidebar-processing-count">0</div>
                </div>
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon shipped">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="stat-label">Shipped</div>
                    </div>
                    <div class="stat-value" id="sidebar-shipped-count">0</div>
                </div>
                <div class="stat-item">
                    <div class="stat-info">
                        <div class="stat-icon delivered">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-label">Delivered</div>
                    </div>
                    <div class="stat-value" id="sidebar-delivered-count">0</div>
                </div>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li class="active"><a href="<?php echo url('/logistics'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="<?php echo url('/logistics/shipping'); ?>"><i class="fas fa-list"></i> All Orders</a></li>
                    <li><a href="<?php echo url('/logistics/shipping'); ?>"><i class="fas fa-shipping-fast"></i> Shipping</a></li>
                    <li><a href="<?php echo url('/logistics/returns'); ?>"><i class="fas fa-undo"></i> Returns</a></li>
                    <li><a href="#" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-chart-bar"></i> Analytics</a></li>
                    <li><a href="#" aria-disabled="true" tabindex="-1" style="pointer-events:none;opacity:0.45;cursor:not-allowed;"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="<?php echo url('/logistics/logout'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Logistics Dashboard</h1>
                <div class="user-info">
                    <div class="user-avatar">LM</div>
                    <span>Logistics Manager</span>
                </div>
            </div>
            
            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Pending Orders</div>
                        <div class="card-icon" style="background: var(--warning);">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="card-value" id="pending-count">0</div>
                    <div class="card-footer">Awaiting processing</div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Processing</div>
                        <div class="card-icon" style="background: var(--info);">
                            <i class="fas fa-cog"></i>
                        </div>
                    </div>
                    <div class="card-value" id="processing-count">0</div>
                    <div class="card-footer">Being prepared for shipment</div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Shipped Today</div>
                        <div class="card-icon" style="background: var(--primary);">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                    </div>
                    <div class="card-value" id="shipped-count">0</div>
                    <div class="card-footer">Out for delivery</div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Return Requests</div>
                        <div class="card-icon" style="background: var(--danger);">
                            <i class="fas fa-undo"></i>
                        </div>
                    </div>
                    <div class="card-value" id="returns-count">0</div>
                    <div class="card-footer">Pending return approval</div>
                </div>
            </div>
            
            <!-- Charts Section -->
            <div class="charts-container">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Order Status Distribution</h3>
                        <select id="chart-period" class="form-control" style="width: auto;">
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="quarter">This Quarter</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="orders-chart"></canvas>
                    </div>
                </div>
                
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Shipping Performance</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="shipping-chart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Tables Section -->
            <div class="tables-container">
                <!-- Recent Orders Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Recent Orders</h2>
                        <button class="btn btn-primary" id="refresh-orders">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <table id="recent-orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recent-orders-body">
                            <!-- Data will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Return Requests Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Return Requests</h2>
                        <button class="btn btn-primary" id="refresh-returns">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <table id="return-requests-table">
                        <thead>
                            <tr>
                                <th>Return ID</th>
                                <th>Order ID</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="return-requests-body">
                            <!-- Data will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Shipping Management Table -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Shipping Management</h2>
                    <button class="btn btn-primary" id="refresh-shipping">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                <table id="shipping-management-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Tracking No.</th>
                            <th>Last Update</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="shipping-management-body">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Update Status Modal -->
    <div class="modal" id="status-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Order Status</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="status-form">
                    <div class="form-group">
                        <label for="modal-order-id">Order ID</label>
                        <input type="text" id="modal-order-id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="modal-order-status">Status</label>
                        <select id="modal-order-status" class="form-control">
                            <option value="Pending">Pending</option>
                            <option value="Processing">Processing</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group" id="tracking-group" style="display: none;">
                        <label for="modal-tracking-number">Tracking Number</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="modal-tracking-number" class="form-control">
                            <button type="button" class="btn btn-primary" id="generate-tracking">
                                <i class="fas fa-barcode"></i> Generate
                            </button>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-danger" id="cancel-update">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Return Action Modal -->
    <div class="modal" id="return-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Process Return Request</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="return-form">
                    <div class="form-group">
                        <label for="modal-return-id">Return ID</label>
                        <input type="text" id="modal-return-id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="modal-return-order-id">Order ID</label>
                        <input type="text" id="modal-return-order-id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="modal-return-reason">Reason</label>
                        <textarea id="modal-return-reason" class="form-control" rows="3" readonly></textarea>
                    </div>
                    <div class="form-group">
                        <label for="modal-return-status">Action</label>
                        <select id="modal-return-status" class="form-control">
                            <option value="Pending">Pending</option>
                            <option value="Processing">Mark as Processing</option>
                            <option value="Approved">Approve</option>
                            <option value="Rejected">Reject</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-danger" id="cancel-return">Cancel</button>
                        <button type="submit" class="btn btn-success">Process Return</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="<?php echo url('/Logistics/js/dashboard.js'); ?>"></script>
</body>
</html>