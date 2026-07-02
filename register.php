<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['client_id'])) {
    header('Location: /Holy-Shop-Congo/client/dashboard.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom   = trim($_POST['prenom']   ?? '');
    $nom      = trim($_POST['nom']      ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $confirm  = $_POST['confirm']       ?? '';
    $tel      = trim($_POST['telephone'] ?? '');
    $wa       = trim($_POST['whatsapp'] ?? '');
    $ville    = $_POST['ville']         ?? 'Brazzaville';
    $adresse  = trim($_POST['adresse']  ?? '');

    if (!$prenom)                          $errors[] = 'Le prénom est requis.';
    if (!$nom)                             $errors[] = 'Le nom est requis.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
    if (strlen($password) < 8)             $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    if ($password !== $confirm)            $errors[] = 'Les mots de passe ne correspondent pas.';

    if (empty($errors)) {
        try {
            $pdo  = getDB();
            $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Cet email est déjà utilisé. <a href="/Holy-Shop-Congo/login.php" style="color:var(--violet);font-weight:600">Se connecter</a>';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins  = $pdo->prepare(
                    "INSERT INTO clients (prenom, nom, email, password_hash, telephone, whatsapp, ville, adresse, statut_compte)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'en attente')"
                );
                $ins->execute([$prenom, $nom, $email, $hash, $tel, $wa, $ville, $adresse]);
                $success = true;
            }
        } catch (PDOException $e) {
            $errors[] = 'Erreur technique. Veuillez réessayer.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Créer un compte — Holy Shop Congo</title>
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
      padding: 44px 40px;
      width: 100%;
      max-width: 600px;
      box-shadow: 0 24px 64px rgba(0,0,0,.25);
      position: relative;
      z-index: 1;
    }
    .auth-title {
      font-family: 'Anton', sans-serif;
      font-weight: 400;
      font-size: 1.9rem;
      color: var(--violet);
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: .02em;
    }
    .auth-subtitle { font-size: .87rem; color: var(--muted); margin-bottom: 28px; }
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
    .form-select {
      width: 100%;
      padding: 11px 14px;
      background: var(--bg);
      border: 2px solid rgba(28,0,108,.12);
      border-radius: var(--r);
      color: var(--txt);
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: .93rem;
      outline: none;
      transition: border-color var(--t);
      appearance: none;
    }
    .form-select:focus { border-color: var(--violet); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .error-box {
      background: rgba(206,17,38,.07);
      border: 1px solid rgba(206,17,38,.25);
      border-radius: var(--r);
      padding: 12px 16px;
      margin-bottom: 20px;
    }
    .error-box p { font-size: .84rem; color: var(--rouge-d); line-height: 1.7; }
    .success-box {
      background: rgba(22,163,74,.07);
      border: 1px solid rgba(22,163,74,.25);
      border-radius: var(--r-lg);
      padding: 40px 28px;
      text-align: center;
    }
    .success-box h3 {
      font-family: 'Anton', sans-serif;
      font-weight: 400;
      font-size: 1.5rem;
      color: #16a34a;
      margin-bottom: 14px;
      text-transform: uppercase;
    }
    .success-box p { font-size: .9rem; color: var(--muted); line-height: 1.7; }
    .auth-link { text-align: center; margin-top: 20px; font-size: .84rem; color: var(--muted); }
    .auth-link a { color: var(--violet); font-weight: 600; }
    .auth-link a:hover { color: var(--rouge); }
    @media (max-width: 560px) { .form-row { grid-template-columns: 1fr; } .auth-card { padding: 32px 20px; } }
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
        <li><a href="/Holy-Shop-Congo/index.html#calculator">Calculateur</a></li>
        <li><a href="/Holy-Shop-Congo/login.php" class="nav-cta">Se connecter</a></li>
      </ul>
    </nav>
  </div>
</header>

<section class="auth-section">
  <div class="auth-card">

    <?php if ($success): ?>
      <div class="success-box">
        <div style="font-size:3rem;margin-bottom:16px">✅</div>
        <h3>Inscription réussie !</h3>
        <p>
          Votre compte est en cours de validation par notre équipe.<br>
          Vous recevrez une confirmation sous 24h.<br><br>
          En attendant, contactez-nous sur WhatsApp :
          <a href="https://wa.me/242064487395" style="color:var(--rouge)">+242 06 448 73 95</a>
        </p>
        <a href="/Holy-Shop-Congo/index.html" class="btn btn-primary" style="margin-top:24px;display:inline-flex">
          Retour à l'accueil
        </a>
      </div>

    <?php else: ?>

      <h2 class="auth-title">Créer votre compte</h2>
      <p class="auth-subtitle">Accédez à vos commandes et suivez vos expéditions en temps réel.</p>

      <?php if (!empty($errors)): ?>
        <div class="error-box">
          <?php foreach ($errors as $e): ?>
            <p>⚠ <?= $e ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="/Holy-Shop-Congo/register.php">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="prenom">Prénom *</label>
            <input type="text" id="prenom" name="prenom" class="form-input"
                   value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" class="form-input"
                   value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="email">Adresse email *</label>
          <input type="email" id="email" name="email" class="form-input"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required placeholder="votre@email.com">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="password">Mot de passe *</label>
            <input type="password" id="password" name="password" class="form-input"
                   required placeholder="8 caractères minimum">
          </div>
          <div class="form-group">
            <label class="form-label" for="confirm">Confirmer le MDP *</label>
            <input type="password" id="confirm" name="confirm" class="form-input"
                   required placeholder="Répétez le mot de passe">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" class="form-input"
                   value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" placeholder="+242 06…">
          </div>
          <div class="form-group">
            <label class="form-label" for="whatsapp">WhatsApp</label>
            <input type="tel" id="whatsapp" name="whatsapp" class="form-input"
                   value="<?= htmlspecialchars($_POST['whatsapp'] ?? '') ?>" placeholder="+242 06…">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="ville">Ville de livraison</label>
          <select id="ville" name="ville" class="form-select">
            <option value="Brazzaville" <?= ($_POST['ville']??'Brazzaville')==='Brazzaville'?'selected':'' ?>>Brazzaville</option>
            <option value="Pointe-Noire" <?= ($_POST['ville']??'')==='Pointe-Noire'?'selected':'' ?>>Pointe-Noire</option>
            <option value="Autre Congo" <?= ($_POST['ville']??'')==='Autre Congo'?'selected':'' ?>>Autre ville du Congo</option>
            <option value="Autre pays" <?= ($_POST['ville']??'')==='Autre pays'?'selected':'' ?>>Autre pays</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="adresse">Adresse de livraison</label>
          <textarea id="adresse" name="adresse" class="form-input" rows="2"
                    placeholder="Quartier, rue, numéro…"><?= htmlspecialchars($_POST['adresse'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px;">
          Créer mon compte
        </button>
      </form>

      <p class="auth-link">
        Déjà un compte ? <a href="/Holy-Shop-Congo/login.php">Se connecter</a>
      </p>

    <?php endif; ?>
  </div>
</section>

<script>
window.addEventListener('scroll', () => {
  document.getElementById('mainHeader').classList.toggle('scrolled', window.scrollY > 50);
});
</script>
</body>
</html>
