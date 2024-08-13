<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Call</title>
    <script src="https://media.twiliocdn.com/sdk/js/video/releases/2.20.0/twilio-video.min.js"></script>
    <style>
        #local-video, #remote-video {
            width: 48%;
            display: inline-block;
        }
        video {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
<h1>Video Call</h1>
<div>
    <div id="local-video"></div>
    <div id="remote-video"></div>
</div>
<button id="start-call">Start Call</button>

<script>
    const startCallButton = document.getElementById('start-call');
    const localVideo = document.getElementById('local-video');
    const remoteVideo = document.getElementById('remote-video');

    startCallButton.onclick = async () => {
        try {
            // 1. Get token from your Laravel backend
            const response = await fetch('/api/token');
            const data = await response.json();
            const token = data.token;

            // 2. Connect to Twilio Video
            const room = await Twilio.Video.connect(token, { video: true, audio: true });

            // 3. Display local video
            const localTrack = await Twilio.Video.createLocalVideoTrack();
            localVideo.appendChild(localTrack.attach());

            // 4. Handle remote participants
            const handleParticipant = (participant) => {
                participant.tracks.forEach(publication => {
                    if (publication.isSubscribed) {
                        const track = publication.track;
                        remoteVideo.appendChild(track.attach());
                    }
                });

                participant.on('trackSubscribed', track => {
                    remoteVideo.appendChild(track.attach());
                });

                participant.on('trackUnsubscribed', track => {
                    track.detach().forEach(element => element.remove());
                });
            };

            room.participants.forEach(handleParticipant);
            room.on('participantConnected', handleParticipant);

            // Handle participant disconnect
            room.on('participantDisconnected', participant => {
                participant.tracks.forEach(publication => {
                    if (publication.track) {
                        publication.track.detach().forEach(element => element.remove());
                    }
                });
            });
        } catch (error) {
            console.error('Error starting call:', error);
        }
    };
</script>
</body>
</html>
