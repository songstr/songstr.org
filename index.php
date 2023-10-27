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
      $id = end($path);
      $dataURL = $uri ?? (strlen($id) === 36 ? "https://nosdav.net/melvin/songstr/$id.json" : null);
      $songData = null;

      if ($dataURL) {
        $json = file_get_contents($dataURL);
        if ($json) {
          $songData = json_decode($json, true);
        }
      }
    ?>

    <script type="application/ld+json" id="songData">
      <?= json_encode($songData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?>
    </script>



    
    <?php if ($songData): ?>
      <meta property="og:title" content="<?= htmlspecialchars($songData['title'] ?? 'Title') ?>" />
      <meta property="og:type" content="music.song" />
      <meta property="og:url" content="<?= "https://songstr.org/$id" ?>" />
      <meta property="og:image" content="<?= htmlspecialchars($songData['image'] ?? 'default-image-url') ?>" />
      <meta property="og:description" content="Listen to <?= htmlspecialchars($songData['title'] ?? 'Title') ?> by <?= htmlspecialchars($songData['creator']['title'] ?? 'Creator') ?>" />
      <meta property="music:musician" content="<?= htmlspecialchars($songData['creator'] ?? 'Creator') ?>" />

    <?php endif; ?>
  </head>

  <script type="module">
      import { html, render } from './js/standalone.module.js'

      async function fetchSongData() {
        const params = new URLSearchParams(window.location.search)
        const uri = params.get('uri')
        const path = window.location.pathname.split('/').pop()
        const dataURL =
          uri ||
          (path.length === 36
            ? `https://nosdav.net/melvin/songstr/${path}.json`
            : null)
        console.log('dataURL:', dataURL)
        if (dataURL) {
          try {
            const response = await fetch(dataURL)
            if (response.ok) {
              const data = await response.json()
              console.log('Fetched data:', data)
              return data
            }
          } catch (err) {
            console.warn(
              'Failed to fetch from provided URL, falling back to data island.',
              err
            )
          }
        }
        const songDataEl = document.getElementById('songData')
        return JSON.parse(songDataEl.textContent)
      }

      // Style
      const styles = `
            body {
                font-family: 'Arial', sans-serif;
                padding: 50px;
                background-color: #ececec;
            }
        `

      function getYouTubeVideoID(url) {
        if (!url) return null
        const regex = /(?:v=)([a-zA-Z0-9_-]{11})/
        const match = url.match(regex)
        return match ? match[1] : null
      }

      function YouTubeEmbed(props) {
        const videoID = getYouTubeVideoID(props.url)
        if (!videoID) return null

        return html`
          <div class="youtube-embed">
            <iframe
              width="480"
              height="270"
              src="https://www.youtube.com/embed/${videoID}"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            ></iframe>
          </div>
        `
      }

      // Songstr Component
      function Songstr(props) {
        // Get data from structured data island
        // const songDataEl = document.getElementById('songData')
        // const songData = JSON.parse(songDataEl.textContent)
        var songData = props.songData
        songData.url = songData.url || {}
        const videoID = getYouTubeVideoID(songData.url.YouTube)

        console.log(songData)
        console.log('videoID', videoID)

        const params = new URLSearchParams(window.location.search)
        const dataURL = params.get('uri')
        // set to the value in dataURL before .json and after the last trailing /
        songData.url.musicbrainz = dataURL
          ? dataURL
              .replace(/\.json$/, '')
              .split('/')
              .pop()
          : null

        var identifier = songData.identifier ? songData.identifier.replace('https://musicbrainz.org/recording/', '') : null
        songData.url.musicbrainz = identifier || songData.id || songData.url.musicbrainz

        return html`
          <style>
            ${styles}
          </style>
          <div class="container">
            <div class="media-content">
              <img src="${songData.image}" alt=" " />
              <div>
                <h2>${songData.title}</h2>
                <p>${songData.creator}</p>
              </div>
            </div>
            <${YouTubeEmbed} url=${songData.url.YouTube} />

            <div class="listen">
              <h3>Listen on</h3>
              <a
                href="${songData.url.YouTube}"
                class="${songData.url.YouTube ? '' : 'disabled'}"
                ><i class="fab fa-youtube"></i> YouTube</a
              >

              <a
                href="https://listenbrainz.org/player/?recording_mbids=${songData
                  .url.musicbrainz}"
                class="${songData.url.musicbrainz ? '' : 'disabled'}"
                target="_blank"
              >
                <img
                  src="./images/mb.png"
                  alt="musicbrainz"
                  style="width: 16px; height: 16px; filter: invert(100%);"
                />${' '} MusicBrainz
              </a>

              <a href="${songData.url.AppleMusic}" class="disabled"
                ><i class="fab fa-apple"></i> Apple Music</a
              >
            </div>
            <div class="buy">
              <div class="buy">
                <h3>Buy on</h3>
                <a href="${songData.url.Tidal}" class="disabled">
                  <img
                    src="./images/tidal.png"
                    alt="Tidal"
                    style="width: 16px; height: 16px; filter: invert(100%);"
                  />${' '} Tidal
                </a>

                <a href="${songData.url.Amazon}" class="disabled">
                  <i class="fab fa-amazon"></i> Amazon
                </a>
              </div>
            </div>
            <div class="customize">
              <h3>Edit and customize this page</h3>
              <ul>
                <li>
                  <i class="fas fa-ticket-alt"></i>Add links for tickets, merch
                  or anything
                </li>
                <li>
                  <i class="fas fa-link"></i>Customize the URL using domains
                </li>
                <li>
                  <i class="fas fa-sync-alt"></i>Easily update all streaming
                  links with each new release
                </li>
              </ul>

              <button class="disabled">Customize</button>
              <button class="disabled">Create new</button>
            </div>
          </div>
        `
      }

      // Render the Songstr component when the document is ready
      window.addEventListener('DOMContentLoaded', async () => {
        const songData = await fetchSongData()
        console.log(songData)
        render(html`<${Songstr} songData=${songData} />`, document.body)
      })
    </script>


  <body></body>
</html>

