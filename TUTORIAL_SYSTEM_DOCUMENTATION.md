# EllaContractors Tutorial System Documentation

## Overview

The Tutorial System provides an interactive, step-by-step guided tour for first-time users of the EllaContractors Appointments module. It helps users understand key features and functionality through contextual tooltips and highlights.

## Features

- ✅ **Step-by-step guidance** with progress indicators
- ✅ **Element highlighting** to focus user attention
- ✅ **Smart positioning** that adapts to screen size
- ✅ **"Don't show again"** functionality with persistence
- ✅ **Skip option** for users who want to skip the tutorial
- ✅ **Responsive design** for mobile and desktop
- ✅ **Accessibility** features (keyboard navigation, focus management)
- ✅ **Server-side preference storage** using user meta system

## Architecture

### File Structure

```
modules/ella_contractors/
├── assets/
│   ├── css/
│   │   └── appointment-tutorial.css      # Tutorial styling
│   └── js/
│       └── appointment-tutorial.js       # Tutorial logic
├── controllers/
│   └── Appointments.php                  # Controller methods for preferences
└── views/
    └── appointments/
        └── index.php                      # Main view (includes tutorial assets)
```

### Components

1. **JavaScript Module** (`appointment-tutorial.js`)
   - Manages tutorial flow and state
   - Handles element positioning and highlighting
   - Manages user preferences (localStorage + server)

2. **CSS Styles** (`appointment-tutorial.css`)
   - Tooltip styling with animations
   - Overlay and highlight effects
   - Responsive breakpoints

3. **Controller Methods** (`Appointments.php`)
   - `check_tutorial_status()` - Check if tutorial should be shown
   - `save_tutorial_preference()` - Save user dismissal preference
   - `reset_tutorial()` - Reset tutorial for user

## Tutorial Steps Configuration

The tutorial consists of 7 steps:

1. **Welcome** - Introduction to the module
2. **New Appointment Button** - How to create appointments
3. **Filter Dropdown** - How to filter appointments
4. **Calendar Button** - How to view calendar
5. **Appointments Table** - Understanding the table
6. **Status Column** - How to change status (optional)
7. **Completion** - Tutorial completion message

### Step Configuration Structure

Each step is defined with the following properties:

```javascript
{
    id: 'unique_step_id',              // Unique identifier
    title: 'Step Title',                // Tooltip title
    content: 'Step description...',    // Tooltip content
    target: '#element-selector',       // Target element (null for center)
    position: 'bottom',                 // Position: top, bottom, left, right, center
    showNext: true,                     // Show next button
    showBack: true,                     // Show back button
    showSkip: true,                     // Show skip button
    highlight: true,                    // Highlight target element
    waitForElement: false,              // Wait for element to appear
    optional: false,                    // Skip if element not found
    isLast: false                       // Is last step
}
```

## Usage

### Automatic Initialization

The tutorial automatically initializes when:
1. User visits the appointments page (`/admin/ella_contractors/appointments`)
2. User hasn't dismissed the tutorial previously
3. Page is fully loaded

### Manual Control

You can manually control the tutorial using the global `AppointmentTutorial` object:

```javascript
// Restart tutorial
AppointmentTutorial.restart();

// Start tutorial
AppointmentTutorial.start();

// Skip tutorial
AppointmentTutorial.skip();

// Go to specific step
AppointmentTutorial.showStep(2);
```

## Customization

### Adding New Steps

Edit `appointment-tutorial.js` and add steps to the `loadTutorialSteps()` method:

```javascript
{
    id: 'my_new_step',
    title: 'My New Step',
    content: 'This is a new tutorial step explaining a feature.',
    target: '#my-element',
    position: 'right',
    showNext: true,
    showBack: true,
    showSkip: true,
    highlight: true
}
```

### Modifying Step Content

Update the `content` field in the step configuration:

```javascript
{
    id: 'new_appointment_button',
    content: 'Your custom content here. You can use <strong>HTML</strong> for formatting.'
}
```

### Changing Styling

Edit `appointment-tutorial.css` to customize:
- Colors (search for `#667eea` and `#764ba2` for gradient colors)
- Font sizes
- Spacing and padding
- Animation timing
- Responsive breakpoints

### Changing Storage Key

Modify the `storageKey` in `appointment-tutorial.js`:

```javascript
config: {
    storageKey: 'your_custom_key',
    storageKeyDismissed: 'your_custom_dismissed_key'
}
```

## User Preferences

### Storage Methods

The tutorial uses two storage methods:

1. **localStorage** (Client-side)
   - Fast access
   - Persists across sessions
   - Key: `ella_contractors_tutorial_dismissed`

2. **Database** (Server-side)
   - Uses Perfex CRM's user meta system
   - Key: `ella_contractors_tutorial_dismissed`
   - Table: `tbluser_meta`
   - Column: `staffid` (for staff members)

### Preference States

- **Not Dismissed**: Tutorial shows on first visit
- **Dismissed**: Tutorial hidden permanently (until reset)
- **Completed**: Tutorial completed but can be restarted

## API Endpoints

### Check Tutorial Status

**Endpoint**: `GET /admin/ella_contractors/appointments/check_tutorial_status`

**Response**:
```json
{
    "show_tutorial": true,
    "dismissed": false
}
```

### Save Tutorial Preference

**Endpoint**: `POST /admin/ella_contractors/appointments/save_tutorial_preference`

**Parameters**:
- `dismissed` (int): 1 to dismiss, 0 to allow

**Response**:
```json
{
    "success": true,
    "message": "Tutorial preference saved successfully"
}
```

### Reset Tutorial

**Endpoint**: `POST /admin/ella_contractors/appointments/reset_tutorial`

**Response**:
```json
{
    "success": true,
    "message": "Tutorial reset successfully. Refresh the page to see it again."
}
```

## Integration with Other Modules

### Adding Tutorial to Other Modules

1. **Copy tutorial files** to your module:
   ```bash
   cp appointment-tutorial.js your_module/assets/js/
   cp appointment-tutorial.css your_module/assets/css/
   ```

2. **Update configuration** in the JavaScript file:
   ```javascript
   config: {
       storageKey: 'your_module_tutorial_completed',
       storageKeyDismissed: 'your_module_tutorial_dismissed',
       tutorialId: 'your_module_tutorial'
   }
   ```

3. **Add controller methods** (similar to `Appointments.php`)

4. **Include assets** in your view:
   ```php
   <link rel="stylesheet" href="<?php echo module_dir_url('your_module', 'assets/css/appointment-tutorial.css'); ?>">
   <script src="<?php echo module_dir_url('your_module', 'assets/js/appointment-tutorial.js'); ?>"></script>
   ```

5. **Update step configuration** for your module's elements

## Best Practices

### Step Design

1. **Keep steps concise** - Each step should explain one concept
2. **Use clear language** - Avoid technical jargon
3. **Highlight important elements** - Use `highlight: true` for key features
4. **Make steps optional** - Use `optional: true` for elements that may not exist
5. **Wait for dynamic content** - Use `waitForElement: true` for AJAX-loaded content

### Positioning

- **Top**: Use for elements at bottom of viewport
- **Bottom**: Use for elements at top of viewport
- **Left**: Use for elements on right side
- **Right**: Use for elements on left side
- **Center**: Use for welcome/completion messages

### Performance

- Tutorial only loads on relevant pages
- Uses efficient DOM queries
- Minimal impact on page load time
- Animations use CSS (GPU accelerated)

## Troubleshooting

### Tutorial Not Showing

1. **Check localStorage**: Open browser console and check:
   ```javascript
   localStorage.getItem('ella_contractors_tutorial_dismissed')
   ```
   Should be `null` or not `'true'`

2. **Check server preference**: Verify user meta in database:
   ```sql
   SELECT * FROM tbluser_meta 
   WHERE staffid = [USER_ID] 
   AND meta_key = 'ella_contractors_tutorial_dismissed'
   ```

3. **Check page detection**: Ensure page has required elements:
   ```javascript
   $('.table-ella_appointments').length > 0 || $('#new-appointment').length > 0
   ```

4. **Check console errors**: Look for JavaScript errors in browser console

### Elements Not Highlighting

1. **Verify selector**: Check if selector matches element:
   ```javascript
   $('#new-appointment').length  // Should be > 0
   ```

2. **Check z-index**: Ensure element doesn't have higher z-index
3. **Check visibility**: Element must be visible (`:visible`)

### Tooltip Positioning Issues

1. **Check viewport**: Tooltip auto-adjusts to stay in viewport
2. **Check responsive breakpoints**: Mobile uses different positioning
3. **Check element position**: Ensure target element has valid offset

## Accessibility

### Keyboard Navigation

- **Tab**: Navigate between buttons
- **Enter/Space**: Activate buttons
- **Escape**: Close tutorial (via close button)

### Screen Readers

- Tooltip has proper ARIA labels
- Progress indicator announces current step
- Buttons have descriptive text

### Focus Management

- Focus moves to tooltip when shown
- Focus returns to page when dismissed
- Focus trap within tooltip

## Browser Support

- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Future Enhancements

Potential improvements:

1. **Multi-language support** - Load tutorial content based on user language
2. **Video tutorials** - Embed video content in tooltips
3. **Interactive steps** - Require user action before proceeding
4. **Analytics** - Track tutorial completion rates
5. **A/B testing** - Test different tutorial flows
6. **Contextual help** - Show help based on user actions
7. **Tutorial builder** - Admin interface to create tutorials

## Support

For issues or questions:
1. Check this documentation
2. Review browser console for errors
3. Check server logs for PHP errors
4. Contact development team

## Changelog

### Version 1.0.0 (Initial Release)
- Basic tutorial system
- 7 tutorial steps
- LocalStorage and database persistence
- Responsive design
- Accessibility features

---

**Last Updated**: 2024
**Maintained By**: EllaContractors Development Team


