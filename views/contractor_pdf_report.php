<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contractor Profile - <?= htmlspecialchars($contractor->company_name) ?></title>
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
        .list-item {
            margin-bottom: 5px;
            padding-left: 20px;
        }
        .total {
            font-weight: bold;
            font-size: 16px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e9ecef;
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
        <h1>CONTRACTOR PROFILE</h1>
        <h2><?= htmlspecialchars($contractor->company_name) ?></h2>
        <p>Generated on <?= date('F j, Y \a\t g:i A') ?></p>
    </div>

    <div class="section">
        <h2>Company Information</h2>
        <div class="info-row">
            <div class="info-label">Company Name:</div>
            <div class="info-value"><?= htmlspecialchars($contractor->company_name) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Contact Person:</div>
            <div class="info-value"><?= htmlspecialchars($contractor->contact_person) ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value"><?= htmlspecialchars($contractor->email) ?></div>
        </div>
        <?php if ($contractor->phone): ?>
        <div class="info-row">
            <div class="info-label">Phone:</div>
            <div class="info-value"><?= htmlspecialchars($contractor->phone) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($contractor->website): ?>
        <div class="info-row">
            <div class="info-label">Website:</div>
            <div class="info-value"><?= htmlspecialchars($contractor->website) ?></div>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value"><?= ucfirst($contractor->status) ?></div>
        </div>
        <?php if ($contractor->hourly_rate): ?>
        <div class="info-row">
            <div class="info-label">Hourly Rate:</div>
            <div class="info-value">$<?= number_format($contractor->hourly_rate, 2) ?>/hr</div>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($contractor->address || $contractor->city || $contractor->state): ?>
    <div class="section">
        <h2>Address Information</h2>
        <?php if ($contractor->address): ?>
        <div class="info-row">
            <div class="info-label">Address:</div>
            <div class="info-value"><?= htmlspecialchars($contractor->address) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($contractor->city || $contractor->state || $contractor->zip_code): ?>
        <div class="info-row">
            <div class="info-label">City/State/ZIP:</div>
            <div class="info-value">
                <?php
                $address_parts = [];
                if ($contractor->city) $address_parts[] = $contractor->city;
                if ($contractor->state) $address_parts[] = $contractor->state;
                if ($contractor->zip_code) $address_parts[] = $contractor->zip_code;
                echo htmlspecialchars(implode(', ', $address_parts));
                ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($contractor->country): ?>
        <div class="info-row">
            <div class="info-label">Country:</div>
            <div class="info-value"><?= htmlspecialchars($contractor->country) ?></div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($contractor->tax_id || $contractor->business_license || $contractor->specialties): ?>
    <div class="section">
        <h2>Business Information</h2>
        <?php if ($contractor->tax_id): ?>
        <div class="info-row">
            <div class="info-label">Tax ID:</div>
            <div class="info-value"><?= htmlspecialchars($contractor->tax_id) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($contractor->business_license): ?>
        <div class="info-row">
            <div class="info-label">Business License:</div>
            <div class="info-value"><?= htmlspecialchars($contractor->business_license) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($contractor->specialties): ?>
        <div class="info-row">
            <div class="info-label">Specialties:</div>
            <div class="info-value"><?= htmlspecialchars($contractor->specialties) ?></div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($contracts)): ?>
    <div class="section">
        <h2>Contracts Summary (<?= count($contracts) ?>)</h2>
        <?php 
        $total_contract_value = 0;
        foreach ($contracts as $contract): 
            $total_contract_value += $contract->amount;
        ?>
        <div class="list-item">
            • <?= htmlspecialchars($contract->title) ?> - $<?= number_format($contract->amount, 2) ?> (<?= ucfirst($contract->status) ?>)
        </div>
        <?php endforeach; ?>
        <div class="total">Total Contract Value: $<?= number_format($total_contract_value, 2) ?></div>
    </div>
    <?php endif; ?>

    <?php if (!empty($projects)): ?>
    <div class="section">
        <h2>Projects Summary (<?= count($projects) ?>)</h2>
        <?php 
        $total_project_budget = 0;
        foreach ($projects as $project): 
            $total_project_budget += $project->budget;
        ?>
        <div class="list-item">
            • <?= htmlspecialchars($project->name) ?> - $<?= number_format($project->budget, 2) ?> (<?= ucfirst($project->status) ?>)
        </div>
        <?php endforeach; ?>
        <div class="total">Total Project Budget: $<?= number_format($total_project_budget, 2) ?></div>
    </div>
    <?php endif; ?>

    <?php if (!empty($payments)): ?>
    <div class="section">
        <h2>Payments Summary (<?= count($payments) ?>)</h2>
        <?php 
        $total_paid = 0;
        $total_pending = 0;
        foreach ($payments as $payment): 
            if ($payment->status == 'paid') {
                $total_paid += $payment->amount;
            } elseif ($payment->status == 'pending') {
                $total_pending += $payment->amount;
            }
        ?>
        <div class="list-item">
            • $<?= number_format($payment->amount, 2) ?> - <?= ucfirst($payment->status) ?> (<?= date('M j, Y', strtotime($payment->payment_date)) ?>)
        </div>
        <?php endforeach; ?>
        <div class="total">Total Paid: $<?= number_format($total_paid, 2) ?></div>
        <div class="total">Total Pending: $<?= number_format($total_pending, 2) ?></div>
    </div>
    <?php endif; ?>

    <?php if (!empty($documents)): ?>
    <div class="section">
        <h2>Documents (<?= count($documents) ?>)</h2>
        <?php foreach ($documents as $document): ?>
        <div class="list-item">
            • <?= htmlspecialchars($document->title) ?> (<?= ucfirst($document->document_type) ?>)
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($contractor->notes): ?>
    <div class="section">
        <h2>Notes & Comments</h2>
        <p><?= nl2br(htmlspecialchars($contractor->notes)) ?></p>
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
