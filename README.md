# 🎬 MovieBuzz - Movie Ticket Booking Platform

MovieBuzz is a full-stack movie ticket booking web application developed using **Laravel 12** and **MySQL**. The system allows customers to browse movies, book cinema tickets, make demo wallet payments, manage bookings, and request refunds. Administrators can manage movies, cinemas, halls, shows, bookings, users, and platform content through a dedicated admin dashboard.

The project also integrates the **TMDB (The Movie Database) API**, allowing administrators to fetch movie information automatically by entering a movie title instead of manually filling every field.

---

# Features

## Customer Features

- User Registration & Login
- Secure Authentication
- Browse Movies
- Search Movies
- Movie Details Page
- Browse Cinemas
- Browse Available Shows
- Seat Selection
- Real-time Seat Availability
- Demo Wallet Recharge
- Ticket Booking
- Booking Confirmation
- Download Ticket
- Booking History
- Ticket Cancellation
- Automatic Wallet Refund
- Wishlist
- Movie Reviews & Ratings
- User Profile Management
- Password Change

---

## Admin Features

- Admin Login
- Dashboard with Statistics
- Movie Management (CRUD)
- Fetch Movie Details from TMDB API
- Automatic Movie Poster
- Automatic Genres
- Automatic Runtime
- Automatic Release Date
- Automatic Overview
- Cinema Management
- Hall Management
- Seat Layout Management
- Show Scheduling
- Booking Management
- User Management
- Review Moderation
- Coupon Management
- Featured Movies
- Cache Management

---

# TMDB API Integration

Instead of entering every movie manually, an administrator can simply type the movie title.

Example:

```
Avengers
```

MovieBuzz automatically retrieves

- Poster
- Backdrop
- Overview
- Genres
- Release Date
- Runtime
- Language
- Popularity
- Rating
- Vote Count

using

```
https://api.themoviedb.org
```

---

# Booking Workflow

```
Customer Registration
        │
        ▼
Browse Movies
        │
        ▼
Select Movie
        │
        ▼
Select Cinema
        │
        ▼
Select Show
        │
        ▼
Choose Seats
        │
        ▼
Payment (Demo Wallet)
        │
        ▼
Booking Confirmed
        │
        ▼
Download Ticket
```

---

# Ticket Cancellation Workflow

```
Booking
     │
     ▼
Cancel Ticket
     │
     ▼
Booking Status Updated
     │
     ▼
Refund Added To Wallet
```

---

# Admin Workflow

```
Admin Login
      │
      ▼
Dashboard
      │
      ├── Movies
      ├── Cinemas
      ├── Halls
      ├── Shows
      ├── Users
      ├── Bookings
      ├── Reviews
      └── Coupons
```

---

# Technology Stack

## Backend

- Laravel 12
- PHP 8.4

## Frontend

- Blade Templates
- Bootstrap 5
- JavaScript
- AJAX

## Database

- MySQL
- phpMyAdmin

## API

- TMDB API

## Authentication

- Laravel Authentication
- Admin Guard
- Customer Guard

---

# Database

The project uses MySQL.

Main entities include

- Users
- Admins
- Movies
- Genres
- Cinemas
- Halls
- Seats
- Shows
- Bookings
- Booking Seats
- Payments
- Wallet Transactions
- Reviews
- Coupons
- Wishlists

---

# Installation

## Clone Repository

```bash
git clone https://github.com/yourusername/MovieBuzz.git
```

---

## Enter Project

```bash
cd MovieBuzz
```

---

## Install Dependencies

```bash
composer install
```

```bash
npm install
```

---

## Environment

Copy

```bash
.env.example
```

to

```bash
.env
```

Generate key

```bash
php artisan key:generate
```

---

## Configure Database

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=moviebuzz
DB_USERNAME=root
DB_PASSWORD=
```

---

## TMDB API

Add your API key inside `.env`

```
TMDB_API_KEY=YOUR_API_KEY
```

---

## Run Migrations

```bash
php artisan migrate
```

---

## Seed Database

```bash
php artisan db:seed
```

---

## Storage Link

```bash
php artisan storage:link
```

---

## Start Server

```bash
php artisan serve
```

---

# Project Structure

```
app/
    Http/
    Models/
    Services/

resources/
    views/
        admin/
        customer/

routes/
    web.php
    admin.php

database/
    migrations/
    seeders/

public/

storage/
```

---

# Screens

### Customer

- Home
- Movie List
- Movie Details
- Seat Selection
- Booking Summary
- Wallet
- Booking History
- Profile

### Admin

- Dashboard
- Movies
- Cinemas
- Halls
- Shows
- Bookings
- Users
- Reviews
- Coupons

---

# Demo Payment

MovieBuzz uses a **Demo Wallet Payment System**.

Customers can

- Recharge Wallet
- Book Tickets
- Receive Refunds
- View Wallet Transactions

No real payment gateway is required.

---

# Future Improvements

- SSLCommerz Integration
- Stripe Integration
- Razorpay Integration
- Email Ticket Delivery
- QR Code Ticket Verification
- Push Notifications
- Multi-Cinema Support
- Mobile Application
- Online Food Ordering
- Loyalty Rewards
- Recommendation System

---

# Author

**Bipro Biswas**

Department of Computer Science and Engineering

Khulna University of Engineering & Technology (KUET)

Bangladesh

---

# License

This project was developed for educational and academic purposes.

```
© MovieBuzz - Movie Ticket Booking Platform
```