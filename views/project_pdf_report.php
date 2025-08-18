<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details - <?= htmlspecialchars($project->name) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #4075A1;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #4075A1;
            margin: 0;
            font-size: 28px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            color: #4075A1;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            flex-shrink: 0;
        }
        .info-value {
            flex: 1;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            background-color: #4075A1;
            transition: width 0.3s ease;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-style: italic;
            color: #666;
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PROJECT DETAILS</h1>
        <h2><?= htmlspecialchars($project->name) ?></h2>
        <p>Generated on <?= date('F j, Y \a\t g:i A') ?></p>
    </div>

    <div class="section">
        <h2>Project Information</h2>
        <div class="info-row">
            <div class="info-label">Project Name:</div>
            <div class="info-value"><?= htmlspecialchars($project->name) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Contractor:</div>
            <div class="info-value"><?= htmlspecialchars($contractor->company_name) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Budget:</div>
            <div class="info-value">$<?= number_format($project->budget, 2) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Start Date:</div>
            <div class="info-value"><?= date('F j, Y', strtotime($project->start_date)) ?></div>
        </div>
        <?php if ($project->end_date): ?>
        <div class="info-row">
            <div class="info-label">End Date:</div>
            <div class="info-value"><?= date('F j, Y', strtotime($project->end_date)) ?></div>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value"><?= ucfirst($project->status) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Progress:</div>
            <div class="info-value">
                <?= $project->progress ?>%
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $project->progress ?>%"></div>
                </div>
            </div>
        </div>
        <?php if ($project->location): ?>
        <div class="info-row">
            <div class="info-label">Location:</div>
            <div class="info-value"><?= htmlspecialchars($project->location) ?></div>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($project->description): ?>
    <div class="section">
        <h2>Description</h2>
        <p><?= nl2br(htmlspecialchars($project->description)) ?></p>
    </div>
    <?php endif; ?>

    <?php if ($project->notes): ?>
    <div class="section">
        <h2>Notes</h2>
        <p><?= nl2br(htmlspecialchars($project->notes)) ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Generated on <?= date('F j, Y \a\t g:i A') ?> by Ella Contractors CRM</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()">Print Report</button>
        <button onclick="window.close()">Close</button>
    </div>
</body>
</html>
