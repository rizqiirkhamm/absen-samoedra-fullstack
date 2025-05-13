<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    <title>Absensi Digital | Rumah Samoedra</title>
    <link rel="icon" href="{{ asset('assets/samoedra-fav-icon.png') }}" type="image/png">
    <title>Absensi Digital | Samoedra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fredericka+the+Great&family=Fredoka:wght@300..700&family=Fuzzy+Bubbles:wght@400;700&family=Onest:wght@100..900&display=swap"
        rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>

<body class="bg-gradient-to-br from-teal-50 via-blue-50 to-indigo-50 min-h-screen" style="font-family: 'Fredoka', sans-serif;">
    <div class="container mx-auto px-4 py-8">
        <!-- Card utama dengan efek glassmorphism -->
        <div class="max-w-xl mx-auto bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl overflow-hidden border border-white/20">
            <!-- Header card dengan gradient -->
            <div class="bg-gradient-to-r from-[#3E5467] to-[#7BA5B0] px-6 py-4">
                <h2 class="text-2xl font-semibold text-white">Formulir Presensi</h2>
                <p class="text-blue-100 text-sm">Silakan lengkapi data presensi Anda</p>
            </div>

            <!-- Body card -->
            <div class="p-6 space-y-6">
                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                <form id="attendanceForm" class="space-y-6">
                    <!-- Input Nama (Dropdown) dengan ikon dan efek hover -->
                    <div class="group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Karyawan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-user"></i>
                            </div>
                            <select id="nama" name="nama" required
                                class="pl-10 w-full h-12 rounded-xl border-gray-200 shadow-sm focus:border-[#7BA5B0] focus:ring-[#7BA5B0] bg-white">
                                <option value="" disabled selected>Pilih karyawan</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->nama }}" data-hadir-untuk="{{ $employee->hadir_untuk }}" data-id="{{ $employee->id }}">
                                    {{ $employee->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="alreadyCheckedInWarning" class="hidden mt-2 text-yellow-600 text-sm bg-yellow-50 p-3 rounded-lg">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <span>Anda sudah melakukan check-in hari ini. Apakah Anda ingin melakukan check-out?</span>
                        </div>
                    </div>

                    <!-- Input Hadir Untuk (Autofill) dengan ikon -->
                    <div class="group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hadir Untuk</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <input type="text" id="hadir_untuk" name="hadir_untuk" readonly
                                class="pl-10 w-full h-12 rounded-xl border-gray-200 shadow-sm bg-white">
                        </div>
                    </div>

                    <!-- Foto Selfie dengan preview yang lebih besar -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Selfie</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center bg-white/50 backdrop-blur-sm transition hover:border-[#7BA5B0]">
                            <div class="relative w-full max-w-md mx-auto">
                                <div class="relative w-full" style="padding-bottom: 75%;">
                                    <video id="video" class="absolute inset-0 w-full h-full object-cover rounded-xl shadow-lg" autoplay></video>
                                    <canvas id="canvas" class="absolute inset-0 w-full h-full object-cover rounded-xl shadow-lg hidden"></canvas>
                                    <div id="flash" class="flash-effect"></div>
                                    <div id="countdown" class="countdown"></div>
                                </div>

                                <button type="button" id="captureBtn"
                                    class="mt-4 inline-flex items-center px-6 py-3 hover:bg-[#7BA5B0] border border-transparent rounded-xl font-medium text-white bg-[#3E5467] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#7BA5B0] transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-camera mr-2"></i>
                                    <span id="captureBtnText">Ambil Foto</span>
                                </button>
                            </div>
                            <p class="text-sm text-gray-500 mt-3">Pastikan wajah terlihat jelas dalam frame</p>
                        </div>
                    </div>

                    <!-- Informasi Presensi dalam grid yang lebih menarik -->
                    <div class="space-y-4">
                        <!-- Lokasi in its own row -->
                        <div class="group w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <input type="text" id="lokasi" name="lokasi" readonly
                                    class="pl-10 w-full h-12 rounded-xl border-gray-200 shadow-sm bg-white">
                            </div>
                        </div>

                        <!-- Date and Time in a row with 2 columns -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="far fa-calendar"></i>
                                    </div>
                                    <input type="text" id="tanggal" name="tanggal" readonly
                                        class="pl-10 w-full h-12 rounded-xl border-gray-200 shadow-sm bg-white">
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jam</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="far fa-clock"></i>
                                    </div>
                                    <input type="text" id="jam" name="jam" readonly
                                        class="pl-10 w-full h-12 rounded-xl border-gray-200 shadow-sm bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Early Leave Reason dengan styling yang lebih baik -->
                    <div id="earlyLeaveContainer" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Check Out Awal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-comment-alt"></i>
                            </div>
                            <textarea id="earlyLeaveReason" name="early_leave_reason" rows="3"
                                class="pl-10 w-full rounded-xl border-gray-200 shadow-sm focus:border-[#7BA5B0] focus:ring-[#7BA5B0] resize-none"
                                placeholder="Jelaskan alasan Anda Check Out sebelum jam 5"></textarea>
                        </div>
                    </div>

                    <!-- Tombol Submit dengan efek hover yang lebih menarik -->
                    <div class="pt-4">
                        <button type="submit" id="submitBtn"
                            class="w-full flex justify-center items-center h-12 border border-transparent rounded-xl text-lg font-medium text-white bg-gradient-to-r from-[#3E5467] to-[#7BA5B0] hover:from-[#7BA5B0] hover:to-[#3E5467] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#7BA5B0] transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl">
                            <i class="fas fa-paper-plane mr-2"></i>
                            <span id="submitBtnText">Check In</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer card dengan efek glassmorphism -->
            <div class="bg-gray-50/80 backdrop-blur-sm px-6 py-4 border-t border-gray-200/50">
                <p class="text-sm text-gray-500 text-center flex items-center justify-center">
                    <i class="fas fa-shield-alt mr-2 text-[#7BA5B0]"></i>
                    Data presensi Anda aman dan terenkripsi
                </p>
            </div>
        </div>
    </div>

    @include('components.alert-modal')
    <script src="{{ asset('script.js') }}"></script>

    <style>
        /* Animasi blob background */
        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
    </style>
</body>
