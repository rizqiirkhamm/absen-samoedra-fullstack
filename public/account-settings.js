document.addEventListener('DOMContentLoaded', function () {
    // =============================================
    // SIDEBAR TOGGLE FUNCTIONALITY
    // =============================================
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebarBackdrop = document.getElementById('sidebar-backdrop');

    // Toggle sidebar function
    const toggleSidebarFn = () => {
        sidebar.classList.toggle('open');
        sidebarBackdrop.classList.toggle('active');

        // Untuk desktop, tetap geser konten
        if (window.innerWidth >= 768) {
            mainContent.classList.toggle('ml-64');
        }
    };

    // Sidebar toggle button
    if (toggleSidebar) {
        toggleSidebar.addEventListener('click', toggleSidebarFn);
    }

    // Close sidebar when clicking on backdrop
    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', toggleSidebarFn);
    }

    // Responsive behavior
    function handleResize() {
        if (window.innerWidth >= 768) {
            // Desktop - sidebar selalu terbuka
            sidebar.classList.add('open');
            sidebarBackdrop.classList.remove('active');
            mainContent.classList.add('ml-64');
        } else {
            // Mobile - sidebar default tertutup
            sidebar.classList.remove('open');
            sidebarBackdrop.classList.remove('active');
            mainContent.classList.remove('ml-64');
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize(); // Initialize

    // =============================================
    // FORM VALIDATION
    // =============================================
    const updateNameForm = document.getElementById('updateNameForm');
    const updateEmailForm = document.getElementById('updateEmailForm');
    const updatePasswordForm = document.getElementById('updatePasswordForm');

    if (updateNameForm) {
        updateNameForm.addEventListener('submit', function (e) {
            const newName = document.getElementById('new_name').value;
            
            if (newName.length < 3) {
                e.preventDefault();
                alert('Nama minimal harus 3 karakter!');
            }
        });
    }

    if (updateEmailForm) {
        updateEmailForm.addEventListener('submit', function (e) {
            const newEmail = document.getElementById('new_email').value;
            const confirmEmail = document.getElementById('new_email_confirmation').value;

            if (newEmail !== confirmEmail) {
                e.preventDefault();
                alert('Email baru dan konfirmasi email tidak cocok!');
            }
        });
    }

    if (updatePasswordForm) {
        updatePasswordForm.addEventListener('submit', function (e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('new_password_confirmation').value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Password baru dan konfirmasi password tidak cocok!');
            }
        });
    }

    // =============================================
    // NOTIFICATION BADGE UPDATE
    // =============================================
    function updateNotificationCount() {
        const countElement = document.querySelector('.notification-count');
        if (countElement) {
            fetch('/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    if (data.count > 0) {
                        countElement.textContent = data.count;
                        countElement.classList.remove('hidden');
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
}); 