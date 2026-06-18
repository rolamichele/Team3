## Team
Event Management System - Team 3

# Event Management System API

A RESTful API for managing event booking services, vendors, packages, and orders.

## Features
- JWT Authentication
- User Registration & Login
- Vendor Registration & Login
- Category Management
- Vendor Management
- Package Management
- Order Management
- Search & Filtering
- Pagination
- Wallet Tracking
- Send Email
- Reviews & Ratings

## Tech Stack
- PHP
- MySQL
- JWT
- REST API
- Postman

## Project Structure

Event/
│
├── Repos/              # Database operations
│
├── controller/         # Business logic
│
├── Routes/             # API Endpoints
│
├── config/             # Database & application configuration
│
└── helper/
    ├── Jwt.php         # JWT generation & verification
    └── response.php    # Standard API responses

## Authentication

Protected routes require:

```http
Authorization: Bearer YOUR_TOKEN
```

## Modules

### Authentication
- Signup
- Login
- Current User
- Vendor Login

### Categories
- Get All Categories
- Get Category By ID
- Create Category
- Update Category
- Delete Category

### Vendors
- Get All Vendors
- Get Vendor By ID
- Search Vendors
- Filter By Category
- Filter By Location
- Filter By Day
- Active Vendors
- Top Rated Vendors
- Update Vendor
- Current Vendor

### Packages
- Get All Packages
- Get Package By ID
- Create Package
- Update Package
- Delete Package
- Review Package

### Orders
- Get Orders
- Get Order By ID
- Create Order
- Update Order Status
- Delete Order
- Vendor Wallet
