<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php?controller=dashboard&action=index">
            Vinimaya
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_GET['controller'] ?? '') == 'dashboard' ? 'active' : ''; ?>" 
                       href="index.php?controller=dashboard&action=index">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_GET['controller'] ?? '') == 'expense' && ($_GET['action'] ?? '') == 'create' ? 'active' : ''; ?>" 
                       href="index.php?controller=expense&action=create">
                        Add Expense
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_GET['controller'] ?? '') == 'expense' && ($_GET['action'] ?? '') == 'list' ? 'active' : ''; ?>" 
                       href="index.php?controller=expense&action=list">
                        View Expenses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_GET['controller'] ?? '') == 'reports' ? 'active' : ''; ?>" 
                       href="index.php?controller=reports&action=index">
                        Reports
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="btn btn-outline-light btn-sm" href="index.php?controller=auth&action=logout">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>