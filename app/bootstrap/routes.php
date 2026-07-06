<?php
declare(strict_types=1);

use Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AboutController;
use App\Controllers\ServicesController;
use App\Controllers\PortfolioController;
use App\Controllers\ContactController;
use App\Controllers\BookingController;
use App\Controllers\Admin\LoginController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\AlbumController;
use App\Controllers\Admin\PhotoController;
use App\Controllers\Admin\ServiceController;
use App\Controllers\Admin\BookingController as AdminBookingController;
use App\Controllers\Admin\ContactController as AdminContactController;
use App\Controllers\Admin\SettingsController;

$router = new Router();

// Public Routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [AboutController::class, 'index']);
$router->get('/services', [ServicesController::class, 'index']);
$router->get('/portfolio', [PortfolioController::class, 'index']);
$router->get('/portfolio/ajax', [PortfolioController::class, 'ajax']);
$router->get('/contact', [ContactController::class, 'index']);
$router->post('/contact/submit', [ContactController::class, 'submit']);
$router->get('/booking', [BookingController::class, 'index']);
$router->post('/booking/submit', [BookingController::class, 'submit']);

// Admin Authentication
$router->get('/admin/login', [LoginController::class, 'showLogin']);
$router->post('/admin/login', [LoginController::class, 'login']);
$router->get('/admin/logout', [LoginController::class, 'logout']);

// Admin Dashboard
$router->get('/admin', [DashboardController::class, 'index']);

// Admin Categories
$router->get('/admin/categories', [CategoryController::class, 'index']);
$router->get('/admin/categories/create', [CategoryController::class, 'create']);
$router->post('/admin/categories/store', [CategoryController::class, 'store']);
$router->get('/admin/categories/edit', [CategoryController::class, 'edit']);
$router->post('/admin/categories/update', [CategoryController::class, 'update']);
$router->post('/admin/categories/delete', [CategoryController::class, 'delete']);

// Admin Albums
$router->get('/admin/albums', [AlbumController::class, 'index']);
$router->get('/admin/albums/create', [AlbumController::class, 'create']);
$router->post('/admin/albums/store', [AlbumController::class, 'store']);
$router->post('/admin/albums/delete', [AlbumController::class, 'delete']);

// Admin Photos
$router->get('/admin/photos', [PhotoController::class, 'index']);
$router->get('/admin/photos/upload', [PhotoController::class, 'upload']);
$router->post('/admin/photos/store', [PhotoController::class, 'store']);
$router->post('/admin/photos/delete', [PhotoController::class, 'delete']);

// Admin Services
$router->get('/admin/services', [ServiceController::class, 'index']);
$router->get('/admin/services/create', [ServiceController::class, 'create']);
$router->post('/admin/services/store', [ServiceController::class, 'store']);
$router->get('/admin/services/edit', [ServiceController::class, 'edit']);
$router->post('/admin/services/update', [ServiceController::class, 'update']);
$router->post('/admin/services/delete', [ServiceController::class, 'delete']);

// Admin Bookings
$router->get('/admin/bookings', [AdminBookingController::class, 'index']);
$router->post('/admin/bookings/update', [AdminBookingController::class, 'update']);

// Admin Contacts (Inbox)
$router->get('/admin/contacts', [AdminContactController::class, 'index']);
$router->post('/admin/contacts/read', [AdminContactController::class, 'markAsRead']);
$router->post('/admin/contacts/delete', [AdminContactController::class, 'delete']);

// Admin Settings
$router->get('/admin/settings', [SettingsController::class, 'index']);
$router->post('/admin/settings/update', [SettingsController::class, 'update']);

return $router;