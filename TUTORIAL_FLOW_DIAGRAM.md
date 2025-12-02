# Tutorial System Flow Diagram

## Complete Tutorial Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    USER VISITS PAGE                          │
│              /admin/ella_contractors/appointments            │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│              PAGE LOADS (init_head, init_tail)               │
│         Tutorial CSS & JS files loaded                       │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│         $(document).ready() - Tutorial.init()               │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│         shouldShowTutorial() Check                           │
│  ├── Check localStorage: 'tutorial_dismissed'               │
│  ├── Check server: get_meta('tutorial_dismissed')           │
│  └── Return: true/false                                     │
└─────────────────────────────────────────────────────────────┘
                            │
                    ┌───────┴───────┐
                    │               │
                    ▼               ▼
            ┌───────────┐   ┌──────────────┐
            │   TRUE    │   │    FALSE     │
            └───────────┘   └──────────────┘
                    │               │
                    │               └──► Tutorial NOT shown
                    │
                    ▼
┌─────────────────────────────────────────────────────────────┐
│              loadTutorialSteps()                            │
│         Load 7-step configuration                            │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│              start() - Show Step 0                           │
│         (Welcome Step)                                       │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│              renderStep(step, index)                         │
│  ├── createOverlay() - Dark backdrop                        │
│  ├── createTooltip() - Tooltip with content                  │
│  ├── positionTooltip() - Position relative to target        │
│  └── highlightElement() - Highlight target (if any)         │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│              USER INTERACTION                                │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │   Next   │  │   Back   │  │   Skip   │  │  Close   │  │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
        ▼                   ▼                   ▼
┌──────────────┐   ┌──────────────┐   ┌──────────────┐
│    Next      │   │    Back      │   │    Skip      │
│  showStep()  │   │  showStep()  │   │  dismiss()   │
│  index + 1   │   │  index - 1   │   │  (true)      │
└──────────────┘   └──────────────┘   └──────────────┘
        │                   │                   │
        │                   │                   └──► Save Preference
        │                   │                         localStorage + DB
        │                   │                         Tutorial Hidden
        │                   │
        └───────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│              STEP PROGRESSION                                │
│  Step 0: Welcome                                            │
│  Step 1: New Appointment Button                              │
│  Step 2: Filter Dropdown                                     │
│  Step 3: Calendar Button                                     │
│  Step 4: Appointments Table                                 │
│  Step 5: Status Column (optional)                            │
│  Step 6: Completion                                         │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│              LAST STEP (Completion)                          │
│  ┌────────────────────────────────────────────────────┐   │
│  │  "Don't show me this again" checkbox                │   │
│  │  [ ] Don't show me this again                       │   │
│  │                                                      │   │
│  │  [Back]                    [Got it!]                │   │
│  └────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│              complete()                                      │
│  ├── Check "Don't show again" checkbox                      │
│  ├── If checked: dismiss(true)                              │
│  │   ├── Save to localStorage                              │
│  │   └── Save to database (user_meta)                      │
│  └── If not checked: dismiss(false)                        │
│       └── Mark as completed (can restart)                   │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│              removeCurrentStep()                             │
│  ├── Remove overlay                                         │
│  ├── Remove tooltip                                         │
│  └── Remove highlight                                       │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│              Tutorial Complete                               │
│         User can now use the module                          │
└─────────────────────────────────────────────────────────────┘
```

## Step-by-Step Visual Flow

### Step 1: Welcome
```
┌─────────────────────────────────────┐
│  [Overlay - Dark Background]       │
│                                     │
│         ┌──────────────────┐       │
│         │  Welcome to      │       │
│         │  Appointments    │       │
│         │                  │       │
│         │  Step 1 of 7     │       │
│         │                  │       │
│         │  Welcome message │       │
│         │                  │       │
│         │  [Skip]  [Next]  │       │
│         └──────────────────┘       │
└─────────────────────────────────────┘
```

### Step 2: New Appointment Button
```
┌─────────────────────────────────────┐
│  [Overlay - Dark Background]       │
│                                     │
│  ┌──────────────────────────────┐  │
│  │ [Highlighted Button]         │  │
│  │ [+ New Appointment]          │  │
│  └──────────────────────────────┘  │
│         │                            │
│         ▼                            │
│  ┌──────────────────┐               │
│  │ Create New       │               │
│  │ Appointment      │               │
│  │                  │               │
│  │ Step 2 of 7      │               │
│  │                  │               │
│  │ [Back] [Skip]    │               │
│  │        [Next]    │               │
│  └──────────────────┘               │
└─────────────────────────────────────┘
```

## Data Flow

### Preference Storage Flow

```
User Dismisses Tutorial
    │
    ▼
┌─────────────────────────┐
│  Client-Side (Fast)      │
│  localStorage.setItem()  │
│  Key: tutorial_dismissed│
│  Value: 'true'          │
└─────────────────────────┘
    │
    ▼
┌─────────────────────────┐
│  AJAX Request           │
│  POST /save_preference  │
└─────────────────────────┘
    │
    ▼
┌─────────────────────────┐
│  Server-Side (Persistent)│
│  update_meta()          │
│  Table: tbluser_meta    │
│  staffid + meta_key     │
└─────────────────────────┘
```

### Element Detection Flow

```
Show Step with Target Element
    │
    ▼
┌─────────────────────────┐
│  Check Element Exists   │
│  $(selector).length > 0 │
└─────────────────────────┘
    │
    ├─── YES ───► Element Found
    │              │
    │              ▼
    │         ┌─────────────────┐
    │         │ Check Visibility │
    │         │ .is(':visible')  │
    │         └─────────────────┘
    │              │
    │              ├─── YES ───► Show Tooltip
    │              │
    │              └─── NO ───► waitForElement()
    │                            │
    │                            ▼
    │                         Retry (max 20x)
    │                            │
    │                            ├─── Found ───► Show Tooltip
    │                            │
    │                            └─── Not Found ───► Skip Step (if optional)
    │
    └─── NO ───► Skip Step (if optional) or Center Tooltip
```

## State Management

### Tutorial State Machine

```
[INACTIVE]
    │
    │ init()
    ▼
[CHECKING]
    │
    │ shouldShowTutorial()
    ├─── false ───► [DISMISSED]
    │
    └─── true ───► [LOADING]
                    │
                    │ loadTutorialSteps()
                    ▼
                [ACTIVE]
                    │
                    │ showStep(0)
                    ▼
                [STEP_0]
                    │
                    │ next()
                    ▼
                [STEP_1]
                    │
                    │ ... (continue through steps)
                    │
                    │ next() (on last step)
                    ▼
                [COMPLETING]
                    │
                    │ complete()
                    ├─── dontShowAgain ───► [DISMISSED]
                    │
                    └─── showAgain ───► [COMPLETED]
```

## User Interaction Patterns

### Pattern 1: Complete Tutorial
```
User → Next → Next → Next → ... → Got it! (checked) → Tutorial Hidden Forever
```

### Pattern 2: Skip Tutorial
```
User → Skip Tutorial → Tutorial Hidden Forever
```

### Pattern 3: Navigate Back
```
User → Next → Next → Back → Next → ... → Complete
```

### Pattern 4: Close Tutorial
```
User → [X] Close → Tutorial Hidden (Can Restart)
```

## Element Highlighting Flow

```
Target Element Selected
    │
    ▼
┌─────────────────────────┐
│  Add CSS Classes        │
│  .tutorial-highlight    │
└─────────────────────────┘
    │
    ▼
┌─────────────────────────┐
│  Apply Styles           │
│  ├── outline: 3px solid │
│  ├── box-shadow         │
│  └── animation: pulse   │
└─────────────────────────┘
    │
    ▼
┌─────────────────────────┐
│  Scroll Into View       │
│  (if needed)            │
└─────────────────────────┘
    │
    ▼
┌─────────────────────────┐
│  Position Tooltip       │
│  Relative to Element    │
└─────────────────────────┘
```

## Error Handling Flow

```
Tutorial Initialization
    │
    ├─── localStorage Error ───► Continue (use server only)
    │
    ├─── AJAX Error ───► Default to showing tutorial
    │
    ├─── Element Not Found ───► Skip step (if optional) or center tooltip
    │
    └─── JavaScript Error ───► Fail silently (don't break page)
```

---

**Note**: This diagram represents the complete flow of the tutorial system. Each step follows this pattern, with variations based on step configuration (target element, position, etc.).


