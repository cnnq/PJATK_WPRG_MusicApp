function togglePlay(songId) {
    const audio = document.getElementById('audio-' + songId);
    const icon = document.getElementById('icon-' + songId);

    if (audio.paused) {
        audio.play();
        icon.classList.remove('fa-play');
        icon.classList.add('fa-pause');

        // Increment play count
        fetch('../src/scripts/increment_plays.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'song_id=' + songId
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });

    } else {
        audio.pause();
        icon.classList.remove('fa-pause');
        icon.classList.add('fa-play');
    }
}
