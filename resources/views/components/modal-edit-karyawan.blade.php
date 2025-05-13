<div id="editEmployeeModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 transform transition-all duration-300 scale-95 opacity-0"
        id="editEmployeeModalContent">
        <div class="p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6">Edit Karyawan</h3>
            <form id="editEmployeeForm" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="editEmployeeId" name="id">
                <div>
                    <label for="editEmployeeNama" class="block text-base font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" id="editEmployeeNama" name="nama" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <label for="editEmployeeMengajar"
                        class="block text-base font-medium text-gray-700 mb-1">Hadir Untuk</label>
                    <input type="text" id="editEmployeeMengajar" name="hadir_untuk" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" id="closeEditEmployeeModalBtnSecondary"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>