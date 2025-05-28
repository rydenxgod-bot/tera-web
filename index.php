<?php
$title = "";
$thumbnail = "";
$filesize = "";
$downloadUrl = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["url"])) {
    $videoUrl = trim($_POST["url"]);
    if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
        $error = "Please enter a valid URL.";
    } else {
        $apiUrl = "https://tera-api-thory.vercel.app/api?api_key=lifetime&url=" . urlencode($videoUrl);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = "Error fetching data from API.";
        }
        curl_close($ch);
        if (empty($error) && $response) {
            $data = json_decode($response, true);
            if ($data) {
                $title = isset($data['title']) ? $data['title'] : (isset($data['fileName']) ? $data['fileName'] : "");
                $thumbnail = isset($data['thumbnail']) ? $data['thumbnail'] : "";
                $filesize = isset($data['fileSize']) ? $data['fileSize'] : (isset($data['filesize']) ? $data['filesize'] : "");
                $downloadUrl = isset($data['downloadUrl']) ? $data['downloadUrl'] : (isset($data['downloadLink']) ? $data['downloadLink'] : "");
                if(empty($downloadUrl) && isset($data['videos'][0]['url'])) {
                    $downloadUrl = $data['videos'][0]['url'];
                }
                if (!$title && isset($data['videos'][0]['title'])) {
                    $title = $data['videos'][0]['title'];
                }
            } else {
                $error = "Invalid response from API.";
            }
        } else {
            $error = "Could not retrieve video information.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>TeraBox Video Downloader</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #333;
    }
    .container {
        background: #fff;
        width: 90%;
        max-width: 600px;
        margin: 40px auto;
        padding: 30px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        border-radius: 8px;
    }
    h1 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 24px;
        color: #333;
    }
    form {
        display: flex;
        flex-direction: column;
    }
    input[type="text"] {
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 16px;
        width: 100%;
    }
    input[type="submit"] {
        padding: 12px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s ease;
        width: 100%;
    }
    input[type="submit"]:hover {
        background: #5a67d8;
    }
    .error {
        color: #e74c3c;
        text-align: center;
        margin-bottom: 15px;
    }
    .result {
        text-align: center;
        margin-top: 20px;
    }
    .result img {
        max-width: 100%;
        border-radius: 6px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .result h2 {
        font-size: 20px;
        margin: 15px 0 10px;
        color: #444;
    }
    .result p {
        font-size: 16px;
        margin: 5px 0;
        color: #555;
    }
    .download-btn {
        display: inline-block;
        margin-top: 15px;
        padding: 12px 25px;
        background: #48bb78;
        color: #fff;
        text-decoration: none;
        font-size: 18px;
        border-radius: 6px;
        transition: background 0.3s ease;
    }
    .download-btn:hover {
        background: #38a169;
    }
    footer {
        text-align: center;
        margin-top: 30px;
        padding: 15px 0;
        font-family: sans-serif;
        font-weight: bold;
        color: #fff;
    }
    @media (max-width: 480px) {
        .container {
            margin: 20px;
            padding: 20px 15px;
        }
        h1 { font-size: 20px; }
        input[type="text"], input[type="submit"], .download-btn {
            font-size: 15px;
        }
        .download-btn { padding: 10px 20px; }
    }
</style>
</head>
<body>
<div class="container">
    <h1>Terabox Video Downloader</h1>
    <form method="post">
        <input type="text" name="url" placeholder="Enter Terabox video URL" required>
        <input type="submit" value="Fetch Video">
    </form>
    <?php if (!empty($error)): ?>
        <div class="error"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>
    <?php if ($downloadUrl): ?>
        <div class="result">
            <?php if ($thumbnail): ?>
                <img src="<?=htmlspecialchars($thumbnail)?>" alt="Video Thumbnail">
            <?php endif; ?>
            <?php if ($title): ?>
                <h2><?=htmlspecialchars($title)?></h2>
            <?php endif; ?>
            <?php if ($filesize): ?>
                <p>Size: <?=htmlspecialchars($filesize)?></p>
            <?php endif; ?>
            <a href="<?=htmlspecialchars($downloadUrl)?>" class="download-btn">Download</a>
        </div>
    <?php endif; ?>
</div>
<footer>Developer RydenXGod</footer>
</body>
</html>
