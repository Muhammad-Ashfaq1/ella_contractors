/**
 * EllaContractors Appointment Tutorial System
 * 
 * Provides step-by-step guided tours for first-time users
 * Supports "Don't show again" functionality with persistence
 * 
 * @version 1.0.0
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
            targetElement: null
        },

        /**
         * Initialize tutorial system
         */
        init: function() {
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
                    target: '.btn-group .dropdown-toggle',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
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
                    id: 'completion',
                    title: 'Tutorial Complete!',
                    content: 'You\'re all set! You can now create, manage, and track appointments efficiently. If you need help anytime, look for the help icon or contact support.',
                    target: null,
                    position: 'center',
                    showNext: false,
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
            this.showStep(0);
        },

        /**
         * Show a specific step
         * @param {number} stepIndex - Index of the step to show
         */
        showStep: function(stepIndex) {
            if (stepIndex < 0 || stepIndex >= this.config.steps.length) {
                this.complete();
                return;
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
                        this.next();
                        return;
                    }
                }
            } else {
                this.renderStep(step, stepIndex);
            }
        },

        /**
         * Wait for an element to appear in DOM
         * @param {string} selector - CSS selector
         * @param {function} callback - Callback when element found
         * @param {number} maxAttempts - Maximum attempts (default: 20)
         * @returns {boolean} - True if element found
         */
        waitForElement: function(selector, callback, maxAttempts) {
            maxAttempts = maxAttempts || 20;
            var attempts = 0;

            var checkElement = setInterval(function() {
                attempts++;
                var element = $(selector);
                
                if (element.length > 0 && element.is(':visible')) {
                    clearInterval(checkElement);
                    callback();
                    return true;
                }

                if (attempts >= maxAttempts) {
                    clearInterval(checkElement);
                    return false;
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
            // Remove previous step
            this.removeCurrentStep();

            // Create overlay
            this.createOverlay();

            // Create tooltip
            this.createTooltip(step, stepIndex);

            // Position tooltip
            this.positionTooltip(step);

            // Highlight target element if specified
            if (step.target && step.highlight) {
                this.highlightElement(step.target);
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
                    zIndex: 9998,
                    pointerEvents: 'auto'
                }
            });

            $('body').append(this.state.overlay);

            // Click overlay to go to next step
            this.state.overlay.on('click', function(e) {
                // Only advance if clicking the overlay itself, not the tooltip
                if ($(e.target).hasClass('tutorial-overlay')) {
                    AppointmentTutorial.next();
                }
            });
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
                tooltipHtml += '</div>';
            }

            tooltipHtml += '</div>';
            tooltipHtml += '</div>';

            this.state.tooltip = $(tooltipHtml);
            $('body').append(this.state.tooltip);

            // Bind events
            this.bindTooltipEvents(step, stepIndex);
        },

        /**
         * Position tooltip relative to target element
         * @param {object} step - Step configuration
         */
        positionTooltip: function(step) {
            var tooltip = this.state.tooltip;
            
            if (!step.target || step.position === 'center') {
                // Center on screen
                tooltip.css({
                    position: 'fixed',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    zIndex: 9999
                });
                return;
            }

            // Position relative to target element
            var target = $(step.target);
            if (target.length === 0) {
                // Fallback to center if target not found
                tooltip.css({
                    position: 'fixed',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    zIndex: 9999
                });
                return;
            }

            var targetOffset = target.offset();
            var targetWidth = target.outerWidth();
            var targetHeight = target.outerHeight();
            var tooltipWidth = tooltip.outerWidth();
            var tooltipHeight = tooltip.outerHeight();
            var spacing = 5; // Very close spacing - tooltip appears right next to button

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

            // Calculate position relative to viewport (for fixed positioning)
            switch (step.position) {
                case 'top':
                    position.top = viewportTop - tooltipHeight - spacing;
                    // Align left edge of tooltip with left edge of button
                    position.left = viewportLeft;
                    position.arrowPosition = 'bottom'; // Arrow points down
                    break;
                case 'bottom':
                    position.top = viewportTop + targetHeight + spacing;
                    // Align left edge of tooltip with left edge of button
                    position.left = viewportLeft;
                    position.arrowPosition = 'top'; // Arrow points up
                    break;
                case 'left':
                    position.top = viewportTop + (targetHeight / 2) - (tooltipHeight / 2);
                    position.left = viewportLeft - tooltipWidth - spacing;
                    position.arrowPosition = 'right'; // Arrow points right
                    break;
                case 'right':
                    position.top = viewportTop + (targetHeight / 2) - (tooltipHeight / 2);
                    position.left = viewportLeft + targetWidth + spacing;
                    position.arrowPosition = 'left'; // Arrow points left
                    break;
            }
            
            // Add arrow class to tooltip for CSS styling
            if (position.arrowPosition) {
                tooltip.addClass('tutorial-arrow-' + position.arrowPosition);
            }

            // Only adjust if tooltip would be completely off-screen
            // Try to keep it close to button as much as possible
            if (position.left + tooltipWidth > windowWidth - 10) {
                // If tooltip extends beyond right edge, shift left but keep close to button
                var maxLeft = windowWidth - tooltipWidth - 10;
                if (maxLeft < viewportLeft) {
                    position.left = maxLeft;
                }
            }
            if (position.left < 10) {
                position.left = 10;
            }
            
            if (position.top + tooltipHeight > windowHeight - 10) {
                // If tooltip extends beyond bottom, try to position above button instead
                if (step.position === 'bottom') {
                    position.top = viewportTop - tooltipHeight - spacing;
                    position.arrowPosition = 'bottom';
                    tooltip.removeClass('tutorial-arrow-top').addClass('tutorial-arrow-bottom');
                } else {
                    position.top = windowHeight - tooltipHeight - 10;
                }
            }
            if (position.top < 10) {
                position.top = 10;
            }

            tooltip.css({
                position: 'fixed', // Fixed positioning for viewport-relative placement
                top: position.top + 'px',
                left: position.left + 'px',
                zIndex: 9999
            });
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

            // Add highlight class
            element.addClass('tutorial-highlight');

            // Scroll element into view if needed
            var elementOffset = element.offset();
            var elementHeight = element.outerHeight();
            var windowHeight = $(window).height();
            var scrollTop = $(window).scrollTop();

            if (elementOffset.top < scrollTop) {
                // Element is above viewport
                $('html, body').animate({
                    scrollTop: elementOffset.top - 100
                }, 300);
            } else if (elementOffset.top + elementHeight > scrollTop + windowHeight) {
                // Element is below viewport
                $('html, body').animate({
                    scrollTop: elementOffset.top - windowHeight + elementHeight + 100
                }, 300);
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
            this.showStep(this.state.currentStepIndex + 1);
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
            var dontShowAgain = $('#tutorial-dont-show-again').is(':checked');
            this.dismiss(dontShowAgain);
        },

        /**
         * Dismiss tutorial
         * @param {boolean} dontShowAgain - Whether to hide permanently
         */
        dismiss: function(dontShowAgain) {
            this.removeCurrentStep();
            this.state.isActive = false;

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
            // Remove highlight
            if (this.state.targetElement) {
                this.state.targetElement.removeClass('tutorial-highlight');
                this.state.targetElement = null;
            }

            // Remove tooltip
            if (this.state.tooltip) {
                this.state.tooltip.remove();
                this.state.tooltip = null;
            }

            // Remove overlay
            if (this.state.overlay) {
                this.state.overlay.remove();
                this.state.overlay = null;
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

