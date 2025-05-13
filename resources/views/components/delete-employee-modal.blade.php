<div id="deleteEmployeeModal"
    class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-in-out">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 ease-out scale-95 opacity-0"
        id="deleteEmployeeModalContent">
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="bg-red-100 p-3 rounded-xl mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 text-red-600">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">Konfirmasi Hapus</h3>
            </div>
            <p class="text-base text-gray-600 mb-6">Apakah Anda yakin ingin menghapus karyawan <span
                    id="deleteEmployeeName" class="font-medium"></span>?</p>
            <form id="deleteEmployeeForm" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" id="deleteEmployeeId">
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelDeleteEmployeeBtn"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-trash mr-2"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>