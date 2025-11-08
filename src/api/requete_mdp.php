<?php
use bdd\Bdd;

require_once __DIR__ . '/../bdd/Bdd.php';

// ✅ Vérifier que l'email a bien été envoyé
if (!isset($_POST['email']) || empty($_POST['email'])) {
    echo "Erreur : adresse email manquante.";
    exit;
}

$email = trim($_POST['email']);

// ✅ Connexion BDD
$bdd = new Bdd();
$pdo = $bdd->getBdd();

// ✅ Vérifier si l'utilisateur existe
$sql = "SELECT id_user FROM user WHERE email = :email LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if (!$user) {
    echo '
        <p>Aucun utilisateur trouvé avec cette adresse email. Vous allez être redirigé vers la page précédente dans 5 secondes...</p>
        <script>
            setTimeout(function() {
                window.history.back(); // Retour à la page précédente
            }, 5000); // 5000 ms = 5 secondes
        </script>
    ';
    exit();
}

$userId = $user['id_user'];

// ✅ Génération d'un token sécurisé
$token = bin2hex(random_bytes(32));   // 64 caractères hex
$token_hash = hash('sha256', $token); // hash à stocker

// ✅ Expiration dans 1 heure
$expire_at = date("Y-m-d H:i:s", time() + 3600);

// ✅ (Option) supprimer d’anciens tokens
$pdo->prepare("DELETE FROM password_reset_tokens WHERE user_id = :uid")
    ->execute(['uid' => $userId]);

// ✅ Stocker le nouveau token
$sql = "INSERT INTO password_reset_tokens (user_id, token_hash, expires_at)
        VALUES (:uid, :token_hash, :expire_at)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'uid'        => $userId,
    'token_hash' => $token_hash,
    'expire_at'  => $expire_at
]);

// ✅ Lien de réinitialisation
$reset_link = "http://localhost/tp-lprs/public/reset-password.php?token=" . $token;

// ✅ Envoi via FormSubmit (simple, pour TP)
echo "
<form id='sendMail' action='https://formsubmit.co/contact2jrs@gmail.com' method='POST'>
    <input type='hidden' name='subject' value='Réinitialisation du mot de passe'>
    <input type='hidden' name='message'
           value='Voici votre lien pour réinitialiser votre mot de passe : $reset_link'>
</form>

<script>
    document.getElementById('sendMail').submit();
</script>

<p>Email envoyé ! Vérifiez votre boîte mail.</p>
";
