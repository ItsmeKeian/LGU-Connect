

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Inbox - LGU-Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f6fa;
            display: flex;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 220px;
            background: #2c2c2c;
            color: #fff;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            background: #1a1a1a;
            border-bottom: 1px solid #404040;
        }

        .sidebar-header h2 {
            font-size: 18px;
            color: #fff;
        }

        .sidebar-header p {
            font-size: 11px;
            color: #888;
            margin-top: 5px;
            text-transform: uppercase;
        }

        .user-badge {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: rgba(220, 53, 69, 0.1);
            border-left: 3px solid #dc3545;
            margin: 10px 0;
        }

        .user-badge i {
            font-size: 14px;
            color: #dc3545;
            margin-right: 10px;
        }

        .user-badge span {
            font-size: 13px;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-section {
            margin-bottom: 25px;
        }

        .menu-section-title {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            padding: 0 20px;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        .menu-item.active {
            background: #dc3545;
            color: #fff;
        }

        .menu-item i {
            width: 20px;
            margin-right: 12px;
            font-size: 14px;
        }

        .menu-item span {
            font-size: 14px;
        }

        .menu-item .badge {
            margin-left: auto;
            background: #dc3545;
            color: #fff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
        }

        /* Main Content */
        .main-content {
            margin-left: 220px;
            flex: 1;
            padding: 0;
        }

        /* Top Header */
        .top-header {
            background: #fff;
            padding: 15px 30px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title h1 {
            font-size: 20px;
            color: #2c2c2c;
            margin-bottom: 5px;
        }

        .breadcrumb {
            font-size: 13px;
            color: #666;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 8px 35px 8px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            width: 300px;
            font-size: 13px;
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #dc3545;
            color: #fff;
        }

        .btn-primary:hover {
            background: #c82333;
        }

        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }

        /* Filter Bar */
        .filter-bar {
            background: #fff;
            padding: 20px 30px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-group label {
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }

        .filter-group select,
        .filter-group input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 13px;
            min-width: 150px;
        }

        .filter-chips {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }

        .chip {
            padding: 6px 12px;
            background: #f0f0f0;
            border-radius: 15px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .chip:hover {
            background: #e0e0e0;
        }

        .chip.active {
            background: #dc3545;
            color: #fff;
        }

        /* Feedback Content Area */
        .content-area {
            padding: 30px;
        }

        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .stat-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #2c2c2c;
        }

        /* Feedback List */
        .feedback-list {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .feedback-item {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .feedback-item:hover {
            background: #f9f9f9;
        }

        .feedback-item:last-child {
            border-bottom: none;
        }

        .feedback-item.unread {
            background: #fff8f0;
            border-left: 4px solid #ffc107;
        }

        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .feedback-info {
            flex: 1;
        }

        .feedback-id {
            font-size: 12px;
            color: #999;
            font-weight: 600;
        }

        .feedback-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 8px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #666;
        }

        .meta-item i {
            font-size: 12px;
            color: #999;
        }

        .rating-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .rating-badge.excellent {
            background: #d4edda;
            color: #155724;
        }

        .rating-badge.good {
            background: #d1ecf1;
            color: #0c5460;
        }

        .rating-badge.fair {
            background: #fff3cd;
            color: #856404;
        }

        .rating-badge.poor {
            background: #f8d7da;
            color: #721c24;
        }

        .rating-badge .stars {
            color: #ffc107;
        }

        .feedback-preview {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .feedback-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .tag {
            padding: 4px 10px;
            background: #f0f0f0;
            border-radius: 12px;
            font-size: 11px;
            color: #666;
        }

        .tag.sqd-low {
            background: #f8d7da;
            color: #721c24;
        }

        .tag.sqd-high {
            background: #d4edda;
            color: #155724;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 20px;
        }

        .pagination button {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
        }

        .pagination button:hover {
            background: #f0f0f0;
        }

        .pagination button.active {
            background: #dc3545;
            color: #fff;
            border-color: #dc3545;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }

        .modal.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            border-radius: 8px;
            overflow: hidden;
            animation: slideUp 0.3s;
        }

        .modal-header {
            padding: 20px 30px;
            background: #dc3545;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 18px;
        }

        .close-modal {
            background: none;
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
        }

        .modal-body {
            padding: 30px;
            max-height: calc(90vh - 140px);
            overflow-y: auto;
        }

        .detail-section {
            margin-bottom: 25px;
        }

        .detail-section h3 {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 8px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-item label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
        }

        .detail-item .value {
            font-size: 14px;
            color: #2c2c2c;
            font-weight: 500;
        }

        .sqd-scores {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .sqd-item {
            text-align: center;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .sqd-item .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
        }

        .sqd-item .score {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Loading Spinner */
        .loading {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #dc3545;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>LGU-Connect</h2>
            <p>San Julian, E. Samar</p>
        </div>

        <div class="user-badge">
            <i class="fas fa-user-circle"></i>
            <span>Department Administrator</span>
        </div>

        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">My Department</div>
                <a href="dept_dashboard.php" class="menu-item">
                    <i class="fas fa-chart-line"></i>
                    <span>My Dashboard</span>
                </a>
                <a href="feedback_inbox.php" class="menu-item active">
                    <i class="fas fa-inbox"></i>
                    <span>Feedback Inbox</span>
                    <span class="badge" id="unreadCount">28</span>
                </a>
                <a href="my_qr_code.php" class="menu-item">
                    <i class="fas fa-qrcode"></i>
                    <span>My QR Code</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Reports</div>
                <a href="generate_csmr.php" class="menu-item">
                    <i class="fas fa-file-alt"></i>
                    <span>Generate CSMR</span>
                </a>
                <a href="my_analytics.php" class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>My Analytics</span>
                </a>
                <a href="export_data.php" class="menu-item">
                    <i class="fas fa-download"></i>
                    <span>Export Data</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Account</div>
                <a href="my_profile.php" class="menu-item">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
                <a href="settings.php" class="menu-item">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </div>
        </div>

        <a href="logout.php" class="menu-item" style="position: absolute; bottom: 20px; width: calc(100% - 40px); margin: 0 20px;">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sign Out</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <div class="page-title">
                <h1>Feedback Inbox</h1>
                <div class="breadcrumb">Department Dashboard / Feedback Inbox</div>
            </div>
            <div class="header-actions">
                <div class="search-box">
                    <input type="text" id="globalSearch" placeholder="Search feedback...">
                    <i class="fas fa-search"></i>
                </div>
                <button class="btn btn-secondary" onclick="location.reload()">
                    <i class="fas fa-sync"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-group">
                <label>Status:</label>
                <select id="filterStatus">
                    <option value="">All</option>
                    <option value="unread">Unread</option>
                    <option value="read">Read</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Rating:</label>
                <select id="filterRating">
                    <option value="">All Ratings</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Date Range:</label>
                <input type="date" id="filterDateFrom">
                <span>to</span>
                <input type="date" id="filterDateTo">
            </div>
            <div class="filter-chips">
                <div class="chip active" data-filter="all">
                    <i class="fas fa-inbox"></i> All
                </div>
                <div class="chip" data-filter="today">
                    <i class="fas fa-calendar-day"></i> Today
                </div>
                <div class="chip" data-filter="week">
                    <i class="fas fa-calendar-week"></i> This Week
                </div>
                <div class="chip" data-filter="month">
                    <i class="fas fa-calendar-alt"></i> This Month
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Stats Bar -->
            <div class="stats-bar">
                <div class="stat-card">
                    <h3>Total Feedback</h3>
                    <div class="value" id="totalFeedback">0</div>
                </div>
                <div class="stat-card">
                    <h3>Unread</h3>
                    <div class="value" id="unreadFeedback">0</div>
                </div>
                <div class="stat-card">
                    <h3>Average Rating</h3>
                    <div class="value" id="avgRating">0.0</div>
                </div>
                <div class="stat-card">
                    <h3>This Month</h3>
                    <div class="value" id="monthlyFeedback">0</div>
                </div>
            </div>

            <!-- Feedback List -->
            <div class="feedback-list" id="feedbackList">
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Loading feedback...</p>
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination" id="pagination" style="display: none;">
                <!-- Pagination buttons will be inserted here -->
            </div>
        </div>
    </div>

    <!-- Feedback Detail Modal -->
    <div class="modal" id="feedbackModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Feedback Details</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Modal content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentPage = 1;
        let totalPages = 1;
        const itemsPerPage = 10;
        let currentFilters = {
            status: '',
            rating: '',
            dateFrom: '',
            dateTo: '',
            search: '',
            timeFilter: 'all'
        };

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadFeedback();
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Filter listeners
            document.getElementById('filterStatus').addEventListener('change', function() {
                currentFilters.status = this.value;
                currentPage = 1;
                loadFeedback();
            });

            document.getElementById('filterRating').addEventListener('change', function() {
                currentFilters.rating = this.value;
                currentPage = 1;
                loadFeedback();
            });

            document.getElementById('filterDateFrom').addEventListener('change', function() {
                currentFilters.dateFrom = this.value;
                currentPage = 1;
                loadFeedback();
            });

            document.getElementById('filterDateTo').addEventListener('change', function() {
                currentFilters.dateTo = this.value;
                currentPage = 1;
                loadFeedback();
            });

            // Search listener with debounce
            let searchTimeout;
            document.getElementById('globalSearch').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentFilters.search = this.value;
                    currentPage = 1;
                    loadFeedback();
                }, 500);
            });

            // Time filter chips
            document.querySelectorAll('.chip').forEach(chip => {
                chip.addEventListener('click', function() {
                    document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    currentFilters.timeFilter = this.dataset.filter;
                    currentPage = 1;
                    loadFeedback();
                });
            });
        }

        // Load feedback data
        function loadFeedback() {
            const feedbackList = document.getElementById('feedbackList');
            feedbackList.innerHTML = '<div class="loading"><div class="spinner"></div><p>Loading feedback...</p></div>';

            const params = new URLSearchParams({
                page: currentPage,
                limit: itemsPerPage,
                ...currentFilters
            });

            fetch(`api/get_feedback.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayFeedback(data.feedback);
                        updateStats(data.stats);
                        setupPagination(data.totalPages, data.totalCount);
                    } else {
                        feedbackList.innerHTML = '<div class="loading"><p>Error loading feedback</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    feedbackList.innerHTML = '<div class="loading"><p>Error loading feedback</p></div>';
                });
        }

        // Display feedback items
        function displayFeedback(feedback) {
            const feedbackList = document.getElementById('feedbackList');
            
            if (feedback.length === 0) {
                feedbackList.innerHTML = '<div class="loading"><p>No feedback found</p></div>';
                return;
            }

            feedbackList.innerHTML = feedback.map(item => `
                <div class="feedback-item ${item.is_read === '0' ? 'unread' : ''}" onclick="viewFeedback(${item.id})">
                    <div class="feedback-header">
                        <div class="feedback-info">
                            <div class="feedback-id">Feedback #${item.id}</div>
                            <div class="feedback-meta">
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    ${formatDate(item.created_at)}
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-user"></i>
                                    ${item.respondent_type}
                                </div>
                                ${item.age ? `<div class="meta-item"><i class="fas fa-birthday-cake"></i>${item.age} years old</div>` : ''}
                            </div>
                        </div>
                        <div class="rating-badge ${getRatingClass(item.average_rating)}">
                            <span class="stars">${getStars(item.average_rating)}</span>
                            ${parseFloat(item.average_rating).toFixed(1)}
                        </div>
                    </div>
                    <div class="feedback-preview">
                        ${item.comments ? item.comments.substring(0, 150) + (item.comments.length > 150 ? '...' : '') : 'No comments provided'}
                    </div>
                    <div class="feedback-tags">
                        ${getSQDTags(item)}
                    </div>
                </div>
            `).join('');
        }

        // Update statistics
        function updateStats(stats) {
            document.getElementById('totalFeedback').textContent = stats.total || 0;
            document.getElementById('unreadFeedback').textContent = stats.unread || 0;
            document.getElementById('avgRating').textContent = parseFloat(stats.avgRating || 0).toFixed(1);
            document.getElementById('monthlyFeedback').textContent = stats.monthly || 0;
            document.getElementById('unreadCount').textContent = stats.unread || 0;
        }

        // Setup pagination
        function setupPagination(pages, totalCount) {
            totalPages = pages;
            const pagination = document.getElementById('pagination');
            
            if (totalPages <= 1) {
                pagination.style.display = 'none';
                return;
            }

            pagination.style.display = 'flex';
            
            let paginationHTML = '';
            
            // Previous button
            paginationHTML += `<button ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                <i class="fas fa-chevron-left"></i>
            </button>`;
            
            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    paginationHTML += `<button class="${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    paginationHTML += '<button disabled>...</button>';
                }
            }
            
            // Next button
            paginationHTML += `<button ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                <i class="fas fa-chevron-right"></i>
            </button>`;
            
            pagination.innerHTML = paginationHTML;
        }

        // Change page
        function changePage(page) {
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            loadFeedback();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // View feedback details
        function viewFeedback(id) {
            fetch(`api/get_feedback_detail.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayFeedbackModal(data.feedback);
                        markAsRead(id);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Display feedback modal
        function displayFeedbackModal(feedback) {
            const modalBody = document.getElementById('modalBody');
            
            modalBody.innerHTML = `
                <div class="detail-section">
                    <h3>Respondent Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Respondent Type</label>
                            <div class="value">${feedback.respondent_type || 'N/A'}</div>
                        </div>
                        <div class="detail-item">
                            <label>Age</label>
                            <div class="value">${feedback.age || 'N/A'}</div>
                        </div>
                        <div class="detail-item">
                            <label>Gender</label>
                            <div class="value">${feedback.gender || 'N/A'}</div>
                        </div>
                        <div class="detail-item">
                            <label>Date Submitted</label>
                            <div class="value">${formatDateTime(feedback.created_at)}</div>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h3>Service Quality Dimensions (SQD)</h3>
                    <div class="sqd-scores">
                        ${generateSQDScores(feedback)}
                    </div>
                </div>

                <div class="detail-section">
                    <h3>Overall Rating</h3>
                    <div style="text-align: center; padding: 20px;">
                        <div class="rating-badge ${getRatingClass(feedback.average_rating)}" style="font-size: 18px; padding: 12px 24px;">
                            <span class="stars">${getStars(feedback.average_rating)}</span>
                            ${parseFloat(feedback.average_rating).toFixed(1)} / 5.0
                        </div>
                    </div>
                </div>

                ${feedback.comments ? `
                <div class="detail-section">
                    <h3>Comments / Suggestions</h3>
                    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; line-height: 1.6;">
                        ${feedback.comments}
                    </div>
                </div>
                ` : ''}
            `;

            document.getElementById('feedbackModal').classList.add('active');
        }

        // Generate SQD scores HTML
        function generateSQDScores(feedback) {
            const dimensions = [
                { key: 'sqd0_responsiveness', label: 'Responsiveness' },
                { key: 'sqd1_reliability', label: 'Reliability' },
                { key: 'sqd2_access', label: 'Access & Facilities' },
                { key: 'sqd3_communication', label: 'Communication' },
                { key: 'sqd4_costs', label: 'Costs' },
                { key: 'sqd5_integrity', label: 'Integrity' },
                { key: 'sqd6_assurance', label: 'Assurance' },
                { key: 'sqd7_outcome', label: 'Outcome' }
            ];

            return dimensions.map(dim => `
                <div class="sqd-item">
                    <div class="label">${dim.label}</div>
                    <div class="score">${feedback[dim.key] || 'N/A'}</div>
                </div>
            `).join('');
        }

        // Mark feedback as read
        function markAsRead(id) {
            fetch('api/mark_as_read.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            })
            .then(() => loadFeedback())
            .catch(error => console.error('Error:', error));
        }

        // Close modal
        function closeModal() {
            document.getElementById('feedbackModal').classList.remove('active');
        }

        // Helper functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        function formatDateTime(dateString) {
            const date = new Date(dateString);
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return date.toLocaleDateString('en-US', options);
        }

        function getStars(rating) {
            const fullStars = Math.floor(rating);
            return '★'.repeat(fullStars) + '☆'.repeat(5 - fullStars);
        }

        function getRatingClass(rating) {
            rating = parseFloat(rating);
            if (rating >= 4.5) return 'excellent';
            if (rating >= 3.5) return 'good';
            if (rating >= 2.5) return 'fair';
            return 'poor';
        }

        function getSQDTags(item) {
            let tags = [];
            
            // Check for low SQD scores
            const dimensions = ['sqd0_responsiveness', 'sqd1_reliability', 'sqd2_access', 'sqd3_communication', 'sqd4_costs', 'sqd5_integrity', 'sqd6_assurance', 'sqd7_outcome'];
            const lowScores = dimensions.filter(dim => parseFloat(item[dim]) < 3);
            
            if (lowScores.length > 0) {
                tags.push('<span class="tag sqd-low">Low SQD Score</span>');
            }
            
            if (item.comments) {
                tags.push('<span class="tag">Has Comments</span>');
            }
            
            if (item.is_read === '0') {
                tags.push('<span class="tag">Unread</span>');
            }
            
            return tags.join('');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('feedbackModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>