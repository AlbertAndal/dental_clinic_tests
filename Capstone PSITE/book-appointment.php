<?php
require_once 'includes/config.php';
require_once 'includes/session_check.php';

// Ensure user is logged in
requireLogin();

// Get available services
$stmt = $conn->query("SELECT * FROM services ORDER BY name");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="card-title mb-4">Book an Appointment</h3>

                    <form id="appointmentForm" class="needs-validation" novalidate>
                        <!-- Step 1: Service Selection -->
                        <div class="booking-step" id="step1">
                            <h5 class="mb-4">Step 1: Select Service</h5>
                            <div class="row g-4">
                                <?php foreach ($services as $service): ?>
                                    <div class="col-md-6">
                                        <div class="service-card card h-100 border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" 
                                                           name="service_id" 
                                                           value="<?php echo $service['id']; ?>"
                                                           data-duration="<?php echo $service['duration']; ?>"
                                                           data-price="<?php echo $service['price']; ?>"
                                                           id="service_<?php echo $service['id']; ?>" required>
                                                    <label class="form-check-label" for="service_<?php echo $service['id']; ?>">
                                                        <h5 class="mb-2"><?php echo htmlspecialchars($service['name']); ?></h5>
                                                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($service['description']); ?></p>
                                                        <div class="d-flex justify-content-between">
                                                            <span class="badge bg-primary">
                                                                <i class="far fa-clock me-1"></i>
                                                                <?php echo $service['duration']; ?> mins
                                                            </span>
                                                            <span class="text-primary fw-bold">
                                                                $<?php echo number_format($service['price'], 2); ?>
                                                            </span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="button" class="btn btn-primary" onclick="nextStep(2)">Continue</button>
                            </div>
                        </div>

                        <!-- Step 2: Date Selection -->
                        <div class="booking-step d-none" id="step2">
                            <h5 class="mb-4">Step 2: Select Date</h5>
                            <div class="mb-4">
                                <label class="form-label">Appointment Date</label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                                <div class="form-text">Choose a date within the next 30 days</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">Back</button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(3)">Continue</button>
                            </div>
                        </div>

                        <!-- Step 3: Time Slot Selection -->
                        <div class="booking-step d-none" id="step3">
                            <h5 class="mb-4">Step 3: Select Time Slot</h5>
                            <div id="timeSlots" class="row g-3 mb-4">
                                <!-- Time slots will be loaded dynamically -->
                            </div>
                            <input type="hidden" id="appointment_time" name="appointment_time" required>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">Back</button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(4)">Continue</button>
                            </div>
                        </div>

                        <!-- Step 4: Confirmation -->
                        <div class="booking-step d-none" id="step4">
                            <h5 class="mb-4">Step 4: Confirm Booking</h5>
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-muted">Appointment Details</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="small text-muted d-block">Service</label>
                                            <span id="confirm_service" class="fw-bold"></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="small text-muted d-block">Duration</label>
                                            <span id="confirm_duration" class="fw-bold"></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="small text-muted d-block">Date</label>
                                            <span id="confirm_date" class="fw-bold"></span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="small text-muted d-block">Time</label>
                                            <span id="confirm_time" class="fw-bold"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small text-muted d-block">Price</label>
                                            <span id="confirm_price" class="fw-bold text-primary"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Additional Notes</label>
                                <textarea class="form-control" name="notes" rows="3" 
                                          placeholder="Any special requirements or concerns..."></textarea>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="prevStep(3)">Back</button>
                                <button type="submit" class="btn btn-primary">Confirm Booking</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.service-card {
    cursor: pointer;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.service-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}

.service-card .form-check-input {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

.time-slot {
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}

.time-slot:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1) !important;
}

.time-slot.selected {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.time-slot.unavailable {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

<script>
let currentStep = 1;
const timeSlotDuration = 30; // minutes
const startTime = '08:00';
const endTime = '17:00';

function nextStep(step) {
    // Validate current step
    if (!validateStep(currentStep)) return;
    
    document.getElementById(`step${currentStep}`).classList.add('d-none');
    document.getElementById(`step${step}`).classList.remove('d-none');
    currentStep = step;

    if (step === 3) {
        loadTimeSlots();
    } else if (step === 4) {
        updateConfirmation();
    }
}

function prevStep(step) {
    document.getElementById(`step${currentStep}`).classList.add('d-none');
    document.getElementById(`step${step}`).classList.remove('d-none');
    currentStep = step;
}

function validateStep(step) {
    switch(step) {
        case 1:
            const service = document.querySelector('input[name="service_id"]:checked');
            if (!service) {
                alert('Please select a service');
                return false;
            }
            return true;

        case 2:
            const date = document.getElementById('appointment_date').value;
            if (!date) {
                alert('Please select a date');
                return false;
            }
            return true;

        case 3:
            const time = document.getElementById('appointment_time').value;
            if (!time) {
                alert('Please select a time slot');
                return false;
            }
            return true;

        default:
            return true;
    }
}

function loadTimeSlots() {
    const date = document.getElementById('appointment_date').value;
    const service = document.querySelector('input[name="service_id"]:checked');
    const duration = parseInt(service.dataset.duration);

    // Fetch booked slots from server
    fetch(`get_booked_slots.php?date=${date}`)
        .then(response => response.json())
        .then(bookedSlots => {
            const timeSlotsContainer = document.getElementById('timeSlots');
            timeSlotsContainer.innerHTML = '';

            const slots = generateTimeSlots(startTime, endTime, timeSlotDuration, duration, bookedSlots);
            
            slots.forEach(slot => {
                const slotElement = document.createElement('div');
                slotElement.className = 'col-md-4';
                slotElement.innerHTML = `
                    <div class="time-slot card border-0 shadow-sm text-center p-3 ${slot.available ? '' : 'unavailable'}"
                         data-time="${slot.time}" onclick="selectTimeSlot(this)">
                        <h6 class="mb-0">${formatTime(slot.time)}</h6>
                    </div>
                `;
                timeSlotsContainer.appendChild(slotElement);
            });
        });
}

function generateTimeSlots(start, end, interval, duration, bookedSlots) {
    const slots = [];
    let current = new Date(`2000-01-01 ${start}`);
    const endTime = new Date(`2000-01-01 ${end}`);

    while (current < endTime) {
        const timeString = current.toTimeString().slice(0, 5);
        const endTimeString = new Date(current.getTime() + duration * 60000).toTimeString().slice(0, 5);
        
        // Check if slot is available
        const isAvailable = !isSlotBooked(timeString, endTimeString, bookedSlots);
        
        slots.push({
            time: timeString,
            available: isAvailable
        });

        current = new Date(current.getTime() + interval * 60000);
    }

    return slots;
}

function isSlotBooked(start, end, bookedSlots) {
    return bookedSlots.some(slot => {
        const slotStart = slot.start_time;
        const slotEnd = slot.end_time;
        return (start >= slotStart && start < slotEnd) || 
               (end > slotStart && end <= slotEnd) ||
               (start <= slotStart && end >= slotEnd);
    });
}

function selectTimeSlot(element) {
    if (element.classList.contains('unavailable')) return;

    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected');
    });
    element.classList.add('selected');
    document.getElementById('appointment_time').value = element.dataset.time;
}

function formatTime(time) {
    const [hours, minutes] = time.split(':');
    const period = hours >= 12 ? 'PM' : 'AM';
    const hour = hours % 12 || 12;
    return `${hour}:${minutes} ${period}`;
}

function updateConfirmation() {
    const service = document.querySelector('input[name="service_id"]:checked');
    const serviceName = service.closest('.service-card').querySelector('h5').textContent;
    const duration = service.dataset.duration;
    const price = service.dataset.price;
    const date = document.getElementById('appointment_date').value;
    const time = document.getElementById('appointment_time').value;

    document.getElementById('confirm_service').textContent = serviceName;
    document.getElementById('confirm_duration').textContent = `${duration} minutes`;
    document.getElementById('confirm_date').textContent = new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    document.getElementById('confirm_time').textContent = formatTime(time);
    document.getElementById('confirm_price').textContent = `$${parseFloat(price).toFixed(2)}`;
}

// Initialize date input constraints
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('appointment_date');
    const today = new Date();
    const maxDate = new Date();
    maxDate.setDate(today.getDate() + 30);
    
    dateInput.min = today.toISOString().split('T')[0];
    dateInput.max = maxDate.toISOString().split('T')[0];
});

// Form submission
document.getElementById('appointmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('save_appointment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'profile.php?booking=success';
        } else {
            alert(data.error || 'Failed to book appointment');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
