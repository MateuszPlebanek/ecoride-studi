<?php

namespace App\Controller;

class StaticController
{
    public function legal(): void
    {
        $title = 'Mentions légales - EcoRide';

        ob_start();
        require __DIR__ . '/../View/legal.php';
        $content = ob_get_clean();

        require __DIR__ . '/../View/layout.php';
    }

    public function contact(): void
    {
        $title = 'Contact - EcoRide';

        ob_start();
        require __DIR__ . '/../View/contact.php';
        $content = ob_get_clean();

        require __DIR__ . '/../View/layout.php';
    }
}
