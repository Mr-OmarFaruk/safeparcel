<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    $passwordHash = $user['password'] ?? '';
    $passwordOk = $user && (
        password_verify($password, $passwordHash)
        || hash_equals($passwordHash, $password)
    );

    if ($user && $passwordOk && $user['role'] === $role) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid login details. Please check email, password, and role.';
}

$pageTitle = 'Login - SafeParcel';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section-pad">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5">
        <div class="content-card bg-white p-4 p-md-5">
          <h2 class="fw-bold mb-1">Admin & User Login</h2>
          <p class="text-muted mb-4">Choose your role and sign in to manage SafeParcel activities.</p>
          <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
          <?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label" for="role">Login As</label>
              <select class="form-select" id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="user">User</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label" for="email">Email</label>
              <input class="form-control" type="email" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="password">Password</label>
              <input class="form-control" type="password" id="password" name="password" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Login</button>
          </form>
          <div class="alert alert-info mt-4 mb-0 small">
            Demo admin: admin@safeparcel.test / password<br>
            Demo user: user@safeparcel.test / password<br>
            If demo login still fails, open <a href="reset_demo_users.php">Reset Demo Users</a> once.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
