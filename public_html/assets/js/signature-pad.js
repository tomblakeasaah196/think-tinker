/**
 * assets/js/signature-pad.js — Draw-to-sign canvas for contracts
 */
'use strict';
const SignaturePad = window.SignaturePad || {};

SignaturePad.canvas = null;
SignaturePad.ctx = null;
SignaturePad.drawing = false;
SignaturePad.lastX = 0;
SignaturePad.lastY = 0;

SignaturePad.init = function(canvasSelector) {
    const canvas = document.querySelector(canvasSelector);
    if (!canvas) return;
    SignaturePad.canvas = canvas;
    SignaturePad.ctx = canvas.getContext('2d');
    SignaturePad.ctx.strokeStyle = '#1B2A4A';
    SignaturePad.ctx.lineWidth = 2;
    SignaturePad.ctx.lineCap = 'round';
    SignaturePad.ctx.lineJoin = 'round';

    // Resize canvas
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;

    // Mouse events
    canvas.addEventListener('mousedown', e => { SignaturePad.drawing = true; const r = canvas.getBoundingClientRect(); SignaturePad.lastX = e.clientX - r.left; SignaturePad.lastY = e.clientY - r.top; });
    canvas.addEventListener('mousemove', e => { if (!SignaturePad.drawing) return; const r = canvas.getBoundingClientRect(); SignaturePad.draw(e.clientX - r.left, e.clientY - r.top); });
    canvas.addEventListener('mouseup', () => { SignaturePad.drawing = false; });
    canvas.addEventListener('mouseleave', () => { SignaturePad.drawing = false; });

    // Touch events
    canvas.addEventListener('touchstart', e => { e.preventDefault(); SignaturePad.drawing = true; const r = canvas.getBoundingClientRect(); const t = e.touches[0]; SignaturePad.lastX = t.clientX - r.left; SignaturePad.lastY = t.clientY - r.top; });
    canvas.addEventListener('touchmove', e => { e.preventDefault(); if (!SignaturePad.drawing) return; const r = canvas.getBoundingClientRect(); const t = e.touches[0]; SignaturePad.draw(t.clientX - r.left, t.clientY - r.top); });
    canvas.addEventListener('touchend', () => { SignaturePad.drawing = false; });
};

SignaturePad.draw = function(x, y) {
    SignaturePad.ctx.beginPath();
    SignaturePad.ctx.moveTo(SignaturePad.lastX, SignaturePad.lastY);
    SignaturePad.ctx.lineTo(x, y);
    SignaturePad.ctx.stroke();
    SignaturePad.lastX = x;
    SignaturePad.lastY = y;
};

SignaturePad.clear = function() {
    SignaturePad.ctx.clearRect(0, 0, SignaturePad.canvas.width, SignaturePad.canvas.height);
};

SignaturePad.isEmpty = function() {
    const blank = document.createElement('canvas');
    blank.width = SignaturePad.canvas.width;
    blank.height = SignaturePad.canvas.height;
    return SignaturePad.canvas.toDataURL() === blank.toDataURL();
};

SignaturePad.toDataURL = function() {
    return SignaturePad.canvas.toDataURL('image/png');
};

SignaturePad.upload = function() {
    if (SignaturePad.isEmpty()) { TT.toast('Please draw your signature first.', 'warning'); return Promise.reject(); }
    return TT.api('UploadController.php', { action: 'upload_signature', signature_data: SignaturePad.toDataURL() });
};

window.SignaturePad = SignaturePad;
