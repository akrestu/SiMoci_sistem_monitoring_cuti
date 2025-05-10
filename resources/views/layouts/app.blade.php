<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SiCuti - Sistem Managemen Cuti') }}</title>
    <link rel="icon" href="{{ asset('images/leave.png') }}" type="image/png">
    <link rel="alternate icon" href="{{ asset('images/leave.png') }}" type="image/png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Custom Compact CSS -->
    <link href="{{ asset('css/compact-style.css') }}" rel="stylesheet">
    <!-- Custom Cuti CSS -->
    <link href="{{ asset('css/cuti-style.css') }}" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d6efd; /* Bootstrap 5 blue */
            --primary-hover: #0b5ed7;
            --secondary-color: #6c757d; /* Neutral gray */
            --success-color: #198754; /* Standard green */
            --info-color: #0dcaf0; /* Standard blue for info */
            --warning-color: #ffc107; /* Standard yellow */
            --danger-color: #dc3545; /* Standard red */
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --bg-sidebar: #255cdc;
            --bg-light: #f5f7fa;
            --border-radius: 0.375rem; /* Match Bootstrap 5 */
            --box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);

            /* Simplified timing and easing */
            --transition-fast: 150ms ease-out;
            --transition-normal: 200ms ease-out;
            --transition-slow: 300ms ease-out;

            /* Z-index layers */
            --z-toast: 1200;
            --z-modal: 1100;
            --z-dropdown: 1000;
            --z-header: 900;
            --z-sidebar: 890;
            --z-preloader: 9999;
        }

        /* Global fix for rounded circle icons - ensure perfect circles */
        .rounded-circle {
            width: 40px;
            height: 40px;
            aspect-ratio: 1/1;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 50% !important;
            position: relative !important;
        }

        /* Fix for icon centering within circles */
        .rounded-circle i {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin: 0 !important;
            line-height: 1 !important;
        }

        /* Fix for specific icon sizes */
        .rounded-circle i.fa-2x,
        .rounded-circle i.fa-3x {
            line-height: 0 !important;
        }

        /* Larger icons */
        .rounded-circle.icon-lg {
            width: 50px;
            height: 50px;
        }

        /* Smaller icons */
        .rounded-circle.icon-sm {
            width: 32px;
            height: 32px;
        }

        /* Override for badge elements that use rounded-pill class */
        span.badge.rounded-pill {
            width: auto !important;
            aspect-ratio: auto !important;
            border-radius: 50rem !important;
        }

        /* Special container for large icons in circles */
        .icon-container {
            display: block;
            margin: 0 auto;
            position: relative;
            width: 90px;
            height: 90px;
        }

        /* Preloader Styles */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: var(--z-preloader);
            opacity: 1;
            visibility: visible;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        #preloader.hide {
            opacity: 0;
            visibility: hidden;
        }

        .loader {
            width: 70px;
            height: 70px;
            position: relative;
        }

        .loader:before {
            content: "";
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 4px solid transparent;
            border-top-color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            position: absolute;
            top: 0;
            left: 0;
            animation: spin 1.2s linear infinite;
        }

        .loader:after {
            content: "";
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 4px solid transparent;
            border-left-color: var(--bg-sidebar);
            border-right-color: var(--bg-sidebar);
            position: absolute;
            top: 10px;
            left: 10px;
            animation: spin-reverse 0.8s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes spin-reverse {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(-360deg);
            }
        }

        .ajax-loader {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            z-index: var(--z-preloader);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
            transform: translateY(-10px);
        }

        .ajax-loader.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .ajax-loader .spinner {
            width: 18px;
            height: 18px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Button styles */
        .btn {
            font-weight: 400;
            line-height: 1.5;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            cursor: pointer;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            border-radius: var(--border-radius);
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out,
                        border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .btn-primary {
            color: #fff;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .btn-secondary {
            color: #fff;
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .btn-success {
            color: #fff;
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
        }

        .btn-info {
            color: #000;
            background-color: var(--info-color);
            border-color: var(--info-color);
        }

        .btn-info:hover {
            background-color: #31d2f2;
            border-color: #25cff2;
        }

        .btn-warning {
            color: #000;
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .btn-warning:hover {
            background-color: #ffca2c;
            border-color: #ffc720;
        }

        .btn-danger {
            color: #fff;
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-danger:hover {
            background-color: #bb2d3b;
            border-color: #b02a37;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: calc(var(--border-radius) * 0.875);
        }

        .btn-lg {
            padding: 0.5rem 1rem;
            font-size: 1.25rem;
            border-radius: calc(var(--border-radius) * 1.125);
        }

        /* Base styles */
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: #333;
            overflow-x: hidden;
            opacity: 1;
            transition: opacity var(--transition-normal);
        }

        body.page-transitioning {
            opacity: 0.92;
            pointer-events: none;
        }

        body.sidebar-open {
            overflow: hidden;
        }

        /* Sidebar base styles */
        .sidebar-wrapper {
            position: fixed;
            width: 250px; /* Increased from 250px to 280px */
            height: 100vh;
            background: var(--bg-sidebar); /* Use variable */
            z-index: var(--z-sidebar);
            transition: transform var(--transition-normal);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            visibility: visible;
            opacity: 1;
            will-change: transform;
        }

        /* Brand section */
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            height: auto;
            min-height: 80px;
        }

        .sidebar-brand a {
            text-decoration: none;
            color: #ffffff;
            text-align: center;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: baseline;
        }

        .sidebar-logo {
            width: auto;
            height: 50px;
            margin-bottom: 0.75rem;
            display: block;
        }

        .sidebar-brand .brand-text {
            font-size: 2rem; /* Adjusted font size */
            font-weight: 800;
            color: rgba(255, 255, 255, 1);
            text-align: center;
        }

        .sidebar-brand .small {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        /* Menu styles */
        .sidebar-menu {
            padding: 1.5rem 0 0.75rem 0; /* Increased top padding */
            margin-bottom: 4rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            padding: 0.85re 1.5rem; /* Increased padding */
            text-decoration: none;
            transition: all var(--transition-fast);
            position: relative;
            margin: 0.25rem 0.5rem; /* Increased vertical margin */
            border-radius: 0.5rem;
            font-size: 1.05rem; /* Increased font size */
            overflow: hidden;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.15);
        }

        .sidebar-menu a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 60%;
            width: 4px;
            background: #ffffff;
            border-radius: 0 4px 4px 0;
        }

        .sidebar-menu a i,
        .sidebar-dropdown .dropdown-toggle i,
        .sidebar-dropdown .dropdown-item i {
            font-size: 18px; /* Increased from 16px */
            width: 24px; /* Increased from 20px */
            margin-right: 1rem; /* Increased from 0.75rem */
            text-align: center;
            transition: none; /* Prevent icon size transitions */
            flex-shrink: 0; /* Prevent shrinking */
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Dropdown menu */
        .sidebar-dropdown {
            position: relative;
        }

        .sidebar-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            padding: 0.85rem 1.5rem; /* Increased padding to match menu items */
            text-decoration: none;
            transition: all var(--transition-fast);
            position: relative;
            margin: 0.25rem 0.5rem; /* Increased vertical margin */
            border-radius: 0.5rem;
            font-size: 1.05rem; /* Increased font size */
            overflow: hidden;
        }

        .sidebar-dropdown .dropdown-toggle:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.15);
        }

        .sidebar-dropdown .dropdown-toggle.active {
            color: #ffffff;
            background-color: var(--primary-color);
        }

        .sidebar-dropdown .dropdown-toggle.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 60%;
            width: 4px;
            background: #ffffff;
            border-radius: 0 4px 4px 0;
        }

        .sidebar-dropdown .dropdown-toggle::after {
            margin-left: auto;
            vertical-align: middle;
            transition: transform var(--transition-fast);
        }

        .sidebar-dropdown .dropdown-toggle.show::after {
            transform: rotate(180deg);
        }

        .sidebar-dropdown .dropdown-menu {
            position: static;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: none;
            border-radius: 0;
            margin: 0 0.5rem;
            padding: 0.25rem 0;
            box-shadow: none;
            width: auto;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            display: block;
            transform-origin: top center;
            pointer-events: none;
            transition: max-height var(--transition-normal), opacity var(--transition-normal);
        }

        .sidebar-dropdown .dropdown-menu.show {
            max-height: 500px;
            opacity: 1;
            pointer-events: auto;
        }

        .sidebar-dropdown .dropdown-item {
            color: rgba(255, 255, 255, 0.85);
            padding: 0.5rem 1.5rem 0.5rem 3rem;
            background: transparent;
            transition: all var(--transition-fast);
            font-size: 0.9rem;
            position: relative;
            overflow: hidden;
        }

        .sidebar-dropdown .dropdown-item:hover,
        .sidebar-dropdown .dropdown-item:focus,
        .sidebar-dropdown .dropdown-item.active {
            color: #ffffff;
            background-color: var(--primary-color);
        }

        .sidebar-dropdown .dropdown-item i {
            margin-right: 0.75rem;
            opacity: 0.8;
        }

        .sidebar-dropdown .dropdown-item.active {
            position: relative;
            font-weight: 500;
        }

        .sidebar-dropdown .dropdown-item.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            height: 60%;
            transform: translateY(-50%);
            width: 4px;
            background: white;
            border-radius: 0 4px 4px 0;
        }

        /* Footer */
        .sidebar-footer {
            padding: 1rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            left: 0;
            bottom: 0;
            width: 250px; /* Updated to match the new sidebar width */
            background: var(--bg-sidebar); /* Use variable */
        }

        .sidebar-footer .heart {
            color: #e25555;
            animation: heartbeat 1.5s infinite ease-in-out;
            display: inline-block;
        }

        .sidebar-footer .version {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.7rem;
            margin-top: 0.25rem;
        }

        /* Main content */
        .main-content {
            margin-left: 280px; /* Updated to match new sidebar width */
            transition: margin-left var(--transition-normal);
            display: flex; /* Added */
            flex-direction: column; /* Added */
            min-height: 100vh; /* Added */
            overflow-x: hidden; /* Prevent content from overflowing horizontally */
            width: calc(100% - 280px); /* Ensure width is calculated correctly */
        }

        /* Navbar */
        .main-navbar {
            background: white;
            border-bottom: 1px solid #eaeaea;
            position: sticky;
            top: 0;
            z-index: var(--z-header);
            box-shadow: var(--box-shadow);
            }

            .navbar-toggler {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark-color);
            cursor: pointer;
            padding: 0.25rem;
        }

        .navbar-user {
            display: flex;
            align-items: center;
        }

        .navbar-user .dropdown-menu {
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #eaeaea;
            padding: 0.5rem 0;
            min-width: 10rem;
            animation: dropdown-in var(--transition-fast) forwards;
        }

        @keyframes dropdown-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .navbar-user .dropdown-item {
            padding: 0.6rem 1.25rem;
            font-size: 0.9rem;
            transition: background-color var(--transition-fast);
        }

        .navbar-user .dropdown-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .navbar-user .dropdown-item i {
            margin-right: 0.5rem;
            font-size: 0.9rem;
        }

        /* Content area */
        .content-wrapper {
            padding: 1.5rem;
            animation: fade-in var(--transition-normal);
            min-height: calc(100vh - 60px); /* Keep existing min-height as fallback */
            flex-grow: 1; /* Added */
            width: 100%; /* Ensure full width */
            overflow-x: hidden; /* Prevent horizontal overflow */
        }

        /* Table content section */
        #table_content {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            margin-top: 1rem;
        }

        #table_content .container-fluid {
            padding-top: 0;
            padding-bottom: 1rem;
        }

        @keyframes fade-in {
            from { opacity: 0.92; }
            to { opacity: 1; }
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -280px;
                z-index: 1050;
                transition: all 0.35s ease;
            }

            .sidebar.show {
                margin-left: 0;
                box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                overflow-x: hidden; /* Prevent content from overflowing horizontally on mobile */
                /* min-height: 100vh; is already set above and applies here too */
            }

            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.35s ease, visibility 0.35s ease;
            }

            .sidebar-backdrop.show {
                opacity: 1;
                visibility: visible;
            }

            /* Adjust content padding on mobile */
            .content-wrapper {
                padding: 1rem; /* Reduced padding for mobile */
            }

            /* Improve submenu handling on mobile */
            .sidebar .collapse,
            .sidebar .collapsing {
                margin-left: 0.5rem;
            }

            /* Remove specific mobile padding rule for collapsed links, rely on the general one above */
            /* .sidebar .collapse .nav-link,
            .sidebar .collapsing .nav-link {
                padding-left: 1.5rem;
            } */

            /* Make navbar more compact on mobile */
            .main-navbar {
                /* padding: 0.5rem 1rem; */ /* Removed padding */
            }

            /* Make sidebar header more compact and consistent */
            .sidebar .d-flex.align-items-center {
                /* padding: 1rem 0 0.75rem; */ /* Removed old rule */
                padding: 1rem 1.25rem; /* Consistent horizontal padding */
            }
        }

        /* Heart animation in footer */
        .heart {
            color: #e25555;
            animation: heartbeat 1.5s infinite ease-in-out;
            display: inline-block;
        }

        @keyframes heartbeat {
            0% { transform: scale(1); }
            15% { transform: scale(1.25); }
            30% { transform: scale(1); }
            45% { transform: scale(1.25); }
            60% { transform: scale(1); }
        }

        /* Floating sidebar trigger for mobile */
        .floating-sidebar-trigger {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1060;
            opacity: 1;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .floating-sidebar-trigger.hide {
            opacity: 0;
            transform: scale(0.8);
            pointer-events: none;
        }

        .floating-sidebar-trigger .btn {
            width: 56px;
            height: 56px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.2s ease;
        }

        .floating-sidebar-trigger .btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4);
        }

        .floating-sidebar-trigger .btn:active {
            transform: scale(0.95);
        }

        .floating-sidebar-trigger .btn i {
            font-size: 1.5rem;
        }
    </style>

    <!-- Bootstrap Sidebar Custom Styling -->
    <style>
        /* Bootstrap Sidebar Custom Styling */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
            width: 280px;
        }

        .sidebar.bg-dark {
            background-color: #212529 !important;
        }

        .sidebar hr {
            margin: 1rem 0;
            color: rgba(255, 255, 255, 0.2);
        }

        .sidebar .nav-link {
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.25rem;
            border-radius: 0.25rem;
            transition: all 0.2s ease-in-out;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }

        /* Make dropdown items active styling consistent with regular nav items */
        .sidebar .collapse .nav-link.active,
        .sidebar .collapsing .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }

        .sidebar .fa-angle-down {
            transition: transform 0.3s;
        }

        .sidebar a[aria-expanded="true"] .fa-angle-down {
            transform: rotate(180deg);
        }

        /* Enhanced submenu styling with better collapsing */
        .sidebar .collapse,
        .sidebar .collapsing {
            transition-property: height, padding-top, padding-bottom, margin-top, margin-bottom;
            transition-duration: 0.35s;
            transition-timing-function: ease;
        }

        .sidebar .collapse {
            padding-left: 0.75rem;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            margin-left: 1.1rem;
        }

        /* Make sure collapsing elements are initially invisible but transitioning properly */
        .sidebar .collapsing {
            height: 0;
            overflow: hidden;
            padding-left: 0.75rem;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            margin-left: 1.1rem;
        }

        .sidebar .collapse .nav-link,
        .sidebar .collapsing .nav-link {
            padding: 0.6rem 1.25rem 0.6rem 2.5rem; /* Adjusted padding for indentation */
            font-size: 0.9rem;
            transition: all 0.2s ease-in-out;
            margin-bottom: 0.125rem;
        }

        /* Main content adjustment */
        .main-content {
            margin-left: 280px;
            transition: margin 0.3s;
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -280px;
                z-index: 1050;
                transition: all 0.35s ease;
            }

            .sidebar.show {
                margin-left: 0;
                box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                overflow-x: hidden; /* Prevent content from overflowing horizontally on mobile */
                /* min-height: 100vh; is already set above and applies here too */
            }

            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.35s ease, visibility 0.35s ease;
            }

            .sidebar-backdrop.show {
                opacity: 1;
                visibility: visible;
            }

            /* Adjust content padding on mobile */
            .content-wrapper {
                padding: 1rem; /* Reduced padding for mobile */
            }

            /* Improve submenu handling on mobile */
            .sidebar .collapse,
            .sidebar .collapsing {
                margin-left: 0.5rem;
            }

            /* Remove specific mobile padding rule for collapsed links, rely on the general one above */
            /* .sidebar .collapse .nav-link,
            .sidebar .collapsing .nav-link {
                padding-left: 1.5rem;
            } */

            /* Make navbar more compact on mobile */
            .main-navbar {
                /* padding: 0.5rem 1rem; */ /* Removed padding */
            }

            /* Make sidebar header more compact and consistent */
            .sidebar .d-flex.align-items-center {
                /* padding: 1rem 0 0.75rem; */ /* Removed old rule */
                padding: 1rem 1.25rem; /* Consistent horizontal padding */
            }
        }
    </style>

    <script>
        // Navigation timing constants
        const TIMING = {
            FAST: 150,
            NORMAL: 200,
            SLOW: 300
        };

        // Add no-transition class to prevent flickering during page load
        document.documentElement.classList.add('no-transition');

        // Remove no-transition class after a slight delay
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.documentElement.classList.remove('no-transition');
            }, TIMING.FAST);
        });

        /**
         * Navigate to a URL with proper handling of dropdowns in navigation
         * @param {string} url - The URL to navigate to
         */
        function navigateTo(url) {
            // Track which dropdown is currently open
            const dropdownButtons = document.querySelectorAll('.dropdown-toggle');
            let activeDropdownId = null;

            dropdownButtons.forEach(button => {
                const dropdownMenu = button.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('show')) {
                    activeDropdownId = button.id;
                }
            });

            // Store active dropdown in session storage
            if (activeDropdownId) {
                if (url.includes('/settings/')) {
                    sessionStorage.setItem('activeSettingsDropdown', activeDropdownId);
                } else {
                    sessionStorage.removeItem('activeSettingsDropdown');
                }
            }

            // Navigate to the URL
            window.location.href = url;
        }

        // Restore dropdown state when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we're on a settings page and restore the dropdown
            if (window.location.pathname.includes('/settings/')) {
                const activeDropdownId = sessionStorage.getItem('activeSettingsDropdown');
                if (activeDropdownId) {
                    const dropdownButton = document.getElementById(activeDropdownId);
                    if (dropdownButton) {
                        const dropdown = new bootstrap.Dropdown(dropdownButton);
                        dropdown.show();
                    }
                }
            } else {
                // Clear the stored dropdown if not on a settings page
                sessionStorage.removeItem('activeSettingsDropdown');
            }

            // Setup dropdown toggle event listeners
            const dropdownButtons = document.querySelectorAll('.dropdown-toggle');
            dropdownButtons.forEach(button => {
                button.addEventListener('show.bs.dropdown', function() {
                    // Store this dropdown as the active one if on settings page
                    if (window.location.pathname.includes('/settings/')) {
                        sessionStorage.setItem('activeSettingsDropdown', this.id);
                    }
                });
            });

            // Setup sidebar mobile toggle
            const sidebarToggleBtn = document.getElementById('sidebar-toggler'); // Corrected ID
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop'); // Get backdrop element

            if (sidebarToggleBtn && sidebar && backdrop) { // Check for backdrop too
                sidebarToggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show'); // Use 'show' class
                    backdrop.classList.toggle('show'); // Toggle backdrop visibility
                });
            }

            // Close sidebar when backdrop is clicked
            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    backdrop.classList.remove('show');
                });
            }
        });
    </script>

    @stack('styles')

    <!-- Session Messages - Anti-flicker implementation -->
</head>
<body>
    <!-- Main Preloader -->
    <div id="preloader">
        <div class="loader"></div>
    </div>

    <!-- AJAX Indicator -->
    <div class="ajax-loader">
        <div class="spinner"></div>
        <span>Memproses...</span>
    </div>

    @if(!request()->is('login'))
    <!-- Bootstrap Sidebar Component -->
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white sidebar vh-100" id="sidebar" style="width: 280px; background-color: var(--bg-sidebar, #255cdc);">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <img src="{{ asset('images/leave.png') }}" alt="Leave Icon" class="me-2" style="height: 32px; width: auto;">
            <span class="fs-4 fw-semibold">SiMoci</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-white' }}">
                    <i class="fas fa-tachometer-alt fa-fw me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('cutis.index') }}" class="nav-link {{ request()->is('cutis') || request()->is('cutis/*') && !request()->is('cuti-calendar*') ? 'active' : 'text-white' }}">
                    <i class="fas fa-calendar-alt fa-fw me-2"></i>
                    Data Cuti
                </a>
            </li>

            <!-- Monitoring Collapsible Dropdown -->
            <li class="nav-item">
                <a href="#monitoringSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->is('cuti-calendar*') || request()->is('transportasi-details*') || request()->is('memo-kompensasi*') ? 'true' : 'false' }}" class="nav-link {{ request()->is('cuti-calendar*') || request()->is('transportasi-details*') || request()->is('memo-kompensasi*') ? 'active' : 'text-white' }} d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fas fa-chart-line fa-fw me-2"></i>
                        Monitoring
                    </span>
                    <i class="fas fa-angle-down"></i>
                </a>
                <ul class="collapse list-unstyled {{ request()->is('cuti-calendar*') || request()->is('transportasi-details*') || request()->is('memo-kompensasi*') ? 'show' : '' }}" id="monitoringSubmenu">
                    <li>
                        <a href="{{ route('cutis.calendar') }}" class="nav-link {{ request()->routeIs('cutis.calendar') ? 'active' : 'text-white' }} ps-4">
                            <i class="fas fa-calendar-check fa-fw me-2"></i>
                            Monitoring Cuti
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('transportasi_details.dashboard') }}" class="nav-link {{ request()->routeIs('transportasi_details.*') ? 'active' : 'text-white' }} ps-4">
                            <i class="fas fa-ticket-alt fa-fw me-2"></i>
                            Monitoring Tiket
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('memo-kompensasi.index') }}" class="nav-link {{ request()->routeIs('memo-kompensasi.*') ? 'active' : 'text-white' }} ps-4">
                            <i class="fas fa-file-alt fa-fw me-2"></i>
                            Monitoring Memo
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Pengaturan Collapsible Dropdown -->
            <li class="nav-item">
                <a href="#pengaturanSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->is('jenis-cutis*') || request()->is('transportasis*') || request()->is('karyawans*') || request()->is('users*') ? 'true' : 'false' }}" class="nav-link {{ request()->is('jenis-cutis*') || request()->is('transportasis*') || request()->is('karyawans*') || request()->is('users*') ? 'active' : 'text-white' }} d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fas fa-cogs fa-fw me-2"></i>
                        Pengaturan
                    </span>
                    <i class="fas fa-angle-down"></i>
                </a>
                <ul class="collapse list-unstyled {{ request()->is('jenis-cutis*') || request()->is('transportasis*') || request()->is('karyawans*') || request()->is('users*') ? 'show' : '' }}" id="pengaturanSubmenu">
                    <li>
                        <a href="{{ route('jenis-cutis.index') }}" class="nav-link {{ request()->routeIs('jenis-cutis.*') ? 'active' : 'text-white' }} ps-4">
                            <i class="fas fa-list fa-fw me-2"></i>
                            Jenis Cuti
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('transportasis.index') }}" class="nav-link {{ request()->routeIs('transportasis.*') ? 'active' : 'text-white' }} ps-4">
                            <i class="fas fa-plane fa-fw me-2"></i>
                            Transportasi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('karyawans.index') }}" class="nav-link {{ request()->routeIs('karyawans.*') ? 'active' : 'text-white' }} ps-4">
                            <i class="fas fa-users fa-fw me-2"></i>
                            Karyawan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : 'text-white' }} ps-4">
                            <i class="fas fa-user-cog fa-fw me-2"></i>
                            Pengguna
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <hr>
        <div class="text-center text-white-50 small">
            <p class="mb-1">Created with <span class="heart">❤</span> by ak.restu</p>
            <p class="mb-0 version">v1.0.0</p>
        </div>
    </div>

    <!-- Mobile sidebar backdrop -->
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <!-- Floating sidebar trigger for mobile -->
    <div class="floating-sidebar-trigger d-md-none" id="floating-sidebar-trigger">
        <button class="btn btn-primary rounded-circle shadow-lg border-0" aria-label="Open sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="main-navbar navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
            <div class="container-fluid">
                <button id="sidebar-toggler" class="navbar-toggler me-2" type="button" aria-label="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="ms-auto"> <!-- Changed from navbar-user to ms-auto for alignment -->
                    @auth
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-none d-sm-inline me-2">{{ Auth::user()->name }}</span> <!-- Show name on sm screens and up -->
                            <div class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-fw me-2"></i> {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main class="content-wrapper py-4"> <!-- Added py-4 for padding -->
            @yield('content')
        </main>
    </div>
    @else
        @yield('content')
    @endif

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (diperlukan untuk AJAX) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Session Messages - Anti-flicker implementation -->
    @if(session('status') || session('success') || session('error') || session('info') || session('warning'))
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: var(--z-toast); opacity: 0; visibility: hidden;">
        @if (session('success'))
        <div id="success-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
        @endif

        @if (session('status'))
        <div id="status-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-info text-white">
                <i class="fas fa-info-circle me-2"></i>
                <strong class="me-auto">Information</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('status') }}
            </div>
        </div>
        @endif

        @if (session('error'))
        <div id="error-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-danger text-white">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('error') }}
            </div>
        </div>
        @endif

        @if (session('info'))
        <div id="info-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-info text-white">
                <i class="fas fa-info-circle me-2"></i>
                <strong class="me-auto">Information</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('info') }}
            </div>
        </div>
        @endif

        @if (session('warning'))
        <div id="warning-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-warning text-dark">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong class="me-auto">Warning</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('warning') }}
            </div>
        </div>
        @endif
    </div>

    <script>
        // Advanced anti-flicker toast notification system - bottom right position
        document.addEventListener('DOMContentLoaded', function() {
            // Get toast container
            const toastContainer = document.getElementById('toast-container');

            // Skip if no container or no toasts
            if (!toastContainer || !toastContainer.querySelector('.toast')) {
                return;
            }

            // Create bootstrap toast instances with proper configuration
            const toasts = toastContainer.querySelectorAll('.toast');
            const toastInstances = [];
            let activeToastCount = 0;

            // Configure all toasts before showing any of them
            toasts.forEach((toastElement, index) => {
                // Add slight delay for each toast to create a staggered effect
                const delay = 5000 + (index * 500); // Longer delay for later toasts

                // Create toast with animation disabled - we'll handle animations manually
                const toast = new bootstrap.Toast(toastElement, {
                    delay: delay,
                    animation: false // Important: disable Bootstrap's animations
                });

                toastInstances.push(toast);

                // Add slide-in effect to each toast
                toastElement.style.transform = 'translateY(50px)';
                toastElement.style.opacity = '0';

                // Set up toast hidden event
                toastElement.addEventListener('hidden.bs.toast', function() {
                    activeToastCount--;

                    // Slide out animation
                    this.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';
                    this.style.transform = 'translateY(50px)';
                    this.style.opacity = '0';

                    // If no more active toasts, fade out container
                    if (activeToastCount === 0) {
                        // First set transition
                        toastContainer.style.transition = 'opacity 300ms ease-out';
                        toastContainer.style.opacity = '0';

                        // Then hide container after fade out
                        setTimeout(() => {
                            toastContainer.style.visibility = 'hidden';
                        }, 300);
                    }
                });
            });

            // Bottom-up sequential toast display
            setTimeout(() => {
                // Make container visible
                toastContainer.style.visibility = 'visible';
                toastContainer.style.opacity = '1';

                // Show toasts with staggered timing
                toastInstances.forEach((toast, index) => {
                    setTimeout(() => {
                        // Show the Bootstrap toast
                        toast.show();
                        activeToastCount++;

                        // Get the element and animate it
                        const toastElement = toasts[index];
                        toastElement.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';
                        toastElement.style.transform = 'translateY(0)';
                        toastElement.style.opacity = '1';
                    }, index * 150); // Stagger each toast by 150ms
                });
            }, 100);
        });
    </script>
    @endif

    @stack('scripts')

    <script>
        // Preloader control
        document.addEventListener('DOMContentLoaded', function() {
            // Manage page load preloader
            const preloader = document.getElementById('preloader');
            if (preloader) {
                // Hide preloader after a slight delay to ensure content is rendered
                setTimeout(() => {
                    preloader.classList.add('hide');

                    // Remove preloader from DOM after animation completes
                    setTimeout(() => {
                        preloader.style.display = 'none';
                    }, 500);
                }, 500);
            }

            // Setup AJAX loader indicator
            $(document).ajaxStart(function() {
                $('.ajax-loader').addClass('show');
            }).ajaxStop(function() {
                $('.ajax-loader').removeClass('show');
            });

            // Initialize Bootstrap tooltips & popovers
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            popoverTriggerList.map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

            // Initialize Select2
            if ($.fn.select2) {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            }

            // CoreUI Sidebar Nav Group functionality
            document.querySelectorAll('.nav-group-toggle').forEach(item => {
                item.addEventListener('click', event => {
                    event.preventDefault();
                    const parent = item.parentElement;
                    const isOpen = parent.classList.contains('show');

                    // Close all other open nav groups
                    document.querySelectorAll('.nav-group.show').forEach(openGroup => {
                        if (openGroup !== parent && !isOpen) {
                            openGroup.classList.remove('show');
                        }
                    });

                    // Toggle current nav group
                    parent.classList.toggle('show');
                });
            });

            // Mobile sidebar toggle functionality
            const sidebarToggler = document.getElementById('sidebar-toggler');
            const floatingTrigger = document.getElementById('floating-sidebar-trigger');
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');

            // Handle sidebar togglers (both header and floating button)
            const toggleSidebar = () => {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            };

            if (sidebarToggler && sidebar && backdrop) {
                sidebarToggler.addEventListener('click', toggleSidebar);
            }

            if (floatingTrigger && sidebar && backdrop) {
                floatingTrigger.querySelector('button').addEventListener('click', toggleSidebar);
            }

            // Close sidebar when backdrop is clicked
            if (backdrop) {
                backdrop.addEventListener('click', () => {
                    sidebar.classList.remove('show');
                    backdrop.classList.remove('show');
                });
            }

            // Hide floating trigger when sidebar is shown
            const updateFloatingTriggerVisibility = () => {
                if (floatingTrigger) {
                    if (sidebar.classList.contains('show')) {
                        floatingTrigger.classList.add('hide');
                    } else {
                        floatingTrigger.classList.remove('hide');
                    }
                }
            };

            // Set up mutation observer to watch for sidebar show/hide
            if (sidebar && floatingTrigger) {
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.attributeName === 'class') {
                            updateFloatingTriggerVisibility();
                        }
                    });
                });

                observer.observe(sidebar, { attributes: true });

                // Initial state
                updateFloatingTriggerVisibility();
            }
        });
    </script>
</body>
</html>
