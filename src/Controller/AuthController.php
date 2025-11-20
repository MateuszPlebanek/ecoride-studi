<?php

namespace App\Controller;

use App\Model\UserRepository;
use App\Security\Csrf;

class AuthController
{
    
    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $title = 'Connexion - EcoRide';
        $error = '';

        $maxAttempts   = 5;  
        $blockDuration = 120;  

        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
        if (!isset($_SESSION['login_block_until'])) {
            $_SESSION['login_block_until'] = 0;
        }

        if (time() < $_SESSION['login_block_until']) {
            $remaining = $_SESSION['login_block_until'] - time();
            $minutes   = floor($remaining / 60);
            $seconds   = $remaining % 60;

            $error = "Trop de tentatives de connexion. Réessayez dans {$minutes} min {$seconds} s.";

            $csrfToken = Csrf::getToken('login_form');

            ob_start();
            require __DIR__ . '/../View/login.php';
            $content = ob_get_clean();

            require __DIR__ . '/../View/layout.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!Csrf::checkToken('login_form', $token)) {
                $error = "Le formulaire a expiré. Merci de réessayer.";
            } else {
                require __DIR__ . '/../../config/db.php';
                $repository = new UserRepository($pdo);

                $email    = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';

                if ($email === '' || $password === '') {
                    $error = "Veuillez remplir tous les champs.";
                } else {
                    $user = $repository->findByEmail($email);

                    if (!$user || !password_verify($password, $user['password_hash'])) {
                        $_SESSION['login_attempts']++;

                        if ($_SESSION['login_attempts'] >= $maxAttempts) {
                            $_SESSION['login_block_until'] = time() + $blockDuration;
                            $error = "Trop de tentatives de connexion. Réessayez plus tard.";
                        } else {
                            $error = "Email ou mot de passe incorrect.";
                        }

                        sleep(1);

                    } else {
                        $_SESSION['login_attempts']    = 0;
                        $_SESSION['login_block_until'] = 0;

                        session_regenerate_id(true);
                        $_SESSION['user_id']      = (int) $user['id'];
                        $_SESSION['user_pseudo']  = $user['pseudo'];
                        $_SESSION['user_credits'] = (float) $user['credits'];

                        header('Location: index.php?page=account');
                        exit;
                    }
                }
            }
        }

        $csrfToken = Csrf::getToken('login_form');

        ob_start();
        require __DIR__ . '/../View/login.php';
        $content = ob_get_clean();

        require __DIR__ . '/../View/layout.php';
    }

    public function register(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $title = 'Créer un compte - EcoRide';

        require __DIR__ . '/../../config/db.php';
        $repository = new UserRepository($pdo);

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!Csrf::checkToken('register_form', $token)) {
                $error = "Le formulaire a expiré. Merci de réessayer.";
            } else {
                $pseudo   = trim($_POST['pseudo'] ?? '');
                $email    = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';

                if ($pseudo === '' || $email === '' || $password === '') {
                    $error = "Tous les champs sont obligatoires.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "L'adresse email est invalide.";
                } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
                    $error = "Le mot de passe doit contenir au moins 8 caractères, avec une majuscule, une minuscule et un chiffre.";
                } elseif ($repository->emailExists($email)) {
                    $error = "Un compte existe déjà avec cette adresse email.";
                } else {
                    $success = $repository->createUser($pseudo, $email, $password);

                    if ($success) {
                        header('Location: index.php?page=login&registered=1');
                        exit;
                    } else {
                        $error = "Une erreur est survenue lors de la création du compte.";
                    }
                }
            }
        }

        
        $csrfToken = Csrf::getToken('register_form');

        ob_start();
        require __DIR__ . '/../View/register.php';
        $content = ob_get_clean();

        require __DIR__ . '/../View/layout.php';
    }

    public function account(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $title = 'Mon compte - EcoRide';

        require __DIR__ . '/../../config/db.php';
        $repository = new UserRepository($pdo);

        $userId = (int) $_SESSION['user_id'];
        $user   = $repository->findById($userId);

        if (!$user) {
            http_response_code(404);
            echo 'Utilisateur introuvable.';
            return;
        }

        ob_start();
        require __DIR__ . '/../View/account.php';
        $content = ob_get_clean();

        require __DIR__ . '/../View/layout.php';
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header('Location: index.php?page=home');
        exit;
    }
}
