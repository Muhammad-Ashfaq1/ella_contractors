/**
 * EllaContractors Service Items Tutorial System
 * 
 * Provides step-by-step guided tours for the Service Items page
 * Supports "Don't show again" functionality with persistence
 * Fully responsive with dynamic positioning across all screen sizes
 * 
 * @version 1.0.0
 * @author EllaContractors Team
 */

(function($) {
    'use strict';

    /**
     * Tutorial Manager Class
     */
    var ServiceItemsTutorial = {
        // Configuration
        config: {
            storageKey: 'ella_contractors_service_items_tutorial_completed',
            storageKeyDismissed: 'ella_contractors_service_items_tutorial_dismissed',
            tutorialId: 'service_items_tutorial',
            currentStep: 0,
            steps: [],
            shouldShow: true // Default to showing tutorial
        },

        // State
        state: {
            isActive: false,
            currentStepIndex: 0,
            overlay: null,
            tooltip: null,
            targetElement: null,
            resizeHandler: null
        },

        /**
         * Initialize tutorial system
         */
        init: function() {
            var self = this;
            
            // Set up modal event handlers
            $(document).off('show.bs.modal.service_items_tutorial hidden.bs.modal.service_items_tutorial');
            $(document).on('show.bs.modal.service_items_tutorial', function() {
                if (self.state.overlay) {
                    self.state.overlay.css('display', 'none');
                }
                if (self.state.tooltip) {
                    self.state.tooltip.css('display', 'none');
                }
            });

            $(document).on('hidden.bs.modal.service_items_tutorial', function() {
                if (self.state.isActive) {
                    if (self.state.overlay) {
                        self.state.overlay.css('display', 'block');
                    }
                    if (self.state.tooltip) {
                        self.state.tooltip.css('display', 'block');
                    }
                }
            });

            // Also listen to standard Bootstrap modal events
            $(document).on('show.bs.modal', function() {
                if (self.state.overlay) {
                    self.state.overlay.css('display', 'none');
                }
                if (self.state.tooltip) {
                    self.state.tooltip.css('display', 'none');
                }
            });

            $(document).on('hidden.bs.modal', function() {
                if (self.state.isActive) {
                    if (self.state.overlay) {
                        self.state.overlay.css('display', 'block');
                    }
                    if (self.state.tooltip) {
                        self.state.tooltip.css('display', 'block');
                    }
                }
            });

            // Always load tutorial steps (needed for restart functionality)
            this.loadTutorialSteps();
            
            // Check if tutorial should be shown
            if (this.shouldShowTutorial()) {
                // Wait for page to be fully loaded
                $(document).ready(function() {
                    // Small delay to ensure all elements are rendered
                    setTimeout(function() {
                        ServiceItemsTutorial.start();
                    }, 1000);
                });
            }
        },

        /**
         * Check if tutorial should be shown
         * @returns {boolean}
         */
        shouldShowTutorial: function() {
            // Check localStorage first (instant client-side check)
            var dismissed = localStorage.getItem(this.config.storageKeyDismissed);
            if (dismissed === 'true') {
                return false;
            }

            // Check server-side preference (database for cross-device persistence)
            var self = this;
            $.ajax({
                url: admin_url + 'ella_contractors/appointments/check_service_items_tutorial_status',
                type: 'GET',
                async: false, // Synchronous for initialization
                dataType: 'json',
                success: function(response) {
                    if (response && response.show_tutorial === false) {
                        self.config.shouldShow = false;
                        // Sync to localStorage for future instant checks
                        localStorage.setItem(self.config.storageKeyDismissed, 'true');
                    }
                },
                error: function() {
                    // On error, default to showing tutorial
                    self.config.shouldShow = true;
                }
            });

            return this.config.shouldShow !== false;
        },

        /**
         * Load tutorial steps configuration
         */
        loadTutorialSteps: function() {
            this.config.steps = [
                {
                    id: 'welcome',
                    title: 'Welcome to Service Items',
                    content: 'Welcome to the Service Items module! This quick tour will help you understand how to create, manage, and organize service items and categories for your estimates and invoices.',
                    target: null,
                    position: 'center',
                    showNext: true,
                    showBack: false,
                    showSkip: true
                },
                {
                    id: 'new_service_item_button',
                    title: 'Create New Service Item',
                    content: 'Click the "New Service Item" button to create a new service item. You can set the name, description, rate, unit of measurement, tax settings, and assign it to a category. Service items are used when creating estimates and invoices.',
                    target: '.panel-body._buttons .btn-info[data-target="#sales_item_modal"]',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true
                },
                {
                    id: 'categories_button',
                    title: 'Manage Categories',
                    content: 'Click the "Categories" button to organize your service items into groups. Categories help you organize and filter service items, making it easier to find what you need when creating estimates. You can create, edit, and delete categories here.',
                    target: '.panel-body._buttons .btn-info[data-target="#groups"]',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true
                },
                {
                    id: 'import_button',
                    title: 'Import Service Items',
                    content: 'Use the "Import Service Items" button to bulk import service items from a CSV file. This is useful when you have many items to add at once. Make sure your CSV file follows the correct format with columns for name, description, rate, unit, and category.',
                    target: '.panel-body._buttons a[href*="invoice_items/import"]',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true,
                    optional: true
                },
                {
                    id: 'service_items_table',
                    title: 'Service Items Table',
                    content: 'This table displays all your service items with their categories, names, and total rates. You can sort by any column, search for specific items, and use bulk actions to manage multiple items at once. Click on any row to edit the service item.',
                    target: '.table-invoice-items',
                    position: 'top',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true,
                    optional: true
                },
                {
                    id: 'table_actions',
                    title: 'Service Item Actions',
                    content: 'Each service item row has action buttons to view, edit, or delete. You can also use the bulk actions menu to perform operations on multiple selected items. Service items can be edited at any time to update rates, descriptions, or categories.',
                    target: '.table-invoice-items',
                    position: 'right',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true,
                    optional: true
                },
                {
                    id: 'completion',
                    title: 'Tutorial Complete!',
                    content: 'You\'re all set! You can now create and manage service items efficiently. Remember: well-organized service items with accurate rates and descriptions help you create professional estimates and invoices quickly.',
                    target: null,
                    position: 'center',
                    showNext: true,
                    showBack: true,
                    showSkip: false,
                    isLast: true
                }
            ];
        },

        /**
         * Start the tutorial
         */
        start: function() {
            if (this.state.isActive) {
                return; // Tutorial already running
            }

            if (this.config.steps.length === 0) {
                console.warn('ServiceItemsTutorial: No steps configured');
                return;
            }

            this.state.isActive = true;
            this.state.currentStepIndex = 0;
            this.showStep(0);
        },

        /**
         * Show a specific step
         * @param {number} stepIndex
         */
        showStep: function(stepIndex) {
            if (stepIndex < 0 || stepIndex >= this.config.steps.length) {
                this.complete();
                return;
            }

            var step = this.config.steps[stepIndex];
            this.state.currentStepIndex = stepIndex;

            // Mark step as last
            step.isLast = (stepIndex === this.config.steps.length - 1);

            // Wait for element if needed
            if (step.waitForElement && step.target) {
                var element = $(step.target);
                if (element.length === 0) {
                    // Wait for element to appear
                    var attempts = 0;
                    var maxAttempts = 2; // 200ms max wait (2 attempts * 100ms)
                    var self = this;
                    var checkElement = setInterval(function() {
                        attempts++;
                        var el = $(step.target);
                        if (el.length > 0) {
                            clearInterval(checkElement);
                            // Element found, show the step
                            self.showStep(stepIndex);
                        } else if (attempts >= maxAttempts) {
                            clearInterval(checkElement);
                            // Element not found after timeout - show step anyway (centered)
                            // This ensures all steps are shown even if elements are missing
                            self.showStepContent(step, stepIndex, false);
                        }
                    }, 100);
                    return;
                }
            }

            // Element exists or no target - show step normally
            this.showStepContent(step, stepIndex, true);
        },

        /**
         * Show step content (tooltip and overlay)
         * @param {object} step
         * @param {number} stepIndex
         * @param {boolean} elementExists - Whether the target element exists
         */
        showStepContent: function(step, stepIndex, elementExists) {
            // Remove previous overlay and tooltip
            this.cleanup();

            // Create overlay
            this.createOverlay();

            // Create and show tooltip
            this.createTooltip(step, stepIndex);

            // Highlight target element if specified and element exists
            if (step.target && step.highlight && elementExists) {
                this.highlightElement(step.target);
            } else if (step.target && !elementExists) {
                // If element doesn't exist, center the tooltip
                step.position = 'center';
                this.centerTooltip();
            }
        },

        /**
         * Create overlay
         */
        createOverlay: function() {
            this.state.overlay = $('<div class="tutorial-overlay"></div>');
            $('body').append(this.state.overlay);
        },

        /**
         * Create tooltip
         * @param {object} step
         * @param {number} stepIndex
         */
        createTooltip: function(step, stepIndex) {
            var progress = 'Step ' + (stepIndex + 1) + ' of ' + this.config.steps.length;
            
            var tooltipHtml = '<div class="tutorial-tooltip' + (step.position === 'center' ? ' tutorial-centered' : '') + '">';
            
            // Header
            tooltipHtml += '<div class="tutorial-tooltip-header">';
            tooltipHtml += '<h3 class="tutorial-tooltip-title"><i class="fa fa-list-alt"></i> ' + this.escapeHtml(step.title) + '</h3>';
            tooltipHtml += '<button type="button" class="tutorial-close" aria-label="Close">&times;</button>';
            tooltipHtml += '</div>';

            // Progress
            tooltipHtml += '<div class="tutorial-progress">' + progress + '</div>';

            // Content
            tooltipHtml += '<div class="tutorial-tooltip-content">';
            tooltipHtml += '<p>' + this.escapeHtml(step.content) + '</p>';
            tooltipHtml += '</div>';

            // Footer
            tooltipHtml += '<div class="tutorial-tooltip-footer">';
            tooltipHtml += '<div class="tutorial-tooltip-actions">';
            
            // Back button
            if (step.showBack) {
                tooltipHtml += '<button type="button" class="btn btn-default tutorial-btn-back">Back</button>';
            }
            
            // Skip button
            if (step.showSkip) {
                tooltipHtml += '<a href="#" class="tutorial-btn-skip">Skip Tutorial</a>';
            }

            // Next/Finish button
            if (step.showNext) {
                var nextText = step.isLast ? 'Got it!' : 'Next';
                tooltipHtml += '<button type="button" class="btn btn-primary tutorial-btn-next">' + nextText + '</button>';
            }

            tooltipHtml += '</div>';

            // Don't show again checkbox (only on last step)
            if (step.isLast) {
                tooltipHtml += '<div class="tutorial-dont-show">';
                tooltipHtml += '<label>';
                tooltipHtml += '<input type="checkbox" id="tutorial-dont-show-again" />';
                tooltipHtml += ' Don\'t show this tutorial again';
                tooltipHtml += '</label>';
                tooltipHtml += '</div>';
            }

            tooltipHtml += '</div>';
            tooltipHtml += '</div>';

            this.state.tooltip = $(tooltipHtml);
            $('body').append(this.state.tooltip);

            // Use requestAnimationFrame to ensure tooltip is rendered before positioning
            var self = this;
            requestAnimationFrame(function() {
                // Double RAF to ensure layout is complete
                requestAnimationFrame(function() {
                    // Position tooltip after rendering
                    self.positionTooltip(step);
                    
                    // Bind events
                    self.bindTooltipEvents(step, stepIndex);
                });
            });
        },

        /**
         * Ensure tooltip dimensions are calculated accurately
         * @param {jQuery} tooltip
         * @returns {object} Dimensions object with width and height
         */
        ensureTooltipDimensions: function(tooltip) {
            // Force tooltip to be measured by positioning it off-screen with visibility
            // Temporarily make it visible off-screen to get accurate measurements
            tooltip.css({
                position: 'fixed',
                top: '-9999px',
                left: '-9999px',
                visibility: 'visible',
                opacity: 0,
                display: 'block'
            });
            
            // Force browser reflow to calculate dimensions
            tooltip[0].offsetHeight;
            
            var width = tooltip.outerWidth();
            var height = tooltip.outerHeight();
            
            return { width: width, height: height };
        },

        /**
         * Position tooltip relative to target element
         * @param {object} step
         */
        positionTooltip: function(step) {
            if (!this.state.tooltip) {
                return;
            }

            var tooltip = this.state.tooltip;
            var position = step.position || 'bottom';
            var self = this;

            // Remove all arrow classes first
            tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right tutorial-arrow-top-right');

            // Ensure tooltip dimensions are calculated first
            var tooltipDims = this.ensureTooltipDimensions(tooltip);
            var tooltipWidth = tooltipDims.width;
            var tooltipHeight = tooltipDims.height;

            // CUSTOM POSITIONING: Responsive override for specific steps
            // Get viewport dimensions for responsive positioning
            var viewportWidth = $(window).width();
            var viewportHeight = $(window).height();

            // Custom positioning for 'new_service_item_button' step
            if (step.id === 'new_service_item_button') {
                tooltip.addClass('tutorial-arrow-top'); // Arrow at top pointing up to button
                
                var positions;
                if (viewportWidth >= 1920) {
                    // For screens 1920px and above, use dynamic positioning relative to target
                    var target = $(step.target);
                    if (target.length > 0) {
                        var targetOffset = target.offset();
                        var targetWidth = target.outerWidth();
                        var targetHeight = target.outerHeight();
                        var scrollTop = $(window).scrollTop();
                        var spacing = 20;
                        
                        var viewportTop = targetOffset.top - scrollTop;
                        var viewportLeft = targetOffset.left - $(window).scrollLeft();
                        
                        // Position below target (arrow-top points up)
                        var topPos = viewportTop + targetHeight + spacing;
                        var leftPos = viewportLeft + (targetWidth / 2) - (tooltipWidth / 2);
                        
                        // Viewport overflow checks
                        if (leftPos < 10) {
                            leftPos = 10;
                        } else if (leftPos + tooltipWidth > viewportWidth - 10) {
                            leftPos = viewportWidth - tooltipWidth - 10;
                        }
                        
                        if (topPos + tooltipHeight > viewportHeight - 10) {
                            topPos = viewportTop - tooltipHeight - spacing;
                            if (topPos < 10) {
                                topPos = 10;
                            }
                        }
                        
                        positions = { top: topPos + 'px', left: leftPos + 'px' };
                    } else {
                        // Fallback: use fixed position if target not found
                        positions = { top: '140px', left: '110px' };
                    }
                } else if (viewportWidth >= 1800) {
                    positions = { top: '140px', left: '130px' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '140px', left: '150px' };
                } else if (viewportWidth >= 1440) {
                    positions = { top: '140px', left: '170px' };
                } else if (viewportWidth >= 1370) {
                    positions = { top: '140px', left: '190px' };
                } else if (viewportWidth >= 1280) {
                    positions = { top: '140px', left: '210px' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '140px', left: '230px' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '220px', left: '250px' };
                } else {
                    positions = { top: '200px', left: '50%', transform: 'translateX(-50%)' };
                }
                
                var cssProps = {
                    position: 'fixed',
                    top: positions.top,
                    transform: positions.transform || 'none',
                    zIndex: 1041,
                    visibility: 'visible',
                    opacity: 1
                };
                
                if (positions.left) {
                    cssProps.left = positions.left;
                    cssProps.right = 'auto';
                }
                
                tooltip.css(cssProps);
                return;
            }

            // Custom positioning for 'categories_button' step
            if (step.id === 'categories_button') {
                tooltip.addClass('tutorial-arrow-top');
                
                var positions;
                if (viewportWidth >= 1920) {
                    // For screens 1920px and above, use dynamic positioning relative to target
                    var target = $(step.target);
                    if (target.length > 0) {
                        var targetOffset = target.offset();
                        var targetWidth = target.outerWidth();
                        var targetHeight = target.outerHeight();
                        var scrollTop = $(window).scrollTop();
                        var spacing = 20;
                        
                        var viewportTop = targetOffset.top - scrollTop;
                        var viewportLeft = targetOffset.left - $(window).scrollLeft();
                        
                        var topPos = viewportTop + targetHeight + spacing;
                        var leftPos = viewportLeft + (targetWidth / 2) - (tooltipWidth / 2);
                        
                        if (leftPos < 10) {
                            leftPos = 10;
                        } else if (leftPos + tooltipWidth > viewportWidth - 10) {
                            leftPos = viewportWidth - tooltipWidth - 10;
                        }
                        
                        if (topPos + tooltipHeight > viewportHeight - 10) {
                            topPos = viewportTop - tooltipHeight - spacing;
                            if (topPos < 10) {
                                topPos = 10;
                            }
                        }
                        
                        positions = { top: topPos + 'px', left: leftPos + 'px' };
                    } else {
                        // Fallback: use fixed position if target not found
                        positions = { top: '140px', left: '270px' };
                    }
                } else if (viewportWidth >= 1800) {
                    positions = { top: '140px', left: '290px' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '140px', left: '310px' };
                } else if (viewportWidth >= 1440) {
                    positions = { top: '140px', left: '330px' };
                } else if (viewportWidth >= 1380) {
                    positions = { top: '140px', left: '350px' };
                } else if (viewportWidth >= 1280) {
                    positions = { top: '140px', left: '370px' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '140px', left: '390px' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '140px', left: '410px' };
                } else {
                    positions = { top: '200px', left: '50%', transform: 'translateX(-50%)' };
                }
                
                var cssProps = {
                    position: 'fixed',
                    top: positions.top,
                    transform: positions.transform || 'none',
                    zIndex: 1041,
                    visibility: 'visible',
                    opacity: 1
                };
                
                if (positions.left) {
                    cssProps.left = positions.left;
                    cssProps.right = 'auto';
                }
                
                tooltip.css(cssProps);
                return;
            }

            // Custom positioning for 'import_button' step
            if (step.id === 'import_button') {
                tooltip.addClass('tutorial-arrow-top');
                
                var positions;
                if (viewportWidth >= 1920) {
                    // For screens 1920px and above, use dynamic positioning relative to target
                    var target = $(step.target);
                    if (target.length > 0) {
                        var targetOffset = target.offset();
                        var targetWidth = target.outerWidth();
                        var targetHeight = target.outerHeight();
                        var scrollTop = $(window).scrollTop();
                        var spacing = 20;
                        
                        var viewportTop = targetOffset.top - scrollTop;
                        var viewportLeft = targetOffset.left - $(window).scrollLeft();
                        
                        var topPos = viewportTop + targetHeight + spacing;
                        var leftPos = viewportLeft + (targetWidth / 2) - (tooltipWidth / 2);
                        
                        if (leftPos < 10) {
                            leftPos = 10;
                        } else if (leftPos + tooltipWidth > viewportWidth - 10) {
                            leftPos = viewportWidth - tooltipWidth - 10;
                        }
                        
                        if (topPos + tooltipHeight > viewportHeight - 10) {
                            topPos = viewportTop - tooltipHeight - spacing;
                            if (topPos < 10) {
                                topPos = 10;
                            }
                        }
                        
                        positions = { top: topPos + 'px', left: leftPos + 'px' };
                    } else {
                        // Fallback: use fixed position if target not found
                        positions = { top: '140px', left: '640px' };
                    }
                } else if (viewportWidth >= 1800) {
                    positions = { top: '140px', left: '660px' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '140px', left: '680px' };
                } else if (viewportWidth >= 1440) {
                    positions = { top: '140px', left: '700px' };
                } else if (viewportWidth >= 1370) {
                    positions = { top: '140px', left: '720px' };
                } else if (viewportWidth >= 1280) {
                    positions = { top: '140px', left: '740px' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '140px', left: '760px' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '140px', left: '780px' };
                } else {
                    positions = { top: '140px', left: '50%', transform: 'translateX(-50%)' };
                }
                var cssProps = {
                    position: 'fixed',
                    top: positions.top,
                    transform: positions.transform || 'none',
                    zIndex: 1041,
                    visibility: 'visible',
                    opacity: 1
                };
                
                if (positions.left) {
                    cssProps.left = positions.left;
                    cssProps.right = 'auto';
                }
                
                tooltip.css(cssProps);
                return;
            }

            // Custom positioning for 'service_items_table' step
            if (step.id === 'service_items_table') {
                tooltip.addClass('tutorial-arrow-top');
                
                var positions;
                if (viewportWidth >= 1920) {
                    // For screens 1920px and above, use dynamic positioning relative to target
                    var target = $(step.target);
                    if (target.length > 0) {
                        var targetOffset = target.offset();
                        var targetWidth = target.outerWidth();
                        var targetHeight = target.outerHeight();
                        var scrollTop = $(window).scrollTop();
                        var spacing = 20;
                        
                        var viewportTop = targetOffset.top - scrollTop;
                        var viewportLeft = targetOffset.left - $(window).scrollLeft();
                        
                        // Position below target (arrow-top points up)
                        var topPos = viewportTop + targetHeight + spacing;
                        var leftPos = viewportLeft + (targetWidth / 2) - (tooltipWidth / 2);
                        
                        if (leftPos < 10) {
                            leftPos = 10;
                        } else if (leftPos + tooltipWidth > viewportWidth - 10) {
                            leftPos = viewportWidth - tooltipWidth - 10;
                        }
                        
                        if (topPos + tooltipHeight > viewportHeight - 10) {
                            topPos = viewportTop - tooltipHeight - spacing;
                            if (topPos < 10) {
                                topPos = 10;
                            }
                        }
                        
                        positions = { top: topPos + 'px', left: leftPos + 'px' };
                    } else {
                        // Fallback: use percentage if target not found
                        positions = { top: '30%', left: '40%' };
                    }
                } else if (viewportWidth >= 1800) {
                    positions = { top: '33%', left: '40%' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '36%', left: '40%' };
                } else if (viewportWidth >= 1440) {
                    positions = { top: '39%', left: '40%' };
                } else if (viewportWidth >= 1370) {
                    positions = { top: '42%', left: '40%' };
                } else if (viewportWidth >= 1280) {
                    positions = { top: '45%', left: '40%' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '48%', left: '40%' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '51%', left: '40%' };
                } else {
                    positions = { top: '54%', left: '40%' };
                }
                var cssProps = {
                    position: 'fixed',
                    top: positions.top,
                    transform: positions.transform || 'none',
                    zIndex: 1041,
                    visibility: 'visible',
                    opacity: 1
                };
                
                if (positions.left) {
                    cssProps.left = positions.left;
                    cssProps.right = 'auto';
                }
                
                tooltip.css(cssProps);
                return;
            }

            // Custom positioning for 'table_actions' step
            if (step.id === 'table_actions') {
                tooltip.addClass('tutorial-arrow-left');
                
                var positions;
                if (viewportWidth > 1920) {
                    // For screens above 1920px, use dynamic positioning relative to target
                    var target = $(step.target);
                    if (target.length > 0) {
                        var targetOffset = target.offset();
                        var targetWidth = target.outerWidth();
                        var targetHeight = target.outerHeight();
                        var scrollTop = $(window).scrollTop();
                        var scrollLeft = $(window).scrollLeft();
                        var spacing = 20;
                        
                        var viewportTop = targetOffset.top - scrollTop;
                        var viewportLeft = targetOffset.left - scrollLeft;
                        
                        var topPos = viewportTop + (targetHeight / 2) - (tooltipHeight / 2);
                        var leftPos = viewportLeft + targetWidth + spacing;
                        
                        if (leftPos + tooltipWidth > viewportWidth - 10) {
                            leftPos = viewportLeft - tooltipWidth - spacing;
                            if (leftPos < 10) {
                                leftPos = 10;
                            }
                        }
                        
                        if (topPos < 10) {
                            topPos = 10;
                        } else if (topPos + tooltipHeight > viewportHeight - 10) {
                            topPos = viewportHeight - tooltipHeight - 10;
                        }
                        
                        positions = { top: topPos + 'px', left: leftPos + 'px' };
                    } else {
                        // Fallback: use 250px if target not found (same as 1920px)
                        positions = { top: '200px', left: '360px' };
                    }
                } else if (viewportWidth >= 1920) {
                    // Exactly 1920px: use 250px as specified
                    positions = { top: '200px', left: '360px' };
                } else if (viewportWidth >= 1800) {
                    // Proportional: (1800/1920) * 250 = 234px ≈ 230px
                    positions = { top: '200px', left: '340px' };
                } else if (viewportWidth >= 1600) {
                    // Proportional: (1600/1920) * 250 = 208px ≈ 210px
                    positions = { top: '200px', left: '340px' };
                } else if (viewportWidth >= 1440) {
                    // Proportional: (1440/1920) * 250 = 187px ≈ 190px
                    positions = { top: '200px', left: '340px' };
                } else if (viewportWidth >= 1370) {
                    // Proportional: (1370/1920) * 250 = 178px ≈ 180px
                    positions = { top: '200px', left: '340px' };
                } else if (viewportWidth >= 1280) {
                    // Proportional: (1280/1920) * 250 = 166px ≈ 170px
                    positions = { top: '200px', left: '340px' };
                } else if (viewportWidth >= 1024) {
                    // Proportional: (1024/1920) * 250 = 133px ≈ 130px
                    positions = { top: '200px', left: '340px' };
                } else if (viewportWidth >= 768) {
                    // Proportional: (768/1920) * 250 = 100px
                    positions = { top: '200px', left: '340px' };
                } else {
                    positions = { top: '300px', left: '50%', transform: 'translateX(-50%)' };
                }
                
                var cssProps = {
                    position: 'fixed',
                    top: positions.top,
                    transform: positions.transform || 'none',
                    zIndex: 1041,
                    visibility: 'visible',
                    opacity: 1
                };
                
                if (positions.left) {
                    cssProps.left = positions.left;
                    cssProps.right = 'auto';
                }
                
                tooltip.css(cssProps);
                return;
            }

            // Default positioning for steps without custom positioning
            if (!step.target || step.position === 'center') {
                this.centerTooltip(tooltipWidth, tooltipHeight);
                return;
            }

            var $target = $(step.target);
            if ($target.length === 0) {
                this.centerTooltip(tooltipWidth, tooltipHeight);
                return;
            }

            var targetOffset = $target.offset();
            var targetWidth = $target.outerWidth();
            var targetHeight = $target.outerHeight();
            var windowWidth = $(window).width();
            var windowHeight = $(window).height();
            var scrollTop = $(window).scrollTop();
            var scrollLeft = $(window).scrollLeft();
            var viewportTop = targetOffset.top - scrollTop;
            var viewportLeft = targetOffset.left - scrollLeft;

            var position = { top: 0, left: 0 };
            var arrowClass = '';
            var spacing = 15;

            switch (step.position) {
                case 'top':
                    position.top = viewportTop - tooltipHeight - spacing;
                    position.left = viewportLeft + (targetWidth / 2) - (tooltipWidth / 2);
                    arrowClass = 'tutorial-arrow-bottom';
                    if (position.top < 10) {
                        position.top = viewportTop + targetHeight + spacing;
                        arrowClass = 'tutorial-arrow-top';
                    }
                    break;
                case 'bottom':
                    position.top = viewportTop + targetHeight + spacing;
                    position.left = viewportLeft + (targetWidth / 2) - (tooltipWidth / 2);
                    arrowClass = 'tutorial-arrow-top';
                    if (position.top + tooltipHeight > windowHeight - 10) {
                        position.top = viewportTop - tooltipHeight - spacing;
                        arrowClass = 'tutorial-arrow-bottom';
                        if (position.top < 10) {
                            position.top = 10;
                        }
                    }
                    break;
                case 'left':
                    position.top = viewportTop + (targetHeight / 2) - (tooltipHeight / 2);
                    position.left = viewportLeft - tooltipWidth - spacing;
                    arrowClass = 'tutorial-arrow-right';
                    if (position.left < 10) {
                        position.left = viewportLeft + targetWidth + spacing;
                        arrowClass = 'tutorial-arrow-left';
                    }
                    break;
                case 'right':
                    position.top = viewportTop + (targetHeight / 2) - (tooltipHeight / 2);
                    position.left = viewportLeft + targetWidth + spacing;
                    arrowClass = 'tutorial-arrow-left';
                    if (position.left + tooltipWidth > windowWidth - 10) {
                        position.left = viewportLeft - tooltipWidth - spacing;
                        arrowClass = 'tutorial-arrow-right';
                        if (position.left < 10) {
                            position.left = 10;
                        }
                    }
                    break;
            }

            // Adjust for viewport boundaries
            if (position.left < 10) {
                position.left = 10;
            } else if (position.left + tooltipWidth > windowWidth - 10) {
                position.left = windowWidth - tooltipWidth - 10;
            }

            if (position.top < 10) {
                position.top = 10;
            } else if (position.top + tooltipHeight > windowHeight - 10) {
                position.top = windowHeight - tooltipHeight - 10;
            }

            // Apply position
            tooltip.css({
                position: 'fixed',
                top: position.top + 'px',
                left: position.left + 'px',
                visibility: 'visible',
                opacity: 1
            });

            // Add arrow class
            if (arrowClass) {
                tooltip.addClass(arrowClass);
            }

            // Store target element for highlighting
            this.state.targetElement = $target;
        },

        /**
         * Center tooltip on screen
         * @param {number} tooltipWidth - Optional pre-calculated width
         * @param {number} tooltipHeight - Optional pre-calculated height
         */
        centerTooltip: function(tooltipWidth, tooltipHeight) {
            var windowWidth = $(window).width();
            var windowHeight = $(window).height();
            
            if (typeof tooltipWidth === 'undefined' || typeof tooltipHeight === 'undefined') {
                tooltipWidth = this.state.tooltip.outerWidth();
                tooltipHeight = this.state.tooltip.outerHeight();
            }

            this.state.tooltip.css({
                position: 'fixed',
                top: (windowHeight / 2 - tooltipHeight / 2) + 'px',
                left: (windowWidth / 2 - tooltipWidth / 2) + 'px',
                visibility: 'visible',
                opacity: 1,
                zIndex: 1041
            });
        },

        /**
         * Highlight target element
         * @param {string} selector
         */
        highlightElement: function(selector) {
            var $element = $(selector);
            if ($element.length > 0) {
                $element.addClass('tutorial-highlight');
            }
        },

        /**
         * Bind tooltip events
         * @param {object} step
         * @param {number} stepIndex
         */
        bindTooltipEvents: function(step, stepIndex) {
            var self = this;

            // Close button
            this.state.tooltip.find('.tutorial-close').on('click', function() {
                self.complete();
            });

            // Back button
            this.state.tooltip.find('.tutorial-btn-back').on('click', function() {
                self.previous();
            });

            // Next button
            this.state.tooltip.find('.tutorial-btn-next').on('click', function() {
                self.next();
            });

            // Skip button
            this.state.tooltip.find('.tutorial-btn-skip').on('click', function(e) {
                e.preventDefault();
                self.complete();
            });

            // Don't show again checkbox
            if (step.isLast) {
                this.state.tooltip.find('#tutorial-dont-show-again').on('change', function() {
                    var checked = $(this).is(':checked');
                    if (checked) {
                        self.saveDismissPreference(true);
                    }
                });
            }

            // Handle window resize
            if (this.state.resizeHandler) {
                $(window).off('resize', this.state.resizeHandler);
            }
            this.state.resizeHandler = function() {
                self.positionTooltip(step);
            };
            $(window).on('resize', this.state.resizeHandler);
        },

        /**
         * Go to next step
         */
        next: function() {
            var dontShow = false;
            if (this.state.tooltip) {
                var checkbox = this.state.tooltip.find('#tutorial-dont-show-again');
                if (checkbox.length && checkbox.is(':checked')) {
                    dontShow = true;
                }
            }

            if (dontShow) {
                this.saveDismissPreference(true);
            }

            this.showStep(this.state.currentStepIndex + 1);
        },

        /**
         * Go to previous step
         */
        previous: function() {
            this.showStep(this.state.currentStepIndex - 1);
        },

        /**
         * Complete tutorial
         */
        complete: function() {
            var dontShow = false;
            if (this.state.tooltip) {
                var checkbox = this.state.tooltip.find('#tutorial-dont-show-again');
                if (checkbox.length && checkbox.is(':checked')) {
                    dontShow = true;
                }
            }

            if (dontShow) {
                this.saveDismissPreference(true);
            } else {
                // Mark as completed (but not dismissed)
                localStorage.setItem(this.config.storageKey, 'true');
            }

            this.cleanup();
            this.state.isActive = false;
        },

        /**
         * Save dismiss preference
         * @param {boolean} dismissed
         */
        saveDismissPreference: function(dismissed) {
            localStorage.setItem(this.config.storageKeyDismissed, dismissed ? 'true' : 'false');
            
            // Save to server
            $.ajax({
                url: admin_url + 'ella_contractors/appointments/save_service_items_tutorial_preference',
                type: 'POST',
                data: {
                    dismissed: dismissed ? 1 : 0
                },
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        console.log('Service items tutorial preference saved');
                    }
                },
                error: function() {
                    console.warn('Failed to save service items tutorial preference');
                }
            });
        },

        /**
         * Cleanup tutorial elements
         */
        cleanup: function() {
            // Remove highlight
            if (this.state.targetElement) {
                this.state.targetElement.removeClass('tutorial-highlight');
                this.state.targetElement = null;
            }

            // Remove overlay
            if (this.state.overlay) {
                this.state.overlay.remove();
                this.state.overlay = null;
            }

            // Remove tooltip
            if (this.state.tooltip) {
                this.state.tooltip.remove();
                this.state.tooltip = null;
            }

            // Remove resize handler
            if (this.state.resizeHandler) {
                $(window).off('resize', this.state.resizeHandler);
                this.state.resizeHandler = null;
            }
        },

        /**
         * Escape HTML to prevent XSS
         * @param {string} text
         * @returns {string}
         */
        escapeHtml: function(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        },

        /**
         * Restart tutorial (for testing/admin use)
         */
        restart: function() {
            localStorage.removeItem(this.config.storageKey);
            localStorage.removeItem(this.config.storageKeyDismissed);
            this.config.shouldShow = true;
            this.state.currentStepIndex = 0;
            
            // Ensure steps are loaded before starting
            if (this.config.steps.length === 0) {
                this.loadTutorialSteps();
            }
            
            // Clean up any existing tutorial state
            this.cleanup();
            this.state.isActive = false;
            
            // Start the tutorial
            this.start();
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        // Only initialize if we're on the service items page
        if ($('.table-invoice-items').length > 0 || window.location.href.indexOf('invoice_items') > -1) {
            // Check if service_items parameter exists or we're coming from ella_contractors menu
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('service_items') === 'true' || $('.sub-menu-item-ella_contractors_line_items').hasClass('active')) {
                ServiceItemsTutorial.init();
            }
        }
    });

    // Expose to global scope for debugging/admin use
    window.ServiceItemsTutorial = ServiceItemsTutorial;

})(jQuery);


