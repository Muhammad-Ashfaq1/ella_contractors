# Tutorial System - Visual Examples

## Tutorial Tooltip Appearance

### Step 1: Welcome Screen
```
┌─────────────────────────────────────────────────────────┐
│  [Dark Overlay - 50% opacity]                          │
│                                                          │
│                    ┌──────────────────────┐            │
│                    │ 🔒 Welcome to         │            │
│                    │    Appointments       │      [×]   │
│                    │                       │            │
│                    │ Step 1 of 7          │            │
│                    │                       │            │
│                    │ Welcome to the       │            │
│                    │ EllaContractors      │            │
│                    │ Appointments module! │            │
│                    │ This quick tour will │            │
│                    │ help you get started │            │
│                    │                      │            │
│                    │ [Skip Tutorial]      │            │
│                    │            [Next →]  │            │
│                    └──────────────────────┘            │
└─────────────────────────────────────────────────────────┘
```

### Step 2: New Appointment Button (Highlighted)
```
┌─────────────────────────────────────────────────────────┐
│  [Dark Overlay]                                         │
│                                                          │
│  ┌──────────────────────────────────────────────┐      │
│  │  [HIGHLIGHTED - Pulsing Blue Outline]        │      │
│  │  ┌────────────────────────────┐              │      │
│  │  │  [+] New Appointment       │              │      │
│  │  └────────────────────────────┘              │      │
│  └──────────────────────────────────────────────┘      │
│                    │                                    │
│                    ▼                                    │
│         ┌──────────────────────┐                       │
│         │ 🔒 Create New         │                       │
│         │    Appointment        │                 [×]   │
│         │                       │                       │
│         │ Step 2 of 7          │                       │
│         │                       │                       │
│         │ Click the "New        │                       │
│         │ Appointment" button   │                       │
│         │ to create a new       │                       │
│         │ appointment...        │                       │
│         │                       │                       │
│         │ [← Back] [Skip]      │                       │
│         │            [Next →]    │                       │
│         └──────────────────────┘                       │
└─────────────────────────────────────────────────────────┘
```

### Step 7: Completion (with Checkbox)
```
┌─────────────────────────────────────────────────────────┐
│  [Dark Overlay]                                         │
│                                                          │
│                    ┌──────────────────────┐            │
│                    │ ✅ Tutorial          │            │
│                    │    Complete!         │      [×]   │
│                    │                       │            │
│                    │ Step 7 of 7          │            │
│                    │                       │            │
│                    │ You're all set!      │            │
│                    │ You can now create,  │            │
│                    │ manage, and track    │            │
│                    │ appointments...      │            │
│                    │                       │            │
│                    │ ┌──────────────────┐│            │
│                    │ │ ☐ Don't show me  ││            │
│                    │ │   this again     ││            │
│                    │ └──────────────────┘│            │
│                    │                       │            │
│                    │ [← Back]  [Got it!] │            │
│                    └──────────────────────┘            │
└─────────────────────────────────────────────────────────┘
```

## Color Scheme

### Primary Colors
- **Gradient Start**: `#667eea` (Purple-blue)
- **Gradient End**: `#764ba2` (Purple)
- **Overlay**: `rgba(0, 0, 0, 0.6)` (Dark semi-transparent)
- **Highlight**: `#667eea` (Blue outline)

### Text Colors
- **Title**: `#ffffff` (White, on gradient background)
- **Content**: `#555555` (Dark gray)
- **Progress**: `#6c757d` (Medium gray)
- **Links**: `#6c757d` (Medium gray)

### Button Colors
- **Primary (Next)**: Gradient purple-blue
- **Secondary (Back)**: White with gray border
- **Link (Skip)**: Gray text, transparent background

## Responsive Behavior

### Desktop (> 768px)
- Tooltip max-width: 450px
- Positioned relative to target element
- Full feature set available

### Mobile (≤ 768px)
- Tooltip max-width: 90% of screen
- Buttons stack vertically
- Simplified layout
- Touch-friendly button sizes

## Animation Details

### Fade In
- Duration: 0.3s
- Easing: ease-in-out
- Applied to: Overlay

### Slide In
- Duration: 0.3s
- Easing: ease-out
- Applied to: Tooltip
- Effect: Slides down 10px while fading in

### Pulse
- Duration: 2s
- Repeat: Infinite
- Applied to: Highlighted elements
- Effect: Box-shadow pulses

## Accessibility Features

### Keyboard Navigation
- **Tab**: Move between buttons
- **Enter/Space**: Activate button
- **Escape**: Close tutorial (via close button)

### Screen Reader Support
- Tooltip has proper ARIA labels
- Progress announced: "Step X of Y"
- Buttons have descriptive text
- Close button has aria-label

### Focus Management
- Focus moves to tooltip when shown
- Focus trap within tooltip
- Focus returns to page when dismissed

---

**Note**: These are ASCII representations. The actual implementation uses modern CSS with smooth animations and gradients.


