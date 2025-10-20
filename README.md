# MyNotes - Notes Management Application

MyNotes is a simple yet powerful web application for creating, managing, and organizing personal notes. It is built with the Laravel framework for the backend, providing a robust RESTful API, and a clean, dynamic frontend using Blade templates with vanilla JavaScript.

The application is designed to be a single-page-like experience for note management, ensuring users can quickly add, edit, and delete notes without full page reloads.

---

## ✨ Features

-   **Secure User Authentication:** Users can register for a new account and log in securely. The application uses JWT (JSON Web Tokens) for stateless API authentication.
-   **Full CRUD Functionality for Notes:**
    -   **Create:** Easily add new notes with a title and content.
    -   **Read:** View all your notes in a clean, card-based layout.
    -   **Update:** Edit existing notes through a modal interface.
    -   **Delete:** Remove notes you no longer need with a confirmation prompt.
-   **Dynamic UI:** The notes management page is powered by JavaScript, interacting with the backend API asynchronously to provide a smooth user experience.
-   **Responsive Design:** The user interface is built with Bootstrap and custom CSS, ensuring it looks great on all devices, from desktops to mobile phones.
-   **RESTful API:** A well-defined API for handling all user and note-related operations.

---

## 🚀 Technology Stack

-   **Backend:**
    -   [PHP](https://www.php.net/)
    -   [Laravel](https://laravel.com/) Framework
-   **Frontend:**
    -   HTML5 & CSS3
    -   [JavaScript](https://developer.mozilla.org/en-US/docs/Web/JavaScript) (Vanilla)
    -   [Vite](https://vitejs.dev/) for asset bundling
    -   [Bootstrap 5](https://getbootstrap.com/) for responsive layout
    -   [Font Awesome](https://fontawesome.com/) for icons
-   **Database:**
    -   Compatible with MySQL, PostgreSQL, SQLite.

---

## 🔧 Installation and Setup

Follow these steps to get the project up and running on your local machine.

### Prerequisites

-   PHP >= 8.1
-   Composer
-   Node.js and npm
-   A database server (e.g., MySQL, MariaDB)

### Step-by-Step Guide

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/your-username/notes-management.git
    cd notes-management
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Install JavaScript dependencies:**
    ```bash
    npm install
    ```

4.  **Set up the environment file:**
    -   Copy the example environment file.
        ```bash
        cp .env.example .env
        ```
    -   Generate a new application key.
        ```bash
        php artisan key:generate
        ```

5.  **Configure the database:**
    -   Open the `.env` file and update the database connection details (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
        ```
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=mynotes
        DB_USERNAME=root
        DB_PASSWORD=
        ```

6.  **Run database migrations:**
    -   This will create the necessary tables (`users`, `notes`, etc.) in your database.
        ```bash
        php artisan migrate
        ```

7.  **Build frontend assets:**
    -   Compile the CSS and JavaScript files.
        ```bash
        npm run build
        ```

8.  **Start the development server:**
    ```bash
    php artisan serve
    ```

9.  **Access the application:**
    -   Open your web browser and navigate to `http://127.0.0.1:8000`.

---

## 🔌 API Endpoints

The application provides the following API endpoints. All note-related endpoints require a valid Bearer Token in the `Authorization` header.

| Method   | Endpoint                  | Description                               | Authentication |
| :------- | :------------------------ | :---------------------------------------- | :------------- |
| `POST`   | `/api/register`           | Register a new user.                      | None           |
| `POST`   | `/api/login`              | Log in a user and receive a JWT.          | None           |
| `POST`   | `/api/logout`             | Log out the current user.                 | Required       |
| `GET`    | `/api/notes`              | Get all notes for the authenticated user. | Required       |
| `POST`   | `/api/notes`              | Create a new note.                        | Required       |
| `GET`    | `/api/notes/{id}`         | Get a single note by its ID.              | Required       |
| `PUT`    | `/api/notes/{id}`         | Update an existing note.                  | Required       |
| `DELETE` | `/api/notes/{id}`         | Delete a note.                            | Required       |

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).