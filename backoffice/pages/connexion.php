<?php
session_start();

// Si l'utilisateur est déjà connecté, le rediriger vers le backoffice
if (isset($_SESSION['user_id'])) {
    header('Location: /articles');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page de connexion au backoffice de Iran War. Connectez-vous pour gérer les articles et le contenu.">
    <meta name="theme-color" content="#2563eb">
    <meta name="color-scheme" content="light dark">
    <title>Connexion - Backoffice Iran War</title>
    <link rel="stylesheet" href="/assets/backoffice.css">
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
            max-width: 400px;
            width: 100%;
            padding: 40px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h1 {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 8px;
            margin: 0;
        }

        .login-header p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(37, 99, 235, 0.3);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-header">
                <h1>Connexion</h1>
                <p>Accédez à votre backoffice</p>
            </div>

            <form action="/traitements/traitement-connexion.php" method="post">
                <div class="form-group">
                    <label for="username">Email</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="admin" 
                        required
                        aria-required="true"
                        aria-describedby="username-help"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        value="admin" 
                        required
                        aria-required="true"
                        aria-describedby="password-help"
                    >
                </div>

                <button type="submit">Se connecter</button>
            </form>

            <div class="login-footer">
                <p>Assurez-vous que votre connexion est sécurisée</p>
            </div>
        </div>
    </div>
</body>
</html>