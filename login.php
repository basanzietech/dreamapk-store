<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = "Please fill in the email and passwords.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Email or password is incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ingia - dreamapkstore</title>
  <!-- Tailwind CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md transform transition-all duration-500 hover:scale-105">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Login In Your Account</h2>
    <?php if ($errors): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php foreach ($errors as $error): ?>
          <p class="text-sm"><?php echo $error; ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <form method="post" action="">
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
        <input type="email" name="email" id="email" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-300" required>
      </div>
      <div class="mb-6">
        <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
        <input type="password" name="password" id="password" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-300" required>
      </div>
      <div class="mb-6">
        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-300">Login Now</button>
      </div>
    </form>
    <p class="text-center text-gray-600">You don't have an account? <a href="register.php" class="text-blue-600 hover:text-blue-800 transition duration-300">Register</a></p>
  </div>
</body>
</html>