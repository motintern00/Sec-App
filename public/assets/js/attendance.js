(function () {
    'use strict';

    const geoStatus = document.getElementById('geo_status');
    const statusMessage = document.getElementById('status_message');
    const btnAttendance = document.getElementById('btn_attendance');
    const btnSwitchCamera = document.getElementById('btn_switch_camera');
    const video = document.getElementById('camera_preview');
    const canvas = document.getElementById('photo_canvas');
    const stepGps = document.getElementById('step_gps');
    const stepCamera = document.getElementById('step_camera');

    let currentLatitude = null;
    let currentLongitude = null;
    let currentAction = 'check-in';
    let employeeData = window.gpaEmployeePayload || {};
    let cameraStream = null;
    let facingMode = 'user';

    const officeLat = -6.242792163317656;
    const officeLng = 106.84609367942863;
    const radiusM = 100;

    function haversineMeters(lat1, lng1, lat2, lng2) {
        const earthRadius = 6371000;
        const latFrom = lat1 * Math.PI / 180;
        const latTo = lat2 * Math.PI / 180;
        const latDelta = (lat2 - lat1) * Math.PI / 180;
        const lngDelta = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(latDelta / 2) ** 2 + Math.cos(latFrom) * Math.cos(latTo) * Math.sin(lngDelta / 2) ** 2;
        return earthRadius * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function updateGeoStatus(lat, lng) {
        const distance = haversineMeters(officeLat, officeLng, lat, lng);
        const inside = distance <= radiusM;
        geoStatus.className = 'gpa-geo-status ' + (inside ? 'inside' : 'outside');
        geoStatus.innerHTML = inside
            ? '<i class="bi bi-geo-alt-fill me-1"></i> Di area kantor (' + Math.round(distance) + ' m)'
            : '<i class="bi bi-exclamation-triangle me-1"></i> Di luar area (' + Math.round(distance) + ' m)';
        if (stepGps) stepGps.classList.add(inside ? 'done' : 'active');
        return inside;
    }

    function initGeolocation() {
        if (!navigator.geolocation) {
            geoStatus.className = 'gpa-geo-status outside';
            geoStatus.textContent = 'Geolocation tidak didukung.';
            return;
        }
        navigator.geolocation.watchPosition(
            function (position) {
                currentLatitude = position.coords.latitude;
                currentLongitude = position.coords.longitude;
                updateGeoStatus(currentLatitude, currentLongitude);
            },
            function () {
                geoStatus.className = 'gpa-geo-status outside';
                geoStatus.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Izinkan akses GPS.';
            },
            { enableHighAccuracy: true, maximumAge: 10000, timeout: 15000 }
        );
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(function (t) { t.stop(); });
            cameraStream = null;
        }
    }

    async function initCamera() {
        stopCamera();
        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode }, audio: false });
            video.srcObject = cameraStream;
            if (stepCamera) stepCamera.classList.add('done');
        } catch (err) {
            Swal.fire('Kamera', 'Tidak dapat mengakses kamera.', 'error');
        }
    }

    async function switchCamera() {
        facingMode = facingMode === 'user' ? 'environment' : 'user';
        await initCamera();
        GpaApp.showToast('Kamera: ' + (facingMode === 'user' ? 'Depan' : 'Belakang'), 'info');
    }

    function capturePhoto() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        return new Promise(function (resolve) {
            canvas.toBlob(function (blob) { resolve(blob); }, 'image/jpeg', 0.9);
        });
    }

    function updateButtonState() {
        if (employeeData.can_check_out) {
            currentAction = 'check-out';
            btnAttendance.innerHTML = '<i class="bi bi-box-arrow-right me-1"></i> Absen Pulang';
            btnAttendance.disabled = false;
        } else if (employeeData.can_check_in) {
            currentAction = 'check-in';
            btnAttendance.innerHTML = '<i class="bi bi-fingerprint me-1"></i> Absen Masuk';
            btnAttendance.disabled = false;
        } else {
            btnAttendance.disabled = true;
            btnAttendance.innerHTML = '<i class="bi bi-fingerprint me-1"></i> ' + (employeeData.action_label || 'Tidak Tersedia');
        }
        if (statusMessage && employeeData.status_message) {
            statusMessage.textContent = employeeData.status_message;
        }
    }

    async function confirmPhotoPreview(photoBlob) {
        const previewUrl = URL.createObjectURL(photoBlob);
        const result = await Swal.fire({
            title: currentAction === 'check-in' ? 'Konfirmasi Check-in' : 'Konfirmasi Check-out',
            html: '<img src="' + previewUrl + '" class="gpa-preview-img" alt="Preview">',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Ambil Ulang',
            confirmButtonColor: '#3b82f6',
        });
        URL.revokeObjectURL(previewUrl);
        return result.isConfirmed;
    }

    async function submitAttendance() {
        if (currentLatitude === null) {
            Swal.fire('GPS', 'Menunggu lokasi GPS...', 'warning');
            return;
        }
        if (!updateGeoStatus(currentLatitude, currentLongitude)) {
            Swal.fire('Ditolak', 'Anda berada di luar area kantor.', 'error');
            return;
        }

        const photoBlob = await capturePhoto();
        if (!photoBlob) return;

        if (!await confirmPhotoPreview(photoBlob)) return;

        const url = currentAction === 'check-in' ? '/employee/attendance/check-in' : '/employee/attendance/check-out';
        const formData = new FormData();
        formData.append('latitude', currentLatitude);
        formData.append('longitude', currentLongitude);
        formData.append('photo', photoBlob, 'attendance.jpg');

        GpaApp.showLoading();
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData,
            });
            const result = await response.json();
            if (!response.ok || !result.success) {
                Swal.fire('Ditolak', result.message, 'error');
                return;
            }
            Swal.fire('Berhasil', result.message, 'success');
            GpaApp.showToast(result.message, 'success');
            employeeData = result.data;
            updateButtonState();
            GpaApp.loadNotifications();
        } catch (e) {
            Swal.fire('Error', 'Gagal memproses absensi.', 'error');
        } finally {
            GpaApp.hideLoading();
        }
    }

    if (btnAttendance) btnAttendance.addEventListener('click', submitAttendance);
    if (btnSwitchCamera) btnSwitchCamera.addEventListener('click', switchCamera);

    document.addEventListener('DOMContentLoaded', function () {
        updateButtonState();
        initGeolocation();
        initCamera();
    });
})();
