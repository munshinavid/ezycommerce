<?php echo "Hello World"; ?>
<!DOCTYPE html>
<!-- ...existing code... -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - E-Commerce Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/user_management.css">
    <link rel="stylesheet" href="../css/sidebar.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include '../layout/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search...">
            </div>
            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=Super+Admin&background=random" alt="Admin">
                <div>
                    <h4>Super Admin</h4>
                </div>
            </div>
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Users Management</h1>
            <button class="btn btn-primary" id="addUserBtn">
                <i class="fas fa-plus"></i> Add New User
            </button>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div class="filter-group">
                <label for="search">Search Users</label>
                <input type="text" id="search" placeholder="Search by username or email">
            </div>
            <div class="filter-group">
                <label for="roleFilter">Filter by Role</label>
                <select id="roleFilter">
                    <option value="all">All Roles</option>
                    <option value="customer">Customer</option>
                    <option value="vendor">Vendor</option>
                    <option value="admin">Admin</option>
                    <option value="logistics">Logistics</option>
                </select>
            </div>
            <button class="btn btn-primary" style="align-self: flex-end;">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
        </div>

        <!-- Users Table -->
        <div class="table-container">
            <div class="table-header">
                <h3>All Users</h3>
                <div>
                    <span>Showing 1-10 of 48 users</span>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#1001</td>
                        <td>john_doe</td>
                        <td>john.doe@example.com</td>
                        <td><span class="role-badge role-customer">Customer</span></td>
                        <td>Jun 12, 2023</td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1002</td>
                        <td>emma_williams</td>
                        <td>emma.w@example.com</td>
                        <td><span class="role-badge role-vendor">Vendor</span></td>
                        <td>May 28, 2023</td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1003</td>
                        <td>admin_michael</td>
                        <td>michael@example.com</td>
                        <td><span class="role-badge role-admin">Admin</span></td>
                        <td>Apr 15, 2023</td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1004</td>
                        <td>logistics_sarah</td>
                        <td>sarah.l@example.com</td>
                        <td><span class="role-badge role-logistics">Logistics</span></td>
                        <td>Jun 5, 2023</td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1005</td>
                        <td>david_miller</td>
                        <td>david.m@example.com</td>
                        <td><span class="role-badge role-customer">Customer</span></td>
                        <td>Jun 18, 2023</td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1006</td>
                        <td>tech_gadgets</td>
                        <td>contact@techgadgets.com</td>
                        <td><span class="role-badge role-vendor">Vendor</span></td>
                        <td>Mar 22, 2023</td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1007</td>
                        <td>fashion_outlet</td>
                        <td>info@fashionoutlet.com</td>
                        <td><span class="role-badge role-vendor">Vendor</span></td>
                        <td>Feb 10, 2023</td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1008</td>
                        <td>jennifer_k</td>
                        <td>jennifer.k@example.com</td>
                        <td><span class="role-badge role-customer">Customer</span></td>
                        <td>Jun 22, 2023</td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1009</td>
                        <td>logistics_mark</td>
                        <td>mark.t@example.com</td>
                        <td><span class="role-badge role-logistics">Logistics</span></td>
                        <td>May 15, 2023</td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>#1010</td>
                        <td>admin_sophia</td>
                        <td>sophia.a@example.com</td>
                        <td><span class="role-badge role-admin">Admin</span></td>
                        <td>Jan 5, 2023</td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <button>&laquo;</button>
                <button class="active">1</button>
                <button>2</button>
                <button>3</button>
                <button>4</button>
                <button>5</button>
                <button>&raquo;</button>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal" id="addUserModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New User</h3>
                <button class="close-btn">&times;</button>
            </div>
            <form id="addUserForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" required>
                        <option value="">Select Role</option>
                        <option value="customer">Customer</option>
                        <option value="vendor">Vendor</option>
                        <option value="admin">Admin</option>
                        <option value="logistics">Logistics</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-danger">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        const addUserBtn = document.getElementById('addUserBtn');
        const addUserModal = document.getElementById('addUserModal');
        const closeBtn = document.querySelector('.close-btn');
        const cancelBtn = document.querySelector('.btn-danger');

        addUserBtn.addEventListener('click', () => {
            addUserModal.style.display = 'flex';
        });

        closeBtn.addEventListener('click', () => {
            addUserModal.style.display = 'none';
        });

        cancelBtn.addEventListener('click', () => {
            addUserModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === addUserModal) {
                addUserModal.style.display = 'none';
            }
        });

        // Form submission
        document.getElementById('addUserForm').addEventListener('submit', (e) => {
            e.preventDefault();
            // In a real application, you would handle form submission to the server here
            alert('User added successfully!');
            addUserModal.style.display = 'none';
        });

        // Filter functionality
        document.getElementById('search').addEventListener('input', (e) => {
            const searchValue = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const username = row.children[1].textContent.toLowerCase();
                const email = row.children[2].textContent.toLowerCase();
                
                if (username.includes(searchValue) || email.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.getElementById('roleFilter').addEventListener('change', (e) => {
            const roleValue = e.target.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const role = row.children[3].textContent.toLowerCase();
                
                if (roleValue === 'all' || role.includes(roleValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>