/**
 * EllaContractors Estimate Tutorial System
 * 
 * Provides step-by-step guided tours for the Estimates tab in appointment view
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
    var EstimateTutorial = {
        // Configuration
        config: {
            tutorialId: 'estimates_tutorial',
            currentStep: 0,
            steps: [],
            shouldShow: true // Default to showing tutorial (server-side controlled)
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
            $(document).off('show.bs.modal.estimate_tutorial hidden.bs.modal.estimate_tutorial');
            $(document).on('show.bs.modal.estimate_tutorial', function() {
                if (self.state.overlay) {
                    self.state.overlay.css('display', 'none');
                }
                if (self.state.tooltip) {
                    self.state.tooltip.css('display', 'none');
                }
            });

            $(document).on('hidden.bs.modal.estimate_tutorial', function() {
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

            // Check if tutorial should be shown
            if (this.shouldShowTutorial()) {
                // Load tutorial steps configuration
                this.loadTutorialSteps();
                
                // Wait for page to be fully loaded
                $(document).ready(function() {
                    // Small delay to ensure all elements are rendered
                    setTimeout(function() {
                        EstimateTutorial.start();
                    }, 1000);
                });
            }
        },

        /**
         * Check if tutorial should be shown
         * @returns {boolean}
         */
        shouldShowTutorial: function() {
            // Check server-side preference (database-driven, no localStorage)
            var self = this;
            $.ajax({
                url: admin_url + 'ella_contractors/appointments/check_estimate_tutorial_status',
                type: 'GET',
                async: false, // Synchronous for initialization
                dataType: 'json',
                success: function(response) {
                    if (response && response.show_tutorial === false) {
                        self.config.shouldShow = false;
                    } else {
                        self.config.shouldShow = true;
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
                    title: 'Welcome to Estimates.....',
                    content: 'Welcome to the Estimates module! This quick tour will help you understand how to create, manage, and send estimates to your leads and clients.',
                    target: null,
                    position: 'center',
                    showNext: true,
                    showBack: false,
                    showSkip: true
                },
                {
                    id: 'new_estimate_button',
                    title: 'Create New Estimate',
                    content: 'Click the "New Estimate" button to create a new estimate. You can add line items, set pricing, customize terms, and attach files before sending it to your lead or client.',
                    target: '.panel-body._buttons .btn-info',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true
                },
                {
                    id: 'filter_dropdown',
                    title: 'Filter Estimates',
                    content: 'Use the filter dropdown to view estimates by status (Draft, Sent, Open, Revised, Declined, Accepted), by year, by sale agent, or filter for expired, leads-related, or customers-related estimates.',
                    target: '.btn-with-tooltip-group._filter_data .dropdown-toggle',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'proposals_table',
                    title: 'Estimates Table',
                    content: 'This table shows all your estimates with key information: proposal number, recipient, product info, total amount, open till date, creator, creation date, and status. You can sort by any column and search for specific estimates.',
                    target: '.table-proposals',
                    position: 'top',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true
                },
                {
                    id: 'table_actions',
                    title: 'Estimate Actions',
                    content: 'Each estimate row has action buttons to view, edit, convert to invoice, duplicate, or delete. Click on any estimate row to see details in the side panel, or use the action buttons for quick operations.',
                    target: '.table-proposals',
                    position: 'right',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true,
                    optional: true // Optional if table is empty
                },
                {
                    id: 'completion',
                    title: 'Tutorial Complete!',
                    content: 'You\'re all set! You can now create, manage, and send estimates efficiently. Remember: estimates help you provide accurate pricing information to your leads and clients, and can be converted to invoices once accepted.',
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
                console.warn('EstimateTutorial: No steps configured');
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
                    if (step.optional) {
                        // Skip optional step
                        this.next();
                        return;
                    }
                    // Wait for element to appear
                    var attempts = 0;
                    var maxAttempts = 20; // 10 seconds max wait
                    var checkElement = setInterval(function() {
                        attempts++;
                        var el = $(step.target);
                        if (el.length > 0) {
                            clearInterval(checkElement);
                            EstimateTutorial.showStep(stepIndex);
                        } else if (attempts >= maxAttempts) {
                            clearInterval(checkElement);
                            if (step.optional) {
                                EstimateTutorial.next();
                            } else {
                                EstimateTutorial.complete();
                            }
                        }
                    }, 500);
                    return;
                }
            }

            // Remove previous overlay and tooltip
            this.cleanup();

            // Create overlay
            this.createOverlay();

            // Create and show tooltip
            this.createTooltip(step, stepIndex);

            // Highlight target element if specified
            if (step.target && step.highlight) {
                this.highlightElement(step.target);
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
            tooltipHtml += '<h3 class="tutorial-tooltip-title"><i class="fa fa-lightbulb-o"></i> ' + this.escapeHtml(step.title) + '</h3>';
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
            tooltipHtml += '</div>';
            tooltipHtml += '</div>';

            this.state.tooltip = $(tooltipHtml);
            
            // Set initial positioning
            this.state.tooltip.css({
                position: 'fixed',
                top: '-9999px',
                left: '-9999px',
                visibility: 'hidden',
                opacity: 0,
                zIndex: 1041
            });
            
            $('body').append(this.state.tooltip);

            // Bind events
            this.bindTooltipEvents(step, stepIndex);
        },

        /**
         * Position tooltip relative to target element
         * @param {object} step
         * @param {boolean} skipTransition
         */
        positionTooltip: function(step, skipTransition) {
            if (!this.state.tooltip) {
                return;
            }

            var tooltip = this.state.tooltip;
            var position = step.position || 'bottom';

            // Remove all arrow classes first
            tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');

            // CUSTOM POSITIONING: Responsive override for specific steps
            // Get viewport dimensions for responsive positioning
            var viewportWidth = $(window).width();
            var viewportHeight = $(window).height();
            
            // Custom positioning for 'new_estimate_button' step
            if (step.id === 'new_estimate_button') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-top'); // Arrow at top pointing up to button
                
                // Ensure tooltip dimensions are calculated by temporarily making it visible off-screen
                var wasHidden = tooltip.css('visibility') === 'hidden';
                if (wasHidden) {
                    tooltip.css({
                        position: 'fixed',
                        top: '-9999px',
                        left: '-9999px',
                        visibility: 'visible',
                        opacity: 0
                    });
                }
                
                var positions;
                if (viewportWidth > 1920) {
                    // Ultra-wide and 4K+ screens (above 1920px) - Dynamic positioning
                    var target = $(step.target);
                    if (target.length > 0) {
                        var targetOffset = target.offset();
                        var targetWidth = target.outerWidth();
                        var targetHeight = target.outerHeight();
                        var scrollTop = $(window).scrollTop();
                        var tooltipWidth = tooltip.outerWidth();
                        var tooltipHeight = tooltip.outerHeight();
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
                        positions = { top: '180px', left: '180px' };
                    }
                } else if (viewportWidth >= 1920) {
                    // Extra large screens (1920px exactly)
                    positions = { top: '180px', left: '180px' };
                } else if (viewportWidth >= 1800) {
                    // Large screens (1800px - 1919px)
                    positions = { top: '180px', left: '160px' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1799px)
                    positions = { top: '180px', left: '140px' };
                } else if (viewportWidth >= 1440) {
                    // Desktop screens (1440px - 1599px)
                    positions = { top: '180px', left: '120px' };
                } else if (viewportWidth >= 1370) {
                    // Medium screens (1370px - 1439px)
                    positions = { top: '180px', left: '100px' };
                } else if (viewportWidth >= 1280) {
                    // Laptop screens (1280px - 1369px)
                    positions = { top: '180px', left: '80px' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1279px)
                    positions = { top: '180px', left: '60px' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '220px', left: '40px' };
                } else {
                    // Mobile and small tablets (below 768px)
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
                
                // Only set left, not both left and right
                if (positions.left) {
                    cssProps.left = positions.left;
                    cssProps.right = 'auto'; // Clear right when using left
                }
                
                // Apply transition if not skipping
                if (!skipTransition) {
                    cssProps.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                } else {
                    cssProps.transition = 'none';
                }
                
                tooltip.css(cssProps);
                
                // Restore visibility state if it was hidden
                if (wasHidden) {
                    // Small delay to ensure positioning is complete before showing
                    setTimeout(function() {
                        tooltip.css({
                            visibility: 'visible',
                            opacity: 1
                        });
                    }, 10);
                }
                return;
            }
            
            // Custom positioning for 'filter_dropdown' step
            if (step.id === 'filter_dropdown') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-top-right'); // Arrow at top right corner pointing up
                
                // Ensure tooltip dimensions are calculated by temporarily making it visible off-screen
                var wasHidden = tooltip.css('visibility') === 'hidden';
                if (wasHidden) {
                    tooltip.css({
                        position: 'fixed',
                        top: '-9999px',
                        left: '-9999px',
                        visibility: 'visible',
                        opacity: 0
                    });
                }
                
                var positions;
                if (viewportWidth > 1920) {
                    // Ultra-wide and 4K+ screens (above 1920px) - Dynamic positioning
                    var target = $(step.target);
                    if (target.length > 0) {
                        var targetOffset = target.offset();
                        var targetWidth = target.outerWidth();
                        var targetHeight = target.outerHeight();
                        var scrollTop = $(window).scrollTop();
                        var tooltipWidth = tooltip.outerWidth();
                        var tooltipHeight = tooltip.outerHeight();
                        var spacing = 20;
                        
                        var viewportTop = targetOffset.top - scrollTop;
                        var viewportLeft = targetOffset.left - $(window).scrollLeft();
                        
                        // Position below target, aligned to right
                        var topPos = viewportTop + targetHeight + spacing;
                        var rightPos = viewportWidth - (viewportLeft + targetWidth);
                        
                        // Viewport overflow checks
                        var calculatedLeft = viewportWidth - rightPos - tooltipWidth;
                        if (calculatedLeft < 10) {
                            rightPos = viewportWidth - tooltipWidth - 10;
                        }
                        
                        if (rightPos < 10) {
                            rightPos = 10;
                        }
                        
                        if (topPos + tooltipHeight > viewportHeight - 10) {
                            topPos = viewportTop - tooltipHeight - spacing;
                            if (topPos < 10) {
                                topPos = 10;
                            }
                        }
                        
                        positions = { top: topPos + 'px', right: rightPos + 'px' };
                    } else {
                        positions = { top: '200px', right: '30px' };
                    }
                } else if (viewportWidth >= 1920) {
                    // Extra large screens (1920px exactly)
                    positions = { top: '200px', right: '30px' };
                } else if (viewportWidth >= 1800) {
                    // Large screens (1800px - 1919px)
                    positions = { top: '200px', right: '30px' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1799px)
                    positions = { top: '200px', right: '30px' };
                } else if (viewportWidth >= 1440) {
                    // Desktop screens (1440px - 1599px)
                    positions = { top: '200px', right: '30px' };
                } else if (viewportWidth >= 1370) {
                    // Medium screens (1370px - 1439px)
                    positions = { top: '200px', right: '30px' };
                } else if (viewportWidth >= 1280) {
                    // Laptop screens (1280px - 1369px)
                    positions = { top: '200px', right: '30px' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1279px)
                    positions = { top: '220px', right: '20px' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '240px', right: '10px' };
                } else {
                    // Mobile and small tablets (below 768px)
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
                
                // Use right positioning for larger screens, center for mobile
                if (positions.right) {
                    cssProps.right = positions.right;
                    cssProps.left = 'auto'; // Clear left when using right
                } else if (positions.left) {
                    cssProps.left = positions.left;
                    cssProps.right = 'auto'; // Clear right when using left
                }
                
                // Apply transition if not skipping
                if (!skipTransition) {
                    cssProps.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                } else {
                    cssProps.transition = 'none';
                }
                
                tooltip.css(cssProps);
                
                // Restore visibility state if it was hidden
                if (wasHidden) {
                    setTimeout(function() {
                        tooltip.css({
                            visibility: 'visible',
                            opacity: 1
                        });
                    }, 10);
                }
                return;
            }
            
            // Custom positioning for 'proposals_table' step
            if (step.id === 'proposals_table') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-bottom'); // Arrow at bottom pointing down
                
                // Ensure tooltip dimensions are calculated by temporarily making it visible off-screen
                var wasHidden = tooltip.css('visibility') === 'hidden';
                if (wasHidden) {
                    tooltip.css({
                        position: 'fixed',
                        top: '-9999px',
                        left: '-9999px',
                        visibility: 'visible',
                        opacity: 0
                    });
                }
                
                var positions;
                if (viewportWidth > 1920) {
                    // Ultra-wide and 4K+ screens (above 1920px) - Dynamic positioning
                    var target = $(step.target);
                    if (target.length > 0) {
                        var targetOffset = target.offset();
                        var targetWidth = target.outerWidth();
                        var targetHeight = target.outerHeight();
                        var scrollTop = $(window).scrollTop();
                        var tooltipWidth = tooltip.outerWidth();
                        var tooltipHeight = tooltip.outerHeight();
                        var spacing = 20;
                        
                        var viewportTop = targetOffset.top - scrollTop;
                        var viewportLeft = targetOffset.left - $(window).scrollLeft();
                        
                        // Position above target (arrow-bottom points down)
                        var topPos = viewportTop - tooltipHeight - spacing;
                        var leftPos = viewportLeft + (targetWidth / 2) - (tooltipWidth / 2);
                        
                        // Viewport overflow checks
                        if (leftPos < 10) {
                            leftPos = 10;
                        } else if (leftPos + tooltipWidth > viewportWidth - 10) {
                            leftPos = viewportWidth - tooltipWidth - 10;
                        }
                        
                        if (topPos < 10) {
                            // Not enough space above, position below instead
                            topPos = viewportTop + targetHeight + spacing;
                            if (topPos + tooltipHeight > viewportHeight - 10) {
                                topPos = viewportHeight - tooltipHeight - 10;
                            }
                        }
                        
                        positions = { top: topPos + 'px', left: leftPos + 'px' };
                    } else {
                        positions = { top: '150px', left: '400px' };
                    }
                } else if (viewportWidth >= 1920) {
                    // Extra large screens (1920px exactly)
                    positions = { top: '150px', left: '400px' };
                } else if (viewportWidth >= 1800) {
                    // Large screens (1800px - 1919px)
                    positions = { top: '150px', left: '380px' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1799px)
                    positions = { top: '150px', left: '360px' };
                } else if (viewportWidth >= 1440) {
                    // Desktop screens (1440px - 1599px)
                    positions = { top: '150px', left: '340px' };
                } else if (viewportWidth >= 1370) {
                    // Medium screens (1370px - 1439px)
                    positions = { top: '150px', left: '320px' };
                } else if (viewportWidth >= 1280) {
                    // Laptop screens (1280px - 1369px)
                    positions = { top: '150px', left: '300px' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1279px)
                    positions = { top: '170px', left: '280px' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '190px', left: '260px' };
                } else {
                    // Mobile and small tablets (below 768px)
                    positions = { top: '150px', left: '50%', transform: 'translateX(-50%)' };
                }
                
                var cssProps = {
                    position: 'fixed',
                    top: positions.top,
                    transform: positions.transform || 'none',
                    zIndex: 1041,
                    visibility: 'visible',
                    opacity: 1
                };
                
                // Only set left, not both left and right
                if (positions.left) {
                    cssProps.left = positions.left;
                    cssProps.right = 'auto'; // Clear right when using left
                }
                
                // Apply transition if not skipping
                if (!skipTransition) {
                    cssProps.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                } else {
                    cssProps.transition = 'none';
                }
                
                tooltip.css(cssProps);
                
                // Restore visibility state if it was hidden
                if (wasHidden) {
                    setTimeout(function() {
                        tooltip.css({
                            visibility: 'visible',
                            opacity: 1
                        });
                    }, 10);
                }
                return;
            }
            
            // Custom positioning for 'table_actions' step
            if (step.id === 'table_actions') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-left'); // Arrow at left pointing right
                
                // Ensure tooltip dimensions are calculated by temporarily making it visible off-screen
                var wasHidden = tooltip.css('visibility') === 'hidden';
                if (wasHidden) {
                    tooltip.css({
                        position: 'fixed',
                        top: '-9999px',
                        left: '-9999px',
                        visibility: 'visible',
                        opacity: 0
                    });
                }
                
                var positions;
                if (viewportWidth > 1920) {
                    // Ultra-wide and 4K+ screens (above 1920px) - Dynamic positioning
                    var target = $(step.target);
                    if (target.length > 0) {
                        var targetOffset = target.offset();
                        var targetWidth = target.outerWidth();
                        var targetHeight = target.outerHeight();
                        var scrollTop = $(window).scrollTop();
                        var scrollLeft = $(window).scrollLeft();
                        var tooltipWidth = tooltip.outerWidth();
                        var tooltipHeight = tooltip.outerHeight();
                        var spacing = 20;
                        
                        var viewportTop = targetOffset.top - scrollTop;
                        var viewportLeft = targetOffset.left - scrollLeft;
                        
                        // Position to the right of target (arrow-left points right)
                        var topPos = viewportTop + (targetHeight / 2) - (tooltipHeight / 2);
                        var leftPos = viewportLeft + targetWidth + spacing;
                        
                        // Viewport overflow checks
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
                        positions = { top: '300px', left: '600px' };
                    }
                } else if (viewportWidth >= 1920) {
                    // Extra large screens (1920px exactly)
                    positions = { top: '300px', left: '600px' };
                } else if (viewportWidth >= 1800) {
                    // Large screens (1800px - 1919px)
                    positions = { top: '300px', left: '580px' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1799px)
                    positions = { top: '300px', left: '560px' };
                } else if (viewportWidth >= 1440) {
                    // Desktop screens (1440px - 1599px)
                    positions = { top: '300px', left: '540px' };
                } else if (viewportWidth >= 1370) {
                    // Medium screens (1370px - 1439px)
                    positions = { top: '300px', left: '520px' };
                } else if (viewportWidth >= 1280) {
                    // Laptop screens (1280px - 1369px)
                    positions = { top: '300px', left: '500px' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1279px)
                    positions = { top: '320px', left: '480px' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '340px', left: '460px' };
                } else {
                    // Mobile and small tablets (below 768px)
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
                
                // Only set left, not both left and right
                if (positions.left) {
                    cssProps.left = positions.left;
                    cssProps.right = 'auto'; // Clear right when using left
                }
                
                // Apply transition if not skipping
                if (!skipTransition) {
                    cssProps.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                } else {
                    cssProps.transition = 'none';
                }
                
                tooltip.css(cssProps);
                
                // Restore visibility state if it was hidden
                if (wasHidden) {
                    setTimeout(function() {
                        tooltip.css({
                            visibility: 'visible',
                            opacity: 1
                        });
                    }, 10);
                }
                return;
            }

            if (step.target && step.target !== null) {
                var target = $(step.target);
                if (target.length === 0) {
                    // Target not found, center the tooltip
                    this.centerTooltip(skipTransition);
                    return;
                }

                var targetOffset = target.offset();
                var targetWidth = target.outerWidth();
                var targetHeight = target.outerHeight();
                var tooltipWidth = tooltip.outerWidth();
                var tooltipHeight = tooltip.outerHeight();
                var scrollTop = $(window).scrollTop();
                var scrollLeft = $(window).scrollLeft();
                var windowWidth = $(window).width();
                var windowHeight = $(window).height();
                var padding = 15;

                var top, left, arrowOffset = 0;

                // Calculate position based on preferred position
                switch (position) {
                    case 'top':
                        top = targetOffset.top - tooltipHeight - padding;
                        left = targetOffset.left + (targetWidth / 2) - (tooltipWidth / 2);
                        tooltip.addClass('tutorial-arrow-bottom');
                        break;
                    case 'bottom':
                        top = targetOffset.top + targetHeight + padding;
                        left = targetOffset.left + (targetWidth / 2) - (tooltipWidth / 2);
                        tooltip.addClass('tutorial-arrow-top');
                        break;
                    case 'left':
                        top = targetOffset.top + (targetHeight / 2) - (tooltipHeight / 2);
                        left = targetOffset.left - tooltipWidth - padding;
                        tooltip.addClass('tutorial-arrow-right');
                        break;
                    case 'right':
                        top = targetOffset.top + (targetHeight / 2) - (tooltipHeight / 2);
                        left = targetOffset.left + targetWidth + padding;
                        tooltip.addClass('tutorial-arrow-left');
                        break;
                    default:
                        this.centerTooltip(skipTransition);
                        return;
                }

                // Adjust for viewport boundaries
                if (left < padding) {
                    arrowOffset = left - padding;
                    left = padding;
                } else if (left + tooltipWidth > windowWidth - padding) {
                    arrowOffset = (left + tooltipWidth) - (windowWidth - padding);
                    left = windowWidth - tooltipWidth - padding;
                }

                if (top < scrollTop + padding) {
                    top = scrollTop + padding;
                } else if (top + tooltipHeight > scrollTop + windowHeight - padding) {
                    top = scrollTop + windowHeight - tooltipHeight - padding;
                }

                // Set arrow offset CSS variable
                tooltip.css('--arrow-offset', arrowOffset + 'px');

                // Apply position
                if (skipTransition) {
                    tooltip.css({
                        top: top + 'px',
                        left: left + 'px',
                        visibility: 'visible',
                        opacity: 1,
                        transition: 'none'
                    });
                } else {
                    tooltip.css({
                        top: top + 'px',
                        left: left + 'px',
                        visibility: 'visible',
                        opacity: 1,
                        transition: 'opacity 0.3s ease-out, transform 0.3s ease-out'
                    });
                }
            } else {
                // No target, center the tooltip
                this.centerTooltip(skipTransition);
            }
        },

        /**
         * Center tooltip on screen
         * @param {boolean} skipTransition
         */
        centerTooltip: function(skipTransition) {
            if (!this.state.tooltip) {
                return;
            }

            var tooltip = this.state.tooltip;
            var tooltipWidth = tooltip.outerWidth();
            var tooltipHeight = tooltip.outerHeight();
            var windowWidth = $(window).width();
            var windowHeight = $(window).height();
            var scrollTop = $(window).scrollTop();
            var scrollLeft = $(window).scrollLeft();

            var top = scrollTop + (windowHeight / 2) - (tooltipHeight / 2);
            var left = scrollLeft + (windowWidth / 2) - (tooltipWidth / 2);

            if (skipTransition) {
                tooltip.css({
                    top: top + 'px',
                    left: left + 'px',
                    visibility: 'visible',
                    opacity: 1,
                    transition: 'none'
                });
            } else {
                tooltip.css({
                    top: top + 'px',
                    left: left + 'px',
                    visibility: 'visible',
                    opacity: 1,
                    transition: 'opacity 0.3s ease-out'
                });
            }
        },

        /**
         * Highlight target element
         * @param {string} selector
         */
        highlightElement: function(selector) {
            if (!selector) {
                return;
            }

            var element = $(selector);
            if (element.length === 0) {
                return;
            }

            this.state.targetElement = element;
            element.addClass('tutorial-highlight');
        },

        /**
         * Remove highlight from element
         */
        removeHighlight: function() {
            if (this.state.targetElement) {
                this.state.targetElement.removeClass('tutorial-highlight');
                this.state.targetElement = null;
            }
        },

        /**
         * Bind tooltip events
         * @param {object} step
         * @param {number} stepIndex
         */
        bindTooltipEvents: function(step, stepIndex) {
            var self = this;

            // Position tooltip after it's added to DOM
            setTimeout(function() {
                self.positionTooltip(step, false);
                
                // Handle window resize
                if (self.state.resizeHandler) {
                    $(window).off('resize', self.state.resizeHandler);
                }
                self.state.resizeHandler = function() {
                    self.positionTooltip(step, true);
                };
                $(window).on('resize', self.state.resizeHandler);
            }, 10);

            // Close button
            this.state.tooltip.find('.tutorial-close').on('click', function() {
                self.complete();
            });

            // Next button
            this.state.tooltip.find('.tutorial-btn-next').on('click', function() {
                self.next();
            });

            // Back button
            this.state.tooltip.find('.tutorial-btn-back').on('click', function() {
                self.back();
            });

            // Skip button
            this.state.tooltip.find('.tutorial-btn-skip').on('click', function(e) {
                e.preventDefault();
                self.complete();
            });

        },

        /**
         * Move to next step
         */
        next: function() {
            this.removeHighlight();
            var nextIndex = this.state.currentStepIndex + 1;
            this.showStep(nextIndex);
        },

        /**
         * Move to previous step
         */
        back: function() {
            this.removeHighlight();
            var prevIndex = this.state.currentStepIndex - 1;
            this.showStep(prevIndex);
        },

        /**
         * Complete tutorial
         */
        complete: function() {
            // Save tutorial completion preference to server (database)
            $.ajax({
                url: admin_url + 'ella_contractors/appointments/save_estimate_tutorial_preference',
                type: 'POST',
                dataType: 'json',
                data: {
                    dismissed: 1,
                    [csrf_token_name]: csrf_hash
                },
                success: function(response) {
                    console.log('Estimate tutorial preference saved:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to save estimate tutorial preference:', error);
                }
            });
            
            this.cleanup();
            this.state.isActive = false;
        },

        /**
         * Restart tutorial
         */
        restart: function() {
            var self = this;
            
            // Reset tutorial preference on server (database)
            $.ajax({
                url: admin_url + 'ella_contractors/appointments/reset_estimate_tutorial',
                type: 'POST',
                dataType: 'json',
                data: {
                    [csrf_token_name]: csrf_hash
                },
                success: function(response) {
                    console.log('Estimate tutorial reset:', response);
                    // Restart tutorial after server reset
                    self.cleanup();
                    self.state.isActive = false;
                    self.state.currentStepIndex = 0;
                    self.config.shouldShow = true;
                    self.start();
                },
                error: function(xhr, status, error) {
                    console.error('Failed to reset estimate tutorial:', error);
                    // Still attempt to restart locally
                    self.cleanup();
                    self.state.isActive = false;
                    self.state.currentStepIndex = 0;
                    self.config.shouldShow = true;
                    self.start();
                }
            });
        },

        /**
         * Cleanup overlay and tooltip
         */
        cleanup: function() {
            this.removeHighlight();
            
            if (this.state.resizeHandler) {
                $(window).off('resize', this.state.resizeHandler);
                this.state.resizeHandler = null;
            }

            if (this.state.overlay) {
                this.state.overlay.remove();
                this.state.overlay = null;
            }

            if (this.state.tooltip) {
                this.state.tooltip.remove();
                this.state.tooltip = null;
            }
        },

        /**
         * Escape HTML
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
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        console.log('EstimateTutorial initialized');
        // Only initialize if we're on the proposals/estimates listing page
        // Check for key elements that exist on the proposals manage page
        if ($('.table-proposals').length || $('.panel-body._buttons .btn-info').length) {
            EstimateTutorial.init();
        }
    });

    // Expose globally for manual restart
    window.EstimateTutorial = EstimateTutorial;

})(jQuery);

