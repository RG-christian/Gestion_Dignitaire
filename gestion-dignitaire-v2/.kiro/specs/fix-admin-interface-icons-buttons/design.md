stration needed in script setup
```

**Responsibilities**:
- Import icon components from lucide-vue-next
- Use icons as direct components in template
- Ensure proper sizing with Tailwind classes (w-4 h-4, w-5 h-5)
- Apply consistent styling across all icon instances

**Current Issue**: Icons imported but not rendering (empty space in buttons)

**Root Cause**: Potential issues with:
1. Icon components not being treated as components in template
2. Missing `:size` prop or incorrect sizing approach
3. CSS confli  User->>Buttons: Click "Supprimer"
    Buttons->>Modal: Show confirmation
    Modal-->>Component: Execute deleteAdmin()
```

## Components and Interfaces

### Component 1: Icon Registration System

**Purpose**: Ensure lucide-vue-next icons are properly registered and rendered in Vue 3

**Interface**:
```typescript
// Icon imports from lucide-vue-next
import {
  Eye,
  Power,
  Trash2,
  User,
  Upload,
  X,
  Loader2
} from 'lucide-vue-next'

// Icons are self-registering components in Vue 3
// No manual regiomponent-->>Buttons: Update button state
    
  
    
    User->>Component: Load SuperAdminAdminsPage
    Component->>Icons: Import lucide-vue-next icons
    Icons-->>Component: Register icon components
    Component->>Buttons: Render action buttons
    Buttons-->>Component: Display with icons
    
    User->>Buttons: Click "Visualiser"
    Buttons->>Modal: Trigger viewAdmin()
    Modal->>Modal: Build HTML content
    Modal-->>User: Display admin details
    
    User->>Buttons: Click "Désactiver/Activer"
    Buttons->>Component: Call toggleStatus()
    C Icon]
    B --> B7[Loader2 Icon]
    
    C --> C1[View Button]
    C --> C2[Toggle Status Button]
    C --> C3[Delete Button]
    
    D --> D1[Admin Details Display]
    D --> D2[HTML Content Rendering]
    
    style A fill:#00758D,color:#fff
    style B fill:#8E0B56,color:#fff
    style C fill:#5CA621,color:#fff
    style D fill:#DFC32F,color:#000
```

## Main Workflow

```mermaid
sequenceDiagram
    participant User
    participant Component
    participant Icons
    participant Buttons
    participant Modalt icon usage patterns, and CSS specificity conflicts. The solution implements proper Vue 3 component registration, corrects icon component syntax, and ensures consistent button styling with proper visual feedback.

## Architecture

```mermaid
graph TD
    A[SuperAdminAdminsPage.vue] --> B[lucide-vue-next Icons]
    A --> C[Action Buttons]
    A --> D[SweetAlert2 Modal]
    
    B --> B1[Eye Icon]
    B --> B2[Power Icon]
    B --> B3[Trash2 Icon]
    B --> B4[User Icon]
    B --> B5[Upload Icon]
    B --> B6[Xrview

The SuperAdminAdminsPage.vue component has critical UI bugs preventing icons from lucide-vue-next from rendering and action buttons from displaying properly. This design addresses three core issues: (1) icon components not rendering despite correct imports, (2) action buttons invisible or improperly styled, and (3) modal display issues when viewing admin details. The root cause analysis reveals missing component registration, incorrec# Design Document: Fix Admin Interface Icons & Buttons

## Ove