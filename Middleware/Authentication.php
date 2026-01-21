<?php

/**
 * Class Authentication
 *
 * Handles admin authentication using PHP sessions.
 * 
 * Expected session data after successful login:
 * ------------------------------------------------
 * $_SESSION['admin_id']        → Admin unique ID
 * $_SESSION['admin_username'] → Admin username
 * $_SESSION['admin_role']     → Role (must be 'admin')
 * $_SESSION['admin_name']     → Admin full name
 *
 * If the admin is not logged in or not authorized,
 * the user will be redirected to the admin login page.
 */
class Authentication
{

    public function __construct()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->isLoggedIn()) {
            $this->redirectToLogin();
        }
    }

    private function isLoggedIn(): bool
    {
        return isset($_SESSION['admin_id']);
    }

    public function isAdmin(): bool
    {
        if (
            isset($_SESSION['admin_role']) &&
            $_SESSION['admin_role'] === 'admin'
        ) {
            return true;
        }

        $this->redirectToLogin();
    }


    private function redirectToLogin(): void
    {
        header('Location: ../admin/login.php');
        exit;
    }
}
