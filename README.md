# Laravel 9 Task Management System

## 🛠 Project Setup Instructions

### **Step 1: Clone the Repository**

```bash
git clone <repository-url>
cd <project-folder>
```

### **Step 2: Install Dependencies**

```bash
composer install
```

### **Step 3: Configure Environment File**

```bash
cp .env.example .env
```

Set up the following in the `.env` file:

- **Database Configuration**
- **Mail Configuration** (if required)
- **Sanctum & JWT Keys** (for authentication)

### **Step 4: Generate Application Key**

```bash
php artisan key:generate
```

### **Step 5: Run Migrations & Seed Database**

```bash
php artisan migrate --seed
```

**Super Admin Credentials:**

- **Email:** [admin@gmail.com](mailto\:admin@gmail.com)
- **Password:** admin786

### **Step 6: Serve the Application**

```bash
php artisan serve
```

Now visit `http://127.0.0.1:8000` in your browser.

---

## 🚀 Features & Workflow

### **User Registration & Approval**

1. A new user registers via the `/register` API.
2. A **notification is sent to the Super Admin** about the new registration.
3. The **Super Admin approves the user** and assigns a role.
4. If the **role is **``, the user logs in and is redirected to the **User Dashboard**.

### **Task Management**

- Users can **Create, Update, Delete, and Manage Tasks**.
- While creating a task, users **select a category** from the dropdown (Categories created by the Super Admin).
- **Admin can view all tasks** and **comment on them**, which both **User & Admin** can see.

### **Admin Features**

- **View & Approve Users**
- **Assign Roles**
- **Manage Categories** (Create, Update, Delete)
- **View & Comment on Tasks**

### **User Features**

- **Manage Tasks** (Create, Edit, Delete)
- **View Admin Comments**
- **Select Task Category while creating a task**

---

## 📌 API Endpoints

### **Public Routes**

- `POST /register` - Register User
- `POST /login` - User Login
- `POST /forgot-password` - Forgot Password
- `POST /reset-password` - Reset Password

### **Admin Routes (Protected)**

- `GET /admin/users` - List Users
- `POST /admin/approve-user/{id}` - Approve User
- `DELETE /admin/destroy-user/{id}` - Delete User
- `POST /admin/update-user/{id}` - Update User Role
- `GET /admin/categories` - Get Categories
- `POST /admin/categories` - Create Category
- `PUT /admin/categories/{id}` - Update Category
- `DELETE /admin/categories/{id}` - Delete Category
- `GET /admin/tasks` - Get All Tasks
- `POST /admin/tasks/{task}/comment` - Add Comment on Task
- `GET /admin/tasks/{task}/comment` - Get Comments on Task
- `GET /admin/recent-activities` - Recent Activities
- `GET /admin/task-counts` - Get Task Counts (Completed, Pending, In Progress)
- `GET /admin/stats` - Get Stats
- `GET /admin/notifications` - Get Notifications
- `POST /admin/notifications/{id}/mark-as-read` - Mark Notification as Read

### **User Routes (Protected)**

- `GET /user/tasks` - Get User Tasks
- `POST /user/tasks` - Create Task
- `PUT /user/tasks/{task}` - Update Task
- `DELETE /user/tasks/{task}` - Delete Task
- `GET /user/tasks/{task}/comments` - Get Task Comments
- `GET /user/categories` - Get Categories

### **Common Routes**

- `POST /logout` - Logout User

---

## 📂 Technologies Used

- **Laravel 9** (Backend)
- **Sanctum Authentication**
- **MySQL** (Database)
- **Eloquent ORM**
- **Reaxt.js **
- **Bootstrap/Tailwind CSS**

---

## 🎯 Additional Features

- **Form Validations Applied** ✅
- **Search, Filters & Pagination on Tables** ✅
- **Proper Role-Based Access Control** ✅

