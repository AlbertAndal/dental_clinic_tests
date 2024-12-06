document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Appointment date validation
    const appointmentDate = document.getElementById('appointment_date');
    if (appointmentDate) {
        const today = new Date().toISOString().split('T')[0];
        appointmentDate.setAttribute('min', today);
        
        appointmentDate.addEventListener('change', function() {
            const selected = new Date(this.value);
            const day = selected.getDay();
            
            // Disable weekends (0 = Sunday, 6 = Saturday)
            if (day === 0 || day === 6) {
                alert('Please select a weekday for your appointment.');
                this.value = '';
            }
        });
    }

    // Service selection handler
    const serviceSelect = document.getElementById('service_id');
    const durationDisplay = document.getElementById('duration_display');
    const priceDisplay = document.getElementById('price_display');
    
    if (serviceSelect && durationDisplay && priceDisplay) {
        serviceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const duration = selectedOption.getAttribute('data-duration');
            const price = selectedOption.getAttribute('data-price');
            
            durationDisplay.textContent = `Duration: ${duration} minutes`;
            priceDisplay.textContent = `Price: $${price}`;
        });
    }

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Appointment time slot selection
    const timeSlots = document.querySelectorAll('.time-slot');
    timeSlots.forEach(slot => {
        slot.addEventListener('click', function() {
            timeSlots.forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('appointment_time').value = this.dataset.time;
        });
    });
});
