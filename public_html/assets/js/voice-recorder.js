/**
 * assets/js/voice-recorder.js — Web Speech API wrapper for session notes
 */
'use strict';
const VoiceRecorder = window.VoiceRecorder || {};

VoiceRecorder.recognition = null;
VoiceRecorder.isRecording = false;
VoiceRecorder.targetField = null;

VoiceRecorder.init = function(targetSelector, btnSelector) {
    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
        $(btnSelector).hide();
        return;
    }
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    VoiceRecorder.recognition = new SpeechRecognition();
    VoiceRecorder.recognition.continuous = true;
    VoiceRecorder.recognition.interimResults = true;
    VoiceRecorder.recognition.lang = 'en-NG';
    VoiceRecorder.targetField = targetSelector;

    VoiceRecorder.recognition.onresult = function(e) {
        let final = '', interim = '';
        for (let i = e.resultIndex; i < e.results.length; i++) {
            if (e.results[i].isFinal) final += e.results[i][0].transcript + ' ';
            else interim += e.results[i][0].transcript;
        }
        if (final) {
            const current = $(VoiceRecorder.targetField).val();
            $(VoiceRecorder.targetField).val(current + final);
        }
    };

    VoiceRecorder.recognition.onerror = function(e) {
        console.warn('Speech error:', e.error);
        VoiceRecorder.stop(btnSelector);
    };

    VoiceRecorder.recognition.onend = function() {
        if (VoiceRecorder.isRecording) VoiceRecorder.recognition.start(); // keep going
    };

    $(btnSelector).on('click', function() {
        if (VoiceRecorder.isRecording) VoiceRecorder.stop(btnSelector);
        else VoiceRecorder.start(btnSelector);
    });
};

VoiceRecorder.start = function(btnSelector) {
    VoiceRecorder.isRecording = true;
    VoiceRecorder.recognition.start();
    $(btnSelector).addClass('recording').html('🔴 Stop');
};

VoiceRecorder.stop = function(btnSelector) {
    VoiceRecorder.isRecording = false;
    VoiceRecorder.recognition.stop();
    $(btnSelector).removeClass('recording').html('🎤 Voice');
};

window.VoiceRecorder = VoiceRecorder;
