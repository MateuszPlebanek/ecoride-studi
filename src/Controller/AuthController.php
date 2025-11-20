<?php
// src/Controller/AuthController.php

namespace App\Controller;

class AuthController
{
    public function login(): void
    {
        $title = 'Connexion - EcoRide';

        ob_start();
        require __DIR__ . '/../View/login.php';
        $content = ob_get_clean();

        require __DIR__ . '/../View/layout.php';
    }
}
