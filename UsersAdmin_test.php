<?php

// Clase de usuario
class User {
    private $username;
    private $email;
    private $password;

    public function __construct($username, $email, $password) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }
}

// Clase de sesión para verificar el usuario actual
class Session {
    private $currentUser;
    private $currentUserRole; // Almacena el rol como número

    public function __construct($currentUser, $currentUserRole) {
        $this->currentUser = $currentUser;
        $this->currentUserRole = $currentUserRole;
    }

    public function getCurrentUser() {
        return $this->currentUser;
    }

    public function getCurrentUserRole() {
        return $this->currentUserRole;
    }
}

// Función para obtener la lista de usuarios e imprimirla
function printUsers($session) {
    // Verificar si el usuario tiene el rol adecuado
    if ($session->getCurrentUserRole() == 1) { // 1 es igual a admin
        // Aquí se debería agregar la lógica para obtener los usuarios de una base de datos
        $users = [
            new User("user1", "user1@example.com", "Pass1"),
            new User("user2", "user2@example.com", "Pass2")
        ];

        foreach ($users as $user) {
            echo "Username: " . $user->getUsername() . ", Email: " . $user->getEmail() . "\n";
        }
    } else {
        echo "Acceso denegado: No tienes permiso para ver esta información.\n";
    }
}

// Función principal para ejecutar el controlador
function main() {
    $session = new Session("adminUser", 1); // 1 para administrador
    printUsers($session);
}

// Ejecutar el método principal
main();
?>