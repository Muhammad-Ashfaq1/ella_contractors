/**
 * EllaContractors Presentation Tutorial System
 * 
 * Provides step-by-step guided tours for first-time users
 * Supports "Don't show again" functionality with persistence
 * Fully responsive with dynamic positioning
 * 
 * @version 2.0.0
 * @author EllaContractors Team
 */

(function($) {
    'use strict';

    /**
     * Tutorial Manager Class
     */
    var PresentationTutorial = {
        // Configuration
        config: {
            storageKey: 'ella_contractors_presentation_tutorial_completed',
            storageKeyDismissed: 'ella_contractors_presentation_tutorial_dismissed',
            tutorialId: 'presentations_tutorial',
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
            resizeHandler: null // NEW: track resize handler
        },

        /**
         * Initialize tutorial system
         */
        init: function() {
            var self = this;
            
            // Set up modal event handlers (only once) - listen to standard Bootstrap modal events
            $(document).off('show.bs.modal.presentation-tutorial hidden.bs.modal.presentation-tutorial');
            $(document).on('show.bs.modal.presentation-tutorial', function() {
                // Hide tutorial overlay when modals open
                if (self.state.overlay) {
                    self.state.overlay.css('display', 'none');
                }
                if (self.state.tooltip) {
                    self.state.tooltip.css('display', 'none');
                }
            });

            $(document).on('hidden.bs.modal.presentation-tutorial', function() {
                // Show tutorial overlay when modals close (if tutorial is still active)
                if (self.state.isActive) {
                    if (self.state.overlay) {
                        self.state.overlay.css('display', 'block');
                    }
                    if (self.state.tooltip) {
                        self.state.tooltip.css('display', 'block');
                    }
                }
            });

            // Also listen to standard Bootstrap modal events (without namespace for compatibility)
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
                        PresentationTutorial.start();
                    }, 1000);
                });
            }
        },

        /**
         * Check if tutorial should be shown
         * @returns {boolean}
         */
        shouldShowTutorial: function() {
            // Check localStorage first (client-side)
            var dismissed = localStorage.getItem(this.config.storageKeyDismissed);
            if (dismissed === 'true') {
                return false;
            }

            // Check server-side preference
            var self = this;
            $.ajax({
                url: admin_url + 'ella_contractors/presentations/check_tutorial_status',
                type: 'GET',
                async: false, // Synchronous for initialization
                dataType: 'json',
                success: function(response) {
                    if (response && response.show_tutorial === false) {
                        self.config.shouldShow = false;
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
                    title: 'Welcome to Presentations',
                    content: 'Welcome to the EllaContractors Presentations module! This quick tour will help you learn how to upload, manage, and attach presentations to appointments.',
                    target: null, // No specific target for welcome
                    position: 'center', // Center of screen
                    showNext: true,
                    showBack: false,
                    showSkip: true
                },
                {
                    id: 'upload_button',
                    title: 'Upload Presentation',
                    content: 'Click the "Upload Presentation" button to add new presentation files. You can upload PDF, PPT, PPTX, or HTML files. Presentations can be attached to appointments and shared with clients.',
                    target: '[data-toggle="modal"][data-target="#uploadPresentationModal"]',
                    position: 'bottom', // Tooltip appears below button
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'presentations_table',
                    title: 'Presentations Table',
                    content: 'This table displays all your uploaded presentations. You can sort by any column, search for specific presentations, edit names inline, preview files, and use bulk actions to manage multiple presentations.',
                    target: '.table-ella_presentations',
                    position: 'top',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true // Wait for table to load
                },
                {
                    id: 'edit_name',
                    title: 'Edit Presentation Name',
                    content: 'Click the pencil icon next to any presentation name to edit it inline. This is useful for organizing your presentations with meaningful names.',
                    target: '.edit-name-icon',
                    position: 'left',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true,
                    optional: true // This step is optional if element doesn't exist
                },
                {
                    id: 'preview_action',
                    title: 'Preview Presentations',
                    content: 'Click the preview icon to view presentations directly in the browser. PDF files show inline, while PPT/PPTX files use online viewers. This helps you verify content before sharing.',
                    target: '.btn-info[onclick*="previewFile"]',
                    position: 'left',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true,
                    optional: true // This step is optional if element doesn't exist
                },
                {
                    id: 'bulk_actions',
                    title: 'Bulk Actions',
                    content: 'Select multiple presentations using checkboxes, then use the "Delete All" button to remove them at once. This is efficient for managing large numbers of presentations.',
                    target: '#bulk-delete-presentations',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true,
                    optional: false // Always show this step
                },
                {
                    id: 'completion',
                    title: 'Tutorial Complete!',
                    content: 'You\'re all set! You can now upload, manage, and attach presentations to appointments. Presentations help you share important information with clients during appointments.',
                    target: null,
                    position: 'center',
                    showNext: true, // Show "Got it!" button
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
            this.state.isActive = true;
            this.state.currentStepIndex = 0;
            
            // Add class to body to prevent sidebar closing
            $('body').addClass('tutorial-active');
            
            this.setupResizeHandler(); // NEW: Setup resize handler when tutorial starts
            this.showStep(0);
        },

        // NEW: Setup window resize handler
        setupResizeHandler: function() {
            var self = this;
            var resizeTimeout;
            
            // Remove any existing handler
            if (this.state.resizeHandler) {
                $(window).off('resize', this.state.resizeHandler);
            }
            
            // Create debounced resize handler
            this.state.resizeHandler = function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    // Only reposition if tutorial is active and tooltip exists
                    if (self.state.isActive && self.state.tooltip && self.state.tooltip.length > 0) {
                        var currentStep = self.config.steps[self.state.currentStepIndex];
                        if (currentStep) {
                            // Reposition current tooltip without animation
                            self.positionTooltip(currentStep, true);
                        }
                    }
                }, 150); // Debounce for 150ms
            };
            
            $(window).on('resize', this.state.resizeHandler);
        },

        // NEW: Remove resize handler
        removeResizeHandler: function() {
            if (this.state.resizeHandler) {
                $(window).off('resize', this.state.resizeHandler);
                this.state.resizeHandler = null;
            }
        },

        /**
         * Show a specific step
         * @param {number} stepIndex - Index of the step to show
         */
        showStep: function(stepIndex) {
            // Ensure steps array is loaded
            if (!this.config.steps || this.config.steps.length === 0) {
                this.loadTutorialSteps();
            }

            if (stepIndex < 0 || stepIndex >= this.config.steps.length) {
                // If trying to go beyond last step, show completion step
                if (stepIndex >= this.config.steps.length) {
                    // Show the last step (completion)
                    stepIndex = this.config.steps.length - 1;
                } else {
                    this.complete();
                    return;
                }
            }

            var step = this.config.steps[stepIndex];
            this.state.currentStepIndex = stepIndex;

            // Wait for target element if needed
            if (step.waitForElement && step.target) {
                if (!this.waitForElement(step.target, function() {
                    PresentationTutorial.renderStep(step, stepIndex);
                })) {
                    // Element not found, skip this step if optional
                    if (step.optional) {
                        // Don't skip if this is the completion step (last step)
                        if (stepIndex < this.config.steps.length - 1) {
                            this.next();
                            return;
                        } else {
                            // If completion step has no target, render it anyway
                            this.renderStep(step, stepIndex);
                            return;
                        }
                    } else {
                        // Element not found and not optional - render anyway
                        this.renderStep(step, stepIndex);
                        return;
                    }
                }
            } else {
                // No wait needed, render immediately
                this.renderStep(step, stepIndex);
            }
        },

        /**
         * Wait for an element to appear in DOM
         * @param {string} selector - CSS selector
         * @param {function} callback - Callback when element found
         * @param {number} maxAttempts - Maximum attempts (default: 20)
         * @returns {boolean} - True if element found immediately
         */
        waitForElement: function(selector, callback, maxAttempts) {
            maxAttempts = maxAttempts || 20;
            var attempts = 0;
            var element = $(selector);
            
            // Check if element is already visible
            if (element.length > 0 && element.is(':visible')) {
                callback();
                return true;
            }

            // If not visible, wait for it
            var checkElement = setInterval(function() {
                attempts++;
                var element = $(selector);
                
                if (element.length > 0 && element.is(':visible')) {
                    clearInterval(checkElement);
                    callback();
                    return;
                }

                if (attempts >= maxAttempts) {
                    clearInterval(checkElement);
                    return;
                }
            }, 500);

            return false;
        },

        /**
         * Render a tutorial step
         * @param {object} step - Step configuration
         * @param {number} stepIndex - Current step index
         */
        renderStep: function(step, stepIndex) {
            var self = this;
            
            // Ensure step is valid
            if (!step) {
                console.error('Tutorial: Invalid step at index', stepIndex);
                return;
            }

            // Ensure we have steps loaded
            if (!this.config.steps || this.config.steps.length === 0) {
                this.loadTutorialSteps();
            }
            
            // Fade out previous step with animation
            if (this.state.tooltip) {
                this.state.tooltip.css({
                    opacity: 0,
                    transform: 'scale(0.95)',
                    transition: 'opacity 0.2s ease-out, transform 0.2s ease-out'
                });
                
                // Remove after fade out animation
                setTimeout(function() {
                    self.removeCurrentStep();
                    self.showNewStep(step, stepIndex);
                }, 200);
            } else {
                // No previous step, show immediately
                this.showNewStep(step, stepIndex);
            }
        },

        /**
         * Show new step with animation
         * @param {object} step - Step configuration
         * @param {number} stepIndex - Current step index
         */
        showNewStep: function(step, stepIndex) {
            var self = this;

            // Create overlay
            this.createOverlay();

            // Create tooltip
            this.createTooltip(step, stepIndex);

            // Initially hide tooltip completely (off-screen and invisible) to prevent any flashing
            this.state.tooltip.css({
                opacity: 0,
                transform: 'scale(0.95)',
                position: 'fixed',
                top: '-9999px',
                left: '-9999px',
                visibility: 'hidden'
            });

            // UPDATED: Use unified positioning logic for ALL steps (no more hard-coded positions)
            if (step.target && step.position !== 'center') {
                var target = $(step.target);
                
                // Wait a bit for element to be fully rendered
                setTimeout(function() {
                    // Scroll element into view first if needed (while tooltip is still hidden)
                    if (step.highlight) {
                        self.highlightElement(step.target);
                    }
                    
                    // Small delay to allow scroll animation to complete, then position once and show
                    setTimeout(function() {
                        // Position tooltip first (while still hidden)
                        self.positionTooltip(step);
                        
                        // Small delay to ensure positioning is complete, then make visible
                        setTimeout(function() {
                            // Make visible and fade in with animation
                            self.state.tooltip.css({
                                visibility: 'visible',
                                opacity: 1,
                                transform: 'scale(1)',
                                transition: 'opacity 0.3s ease-out, transform 0.3s ease-out'
                            });
                        }, 50);
                    }, 350);
                }, 100);
            } else {
                // No target or center position - position immediately
                this.positionTooltip(step);
                this.state.tooltip.css('visibility', 'visible');
                // Fade in tooltip with animation
                setTimeout(function() {
                    self.state.tooltip.css({
                        opacity: 1,
                        transform: 'scale(1)',
                        transition: 'opacity 0.3s ease-out, transform 0.3s ease-out'
                    });
                }, 50);
            }
        },

        /**
         * Create overlay backdrop
         */
        createOverlay: function() {
            var self = this;
            if (this.state.overlay) {
                return;
            }

            this.state.overlay = $('<div>', {
                class: 'tutorial-overlay',
                css: {
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    width: '100%',
                    height: '100%',
                    backgroundColor: 'rgba(0, 0, 0, 0.5)',
                    zIndex: 1040, // Below Bootstrap modals (1050)
                    pointerEvents: 'auto'
                }
            });

            $('body').append(this.state.overlay);

            // Prevent sidebar from closing when clicking on overlay
            this.state.overlay.on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
            });

            // Overlay click handler removed - steps only change via next/previous buttons
        },

        /**
         * Create tooltip element
         * @param {object} step - Step configuration
         * @param {number} stepIndex - Current step index
         */
        createTooltip: function(step, stepIndex) {
            var totalSteps = this.config.steps.length;
            var stepNumber = stepIndex + 1;

            var tooltipHtml = '<div class="tutorial-tooltip" id="tutorial-tooltip-' + step.id + '">';
            
            // Header
            tooltipHtml += '<div class="tutorial-tooltip-header">';
            tooltipHtml += '<h4 class="tutorial-tooltip-title">';
            if (step.id !== 'welcome' && step.id !== 'completion') {
                tooltipHtml += '<i class="fa fa-file-powerpoint-o" style="margin-right: 5px;"></i>';
            }
            tooltipHtml += step.title;
            tooltipHtml += '</h4>';
            tooltipHtml += '<button type="button" class="tutorial-close" aria-label="Close">&times;</button>';
            tooltipHtml += '</div>';

            // Progress indicator
            tooltipHtml += '<div class="tutorial-progress">';
            tooltipHtml += 'Step ' + stepNumber + ' of ' + totalSteps;
            tooltipHtml += '</div>';

            // Content
            tooltipHtml += '<div class="tutorial-tooltip-content">';
            tooltipHtml += '<p>' + step.content + '</p>';
            tooltipHtml += '</div>';

            // Footer with buttons
            tooltipHtml += '<div class="tutorial-tooltip-footer">';
            tooltipHtml += '<div class="tutorial-tooltip-actions">';
            
            // Back button
            if (step.showBack) {
                tooltipHtml += '<button type="button" class="btn btn-default tutorial-btn-back">Back</button>';
            }

            // Skip button
            if (step.showSkip) {
                tooltipHtml += '<button type="button" class="btn btn-link tutorial-btn-skip">Skip Tutorial</button>';
            }

            // Next/Finish button
            if (step.showNext) {
                var nextText = step.isLast ? 'Got it!' : 'Next';
                tooltipHtml += '<button type="button" class="btn btn-primary tutorial-btn-next">' + nextText + '</button>';
            }

            tooltipHtml += '</div>';

            // Don't show again checkbox (only on last step)
            // if (step.isLast) {
            //     tooltipHtml += '<div class="tutorial-dont-show">';
            //     tooltipHtml += '<label>';
            //     tooltipHtml += '<input type="checkbox" id="tutorial-dont-show-again"> ';
            //     tooltipHtml += "Don't show me this again";
            //     tooltipHtml += '</label>';
            //     tooltipHtml += '<button type="button" class="btn btn-default tutorial-btn-close" style="margin-left: 15px;">Close</button>';
            //     tooltipHtml += '</div>';
            // }

            tooltipHtml += '</div>';
            tooltipHtml += '</div>';

            this.state.tooltip = $(tooltipHtml);
            $('body').append(this.state.tooltip);

            // Prevent sidebar from closing when clicking on tooltip
            this.state.tooltip.on('click', function(e) {
                e.stopPropagation();
            });

            // Bind events
            this.bindTooltipEvents(step, stepIndex);
        },

        /**
         * Position tooltip relative to target element
         * @param {object} step - Step configuration
         * @param {boolean} skipTransition - Skip transition animation (for resize)
         */
        positionTooltip: function(step, skipTransition) {
            var tooltip = this.state.tooltip;
            
            // CUSTOM POSITIONING: Responsive override for specific steps
            // Get viewport dimensions for responsive positioning
            var viewportWidth = $(window).width();
            var viewportHeight = $(window).height();
            
            if (step.id === 'upload_button') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-top'); // Arrow at top pointing up
                
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
                if (viewportWidth >= 1920) {
                    // Extra large screens (1920px and above)
                    positions = { top: '140.391px', right: '30.12px' };
                } else if (viewportWidth >= 1800) {
                    // Large screens (1800px - 1919px)
                    positions = { top: '140.391px', right: '30.12px' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1799px)
                    positions = { top: '140.391px', right: '30.12px' };
                } else if (viewportWidth >= 1440) {
                    // Desktop screens (1440px - 1599px)
                    positions = { top: '140.391px', right: '30.12px' };
                } else if (viewportWidth >= 1370) {
                    // Medium screens (1370px - 1439px)
                    positions = { top: '140.391px', right: '30.12px' };
                } else if (viewportWidth >= 1280) {
                    // Laptop screens (1280px - 1369px)
                    positions = { top: '140.391px', right: '30.12px' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1279px)
                    positions = { top: '160.391px', right: '30.12px' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '180.391px', right: '30.12px' };
                } else {
                    // Mobile and small tablets (below 768px)
                    positions = { top: '140.391px', left: '50%', transform: 'translateX(-50%)' };
                }
                
                var cssProps = {
                    position: 'fixed',
                    top: positions.top,
                    transform: positions.transform || 'none',
                    zIndex: 1041
                };
                
                // Only set right or left, not both
                if (positions.right) {
                    cssProps.right = positions.right;
                    cssProps.left = 'auto'; // Clear left when using right
                } else if (positions.left) {
                    cssProps.left = positions.left;
                    cssProps.right = 'auto'; // Clear right when using left
                }
                
                tooltip.css(cssProps);
                
                // Restore visibility state if it was hidden
                if (wasHidden) {
                    tooltip.css({
                        visibility: 'hidden',
                        opacity: 0
                    });
                }
                return;
            }
            
            if (step.id === 'preview_action') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-right'); // Arrow at right pointing left
                
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
                if (viewportWidth >= 1920) {
                    // Extra large screens (1920px and above)
                    positions = { top: '97.0515px', right: '230.12px' };
                } else if (viewportWidth >= 1800) {
                    // Large screens (1800px - 1919px)
                    positions = { top: '97.0515px', right: '230.12px' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1799px)
                    positions = { top: '97.0515px', right: '230.12px' };
                } else if (viewportWidth >= 1440) {
                    // Desktop screens (1440px - 1599px)
                    positions = { top: '97.0515px', right: '230.12px' };
                } else if (viewportWidth >= 1370) {
                    // Medium screens (1370px - 1439px)
                    positions = { top: '120.0515px', right: '230.12px' };
                } else if (viewportWidth >= 1280) {
                    // Laptop screens (1280px - 1369px)
                    positions = { top: '120.0515px', right: '230.12px' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1279px)
                    positions = { top: '120.0515px', right: '230.12px' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '120.0515px', right: '230.12px' };
                } else {
                    // Mobile and small tablets (below 768px)
                    positions = { top: '97.0515px', left: '50%', transform: 'translateX(-50%)' };
                }
                
                var cssProps = {
                    position: 'fixed',
                    top: positions.top,
                    transform: positions.transform || 'none',
                    zIndex: 1041
                };
                
                // Only set right or left, not both
                if (positions.right) {
                    cssProps.right = positions.right;
                    cssProps.left = 'auto'; // Clear left when using right
                } else if (positions.left) {
                    cssProps.left = positions.left;
                    cssProps.right = 'auto'; // Clear right when using left
                }
                
                tooltip.css(cssProps);
                
                // Restore visibility state if it was hidden
                if (wasHidden) {
                    tooltip.css({
                        visibility: 'hidden',
                        opacity: 0
                    });
                }
                return;
            }
            
            if (step.id === 'edit_name') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-right'); // Arrow at right pointing left
                
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
                if (viewportWidth >= 1920) {
                    // Extra large screens (1920px and above)
                    positions = { top: '121.136px', left: '122.312px' };
                } else if (viewportWidth >= 1800) {
                    // Large screens (1800px - 1919px)
                    positions = { top: '121.136px', left: '122.312px' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1799px)
                    positions = { top: '121.136px', left: '122.312px' };
                } else if (viewportWidth >= 1440) {
                    // Desktop screens (1440px - 1599px)
                    positions = { top: '121.136px', left: '112.312px' };
                } else if (viewportWidth >= 1370) {
                    // Medium screens (1370px - 1439px)
                    positions = { top: '121.136px', left: '121.312px' };
                } else if (viewportWidth >= 1280) {
                    // Laptop screens (1280px - 1369px)
                    positions = { top: '121.136px', left: '91.312px' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1279px)
                    positions = { top: '145.136px', left: '81.312px' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '245.136px', left: '71.312px' };
                } else {
                    // Mobile and small tablets (below 768px)
                    positions = { top: '121.136px', left: '50%', transform: 'translateX(-50%)' };
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: positions.top,
                    left: positions.left,
                    transform: positions.transform || 'none',
                    zIndex: 1041
                });
                
                // Restore visibility state if it was hidden
                if (wasHidden) {
                    tooltip.css({
                        visibility: 'hidden',
                        opacity: 0
                    });
                }
                return;
            }
            
            if (step.id === 'presentations_table') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-bottom');
                
                // Get target element position
                var target = $(step.target);
                if (target.length === 0) {
                    return;
                }
                
                var targetOffset = target.offset();
                var targetWidth = target.outerWidth();
                var targetHeight = target.outerHeight();
                var scrollTop = $(window).scrollTop();
                var scrollLeft = $(window).scrollLeft();
                var viewportTop = targetOffset.top - scrollTop;
                var viewportLeft = targetOffset.left - scrollLeft;
                
                // Ensure tooltip dimensions are calculated
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
                
                var tooltipWidth = tooltip.outerWidth();
                var tooltipHeight = tooltip.outerHeight();
                var spacing = 20;
                var topPos, leftPos;
                
                // Position tooltip above the table (arrow-bottom means arrow points down to table)
                topPos = viewportTop - tooltipHeight - spacing;
                leftPos = viewportLeft + (targetWidth / 2) - (tooltipWidth / 2);
                
                // Ensure tooltip stays within viewport
                if (leftPos < 10) {
                    leftPos = 10;
                } else if (leftPos + tooltipWidth > viewportWidth - 10) {
                    leftPos = viewportWidth - tooltipWidth - 10;
                }
                
                if (topPos < 10) {
                    // Not enough space above, position below instead
                    topPos = viewportTop + targetHeight + spacing;
                    tooltip.removeClass('tutorial-arrow-bottom').addClass('tutorial-arrow-top');
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: topPos + 'px',
                    left: leftPos + 'px',
                    transform: 'none',
                    zIndex: 1041
                });
                
                // Restore visibility state if it was hidden
                if (wasHidden) {
                    tooltip.css({
                        visibility: 'hidden',
                        opacity: 0
                    });
                }
                return;
            }
            
            if (step.id === 'bulk_actions') {
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
                if (viewportWidth >= 1920) {
                    // Extra large screens (1920px and above)
                    positions = { top: '150px', left: '340px' };
                } else if (viewportWidth >= 1800) {
                    // Large screens (1800px - 1919px)
                    positions = { top: '150px', left: '340px' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1799px)
                    positions = { top: '150px', left: '340px' };
                } else if (viewportWidth >= 1440) {
                    // Desktop screens (1440px - 1599px)
                    positions = { top: '150px', left: '340px' };
                } else if (viewportWidth >= 1370) {
                    // Medium screens (1370px - 1439px)
                    positions = { top: '150px', left: '340px' };
                } else if (viewportWidth >= 1280) {
                    // Laptop screens (1280px - 1369px)
                    positions = { top: '150px', left: '340px' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1279px)
                    positions = { top: '150px', left: '340px' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '250px', left: '340px' };
                } else {
                    // Mobile and small tablets (below 768px)
                    positions = { top: '150px', left: '50%', transform: 'translateX(-50%)' };
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: positions.top,
                    left: positions.left,
                    transform: positions.transform || 'none',
                    zIndex: 1041
                });
                
                // Restore visibility state if it was hidden
                if (wasHidden) {
                    tooltip.css({
                        visibility: 'hidden',
                        opacity: 0
                    });
                }
                return;
            }
            
            // Find the target element first to check if it's in a modal
            var target = step.target ? $(step.target) : null;
            var targetInModal = target && target.length > 0 ? target.closest('.modal') : null;
            
            // Check if a modal is currently open (multiple ways to detect)
            var $openModal = $('.modal.in, .modal.show, .modal[style*="display: block"]');
            if ($openModal.length === 0) {
                // Fallback: check if body has modal-open class
                if ($('body').hasClass('modal-open')) {
                    $openModal = $('.modal').filter(function() {
                        return $(this).css('display') !== 'none';
                    });
                }
            }
            
            // If target is in a modal, use that modal; otherwise use any open modal
            var modalContainer = targetInModal && targetInModal.length > 0 ? targetInModal : ($openModal.length > 0 ? $openModal.first() : null);
            var isModalOpen = modalContainer && modalContainer.length > 0;
            
            if (!step.target || step.position === 'center') {
                // Center on screen or modal
                if (isModalOpen && modalContainer) {
                    // Position relative to modal-content (the actual visible container)
                    var modalContent = modalContainer.find('.modal-content').first();
                    var container = modalContent.length > 0 ? modalContent : modalContainer.find('.modal-dialog').first();
                    if (container.length === 0) {
                        container = modalContainer;
                    }
                    
                    var containerOffset = container.offset();
                    var containerWidth = container.outerWidth();
                    var containerHeight = container.outerHeight();
                    var tooltipWidth = tooltip.outerWidth();
                    var tooltipHeight = tooltip.outerHeight();
                    
                    // Append to modal-content for proper positioning context
                    tooltip.appendTo(container);
                    
                    tooltip.css({
                        position: 'absolute',
                        top: (containerHeight / 2) - (tooltipHeight / 2),
                        left: (containerWidth / 2) - (tooltipWidth / 2),
                        transform: 'none',
                        zIndex: 10000 // Above modal
                    });
                } else {
                    // Center on screen
                    if (tooltip.parent().hasClass('modal') || tooltip.parent().hasClass('modal-content') || tooltip.parent().hasClass('modal-body') || tooltip.parent().hasClass('modal-dialog')) {
                        tooltip.appendTo('body');
                    }
                    tooltip.css({
                        position: 'fixed',
                        top: '50%',
                        left: '50%',
                        transform: 'translate(-50%, -50%)',
                        zIndex: 1041
                    });
                }
                return;
            }

            // Position relative to target element
            if (!target || target.length === 0) {
                // Fallback to center if target not found
                if (isModalOpen && modalContainer) {
                    var modalContent = modalContainer.find('.modal-content').first();
                    var container = modalContent.length > 0 ? modalContent : modalContainer.find('.modal-dialog').first();
                    if (container.length === 0) {
                        container = modalContainer;
                    }
                    
                    var containerOffset = container.offset();
                    var containerWidth = container.outerWidth();
                    var containerHeight = container.outerHeight();
                    var tooltipWidth = tooltip.outerWidth();
                    var tooltipHeight = tooltip.outerHeight();
                    
                    tooltip.appendTo(container);
                    
                    tooltip.css({
                        position: 'absolute',
                        top: (containerHeight / 2) - (tooltipHeight / 2),
                        left: (containerWidth / 2) - (tooltipWidth / 2),
                        transform: 'none',
                        zIndex: 10000
                    });
                } else {
                    if (tooltip.parent().hasClass('modal') || tooltip.parent().hasClass('modal-content') || tooltip.parent().hasClass('modal-body') || tooltip.parent().hasClass('modal-dialog')) {
                        tooltip.appendTo('body');
                    }
                    tooltip.css({
                        position: 'fixed',
                        top: '50%',
                        left: '50%',
                        transform: 'translate(-50%, -50%)',
                        zIndex: 1041
                    });
                }
                return;
            }

            var targetOffset = target.offset();
            var targetWidth = target.outerWidth();
            var targetHeight = target.outerHeight();
            var tooltipWidth = tooltip.outerWidth();
            var tooltipHeight = tooltip.outerHeight();
            var spacing = 20; // Spacing between tooltip and target

            // Get viewport dimensions and scroll position for fixed positioning
            var windowWidth = $(window).width();
            var windowHeight = $(window).height();
            var scrollTop = $(window).scrollTop();
            var scrollLeft = $(window).scrollLeft();

            // Convert document-relative offset() to viewport-relative for fixed positioning
            var viewportTop = targetOffset.top - scrollTop;
            var viewportLeft = targetOffset.left - scrollLeft;
            
            var position = {
                top: 0,
                left: 0,
                arrowPosition: null // Track arrow position for CSS
            };

            // Calculate position relative to viewport (for fixed positioning) or modal (if modal is open)
            var arrowOffset = 0; // Track arrow position for CSS
            var useModalPositioning = isModalOpen && modalContainer && targetInModal && targetInModal.length > 0;
            
            if (useModalPositioning) {
                // Find the best container for positioning (modal-content is preferred)
                var modalContent = modalContainer.find('.modal-content').first();
                var modalDialog = modalContainer.find('.modal-dialog').first();
                var modalBody = modalContainer.find('.modal-body').first();
                
                // Use modal-content if available, otherwise modal-dialog, otherwise modal itself
                var positioningContainer = modalContent.length > 0 ? modalContent : (modalDialog.length > 0 ? modalDialog : modalContainer);
                
                // Get container offset for relative positioning
                var containerOffset = positioningContainer.offset();
                var containerPosition = positioningContainer.position();
                
                // Calculate target position relative to container
                // Use position() if container is the parent, otherwise use offset() difference
                var relativeTop, relativeLeft;
                if (target.closest(positioningContainer).length > 0 && positioningContainer.find(target).length > 0) {
                    // Target is inside container, use position relative to container
                    var targetPosition = target.position();
                    relativeTop = targetPosition.top;
                    relativeLeft = targetPosition.left;
                } else {
                    // Calculate relative to container offset
                    relativeTop = targetOffset.top - containerOffset.top;
                    relativeLeft = targetOffset.left - containerOffset.left;
                }
                
                var containerWidth = positioningContainer.outerWidth();
                var containerHeight = positioningContainer.outerHeight();
                
                switch (step.position) {
                    case 'top':
                        position.top = relativeTop - tooltipHeight - spacing;
                        position.left = relativeLeft + (targetWidth / 2) - (tooltipWidth / 2);
                        position.arrowPosition = 'bottom';
                        arrowOffset = (relativeLeft + targetWidth / 2) - position.left;
                        break;
                    case 'bottom':
                        position.top = relativeTop + targetHeight + spacing;
                        position.left = relativeLeft + (targetWidth / 2) - (tooltipWidth / 2);
                        position.arrowPosition = 'top';
                        arrowOffset = (relativeLeft + targetWidth / 2) - position.left;
                        break;
                    case 'left':
                        position.top = relativeTop + (targetHeight / 2) - (tooltipHeight / 2);
                        position.left = relativeLeft - tooltipWidth - spacing;
                        position.arrowPosition = 'right';
                        arrowOffset = (relativeTop + targetHeight / 2) - position.top;
                        break;
                    case 'right':
                        position.top = relativeTop + (targetHeight / 2) - (tooltipHeight / 2);
                        position.left = relativeLeft + targetWidth + spacing;
                        position.arrowPosition = 'left';
                        arrowOffset = (relativeTop + targetHeight / 2) - position.top;
                        break;
                }
                
                // Adjust position to keep tooltip within container bounds
                var minLeft = 10;
                var maxLeft = containerWidth - tooltipWidth - 10;
                var adjustedArrowOffset;
                if (position.left < minLeft) {
                    adjustedArrowOffset = arrowOffset - (minLeft - position.left);
                    position.left = minLeft;
                } else if (position.left > maxLeft) {
                    var overflow = position.left - maxLeft;
                    adjustedArrowOffset = arrowOffset + overflow;
                    position.left = maxLeft;
                } else {
                    adjustedArrowOffset = arrowOffset;
                }
                
                // Vertical adjustments within container
                var minTop = 10;
                var maxTop = containerHeight - tooltipHeight - 10;
                if (position.top < minTop) {
                    position.top = minTop;
                } else if (position.top > maxTop) {
                    if (step.position === 'bottom') {
                        position.top = relativeTop - tooltipHeight - spacing;
                        position.arrowPosition = 'bottom';
                        tooltip.removeClass('tutorial-arrow-top').addClass('tutorial-arrow-bottom');
                        adjustedArrowOffset = (relativeLeft + targetWidth / 2) - position.left;
                    } else {
                        position.top = maxTop;
                    }
                }
            } else {
                // Position relative to viewport (normal case)
                switch (step.position) {
                    case 'top':
                        position.top = viewportTop - tooltipHeight - spacing;
                        position.left = viewportLeft + (targetWidth / 2) - (tooltipWidth / 2);
                        position.arrowPosition = 'bottom';
                        arrowOffset = (viewportLeft + targetWidth / 2) - position.left;
                        break;
                    case 'bottom':
                        position.top = viewportTop + targetHeight + spacing;
                        position.left = viewportLeft + (targetWidth / 2) - (tooltipWidth / 2);
                        position.arrowPosition = 'top';
                        arrowOffset = (viewportLeft + targetWidth / 2) - position.left;
                        break;
                    case 'left':
                        position.top = viewportTop + (targetHeight / 2) - (tooltipHeight / 2);
                        position.left = viewportLeft - tooltipWidth - spacing;
                        position.arrowPosition = 'right';
                        arrowOffset = (viewportTop + targetHeight / 2) - position.top;
                        break;
                    case 'right':
                        position.top = viewportTop + (targetHeight / 2) - (tooltipHeight / 2);
                        position.left = viewportLeft + targetWidth + spacing;
                        position.arrowPosition = 'left';
                        arrowOffset = (viewportTop + targetHeight / 2) - position.top;
                        break;
                }
                
                // Adjust position to keep tooltip within viewport
                var adjustedArrowOffset = arrowOffset;
                if (position.left < 10) {
                    adjustedArrowOffset = arrowOffset - (10 - position.left);
                    position.left = 10;
                } else if (position.left + tooltipWidth > windowWidth - 10) {
                    var overflow = (position.left + tooltipWidth) - (windowWidth - 10);
                    adjustedArrowOffset = arrowOffset + overflow;
                    position.left = windowWidth - tooltipWidth - 10;
                }
                
                if (position.top < 10) {
                    position.top = 10;
                } else if (position.top + tooltipHeight > windowHeight - 10) {
                    if (step.position === 'bottom') {
                        position.top = viewportTop - tooltipHeight - spacing;
                        position.arrowPosition = 'bottom';
                        tooltip.removeClass('tutorial-arrow-top').addClass('tutorial-arrow-bottom');
                        adjustedArrowOffset = (viewportLeft + targetWidth / 2) - position.left;
                    } else {
                        position.top = windowHeight - tooltipHeight - 10;
                    }
                }
            }
            
            // Store arrow offset for CSS positioning
            tooltip.data('arrow-offset', adjustedArrowOffset);
            
            // Remove any existing arrow classes first
            tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
            
            // Add arrow class to tooltip for CSS styling
            if (position.arrowPosition) {
                tooltip.addClass('tutorial-arrow-' + position.arrowPosition);
            }
            
            // Update arrow offset data attribute and CSS variable
            tooltip.css('--arrow-offset', adjustedArrowOffset + 'px');
            
            // Ensure arrow doesn't go outside tooltip bounds (min 20px from edges)
            var minArrowOffset = 20;
            var maxArrowOffset = tooltipWidth - 20;
            if (adjustedArrowOffset < minArrowOffset) {
                tooltip.css('--arrow-offset', minArrowOffset + 'px');
            } else if (adjustedArrowOffset > maxArrowOffset) {
                tooltip.css('--arrow-offset', maxArrowOffset + 'px');
            }

            // Set positioning based on whether modal is open
            if (useModalPositioning) {
                // Find the best container for appending tooltip
                var modalContent = modalContainer.find('.modal-content').first();
                var modalDialog = modalContainer.find('.modal-dialog').first();
                var modalBody = modalContainer.find('.modal-body').first();
                
                // Prefer modal-content, then modal-dialog, then modal-body, then modal itself
                var appendContainer = modalContent.length > 0 ? modalContent : 
                                     (modalDialog.length > 0 ? modalDialog : 
                                     (modalBody.length > 0 ? modalBody : modalContainer));
                
                // Only append if not already in the right place
                if (!tooltip.parent().is(appendContainer)) {
                    tooltip.appendTo(appendContainer);
                }
                
                // Position relative to container
                tooltip.css({
                    position: 'absolute',
                    top: position.top + 'px',
                    left: position.left + 'px',
                    zIndex: 10000 // Above modal
                });
            } else {
                // Ensure tooltip is in body (not modal)
                var parent = tooltip.parent();
                if (parent.hasClass('modal') || parent.hasClass('modal-content') || 
                    parent.hasClass('modal-body') || parent.hasClass('modal-dialog') ||
                    parent.closest('.modal').length > 0) {
                    tooltip.appendTo('body');
                }
                
                // Fixed positioning for viewport-relative placement
                tooltip.css({
                    position: 'fixed',
                    top: position.top + 'px',
                    left: position.left + 'px',
                    zIndex: 1041 // Below Bootstrap modals (1050) but above overlay
                });
            }
        },

        /**
         * Highlight target element
         * @param {string} selector - CSS selector
         */
        highlightElement: function(selector) {
            var element = $(selector);
            if (element.length === 0) {
                return;
            }

            this.state.targetElement = element;

            // Remove any existing highlight first with fade out
            var existingHighlight = $('.tutorial-highlight');
            if (existingHighlight.length > 0) {
                existingHighlight.css({
                    transition: 'all 0.3s ease-out'
                });
                setTimeout(function() {
                    existingHighlight.removeClass('tutorial-highlight');
                }, 100);
            }

            // Add highlight class with fade in animation
            setTimeout(function() {
                element.css({
                    transition: 'all 0.3s ease-out'
                });
                element.addClass('tutorial-highlight');
            }, 150);

            // Scroll element into view if needed - use native scrollIntoView for better control
            var elementOffset = element.offset();
            var elementHeight = element.outerHeight();
            var elementWidth = element.outerWidth();
            var windowHeight = $(window).height();
            var windowWidth = $(window).width();
            var scrollTop = $(window).scrollTop();
            var scrollLeft = $(window).scrollLeft();

            // Check if element is fully visible in viewport
            var isFullyVisible = (
                elementOffset.top >= scrollTop &&
                elementOffset.top + elementHeight <= scrollTop + windowHeight &&
                elementOffset.left >= scrollLeft &&
                elementOffset.left + elementWidth <= scrollLeft + windowWidth
            );

            if (!isFullyVisible) {
                // Use scrollIntoView with smooth behavior and proper alignment
                element[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                    inline: 'center'
                });
            }
        },

        /**
         * Bind tooltip events
         * @param {object} step - Step configuration
         * @param {number} stepIndex - Current step index
         */
        bindTooltipEvents: function(step, stepIndex) {
            var self = this;

            // Close button
            this.state.tooltip.find('.tutorial-close').on('click', function() {
                self.skip();
            });

            // Back button
            this.state.tooltip.find('.tutorial-btn-back').on('click', function() {
                self.previous();
            });

            // Next button
            this.state.tooltip.find('.tutorial-btn-next').on('click', function() {
                if (step.isLast) {
                    self.complete();
                } else {
                    self.next();
                }
            });

            // Skip button
            this.state.tooltip.find('.tutorial-btn-skip').on('click', function() {
                self.skip();
            });
        },

        /**
         * Go to next step
         */
        next: function() {
            var nextIndex = this.state.currentStepIndex + 1;
            // Ensure we don't go beyond the last step
            if (nextIndex >= this.config.steps.length) {
                // Show the last step (completion)
                nextIndex = this.config.steps.length - 1;
            }
            this.showStep(nextIndex);
        },

        /**
         * Go to previous step
         */
        previous: function() {
            this.showStep(this.state.currentStepIndex - 1);
        },

        /**
         * Skip tutorial
         */
        skip: function() {
            this.dismiss(true); // Dismiss with "don't show again"
        },

        /**
         * Complete tutorial
         */
        complete: function() {
            this.dismiss(false);
        },

        /**
         * Dismiss tutorial
         * @param {boolean} dontShowAgain - Whether to hide permanently
         */
        dismiss: function(dontShowAgain) {
            // Set inactive first to ensure cleanup happens
            this.state.isActive = false;
            
            // Remove class from body to allow sidebar closing again
            $('body').removeClass('tutorial-active');
            
            // UPDATED: Remove resize handler when tutorial ends
            this.removeResizeHandler();
            
            // Immediately remove all tutorial elements
            this.removeCurrentStep();
            
            // Force remove overlay and tooltip if they still exist (immediate removal, no animation)
            if (this.state.overlay) {
                this.state.overlay.stop(true, true); // Stop any animations
                this.state.overlay.remove();
                this.state.overlay = null;
            }
            if (this.state.tooltip) {
                this.state.tooltip.stop(true, true); // Stop any animations
                this.state.tooltip.remove();
                this.state.tooltip = null;
            }
            
            // Remove any remaining highlights
            $('.tutorial-highlight').removeClass('tutorial-highlight');
            
            // Remove any tutorial overlays/tooltips that might still exist in DOM
            $('.tutorial-overlay').remove();
            $('.tutorial-tooltip').remove();
            
            // Ensure body is scrollable and interactive
            $('body').css({
                'overflow': '',
                'pointer-events': ''
            });
            
            // Remove any inline styles that might block interaction
            $('html').css({
                'overflow': '',
                'pointer-events': ''
            });

            // Save preference
            if (dontShowAgain) {
                localStorage.setItem(this.config.storageKeyDismissed, 'true');
                
                // Save to server
                $.ajax({
                    url: admin_url + 'ella_contractors/presentations/save_tutorial_preference',
                    type: 'POST',
                    data: {
                        dismissed: 1,
                        [csrf_token_name]: csrf_hash
                    },
                    dataType: 'json'
                });
            } else {
                // Mark as completed but allow restart
                localStorage.setItem(this.config.storageKey, 'true');
            }
        },

        /**
         * Remove current step elements
         */
        removeCurrentStep: function() {
            // Remove highlight immediately
            if (this.state.targetElement) {
                this.state.targetElement.removeClass('tutorial-highlight');
                this.state.targetElement = null;
            }

            // Remove tooltip immediately
            if (this.state.tooltip) {
                this.state.tooltip.remove();
                this.state.tooltip = null;
            }

            // Remove overlay immediately if tutorial is not active (dismissing)
            if (!this.state.isActive) {
                if (this.state.overlay) {
                    this.state.overlay.remove();
                    this.state.overlay = null;
                }
            }
        },

        /**
         * Restart tutorial (for manual restart)
         */
        restart: function() {
            // Ensure tutorial-active class is set before restarting
            $('body').addClass('tutorial-active');
            
            localStorage.removeItem(this.config.storageKey);
            localStorage.removeItem(this.config.storageKeyDismissed);
            this.start();
        }
    };

    // Auto-initialize when DOM is ready
    $(document).ready(function() {
        // Only initialize if on presentations page
        if ($('.table-ella_presentations').length > 0 || $('[data-target="#uploadPresentationModal"]').length > 0) {
            PresentationTutorial.init();
        }
    });

    // Expose globally for manual control
    window.PresentationTutorial = PresentationTutorial;

})(jQuery);
