<?php
$title = $thumbnail = $filesize = $downloadUrl = $watchUrl = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["url"])) {
    $videoUrl = trim($_POST["url"]);
    if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
        $error = "Please enter a valid Terabox URL.";
    } else {
        $apiEndpoint = "https://tera-api-thory.vercel.app/api?api_key=lifetime&url=" . urlencode($videoUrl);

        // Try cURL first
        $ch = curl_init($apiEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $curlErr = curl_errno($ch);
        curl_close($ch);

        // Fallback to file_get_contents
        if ($curlErr && empty($response)) {
            $response = @file_get_contents($apiEndpoint);
        }

        if (!$response) {
            $error = "Could not contact the API. Please try again later.";
        } else {
            $data = json_decode($response, true);
            if (json_last_error() || empty($data)) {
                $error = "Invalid response from API.";
            } else {
                // Common fields
                $title       = $data['title']      ?? $data['fileName']            ?? "Unknown Title";
                $thumbnail   = $data['thumbnail']  ?? "";
                $filesize    = $data['fileSize']   ?? $data['filesize']            ?? "";
                $downloadUrl = $data['downloadUrl']?? $data['downloadLink']         ?? "";
                $watchUrl    = $data['videos'][0]['url'] ?? $downloadUrl;

                // Ensure at least download URL exists
                if (!$downloadUrl) {
                    $error = "Download link not found in API response.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Terabox Downloader</title>
  <style>
    /* ========== Reset & Base ========== */
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      background: linear-gradient(135deg, #1f1c2c, #928dab);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #ececec;
    }
    a { text-decoration:none; }

    /* ========== Container ========== */
    .card {
      background: rgba(255,255,255,0.05);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.5);
      backdrop-filter: blur(8px);
      width: 100%;
      max-width: 500px;
      padding: 2rem;
      animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
      from { opacity:0; transform: translateY(20px); }
      to   { opacity:1; transform: translateY(0); }
    }

    h1 {
      text-align:center;
      margin-bottom:1.5rem;
      font-size:1.8rem;
      letter-spacing:1px;
    }

    /* ========== Form ========== */
    .form-group {
      display:flex;
      gap:0.5rem;
      flex-direction:column;
      margin-bottom:1rem;
    }
    .form-group input {
      padding: 0.75rem 1rem;
      border: none;
      border-radius:8px;
      font-size:1rem;
      outline:none;
    }
    .form-group input[type="url"] {
      background: rgba(255,255,255,0.1);
      color: #ececec;
    }
    .form-group button {
      padding: 0.75rem;
      background: #ff6a00;
      color: #fff;
      border:none;
      border-radius:8px;
      font-size:1rem;
      cursor:pointer;
      transition: background 0.3s ease;
    }
    .form-group button:hover {
      background: #e65c00;
    }

    /* ========== Error ========== */
    .error {
      text-align:center;
      color: #ff4e4e;
      margin-bottom:1rem;
    }

    /* ========== Result ========== */
    .result {
      margin-top:1.5rem;
      text-align:center;
    }
    .result img {
      width:100%;
      border-radius:8px;
      margin-bottom:1rem;
    }
    .result h2 {
      font-size:1.2rem;
      margin-bottom:0.5rem;
      color: #fff;
    }
    .result p {
      font-size:0.95rem;
      margin-bottom:1rem;
      color: #ccc;
    }
    .btn-group {
      display:flex;
      justify-content:center;
      gap:1rem;
    }
    .btn-group a {
      flex:1;
      padding:0.75rem 1rem;
      border-radius:8px;
      font-weight:600;
      transition: transform 0.2s;
    }
    .btn-watch {
      background: #2196f3;
      color: #fff;
    }
    .btn-download {
      background: #4caf50;
      color: #fff;
    }
    .btn-group a:hover {
      transform: translateY(-2px);
    }

    /* ========== Footer ========== */
    footer {
      margin-top:2rem;
      text-align:center;
      font-weight:700;
      color: #ececec;
    }
  </style>
</head>
<body>
  <div class="card">
    <h1>Terabox Video Tool</h1>

    <form method="post">
      <div class="form-group">
        <input type="url" name="url" placeholder="Paste your Terabox URL here" required>
      </div>
      <div class="form-group">
        <button type="submit">Fetch Details</button>
      </div>
    </form>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($downloadUrl)): ?>
      <div class="result">
        <?php if ($thumbnail): ?>
          <img src="<?= htmlspecialchars($thumbnail) ?>" alt="Thumbnail">
        <?php endif; ?>
        <h2><?= htmlspecialchars($title) ?></h2>
        <?php if ($filesize): ?>
          <p>Size: <?= htmlspecialchars($filesize) ?></p>
        <?php endif; ?>
        <div class="btn-group">
          <a href="<?= htmlspecialchars($watchUrl) ?>" target="_blank" class="btn-watch">► Watch Now</a>
          <a href="<?= htmlspecialchars($downloadUrl) ?>" class="btn-download">⬇ Download Now</a>
        </div>
      </div>
    <?php endif; ?>

    <footer>Developer RydenXGod</footer>
  </div>
</body>
</html>
