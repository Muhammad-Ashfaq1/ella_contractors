/**
 * EllaContractors Appointment View Tutorial System
 * 
 * Provides step-by-step guided tours for the appointment view page
 * Supports "Don't show again" functionality with persistence
 * 
 * @version 1.0.0
 * @author EllaContractors Team
 */

(function($) {
    'use strict';

    /**
     * Tutorial Manager Class for View Page
     */
    var AppointmentViewTutorial = {
        // Configuration
        config: {
            storageKey: 'ella_contractors_view_tutorial_completed',
            storageKeyDismissed: 'ella_contractors_view_tutorial_dismissed',
            tutorialId: 'appointment_view_tutorial',
            currentStep: 0,
            steps: [],
            shouldShow: true
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
            var self = this;
            
            // Check if tutorial should be shown
            if (this.shouldShowTutorial()) {
                // Load tutorial steps configuration
                this.loadTutorialSteps();
                
                // Wait for page to be fully loaded
                $(document).ready(function() {
                    // Small delay to ensure all elements are rendered
                    setTimeout(function() {
                        AppointmentViewTutorial.start();
                    }, 1000);
                });
                
                // Handle window resize to reposition tooltip
                var resizeTimeout;
                $(window).on('resize', function() {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(function() {
                        if (self.state.isActive && self.state.tooltip && self.config.steps.length > 0) {
                            var currentStep = self.config.steps[self.state.currentStepIndex];
                            if (currentStep) {
                                // Reposition all steps (including custom positioned ones)
                                self.positionTooltip(currentStep);
                            }
                        }
                    }, 150); // Debounce resize events
                });
            }
        },

        /**
         * Check if tutorial should be shown
         * @returns {boolean}
         */
        shouldShowTutorial: function() {
            // Check localStorage first
            if (localStorage.getItem(this.config.storageKeyDismissed) === 'true') {
                return false;
            }

            // Check server preference via AJAX
            var self = this;
            $.ajax({
                url: admin_url + 'ella_contractors/appointments/check_tutorial_status',
                type: 'POST',
                data: {
                    tutorial_id: this.config.tutorialId
                },
                async: false,
                success: function(response) {
                    if (response && response.should_show === false) {
                        self.config.shouldShow = false;
                    }
                }
            });

            return this.config.shouldShow;
        },

        /**
         * Load tutorial steps configuration
         */
        loadTutorialSteps: function() {
            this.config.steps = [
                {
                    id: 'welcome',
                    title: 'Welcome to Appointment Details',
                    content: 'Welcome to the Appointment Details page! This page shows all information about a specific appointment. Let\'s take a quick tour to help you navigate efficiently.',
                    target: null,
                    position: 'center',
                    showNext: true,
                    showBack: false,
                    showSkip: true
                },
                {
                    id: 'action_buttons',
                    title: 'Action Buttons',
                    content: 'Use these action buttons to navigate and manage the appointment. "Back" returns to the appointments list, "Edit" opens the edit form, and "Delete" removes the appointment.',
                    target: '._buttons',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'appointment_info',
                    title: 'Appointment Information',
                    content: 'This section displays the appointment subject, date, time, duration, and status. You can quickly see all key details at a glance.',
                    target: '.appointment-subject',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'lead_information',
                    title: 'Lead Information',
                    content: 'View the lead or client details associated with this appointment, including contact information, address, and other relevant details.',
                    target: '.col-md-6 h5',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true,
                    optional: true
                },
                {
                    id: 'tabs_section',
                    title: 'Tabs Section',
                    content: 'Use these tabs to access different sections: Measurements, Estimates, Notes, Attachments, and Timeline. Each tab contains related information and actions.',
                    target: '.nav-tabs',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'measurements_tab',
                    title: 'Measurements Tab',
                    content: 'The Measurements tab allows you to view and manage measurement data for this appointment. Click on the tab to see measurement details.',
                    target: '.nav-tabs li:first-child a',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'estimates_tab',
                    title: 'Estimates Tab',
                    content: 'The Estimates tab shows all estimates related to this appointment. You can view, create, or manage estimates from here.',
                    target: '.nav-tabs li:nth-child(2) a',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'notes_tab',
                    title: 'Notes Tab',
                    content: 'The Notes tab contains all notes and comments related to this appointment. Add important information or reminders here.',
                    target: '.nav-tabs li:nth-child(3) a',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'attachments_tab',
                    title: 'Attachments Tab',
                    content: 'The Attachments tab allows you to upload and manage files related to this appointment. Drag and drop files or click to browse.',
                    target: '.nav-tabs li:nth-child(4) a',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'timeline_tab',
                    title: 'Timeline Tab',
                    content: 'The Timeline tab shows a chronological history of all activities and changes related to this appointment. Track the complete history here.',
                    target: '.nav-tabs li:last-child a',
                    position: 'bottom',
                    showNext: true,
                    showBack: true,
                    showSkip: true,
                    highlight: true
                },
                {
                    id: 'completion',
                    title: 'Tutorial Complete!',
                    content: 'You\'re all set! You now know how to navigate the Appointment Details page. Use the tabs to access different sections and the action buttons to manage appointments.',
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
         * Start tutorial
         */
        start: function() {
            if (this.config.steps.length === 0) {
                this.loadTutorialSteps();
            }

            this.state.isActive = true;
            this.state.currentStepIndex = 0;
            this.showStep(0);
        },

        /**
         * Show specific step
         * @param {number} stepIndex - Step index to show
         */
        showStep: function(stepIndex) {
            if (stepIndex < 0 || stepIndex >= this.config.steps.length) {
                this.complete();
                return;
            }

            this.state.currentStepIndex = stepIndex;
            var step = this.config.steps[stepIndex];

            // Check if element exists, wait if needed
            if (step.target && step.waitForElement) {
                if (!this.waitForElement(step.target, function() {
                    AppointmentViewTutorial.renderStep(step, stepIndex);
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
            var self = this;
            
            // Remove previous step
            this.removeCurrentStep();

            // Create overlay
            this.createOverlay();

            // Create tooltip
            this.createTooltip(step, stepIndex);

            // Special handling for centered steps (welcome and completion)
            if (step.id === 'welcome' || step.id === 'completion') {
                // Add class to disable animation and transform transitions
                this.state.tooltip.addClass('tutorial-centered');
                // Position directly in center without any delays or animations
                this.state.tooltip.css({
                    position: 'fixed',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    zIndex: 9999,
                    visibility: 'visible',
                    opacity: 1,
                    transition: 'opacity 0.3s ease-out'
                });
                return;
            }

            // If target element exists, wait for it to be visible, then position
            if (step.target && step.position !== 'center') {
                // Hide tooltip completely off-screen initially
                this.state.tooltip.css({
                    position: 'fixed',
                    top: '-9999px',
                    left: '-9999px',
                    visibility: 'hidden',
                    opacity: 0
                });
                
                var target = $(step.target);
                
                // Wait a bit for element to be fully rendered
                setTimeout(function() {
                    // Scroll element into view first if needed
                    if (step.highlight) {
                        self.highlightElement(step.target);
                    }
                    
                    // Small delay to allow scroll animation to complete
                    setTimeout(function() {
                        // Position tooltip first (while still hidden)
                        self.positionTooltip(step);
                        // Then make visible with fade-in
                        setTimeout(function() {
                            self.state.tooltip.css({
                                visibility: 'visible',
                                opacity: 1,
                                transition: 'opacity 0.3s ease-out'
                            });
                        }, 50);
                    }, 350);
                }, 100);
            } else {
                // No target or center position - position to center while hidden, then make visible
                // Use requestAnimationFrame to ensure positioning happens before browser paints
                requestAnimationFrame(function() {
                    // Position to center while still hidden
                    self.state.tooltip.css({
                        position: 'fixed',
                        top: '50%',
                        left: '50%',
                        transform: 'translate(-50%, -50%)',
                        zIndex: 9999,
                        visibility: 'hidden',
                        opacity: 0
                    });
                    
                    // Make visible in next frame to ensure positioning is complete
                    requestAnimationFrame(function() {
                        self.state.tooltip.css({
                            visibility: 'visible',
                            opacity: 1,
                            transition: 'opacity 0.3s ease-out'
                        });
                    });
                });
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
                tooltipHtml += '<input type="checkbox" id="tutorial-dont-show-again" />';
                tooltipHtml += ' Don\'t show me this tutorial again';
                tooltipHtml += '</label>';
                tooltipHtml += '<button type="button" class="btn btn-default tutorial-btn-close" style="margin-left: 15px;">Close</button>';
                tooltipHtml += '</div>';
            }
            
            tooltipHtml += '</div>';
            tooltipHtml += '</div>';
            
            this.state.tooltip = $(tooltipHtml);
            
            // Apply initial hidden styles BEFORE appending to DOM to prevent flash
            // This ensures the tooltip is never visible in wrong position
            this.state.tooltip.css({
                position: 'fixed',
                top: '-9999px',
                left: '-9999px',
                visibility: 'hidden',
                opacity: 0,
                zIndex: 9999,
                transition: 'none'
            });
             
            $('body').append(this.state.tooltip);
            
            // Bind events
            this.bindTooltipEvents(step, stepIndex);
        },
        // positionTooltip
        /**
         * Position tooltip relative to target element
         * @param {object} step - Step configuration
         */
        positionTooltip: function(step) {
            var tooltip = this.state.tooltip;
            
            // UPDATED: Ensure tooltip is temporarily visible for dimension calculation (but off-screen)
            // This prevents glitch where tooltip appears in wrong position
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
            
            // CUSTOM POSITIONING: Responsive positioning for specific steps
            var viewportWidth = $(window).width();
            var viewportHeight = $(window).height();
            
            if (step.id === 'action_buttons') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-top');
                
                var positions;
                if (viewportWidth >= 1920) {
                    positions = { top: '13%', left: '20%', transform: 'none' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '14%', left: '18%', transform: 'none' };
                } else if (viewportWidth >= 1366) {
                    positions = { top: '15%', left: '15%', transform: 'none' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '16%', left: '12%', transform: 'none' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '20%', left: '10%', transform: 'none' };
                } else {
                    positions = { top: '22%', left: '50%', transform: 'translateX(-50%)' };
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: positions.top,
                    left: positions.left,
                    transform: positions.transform,
                    zIndex: 9999
                });
                return;
            }
            
            if (step.id === 'appointment_info') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-top');
                
                var positions;
                if (viewportWidth >= 1920) {
                    positions = { top: '18%', left: '30%', transform: 'none' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '18%', left: '28%', transform: 'none' };
                } else if (viewportWidth >= 1366) {
                    positions = { top: '18%', left: '25%', transform: 'none' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '20%', left: '22%', transform: 'none' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '22%', left: '18%', transform: 'none' };
                } else {
                    positions = { top: '24%', left: '50%', transform: 'translateX(-50%)' };
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: positions.top,
                    left: positions.left,
                    transform: positions.transform,
                    zIndex: 9999
                });
                return;
            }
            
            if (step.id === 'lead_information') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-top');
                
                var positions;
                if (viewportWidth >= 1920) {
                    positions = { top: '38%', left: '40%', transform: 'none' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '38%', left: '38%', transform: 'none' };
                } else if (viewportWidth >= 1366) {
                    positions = { top: '38%', left: '35%', transform: 'none' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '39%', left: '32%', transform: 'none' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '40%', left: '28%', transform: 'none' };
                } else {
                    positions = { top: '50%', left: '50%', transform: 'translate(-50%, -50%)' };
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: positions.top,
                    left: positions.left,
                    transform: positions.transform,
                    zIndex: 9999
                });
                return;
            }
            
            if (step.id === 'measurements_tab') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-bottom');
                
                var positions;
                if (viewportWidth >= 1920) {
                    positions = { top: '27%', left: '5%', transform: 'none' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '27%', left: '4%', transform: 'none' };
                } else if (viewportWidth >= 1366) {
                    positions = { top: '27%', left: '3%', transform: 'none' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '27%', left: '2%', transform: 'none' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '27%', left: '1%', transform: 'none' };
                } else {
                    positions = { top: '27%', left: '50%', transform: 'translateX(-50%)' };
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: positions.top,
                    left: positions.left,
                    transform: positions.transform,
                    zIndex: 9999
                });
                return;
            }
            
            if (step.id === 'estimates_tab') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-bottom');
                
                var positions;
                if (viewportWidth >= 1920) {
                    positions = { top: '27%', left: '9%', transform: 'none' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '27%', left: '8%', transform: 'none' };
                } else if (viewportWidth >= 1366) {
                    positions = { top: '27%', left: '7%', transform: 'none' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '27%', left: '6%', transform: 'none' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '27%', left: '5%', transform: 'none' };
                } else {
                    positions = { top: '27%', left: '50%', transform: 'translateX(-50%)' };
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: positions.top,
                    left: positions.left,
                    transform: positions.transform,
                    zIndex: 9999
                });
                return;
            }
            
            if (step.id === 'notes_tab') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-bottom');
                
                var positions;
                if (viewportWidth >= 1920) {
                    positions = { top: '27%', left: '13%', transform: 'none' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '27%', left: '12%', transform: 'none' };
                } else if (viewportWidth >= 1366) {
                    positions = { top: '27%', left: '11%', transform: 'none' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '27%', left: '10%', transform: 'none' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '27%', left: '9%', transform: 'none' };
                } else {
                    positions = { top: '27%', left: '50%', transform: 'translateX(-50%)' };
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: positions.top,
                    left: positions.left,
                    transform: positions.transform,
                    zIndex: 9999
                });
                return;
            }
            
            if (step.id === 'attachments_tab') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-bottom');
                
                var positions;
                if (viewportWidth >= 1920) {
                    positions = { top: '27%', left: '17%', transform: 'none' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '27%', left: '16%', transform: 'none' };
                } else if (viewportWidth >= 1366) {
                    positions = { top: '27%', left: '15%', transform: 'none' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '27%', left: '14%', transform: 'none' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '27%', left: '13%', transform: 'none' };
                } else {
                    positions = { top: '27%', left: '50%', transform: 'translateX(-50%)' };
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: positions.top,
                    left: positions.left,
                    transform: positions.transform,
                    zIndex: 9999
                });
                return;
            }
            
            if (step.id === 'timeline_tab') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-bottom');
                
                var positions;
                if (viewportWidth >= 1920) {
                    positions = { top: '27%', left: '21%', transform: 'none' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '27%', left: '20%', transform: 'none' };
                } else if (viewportWidth >= 1366) {
                    positions = { top: '27%', left: '19%', transform: 'none' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '27%', left: '18%', transform: 'none' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '27%', left: '17%', transform: 'none' };
                } else {
                    positions = { top: '27%', left: '50%', transform: 'translateX(-50%)' };
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: positions.top,
                    left: positions.left,
                    transform: positions.transform,
                    zIndex: 9999
                });
                return;
            }
            
            if (step.id === 'tabs_section') {
                tooltip.removeClass('tutorial-arrow-top tutorial-arrow-bottom tutorial-arrow-left tutorial-arrow-right');
                tooltip.addClass('tutorial-arrow-bottom');
                
                var positions;
                if (viewportWidth >= 1920) {
                    positions = { top: '27%', left: '45%', transform: 'none' };
                } else if (viewportWidth >= 1600) {
                    positions = { top: '27%', left: '45%', transform: 'none' };
                } else if (viewportWidth >= 1366) {
                    positions = { top: '27%', left: '45%', transform: 'none' };
                } else if (viewportWidth >= 1024) {
                    positions = { top: '27%', left: '45%', transform: 'none' };
                } else if (viewportWidth >= 768) {
                    positions = { top: '27%', left: '45%', transform: 'none' };
                } else {
                    positions = { top: '27%', left: '50%', transform: 'translateX(-50%)' };
                }
                
                tooltip.css({
                    position: 'fixed',
                    top: positions.top,
                    left: positions.left,
                    transform: positions.transform,
                    zIndex: 9999
                });
                return;
            }
            
            if (!step.target || step.position === 'center') {
                // Center on screen - don't change visibility, let renderStep handle it
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
                // Fallback to center if target not found - don't change visibility
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
            var spacing = 2; // Minimal spacing - tooltip appears very close to button

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
                arrowPosition: null
            };

            // Calculate position relative to viewport (for fixed positioning)
            var arrowOffset = 0;
            
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
            
            // Store arrow offset for CSS positioning
            tooltip.data('arrow-offset', arrowOffset);
            
            // Add arrow class to tooltip for CSS styling
            if (position.arrowPosition) {
                tooltip.addClass('tutorial-arrow-' + position.arrowPosition);
            }

            // Adjust position to keep tooltip within viewport while maintaining arrow alignment
            var adjustedArrowOffset = arrowOffset;
            
            // Horizontal adjustments
            if (position.left < 10) {
                adjustedArrowOffset = arrowOffset - (10 - position.left);
                position.left = 10;
            } else if (position.left + tooltipWidth > windowWidth - 10) {
                var overflow = (position.left + tooltipWidth) - (windowWidth - 10);
                adjustedArrowOffset = arrowOffset + overflow;
                position.left = windowWidth - tooltipWidth - 10;
            }
            
            // Vertical adjustments
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
            
            // Update arrow offset data attribute and CSS variable
            tooltip.data('arrow-offset', adjustedArrowOffset);
            tooltip.css('--arrow-offset', adjustedArrowOffset + 'px');
            
            // Ensure arrow doesn't go outside tooltip bounds (min 20px from edges)
            var minArrowOffset = 20;
            var maxArrowOffset = tooltipWidth - 20;
            if (adjustedArrowOffset < minArrowOffset) {
                tooltip.css('--arrow-offset', minArrowOffset + 'px');
            } else if (adjustedArrowOffset > maxArrowOffset) {
                tooltip.css('--arrow-offset', maxArrowOffset + 'px');
            }

            // UPDATED: Apply positioning without changing visibility/opacity
            // Visibility is controlled by renderStep to prevent glitch
            var finalCss = {
                position: 'fixed',
                top: position.top + 'px',
                left: position.left + 'px',
                zIndex: 9999
            };
            
            // Only set visibility/opacity if tooltip was already visible
            if (!wasHidden) {
                finalCss.visibility = 'visible';
                finalCss.opacity = 1;
            }
            
            tooltip.css(finalCss);
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

            // Remove any existing highlight first
            $('.tutorial-highlight').removeClass('tutorial-highlight');

            // Add highlight class
            element.addClass('tutorial-highlight');

            // Scroll element into view if needed
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
            this.showStep(nextIndex);
        },

        /**
         * Go to previous step
         */
        previous: function() {
            var prevIndex = this.state.currentStepIndex - 1;
            this.showStep(prevIndex);
        },

        /**
         * Skip tutorial
         */
        skip: function() {
            this.complete(true);
        },

        /**
         * Complete tutorial
         * @param {boolean} dontShowAgain - Whether to save preference
         */
        complete: function(dontShowAgain) {
            this.state.isActive = false;
            this.removeCurrentStep();

            if (dontShowAgain) {
                // Save preference to localStorage
                localStorage.setItem(this.config.storageKeyDismissed, 'true');
                localStorage.setItem(this.config.storageKeyCompleted, 'true');

                // Save preference to server
                $.ajax({
                    url: admin_url + 'ella_contractors/appointments/save_tutorial_preference',
                    type: 'POST',
                    data: {
                        tutorial_id: this.config.tutorialId,
                        dismissed: true
                    }
                });
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
         * Restart tutorial
         */
        restart: function() {
            // Clear preferences
            localStorage.removeItem(this.config.storageKeyDismissed);
            localStorage.removeItem(this.config.storageKeyCompleted);

            // Reset on server
            $.ajax({
                url: admin_url + 'ella_contractors/appointments/reset_tutorial',
                type: 'POST',
                data: {
                    tutorial_id: this.config.tutorialId
                },
                success: function() {
                    AppointmentViewTutorial.start();
                }
            });
        }
    };

    // Initialize on page load
    AppointmentViewTutorial.init();

    // Expose globally for manual control
    window.AppointmentViewTutorial = AppointmentViewTutorial;

})(jQuery);

