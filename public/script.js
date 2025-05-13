// Coodingan JS Untuk Tampilan Create

// Tambahkan flag untuk mencegah multiple submit
let isSubmitting = false;
let photoTaken = false; // Add flag to track if photo has been taken

// Ambil elemen video dan canvas
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const captureBtn = document.getElementById('captureBtn');
const captureBtnText = document.getElementById('captureBtnText');
const flash = document.getElementById('flash');
const countdown = document.getElementById('countdown');
const lokasiInput = document.getElementById('lokasi');
const tanggalInput = document.getElementById('tanggal');
const jamInput = document.getElementById('jam');
const form = document.getElementById('attendanceForm');
const submitBtn = document.getElementById('submitBtn');

// Update waktu secara real-time
function updateWaktu() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    tanggalInput.value = now.toLocaleDateString('id-ID', options);
    jamInput.value = now.toLocaleTimeString('id-ID');
}
setInterval(updateWaktu, 1000);
updateWaktu();

// Akses kamera
if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    // Request specific video dimensions
    const constraints = {
        video: {
            width: { ideal: 640 },
            height: { ideal: 480 },
            aspectRatio: 4/3,
            facingMode: 'user'
        }
    };

    navigator.mediaDevices.getUserMedia(constraints)
        .then(stream => {
            video.srcObject = stream;
            // Set canvas size when video metadata is loaded
            video.onloadedmetadata = () => {
                // Set canvas size to match video dimensions exactly
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            };
        })
        .catch(err => {
            console.error("Error accessing camera: ", err);
            alert("Akses kamera ditolak. Mohon izinkan akses kamera untuk melanjutkan.");
        });
}

// Fungsi Countdown dan Capture
function startCountdown() {
    let count = 3;
    captureBtn.disabled = true;
    video.classList.add('border-pulse');
    countdown.classList.add('active');

    const countdownInterval = setInterval(() => {
        countdown.textContent = count;
        if (count === 0) {
            clearInterval(countdownInterval);
            countdown.classList.remove('active');
            capturePhoto();
            captureBtn.disabled = false;
            video.classList.remove('border-pulse');
        }
        count--;
    }, 1000);
}

function capturePhoto() {
    // Flash effect
    flash.classList.add('active');
    setTimeout(() => {
        flash.classList.remove('active');
    }, 300);

    // Get the video dimensions
    const videoWidth = video.videoWidth;
    const videoHeight = video.videoHeight;

    // Set canvas dimensions to match video
    canvas.width = videoWidth;
    canvas.height = videoHeight;

    // Get the context and draw the video frame
    const ctx = canvas.getContext('2d');
    // Clear the canvas first
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    // Draw the video frame
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Show canvas and hide video
    canvas.classList.remove('hidden');
    video.classList.add('hidden');
    captureBtnText.textContent = 'Ambil Ulang';
    photoTaken = true;
}

// Tangkap foto dengan countdown
captureBtn.addEventListener('click', () => {
    if (captureBtnText.textContent === 'Ambil Foto') {
        startCountdown();
    } else {
        // Ambil ulang
        canvas.classList.add('hidden');
        video.classList.remove('hidden');
        captureBtnText.textContent = 'Ambil Foto';
        photoTaken = false; // Reset flag when retaking photo
    }
});

// Dapatkan lokasi
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(position => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
            .then(response => response.json())
            .then(data => {
                const address = data.display_name || `Lat: ${lat}, Lon: ${lon}`;
                lokasiInput.value = address;
            })
            .catch(() => {
                lokasiInput.value = `Lokasi: ${lat.toFixed(4)}, ${lon.toFixed(4)}`;
            });
    }, error => {
        console.error("Error getting location: ", error);
        lokasiInput.value = "Lokasi tidak dapat diakses";
    }, {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
    });
} else {
    lokasiInput.value = "Geolocation tidak didukung";
}

// Autofill hadir_untuk berdasarkan nama karyawan
const namaSelect = document.getElementById('nama');
const hadirUntukInput = document.getElementById('hadir_untuk');

// Di dalam event listener namaSelect
namaSelect.addEventListener('change', async function () {
    const selectedOption = this.options[this.selectedIndex];
    const hadirUntuk = selectedOption.getAttribute('data-hadir-untuk');
    const employeeId = selectedOption.getAttribute('data-id');
    hadirUntukInput.value = hadirUntuk || '';

    if (!employeeId) return;

    try {
        const response = await fetch(`/attendance/check/${employeeId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();

        if (!data.canCheckIn) {
            // Jika sudah checkout, tampilkan pesan dan disable form
            showAlert('Peringatan', data.message, 'warning');
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('captureBtn').disabled = true;
            return;
        }

        if (data.checkedIn) {
            document.getElementById('alreadyCheckedInWarning').classList.remove('hidden');
            document.getElementById('submitBtnText').textContent = 'Check Out';
            // Selalu tampilkan field alasan checkout
            document.getElementById('earlyLeaveContainer').classList.remove('hidden');
            document.getElementById('earlyLeaveReason').setAttribute('required', 'required');
        } else {
            document.getElementById('alreadyCheckedInWarning').classList.add('hidden');
            document.getElementById('submitBtnText').textContent = 'Check In';
            document.getElementById('earlyLeaveContainer').classList.add('hidden');
            document.getElementById('earlyLeaveReason').removeAttribute('required');
            // Reset dan enable form controls
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('captureBtn').disabled = false;
        }

        if (data.message) {
            showAlert('Informasi', data.message, data.checkedIn ? 'info' : 'warning');
        }
    } catch (error) {
        console.error('Error checking attendance:', error);
        showAlert('Error', 'Terjadi kesalahan saat memeriksa status presensi', 'error');
    }
});

// Validasi form pada submit
form.addEventListener('submit', async function (e) {
    e.preventDefault();

    if (!photoTaken) {
        showAlert('Peringatan', 'Silakan ambil foto terlebih dahulu', 'warning');
        return;
    }

    if (isSubmitting) return;
    isSubmitting = true;

    const namaInput = document.getElementById('nama');
    const hadirUntukInput = document.getElementById('hadir_untuk');
    const lokasiInput = document.getElementById('lokasi');
    const tanggalInput = document.getElementById('tanggal');
    const jamInput = document.getElementById('jam');
    const earlyLeaveReason = document.getElementById('earlyLeaveReason');
    const submitBtnText = document.getElementById('submitBtnText');
    const isCheckOut = submitBtnText.textContent === 'Check Out';

    // Validasi
    if (!namaInput.value.trim()) {
        showAlert('Validasi', 'Mohon isi nama lengkap Anda', 'error');
        namaInput.focus();
        isSubmitting = false;
        return;
    }

    if (!hadirUntukInput.value.trim()) {
        showAlert('Validasi', 'Field hadir untuk tidak boleh kosong', 'error');
        hadirUntukInput.focus();
        isSubmitting = false;
        return;
    }

    if (isCheckOut && !earlyLeaveReason.value.trim()) {
        showAlert('Validasi', 'Mohon isi alasan checkout', 'error');
        earlyLeaveReason.focus();
        isSubmitting = false;
        return;
    }

    if (!canvas.classList.contains('hidden')) {
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> ${isCheckOut ? 'MENYIMPAN CHECK OUT...' : 'MENYIMPAN CHECK IN...'}`;

        try {
            const formData = new FormData();
            formData.append('nama', namaInput.value);
            formData.append('lokasi', lokasiInput.value);
            formData.append('hadir_untuk', hadirUntukInput.value);
            formData.append('tanggal', tanggalInput.value);
            formData.append('jam', jamInput.value);
            formData.append('is_checkout', isCheckOut ? 1 : 0);

            if (isCheckOut) {
                formData.append('early_leave_reason', earlyLeaveReason.value);
            }

            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
            formData.append('foto', blob, 'presensi_' + Date.now() + '.png');

            const response = await fetch('/attendance', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showAlert('Sukses', result.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('Error', error.message || 'Terjadi kesalahan saat menyimpan data', 'error');
        } finally {
            isSubmitting = false;
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    } else {
        showAlert('Validasi', 'Silakan ambil foto terlebih dahulu', 'warning');
    }
});

// Alert Modal Functions
function showAlert(title, message, type = 'info') {
    const alertModal = document.getElementById('alertModal');
    const alertModalContent = document.getElementById('alertModalContent');
    const alertTitle = document.getElementById('alertTitle');
    const alertMessage = document.getElementById('alertMessage');
    const alertIcon = document.getElementById('alertIcon');
    const closeAlertBtn = document.getElementById('closeAlertBtn');

    // Set content
    alertTitle.textContent = title;
    alertMessage.textContent = message;

    // Set icon and colors based on type
    let iconHtml = '';
    switch (type) {
        case 'success':
            iconHtml = '<i class="fas fa-check-circle text-green-600 text-xl"></i>';
            alertIcon.className = 'p-3 rounded-xl mr-4 bg-green-100';
            break;
        case 'error':
            iconHtml = '<i class="fas fa-exclamation-circle text-red-600 text-xl"></i>';
            alertIcon.className = 'p-3 rounded-xl mr-4 bg-red-100';
            break;
        case 'warning':
            iconHtml = '<i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>';
            alertIcon.className = 'p-3 rounded-xl mr-4 bg-yellow-100';
            break;
        default:
            iconHtml = '<i class="fas fa-info-circle text-blue-600 text-xl"></i>';
            alertIcon.className = 'p-3 rounded-xl mr-4 bg-blue-100';
    }
    alertIcon.innerHTML = iconHtml;

    // Show modal with animation
    alertModal.classList.remove('hidden');
    setTimeout(() => {
        alertModalContent.classList.add('scale-100', 'opacity-100');
        alertModalContent.classList.remove('scale-95', 'opacity-0');
    }, 10);

    // Handle close
    const closeModal = () => {
        alertModalContent.classList.remove('scale-100', 'opacity-100');
        alertModalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            alertModal.classList.add('hidden');
        }, 300);
    };

    closeAlertBtn.onclick = closeModal;
    alertModal.onclick = (e) => {
        if (e.target === alertModal) closeModal();
    };
}

// Replace all alerts with modal
function replaceAlert(message) {
    showAlert('Perhatian', message, 'warning');
}

// Update form validation to use new alert
form.addEventListener('submit', async function (e) {
    e.preventDefault();

    if (!photoTaken) {
        showAlert('Peringatan', 'Silakan ambil foto terlebih dahulu', 'warning');
        return;
    }

    if (isSubmitting) return;
    isSubmitting = true;

    const namaInput = document.getElementById('nama');
    const hadirUntukInput = document.getElementById('hadir_untuk');
    const lokasiInput = document.getElementById('lokasi');
    const tanggalInput = document.getElementById('tanggal');
    const jamInput = document.getElementById('jam');
    const earlyLeaveReason = document.getElementById('earlyLeaveReason');
    const submitBtnText = document.getElementById('submitBtnText');
    const isCheckOut = submitBtnText.textContent === 'Check Out';

    // Validasi
    if (!namaInput.value.trim()) {
        showAlert('Validasi', 'Mohon isi nama lengkap Anda', 'error');
        namaInput.focus();
        isSubmitting = false;
        return;
    }

    if (!hadirUntukInput.value.trim()) {
        showAlert('Validasi', 'Field hadir untuk tidak boleh kosong', 'error');
        hadirUntukInput.focus();
        isSubmitting = false;
        return;
    }

    if (isCheckOut && !earlyLeaveReason.value.trim()) {
        showAlert('Validasi', 'Mohon isi alasan pulang', 'error');
        earlyLeaveReason.focus();
        isSubmitting = false;
        return;
    }

    if (!canvas.classList.contains('hidden')) {
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> ${isCheckOut ? 'MENYIMPAN CHECK OUT...' : 'MENYIMPAN CHECK IN...'}`;

        try {
            const formData = new FormData();
            formData.append('nama', namaInput.value);
            formData.append('lokasi', lokasiInput.value);
            formData.append('hadir_untuk', hadirUntukInput.value);
            formData.append('tanggal', tanggalInput.value);
            formData.append('jam', jamInput.value);
            formData.append('is_checkout', isCheckOut ? 1 : 0);

            if (isCheckOut) {
                formData.append('early_leave_reason', earlyLeaveReason.value);
            }

            // Convert canvas to blob and append to formData
            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
            formData.append('foto', blob, 'presensi_' + Date.now() + '.png');

            const response = await fetch('/attendance', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
            });

            const result = await response.json();

            if (result.success) {
                showAlert('Sukses', result.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('Error', error.message || 'Terjadi kesalahan saat menyimpan data', 'error');
        } finally {
            isSubmitting = false;
            submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
        }
    } else {
        showAlert('Validasi', 'Silakan ambil foto terlebih dahulu', 'warning');
    }
});

// Notification Badge Update
function updateNotificationCount() {
    const countElement = document.querySelector('.notification-count');
    if (countElement) {
        fetch('/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    // Format count to show 99+ if greater than 99
                    const displayCount = data.count > 99 ? '99+' : data.count;
                    countElement.textContent = displayCount;
                    countElement.classList.remove('hidden');
                    // Adjust padding if showing 99+
                    if (data.count > 99) {
                        countElement.classList.add('px-2');
                    } else {
                        countElement.classList.remove('px-2');
                    }
                } else {
                    countElement.classList.add('hidden');
                }
            })
            .catch(error => console.error('Error fetching notification count:', error));
    }
}

// Update notification count every 30 seconds
if (document.querySelector('.notification-count')) {
    updateNotificationCount();
    setInterval(updateNotificationCount, 30000);
}

// Export functionality
document.addEventListener('DOMContentLoaded', function() {
    const exportBtn = document.getElementById('exportBtn');
    const dateFilter = document.getElementById('attendanceDateFilter');

    if (exportBtn && dateFilter) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get the selected date
            const selectedDate = dateFilter.value;
            
            // Build the export URL with date parameters
            let exportUrl = exportBtn.getAttribute('href');
            if (selectedDate) {
                exportUrl += `?start_date=${selectedDate}&end_date=${selectedDate}`;
            }
            
            // Navigate to the export URL
            window.location.href = exportUrl;
        });
    }
});
