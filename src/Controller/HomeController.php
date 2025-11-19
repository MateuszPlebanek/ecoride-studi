<?php

namespace App\Controller;

class HomeController
{
    public function index(): void
    {
        $title = 'EcoRide - Covoiturage écologique';

        ob_start();
        require __DIR__ . '/../View/home.php';
        $content = ob_get_clean();

        require __DIR__ . '/../View/layout.php';
    }
}
