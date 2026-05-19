/**
 * SUDAMA CLINIC Management System
 * Core JavaScript Utilities
 * Connected to PHP Backend API
 */

// ===== CLINIC DATA MANAGEMENT (API-connected) =====
const ClinicData = {
    // Get doctors from API
    async getDoctors(filters = {}) {
        try {
            const response = await API.doctors.getList(filters);
            return response.success ? response.data : [];
        } catch (error) {
            console.error('Error fetching doctors:', error);
            return [];
        }
    },

    // Get single doctor
    async getDoctor(id) {
        try {
            const response = await API.doctors.getProfile(id);
            return response.success ? response.data : null;
        } catch (error) {
            console.error('Error fetching doctor:', error);
            return null;
        }
    },

    // Get patient profile
    async getPatient(id = null) {
        try {
            const response = await API.patients.getProfile(id);
            return response.success ? response.data : null;
        } catch (error) {
            console.error('Error fetching patient:', error);
            return null;
        }
    },

    // Get patient dashboard
    async getPatientDashboard() {
        try {
            const response = await API.patients.getDashboard();
            return response.success ? response.data : null;
        } catch (error) {
            console.error('Error fetching dashboard:', error);
            return null;
        }
    },

    // Get prescriptions
    async getPrescriptions(filters = {}) {
        try {
            const response = await API.prescriptions.getList(filters);
            return response.success ? response.data : [];
        } catch (error) {
            console.error('Error fetching prescriptions:', error);
            return [];
        }
    },

    // Get messages
    async getMessages(type = 'inbox') {
        try {
            const response = type === 'inbox'
                ? await API.messages.getInbox()
                : await API.messages.getSent();
            return response.success ? response.data : [];
        } catch (error) {
            console.error('Error fetching messages:', error);
            return [];
        }
    },

    // Send message
    async sendMessage(receiverId, subject, message) {
        try {
            const response = await API.messages.send(receiverId, subject, message);
            return response.success;
        } catch (error) {
            console.error('Error sending message:', error);
            return false;
        }
    },

    // Generate new ID (for compatibility)
    generateId(prefix) {
        const num = Math.floor(Math.random() * 900) + 100;
        return `${prefix}${num}`;
    }
};

// ===== OTP VERIFICATION =====
const OTPVerification = {
    generatedOTP: null,
    email: null,
    callback: null,
    timerInterval: null,

    // Generate OTP
    generate() {
        this.generatedOTP = Math.floor(100000 + Math.random() * 900000).toString();
        console.log('Generated OTP:', this.generatedOTP); // For testing
        return this.generatedOTP;
    },

    // Show OTP Modal
    showModal(email, callback) {
        this.email = email;
        this.callback = callback;
        this.generate();

        const modal = document.getElementById('otpModal');
        if (modal) {
            modal.classList.add('active');
            this.startTimer(120); // 2 minutes
            this.focusFirstInput();

            // Show simulated OTP for demo
            if (typeof SmartClinic !== 'undefined') {
                SmartClinic.showNotification(`Demo OTP: ${this.generatedOTP}`, 'info', 10000);
            }
        }
    },

    // Hide OTP Modal
    hideModal() {
        const modal = document.getElementById('otpModal');
        if (modal) {
            modal.classList.remove('active');
            this.clearInputs();
            this.stopTimer();
        }
    },

    // Focus first input
    focusFirstInput() {
        const inputs = document.querySelectorAll('.otp-input');
        if (inputs.length > 0) inputs[0].focus();
    },

    // Clear inputs
    clearInputs() {
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach(input => input.value = '');
    },

    // Start countdown timer
    startTimer(seconds) {
        const timerEl = document.getElementById('otpTimer');
        let remaining = seconds;

        this.timerInterval = setInterval(() => {
            const mins = Math.floor(remaining / 60);
            const secs = remaining % 60;
            if (timerEl) {
                timerEl.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
            }
            remaining--;

            if (remaining < 0) {
                this.stopTimer();
                if (timerEl) timerEl.textContent = 'Expired';
            }
        }, 1000);
    },

    // Stop timer
    stopTimer() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    },

    // Verify OTP
    verify() {
        const inputs = document.querySelectorAll('.otp-input');
        let enteredOTP = '';
        inputs.forEach(input => enteredOTP += input.value);

        if (enteredOTP === this.generatedOTP) {
            this.hideModal();
            if (typeof SmartClinic !== 'undefined') {
                SmartClinic.showNotification('OTP verified successfully!', 'success');
            }
            if (this.callback) this.callback(true);
            return true;
        } else {
            if (typeof SmartClinic !== 'undefined') {
                SmartClinic.showNotification('Invalid OTP. Please try again.', 'error');
            }
            this.clearInputs();
            this.focusFirstInput();
            return false;
        }
    },

    // Resend OTP
    resend() {
        this.generate();
        this.startTimer(120);
        if (typeof SmartClinic !== 'undefined') {
            SmartClinic.showNotification(`New OTP sent! Demo: ${this.generatedOTP}`, 'info', 10000);
        }
    },

    // Initialize OTP input handlers
    initInputs() {
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const value = e.target.value;
                if (value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                // Auto-verify when all filled
                if (index === inputs.length - 1 && value.length === 1) {
                    let allFilled = true;
                    inputs.forEach(inp => {
                        if (!inp.value) allFilled = false;
                    });
                    if (allFilled) this.verify();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Allow only numbers
            input.addEventListener('keypress', (e) => {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });
        });
    }
};

// ===== CHARGE CALCULATION =====
const ChargeCalculator = {
    // Calculate charge based on last visit (uses API data)
    async calculate(patientId, doctorId) {
        try {
            const doctor = await ClinicData.getDoctor(doctorId);
            if (!doctor) return { charge: 0, type: 'unknown' };

            // For now, default to new case charge
            // Backend will calculate actual charge based on patient history
            return {
                charge: doctor.new_case_charge || doctor.consultation_charge,
                type: 'new'
            };
        } catch (error) {
            console.error('Error calculating charge:', error);
            return { charge: 0, type: 'unknown' };
        }
    }
};

// ===== SPECIALIZATIONS LIST =====
const Specializations = [
    'General Medicine',
    'Cardiology',
    'Dermatology',
    'Pediatrics',
    'Orthopedics',
    'Gynecology',
    'Neurology',
    'Ophthalmology',
    'ENT',
    'Psychiatry',
    'Dentistry',
    'Oncology',
    'Gastroenterology',
    'Nephrology',
    'Pulmonology'
];

// ===== TIME SLOTS =====
const TimeSlots = [
    '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
    '12:00', '12:30', '14:00', '14:30', '15:00', '15:30',
    '16:00', '16:30', '17:00', '17:30'
];

// ===== UTILITY FUNCTIONS =====
function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-IN', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 0
    }).format(amount);
}

function getInitials(name) {
    if (!name) return '';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
}

function formatTime(time) {
    if (!time) return '';
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
}

// Initialize OTP inputs when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    OTPVerification.initInputs();
});
