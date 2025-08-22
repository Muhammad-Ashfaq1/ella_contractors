/**
 * Ella Contractors Module - JavaScript Functions
 * Handles all interactive functionality for the module
 */

(function($) {
    'use strict';

    // Module namespace
    window.EllaContractors = {
        
        // Initialize module
    init: function() {
            this.initUploadForm();
            this.initMediaGallery();
            this.initCheckboxBehavior();
            this.initFilePreview();
        this.initFormValidation();
            this.initHoverEffects();
        },

        // Upload form functionality
        initUploadForm: function() {
            var self = this;
            var contractId = $('input[name="contract_id"]').val() || null;
            var isContractSpecific = contractId !== null;

            // Dynamic checkbox behavior
            $('#is_default_checkbox').on('change', function() {
                var isChecked = $(this).is(':checked');
                self.updateUploadButton(isChecked, isContractSpecific);
                self.toggleInfoPanel(isChecked);
            });

            // Initialize checkbox state on page load
            if ($('#is_default_checkbox').is(':checked')) {
                $('#default_info_panel').show();
                self.updateUploadButton(true, isContractSpecific);
            } else {
                self.updateUploadButton(false, isContractSpecific);
            }
        },

        // Update upload button text based on checkbox state
        updateUploadButton: function(isChecked, isContractSpecific) {
            var $submitBtn = $('#upload-form button[type="submit"]');
            
            if (isChecked) {
                if (isContractSpecific) {
                    $submitBtn.html('<i class="fa fa-upload"></i> Upload for Contract & All Contracts');
                } else {
                    $submitBtn.html('<i class="fa fa-upload"></i> Upload as Default Media');
                }
            } else {
                if (isContractSpecific) {
                    $submitBtn.html('<i class="fa fa-upload"></i> Upload for This Contract Only');
                } else {
                    $submitBtn.html('<i class="fa fa-upload"></i> Upload File');
                }
            }
        },

        // Toggle info panel visibility
        toggleInfoPanel: function(show) {
            if (show) {
                $('#default_info_panel').slideDown(300);
            } else {
                $('#default_info_panel').slideUp(300);
            }
        },

        // Media gallery functionality
        initMediaGallery: function() {
            // Delete media confirmation
            $('.delete-media-btn').on('click', function(e) {
                if (!confirm('Are you sure you want to delete this media file? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }
            });

            // Media item hover effects
            $('.media-item').hover(
                function() {
                    $(this).addClass('hover-lift');
                },
                function() {
                    $(this).removeClass('hover-lift');
                }
            );
        },

        // Checkbox behavior
        initCheckboxBehavior: function() {
            // Add hover effects for better UX
            $('.checkbox-default-media').hover(
                function() {
                    $(this).addClass('highlight-checkbox');
                },
                function() {
                    $(this).removeClass('highlight-checkbox');
                }
            );
        },

        // File preview functionality
        initFilePreview: function() {
            $('#media_file').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
                    var fileName = file.name;
                    var fileExt = fileName.split('.').pop().toLowerCase();
                    
                    if (fileSize > 50) {
                        alert('File size exceeds 50MB limit. Please choose a smaller file.');
                        $(this).val('');
                        return;
                    }
                    
                    // Remove previous file info
                    $('.file-preview-alert').remove();
                    
                    // Show file info with smart suggestions
                    var fileInfo = self.createFilePreview(fileName, fileSize, fileExt);
                    $('.help-block').after(fileInfo);
            }
        });
    },

        // Create file preview with smart suggestions
        createFilePreview: function(fileName, fileSize, fileExt) {
            var fileInfo = '<div class="alert alert-success file-preview-alert"><strong>Selected File:</strong> ' + fileName + ' (' + fileSize + ' MB)';
            
            // Smart default checkbox suggestions
            var defaultSuggestions = {
                'pdf': ['contract', 'agreement', 'policy', 'terms', 'brochure', 'manual'],
                'doc': ['template', 'form', 'policy', 'guide', 'manual'],
                'docx': ['template', 'form', 'policy', 'guide', 'manual'],
                'xls': ['template', 'form', 'pricing', 'calculator'],
                'xlsx': ['template', 'form', 'pricing', 'calculator'],
                'ppt': ['presentation', 'brochure', 'overview', 'intro'],
                'pptx': ['presentation', 'brochure', 'overview', 'intro']
            };
            
            if (defaultSuggestions[fileExt]) {
                var isLikelyDefault = defaultSuggestions[fileExt].some(function(keyword) {
                    return fileName.toLowerCase().includes(keyword);
                });
                
                if (isLikelyDefault && !$('#is_default_checkbox').is(':checked')) {
                    fileInfo += '<br><small class="text-info"><i class="fa fa-lightbulb-o"></i> <strong>Suggestion:</strong> This looks like it might be useful for all contracts. Consider checking "Use as default media".</small>';
                }
            }
            
            fileInfo += '</div>';
            return fileInfo;
        },

        // Form validation
        initFormValidation: function() {
            $('#upload-form').on('submit', function(e) {
                var file = $('#media_file')[0].files[0];
                if (!file) {
                    alert('Please select a file to upload.');
                    e.preventDefault();
                    return false;
                }
                
                // Show loading state
                var $submitBtn = $(this).find('button[type="submit"]');
                var originalText = $submitBtn.html();
                $submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Uploading...').prop('disabled', true);
                
                // Re-enable button after a timeout (in case of errors)
                setTimeout(function() {
                    $submitBtn.html(originalText).prop('disabled', false);
                }, 30000);
        });
    },

        // Hover effects
        initHoverEffects: function() {
            // Add hover effects for better UX
            $('.checkbox-default-media').hover(
                function() {
                    $(this).addClass('highlight-checkbox');
                },
                function() {
                    $(this).removeClass('highlight-checkbox');
                }
            );
        },

        // Utility functions
        utils: {
            // Format file size
            formatFileSize: function(bytes) {
                if (bytes === 0) return '0 Bytes';
                var k = 1024;
                var sizes = ['Bytes', 'KB', 'MB', 'GB'];
                var i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            },

            // Show notification
            showNotification: function(message, type) {
                type = type || 'info';
                var alertClass = 'alert-' + type;
                var icon = this.getIconForType(type);
                
                var notification = '<div class="alert ' + alertClass + ' alert-dismissible fade in" role="alert">' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span></button>' +
                    icon + ' ' + message +
                    '</div>';
                
                $('.content').prepend(notification);
                
                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    $('.alert-dismissible').fadeOut();
                }, 5000);
            },

            // Get icon for notification type
            getIconForType: function(type) {
                var icons = {
                    'success': '<i class="fa fa-check-circle"></i>',
                    'info': '<i class="fa fa-info-circle"></i>',
                    'warning': '<i class="fa fa-exclamation-triangle"></i>',
                    'danger': '<i class="fa fa-times-circle"></i>'
                };
                return icons[type] || icons['info'];
            },

            // Confirm action
            confirmAction: function(message, callback) {
                if (confirm(message)) {
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            }
    }
};

// Initialize when document is ready
$(document).ready(function() {
        window.EllaContractors.init();
});

})(jQuery);
