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

    .contractor-header {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 12px;
        border-left: 4px solid #4075A1;
    }

    .contractor-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #4075A1;
        margin-right: 20px;
        flex-shrink: 0;
    }

    .contractor-info h2 {
        color: #4075A1;
        margin-bottom: 10px;
    }

    .contractor-meta {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
    }

    .meta-item i {
        color: #4075A1;
        width: 16px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-active { background-color: #d4edda; color: #155724; }
    .status-inactive { background-color: #f8d7da; color: #721c24; }
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-suspended { background-color: #e2e3e5; color: #383d41; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-section {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
    }

    .info-section h5 {
        color: #4075A1;
        margin-bottom: 15px;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 10px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
    }

    .info-value {
        color: #6c757d;
        text-align: right;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #4075A1;
        color: white;
        border: 1px solid #4075A1;
    }

    .btn-primary:hover {
        background-color: #36648b;
        border-color: #36648b;
        color: white;
    }

    .btn-warning {
        background-color: #ffc107;
        color: #212529;
        border: 1px solid #ffc107;
    }

    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
        color: #212529;
    }

    .btn-info {
        background-color: #17a2b8;
        color: white;
        border: 1px solid #17a2b8;
    }

    .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
        color: white;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
        border: 1px solid #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
        color: white;
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    .table th {
        background-color: #f8f9fa;
        border-top: none;
        font-weight: 600;
        color: #495057;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(135deg, #4075A1 0%, #36648b 100%);
        color: white;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(64, 117, 161, 0.3);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
</style>

<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="no-margin">
                            <i class="fa fa-user"></i> Contractor Details
                        </h4>
                        <div class="action-buttons">
                            <a href="<?= admin_url('ella_contractors/contractors/edit/' . $contractor->id) ?>" class="btn btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contractor Header -->
    <div class="contractor-header">
        <?php if ($contractor->profile_image): ?>
            <img src="<?= base_url($contractor->profile_image) ?>" alt="Profile" class="contractor-avatar">
        <?php else: ?>
            <div class="contractor-avatar bg-secondary d-flex align-items-center justify-content-center">
                <i class="fa fa-user fa-3x text-white"></i>
            </div>
        <?php endif; ?>
        
        <div class="contractor-info">
            <h2><?= htmlspecialchars($contractor->company_name) ?></h2>
            <div class="contractor-meta">
                <div class="meta-item">
                    <i class="fa fa-user"></i>
                    <span><?= htmlspecialchars($contractor->contact_person) ?></span>
                </div>
                <div class="meta-item">
                    <i class="fa fa-envelope"></i>
                    <a href="mailto:<?= htmlspecialchars($contractor->email) ?>" class="text-info">
                        <?= htmlspecialchars($contractor->email) ?>
                    </a>
                </div>
                <?php if ($contractor->phone): ?>
                    <div class="meta-item">
                        <i class="fa fa-phone"></i>
                        <a href="tel:<?= htmlspecialchars($contractor->phone) ?>" class="text-info">
                            <?= htmlspecialchars($contractor->phone) ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if ($contractor->website): ?>
                    <div class="meta-item">
                        <i class="fa fa-globe"></i>
                        <a href="<?= htmlspecialchars($contractor->website) ?>" target="_blank" class="text-info">
                            <?= htmlspecialchars($contractor->website) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="ml-auto text-right">
            <div class="status-badge status-<?= $contractor->status ?>">
                <?= ucfirst($contractor->status) ?>
            </div>
            <?php if ($contractor->hourly_rate): ?>
                <div class="mt-2">
                    <strong class="text-primary">$<?= number_format($contractor->hourly_rate, 2) ?>/hr</strong>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-number"><?= count($contracts) ?></div>
            <div class="stat-label">Total Contracts</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= count($projects) ?></div>
            <div class="stat-label">Total Projects</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= count($payments) ?></div>
            <div class="stat-label">Total Payments</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= count($documents) ?></div>
            <div class="stat-label">Documents</div>
        </div>
    </div>

    <!-- Information Grid -->
    <div class="info-grid">
        <!-- Basic Information -->
        <div class="info-section">
            <h5><i class="fa fa-info-circle"></i> Basic Information</h5>
            <div class="info-row">
                <span class="info-label">Company Name</span>
                <span class="info-value"><?= htmlspecialchars($contractor->company_name) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Contact Person</span>
                <span class="info-value"><?= htmlspecialchars($contractor->contact_person) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value"><?= htmlspecialchars($contractor->email) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone</span>
                <span class="info-value"><?= htmlspecialchars($contractor->phone) ?: 'Not provided' ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Website</span>
                <span class="info-value"><?= htmlspecialchars($contractor->website) ?: 'Not provided' ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    <span class="status-badge status-<?= $contractor->status ?>">
                        <?= ucfirst($contractor->status) ?>
                    </span>
                </span>
            </div>
        </div>

        <!-- Address Information -->
        <div class="info-section">
            <h5><i class="fa fa-map-marker"></i> Address Information</h5>
            <div class="info-row">
                <span class="info-label">Address</span>
                <span class="info-value"><?= htmlspecialchars($contractor->address) ?: 'Not provided' ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">City</span>
                <span class="info-value"><?= htmlspecialchars($contractor->city) ?: 'Not provided' ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">State/Province</span>
                <span class="info-value"><?= htmlspecialchars($contractor->state) ?: 'Not provided' ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">ZIP/Postal Code</span>
                <span class="info-value"><?= htmlspecialchars($contractor->zip_code) ?: 'Not provided' ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Country</span>
                <span class="info-value"><?= htmlspecialchars($contractor->country) ?: 'Not provided' ?></span>
            </div>
        </div>

        <!-- Business Information -->
        <div class="info-section">
            <h5><i class="fa fa-briefcase"></i> Business Information</h5>
            <div class="info-row">
                <span class="info-label">Tax ID / EIN</span>
                <span class="info-value"><?= htmlspecialchars($contractor->tax_id) ?: 'Not provided' ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Business License</span>
                <span class="info-value"><?= htmlspecialchars($contractor->business_license) ?: 'Not provided' ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Specialties</span>
                <span class="info-value"><?= htmlspecialchars($contractor->specialties) ?: 'Not specified' ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Hourly Rate</span>
                <span class="info-value">
                    <?= $contractor->hourly_rate ? '$' . number_format($contractor->hourly_rate, 2) . '/hr' : 'Not set' ?>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Created</span>
                <span class="info-value"><?= _dt($contractor->date_created) ?></span>
            </div>
        </div>
    </div>

    <!-- Insurance Information -->
    <?php if ($contractor->insurance_info): ?>
        <div class="card">
            <div class="card-header-custom">
                <h4 class="mb-0"><i class="fa fa-shield-alt"></i> Insurance Information</h4>
            </div>
            <div class="card-body-custom">
                <p><?= nl2br(htmlspecialchars($contractor->insurance_info)) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Notes -->
    <?php if ($contractor->notes): ?>
        <div class="card">
            <div class="card-header-custom">
                <h4 class="mb-0"><i class="fa fa-sticky-note"></i> Notes & Comments</h4>
            </div>
            <div class="card-body-custom">
                <p><?= nl2br(htmlspecialchars($contractor->notes)) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Contracts Section -->
    <div class="card">
        <div class="card-header-custom">
            <h4 class="mb-0">
                <i class="fa fa-file-contract"></i> Contracts (<?= count($contracts) ?>)
            </h4>
        </div>
        <div class="card-body-custom">
            <?php if (!empty($contracts)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Amount</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contracts as $contract): ?>
                                <tr>
                                    <td>
                                        <a href="<?= admin_url('ella_contractors/contracts/view/' . $contract->id) ?>">
                                            <?= htmlspecialchars($contract->title) ?>
                                        </a>
                                    </td>
                                    <td>$<?= number_format($contract->amount, 2) ?></td>
                                    <td><?= _d($contract->start_date) ?></td>
                                    <td><?= _d($contract->end_date) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $contract->status ?>">
                                            <?= ucfirst($contract->status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= admin_url('ella_contractors/contracts/view/' . $contract->id) ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa fa-file-contract"></i>
                    <h5>No contracts found</h5>
                    <p>This contractor doesn't have any contracts yet.</p>
                    <a href="<?= admin_url('ella_contractors/contracts/add?contractor_id=' . $contractor->id) ?>" 
                       class="btn btn-primary">
                        <i class="fa fa-plus"></i> Create Contract
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Projects Section -->
    <div class="card">
        <div class="card-header-custom">
            <h4 class="mb-0">
                <i class="fa fa-tasks"></i> Projects (<?= count($projects) ?>)
            </h4>
        </div>
        <div class="card-body-custom">
            <?php if (!empty($projects)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Project Name</th>
                                <th>Budget</th>
                                <th>Start Date</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td>
                                        <a href="<?= admin_url('ella_contractors/projects/view/' . $project->id) ?>">
                                            <?= htmlspecialchars($project->name) ?>
                                        </a>
                                    </td>
                                    <td>$<?= number_format($project->budget, 2) ?></td>
                                    <td><?= _d($project->start_date) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $project->status ?>">
                                            <?= ucfirst($project->status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?= $project->progress ?>%">
                                                <?= $project->progress ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="<?= admin_url('ella_contractors/projects/view/' . $project->id) ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa fa-tasks"></i>
                    <h5>No projects found</h5>
                    <p>This contractor doesn't have any projects yet.</p>
                    <a href="<?= admin_url('ella_contractors/projects/add?contractor_id=' . $contractor->id) ?>" 
                       class="btn btn-primary">
                        <i class="fa fa-plus"></i> Create Project
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Documents Section -->
    <div class="card">
        <div class="card-header-custom">
            <h4 class="mb-0">
                <i class="fa fa-folder-open"></i> Documents (<?= count($documents) ?>)
            </h4>
        </div>
        <div class="card-body-custom">
            <?php if (!empty($documents)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>File Size</th>
                                <th>Uploaded</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documents as $document): ?>
                                <tr>
                                    <td><?= htmlspecialchars($document->title) ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= ucfirst($document->document_type) ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($document->file_size / 1024, 2) ?> KB</td>
                                    <td><?= _dt($document->date_uploaded) ?></td>
                                    <td>
                                        <a href="<?= admin_url('ella_contractors/documents/download?id=' . $document->id) ?>" 
                                           class="btn btn-sm btn-success">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa fa-folder-open"></i>
                    <h5>No documents found</h5>
                    <p>This contractor doesn't have any documents uploaded yet.</p>
                    <a href="<?= admin_url('ella_contractors/documents/upload?contractor_id=' . $contractor->id) ?>" 
                       class="btn btn-primary">
                        <i class="fa fa-upload"></i> Upload Document
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
