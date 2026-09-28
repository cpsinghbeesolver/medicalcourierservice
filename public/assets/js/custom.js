function confirmDecline(url) {
    if (confirm('Are you sure you want to decline this enquiry?')) {
        window.location.href = url;
    }
}
function confirmGenerate(url) {
    if (confirm('Are you sure you want to generate credentials for this enquiry?')) {
        window.location.href = url;
    }
}
function confirmDeleteSpecimenType(id) {
    if (confirm('Are you sure you want to delete this specimen type?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
function confirmDeleteTemperatureRequirement(id) {
    if (confirm('Are you sure you want to delete this temperature requirement?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
function confirmDeleteVehicleRequirement(id) {
    if (confirm('Are you sure you want to delete this vehicle requirement?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
$(document).ready(function() {
    const today = new Date();

    const maxDate = new Date(
        today.getFullYear() - 18,
        today.getMonth(),
        today.getDate()
    );

    const minDate = new Date(
        today.getFullYear() - 80,
        today.getMonth(),
        today.getDate()
    );

    flatpickr("#date_of_birth", {
        dateFormat: "Y-m-d",
        minDate: minDate,
        maxDate: maxDate
    });
    flatpickr("#license_expiry_date", {
        minDate: new Date(),
    });
    flatpickr("#insurance_expiry", {
        minDate: new Date(),
    });
    flatpickr("#hipaa_cert_date", {
        dateFormat: "Y-m-d",
    });
    
    flatpickr("#drug_screen_expiry", {
        dateFormat: "Y-m-d",
        minDate: new Date(),
    });
    
    flatpickr("#timeWindowEnd", {
        enableTime: true,
        dateFormat: "Y-m-d H:i:s",
        time_24hr: false,
        minuteIncrement: 1,
        allowInput: false,
        minDate: new Date(),
    });
    flatpickr("#timeWindowStart", {
        enableTime: true,
        dateFormat: "Y-m-d H:i:s",
        time_24hr: false,
        minuteIncrement: 1,
        allowInput: false,
        minDate: new Date(),
    });
    const fp = flatpickr("#dateTime", {
        enableTime: true,
        dateFormat: "Y-m-d H:i:s",
        time_24hr: false,
        minuteIncrement: 1,
        allowInput: false,
        // maxDate: new Date()
    });
    
    // document.getElementById("clearButton").addEventListener("click", function() {
    //     fp.clear(); 
    // });
    // flatpickr("#date_of_birth", { 
    //     dateFormat: "Y-m-d", 
    //     maxDate: "today"
    // }); 
    flatpickr("#hipaa_cert_date", {
        dateFormat: "Y-m-d",
    }); 
    flatpickr("#specimen_handling_cert", {
        dateFormat: "Y-m-d",
    });
    flatpickr("#bloodborne_training_date", {
        dateFormat: "Y-m-d",
    });
    flatpickr("#dob", {
        dateFormat: "Y-m-d",
        maxDate: "today"
    });
    flatpickr("#hipaa_certification_date", {
        dateFormat: "Y-m-d",
    });
    flatpickr("#specimen_handling_certification_date", {
        dateFormat: "Y-m-d",
    });
    
    
});

/* ==== Responsive sidebar drawer (self-injecting; no layout edits required) ==== */
document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.querySelector('.sidebar');
    var header = document.querySelector('.top-header');
    if (!sidebar || !header || document.getElementById('sidebarToggle')) return;
    var body = document.body;

    var backdrop = document.createElement('div');
    backdrop.className = 'sidebar-backdrop';
    backdrop.id = 'sidebarBackdrop';
    sidebar.parentNode.insertBefore(backdrop, sidebar);

    var toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'sidebar-toggle';
    toggle.id = 'sidebarToggle';
    toggle.setAttribute('aria-label', 'Toggle navigation menu');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.innerHTML = '<i class="fas fa-bars"></i>';
    header.insertBefore(toggle, header.firstChild);

    function setOpen(open) {
        body.classList.toggle('sidebar-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    toggle.addEventListener('click', function (e) { e.stopPropagation(); setOpen(!body.classList.contains('sidebar-open')); });
    backdrop.addEventListener('click', function () { setOpen(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
    sidebar.addEventListener('click', function (e) {
        var a = e.target.closest('a');
        if (a && !a.classList.contains('submenu-toggle')) setOpen(false);
    });
    window.addEventListener('resize', function () { if (window.innerWidth >= 992) setOpen(false); });
});