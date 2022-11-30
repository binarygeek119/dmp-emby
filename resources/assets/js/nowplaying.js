let embySettings = {};
let embyPlaying = false;
let embyDevicePlaying = null;

function setEmbyNowPlaying(playing) {
    axios
        .post('/api/now-playing', playing)
        .then(() => {})
        .catch(() => {});
}

function setembyStoppedPlaying() {
    embyPlaying = false;
    axios
        .post('/api/stopped', { service: 'dmp-emby' })
        .then(() => {})
        .catch(() => {});
}

function embyNowPlaying() {
    if (embyDevicePlaying) {
        let protocol = embySettings.emby_use_ssl ? 'https' : 'http';
        let playing = {
            contentRating: embyDevicePlaying.NowPlayingItem.OfficialRating,
            audienceRating: embyDevicePlaying.NowPlayingItem.CommunityRating,
            duration: embyDevicePlaying.NowPlayingItem.RunTimeTicks / 10000 / 1000 / 60,
            poster:
                protocol +
                '://' +
                embySettings.emby_ip_address +
                ':8096/emby/items/' +
                embyDevicePlaying.NowPlayingItem.Id +
                '/Images/Primary',
        };

        setembyNowPlaying(playing);
    }
}

function startembySocket() {
    // emby - we have to poll. Does not have socket for now playing
    let protocol = embySettings.emby_use_ssl ? 'https' : 'http';
    setInterval(() => {
        axios
            .get(
                protocol +
                    '://' +
                    embySettings.emby_ip_address +
                    ':8096/Sessions?api_key=' +
                    embySettings.emby_token
            )
            .then((response) => {
                let devices = response.data;
                embyDevicePlaying = devices.find((device) => {
                    if (
                        device.hasOwnProperty('NowPlayingItem') &&
                        device.NowPlayingItem.Type === 'Movie'
                    ) {
                        return device;
                    }
                });

                if (embyDevicePlaying) {
                    embyNowPlaying();
                } else {
                    setembyStoppedPlaying();
                }
            })
            .catch(() => {
                embyDevicePlaying = null;
                setembyStoppedPlaying();
            });
    }, 7000);
}

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        axios
            .get('/api/dmp-emby-settings')
            .then((response) => {
                embySettings = response.data;
                startembySocket();
            })
            .catch((response) => {
                console.log(response);
                console.log('COULD NOT GET emby SETTINGS');
            });
    }, 5000);
});
