<style>
    .messenger-composer { padding:12px 16px 14px; border-top:1px solid #e4e6eb; background:#fff; }
    .messenger-composer textarea { display:block; width:100%; min-height:48px; max-height:140px; resize:vertical; padding:10px 13px; border:1px solid transparent; border-radius:18px; outline:none; background:#f0f2f5; font:inherit; font-size:14px; }
    .messenger-composer textarea:focus { border-color:#b6d5ff; background:#fff; }
    .messenger-toolbar { display:flex; align-items:center; gap:8px; margin-top:9px; }
    .messenger-tool { display:inline-flex; min-height:38px; align-items:center; justify-content:center; gap:7px; padding:8px 12px; border:0; border-radius:20px; background:#f0f2f5; color:#3a3b3c; font:inherit; font-size:12px; font-weight:700; cursor:pointer; }
    .messenger-tool:hover { background:#e4e6eb; }
    .messenger-tool svg { width:17px; height:17px; }
    .messenger-send { display:inline-flex; width:40px; height:40px; flex:none; align-items:center; justify-content:center; margin-left:auto; border:0; border-radius:50%; background:#0084ff; color:#fff; cursor:pointer; }
    .messenger-send:hover { background:#006edb; }
    .messenger-send svg { width:20px; height:20px; }
    .messenger-file-input { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); }
    .messenger-file-status { min-width:0; overflow:hidden; color:#65676b; font-size:12px; text-overflow:ellipsis; white-space:nowrap; }
    .messenger-preview { display:flex; align-items:center; gap:10px; margin-top:10px; padding:9px; border:1px solid #e4e6eb; border-radius:12px; background:#f7f8fa; }
    .messenger-preview[hidden] { display:none; }
    .messenger-preview-image { width:56px; height:48px; border-radius:7px; object-fit:cover; }
    .messenger-preview-audio { width:min(100%, 340px); height:38px; }
    .messenger-preview-name { min-width:0; overflow:hidden; color:#3a3b3c; font-size:12px; text-overflow:ellipsis; white-space:nowrap; }
    .messenger-preview-remove { width:30px; height:30px; flex:none; margin-left:auto; border:0; border-radius:50%; background:#e4e6eb; color:#3a3b3c; font-size:19px; cursor:pointer; }
    .messenger-recording { display:inline-flex; align-items:center; gap:7px; color:#d93025; font-size:12px; font-weight:700; }
    .messenger-recording[hidden] { display:none; }
    .messenger-recording-dot { width:8px; height:8px; border-radius:50%; background:#d93025; animation:messenger-pulse 1s infinite; }
    .messenger-error { margin-top:6px; color:#dc2626; font-size:12px; }
    @keyframes messenger-pulse { 50% { opacity:.3; } }
    @media (max-width:600px) { .messenger-composer { padding:10px; } .messenger-tool { width:40px; padding:8px; } .messenger-tool-text { display:none; } .messenger-file-status { flex:1; } }
</style>

<form class="messenger-composer" id="message-composer" method="POST" enctype="multipart/form-data" action="{{ $sendUrl }}">
    @csrf
    <label class="sr-only" for="message-body">Write a message</label>
    <textarea id="message-body" name="body" maxlength="5000" placeholder="Write a message…">{{ old('body') }}</textarea>

    <div class="messenger-preview" id="attachment-preview" hidden>
        <img class="messenger-preview-image" id="attachment-preview-image" alt="" hidden>
        <audio class="messenger-preview-audio" id="attachment-preview-audio" controls hidden></audio>
        <span class="messenger-preview-name" id="attachment-preview-name"></span>
        <button class="messenger-preview-remove" id="remove-attachment" type="button" aria-label="Remove attachment">×</button>
    </div>

    <div class="messenger-toolbar">
        <label class="messenger-tool" for="chat-attachment" title="Attach a photo or audio file" aria-label="Attach a photo or audio file">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.4 11.1-8.5 8.5a6 6 0 0 1-8.5-8.5l9.2-9.2a4 4 0 0 1 5.7 5.7l-9.2 9.2a2 2 0 0 1-2.8-2.8l8.5-8.5"/></svg>
            <span class="messenger-tool-text">Attach</span>
        </label>
        <input class="messenger-file-input" id="chat-attachment" type="file" name="attachment" accept="image/*,audio/*,video/webm">

        <button class="messenger-tool" id="record-voice" type="button" title="Record a voice message">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="2" width="6" height="12" rx="3"/><path d="M5 10a7 7 0 0 0 14 0M12 17v5m-4 0h8"/></svg>
            <span class="messenger-tool-text" id="record-label">Voice message</span>
        </button>
        <span class="messenger-recording" id="recording-status" hidden><span class="messenger-recording-dot"></span><span id="recording-time">00:00</span></span>
        <span class="messenger-file-status" id="attachment-status" aria-live="polite">Photo or audio up to 20 MB</span>
        <button class="messenger-send" type="submit" title="Send message" aria-label="Send message">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3.4 20.4 21 12 3.4 3.6 3 10l12 2-12 2z"/></svg>
        </button>
    </div>
    @error('body') <div class="messenger-error">{{ $message }}</div> @enderror
    @error('attachment') <div class="messenger-error">{{ $message }}</div> @enderror
</form>

<script>
    (function () {
        var form = document.getElementById('message-composer');
        var textarea = document.getElementById('message-body');
        var input = document.getElementById('chat-attachment');
        var preview = document.getElementById('attachment-preview');
        var previewImage = document.getElementById('attachment-preview-image');
        var previewAudio = document.getElementById('attachment-preview-audio');
        var previewName = document.getElementById('attachment-preview-name');
        var status = document.getElementById('attachment-status');
        var recordButton = document.getElementById('record-voice');
        var recordLabel = document.getElementById('record-label');
        var recordingStatus = document.getElementById('recording-status');
        var recordingTime = document.getElementById('recording-time');
        var recorder = null;
        var stream = null;
        var chunks = [];
        var startedAt = 0;
        var timer = null;
        var previewUrl = null;

        function clearPreviewUrl() {
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = null;
        }

        function showSelectedFile(file) {
            clearPreviewUrl();
            preview.hidden = false;
            previewImage.hidden = true;
            previewAudio.hidden = true;
            previewImage.removeAttribute('src');
            previewAudio.removeAttribute('src');
            previewName.textContent = file.name;
            previewUrl = URL.createObjectURL(file);

            if (file.type.startsWith('image/')) {
                previewImage.src = previewUrl;
                previewImage.hidden = false;
            } else {
                previewAudio.src = previewUrl;
                previewAudio.hidden = false;
            }

            status.textContent = 'Ready to send';
        }

        function clearAttachment() {
            input.value = '';
            preview.hidden = true;
            previewImage.removeAttribute('src');
            previewAudio.removeAttribute('src');
            previewImage.hidden = true;
            previewAudio.hidden = true;
            previewName.textContent = '';
            clearPreviewUrl();
            status.textContent = 'Photo or audio up to 20 MB';
        }

        input.addEventListener('change', function () {
            if (input.files.length) showSelectedFile(input.files[0]);
            else clearAttachment();
        });
        document.getElementById('remove-attachment').addEventListener('click', clearAttachment);

        recordButton.addEventListener('click', async function () {
            if (recorder && recorder.state === 'recording') {
                recorder.stop();
                clearInterval(timer);
                recordingStatus.hidden = true;
                recordLabel.textContent = 'Voice message';
                status.textContent = 'Preparing voice message…';
                return;
            }

            if (!navigator.mediaDevices || !window.MediaRecorder) {
                status.textContent = 'Recording is not supported here. Attach an audio file instead.';
                return;
            }

            try {
                stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                chunks = [];
                var options = {};
                if (MediaRecorder.isTypeSupported('audio/webm;codecs=opus')) options.mimeType = 'audio/webm;codecs=opus';
                else if (MediaRecorder.isTypeSupported('audio/ogg;codecs=opus')) options.mimeType = 'audio/ogg;codecs=opus';
                recorder = new MediaRecorder(stream, options);
                recorder.addEventListener('dataavailable', function (event) {
                    if (event.data.size) chunks.push(event.data);
                });
                recorder.addEventListener('stop', function () {
                    if (!chunks.length) {
                        status.textContent = 'No audio was recorded. Please try again.';
                        if (stream) stream.getTracks().forEach(function (track) { track.stop(); });
                        stream = null;
                        return;
                    }
                    var mime = recorder.mimeType || 'audio/webm';
                    var extension = mime.indexOf('ogg') !== -1 ? 'ogg' : (mime.indexOf('mp4') !== -1 ? 'm4a' : 'webm');
                    var voiceFile = new File(chunks, 'voice-message.' + extension, { type: mime });
                    var transfer = new DataTransfer();
                    transfer.items.add(voiceFile);
                    input.files = transfer.files;
                    showSelectedFile(voiceFile);
                    if (stream) stream.getTracks().forEach(function (track) { track.stop(); });
                    stream = null;
                });
                recorder.start();
                startedAt = Date.now();
                recordingStatus.hidden = false;
                recordLabel.textContent = 'Stop';
                status.textContent = 'Recording voice…';
                timer = setInterval(function () {
                    var seconds = Math.floor((Date.now() - startedAt) / 1000);
                    recordingTime.textContent = String(Math.floor(seconds / 60)).padStart(2, '0') + ':' + String(seconds % 60).padStart(2, '0');
                }, 250);
            } catch (error) {
                status.textContent = 'Microphone unavailable. Check browser permission or attach an audio file.';
            }
        });

        form.addEventListener('submit', function (event) {
            if (recorder && recorder.state === 'recording') {
                event.preventDefault();
                status.textContent = 'Stop recording before sending.';
            } else if (!textarea.value.trim() && !input.files.length) {
                event.preventDefault();
                textarea.focus();
                status.textContent = 'Write a message or attach a photo/audio file first.';
            }
        });

        textarea.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                form.requestSubmit();
            }
        });

        var chatMessages = document.getElementById('chat-messages');
        if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
    })();
</script>
