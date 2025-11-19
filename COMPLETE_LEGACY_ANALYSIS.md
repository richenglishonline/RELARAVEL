# Complete Legacy System Analysis

This document provides a **COMPREHENSIVE** analysis of EVERY feature, page, route, and functionality found in the legacy MERN stack application (`legacy-mern-client` and `legacy-mern-server`).

## Analysis Date
**Date:** 2025-01-27  
**Scope:** Complete analysis of both `legacy-mern-client` and `legacy-mern-server` directories

---

## Table of Contents

1. [User Roles & Permissions](#user-roles--permissions)
2. [API Endpoints (Complete List)](#api-endpoints-complete-list)
3. [Frontend Pages (Complete List)](#frontend-pages-complete-list)
4. [Database Models/Schemas](#database-modelsschemas)
5. [Features by Module](#features-by-module)
6. [Authentication & Security](#authentication--security)
7. [File Upload Features](#file-upload-features)
8. [Real-time Features](#real-time-features)
9. [UI Components](#ui-components)
10. [Third-party Integrations](#third-party-integrations)

---

## User Roles & Permissions

### 1. **Teacher** (`role: "teacher"`)
- **Access Level:** Limited to own resources
- **Key Permissions:**
  - View own students
  - View own classes and schedule
  - Create/upload recordings and screenshots
  - View assigned books
  - Manage makeup classes
  - View own attendance records
  - Generate own reports
  - Cannot create/delete students
  - Cannot manage other teachers
  - Cannot access payout information

### 2. **Admin** (`role: "admin"`)
- **Access Level:** Management of assigned teachers
- **Key Permissions:**
  - View and manage assigned teachers
  - View all students (read-only, cannot create/delete)
  - Create/update/delete classes
  - Create/update/delete attendance records
  - Upload books
  - Create/update/delete book assignments
  - Update/delete recordings and screenshots
  - View payout summaries
  - Search functionality
  - Cannot create/delete students (super-admin only)
  - Cannot delete teachers (super-admin only)
  - Cannot manage payouts (super-admin only)
  - Cannot manage other admins

### 3. **Super Admin** (`role: "super-admin"`)
- **Access Level:** Full system access
- **Key Permissions:**
  - Full CRUD on all resources
  - Create/delete students
  - Create/delete teachers and admins
  - Manage all payouts and salary
  - Manage system settings
  - Assign books and manage curriculum
  - Update/delete any recording or screenshot
  - Advanced search
  - System-wide reports and analytics

---

## API Endpoints (Complete List)

### Authentication (`/api/v1/auth`)
- `POST /api/v1/auth/login` - Login with email/password
- `DELETE /api/v1/auth/logout` - Logout (requires auth)
- `GET /api/v1/auth/refresh` - Refresh token (requires auth)
- `POST /api/v1/auth/forgot-password` - Request password reset
- `POST /api/v1/auth/reset-password` - Reset password with token (requires auth)
- `POST /api/v1/auth/verify-otp` - Verify OTP code
- `POST /api/v1/auth/resend-email` - Resend OTP email

### Students (`/api/v1/students`)
- `GET /api/v1/students` - List students (paginated, filtered)
  - Query params: `name`, `nationality`, `manager_type`, `category_level`, `class_type`, `platform`, `page`, `limit`
- `POST /api/v1/students` - Create student (super-admin only)
- `GET /api/v1/students/:id` - Get student details
- `PATCH /api/v1/students/:id` - Update student
- `DELETE /api/v1/students/:id` - Delete student (super-admin only)

### Teachers (`/api/v1/teacher`)
- `POST /api/v1/teacher/application` - Submit teacher application (public)
- `GET /api/v1/teacher` - List teachers (admin, super-admin)
  - Query params: `page`, `limit`
- `POST /api/v1/teacher` - Create teacher (admin, super-admin)
- `GET /api/v1/teacher/:id` - Get teacher details
- `PATCH /api/v1/teacher/:id` - Update teacher (admin, super-admin)
- `DELETE /api/v1/teacher/:id` - Delete teacher (super-admin only)

### Classes (`/api/v1/class`)
- `GET /api/v1/class` - List classes (paginated, filtered)
  - Query params: `teacher_id`, `name`, `page`, `limit`
- `POST /api/v1/class` - Create class (admin, super-admin)
- `GET /api/v1/class/:id` - Get class details
- `PATCH /api/v1/class/:id` - Update class
- `DELETE /api/v1/class/:id` - Delete class

### Attendance (`/api/v1/attendance`)
- `GET /api/v1/attendance` - List attendance records (paginated, filtered)
- `POST /api/v1/attendance` - Create attendance (admin, super-admin)
- `GET /api/v1/attendance/:id` - Get attendance details
- `PATCH /api/v1/attendance/:id` - Update attendance (admin, super-admin)
- `DELETE /api/v1/attendance/:id` - Delete attendance (admin, super-admin)

### Books (`/api/v1/books`)
- `GET /api/v1/books` - List books (paginated, filtered)
  - Query params: `title`, `filename`, `uploaded_by`, `page`, `limit`
- `POST /api/v1/books` - Upload book PDF (admin, super-admin)
  - Multipart form: `file` (PDF, max 50MB), `title`
- `GET /api/v1/books/:id` - Get book details
- `GET /api/v1/books/:id/stream` - Stream PDF file

### Book Assignments (`/api/v1/book-assign`)
- `GET /api/v1/book-assign` - List all book assignments
- `POST /api/v1/book-assign` - Create assignment (admin, super-admin)
  - Body: `student_id`, `teacher_id`, `book_id`
- `GET /api/v1/book-assign/:id` - Get assignment details
- `PATCH /api/v1/book-assign/:id` - Update assignment (admin, super-admin)
- `DELETE /api/v1/book-assign/:id` - Delete assignment (admin, super-admin)

### Payouts (`/api/v1/payout`)
- `GET /api/v1/payout` - List payouts (super-admin only)
  - Query params: `teacher_id`, `status`, `start_date`, `end_date`, `page`, `limit`
- `POST /api/v1/payout` - Create payout (super-admin only)
  - Body: `teacher_id`, `start_date`, `end_date`, `duration`, `total_class`, `incentives`, `status`
- `GET /api/v1/payout/:id` - Get payout details
- `PATCH /api/v1/payout/:id` - Update payout (super-admin only)
- `DELETE /api/v1/payout/:id` - Delete payout (super-admin only)

### Recordings (`/api/v1/recording`)
- `GET /api/v1/recording` - List recordings (paginated, filtered)
- `POST /api/v1/recording` - Upload recording (all authenticated users)
  - Multipart form: `file` (video: mp4, mkv, webm, max 500MB)
- `GET /api/v1/recording/:id` - Get recording details
- `PATCH /api/v1/recording/:id` - Update recording (admin only)
- `DELETE /api/v1/recording/:id` - Delete recording (admin only)

### Screenshots (`/api/v1/screen-shot`)
- `GET /api/v1/screen-shot` - List screenshots (paginated, filtered)
- `POST /api/v1/screen-shot` - Upload screenshot (all authenticated users)
  - Multipart form: `file` (image: png, jpeg, jpg, webp, max 20MB)
- `GET /api/v1/screen-shot/:id` - Get screenshot details
- `PATCH /api/v1/screen-shot/:id` - Update screenshot (admin only)
- `DELETE /api/v1/screen-shot/:id` - Delete screenshot (admin only)

### Messages (`/api/v1/message`)
- `GET /api/v1/message/get-user` - Get list of users for messaging
- `GET /api/v1/message/:id` - Get messages with specific user
- `POST /api/v1/message` - Send message
  - Body: `receiver_id`, `message`

### Notifications (`/api/v1/notification`)
- `GET /api/v1/notification/:id` - Get notification details
- `POST /api/v1/notification` - Create notification
  - Body: `id` (user_id), `type`, `message`
- `PATCH /api/v1/notification/:id` - Update notification (mark as read)
  - Body: `is_read`

### Dashboard (`/api/v1/dashboard`)
- `GET /api/v1/dashboard/stats` - Get dashboard statistics (role-based)
- `GET /api/v1/dashboard/students` - Get student dropdown list
- `GET /api/v1/dashboard/teachers` - Get teacher dropdown list (admin, super-admin, teacher)

### Health Check
- `GET /api/health` - Health check endpoint

---

## Frontend Pages (Complete List)

### Public Pages (No Authentication)

1. **Landing Page** (`/`)
   - Hero section with CTA
   - Features section
   - Benefits section
   - Testimonials
   - Contact form
   - Footer with links

2. **About** (`/about`)
   - Company information
   - Mission and values

3. **Contact** (`/contact`)
   - Contact form
   - Contact information

4. **FAQ** (`/faq`)
   - Frequently asked questions

5. **Teacher Application** (`/apply`)
   - Multi-step form (4 steps)
   - Personal information
   - Qualifications
   - Equipment/technical requirements
   - File uploads (resume, intro video, speed test screenshot)

6. **Teacher Leaderboard** (`/leaderboard`)
   - Ranking of teachers
   - Statistics and achievements

7. **Login** (`/login`)
   - Email/password form
   - Remember me checkbox
   - Forgot password link
   - Redirects based on role after login

8. **Forgot Password** (`/forgot-password`)
   - Email input form
   - Sends OTP email

9. **OTP Verification** (`/otp`)
   - OTP code input
   - Resend OTP option

10. **Reset Password** (`/reset-password`)
    - New password form
    - Requires valid token

11. **404 Not Found** (`*`)
    - Error page for invalid routes

### Teacher Portal Pages (`/portal/teacher/*`)

1. **Dashboard** (`/portal/teacher/dashboard`)
   - Stats cards: Total Students, Active Classes, Today's Attendance, Pending Makeups
   - Calendar view with:
     - Active classes (green)
     - Scheduled classes (purple)
     - Makeup classes (red)
   - Upcoming classes list

2. **My Students** (`/portal/teacher/students`)
   - List of assigned students
   - Filters and search
   - Student detail view (`/portal/teacher/students/:id`)

3. **My Schedule** (`/portal/teacher/schedule`)
   - Personal teaching schedule
   - Calendar view

4. **Classes** (`/portal/teacher/classes`)
   - Calendar view of all classes
   - Class list
   - Class detail view (`/portal/teacher/classes/:id`)

5. **Makeup Classes** (`/portal/teacher/makeup-classes`)
   - List of makeup class requests
   - Create makeup class
   - Makeup class detail view (`/portal/teacher/makeup-classes/:id`)

6. **Attendance** (`/portal/teacher/attendance`)
   - View attendance records
   - Create attendance entries

7. **Books** (`/portal/teacher/books`)
   - List of assigned books
   - Book detail/viewer (`/portal/teacher/books/:id`)
   - PDF viewer integration

8. **Recordings** (`/portal/teacher/recordings`)
   - List of recordings
   - Upload recording
   - Recording detail view (`/portal/teacher/recordings/:id`)

9. **Reports** (`/portal/teacher/reports`)
   - Generate teaching reports
   - View report history

### Admin Portal Pages (`/portal/admin/*`)

1. **Dashboard** (`/portal/admin/dashboard`)
   - Admin-specific statistics

2. **Teachers** (`/portal/admin/teachers`)
   - List of assigned teachers
   - Teacher detail view (`/portal/admin/teachers/:id`)

3. **Students** (`/portal/admin/students`)
   - List of all students
   - Student detail view (`/portal/admin/students/:id`)

4. **Schedules** (`/portal/admin/schedules`)
   - Manage class schedules
   - Schedule detail view (`/portal/admin/schedules/:id`)

5. **Attendance** (`/portal/admin/attendance`)
   - View and manage attendance
   - Attendance detail view (`/portal/admin/attendance/:id`)

6. **Reports** (`/portal/admin/reports`)
   - Generate reports
   - Report detail view (`/portal/admin/reports/:id`)

7. **Payout Summary** (`/portal/admin/payouts`)
   - View payout information
   - Payout detail view (`/portal/admin/payouts/:id`)

8. **Screenshots** (`/portal/admin/screenshots`)
   - View class screenshots
   - Screenshot detail view (`/portal/admin/screenshots/:id`)

9. **Recordings** (`/portal/admin/recordings`)
   - View class recordings
   - Recording detail view (`/portal/admin/recordings/:id`)

10. **Books Archive** (`/portal/admin/books`)
    - View all books
    - Book detail view (`/portal/admin/books/:id`)

11. **Search** (`/portal/admin/search`)
    - Search functionality across system

### Super Admin Portal Pages (`/portal/super-admin/*`)

1. **Dashboard** (`/portal/super-admin/dashboard`)
   - System-wide statistics

2. **Teachers** (`/portal/super-admin/teachers`)
   - Full CRUD on teachers
   - Teacher detail view (`/portal/super-admin/teachers/:id`)

3. **Admins** (`/portal/super-admin/admins`)
   - Manage admin accounts
   - Admin detail view (`/portal/super-admin/admins/:id`)

4. **Students** (`/portal/super-admin/students`)
   - Full CRUD on students
   - Student detail view (`/portal/super-admin/students/:id`)

5. **Schedules** (`/portal/super-admin/schedules`)
   - Manage all schedules
   - Schedule detail view (`/portal/super-admin/schedules/:id`)

6. **Books Management** (`/portal/super-admin/books`)
   - Full book management
   - Book detail view (`/portal/super-admin/books/:id`)

7. **Assign Books** (`/portal/super-admin/assign-books`)
   - Manage book assignments
   - Assignment detail view (`/portal/super-admin/assign-books/:id`)

8. **Curriculum Access** (`/portal/super-admin/curriculum`)
   - Manage curriculum resources
   - Curriculum detail view (`/portal/super-admin/curriculum/:id`)

9. **Attendance** (`/portal/super-admin/attendance`)
   - Full attendance management
   - Attendance detail view (`/portal/super-admin/attendance/:id`)

10. **Reports** (`/portal/super-admin/reports`)
    - System-wide reports
    - Report detail view (`/portal/super-admin/reports/:id`)

11. **Salary Management** (`/portal/super-admin/salary`)
    - Manage teacher salaries
    - Salary detail view (`/portal/super-admin/salary/:id`)

12. **Payout Overview** (`/portal/super-admin/payouts`)
    - Full payout management
    - Payout detail view (`/portal/super-admin/payouts/:id`)

13. **Screenshots** (`/portal/super-admin/screenshots`)
    - View all screenshots
    - Screenshot detail view (`/portal/super-admin/screenshots/:id`)

14. **Recordings** (`/portal/super-admin/recordings`)
    - View all recordings
    - Recording detail view (`/portal/super-admin/recordings/:id`)

15. **Search** (`/portal/super-admin/search`)
    - Advanced search functionality

16. **Settings** (`/portal/super-admin/settings`)
    - System settings
    - Settings detail view (`/portal/super-admin/settings/:id`)

---

## Database Models/Schemas

### User Model
- `_id` (ObjectId)
- `name` (String)
- `email` (String, unique)
- `password` (String, hashed)
- `role` (String: "teacher", "admin", "super-admin")
- `country` (String)
- `status` (String: "active", "inactive")
- `timezone` (String)
- `accepted` (Boolean) - For teachers
- `assignedTeachers` (Array of ObjectIds) - For admins
- `reset` (Object) - `{ otp: String, expiration: Date }`
- `verification` (Object) - `{ isVerified: Boolean }`
- `createdAt`, `updatedAt` (Date)

### Teacher Profile (Embedded in User or Separate)
- `firstName`, `lastName` (String)
- `phone` (String)
- `degree`, `major` (String)
- `englishLevel` (String)
- `experience` (String)
- `motivation` (String)
- `availability` (String)
- `internetSpeed` (String)
- `computerSpecs` (String)
- `hasWebcam`, `hasHeadset`, `hasBackupInternet`, `hasBackupPower` (Boolean)
- `teachingEnvironment` (String)
- `resume`, `introVideo`, `speedTestScreenshot` (String - URLs)
- `zoom_link` (String)
- `birth_day` (Date)
- `assignedAdmin` (ObjectId reference)

### Student Model
- `_id` (ObjectId)
- `student_identification` (String, auto-generated)
- `name` (String)
- `age` (Number)
- `nationality` (String: "KOREAN", "CHINESE")
- `manager_type` (String: "KM", "CM")
- `email` (String)
- `book` (String)
- `category_level` (String)
- `class_type` (String)
- `platform` (String: "Zoom", "Voov")
- `platform_link` (String)
- `createdAt`, `updatedAt` (Date)

### Class Model
- `_id` (ObjectId)
- `teacher_id` (ObjectId reference)
- `student_id` (ObjectId reference)
- `type` (String: "schedule", "reoccurring", "makeupClass")
- `start_date`, `end_date` (Date)
- `start_time`, `end_time` (String)
- `reoccurringDays` (Array: ["M", "W", "F"])
- `duration` (Number - minutes)
- `platform_link` (String)
- `book_id` (ObjectId reference)
- `reason` (String) - For makeup classes
- `note` (String) - For makeup classes
- `original_class_id` (ObjectId) - For makeup classes
- `createdAt`, `updatedAt` (Date)

### Attendance Model
- `_id` (ObjectId)
- `teacher_id` (ObjectId reference)
- `student_id` (ObjectId reference)
- `class_id` (ObjectId reference)
- `date` (Date)
- `duration` (Number - minutes)
- `start_time`, `end_time` (String)
- `minutes_attended` (Number)
- `notes` (String)
- `screenshots` (Array of ObjectIds)
- `recording` (ObjectId reference)
- `createdAt`, `updatedAt` (Date)

### Book Model
- `_id` (ObjectId)
- `id` (String - short UUID)
- `title` (String)
- `filename` (String)
- `original_filename` (String)
- `path` (String)
- `uploaded_by` (ObjectId reference)
- `created_at` (Date)

### Book Assignment Model
- `_id` (ObjectId)
- `student_id` (ObjectId reference)
- `teacher_id` (ObjectId reference)
- `book_id` (ObjectId reference)
- `assigned_by` (ObjectId reference)
- `createdAt`, `updatedAt` (Date)

### Payout Model
- `_id` (ObjectId)
- `teacher_id` (ObjectId reference)
- `start_date`, `end_date` (Date)
- `duration` (Number - hours)
- `total_class` (Number)
- `incentives` (Number, default: 0)
- `status` (String: "pending", "processing", "completed")
- `createdAt`, `updatedAt` (Date)

### Recording Model
- `_id` (ObjectId)
- `class_id` (ObjectId reference)
- `uploaded_by` (ObjectId reference)
- `path` (String)
- `filename` (String)
- `drive_link` (String - optional, Google Drive)
- `createdAt`, `updatedAt` (Date)

### Screenshot Model
- `_id` (ObjectId)
- `class_id` (ObjectId reference)
- `uploaded_by` (ObjectId reference)
- `path` (String)
- `filename` (String)
- `drive_link` (String - optional, Google Drive)
- `createdAt`, `updatedAt` (Date)

### Notification Model
- `_id` (ObjectId)
- `user_id` (ObjectId reference)
- `type` (String: "offline_alert", "schedule_update", "payout_notice", etc.)
- `message` (String)
- `is_read` (Boolean, default: false)
- `created_at` (Date)

### Message Model
- `_id` (ObjectId)
- `sender_id` (ObjectId reference)
- `receiver_id` (ObjectId reference)
- `message` (String)
- `read_at` (Date, nullable)
- `createdAt`, `updatedAt` (Date)

---

## Features by Module

### Authentication Module
- ✅ Login with email/password
- ✅ Logout
- ✅ Token refresh
- ✅ Remember me functionality
- ✅ Forgot password flow
- ✅ OTP verification
- ✅ Password reset
- ✅ Role-based redirects after login
- ✅ Cookie-based token storage
- ✅ JWT token authentication

### Student Management Module
- ✅ List students (paginated, filtered)
- ✅ View student details
- ✅ Create student (super-admin only)
- ✅ Update student
- ✅ Delete student (super-admin only)
- ✅ Filter by: name, nationality, manager_type, category_level, class_type, platform
- ✅ Auto-generate student identification code

### Teacher Management Module
- ✅ Teacher application form (public)
- ✅ List teachers (admin, super-admin)
- ✅ View teacher details
- ✅ Create teacher (admin, super-admin)
- ✅ Update teacher (admin, super-admin)
- ✅ Delete teacher (super-admin only)
- ✅ Assign teachers to admins
- ✅ Teacher profile with qualifications
- ✅ Teacher acceptance workflow

### Class/Schedule Management Module
- ✅ List classes (paginated, filtered)
- ✅ View class details
- ✅ Create class (admin, super-admin)
- ✅ Update class
- ✅ Delete class
- ✅ Support for scheduled classes (one-time)
- ✅ Support for recurring classes
- ✅ Support for makeup classes
- ✅ Calendar view integration
- ✅ Filter by teacher_id, name

### Attendance Module
- ✅ List attendance records
- ✅ View attendance details
- ✅ Create attendance (admin, super-admin)
- ✅ Update attendance (admin, super-admin)
- ✅ Delete attendance (admin, super-admin)
- ✅ Track minutes attended
- ✅ Link to screenshots and recordings
- ✅ Notes field

### Book Management Module
- ✅ List books (paginated, filtered)
- ✅ View book details
- ✅ Upload book PDF (admin, super-admin)
  - Max file size: 50MB
  - PDF only
- ✅ Stream PDF files
- ✅ Filter by title, filename, uploaded_by

### Book Assignment Module
- ✅ List book assignments
- ✅ View assignment details
- ✅ Create assignment (admin, super-admin)
- ✅ Update assignment (admin, super-admin)
- ✅ Delete assignment (admin, super-admin)
- ✅ Track assigned_by (admin/super-admin)

### Payout Module
- ✅ List payouts (super-admin only)
- ✅ View payout details
- ✅ Create payout (super-admin only)
- ✅ Update payout (super-admin only)
- ✅ Delete payout (super-admin only)
- ✅ Track duration, total_class, incentives
- ✅ Status: pending, processing, completed
- ✅ Filter by teacher_id, status, date range

### Recording Module
- ✅ List recordings
- ✅ View recording details
- ✅ Upload recording (all authenticated users)
  - Max file size: 500MB
  - Formats: mp4, mkv, webm
- ✅ Update recording (admin only)
- ✅ Delete recording (admin only)
- ✅ Optional Google Drive link

### Screenshot Module
- ✅ List screenshots
- ✅ View screenshot details
- ✅ Upload screenshot (all authenticated users)
  - Max file size: 20MB
  - Formats: png, jpeg, jpg, webp
- ✅ Update screenshot (admin only)
- ✅ Delete screenshot (admin only)
- ✅ Optional Google Drive link

### Messaging Module
- ✅ Get list of users for messaging
- ✅ Get messages with specific user
- ✅ Send message
- ✅ Mark messages as read
- ✅ Real-time messaging (Socket.IO)

### Notification Module
- ✅ Get notification details
- ✅ Create notification
- ✅ Update notification (mark as read)
- ✅ Notification types: offline_alert, schedule_update, payout_notice
- ✅ Real-time notifications (Socket.IO)

### Dashboard Module
- ✅ Role-based dashboard statistics
- ✅ Student dropdown list
- ✅ Teacher dropdown list
- ✅ Calendar integration
- ✅ Stats cards (varies by role)

### Search Module
- ✅ Search functionality (admin, super-admin)
- ✅ Search across multiple entities

### Settings Module
- ✅ System settings (super-admin only)
- ✅ Settings detail view

### Curriculum Module
- ✅ Curriculum access management (super-admin only)
- ✅ Curriculum detail view

### Salary Module
- ✅ Salary management (super-admin only)
- ✅ Salary detail view

---

## Authentication & Security

### Authentication Flow
1. User submits login form
2. Server validates credentials
3. Server generates JWT token
4. Token stored in HTTP-only cookie
5. Token sent with subsequent requests
6. Middleware validates token on protected routes

### Security Features
- ✅ JWT token authentication
- ✅ HTTP-only cookies
- ✅ Password hashing (bcrypt)
- ✅ Role-based access control (RBAC)
- ✅ CORS configuration
- ✅ Helmet.js security headers
- ✅ Input validation (Joi)
- ✅ File upload validation
- ✅ OTP-based password reset

### Middleware
- `authenticateToken` - Validates JWT token
- `requireAdmin` - Checks user role (admin, super-admin)
- `cache` - Redis caching middleware
- `errorHandler` - Custom error handling
- `notFoundMiddleware` - 404 handler

---

## File Upload Features

### Book Uploads
- **Location:** `uploads/books/`
- **Max Size:** 50MB
- **Format:** PDF only
- **Naming:** `{timestamp}-{random}.pdf`

### Recording Uploads
- **Location:** `uploads/recording/`
- **Max Size:** 500MB
- **Formats:** mp4, mkv, webm
- **Naming:** `{timestamp}-{random}-{originalname}`

### Screenshot Uploads
- **Location:** `uploads/screenshots/`
- **Max Size:** 20MB
- **Formats:** png, jpeg, jpg, webp
- **Naming:** `{timestamp}-{random}-{originalname}`

### Teacher Application Files
- Resume (URL or file)
- Intro video (URL or file)
- Speed test screenshot (URL or file)

---

## Real-time Features

### Socket.IO Integration
- **Server:** Socket.IO server initialized in `index.js`
- **Client:** Socket.IO client in React app
- **Features:**
  - Real-time messaging
  - Real-time notifications
  - Live updates

### Socket Events (Inferred)
- `connection` - User connects
- `message` - Send/receive messages
- `notification` - Send/receive notifications
- `disconnect` - User disconnects

---

## UI Components

### Reusable Components
- `Layout` - Main layout with sidebar navigation
- `Header` - Public header
- `Footer` - Public footer
- `DashboardHeader` - Dashboard header
- `ChatSideBar` - Chat sidebar
- `MessageModal` - Message modal
- `ProtectedRoute` - Route protection component
- `DynamicCalendar` - Calendar component
- `DataTable` - Data table component
- `DataTableColumnHeader` - Table column header
- `DataTablePagination` - Table pagination
- `DynamicTable` - Dynamic table component
- `StudentForm` - Student form component

### UI Library Components (shadcn/ui)
- `badge` - Badge component
- `button` - Button component
- `calendar` - Calendar component
- `dialog` - Dialog/modal component
- `dropdown-menu` - Dropdown menu
- `input` - Input field
- `label` - Label component
- `select` - Select dropdown
- `skeleton` - Loading skeleton
- `table` - Table component

### Custom Hooks
- `useFormatClass` - Format class data for calendar
- `useFormatDate` - Format date data for calendar
- `useAuthStore` - Zustand auth store

---

## Third-party Integrations

### Google APIs
- Google Drive integration (commented out in code)
- OAuth2 for Google Drive access

### Email Service
- Nodemailer for sending emails
- OTP emails
- Password reset emails

### Caching
- Redis for caching
- Cache middleware for API responses

### Documentation
- Swagger/OpenAPI documentation
- Auto-generated from JSDoc comments

---

## Summary

### Total Counts
- **API Endpoints:** 50+ endpoints
- **Frontend Pages:** 60+ pages
- **Database Models:** 12+ models
- **User Roles:** 3 roles
- **File Upload Types:** 3 types (books, recordings, screenshots)
- **Real-time Features:** Messaging, notifications

### Key Technologies
- **Backend:** Node.js, Express, MongoDB, Mongoose
- **Frontend:** React, React Router, Zustand, React Query
- **Authentication:** JWT, bcrypt
- **File Upload:** Multer
- **Real-time:** Socket.IO
- **Caching:** Redis
- **Email:** Nodemailer
- **Documentation:** Swagger

### Migration Priority
1. **Critical:** Authentication, User management, Core CRUD operations
2. **High:** File uploads, Dashboard, Class management
3. **Medium:** Real-time features, Advanced search, Reports
4. **Low:** UI polish, Animations, Advanced features

---

**End of Analysis**




