# Layout System Implementation Guide

## Overview
This guide explains how to implement a centralized layout system to avoid duplicating sidebar and header code across all views.

## Files Created

1. **application/views/layouts/main.php** - Main layout template containing:
   - Header with user info dropdown
   - Consistent sidebar navigation
   - Footer
   - All CSS/JS includes

2. **application/views/content/dashboard.php** - Sample content view
3. **application/views/content/sample.php** - Another sample content view

## How It Works

Instead of having the full HTML structure in each view file, we separate:
- **Layout** (`layouts/main.php`): Contains everything except the main content area
- **Content Views** (`content/*.php`): Contains only the unique content for each page

## Implementation Steps

### 1. Modify Controllers to Use Layout System

In your controllers, instead of:
```php
$this->load->view('main/dashboard', $data);
```

Use:
```php
// Load the content view into a variable
$content = $this->load->view('content/dashboard', $content_data, TRUE);

// Pass the content to the layout
$layout_data = array();
$layout_data['user_info'] = $data['user_info'];
$layout_data['content'] = $content;
$layout_data['page_title'] = 'Dashboard';
$layout_data['breadcrumb'] = 'Dashboard';

$this->load->view('layouts/main', $layout_data);
```

### 2. Convert Existing Views to Content Views

Extract only the unique content from each view and place it in a new file under `application/views/content/`.

For example, from `application/views/main/dashboard.php`, extract only the content inside `<div class="container-fluid">` and place it in `application/views/content/dashboard.php`.

### 3. Benefits

1. **Consistency**: All pages will have the same header/sidebar/footer
2. **Maintainability**: Changes to layout only need to be made in one file
3. **No Duplication**: Eliminates the need to copy sidebar code to every view
4. **Easier Updates**: Adding/removing menu items only requires changes to the layout file

## Example Controller Method

```php
public function dashboard() 
{
    // Check if user is logged in
    if (!$this->session->userdata('user_id')) {
        redirect('login');
        return;
    }

    $session_id = $this->session->userdata('user_id');

    // Load the dashboard content
    $data['user_info'] = $this->users->getUser($session_id);
    
    // For layout system - load content into a variable
    $content_data = array();
    $content_data['user_info'] = $data['user_info'];
    
    // Load the content view into a variable
    $content = $this->load->view('content/dashboard', $content_data, TRUE);
    
    // Pass the content to the layout
    $layout_data = array();
    $layout_data['user_info'] = $data['user_info'];
    $layout_data['content'] = $content;
    $layout_data['page_title'] = 'Dashboard';
    $layout_data['breadcrumb'] = 'Dashboard';
    
    $this->load->view('layouts/main', $layout_data);
}
```

## Migration Process

To migrate your existing views:

1. Create content views by extracting the unique content from each existing view
2. Update all controllers to use the layout system
3. Test each page to ensure it displays correctly
4. Once all pages are migrated, you can remove the old duplicated view files

This approach ensures that your sidebar and header will be consistent across all pages without having to maintain the same code in multiple files.