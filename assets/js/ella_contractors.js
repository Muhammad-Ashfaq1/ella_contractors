/**
 * Ella Contractors Module JavaScript
 */

var EllaContractors = {
    
    /**
     * Initialize the module
     */
    init: function() {
        this.initDataTables();
        this.initFormValidation();
        this.initEventHandlers();
        this.initCharts();
    },

    /**
     * Initialize DataTables
     */
    initDataTables: function() {
        // Common DataTable settings
        var commonSettings = {
            responsive: true,
            pageLength: 25,
            language: {
                emptyTable: "No data available",
                zeroRecords: "No matching records found",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                lengthMenu: "Show _MENU_ entries",
                search: "Search:",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip'
        };

        // Initialize contractors table
        if ($('#contractors-table').length) {
            $('#contractors-table').DataTable($.extend({}, commonSettings, {
                ajax: {
                    url: admin_url + 'ella_contractors/ajax/contractors_table',
                    type: 'POST',
                    data: function(d) {
                        d.status = $('#status-filter').val();
                        d.category = $('#category-filter').val();
                        d.search_term = $('#search-input').val();
                    }
                },
                columns: [
                    {data: 'company_name', name: 'company_name'},
                    {data: 'contact_person', name: 'contact_person'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    {data: 'specialties', name: 'specialties'},
                    {data: 'status', name: 'status', orderable: false},
                    {data: 'date_created', name: 'date_created'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[6, 'desc']]
            }));
        }

        // Initialize contracts table
        if ($('#contracts-table').length) {
            $('#contracts-table').DataTable($.extend({}, commonSettings, {
                ajax: {
                    url: admin_url + 'ella_contractors/ajax/contracts_table',
                    type: 'POST'
                },
                columns: [
                    {data: 'contract_number', name: 'contract_number'},
                    {data: 'contractor_name', name: 'contractor_name'},
                    {data: 'title', name: 'title'},
                    {data: 'value', name: 'value'},
                    {data: 'status', name: 'status', orderable: false},
                    {data: 'end_date', name: 'end_date'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[5, 'desc']]
            }));
        }

        // Initialize payments table
        if ($('#payments-table').length) {
            $('#payments-table').DataTable($.extend({}, commonSettings, {
                ajax: {
                    url: admin_url + 'ella_contractors/ajax/payments_table',
                    type: 'POST'
                },
                columns: [
                    {data: 'contractor_name', name: 'contractor_name'},
                    {data: 'contract_number', name: 'contract_number'},
                    {data: 'amount', name: 'amount'},
                    {data: 'due_date', name: 'due_date'},
                    {data: 'status', name: 'status', orderable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[3, 'asc']]
            }));
        }
    },

    /**
     * Initialize form validation
     */
    initFormValidation: function() {
        // Contractor form validation
        $('#contractor-form').validate({
            rules: {
                company_name: {
                    required: true,
                    minlength: 2
                },
                email: {
                    email: true
                },
                hourly_rate: {
                    number: true,
                    min: 0
                }
            },
            messages: {
                company_name: {
                    required: "Company name is required",
                    minlength: "Company name must be at least 2 characters"
                },
                email: "Please enter a valid email address",
                hourly_rate: {
                    number: "Please enter a valid number",
                    min: "Hourly rate cannot be negative"
                }
            },
            errorClass: "text-danger",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });

        // Contract form validation
        $('#contract-form').validate({
            rules: {
                contractor_id: {
                    required: true
                },
                title: {
                    required: true,
                    minlength: 3
                },
                start_date: {
                    required: true,
                    date: true
                },
                end_date: {
                    required: true,
                    date: true
                }
            },
            messages: {
                contractor_id: "Please select a contractor",
                title: {
                    required: "Contract title is required",
                    minlength: "Title must be at least 3 characters"
                },
                start_date: "Please enter a valid start date",
                end_date: "Please enter a valid end date"
            }
        });

        // Payment form validation
        $('#payment-form').validate({
            rules: {
                contractor_id: {
                    required: true
                },
                amount: {
                    required: true,
                    number: true,
                    min: 0.01
                },
                due_date: {
                    required: true,
                    date: true
                }
            },
            messages: {
                contractor_id: "Please select a contractor",
                amount: {
                    required: "Payment amount is required",
                    number: "Please enter a valid amount",
                    min: "Amount must be greater than 0"
                },
                due_date: "Please enter a valid due date"
            }
        });
    },

    /**
     * Initialize event handlers
     */
    initEventHandlers: function() {
        var self = this;

        // Filter change handlers
        $('#status-filter, #category-filter').on('change', function() {
            var table = $('#contractors-table').DataTable();
            if (table) {
                table.ajax.reload();
            }
        });

        // Search functionality
        $('#search-btn').on('click', function() {
            var table = $('#contractors-table').DataTable();
            if (table) {
                table.ajax.reload();
            }
        });

        $('#search-input').on('keypress', function(e) {
            if (e.which == 13) {
                var table = $('#contractors-table').DataTable();
                if (table) {
                    table.ajax.reload();
                }
            }
        });

        // Delete confirmation handlers
        $(document).on('click', '.delete-contractor', function(e) {
            e.preventDefault();
            self.confirmDelete($(this), 'contractor', 'Are you sure you want to delete this contractor?');
        });

        $(document).on('click', '.delete-contract', function(e) {
            e.preventDefault();
            self.confirmDelete($(this), 'contract', 'Are you sure you want to delete this contract?');
        });

        $(document).on('click', '.delete-payment', function(e) {
            e.preventDefault();
            self.confirmDelete($(this), 'payment', 'Are you sure you want to delete this payment?');
        });

        // Status update handlers
        $(document).on('click', '.update-status', function(e) {
            e.preventDefault();
            self.updateStatus($(this));
        });

        // Quick actions
        $(document).on('click', '.quick-approve', function(e) {
            e.preventDefault();
            self.quickApprove($(this));
        });

        // Auto-save for forms
        $('.auto-save').on('change', function() {
            self.autoSave($(this));
        });

        // File upload handlers
        $('#document-upload').on('change', function() {
            self.handleFileUpload($(this));
        });

        // Notification handlers
        $('.notification-close').on('click', function() {
            $(this).closest('.notification-panel').fadeOut();
        });
    },

    /**
     * Initialize charts
     */
    initCharts: function() {
        // Dashboard charts
        if ($('#contractors-by-status-chart').length) {
            this.initStatusChart();
        }

        if ($('#payments-by-month-chart').length) {
            this.initPaymentsChart();
        }

        if ($('#revenue-chart').length) {
            this.initRevenueChart();
        }
    },

    /**
     * Initialize status chart
     */
    initStatusChart: function() {
        var ctx = document.getElementById('contractors-by-status-chart').getContext('2d');
        
        // Get data from backend
        $.get(admin_url + 'ella_contractors/ajax/chart_data/status', function(data) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: [
                            '#28a745',
                            '#6c757d',
                            '#ffc107',
                            '#dc3545'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'bottom'
                    }
                }
            });
        });
    },

    /**
     * Initialize payments chart
     */
    initPaymentsChart: function() {
        var ctx = document.getElementById('payments-by-month-chart').getContext('2d');
        
        $.get(admin_url + 'ella_contractors/ajax/chart_data/payments', function(data) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Payments',
                        data: data.values,
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
        });
    },

    /**
     * Confirm delete action
     */
    confirmDelete: function(element, type, message) {
        var id = element.data('id');
        
        if (confirm(message + ' This action cannot be undone.')) {
            var url = admin_url + 'ella_contractors/' + type + '/delete/' + id;
            
            $.post(url, function(response) {
                if (response.success) {
                    alert_float('success', ucfirst(type) + ' deleted successfully');
                    
                    // Reload the appropriate table
                    var tableId = '#' + type + 's-table';
                    if ($(tableId).length) {
                        $(tableId).DataTable().ajax.reload();
                    } else {
                        location.reload();
                    }
                } else {
                    alert_float('danger', response.message || 'Failed to delete ' + type);
                }
            }, 'json').fail(function() {
                alert_float('danger', 'An error occurred while deleting the ' + type);
            });
        }
    },

    /**
     * Update status
     */
    updateStatus: function(element) {
        var id = element.data('id');
        var type = element.data('type');
        var status = element.data('status');
        
        var url = admin_url + 'ella_contractors/' + type + '/status/' + id;
        
        $.post(url, {status: status}, function(response) {
            if (response.success) {
                alert_float('success', 'Status updated successfully');
                
                // Reload the appropriate table
                var tableId = '#' + type + 's-table';
                if ($(tableId).length) {
                    $(tableId).DataTable().ajax.reload();
                }
            } else {
                alert_float('danger', response.message || 'Failed to update status');
            }
        }, 'json').fail(function() {
            alert_float('danger', 'An error occurred while updating the status');
        });
    },

    /**
     * Quick approve
     */
    quickApprove: function(element) {
        var id = element.data('id');
        var type = element.data('type');
        
        var url = admin_url + 'ella_contractors/' + type + '/approve/' + id;
        
        $.post(url, function(response) {
            if (response.success) {
                alert_float('success', ucfirst(type) + ' approved successfully');
                
                // Update the element
                element.removeClass('btn-warning').addClass('btn-success');
                element.html('<i class="fa fa-check"></i> Approved');
                element.prop('disabled', true);
            } else {
                alert_float('danger', response.message || 'Failed to approve ' + type);
            }
        }, 'json').fail(function() {
            alert_float('danger', 'An error occurred while approving the ' + type);
        });
    },

    /**
     * Auto-save functionality
     */
    autoSave: function(element) {
        var form = element.closest('form');
        var url = form.attr('action') + '/auto_save';
        
        $.post(url, form.serialize(), function(response) {
            if (response.success) {
                // Show subtle indication of save
                element.closest('.form-group').addClass('auto-saved');
                setTimeout(function() {
                    element.closest('.form-group').removeClass('auto-saved');
                }, 2000);
            }
        }, 'json');
    },

    /**
     * Handle file upload
     */
    handleFileUpload: function(element) {
        var file = element[0].files[0];
        var maxSize = 5 * 1024 * 1024; // 5MB
        
        if (file.size > maxSize) {
            alert_float('danger', 'File size must be less than 5MB');
            element.val('');
            return;
        }
        
        var allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        var fileExtension = file.name.split('.').pop().toLowerCase();
        
        if (allowedTypes.indexOf(fileExtension) === -1) {
            alert_float('danger', 'File type not allowed. Allowed types: ' + allowedTypes.join(', '));
            element.val('');
            return;
        }
        
        // Show upload progress if needed
        var progressBar = element.siblings('.progress');
        if (progressBar.length) {
            progressBar.show();
            // Simulate progress (replace with actual upload progress)
            var progress = 0;
            var interval = setInterval(function() {
                progress += 10;
                progressBar.find('.progress-bar').css('width', progress + '%');
                if (progress >= 100) {
                    clearInterval(interval);
                    progressBar.hide();
                }
            }, 100);
        }
    },

    /**
     * Utility function to capitalize first letter
     */
    ucfirst: function(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    },

    /**
     * Show loading overlay
     */
    showLoading: function(container) {
        var overlay = $('<div class="loading-overlay"><div class="spinner"></div></div>');
        container.css('position', 'relative').append(overlay);
    },

    /**
     * Hide loading overlay
     */
    hideLoading: function(container) {
        container.find('.loading-overlay').remove();
    },

    /**
     * Format currency
     */
    formatCurrency: function(amount) {
        return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    },

    /**
     * Format date
     */
    formatDate: function(date) {
        return moment(date).format('MMM D, YYYY');
    }
};

// Initialize when document is ready
$(document).ready(function() {
    EllaContractors.init();
});

// Export for global access
window.EllaContractors = EllaContractors;
