<?php

namespace App\Controller;

class CarpoolController
{
    public function index(): void
    {
        $title = 'Covoiturages - EcoRide';

        ob_start();
        require __DIR__ . '/../View/carpools.php';
        $content = ob_get_clean();

        require __DIR__ . '/../View/layout.php';
    }
}
