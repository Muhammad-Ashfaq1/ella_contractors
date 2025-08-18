<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 5px 7px #4075A1;
        margin-bottom: 20px;
    }

    .card-header-custom {
        background-color: #4075A1;
        color: white;
        padding: 15px 20px;
        border-bottom: none;
        border-top-left-radius: 0.3rem;
        border-top-right-radius: 0.3rem;
    }

    .card-header-custom h4 {
        margin-bottom: 0;
        font-weight: 600;
    }

    .card-body-custom {
        padding: 1.5rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(135deg, #4075A1 0%, #36648b 100%);
        color: white;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(64, 117, 161, 0.3);
        transition: transform 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card:hover::before {
        transform: scale(1.5);
    }

    .stat-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.8;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 1rem;
        opacity: 0.9;
        font-weight: 500;
    }

    .quick-actions {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .quick-actions h5 {
        color: #4075A1;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .action-btn {
        background: #4075A1;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .action-btn:hover {
        background: #36648b;
        color: white;
        transform: translateY(-2px);
        text-decoration: none;
    }

    .recent-activity {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .activity-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #4075A1;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }

    .activity-meta {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .chart-title {
        color: #4075A1;
        font-weight: 600;
        margin: 0;
    }

    .chart-filters {
        display: flex;
        gap: 10px;
    }

    .filter-btn {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #495057;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-btn.active {
        background: #4075A1;
        color: white;
        border-color: #4075A1;
    }

    .status-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .status-active { background-color: #28a745; }
    .status-pending { background-color: #ffc107; }
    .status-inactive { background-color: #dc3545; }
    .status-completed { background-color: #17a2b8; }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .progress-ring {
        width: 120px;
        height: 120px;
        margin: 0 auto 20px;
    }

    .progress-ring circle {
        fill: none;
        stroke-width: 8;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }

    .progress-ring .bg {
        stroke: #e9ecef;
    }

    .progress-ring .progress {
        stroke: #4075A1;
        stroke-dasharray: 283;
        stroke-dashoffset: 283;
        transition: stroke-dashoffset 0.5s ease;
    }
</style>

<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <h4 class="no-margin">
                        <i class="fa fa-tachometer-alt"></i> Ella Contractors Dashboard
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa fa-users"></i>
            </div>
            <div class="stat-number"><?= $total_contractors ?? 0 ?></div>
            <div class="stat-label">Total Contractors</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa fa-file-contract"></i>
            </div>
            <div class="stat-number"><?= $active_contracts ?? 0 ?></div>
            <div class="stat-label">Active Contracts</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa fa-tasks"></i>
            </div>
            <div class="stat-number"><?= $active_projects ?? 0 ?></div>
            <div class="stat-label">Active Projects</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa fa-dollar-sign"></i>
            </div>
            <div class="stat-number"><?= $pending_payments ?? 0 ?></div>
            <div class="stat-label">Pending Payments</div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="quick-actions">
                <h5><i class="fa fa-bolt"></i> Quick Actions</h5>
                <div class="action-buttons">
                    <a href="<?= admin_url('ella_contractors/contractors/add') ?>" class="action-btn">
                        <i class="fa fa-plus"></i> Add Contractor
                    </a>
                    <a href="<?= admin_url('ella_contractors/contracts/add') ?>" class="action-btn">
                        <i class="fa fa-file-contract"></i> New Contract
                    </a>
                    <a href="<?= admin_url('ella_contractors/projects/add') ?>" class="action-btn">
                        <i class="fa fa-tasks"></i> New Project
                    </a>
                    <a href="<?= admin_url('ella_contractors/payments/add') ?>" class="action-btn">
                        <i class="fa fa-dollar-sign"></i> Record Payment
                    </a>
                    <a href="<?= admin_url('ella_contractors/documents/upload') ?>" class="action-btn">
                        <i class="fa fa-upload"></i> Upload Document
                    </a>
                </div>
            </div>

            <!-- Status Overview -->
            <div class="card">
                <div class="card-header-custom">
                    <h4 class="mb-0"><i class="fa fa-chart-pie"></i> Status Overview</h4>
                </div>
                <div class="card-body-custom">
                    <div class="progress-ring">
                        <svg width="120" height="120">
                            <circle class="bg" cx="60" cy="60" r="45"></circle>
                            <circle class="progress" cx="60" cy="60" r="45" 
                                    style="stroke-dashoffset: <?= 283 - (($active_contractors ?? 0) / max(1, $total_contractors ?? 1)) * 283 ?>"></circle>
                        </svg>
                    </div>
                    <div class="text-center">
                        <h5><?= round(($active_contractors ?? 0) / max(1, $total_contractors ?? 1) * 100) ?>%</h5>
                        <p class="text-muted">Active Contractors</p>
                    </div>
                    
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><span class="status-indicator status-active"></span>Active</span>
                            <span class="badge badge-success"><?= $active_contractors ?? 0 ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><span class="status-indicator status-pending"></span>Pending</span>
                            <span class="badge badge-warning"><?= $pending_contractors ?? 0 ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><span class="status-indicator status-inactive"></span>Inactive</span>
                            <span class="badge badge-danger"><?= $inactive_contractors ?? 0 ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Charts -->
        <div class="col-md-8">
            <!-- Recent Activity -->
            <div class="recent-activity">
                <h5><i class="fa fa-clock"></i> Recent Activity</h5>
                <?php if (!empty($recent_contractors) || !empty($recent_contracts) || !empty($recent_payments)): ?>
                    <?php 
                    $all_activities = [];
                    
                    if (!empty($recent_contractors)) {
                        foreach ($recent_contractors as $contractor) {
                            $all_activities[] = [
                                'type' => 'contractor',
                                'title' => 'New contractor added: ' . $contractor->company_name,
                                'date' => $contractor->date_created,
                                'icon' => 'fa-user-plus'
                            ];
                        }
                    }
                    
                    if (!empty($recent_contracts)) {
                        foreach ($recent_contracts as $contract) {
                            $all_activities[] = [
                                'type' => 'contract',
                                'title' => 'New contract: ' . $contract->title,
                                'date' => $contract->date_created,
                                'icon' => 'fa-file-contract'
                            ];
                        }
                    }
                    
                    if (!empty($recent_payments)) {
                        foreach ($recent_payments as $payment) {
                            $all_activities[] = [
                                'type' => 'payment',
                                'title' => 'Payment recorded: $' . number_format($payment->amount, 2),
                                'date' => $payment->date_created,
                                'icon' => 'fa-dollar-sign'
                            ];
                        }
                    }
                    
                    // Sort by date (newest first)
                    usort($all_activities, function($a, $b) {
                        return strtotime($b['date']) - strtotime($a['date']);
                    });
                    
                    // Show only first 10
                    $all_activities = array_slice($all_activities, 0, 10);
                    ?>
                    
                    <?php foreach ($all_activities as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="<?= $activity['icon'] ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title"><?= htmlspecialchars($activity['title']) ?></div>
                                <div class="activity-meta"><?= _dt($activity['date']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa fa-clock"></i>
                        <h5>No recent activity</h5>
                        <p>Start by adding contractors, contracts, or recording payments.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h6 class="chart-title">Contract Status Distribution</h6>
                        </div>
                        <canvas id="contractStatusChart" width="400" height="200"></canvas>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h6 class="chart-title">Monthly Payments</h6>
                            <div class="chart-filters">
                                <button class="filter-btn active" data-period="6">6M</button>
                                <button class="filter-btn" data-period="12">12M</button>
                            </div>
                        </div>
                        <canvas id="paymentsChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Data Tables -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header-custom">
                    <h4 class="mb-0"><i class="fa fa-users"></i> Recent Contractors</h4>
                </div>
                <div class="card-body-custom">
                    <?php if (!empty($recent_contractors)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Added</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($recent_contractors, 0, 5) as $contractor): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= admin_url('ella_contractors/contractors/view/' . $contractor->id) ?>">
                                                    <?= htmlspecialchars($contractor->company_name) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="status-indicator status-<?= $contractor->status ?>"></span>
                                                <?= ucfirst($contractor->status) ?>
                                            </td>
                                            <td><?= _dt($contractor->date_created) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2">
                            <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-sm btn-outline-primary">
                                View All Contractors
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa fa-users"></i>
                            <p>No contractors added yet.</p>
                            <a href="<?= admin_url('ella_contractors/contractors/add') ?>" class="btn btn-primary">
                                Add First Contractor
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header-custom">
                    <h4 class="mb-0"><i class="fa fa-file-contract"></i> Recent Contracts</h4>
                </div>
                <div class="card-body-custom">
                    <?php if (!empty($recent_contracts)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Contract</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($recent_contracts, 0, 5) as $contract): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= admin_url('ella_contractors/contracts/view/' . $contract->id) ?>">
                                                    <?= htmlspecialchars($contract->title) ?>
                                                </a>
                                            </td>
                                            <td>$<?= number_format($contract->amount, 2) ?></td>
                                            <td>
                                                <span class="status-indicator status-<?= $contract->status ?>"></span>
                                                <?= ucfirst($contract->status) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2">
                            <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-sm btn-outline-primary">
                                View All Contracts
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa fa-file-contract"></i>
                            <p>No contracts created yet.</p>
                            <a href="<?= admin_url('ella_contractors/contracts/add') ?>" class="btn btn-primary">
                                Create First Contract
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Contract Status Chart
    const contractCtx = document.getElementById('contractStatusChart').getContext('2d');
    const contractChart = new Chart(contractCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Draft', 'Completed', 'Terminated'],
            datasets: [{
                data: [
                    <?= $active_contracts ?? 0 ?>,
                    <?= $draft_contracts ?? 0 ?>,
                    <?= $completed_contracts ?? 0 ?>,
                    <?= $terminated_contracts ?? 0 ?>
                ],
                backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Payments Chart
    const paymentsCtx = document.getElementById('paymentsChart').getContext('2d');
    const paymentsChart = new Chart(paymentsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Payments',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: '#4075A1',
                backgroundColor: 'rgba(64, 117, 161, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Chart filter buttons
    $('.filter-btn').on('click', function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Here you would typically update the chart data based on the selected period
        // For now, we'll just show a simple alert
        var period = $(this).data('period');
        console.log('Filter changed to ' + period + ' months');
    });

    // Auto-refresh dashboard every 5 minutes
    setInterval(function() {
        // You could implement AJAX refresh here
        console.log('Dashboard refresh interval');
    }, 300000);
});
</script>

<?php init_tail(); ?>
