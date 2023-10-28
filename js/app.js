import { html, render } from '../js/standalone.module.js'

async function fetchSongData() {
  const params = new URLSearchParams(window.location.search)
  const uri = params.get('uri')
  const path = window.location.pathname.split('/').pop()
  let dataURL =
    uri ||
    (path.length === 36
      ? `https://nosdav.net/melvin/songstr/${path}.json`
      : null)
  console.log('dataURL:', dataURL)
  dataURL = dataURL || 'https://nosdav.net/melvin/songstr/83cf16bc-01db-4766-9e3e-f3c796639cf2.json'

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
  const ret = JSON.parse(songDataEl.textContent)
  return ret
}

// Style
const styles = `
      body {
        font - family: 'Arial', sans-serif;
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
  const songData = props.songData
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

  const identifier = songData.identifier ? songData.identifier.replace('https://musicbrainz.org/recording/', '') : null
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

        <br />
        <h3>Listen on</h3>

        <div class="listen">
          <a
          href="${songData.url.YouTube}"
          class="button youtube ${songData.url.YouTube ? '' : 'disabled'}"
          target="_blank"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#fff">
            <path
              d="M23.499 6.203a3.003 3.003 0 0 0-2.115-2.115C20.355 4 12 4 12 4s-8.355 0-9.384.088a3.003 3.003 0 0 0-2.115 2.115C.001 7.232 0 8.866 0 12s.001 4.768.501 5.797a3.003 3.003 0 0 0 2.115 2.115C3.645 20 12 20 12 20s8.355 0 9.384-.088a3.003 3.003 0 0 0 2.115-2.115C24.001 16.768 24 15.134 24 12s-.001-4.768-.501-5.797zM9.523 15.547V8.453l6.062 3.547-6.062 3.547z"
            ></path>
          </svg>
          YouTube
        </a>
  

          <a
          href="https://listenbrainz.org/player/?recording_mbids=${songData
      .url.musicbrainz}"
            class="${songData.url.musicbrainz ? '' : 'disabled'} button musicbrainz"
            target="_blank"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" fill="#fff">
            <g transform="translate(1.5)">
              <path d="m13 1-12 7v14l12 7z" fill="#ba478f" />
              <path d="m14 1 12 7v14l-12 7z" fill="#eb743b" />
            </g>
          </svg>
          MusicBrainz
        </a>


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
