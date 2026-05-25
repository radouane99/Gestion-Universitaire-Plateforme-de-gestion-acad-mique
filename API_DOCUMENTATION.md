# UPF Academic Portal - API REST Documentation

This module exposes a complete, secure REST API designed for programmatically accessing the UPF Academic Portal's data.
It strictly adheres to RESTful conventions, using Laravel Sanctum for robust token-based authentication.

---

## 1. Authentication (Sanctum)

All secured endpoints require a `Bearer` token. To obtain this token, you must authenticate using your credentials.

### Login Endpoint
*   **URL**: `/api/login`
*   **Method**: `POST`
*   **Headers**: 
    * `Accept: application/json`
    * `Content-Type: application/json`
*   **Body (JSON)**:
    ```json
    {
        "email": "student@university.com",
        "password": "password"
    }
    ```
*   **Success Response (200 OK)**:
    ```json
    {
        "status": "success",
        "access_token": "3|1234abcd5678efgh9012ijkl",
        "token_type": "Bearer",
        "user": {
            "id": 15,
            "name": "Amine El Amrani",
            "email": "student@university.com",
            "role": "student"
        }
    }
    ```
*   **Error Response (401 Unauthorized)**:
    ```json
    {
        "status": "error",
        "message": "Invalid credentials"
    }
    ```

---

## 2. Resources

For all requests below, include the following header:
`Authorization: Bearer <your_access_token>`

### 2.1 Get Modules
Retrieves all modules taught in the university.
*   **URL**: `/api/modules`
*   **Method**: `GET`
*   **Security**: Bearer Token required
*   **Success Response (200 OK)**:
    ```json
    {
        "status": "success",
        "modules": [
            {
                "id": 1,
                "code": "INF-201",
                "name": "Algorithmique & Structures de Données",
                "coefficient": "2.00",
                "created_at": "...",
                "updated_at": "..."
            }
        ]
    }
    ```

### 2.2 Get Grades (Student Only)
Returns the grades strictly for the connected student. Returns `403 Forbidden` if accessed by a non-student.
*   **URL**: `/api/grades`
*   **Method**: `GET`
*   **Security**: Bearer Token required
*   **Success Response (200 OK)**:
    ```json
    {
        "status": "success",
        "student": {
            "id": 1,
            "student_number": "S202601",
            "name": "Amine El Amrani"
        },
        "grades": [
            {
                "id": 1,
                "student_id": 1,
                "module_id": 1,
                "cc1": "14.50",
                "cc2": "15.00",
                "exam": "16.00",
                "final_grade": "15.40",
                "module": { ... }
            }
        ]
    }
    ```

### 2.3 Get Schedule (Role-Based)
Retrieves the timetable based on the user's role. A student sees their group's schedule, a professor sees their teaching schedule, and an admin sees all schedules.
*   **URL**: `/api/schedule`
*   **Method**: `GET`
*   **Security**: Bearer Token required
*   **Success Response (200 OK)**:
    ```json
    {
        "status": "success",
        "role": "student",
        "schedules": [
            {
                "id": 1,
                "day_of_week": 1,
                "start_time": "08:30:00",
                "end_time": "10:30:00",
                "module": { "name": "Algorithmique & Structures de Données" },
                "room": { "name": "Amphi Ibn Khaldoun" },
                "professor": { ... }
            }
        ]
    }
    ```

### 2.4 Get Absences (Student Only)
Retrieves the attendance/absence record for the connected student.
*   **URL**: `/api/absences`
*   **Method**: `GET`
*   **Security**: Bearer Token required
*   **Success Response (200 OK)**:
    ```json
    {
        "status": "success",
        "student": { ... },
        "absences": [
            {
                "id": 1,
                "date": "2026-05-12",
                "session_type": "course",
                "is_justified": 0,
                "justification_status": "none",
                "module": { ... }
            }
        ]
    }
    ```

---

## 3. Testing with cURL

### Step 1: Login to get token
```bash
curl -X POST http://127.0.0.1:8000/api/login \
     -H "Accept: application/json" \
     -H "Content-Type: application/json" \
     -d '{"email": "student@university.com", "password": "password"}'
```

### Step 2: Fetch Grades
*(Replace `YOUR_TOKEN` with the `access_token` returned from the login request)*
```bash
curl -X GET http://127.0.0.1:8000/api/grades \
     -H "Accept: application/json" \
     -H "Authorization: Bearer YOUR_TOKEN"
```

## 4. Test Accounts

For demonstration during the presentation, you can use these seeded credentials:
- **Student**: `student@university.com` / `password`
- **Professor**: `prof@university.com` / `password`
- **Admin**: `admin@university.com` / `password`
