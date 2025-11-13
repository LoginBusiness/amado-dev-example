<?php
// Simple guestbook that stores entries in MariaDB using PDO

$host = getenv('DB_HOST') ?: 'db';
$db   = getenv('DB_NAME') ?: 'guestbook';
$user = getenv('DB_USER') ?: 'guest';
$pass = getenv('DB_PASS') ?: 'guestpass';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    echo "<h2>Database connection failed</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    exit;
}

// Create table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name !== '' && $message !== '') {
        $stmt = $pdo->prepare('INSERT INTO entries (name, message) VALUES (:name, :message)');
        $stmt->execute([':name' => $name, ':message' => $message]);
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }
}

$rows = $pdo->query('SELECT id, name, message, created_at FROM entries ORDER BY id DESC')->fetchAll();

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Amado Guestbook</title>
    <style>
      body { font-family: Arial, sans-serif; max-width: 800px; margin: 2rem auto; }
      form { margin-bottom: 1.5rem; }
      textarea { width: 100%; height: 80px; }
      .entry { border-bottom: 1px solid #ddd; padding: 0.5rem 0; }
      .meta { color: #666; font-size: 0.9rem; }
    </style>
  </head>
  <body>
    <h1>Guestbook</h1>

    <form method="post">
      <label>Name<br>
        <input type="text" name="name" required>
      </label>
      <br><br>
      <label>Message<br>
        <textarea name="message" required></textarea>
      </label>
      <br><br>
      <button type="submit">Sign guestbook</button>
    </form>

    <h2>Messages</h2>
    <?php if (count($rows) === 0): ?>
      <p>No messages yet.</p>
    <?php else: ?>
      <?php foreach ($rows as $r): ?>
        <div class="entry">
          <div class="meta"><?php echo htmlspecialchars($r['name']); ?> — <?php echo $r['created_at']; ?></div>
          <div class="msg"><?php echo nl2br(htmlspecialchars($r['message'])); ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </body>
</html>
