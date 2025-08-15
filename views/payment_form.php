<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($payment) ? 'Edit Payment' : 'Add New Payment' ?> - Ella Contractors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-section { background-color: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
        .form-section h5 { color: #495057; margin-bottom: 1rem; }
        .required-field::after { content: " *"; color: #dc3545; }
        .help-text { font-size: 0.875rem; color: #6c757d; margin-top: 0.25rem; }
        .contract-info { background-color: #e3f2fd; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1>
                        <i class="fas fa-credit-card"></i> 
                        <?= isset($payment) ? 'Edit Payment' : 'Add New Payment' ?>
                    </h1>
                    <a href="<?= admin_url('ella_contractors/payments') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Payments
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="card">
            <div class="card-body">
                <form method="POST" action="" id="paymentForm">
                    
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h5><i class="fas fa-info-circle"></i> Basic Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="contractor_id" class="form-label required-field">Contractor</label>
                                <select class="form-select" id="contractor_id" name="contractor_id" required>
                                    <option value="">Select Contractor</option>
                                    <?php foreach ($contractors as $contractor): ?>
                                        <option value="<?= $contractor->id ?>" 
                                                <?= (isset($payment) && $payment->contractor_id == $contractor->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($contractor->company_name) ?> - <?= htmlspecialchars($contractor->contact_person) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="help-text">Choose the contractor for this payment</div>
                            </div>
                            <div class="col-md-6">
                                <label for="contract_id" class="form-label">Related Contract</label>
                                <select class="form-select" id="contract_id" name="contract_id">
                                    <option value="">Select Contract (Optional)</option>
                                    <!-- This will be populated via AJAX when contractor is selected -->
                                </select>
                                <div class="help-text">Link this payment to a specific contract</div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="invoice_number" class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" id="invoice_number" name="invoice_number" 
                                       value="<?= isset($payment) ? htmlspecialchars($payment->invoice_number) : '' ?>"
                                       placeholder="e.g., INV-2024-001">
                                <div class="help-text">Optional: Custom invoice number (auto-generated if left blank)</div>
                            </div>
                            <div class="col-md-6">
                                <label for="amount" class="form-label required-field">Payment Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                           step="0.01" min="0" required
                                           value="<?= isset($payment) ? $payment->amount : '' ?>"
                                           placeholder="0.00">
                                </div>
                                <div class="help-text">Payment amount in USD</div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="form-section">
                        <h5><i class="fas fa-credit-card"></i> Payment Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="invoice_date" class="form-label">Invoice Date</label>
                                <input type="date" class="form-control" id="invoice_date" name="invoice_date"
                                       value="<?= isset($payment) ? $payment->invoice_date : date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date"
                                       value="<?= isset($payment) ? $payment->due_date : '' ?>">
                                <div class="help-text">When payment is due</div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label required-field">Payment Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" <?= (isset($payment) && $payment->status == 'pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="paid" <?= (isset($payment) && $payment->status == 'paid') ? 'selected' : '' ?>>Paid</option>
                                    <option value="overdue" <?= (isset($payment) && $payment->status == 'overdue') ? 'selected' : '' ?>>Overdue</option>
                                    <option value="cancelled" <?= (isset($payment) && $payment->status == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="">Select Method</option>
                                    <option value="check" <?= (isset($payment) && $payment->payment_method == 'check') ? 'selected' : '' ?>>Check</option>
                                    <option value="bank_transfer" <?= (isset($payment) && $payment->payment_method == 'bank_transfer') ? 'selected' : '' ?>>Bank Transfer</option>
                                    <option value="credit_card" <?= (isset($payment) && $payment->payment_method == 'credit_card') ? 'selected' : '' ?>>Credit Card</option>
                                    <option value="cash" <?= (isset($payment) && $payment->payment_method == 'cash') ? 'selected' : '' ?>>Cash</option>
                                    <option value="other" <?= (isset($payment) && $payment->payment_method == 'other') ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Description and Notes -->
                    <div class="form-section">
                        <h5><i class="fas fa-sticky-note"></i> Description & Notes</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Payment Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                          placeholder="Describe what this payment is for, services rendered, etc..."><?= isset($payment) ? htmlspecialchars($payment->description) : '' ?></textarea>
                                <div class="help-text">Clear description of what this payment covers</div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="notes" class="form-label">Internal Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"
                                          placeholder="Any internal notes, special instructions, or important information..."><?= isset($payment) ? htmlspecialchars($payment->notes) : '' ?></textarea>
                                <div class="help-text">Internal notes and comments about the payment</div>
                            </div>
                        </div>
                    </div>

                    <!-- Contract Information Display -->
                    <div id="contractInfo" class="contract-info" style="display: none;">
                        <h6><i class="fas fa-file-contract"></i> Contract Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Project:</strong> <span id="contractProject">-</span><br>
                                <strong>Contract Value:</strong> <span id="contractValue">-</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Start Date:</strong> <span id="contractStart">-</span><br>
                                <strong>End Date:</strong> <span id="contractEnd">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="<?= admin_url('ella_contractors/payments') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <div>
                                    <?php if (isset($payment)): ?>
                                        <a href="<?= admin_url('ella_contractors/pdf/invoice/' . $payment->id) ?>" 
                                           class="btn btn-outline-danger me-2">
                                            <i class="fas fa-file-pdf"></i> Generate PDF
                                        </a>
                                    <?php endif; ?>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> 
                                        <?= isset($payment) ? 'Update Payment' : 'Create Payment' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const contractorId = document.getElementById('contractor_id').value;
            const amount = document.getElementById('amount').value;
            const status = document.getElementById('status').value;

            if (!contractorId || !amount || !status) {
                e.preventDefault();
                alert('Please fill in all required fields marked with *');
                return false;
            }

            if (parseFloat(amount) <= 0) {
                e.preventDefault();
                alert('Payment amount must be greater than 0');
                return false;
            }

            // Date validation
            const invoiceDate = document.getElementById('invoice_date').value;
            const dueDate = document.getElementById('due_date').value;
            
            if (invoiceDate && dueDate && invoiceDate > dueDate) {
                e.preventDefault();
                alert('Invoice date cannot be after due date');
                return false;
            }
        });

        // Auto-generate invoice number if empty
        document.getElementById('contractor_id').addEventListener('change', function() {
            const invoiceNumber = document.getElementById('invoice_number');
            if (!invoiceNumber.value) {
                const year = new Date().getFullYear();
                const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                invoiceNumber.value = `INV-${year}-${random}`;
            }
            
            // Load contracts for this contractor
            loadContracts(this.value);
        });

        // Load contracts when contractor is selected
        function loadContracts(contractorId) {
            if (!contractorId) {
                document.getElementById('contract_id').innerHTML = '<option value="">Select Contract (Optional)</option>';
                document.getElementById('contractInfo').style.display = 'none';
                return;
            }

            // This would typically be an AJAX call to get contracts
            // For now, we'll show a placeholder
            document.getElementById('contract_id').innerHTML = '<option value="">Loading contracts...</option>';
            
            // Simulate loading contracts (replace with actual AJAX call)
            setTimeout(() => {
                document.getElementById('contract_id').innerHTML = `
                    <option value="">Select Contract (Optional)</option>
                    <option value="1">CON-2024-001 - Office Renovation</option>
                    <option value="2">CON-2024-002 - Electrical Work</option>
                `;
            }, 500);
        }

        // Show contract info when contract is selected
        document.getElementById('contract_id').addEventListener('change', function() {
            const contractId = this.value;
            const contractInfo = document.getElementById('contractInfo');
            
            if (contractId) {
                // This would typically be an AJAX call to get contract details
                // For now, we'll show sample data
                document.getElementById('contractProject').textContent = 'Office Building Renovation';
                document.getElementById('contractValue').textContent = '$125,000.00';
                document.getElementById('contractStart').textContent = 'Jan 15, 2024';
                document.getElementById('contractEnd').textContent = 'Jun 30, 2024';
                contractInfo.style.display = 'block';
            } else {
                contractInfo.style.display = 'none';
            }
        });

        // Status change handler
        document.getElementById('status').addEventListener('change', function() {
            const status = this.value;
            const dueDate = document.getElementById('due_date');
            
            if (status === 'paid') {
                // If marked as paid, set due date to today if not set
                if (!dueDate.value) {
                    dueDate.value = new Date().toISOString().split('T')[0];
                }
            }
        });
    </script>
</body>
</html>
