/**
 * EllaContractors Appointment Tutorial System
 * 
 * Provides step-by-step guided tours for first-time users
 * Supports "Don't show again" functionality with persistence
 * Fully responsive with dynamic positioning across all screen sizes
 * 
 * @version 2.0.0
 * @author EllaContractors Team
 */

(function($) {
    'use strict';

    /**
     * Tutorial Manager Class
     */
    var AppointmentTutorial = {
        // Configuration
        config: {
            storageKey: 'ella_contractors_tutorial_completed',
            storageKeyDismissed: 'ella_contractors_tutorial_dismissed',
            tutorialId: 'appointments_tutorial',
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
            $(document).off('show.bs.modal.tutorial hidden.bs.modal.tutorial');
            $(document).on('show.bs.modal.tutorial', function() {
                // Hide tutorial overlay when modals open
                if (self.state.overlay) {
                    self.state.overlay.css('display', 'none');
                }
                if (self.state.tooltip) {
                    self.state.tooltip.css('display', 'none');
                }
            });

            $(document).on('hidden.bs.modal.tutorial', function() {
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
                        AppointmentTutorial.start();
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
                url: admin_url + 'ella_contractors/appointments/check_tutorial_status',
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
                    title: 'Welcome to Appointments',
                    content: 'Welcome to the EllaContractors Appointments module! This quick tour will help you get started with managing appointments efficiently.',
                    target: null, // No specific target for welcome
                    position: 'center', // Center of screen
                    showNext: true,
                    showBack: false,
                    showSkip: true
                },
                {
                    id: 'new_appointment_button',
                    title: 'Create New Appointment',
                    content: 'Click the "New Appointment" button to create a new appointment. You can schedule appointments with leads or clients, set reminders, and add attendees.',
                    target: '#new-appointment',
                    position: 'bottom', // Tooltip appears below button
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'filter_dropdown',
                    title: 'Filter Appointments',
                    content: 'Use the filter dropdown to view appointments by status (Scheduled, Complete, Cancelled) or by date range (Today, This Week, This Month).',
                    target: '.panel-body .btn-group:last-child .dropdown-toggle',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                },
                {
                    id: 'calendar_button',
                    title: 'View Calendar',
                    content: 'Click the calendar icon to view all your appointments in a calendar view. This helps you visualize your schedule at a glance.',
                    target: '#open-calendar-modal',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'google_calendar',
                    title: 'Google Calendar Sync',
                    content: 'Sync your appointments with Google Calendar to keep all your schedules in one place. Click the Google Calendar icon to connect your account and automatically sync appointments both ways.',
                    target: '.btn-group.btn-with-tooltip-group.mright5',
                    position: 'bottom', // Tooltip below target, arrow points up
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'status_column',
                    title: 'Appointment Status',
                    content: 'Click on any status badge to quickly change the appointment status. Statuses include Scheduled, Complete, and Cancelled.',
                    target: '.status-button',
                    position: 'left',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true,
                    optional: true // This step is optional if element doesn't exist
                },
                {
                    id: 'appointment_actions',
                    title: 'Appointment Actions',
                    content: 'Use the action buttons in each row to edit appointment details, attach presentations, add notes, send reminders, or delete appointments. Hover over icons to see what each action does.',
                    target: '.table-ella_appointments',
                    position: 'right', // Tooltip to the right, arrow points left
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'appointments_table',
                    title: 'Appointments Table',
                    content: 'This table shows all your appointments. You can sort by any column, search for specific appointments, and use bulk actions to manage multiple appointments at once.',
                    target: '.table-ella_appointments',
                    position: 'top',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    waitForElement: true // Wait for table to load
                },
                {
                    id: 'completion',
                    title: 'Tutorial Complete!',
                    content: 'You\'re all set! You can now create, manage, and track appointments efficiently. If you need help anytime, look for the help icon or contact support.',
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
                    AppointmentTutorial.renderStep(step, stepIndex);
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
            
            // UPDATED: Check if element is already visible
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

            // NEW: Scroll to top for Google Calendar step
            if (step.id === 'google_calendar') {
                $('.content').animate({ scrollTop: 0 }, 300);
                // Also scroll window to top
                $('html, body').animate({ scrollTop: 0 }, 300);
            }

            // Create overlay
            this.createOverlay();

            // Create tooltip
            this.createTooltip(step, stepIndex);

            // Initially hide tooltip for fade-in animation (already set in createTooltip)
            this.state.tooltip.css({
                opacity: 0,
                transform: 'scale(0.95)'
            });

            // If target element exists, wait for it to be visible, then position
            if (step.target && step.position !== 'center') {
                var target = $(step.target);
                
                // Wait a bit for element to be fully rendered
                setTimeout(function() {
                    // Scroll element into view first if needed
                    if (step.highlight) {
                        self.highlightElement(step.target);
                    }
                    
                    // Small delay to allow scroll animation to complete
                    setTimeout(function() {
                        self.positionTooltip(step);
                        // Make visible and fade in tooltip with animation
                        self.state.tooltip.css({
                            visibility: 'visible',
                            opacity: 1,
                            transform: 'scale(1)',
                            transition: 'opacity 0.3s ease-out, transform 0.3s ease-out'
                        });
                    }, 350);
                }, 100);
            } else {
                // No target or center position - position immediately
                this.positionTooltip(step);
                // Make visible and fade in tooltip with animation
                setTimeout(function() {
                    self.state.tooltip.css({
                        visibility: 'visible',
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
                tooltipHtml += '<i class="fa fa-lock" style="margin-right: 5px;"></i>';
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
            if (step.isLast) {
                tooltipHtml += '<div class="tutorial-dont-show">';
                tooltipHtml += '<label>';
                tooltipHtml += '<input type="checkbox" id="tutorial-dont-show-again"> ';
                tooltipHtml += "Don't show me this again";
                tooltipHtml += '</label>';
                tooltipHtml += '<button type="button" class="btn btn-default tutorial-btn-close" style="margin-left: 15px;">Close</button>';
                tooltipHtml += '</div>';
            }

            tooltipHtml += '</div>';
            tooltipHtml += '</div>';

            this.state.tooltip = $(tooltipHtml);
            
            // Set initial positioning immediately to prevent tooltip from appearing at default position
            // Position off-screen until final position is calculated
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
         * @param {object} step - Step configuration
         * @param {boolean} skipTransition - Skip transition animation (for resize)
         */
        positionTooltip: function(step, skipTransition) {
            var tooltip = this.state.tooltip;
            var self = this;
            
            // CUSTOM POSITIONING: Responsive override for specific steps
            // Get viewport width for responsive positioning
            var viewportWidth = $(window).width();
            var viewportHeight = $(window).height();
            
            if (step.id === 'new_appointment_button') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-top'); // Arrow points up
                
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
                    positions = { top: '145px', left: '6.87622%' };
                } else if (viewportWidth >= 1800) {
                    // Large screens (1800px - 1919px)
                    var topPos = viewportHeight > 900 ? '165px' : (viewportHeight * 0.19) + 'px';
                    positions = { top: topPos, left: '5%' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1799px)
                    var topPos = viewportHeight > 900 ? '170px' : (viewportHeight * 0.20) + 'px';
                    positions = { top: topPos, left: '4%' };
                } else if (viewportWidth >= 1440) {
                    // Desktop screens (1440px - 1599px)
                    var topPos = viewportHeight > 900 ? '175px' : (viewportHeight * 0.20) + 'px';
                    positions = { top: topPos, left: '3.5%' };
                } else if (viewportWidth >= 1370) {
                    // Medium screens (1370px - 1439px)
                    var topPos = viewportHeight > 900 ? '180px' : (viewportHeight * 0.21) + 'px';
                    positions = { top: topPos, left: '3%' };
                } else if (viewportWidth >= 1280) {
                    // Laptop screens (1280px - 1369px)
                    positions = { top: '140px', left: '3.5%' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1279px)
                    positions = { top: '140px', left: '4%' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '140px', left: '3%' };
                } else {
                    // Mobile and small tablets (below 768px)
                    var topPos = viewportHeight > 600 ? '130px' : '130px';
                    positions = { top: topPos, left: '50%', transform: 'translateX(-50%)' };
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
            
            if (step.id === 'filter_dropdown') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-top'); // Arrow points up
                
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
                    positions = { top: '145px', left: '82.2591%' };
                } else if (viewportWidth >= 1800) {
                    // Large screens (1800px - 1919px)
                    var topPos = viewportHeight > 900 ? '165px' : (viewportHeight * 0.19) + 'px';
                    positions = { top: topPos, left: '81%' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1799px)
                    var topPos = viewportHeight > 900 ? '170px' : (viewportHeight * 0.20) + 'px';
                    positions = { top: topPos, left: '80%' };
                } else if (viewportWidth >= 1440) {
                    // Desktop screens (1440px - 1599px)
                    var topPos = viewportHeight > 900 ? '175px' : (viewportHeight * 0.20) + 'px';
                    positions = { top: topPos, left: '79%' };
                } else if (viewportWidth >= 1370) {
                    // Medium screens (1370px - 1439px)
                    var topPos = viewportHeight > 900 ? '180px' : (viewportHeight * 0.21) + 'px';
                    positions = { top: topPos, left: '78%' };
                } else if (viewportWidth >= 1280) {
                    // Laptop screens (1280px - 1369px)
                    positions = { top: '140px', left: '76.5%' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1279px)
                    positions = { top: '140px', left: '75%' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '140px', left: '70%' };
                } else {
                    // Mobile and small tablets (below 768px)
                    var topPos = viewportHeight > 600 ? '200px' : '200px';
                    positions = { top: topPos, left: '50%', transform: 'translateX(-50%)' };
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
            
            if (step.id === 'calendar_button') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-top'); // Arrow points up
                
                // Temporarily make tooltip visible off-screen for dimension calculation
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
                
                // Calculate left position from target element (original behavior - centered on target)
                var target = $(step.target);
                var leftPosition;
                if (target.length > 0 && target.is(':visible')) {
                    var targetOffset = target.offset();
                    var targetWidth = target.outerWidth();
                    var tooltipWidth = tooltip.outerWidth() || tooltip[0].offsetWidth || 300;
                    var scrollLeft = $(window).scrollLeft();
                    var windowWidth = $(window).width();
                    var viewportLeft = targetOffset.left - scrollLeft;
                    // Calculate left position in pixels (centered on target)
                    var leftPx = viewportLeft + (targetWidth / 2) - (tooltipWidth / 2);
                    // Convert to percentage
                    leftPosition = ((leftPx / windowWidth) * 100);
                    leftPosition = Math.max(1, Math.min(99, leftPosition));
                    leftPosition = leftPosition + '%';
                } else {
                    // Fallback to center if target not found
                    leftPosition = '50%';
                }
                
                // Responsive top positioning (145px on large screens)
                var topPosition;
                if (viewportWidth >= 1920) {
                    topPosition = '145px';
                } else if (viewportWidth >= 1800) {
                    topPosition = viewportHeight > 900 ? '165px' : (viewportHeight * 0.19) + 'px';
                } else if (viewportWidth >= 1600) {
                    topPosition = viewportHeight > 900 ? '170px' : (viewportHeight * 0.20) + 'px';
                } else if (viewportWidth >= 1440) {
                    topPosition = viewportHeight > 900 ? '175px' : (viewportHeight * 0.20) + 'px';
                } else if (viewportWidth >= 1370) {
                    topPosition = viewportHeight > 900 ? '180px' : (viewportHeight * 0.21) + 'px';
                } else if (viewportWidth >= 1280) {
                    topPosition = '140px';
                } else if (viewportWidth >= 1024) {
                    topPosition = '140px';
                } else if (viewportWidth >= 768) {
                    topPosition = '140px';
                } else {
                    topPosition = viewportHeight > 600 ? '200px' : '200px';
                    leftPosition = '50%'; // Center on mobile
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: topPosition,
                    left: leftPosition,
                    transform: viewportWidth < 768 ? 'translateX(-50%)' : 'none',
                    zIndex: 1041
                });
                
                // Restore visibility state
                if (wasHidden) {
                    tooltip.css({
                        visibility: 'hidden',
                        opacity: 0
                    });
                }
                
                return;
            }
            
            if (step.id === 'appointment_actions') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-left'); // Arrow points left
                
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
                    // Large screens (1920px and above)
                    positions = { top: '20%', left: '35%' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1919px)
                    positions = { top: '20%', left: '33%' };
                } else if (viewportWidth >= 1366) {
                    // Medium screens (1366px - 1599px)
                    positions = { top: '21%', left: '30%' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1365px)
                    positions = { top: '22%', left: '28%' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '23%', left: '25%' };
                } else {
                    // Mobile and small tablets (below 768px)
                    positions = { top: '25%', left: '50%', transform: 'translateX(-50%)' };
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
            
            if (step.id === 'google_calendar') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-top'); // Arrow points up
                
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
                    positions = { top: '145px', left: '76%' };
                } else if (viewportWidth >= 1800) {
                    // Large screens (1800px - 1919px)
                    positions = { top: '165px', left: '75%' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1799px)
                    positions = { top: '170px', left: '74%' };
                } else if (viewportWidth >= 1440) {
                    // Desktop screens (1440px - 1599px)
                    positions = { top: '175px', left: '73%' };
                } else if (viewportWidth >= 1370) {
                    // Medium screens (1370px - 1439px)
                    positions = { top: '180px', left: '72%' };
                } else if (viewportWidth >= 1280) {
                    // Laptop screens (1280px - 1369px)
                    positions = { top: '140px', left: '70%' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1279px)
                    positions = { top: '140px', left: '68%' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '140px', left: '61%' };
                } else {
                    // Mobile and small tablets (below 768px)
                    positions = { top: '200px', left: '50%', transform: 'translateX(-50%)' };
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
            
            if (step.id === 'status_column') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-right'); // Arrow points right
                
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
                
                // Responsive positioning for status column tooltip across all media screens
                var positions;
                if (viewportWidth >= 1920) {
                    // Extra large screens (1920px and above)
                    positions = { top: '203.891px', left: '49.1535%' };
                } else if (viewportWidth >= 1800) {
                    // Large screens (1800px - 1919px)
                    positions = { top: '200px', left: '47%' };
                } else if (viewportWidth >= 1600) {
                    // Medium-large screens (1600px - 1799px)
                    positions = { top: '195px', left: '40.5%' };
                } else if (viewportWidth >= 1440) {
                    // Desktop screens (1440px - 1599px)
                    positions = { top: '190px', left: '37%' };
                } else if (viewportWidth >= 1366) {
                    // Medium screens (1366px - 1439px)
                    positions = { top: '185px', left: '35.5%' };
                } else if (viewportWidth >= 1280) {
                    // Laptop screens (1280px - 1365px)
                    positions = { top: '180px', left: '35%' };
                } else if (viewportWidth >= 1024) {
                    // Small-medium screens (1024px - 1279px)
                    positions = { top: '175px', left: '34.5%' };
                } else if (viewportWidth >= 768) {
                    // Tablet landscape (768px - 1023px)
                    positions = { top: '170px', left: '32.5%', transform: 'translateX(-50%)' };
                } else {
                    // Mobile and small tablets (below 768px)
                    positions = { top: '165px', left: '50%', transform: 'translateX(-50%)' };
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
            // Retry finding the element with multiple attempts
            var target = null;
            var attempts = 0;
            var maxAttempts = 10;
            
            if (step.target) {
                while (attempts < maxAttempts && (!target || target.length === 0 || !target.is(':visible'))) {
                    target = $(step.target);
                    if (target.length > 0 && target.is(':visible') && target.outerWidth() > 0 && target.outerHeight() > 0) {
                        break;
                    }
                    attempts++;
                    if (attempts < maxAttempts) {
                        // Wait a bit before retrying
                        var start = new Date().getTime();
                        while (new Date().getTime() - start < 50) {
                            // Busy wait
                        }
                    }
                }
            }
            
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
            if (!target || target.length === 0 || !target.is(':visible') || target.outerWidth() === 0 || target.outerHeight() === 0) {
                // Target not found or not visible - retry positioning after a short delay
                if (attempts < maxAttempts) {
                    setTimeout(function() {
                        self.positionTooltip(step);
                    }, 100);
                    return;
                }
                
                // Fallback to center if target still not found after retries
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

            // Get tooltip dimensions - ensure they're calculated
            var tooltipWidth = tooltip.outerWidth();
            var tooltipHeight = tooltip.outerHeight();
            
            // If dimensions are 0, force calculation by temporarily making visible
            if (tooltipWidth === 0 || tooltipHeight === 0) {
                tooltip.css({ visibility: 'hidden', display: 'block', position: 'fixed', top: '-9999px' });
                tooltipWidth = tooltip.outerWidth();
                tooltipHeight = tooltip.outerHeight();
                tooltip.css({ visibility: 'visible', top: '', left: '' });
            }
            
            // Ensure target is in viewport before positioning
            var targetOffset = target.offset();
            var targetWidth = target.outerWidth();
            var targetHeight = target.outerHeight();
            
            // Scroll target into view if needed (smooth scroll)
            var windowHeight = $(window).height();
            var windowWidth = $(window).width();
            var scrollTop = $(window).scrollTop();
            var scrollLeft = $(window).scrollLeft();
            
            var targetTop = targetOffset.top;
            var targetBottom = targetTop + targetHeight;
            var targetLeft = targetOffset.left;
            var targetRight = targetLeft + targetWidth;
            
            // Check if target is fully visible, if not scroll it into view
            if (targetTop < scrollTop || targetBottom > scrollTop + windowHeight || 
                targetLeft < scrollLeft || targetRight > scrollLeft + windowWidth) {
                // Use native scrollIntoView for smooth scrolling
                var targetElement = target[0];
                if (targetElement && targetElement.scrollIntoView) {
                    targetElement.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
                    // Wait for scroll to complete before positioning
                    var self = this;
                    setTimeout(function() {
                        targetOffset = target.offset();
                        targetWidth = target.outerWidth();
                        targetHeight = target.outerHeight();
                        self.calculateAndSetPosition(step, target, targetOffset, targetWidth, targetHeight, tooltip, tooltipWidth, tooltipHeight, targetInModal);
                    }, 300);
                    return;
                }
            }
            
            // Calculate and set position immediately
            this.calculateAndSetPosition(step, target, targetOffset, targetWidth, targetHeight, tooltip, tooltipWidth, tooltipHeight, targetInModal);
        },

        /**
         * Calculate and set tooltip position
         * @param {object} step - Step configuration
         * @param {jQuery} target - Target element
         * @param {object} targetOffset - Target offset
         * @param {number} targetWidth - Target width
         * @param {number} targetHeight - Target height
         * @param {jQuery} tooltip - Tooltip element
         * @param {number} tooltipWidth - Tooltip width
         * @param {number} tooltipHeight - Tooltip height
         * @param {jQuery} targetInModal - Target's modal container if any
         */
        calculateAndSetPosition: function(step, target, targetOffset, targetWidth, targetHeight, tooltip, tooltipWidth, tooltipHeight, targetInModal) {
            // UPDATED: Enhanced responsive spacing across all screen sizes
            var windowWidth = $(window).width();
            var spacing;
            if (windowWidth >= 1920) {
                spacing = 15; // Large screens
            } else if (windowWidth >= 1600) {
                spacing = 12; // Medium-large screens
            } else if (windowWidth >= 1366) {
                spacing = 12; // Medium screens
            } else if (windowWidth >= 1024) {
                spacing = 10; // Small-medium screens (laptops)
            } else if (windowWidth >= 768) {
                spacing = 10; // Tablet landscape
            } else if (windowWidth >= 480) {
                spacing = 8; // Tablet portrait
            } else {
                spacing = 5; // Mobile
            }

            // Check if a modal is currently open
            var $openModal = $('.modal.in, .modal.show, .modal[style*="display: block"]');
            if ($openModal.length === 0) {
                if ($('body').hasClass('modal-open')) {
                    $openModal = $('.modal').filter(function() {
                        return $(this).css('display') !== 'none';
                    });
                }
            }
            
            var modalContainer = targetInModal && targetInModal.length > 0 ? targetInModal : ($openModal.length > 0 ? $openModal.first() : null);
            var isModalOpen = modalContainer && modalContainer.length > 0;

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
                
                switch (step.position)
                
                {
                    
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
                
                // UPDATED: Responsive bounds checking for modal positioning
                var containerMargin = windowWidth >= 768 ? 10 : 5; // Smaller margins on mobile
                var minLeft = containerMargin;
                var maxLeft = containerWidth - tooltipWidth - containerMargin;
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
                var minTop = containerMargin;
                var maxTop = containerHeight - tooltipHeight - containerMargin;
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
                
                // UPDATED: Responsive viewport bounds checking - adjust margins based on screen size
                var viewportMargin = windowWidth >= 768 ? 10 : 5; // Smaller margins on mobile
                var adjustedArrowOffset = arrowOffset;
                
                if (position.left < viewportMargin) {
                    adjustedArrowOffset = arrowOffset - (viewportMargin - position.left);
                    position.left = viewportMargin;
                } else if (position.left + tooltipWidth > windowWidth - viewportMargin) {
                    var overflow = (position.left + tooltipWidth) - (windowWidth - viewportMargin);
                    adjustedArrowOffset = arrowOffset + overflow;
                    position.left = windowWidth - tooltipWidth - viewportMargin;
                }
                
                if (position.top < viewportMargin) {
                    position.top = viewportMargin;
                } else if (position.top + tooltipHeight > windowHeight - viewportMargin) {
                    if (step.position === 'bottom') {
                        position.top = viewportTop - tooltipHeight - spacing;
                        position.arrowPosition = 'bottom';
                        tooltip.removeClass('tutorial-arrow-top').addClass('tutorial-arrow-bottom');
                        adjustedArrowOffset = (viewportLeft + targetWidth / 2) - position.left;
                    } else {
                        position.top = windowHeight - tooltipHeight - viewportMargin;
                    }
                }
            }
            
            // UPDATED: Enhanced percentage-based positioning for responsive behavior across all screen sizes
            var leftPercent = null;
            var topPercent = null;
            var usePercentPositioning = false;
            
            // Convert pixel position to percentage for better responsiveness on all screen sizes
            if (position.left !== undefined && !useModalPositioning) {
                leftPercent = (position.left / windowWidth) * 100;
                usePercentPositioning = true;
            }
            if (position.top !== undefined && !useModalPositioning) {
                topPercent = (position.top / windowHeight) * 100;
                // For better mobile experience, cap top percentage
                if (windowWidth < 768 && topPercent < 5) {
                    topPercent = 5;
                }
            }
            
            // Store arrow offset for CSS positioning
            tooltip.data('arrow-offset', adjustedArrowOffset);
            
            // UPDATED: Clean up existing arrow classes before adding new ones
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
                
                // UPDATED: Responsive fixed positioning for viewport-relative placement
                // Use percentage for horizontal positioning for better responsiveness across all screen sizes
                var cssPosition = {
                    position: 'fixed',
                    zIndex: 1041 // Below Bootstrap modals (1050) but above overlay
                };
                
                // Apply top position (keep as pixels for better vertical precision)
                cssPosition.top = position.top + 'px';
                
                // Apply left position (use percentage on larger screens, pixels on mobile for precision)
                if (usePercentPositioning && leftPercent !== null && windowWidth >= 768) {
                    // Use percentage on tablets and above for fluid responsiveness
                    cssPosition.left = leftPercent + '%';
                } else {
                    // Use pixels on mobile for better control
                    cssPosition.left = position.left + 'px';
                }
                
                // On very small screens, ensure tooltip doesn't overflow
                if (windowWidth < 480) {
                    cssPosition.maxWidth = (windowWidth - 20) + 'px';
                }
                
                tooltip.css(cssPosition);
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
                    // Check if "Don't show again" is checked
                    var dontShow = $('#tutorial-dont-show-again').is(':checked');
                    self.complete(dontShow);
                } else {
                    self.next();
                }
            });

            // Skip button
            this.state.tooltip.find('.tutorial-btn-skip').on('click', function() {
                self.skip();
            });

            // Close button (on completion step)
            this.state.tooltip.find('.tutorial-btn-close').on('click', function() {
                // Check if "Don't show again" is checked
                var dontShow = $('#tutorial-dont-show-again').is(':checked');
                self.complete(dontShow);
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
        complete: function(dontShowAgain) {
            // If parameter not provided, check checkbox
            if (dontShowAgain === undefined) {
                dontShowAgain = $('#tutorial-dont-show-again').is(':checked');
            }
            this.dismiss(dontShowAgain);
        },

        /**
         * Dismiss tutorial
         * @param {boolean} dontShowAgain - Whether to hide permanently
         */
        dismiss: function(dontShowAgain) {
            // Set inactive first to ensure cleanup happens
            this.state.isActive = false;
            
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
                    url: admin_url + 'ella_contractors/appointments/save_tutorial_preference',
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
            localStorage.removeItem(this.config.storageKey);
            localStorage.removeItem(this.config.storageKeyDismissed);
            this.start();
        }
    };

    // Auto-initialize when DOM is ready
    $(document).ready(function() {
        // Only initialize if on appointments page
        if ($('.table-ella_appointments').length > 0 || $('#new-appointment').length > 0) {
            AppointmentTutorial.init();
        }
    });

    // Expose globally for manual control
    window.AppointmentTutorial = AppointmentTutorial;

})(jQuery);

