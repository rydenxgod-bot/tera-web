<?php
$videoURL = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["link"])) {
    $userLink = trim($_POST["link"]);
    if (filter_var($userLink, FILTER_VALIDATE_URL)) {
        $videoURL = "https://tera-api-thory.vercel.app/api?api_key=lifetime&url=" . urlencode($userLink);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>🔥 Terabox Video Player</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #1f1c2c, #928dab);
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 2rem 1rem;
      min-height: 100vh;
    }

    .container {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 16px;
      padding: 2rem;
      max-width: 720px;
      width: 100%;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(8px);
      text-align: center;
      animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h1 {
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }

    p.subtitle {
      font-size: 1.1rem;
      margin-bottom: 1.5rem;
      color: #ccc;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-bottom: 2rem;
    }

    input[type="url"] {
      padding: 0.75rem 1rem;
      border-radius: 8px;
      border: none;
      background: rgba(255,255,255,0.1);
      color: #fff;
      font-size: 1rem;
    }

    button {
      padding: 0.75rem;
      background: #ff6a00;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #e65c00;
    }

    video {
      width: 100%;
      max-width: 640px;
      border-radius: 12px;
      margin-top: 1rem;
      box-shadow: 0 6px 20px rgba(0,0,0,0.4);
    }

    footer {
      margin-top: 2rem;
      text-align: center;
      font-size: 0.9rem;
      color: #ccc;
    }

    .tagline {
      margin-top: 0.5rem;
      font-size: 1rem;
      color: #90caf9;
      font-weight: 500;
    }

    @media(max-width: 600px) {
      h1 { font-size: 1.6rem; }
      input[type="url"] { font-size: 0.9rem; }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>🎬 Welcome to Terabox Player</h1>
    <p class="subtitle">Free. Fast. Secure Streaming. Just paste your Terabox link 👇</p>
    <p class="tagline">Made with ❤️ by RydenXGod</p>

    <form method="POST">
      <input type="url" name="link" placeholder="Paste your Terabox URL here" required>
      <button type="submit">▶ Start Streaming</button>
    </form>

    <?php if ($videoURL): ?>
      <h2>Now Playing:</h2>
      <video controls autoplay>
        <source src="<?= htmlspecialchars($videoURL) ?>" type="video/mp4">
        Your browser does not support video streaming.
      </video>
    <?php endif; ?>

    <footer>© <?= date("Y") ?> Terabox Player — All rights reserved.</footer>
  </div>
</body>
</html>
