<div id="detailModal"
    class="fixed inset-0 bg-black bg-opacity-35 backdrop-blur-sm flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-in-out p-6">
    <div class="bg-white rounded-xl shadow-xl w-full sm:max-w-4xl mx-0 sm:mx-4 max-h-[85vh] sm:max-h-[90vh] overflow-y-auto modal-scroll transform transition-all duration-300 ease-out scale-95 opacity-0"
        id="detailModalContent">
        <!-- Header with Close Button -->
        <div class="sticky top-0 z-10 bg-white border-b border-gray-100">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center">
                    <div class="bg-indigo-50 p-2 sm:p-3 rounded-xl mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-indigo-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Detail Presensi</h3>
                </div>
                <button id="closeModalBtn"
                    class="p-2 rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Content -->
        <div class="p-4">
            <!-- Content Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Check-in Section -->
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-4 sm:p-5 rounded-xl">
                    <h4 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Check In</h4>
                    <div class="relative rounded-xl overflow-hidden mb-3 sm:mb-4">
                        <img id="detailFotoCheckIn" src="" alt="Foto Check In"
                            class="w-full h-48 sm:h-56 object-cover bg-gray-200">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        <div class="bg-white/90 p-3 sm:p-4 rounded-lg">
                            <label class="block text-sm text-gray-600 mb-1">Waktu Check In</label>
                            <p id="detailCheckInTime" class="text-gray-900 font-medium">-</p>
                        </div>
                        <div class="bg-white/90 p-3 sm:p-4 rounded-lg">
                            <label class="block text-sm text-gray-600 mb-1">Status</label>
                            <p id="detailCheckInStatus" class="text-base sm:text-lg text-green-700 font-semibold">-</p>
                        </div>
                    </div>
                </div>

                <!-- Check-out Section -->
                <div id="checkoutInfo"
                    class="bg-gradient-to-br from-indigo-50 to-blue-50 p-4 sm:p-5 rounded-xl hidden">
                    <h4 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Check Out</h4>
                    <div id="checkoutPhotoContainer" class="relative rounded-xl overflow-hidden mb-3 sm:mb-4">
                        <img id="detailFotoCheckOut" src="" alt="Foto Check Out"
                            class="w-full h-48 sm:h-56 object-cover bg-gray-200">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        <div class="bg-white/90 p-3 sm:p-4 rounded-lg">
                            <label class="block text-sm text-gray-600 mb-1">Waktu Check Out</label>
                            <p id="detailCheckOutTime" class="text-gray-900 font-medium">-</p>
                        </div>
                        <div class="bg-white/90 p-3 sm:p-4 rounded-lg">
                            <label class="block text-sm text-gray-600 mb-1">Durasi Kerja</label>
                            <p id="detailWorkHours" class="text-gray-900 font-medium">-</p>
                        </div>
                    </div>
                    <div id="earlyLeaveInfo" class="mt-3 sm:mt-4 bg-yellow-50 p-3 sm:p-4 rounded-lg hidden">
                        <label class="block text-sm text-yellow-600 mb-1">Alasan Pulang Awal</label>
                        <p id="detailEarlyLeaveReason" class="text-gray-900 font-medium">-</p>
                    </div>
                </div>

                <!-- Employee Info Section -->
                <div class="col-span-1 lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    <div class="bg-gray-100 p-3 sm:p-4 rounded-xl">
                        <label class="block text-sm text-gray-600 mb-1 uppercase tracking-wide">Nama</label>
                        <p id="detailNama" class="text-gray-900 font-medium">-</p>
                    </div>
                    <div class="bg-gray-100 p-3 sm:p-4 rounded-xl">
                        <label class="block text-sm text-gray-600 mb-1 uppercase tracking-wide">Hadir Untuk</label>
                        <p id="detailHadirUntuk" class="text-gray-900 font-medium">-</p>
                    </div>
                    <div class="bg-gray-100 p-3 sm:p-4 rounded-xl">
                        <label class="block text-sm text-gray-600 mb-1 uppercase tracking-wide">Lokasi</label>
                        <p id="detailLokasi" class="text-gray-900 font-medium">-</p>
                    </div>
                    <div class="bg-gray-100 p-3 sm:p-4 rounded-xl">
                        <label class="block text-sm text-gray-600 mb-1 uppercase tracking-wide">Tanggal</label>
                        <p id="detailTanggal" class="text-gray-900 font-medium">-</p>
                    </div>
                    <div class="bg-gray-100 p-3 sm:p-4 rounded-xl">
                        <label class="block text-sm text-gray-600 mb-1 uppercase tracking-wide">Persentase
                            Kehadiran</label>
                        <p id="detailAttendancePercentage" class="text-gray-900 font-medium">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Hide scrollbar but keep functionality */
        .modal-scroll {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        
        .modal-scroll::-webkit-scrollbar {
            display: none;  /* Chrome, Safari and Opera */
        }
    </style>
</div>
