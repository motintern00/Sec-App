(function () {
    'use strict';

    const employeeSelect = document.getElementById('employee_id');
    const departmentInput = document.getElementById('department_name');
    const shiftInfo = document.getElementById('shift_info');
    const geoStatus = document.getElementById('geo_status');
    const statusMessage = document.getElementById('status_message');
    const btnAttendance = document.getElementById('btn_attendance');
    const video = document.getElementById('camera_preview');
    const canvas = document.getElementById('photo_canvas');

    let currentLatitude = null;
    let currentLongitude = null;
    let currentAction = 'check-in';
    let employeeData = null;
    let cameraStream = null;

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
            : '<i class="bi bi-exclamation-triangle me-1"></i> Di luar area kantor (' + Math.round(distance) + ' m)';
        return inside;
    }

    function initGeolocation() {
        if (!navigator.geolocation) {
            geoStatus.className = 'gpa-geo-status outside';
            geoStatus.textContent = 'Geolocation tidak didukung browser.';
            return;
        }

        navigator.geolocation.watchPosition(
            (position) => {
                currentLatitude = position.coords.latitude;
                currentLongitude = position.coords.longitude;
                updateGeoStatus(currentLatitude, currentLongitude);
            },
            () => {
                geoStatus.className = 'gpa-geo-status outside';
                geoStatus.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Gagal mendapatkan lokasi. Izinkan akses GPS.';
            },
            { enableHighAccuracy: true, maximumAge: 10000, timeout: 15000 }
        );
    }

    async function initCamera() {
        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            video.srcObject = cameraStream;
        } catch (err) {
            Swal.fire('Kamera', 'Tidak dapat mengakses kamera. Periksa izin browser.', 'error');
        }
    }

    function capturePhoto() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        return new Promise((resolve) => {
            canvas.toBlob((blob) => resolve(blob), 'image/jpeg', 0.9);
        });
    }

    function updateButtonState() {
        if (!employeeData) {
            btnAttendance.disabled = true;
            return;
        }

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
            btnAttendance.innerHTML = '<i class="bi bi-fingerprint me-1"></i> ' + employeeData.action_label;
        }
    }

    async function loadEmployeeDetail(employeeId) {
        if (!employeeId) {
            departmentInput.value = '';
            shiftInfo.textContent = 'Pilih pegawai untuk melihat shift.';
            statusMessage.className = 'alert alert-info small';
            statusMessage.textContent = 'Pilih pegawai untuk memulai absensi.';
            employeeData = null;
            updateButtonState();
            return;
        }

        GpaApp.showLoading();
        try {
            const response = await fetch('/security/employees/' + employeeId + '/detail', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Gagal memuat data pegawai.');

            employeeData = data;
            departmentInput.value = data.department;
            shiftInfo.innerHTML = '<strong>' + data.shift.name + '</strong> — Masuk: ' + data.shift.check_in + ', Toleransi: ' + data.shift.tolerance + ', Pulang: ' + data.shift.check_out;
            statusMessage.className = 'alert alert-secondary small';
            statusMessage.textContent = data.status_message;
            updateButtonState();
        } catch (error) {
            GpaApp.showToast(error.message, 'error');
        } finally {
            GpaApp.hideLoading();
        }
    }

    async function submitAttendance() {
        const employeeId = employeeSelect.value;
        if (!employeeId || !employeeData) return;

        if (currentLatitude === null || currentLongitude === null) {
            Swal.fire('Lokasi', 'Lokasi GPS belum tersedia. Tunggu sebentar atau izinkan akses lokasi.', 'warning');
            return;
        }

        if (!updateGeoStatus(currentLatitude, currentLongitude)) {
            Swal.fire('Ditolak', 'Anda berada di luar area kantor. Absensi ditolak.', 'error');
            return;
        }

        const photoBlob = await capturePhoto();
        if (!photoBlob) {
            Swal.fire('Kamera', 'Gagal mengambil foto.', 'error');
            return;
        }

        const url = currentAction === 'check-in'
            ? '/security/attendance/check-in'
            : '/security/attendance/check-out';

        const formData = new FormData();
        formData.append('employee_id', employeeId);
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
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });
            const result = await response.json();

            if (!response.ok || !result.success) {
                Swal.fire('Ditolak', result.message || 'Absensi gagal.', 'error');
                return;
            }

            Swal.fire('Berhasil', result.message, 'success');
            GpaApp.showToast(result.message, 'success');
            employeeData = result.data;
            statusMessage.textContent = result.data.status_message;
            updateButtonState();
        } catch (error) {
            Swal.fire('Error', 'Terjadi kesalahan saat memproses absensi.', 'error');
        } finally {
            GpaApp.hideLoading();
        }
    }

    employeeSelect.addEventListener('change', () => loadEmployeeDetail(employeeSelect.value));
    btnAttendance.addEventListener('click', submitAttendance);

    document.addEventListener('DOMContentLoaded', () => {
        initGeolocation();
        initCamera();
    });
})();
