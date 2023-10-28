<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Songstr</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <style>
      .disabled {
        pointer-events: none; /* Makes the element non-clickable */
        opacity: 0.5; /* Makes the element semi-transparent */
      }
      body {
        font-family: 'Arial', sans-serif;
        padding: 50px;
        background-color: #ececec;
      }
      .container {
        max-width: 500px;
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
      }
      .media-content {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #f1f1f1;
        padding-bottom: 20px;
      }
      .media-content img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        margin-right: 20px;
      }
      .listen,
      .buy,
      .customize {
        margin-bottom: 30px;
      }
      .listen a,
      .buy a {
        display: inline-block;
        background-color: #333;
        padding: 10px 20px;
        margin-right: 10px;
        border-radius: 5px;
        text-decoration: none;
        color: #fff;
        transition: background-color 0.3s ease;
      }
      .listen a:hover,
      .buy a:hover {
        background-color: #555;
      }
      .customize ul {
        list-style-type: none;
        padding: 0;
      }
      .customize ul li {
        padding: 5px 0;
        display: flex;
        align-items: center;
      }
      .customize ul li i {
        margin-right: 10px;
        color: #ff5252;
      }
      button {
        background-color: #ff5252;
        border: none;
        padding: 10px 20px;
        color: #fff;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-right: 10px;
      }
      button:hover {
        background-color: #ff7676;
      }
      .youtube-embed {
        position: relative;
        padding-bottom: 56.25%; /* Aspect ratio */
        padding-top: 30px;
        height: 0;
        overflow: hidden;
      }

      .youtube-embed iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
      }
    </style>

    <?php
      $uri = $_GET['uri'] ?? null;
      $path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
      error_log(print_r($path, true));
      $id = end($path);
      $dataURL = $uri ?? (strlen($id) === 36 ? "https://nosdav.net/melvin/songstr/$id.json" : null);
      $songData = null;
      error_log($dataURL);

      if ($dataURL) {
        $json = file_get_contents($dataURL);
        error_log($json);
        if ($json) {
          $songData = json_decode($json, true);
        }
      }
?>

<?php

if ($songData):
    $songTitle = $songData['title'] ?? null;
    $songDescription = $songData['creator'] ?? null;
    $songThumbnail = $songData['image'] ?? null;
    $youtubeURL = $songData['url']['YouTube'] ?? null;
    ?>

    <!-- Open Graph Basic Info -->

    <!-- Open Graph Video Info -->
    <?php if ($youtubeURL): ?>
        <meta property="og:video:url" content="<?php echo $youtubeURL; ?>">
        <meta property="og:video:type" content="text/html">
        <meta property="og:video:width" content="1280"> <!-- Adjust width based on your video's dimensions -->
        <meta property="og:video:height" content="720"> <!-- Adjust height based on your video's dimensions -->
    <?php endif; ?>

<?php endif; ?>


    <script type="application/ld+json" id="songData">
      <?= json_encode($songData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?>
    </script>



    
    <?php if ($songData): ?>
      <meta property="og:title" content="<?= htmlspecialchars($songData['title'] ?? 'Title') ?>" />
      <meta property="og:type" content="music.song" />
      <meta property="og:url" content="<?= "https://songstr.org/$id" ?>" />
      <meta property="og:image" content="<?= htmlspecialchars($songData['image'] ?? 'default-image-url') ?>" />
      <meta property="og:description" content="Listen to <?= htmlspecialchars($songData['title'] ?? 'Title') ?> by <?= htmlspecialchars($songData['creator'] ?? 'Creator') ?>" />
      <meta property="music:musician" content="<?= htmlspecialchars($songData['creator'] ?? 'Creator') ?>" />

    <?php endif; ?>
  </head>

  <script type="module" src="./js/app.js">
  </script>


  <body></body>
</html>

