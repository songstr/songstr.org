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
    <link rel="stylesheet" href="css/main.css" />

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

