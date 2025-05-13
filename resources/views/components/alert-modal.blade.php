<div id="alertModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-in-out">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all duration-300 ease-out scale-95 opacity-0" id="alertModalContent">
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div id="alertIcon" class="p-3 rounded-xl mr-4">
                    <!-- Icon will be injected by JavaScript -->
                </div>
                <h3 id="alertTitle" class="text-xl font-semibold text-gray-800"><!-- Title will be injected --></h3>
            </div>
            <p id="alertMessage" class="text-base text-gray-600 mb-6"><!-- Message will be injected --></p>
            <div class="flex justify-end">
                <button type="button" id="closeAlertBtn" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    OK
                </button>
            </div>
        </div>
    </div>
</div> 