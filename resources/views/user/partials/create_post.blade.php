<form class="compose-box" id="feedComposeForm" action="{{ route('user.feed.posts.store') }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    <div class="row mt-2">
        <div class="col-12">
            <div class="d-flex align-items-center gap-2 mb-2">
                <a href="{{ route('user.member.profile', auth()->user()->id) }}" class="text-decoration-none">
                    @if (auth()->user()->image)
                        <img src="{{ getPhoto(auth()->user()->image) }}" class="rounded-circle" width="40"
                            height="40" alt="{{ auth()->user()->name }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center fs-6 rounded-circle" style="width: 40px; height: 40px; background-color: #7c3aed; color: #fff">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                </a>

                <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle" type="button" id="privacyBtn" data-bs-toggle="dropdown"
                        aria-expanded="false"
                        style="border: 1px solid #cccbcb; color: #984bf3; background-color: transparent; border-radius: 5px;">
                        <i class="fa-solid fa-globe"></i> Public
                    </button>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:void(0)"
                                onclick="updatePrivacy('public', 'fa-solid fa-globe')"><i class="fa-solid fa-globe"></i>
                                Public</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)"
                                onclick="updatePrivacy('private', 'fa-solid fa-lock')"><i class="fa-solid fa-lock"></i>
                                Private</a></li>
                    </ul>

                    <input type="hidden" name="privacy" id="privacyValue" value="public">
                </div>
            </div>

            <textarea class="form-control compose-input" id="composeInput" name="content"
                placeholder="Write a note, or post a photo or voice memory..." rows="2">{{ old('content') }}</textarea>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mt-3">
        <div class="d-flex align-items-center gap-1 flex-nowrap">
            <label class="compose-btn mb-0 text-nowrap d-inline-flex align-items-center gap-1" for="imagesInput"
                style="padding: 6px 10px;">
                <i class="fa-regular fa-image fs-6" style="color: #984bf3;"></i>
                <span class="d-none d-sm-inline">Photo</span>
            </label>
            <input type="file" id="imagesInput" name="images[]" class="d-none" accept="image/*" multiple>

            <label class="compose-btn mb-0 text-nowrap d-inline-flex align-items-center gap-1" for="audioInput"
                style="padding: 6px 10px;">
                <i class="fa-solid fa-microphone fs-6" style="color: #984bf3;"></i>
                <span class="d-none d-sm-inline">Voice</span>
            </label>
            <input type="file" id="audioInput" name="audio" class="d-none" accept="audio/*">

            <button class="compose-btn mb-0 text-nowrap d-inline-flex align-items-center gap-1" type="button"
                id="recordVoiceBtn" style="padding: 6px 10px;">
                <i class="fa-solid fa-circle-dot fs-6" id="recordIcon" style="color: #984bf3;"></i>
                <span class="d-none d-sm-inline" id="recordBtnText">Record</span>
            </button>
        </div>

        <button class="btn-post" type="submit">
            <i class="fa-solid fa-paper-plane fs-6"></i>
            <span>Post</span>
        </button>
    </div>

    <div class="mt-2 d-flex flex-wrap align-items-center gap-2 small text-muted">
        <span id="imagesSelectedText"></span>
    </div>

    <input type="hidden" name="recorded_audio" id="recordedAudioInput">

    <div id="recordingPanel" class="recording-panel d-none mt-3 p-2 px-3 rounded-pill bg-white border shadow-sm w-100">
        <div class="d-flex align-items-center justify-content-between gap-2 w-100">

            <div class="d-flex align-items-center gap-2 min-w-0">
                <span class="rec-dot flex-shrink-0"></span>
                <div class="d-flex align-items-center gap-2 text-nowrap">
                    <span class="fw-semibold text-dark fs-7">Recording</span>
                    <span id="recordingTimer"
                        class="badge rounded-pill bg-danger-subtle text-danger fw-bold border border-danger-subtle fs-8 px-2 py-1">
                        00:00
                    </span>
                </div>

                <div class="rec-wave-container d-none d-sm-flex align-items-center gap-1 ms-1">
                    <span class="wave-bar"></span>
                    <span class="wave-bar"></span>
                    <span class="wave-bar"></span>
                    <span class="wave-bar"></span>
                </div>
            </div>

            <button type="button"
                class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-muted hover-danger ms-auto"
                id="cancelRecordingBtn" style="width: 32px; height: 32px;" title="Cancel Recording">
                <i class="fa-solid fa-xmark fs-6"></i>
            </button>

        </div>
    </div>

    <div id="recordedPreviewTools"
        class="recorded-preview-tools d-none mt-3 p-2 px-3 rounded-4 bg-light border align-items-center justify-content-between gap-2 shadow-sm w-100">

        <audio id="audioPreview" class="d-none"></audio>

        <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
            <button type="button" id="playPreviewBtn"
                class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0 border-0 shadow-sm"
                style="width: 36px; height: 36px; background-color: #984bf3;">
                <i class="fa-solid fa-play fs-6 ms-1" id="playIcon"></i>
            </button>

            <div class="min-w-0 flex-grow-1">
                <p class="mb-0 fw-semibold text-dark fs-7 text-truncate" id="recordedPreviewName">
                    Voice Recording</p>
                <p class="mb-0 text-muted fs-8 text-truncate" id="recordedPreviewStatus">Click
                    play to listen</p>
            </div>
        </div>

        <button type="button"
            class="btn btn-sm btn-outline-danger flex-shrink-0 d-flex align-items-center gap-1 rounded-3"
            id="discardRecordedBtn" style="font-size: 12px; height: 32px;">
            <i class="fa-regular fa-trash-can"></i>
            <span class="d-inline">Discard</span>
        </button>

    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Element Selectors ---
        const composeInput = document.getElementById('composeInput');
        const imagesInput = document.getElementById('imagesInput');
        const audioInput = document.getElementById('audioInput');
        const imagesSelectedText = document.getElementById('imagesSelectedText');
        const audioSelectedText = document.getElementById('audioSelectedText');

        const recordVoiceBtn = document.getElementById('recordVoiceBtn');
        const recordIcon = document.getElementById('recordIcon');
        const recordBtnText = document.getElementById('recordBtnText');

        const recordingPanel = document.getElementById('recordingPanel');
        const recordingStatusText = document.getElementById('recordingStatusText');
        const recordingTimer = document.getElementById('recordingTimer');

        const audioPreview = document.getElementById('audioPreview');
        const playPreviewBtn = document.getElementById('playPreviewBtn');
        const playIcon = document.getElementById('playIcon');
        const recordedPreviewStatus = document.getElementById('recordedPreviewStatus');
        const recordedPreviewTools = document.getElementById('recordedPreviewTools');
        const recordedPreviewName = document.getElementById('recordedPreviewName');
        const recordedAudioInput = document.getElementById('recordedAudioInput');

        const cancelRecordingBtn = document.getElementById('cancelRecordingBtn');
        const discardRecordedBtn = document.getElementById('discardRecordedBtn');
        const feedComposeForm = document.getElementById('feedComposeForm');
        const galleries = document.querySelectorAll('.js-lightgallery');

        // --- 2. State Variables ---
        let mediaRecorder = null;
        let recordingChunks = [];
        let recordingStream = null;
        let recordedAudioFile = null;
        let recordedAudioUrl = null;
        let submitAfterRecording = false;
        let recordingCancelled = false;

        let timerInterval = null;
        let seconds = 0;

        // Apply primary color directly to Preview Play Button if present
        if (playPreviewBtn) {
            playPreviewBtn.style.backgroundColor = '#984bf3';
        }

        // --- 3. Timer Functions ---
        function startTimer() {
            seconds = 0;
            if (recordingTimer) recordingTimer.textContent = "00:00";
            clearInterval(timerInterval);

            timerInterval = setInterval(() => {
                seconds++;
                let mins = Math.floor(seconds / 60);
                let secs = seconds % 60;
                if (recordingTimer) {
                    recordingTimer.textContent =
                        (mins < 10 ? "0" + mins : mins) + ":" + (secs < 10 ? "0" + secs : secs);
                }
            }, 1000);
        }

        function stopTimer() {
            clearInterval(timerInterval);
            seconds = 0;
            if (recordingTimer) recordingTimer.textContent = "00:00";
        }

        // --- 4. Master Play/Pause Toggle ---
        function togglePlayPause() {
            if (!audioPreview || !audioPreview.src) return;

            if (audioPreview.paused) {
                audioPreview.play().catch(err => console.log(err));
            } else {
                audioPreview.pause();
            }
        }

        // --- 5. Audio Player Event Listeners (Auto UI Sync) ---
        if (audioPreview) {
            audioPreview.addEventListener('play', function() {
                if (playIcon) playIcon.className = 'fa-solid fa-pause fs-6';
                if (recordIcon) {
                    recordIcon.className = 'fa-solid fa-pause fs-6';
                    recordIcon.style.color = '#984bf3';
                }
                if (recordBtnText) recordBtnText.textContent = 'Pause';
                if (recordedPreviewStatus) recordedPreviewStatus.textContent =
                    'Playing recorded voice...';
            });

            audioPreview.addEventListener('pause', function() {
                if (playIcon) playIcon.className = 'fa-solid fa-play fs-6 ms-1';
                if (recordIcon) {
                    recordIcon.className = 'fa-solid fa-play fs-6';
                    recordIcon.style.color = '#984bf3';
                }
                if (recordBtnText) recordBtnText.textContent = 'Listen voice';
                if (recordedPreviewStatus) recordedPreviewStatus.textContent = 'Paused';
            });

            audioPreview.addEventListener('ended', function() {
                if (playIcon) playIcon.className = 'fa-solid fa-play fs-6 ms-1';
                if (recordIcon) {
                    recordIcon.className = 'fa-solid fa-play fs-6';
                    recordIcon.style.color = '#984bf3';
                }
                if (recordBtnText) recordBtnText.textContent = 'Listen voice';
                if (recordedPreviewStatus) recordedPreviewStatus.textContent = 'Click play to listen';
            });
        }

        // --- 6. Helper Functions ---
        function clearRecordedAudio() {
            stopTimer();
            recordedAudioFile = null;

            if (recordedAudioInput) recordedAudioInput.value = '';
            if (audioInput) audioInput.value = '';
            if (audioSelectedText) audioSelectedText.textContent = '';

            if (audioPreview) {
                audioPreview.pause();
                audioPreview.classList.add('d-none');
                audioPreview.removeAttribute('src');
            }
            if (recordedPreviewTools) {
                recordedPreviewTools.classList.add('d-none');
                recordedPreviewTools.classList.remove('d-flex');
            }
            if (recordedAudioUrl) {
                URL.revokeObjectURL(recordedAudioUrl);
                recordedAudioUrl = null;
            }

            if (recordVoiceBtn) {
                recordVoiceBtn.removeAttribute('style');
                recordVoiceBtn.style.padding = '6px 10px';
                recordVoiceBtn.className =
                    'compose-btn mb-0 text-nowrap d-inline-flex align-items-center gap-1 btn-record-state rounded';
            }

            if (recordIcon) {
                recordIcon.className = 'fa-solid fa-circle-dot fs-6';
                recordIcon.style.color = '#984bf3';
            }

            if (recordBtnText) {
                recordBtnText.textContent = 'Record';
            }
        }

        // --- 6. Helper Functions ---
        function hideRecordingPanel() {
            stopTimer();
            if (recordingPanel) recordingPanel.classList.add('d-none');
            if (recordingStatusText) {
                recordingStatusText.innerHTML =
                    'Speak clearly. Press stop when you\'re done. <span class="recording-wave"><span></span><span></span><span></span><span></span><span></span><span></span></span>';
            }
        }

        function showRecordingPanel() {
            if (recordingPanel) recordingPanel.classList.remove('d-none');
            if (recordingStatusText) {
                recordingStatusText.innerHTML =
                    'Recording in progress <span class="recording-wave"><span></span><span></span><span></span><span></span><span></span><span></span></span>';
            }
            startTimer();
        }

        // --- 7. Compose Text Area Auto Height ---
        if (composeInput) {
            composeInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        }

        // --- 8. Image Selection Change Listener ---
        if (imagesInput) {
            imagesInput.addEventListener('change', function() {
                const count = this.files ? this.files.length : 0;
                if (imagesSelectedText) {
                    imagesSelectedText.textContent = count ? count + ' image(s) selected' : '';
                }
            });
        }

        // --- 9. Audio Input File Upload Listener ---
        if (audioInput) {
            audioInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    recordedAudioFile = null;
                    if (recordedAudioInput) recordedAudioInput.value = '';

                    const uploadedFile = this.files[0];
                    if (recordedPreviewName) recordedPreviewName.textContent = uploadedFile.name;
                    if (audioSelectedText) audioSelectedText.textContent = uploadedFile.name;
                    if (recordedPreviewStatus) recordedPreviewStatus.textContent =
                        'Click play to listen';

                    if (recordedPreviewTools) {
                        recordedPreviewTools.classList.remove('d-none');
                        recordedPreviewTools.classList.add('d-flex');
                    }

                    if (audioPreview) {
                        if (recordedAudioUrl) URL.revokeObjectURL(recordedAudioUrl);
                        recordedAudioUrl = URL.createObjectURL(uploadedFile);
                        audioPreview.src = recordedAudioUrl;
                        audioPreview.classList.remove('d-none');
                        audioPreview.load();
                    }

                    if (recordIcon) {
                        recordIcon.className = 'fa-solid fa-play fs-6';
                        recordIcon.style.color = '#984bf3';
                    }
                    if (recordBtnText) recordBtnText.textContent = 'Listen voice';
                } else {
                    clearRecordedAudio();
                }
            });
        }

        // --- 10. Record / Main Top Button Click Event ---
        if (recordVoiceBtn) {
            if (!navigator.mediaDevices || !window.MediaRecorder) {
                recordVoiceBtn.disabled = true;
                recordVoiceBtn.title = 'Voice recording is not supported in this browser.';
            } else {
                recordVoiceBtn.addEventListener('click', async function() {
                    if (audioPreview && audioPreview.src && (!mediaRecorder || mediaRecorder
                            .state !== 'recording')) {
                        togglePlayPause();
                        return;
                    }

                    // B. recording
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                        return;
                    }

                    // C. new recording
                    try {
                        recordingStream = await navigator.mediaDevices.getUserMedia({
                            audio: true
                        });
                        recordingChunks = [];
                        recordingCancelled = false;
                        mediaRecorder = new MediaRecorder(recordingStream);

                        mediaRecorder.ondataavailable = function(event) {
                            if (event.data && event.data.size > 0) {
                                recordingChunks.push(event.data);
                            }
                        };

                        mediaRecorder.onstop = function() {
                            if (recordingCancelled) {
                                recordingCancelled = false;
                                hideRecordingPanel();
                                clearRecordedAudio();
                                if (recordingStream) {
                                    recordingStream.getTracks().forEach(track => track.stop());
                                    recordingStream = null;
                                }
                                return;
                            }

                            const blob = new Blob(recordingChunks, {
                                type: mediaRecorder.mimeType || 'audio/webm',
                            });
                            const fileName = `voice-memory-${Date.now()}.webm`;
                            recordedAudioFile = new File([blob], fileName, {
                                type: blob.type
                            });

                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(recordedAudioFile);
                            if (audioInput) audioInput.files = dataTransfer.files;
                            if (audioSelectedText) audioSelectedText.textContent =
                                recordedAudioFile.name;

                            if (audioPreview) {
                                if (recordedAudioUrl) URL.revokeObjectURL(recordedAudioUrl);
                                recordedAudioUrl = URL.createObjectURL(blob);
                                audioPreview.src = recordedAudioUrl;
                                audioPreview.classList.remove('d-none');
                            }

                            if (recordedPreviewTools) {
                                recordedPreviewTools.classList.remove('d-none');
                                recordedPreviewTools.classList.add('d-flex');
                            }
                            if (recordedPreviewName) recordedPreviewName.textContent = fileName;
                            if (recordedPreviewStatus) recordedPreviewStatus.textContent =
                                'Click play to listen';

                            if (recordedAudioInput) {
                                const reader = new FileReader();
                                reader.onloadend = function() {
                                    recordedAudioInput.value = reader.result;
                                };
                                reader.readAsDataURL(blob);
                            }

                            hideRecordingPanel();

                            // --- RECORDING STOPPED UI RESET ---
                            if (recordIcon) {
                                recordIcon.className = 'fa-solid fa-play fs-6';
                                recordIcon.style.color = '#984bf3';
                            }
                            if (recordBtnText) recordBtnText.textContent = 'Listen voice';

                            // reset btn
                            recordVoiceBtn.style.backgroundColor = '';
                            recordVoiceBtn.style.borderColor = '';
                            recordVoiceBtn.style.color = '';

                            if (recordingStream) {
                                recordingStream.getTracks().forEach(track => track.stop());
                                recordingStream = null;
                            }

                            if (submitAfterRecording && feedComposeForm) {
                                submitAfterRecording = false;
                                feedComposeForm.submit();
                            }
                        };

                        mediaRecorder.start();
                        showRecordingPanel();

                        // --- RECORDING IN PROGRESS UI---
                        recordVoiceBtn.style.backgroundColor = '#984bf3';
                        recordVoiceBtn.style.borderColor = '#984bf3';
                        recordVoiceBtn.style.color = '#ffffff';

                        if (recordIcon) {
                            recordIcon.className = 'fa-solid fa-square fs-6 text-white';
                            recordIcon.style.color = '#ffffff';
                        }
                        if (recordBtnText) recordBtnText.textContent = 'Stop to listen';

                    } catch (error) {
                        if (recordingStatusText) {
                            recordingStatusText.textContent = 'Microphone access was denied.';
                        }
                        showRecordingPanel();
                    }
                });
            }
        }

        // --- 11. Custom Player Green Play Button Event ---
        if (playPreviewBtn) {
            playPreviewBtn.addEventListener('click', function(e) {
                e.preventDefault();
                togglePlayPause();
            });
        }

        // --- 12. Cancel & Discard Recording Buttons ---
        if (cancelRecordingBtn) {
            cancelRecordingBtn.addEventListener('click', function() {
                submitAfterRecording = false;
                recordingCancelled = true;

                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    mediaRecorder.stop();
                    return;
                }

                hideRecordingPanel();
                clearRecordedAudio();
            });
        }

        if (discardRecordedBtn) {
            discardRecordedBtn.addEventListener('click', function() {
                clearRecordedAudio();
            });
        }

        // --- 13. Form Submit Logic ---
        if (feedComposeForm) {
            feedComposeForm.addEventListener('submit', function(event) {
                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    event.preventDefault();
                    submitAfterRecording = true;
                    mediaRecorder.stop();
                    return;
                }

                if (recordedAudioFile && audioInput && (!audioInput.files || !audioInput.files
                        .length)) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(recordedAudioFile);
                    audioInput.files = dataTransfer.files;
                }

                if (recordedAudioFile && recordedAudioInput && !recordedAudioInput.value) {
                    const reader = new FileReader();
                    reader.onloadend = function() {
                        recordedAudioInput.value = reader.result;
                        feedComposeForm.submit();
                    };
                    reader.readAsDataURL(recordedAudioFile);
                    event.preventDefault();
                }
            });
        }

        // --- 14. LightGallery Integration ---
        galleries.forEach(function(gallery) {
            const items = JSON.parse(gallery.dataset.galleryItems || '[]')
                .filter(function(item) {
                    return item && item.src;
                });
            const triggers = gallery.querySelectorAll('[data-gallery-index]');
            let galleryInstance = null;

            function destroyGalleryInstance() {
                if (galleryInstance && typeof galleryInstance.destroy === 'function') {
                    galleryInstance.destroy(true);
                }
                galleryInstance = null;
            }

            triggers.forEach(function(trigger) {
                trigger.addEventListener('click', function(event) {
                    event.preventDefault();

                    const index = Number(this.dataset.galleryIndex || 0);

                    destroyGalleryInstance();

                    galleryInstance = lightGallery(gallery, {
                        dynamic: true,
                        dynamicEl: items,
                        index: index,
                        plugins: [lgZoom, lgThumbnail],
                        speed: 300,
                        download: true,
                        closable: true,
                        showCloseIcon: true,

                        mobileSettings: {
                            controls: true,
                            showCloseIcon: true,
                            download: true,
                            rotate: false
                        }
                    });

                    galleryInstance.openGallery(index);
                });
            });
        });
    });
</script>
