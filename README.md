# Blog App - Laravel + Vue

A full-stack blog application with multi-authentication and posts management.

## Features

- Multi-auth for Admins and Users
- Role-based post management
- Image upload for posts
- Search and pagination
- Responsive UI

## Installation

1. Clone the repository
2. Run `composer install`
3. Run `npm install`
4. Copy `.env.example` to `.env`
5. Create a database named `blog_app` using phpMyAdmin:
   - Open `http://localhost/phpmyadmin` in your browser
   - Click on **"New"** in the left sidebar
   - Enter `blog_app` as the **Database name**
   - Select `utf8mb4_general_ci` as the **Collation** (recommended)
   - Click **"Create"**
6. Update your `.env` file with the correct DB credentials if there is a change
7. Generate app key: `php artisan key:generate`
8. Run migrations: `php artisan migrate`
9. Run seeders: `php artisan db:seed`
10. Link storage: `php artisan storage:link`
11. Build assets: `npm run dev`
12. Start server: `php artisan serve`

## Default Credentials

**Admin:**
- Email: admin@example.com
- Password: password

**User:**
- Email: user@example.com
- Password: password

## API Endpoints

- POST `/api/admin/login` - Admin login
- POST `/api/user/login` - User login
- POST `/api/logout` - Logout
- GET `/api/posts` - List posts
- GET `/api/posts{id}` - Get a post
- POST `/api/posts` - Create post
- PUT `/api/posts/{id}` - Update post
- DELETE `/api/posts/{id}` - Delete post

## Testing

Run tests: `php artisan test`