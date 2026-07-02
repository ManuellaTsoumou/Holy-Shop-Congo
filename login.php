<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['client_id'])) {
    header('Location: /Holy-Shop-Congo/client/dashboard.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (!$email || !$password) {
        $error = 'Email et mot de passe requis.';
    } else {
        try {
            $pdo  = getDB();
            $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $client = $stmt->fetch();

            if (!$client || !password_verify($password, $client['password_hash'] ?? '')) {
                $error = 'Email ou mot de passe incorrect.';
            } elseif ($client['statut_compte'] === 'en attente') {
                $error = 'Votre compte est en cours de validation par notre équipe. Nous vous contacterons sous 24h.';
            } elseif ($client['statut_compte'] === 'suspendu') {
                $error = 'Votre compte est suspendu. Contactez-nous sur WhatsApp : +242 06 448 73 95';
            } else {
                session_regenerate_id(true);
                $_SESSION['client_id']     = $client['id'];
                $_SESSION['client_nom']    = $client['nom'];
                $_SESSION['client_prenom'] = $client['prenom'];
                header('Location: /Holy-Shop-Congo/client/dashboard.php');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Erreur technique. Veuillez réessayer.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Se connecter — Holy Shop Congo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/Holy-Shop-Congo/style.css">
  <link rel="icon" type="image/png" href="/Holy-Shop-Congo/images/Holy-shop_logo.jpg">
  <style>
    .auth-section {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 80px 20px 60px;
      background: var(--violet);
      position: relative;
      overflow: hidden;
    }
    .auth-section::before {
      content: '';
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 80% 50% at 100% 0%, rgba(206,17,38,.10) 0%, transparent 55%),
        radial-gradient(ellipse 60% 60% at 0% 100%, rgba(206,17,38,.06) 0%, transparent 50%);
      pointer-events: none;
    }
    .auth-card {
      background: var(--blanc);
      border-radius: var(--r-lg);
      padding: 48px 44px;
      width: 100%;
      max-width: 460px;
      box-shadow: 0 24px 64px rgba(0,0,0,.25);
      position: relative;
      z-index: 1;
    }
    .auth-logo { text-align: center; margin-bottom: 28px; }
    .auth-logo img { height: 56px; border-radius: 50%; margin: 0 auto 12px; display: block; }
    .auth-title {
      font-family: 'Anton', sans-serif;
      font-weight: 400;
      font-size: 1.8rem;
      color: var(--violet);
      text-align: center;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: .02em;
    }
    .auth-subtitle { text-align: center; font-size: .85rem; color: var(--muted); margin-bottom: 28px; }
    .form-group { margin-bottom: 16px; }
    .form-label {
      display: block;
      font-size: .74rem;
      font-weight: 600;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: var(--txt);
      margin-bottom: 6px;
    }
    .form-input {
      width: 100%;
      padding: 11px 14px;
      background: var(--bg);
      border: 2px solid rgba(28,0,108,.12);
      border-radius: var(--r);
      color: var(--txt);
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: .93rem;
      outline: none;
      transition: border-color var(--t), background var(--t);
    }
    .form-input:focus { border-color: var(--violet); background: var(--blanc); }
    .form-input::placeholder { color: var(--muted); opacity: .7; }
    .error-box {
      background: rgba(206,17,38,.07);
      border: 1px solid rgba(206,17,38,.25);
      border-radius: var(--r);
      padding: 12px 16px;
      margin-bottom: 20px;
      font-size: .84rem;
      color: var(--rouge-d);
      line-height: 1.6;
    }
    .auth-link { text-align: center; margin-top: 20px; font-size: .84rem; color: var(--muted); }
    .auth-link a { color: var(--violet); font-weight: 600; }
    .auth-link a:hover { color: var(--rouge); }
    .divider { display: flex; align-items: center; gap: 14px; margin: 20px 0; }
    .divider::before, .divider::after { content:''; flex:1; height:1px; background: rgba(28,0,108,.12); }
    .divider span { font-size: .72rem; color: var(--muted); }
    .btn-wa {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 11px;
      background: transparent;
      border: 2px solid rgba(37,211,102,.35);
      border-radius: 50px;
      color: #16a34a;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: .87rem;
      font-weight: 600;
      cursor: pointer;
      transition: all var(--t);
      text-decoration: none;
    }
    .btn-wa:hover { background: rgba(37,211,102,.08); border-color: rgba(37,211,102,.6); }
    @media (max-width: 480px) { .auth-card { padding: 32px 20px; } }
  </style>
</head>
<body>

<header class="main-header" id="mainHeader">
  <div class="container header-wrapper">
    <div class="logo">
      <a href="/Holy-Shop-Congo/index.html">
        <img src="/Holy-Shop-Congo/images/Holy-shop_logo.jpg" alt="Holy Shop Logo" class="logo-img">
      </a>
    </div>
    <nav id="nav" class="main-nav">
      <ul>
        <li><a href="/Holy-Shop-Congo/index.html#accueil">Accueil</a></li>
        <li><a href="/Holy-Shop-Congo/index.html#features">Services</a></li>
        <li><a href="/Holy-Shop-Congo/register.php">Créer un compte</a></li>
        <li><a href="/Holy-Shop-Congo/login.php" class="nav-cta">Se connecter</a></li>
      </ul>
    </nav>
  </div>
</header>

<section class="auth-section">
  <div class="auth-card">
    <div class="auth-logo">
      <img src="/Holy-Shop-Congo/images/Holy-shop_logo.jpg" alt="Holy Shop Congo">
    </div>
    <h2 class="auth-title">Bon retour !</h2>
    <p class="auth-subtitle">Connectez-vous pour suivre vos commandes.</p>

    <?php if ($error): ?>
      <div class="error-box">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/Holy-Shop-Congo/login.php">
      <div class="form-group">
        <label class="form-label" for="email">Adresse email</label>
        <input type="email" id="email" name="email" class="form-input"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               required autofocus placeholder="votre@email.com">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Mot de passe</label>
        <input type="password" id="password" name="password" class="form-input"
               required placeholder="••••••••">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px;">
        Se connecter
      </button>
    </form>

    <div class="divider"><span>ou</span></div>

    <a href="https://wa.me/242064487395" class="btn-wa" target="_blank" rel="noopener">
      💬 Commander via WhatsApp
    </a>

    <p class="auth-link">
      Pas encore de compte ? <a href="/Holy-Shop-Congo/register.php">Créer un compte</a>
    </p>
  </div>
</section>

<script>
window.addEventListener('scroll', () => {
  document.getElementById('mainHeader').classList.toggle('scrolled', window.scrollY > 50);
});
</script>
</body>
</html>
