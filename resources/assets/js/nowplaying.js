let jelllyfinSettings = {};
let jelllyfinPlaying = false;
let jellyfinDevicePlaying = null;

function setJellyfinNowPlaying(playing) {
    axios
        .post('/api/now-playing', playing)
        .then(() => {})
        .catch(() => {});
}

function setJellyfinStoppedPlaying() {
    jelllyfinPlaying = false;
    axios
        .post('/api/stopped', { service: 'dmp-jellyfin' })
        .then(() => {})
        .catch(() => {});
}

function jellyfinNowPlaying() {
    if (jellyfinDevicePlaying) {
        let protocol = jelllyfinSettings.jellyfin_use_ssl ? 'https' : 'http';
        let playing = {
            contentRating: jellyfinDevicePlaying.NowPlayingItem.OfficialRating,
            audienceRating: jellyfinDevicePlaying.NowPlayingItem.CommunityRating,
            duration: jellyfinDevicePlaying.NowPlayingItem.RunTimeTicks / 10000 / 1000 / 60,
            poster:
                protocol +
                '://' +
                jelllyfinSettings.jellyfin_ip_address +
                ':8096/Items/' +
                jellyfinDevicePlaying.NowPlayingItem.Id +
                '/Images/Primary',
        };

        setJellyfinNowPlaying(playing);
    }
}

function startJellyfinSocket() {
    // Jellyfin - we have to poll. Does not have socket for now playing
    let protocol = jelllyfinSettings.jellyfin_use_ssl ? 'https' : 'http';
    setInterval(() => {
        axios
            .get(
                protocol +
                    '://' +
                    jelllyfinSettings.jellyfin_ip_address +
                    ':8096/Sessions?api_key=' +
                    jelllyfinSettings.jellyfin_token
            )
            .then((response) => {
                let devices = response.data;
                jellyfinDevicePlaying = devices.find((device) => {
                    if (
                        device.hasOwnProperty('NowPlayingItem') &&
                        device.NowPlayingItem.Type === 'Movie'
                    ) {
                        return device;
                    }
                });

                if (jellyfinDevicePlaying) {
                    jellyfinNowPlaying();
                } else {
                    setJellyfinStoppedPlaying();
                }
            })
            .catch(() => {
                jellyfinDevicePlaying = null;
                setJellyfinStoppedPlaying();
            });
    }, 7000);
}

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        axios
            .get('/api/dmp-jelllyfin-settings')
            .then((response) => {
                jelllyfinSettings = response.data;
                startJellyfinSocket();
            })
            .catch((response) => {
                console.log(response);
                console.log('COULD NOT GET JELLYFIN SETTINGS');
            });
    }, 5000);
});
