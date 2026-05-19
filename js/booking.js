// ===================================
// SUDAMA CLINIC - Booking Page Logic
// Handle appointment booking flow
// Connected to PHP Backend API
// ===================================

// Booking state
let currentBookingStep = 1;
let selectedDoctor = null;
let selectedDate = null;
let selectedTime = null;
let doctorsList = [];

// Initialize booking page
document.addEventListener('DOMContentLoaded', async function () {
    // Check authentication
    const auth = Auth.checkAuth();
    if (!auth.isAuthenticated) {
        window.location.href = 'patient-login.html';
        return;
    }

    // Load user info
    const currentUser = auth.user;
    if (currentUser) {
        const firstName = currentUser.firstName || currentUser.name?.split(' ')[0] || '';
        const lastName = currentUser.lastName || currentUser.name?.split(' ').slice(1).join(' ') || '';
        const initials = (firstName[0] + (lastName[0] || '')).toUpperCase();

        const userName = document.getElementById('userName');
        const sidebarUserName = document.getElementById('sidebarUserName');
        const userAvatar = document.getElementById('userAvatar');

        if (userName) userName.textContent = currentUser.name || `${firstName} ${lastName}`;
        if (sidebarUserName) sidebarUserName.textContent = currentUser.name || `${firstName} ${lastName}`;
        if (userAvatar) userAvatar.textContent = initials;
    }

    // Load doctors from API
    await loadDoctors();

    // Load specializations for filter
    await loadSpecializations();

    // Set min date to today
    const today = new Date().toISOString().split('T')[0];
    const appointmentDateField = document.getElementById('appointmentDate');
    if (appointmentDateField) {
        appointmentDateField.min = today;
    }
});

// Load specializations for dropdown
async function loadSpecializations() {
    try {
        const response = await API.doctors.getSpecializations();
        if (response.success && response.data) {
            const select = document.getElementById('specialty');
            if (select) {
                response.data.forEach(spec => {
                    const option = document.createElement('option');
                    option.value = spec.name;
                    option.textContent = spec.name;
                    select.appendChild(option);
                });
            }
        }
    } catch (error) {
        console.error('Failed to load specializations:', error);
    }
}

// Load doctors from PHP API
async function loadDoctors(filterSpecialty = '') {
    try {
        const params = { status: 'active' };
        if (filterSpecialty) {
            params.specialization = filterSpecialty;
        }

        const response = await API.doctors.getList(params);

        if (response.success && response.data) {
            doctorsList = response.data;
            renderDoctors(doctorsList);
        } else {
            throw new Error('Failed to load doctors');
        }
    } catch (error) {
        console.error('Error loading doctors:', error);
        SmartClinic.showNotification('Failed to load doctors. Please refresh the page.', 'error');

        const grid = document.getElementById('doctorsGrid');
        if (grid) {
            grid.innerHTML = '<div class="text-center" style="padding: 2rem; color: var(--color-text-muted);">Failed to load doctors. Please try again.</div>';
        }
    }
}

// Render doctors grid
function renderDoctors(doctors) {
    const grid = document.getElementById('doctorsGrid');
    if (!grid) return;

    if (doctors.length === 0) {
        grid.innerHTML = '<div class="text-center" style="padding: 2rem; color: var(--color-text-muted);">No doctors found for the selected specialty.</div>';
        return;
    }

    grid.innerHTML = '';
    doctors.forEach(doctor => {
        const card = document.createElement('div');
        card.className = 'doctor-card';
        card.dataset.doctorId = doctor.id;

        // Create initials for avatar
        const initials = doctor.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

        card.innerHTML = `
            <div class="doctor-avatar">${doctor.image || initials}</div>
            <div class="doctor-name">${doctor.name}</div>
            <div class="doctor-specialty">${doctor.specialization || 'General'}</div>
            <div class="doctor-meta">
                <span>
                    <strong>${doctor.experience || doctor.experience_years + ' years'}</strong>
                    <small>Experience</small>
                </span>

            </div>
            <div class="doctor-charge">Consultation: ₹${doctor.consultation_charge || doctor.new_case_charge}</div>
        `;

        card.addEventListener('click', () => selectDoctor(doctor, card));
        grid.appendChild(card);
    });
}

// Filter doctors by specialty
function filterDoctors() {
    const specialty = document.getElementById('specialty')?.value || '';
    loadDoctors(specialty);
    selectedDoctor = null;
    const selectBtn = document.getElementById('selectDoctorBtn');
    if (selectBtn) selectBtn.disabled = true;
}

// Select a doctor
function selectDoctor(doctor, cardElement) {
    selectedDoctor = doctor;

    // Update UI
    document.querySelectorAll('.doctor-card').forEach(card => {
        card.classList.remove('selected');
    });
    cardElement.classList.add('selected');

    const selectBtn = document.getElementById('selectDoctorBtn');
    if (selectBtn) selectBtn.disabled = false;
}

// Navigate between booking steps
function goToStep(step) {
    if (step === 2 && !selectedDoctor) {
        SmartClinic.showNotification('Please select a doctor first', 'warning');
        return;
    }

    if (step === 3 && (!selectedDate || !selectedTime)) {
        SmartClinic.showNotification('Please select date and time first', 'warning');
        return;
    }

    currentBookingStep = step;
    updateStepDisplay();

    // Update selected doctor info in step 2
    if (step === 2 && selectedDoctor) {
        const info = document.getElementById('selectedDoctorInfo');
        if (info) {
            const initials = selectedDoctor.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
            info.innerHTML = `
                <div class="doctor-avatar">${selectedDoctor.image || initials}</div>
                <div class="selected-doctor-details">
                    <h4>${selectedDoctor.name}</h4>
                    <p>${selectedDoctor.specialization || 'General Medicine'}</p>
                    <p>${selectedDoctor.experience || selectedDoctor.experience_years + ' years'} experience</p>
                    <p><strong>Consultation: ₹${selectedDoctor.consultation_charge || selectedDoctor.new_case_charge}</strong></p>
                </div>
            `;
        }
    }

    // Update summary in step 3
    if (step === 3) {
        const summaryDoctor = document.getElementById('summaryDoctor');
        const summarySpecialty = document.getElementById('summarySpecialty');
        const summaryDate = document.getElementById('summaryDate');
        const summaryTime = document.getElementById('summaryTime');

        if (summaryDoctor) summaryDoctor.textContent = selectedDoctor.name;
        if (summarySpecialty) summarySpecialty.textContent = selectedDoctor.specialization || 'General Medicine';
        if (summaryDate) summaryDate.textContent = SmartClinic.formatDate(selectedDate);
        if (summaryTime) summaryTime.textContent = formatTimeDisplay(selectedTime);
    }
}

// Update step display
function updateStepDisplay() {
    // Update step indicators
    document.querySelectorAll('.booking-step').forEach((step, index) => {
        const stepNum = index + 1;
        step.classList.remove('active', 'completed');

        if (stepNum < currentBookingStep) {
            step.classList.add('completed');
        } else if (stepNum === currentBookingStep) {
            step.classList.add('active');
        }
    });

    // Update form steps
    document.querySelectorAll('.booking-form-step').forEach((step, index) => {
        step.classList.remove('active');
        if (index + 1 === currentBookingStep) {
            step.classList.add('active');
        }
    });
}

// Load time slots from PHP API
async function loadTimeSlots() {
    const dateInput = document.getElementById('appointmentDate');
    const date = dateInput?.value;

    if (!date || !selectedDoctor) return;

    selectedDate = date;
    selectedTime = null;

    const selectBtn = document.getElementById('selectTimeBtn');
    if (selectBtn) selectBtn.disabled = true;

    const container = document.getElementById('timeSlots');
    if (!container) return;

    // Show loading
    container.innerHTML = '<div class="text-center" style="padding: 1rem;">Loading available slots...</div>';

    try {
        const response = await API.appointments.getSlots(selectedDoctor.id, date);

        if (response.success && response.data) {
            const availableSlots = response.data;

            if (availableSlots.length === 0) {
                container.innerHTML = `
                    <div class="text-center" style="padding: var(--spacing-xl); color: var(--color-text-muted);">
                        No available slots for this date. Please choose another date.
                    </div>
                `;
                return;
            }

            container.innerHTML = '';
            availableSlots.forEach(slot => {
                const slotBtn = document.createElement('button');
                slotBtn.type = 'button';
                slotBtn.className = 'time-slot';
                slotBtn.textContent = slot.formatted || formatTimeDisplay(slot.time);
                slotBtn.dataset.time = slot.time;
                slotBtn.onclick = () => selectTimeSlot(slot.time, slotBtn);
                container.appendChild(slotBtn);
            });
        } else {
            throw new Error('Failed to load time slots');
        }
    } catch (error) {
        console.error('Error loading time slots:', error);
        container.innerHTML = `
            <div class="text-center" style="padding: var(--spacing-xl); color: var(--color-danger);">
                Failed to load time slots. Please try again.
            </div>
        `;
    }
}

// Format time for display
function formatTimeDisplay(time) {
    if (!time) return '';
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
}

// Select a time slot
function selectTimeSlot(time, btnElement) {
    selectedTime = time;

    document.querySelectorAll('.time-slot').forEach(btn => {
        btn.classList.remove('selected');
    });
    btnElement.classList.add('selected');

    const selectBtn = document.getElementById('selectTimeBtn');
    if (selectBtn) selectBtn.disabled = false;
}

// Logout function
function logout() {
    Auth.logout();
}

// Form submission - Book appointment via PHP API
const bookingForm = document.getElementById('bookingForm');
if (bookingForm) {
    bookingForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const appointmentType = document.getElementById('appointmentType')?.value;
        const reason = document.getElementById('appointmentReason')?.value;

        if (!appointmentType || !reason) {
            SmartClinic.showNotification('Please fill in all required fields', 'warning');
            return;
        }

        const currentUser = Auth.getCurrentUser();
        if (!currentUser) {
            SmartClinic.showNotification('Please log in again', 'error');
            window.location.href = 'patient-login.html';
            return;
        }

        const appointmentData = {
            doctor_id: selectedDoctor.id,
            date: selectedDate,
            time: selectedTime,
            type: appointmentType,
            reason: reason
        };

        SmartClinic.showLoading('Booking your appointment...');

        try {
            const response = await API.appointments.create(appointmentData);
            SmartClinic.hideLoading();

            if (response.success) {
                // Show success modal
                const successModal = document.getElementById('successModal');
                if (successModal) {
                    SmartClinic.openModal('successModal');
                }

                // Send notification
                SmartClinic.showNotification('Appointment booked successfully!', 'success');
            } else {
                throw new Error(response.message || 'Booking failed');
            }

        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification(error.message || 'Failed to book appointment', 'error');
        }
    });
}
