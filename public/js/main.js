document.addEventListener('DOMContentLoaded', function() {
    var sslSwitchInputs = document.querySelectorAll('.form-check-input');
    for (var i = 0; i < sslSwitchInputs.length; i++) {
        sslSwitchInputs[i].addEventListener('change', function() {
            var hiddenInput = this.closest('form').querySelector('input[type="hidden"][name="' + this.getAttribute('data-for') + '"]');
            if (hiddenInput) {
                hiddenInput.value = this.checked ? '1' : '0';
            }
        });
        // url içindeki parametreye göre switch durumu
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has(sslSwitchInputs[i].name)) {
            sslSwitchInputs[i].checked = urlParams.get(sslSwitchInputs[i].name) === '1';
        }
    }
});


function toggleNav() {
    const dropdown = document.getElementById('navDropdown');
    const toggle = document.querySelector('.nav-toggle');

    dropdown.classList.toggle('show');
    toggle.classList.toggle('active');

    // Close dropdown when clicking outside
    if (dropdown.classList.contains('show')) {
        document.addEventListener('click', closeNavOnOutsideClick);
    } else {
        document.removeEventListener('click', closeNavOnOutsideClick);
    }
}

function closeNavOnOutsideClick(event) {
    const navMenu = document.querySelector('.nav-menu');
    if (!navMenu.contains(event.target)) {
        document.getElementById('navDropdown').classList.remove('show');
        document.querySelector('.nav-toggle').classList.remove('active');
        document.removeEventListener('click', closeNavOnOutsideClick);
    }
}

// Close dropdown on ESC key
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        document.getElementById('navDropdown').classList.remove('show');
        document.querySelector('.nav-toggle').classList.remove('active');
    }
});


// Mobile Sidebar Toggle
function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    sidebar.classList.toggle('mobile-open');
    overlay.classList.toggle('show');
}

function closeMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('show');
}

// Close sidebar on ESC key
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeMobileSidebar();
    }
});
