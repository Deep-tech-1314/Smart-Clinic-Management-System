/**
 * SUDAMA CLINIC - Patient Portal Dynamic Module
 * Handles all dynamic functionality for patient pages
 */

const PatientPortal = {
    /**
     * Initialize Dashboard
     */
    async initDashboard() {
        try {
            SmartClinic.showLoading('Loading dashboard...');

            const [dashboardData, profileData] = await Promise.all([
                API.patients.getDashboard(),
                API.patients.getProfile()
            ]);

            if (dashboardData.success) {
                this.renderDashboardStats(dashboardData.data);
            }

            if (profileData.success) {
                this.renderUserProfile(profileData.data);
            }

            SmartClinic.hideLoading();
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Error loading dashboard: ' + error.message, 'error');
        }
    },

    renderDashboardStats(data) {
        const stats = data.stats;

        // Update stat cards
        if (document.getElementById('upcomingCount')) {
            document.getElementById('upcomingCount').textContent = stats.upcoming_appointments || 0;
        }
        if (document.getElementById('visitsCount')) {
            document.getElementById('visitsCount').textContent = stats.total_visits || 0;
        }
        if (document.getElementById('prescriptionsCount')) {
            document.getElementById('prescriptionsCount').textContent = stats.prescriptions || 0;
        }
        if (document.getElementById('messagesCount')) {
            document.getElementById('messagesCount').textContent = stats.unread_messages || 0;
        }

        // Render upcoming appointments
        if (data.upcoming_appointments && document.getElementById('upcomingAppointments')) {
            const appointmentsHTML = data.upcoming_appointments.map(apt => `
                <div class="appointment-card" onclick="window.location.href='my-appointments.html'">
                    <div class="appointment-info">
                        <h4>${apt.doctor_name}</h4>
                        <p class="specialization">${apt.specialization}</p>
                        <p class="datetime"><i class="icon">📅</i> ${apt.date} at ${apt.time}</p>
                    </div>
                    <div class="appointment-status ${apt.status}">${apt.status}</div>
                </div>
            `).join('');

            document.getElementById('upcomingAppointments').innerHTML = appointmentsHTML || '<p class="empty-state">No upcoming appointments</p>';
        }

        // Render recent prescriptions
        if (data.recent_prescriptions && document.getElementById('recentPrescriptions')) {
            const prescriptionsHTML = data.recent_prescriptions.map(pres => `
                <div class="prescription-card" onclick="window.location.href='view-prescription.html?id=${pres.id}'">
                    <h4>${pres.doctor_name}</h4>
                    <p class="diagnosis">${pres.diagnosis}</p>
                    <p class="date">${new Date(pres.date).toLocaleDateString()}</p>
                </div>
            `).join('');

            document.getElementById('recentPrescriptions').innerHTML = prescriptionsHTML || '<p class="empty-state">No prescriptions yet</p>';
        }
    },

    renderUserProfile(data) {
        // Update user name in header/sidebar
        const userNameElements = document.querySelectorAll('.user-name, [data-user-name]');
        userNameElements.forEach(el => {
            el.textContent = data.name || data.first_name + ' ' + data.last_name;
        });

        // Update email
        const emailElements = document.querySelectorAll('.user-email, [data-user-email]');
        emailElements.forEach(el => {
            el.textContent = data.email;
        });
    },

    /**
     * Initialize Book Appointment Page
     */
    async initBookAppointment() {
        try {
            SmartClinic.showLoading('Loading...');

            // Load specializations
            const specsData = await API.doctors.getSpecializations();

            if (specsData.success) {
                this.renderSpecializations(specsData.data);
            }

            SmartClinic.hideLoading();
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Error loading: ' + error.message, 'error');
        }
    },

    renderSpecializations(specializations) {
        const container = document.getElementById('specializationsContainer');
        if (!container) return;

        const html = specializations.map(spec => `
            <div class="specialization-card" onclick="PatientPortal.selectSpecialization(${spec.id})">
                <div class="spec-icon">${spec.icon}</div>
                <h3>${spec.name}</h3>
                <p>${spec.description}</p>
                <span class="doctor-count">${spec.doctor_count} Doctors</span>
            </div>
        `).join('');

        container.innerHTML = html;
    },

    async selectSpecialization(specializationId) {
        try {
            SmartClinic.showLoading('Loading doctors...');

            const doctorsData = await API.doctors.getList({ specialization_id: specializationId });

            if (doctorsData.success) {
                this.renderDoctors(doctorsData.data.doctors);

                // Scroll to doctors section
                document.getElementById('doctorsSection')?.scrollIntoView({ behavior: 'smooth' });
            }

            SmartClinic.hideLoading();
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Error: ' + error.message, 'error');
        }
    },

    renderDoctors(doctors) {
        const container = document.getElementById('doctorsContainer');
        if (!container) return;

        const html = doctors.map(doc => `
            <div class="doctor-card">
                <div class="doctor-info">
                    <h3>${doc.name}</h3>
                    <p class="qualification">${doc.qualification}</p>
                    <p class="specialization">${doc.specialization}</p>
                    <p class="experience">${doc.experience_years} years experience</p>
                    <p class="charge">₹${doc.new_case_charge} (New) | ₹${doc.old_case_charge} (Follow-up)</p>
                </div>
                <button class="btn btn-primary" onclick="PatientPortal.selectDoctor(${doc.id}, '${doc.name}')">
                    Book Appointment
                </button>
            </div>
        `).join('');

        container.innerHTML = html || '<p class="empty-state">No doctors found</p>';
    },

    async selectDoctor(doctorId, doctorName) {
        // Store selected doctor
        this.selectedDoctor = { id: doctorId, name: doctorName };

        // Show appointment form
        const appointmentForm = document.getElementById('appointmentForm');
        if (appointmentForm) {
            appointmentForm.style.display = 'block';
            document.getElementById('selectedDoctorName').textContent = doctorName;
            appointmentForm.scrollIntoView({ behavior: 'smooth' });
        }

        // Load available dates (today + next 30 days)
        this.renderAvailableDates();
    },

    renderAvailableDates() {
        const dateSelect = document.getElementById('appointmentDate');
        if (!dateSelect) return;

        const dates = [];
        const today = new Date();

        for (let i = 1; i <= 30; i++) {
            const date = new Date(today);
            date.setDate(today.getDate() + i);
            const dateStr = date.toISOString().split('T')[0];
            const displayDate = date.toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short' });
            dates.push(`<option value="${dateStr}">${displayDate}</option>`);
        }

        dateSelect.innerHTML = '<option value="">Select Date</option>' + dates.join('');

        // Add event listener to load slots when date changes
        dateSelect.addEventListener('change', (e) => {
            if (e.target.value) {
                this.loadTimeSlots(this.selectedDoctor.id, e.target.value);
            }
        });
    },

    async loadTimeSlots(doctorId, date) {
        try {
            SmartClinic.showLoading('Loading available slots...');

            const slotsData = await API.appointments.getSlots(doctorId, date);

            if (slotsData.success) {
                this.renderTimeSlots(slotsData.data.slots);
            }

            SmartClinic.hideLoading();
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Error loading slots: ' + error.message, 'error');
        }
    },

    renderTimeSlots(slots) {
        const container = document.getElementById('timeSlotsContainer');
        if (!container) return;

        const html = slots.map(slot => {
            const disabled = !slot.available ? 'disabled' : '';
            const status = slot.booked ? 'booked' : (slot.available ? 'available' : 'unavailable');

            return `
                <button class="time-slot ${status}" ${disabled} 
                        onclick="PatientPortal.selectTimeSlot('${slot.time}')">
                    ${slot.time}
                </button>
            `;
        }).join('');

        container.innerHTML = html;
    },

    selectTimeSlot(time) {
        // Remove previous selection
        document.querySelectorAll('.time-slot').forEach(slot => slot.classList.remove('selected'));

        // Add selection to clicked slot
        event.target.classList.add('selected');

        this.selectedTime = time;
    },

    async bookAppointment() {
        const form = document.getElementById('appointmentBookingForm');
        if (!form) return;

        const appointmentType = form.querySelector('#appointmentType').value;
        const reason = form.querySelector('#reason').value;
        const symptoms = form.querySelector('#symptoms').value;
        const date = document.getElementById('appointmentDate').value;

        if (!this.selectedDoctor || !date || !this.selectedTime) {
            SmartClinic.showNotification('Please select doctor, date, and time', 'error');
            return;
        }

        try {
            SmartClinic.showLoading('Booking appointment...');

            const result = await API.appointments.create({
                doctor_id: this.selectedDoctor.id,
                appointment_date: date,
                appointment_time: this.selectedTime + ':00',
                appointment_type: appointmentType,
                reason: reason,
                symptoms: symptoms
            });

            SmartClinic.hideLoading();

            if (result.success) {
                SmartClinic.showNotification('Appointment booked successfully!', 'success');
                setTimeout(() => {
                    window.location.href = 'my-appointments.html';
                }, 2000);
            }
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Booking failed: ' + error.message, 'error');
        }
    },

    /**
     * Initialize My Appointments Page
     */
    async initMyAppointments() {
        this.currentAppointmentStatus = 'upcoming';
        await this.loadAppointments('upcoming');

        // Setup tab listeners
        document.querySelectorAll('.appointment-tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                const status = e.target.dataset.status;
                this.loadAppointments(status);
            });
        });
    },

    async loadAppointments(status) {
        try {
            SmartClinic.showLoading('Loading appointments...');

            const data = await API.appointments.getList({ status: status });

            if (data.success) {
                this.renderAppointmentsList(data.data.appointments);
                this.currentAppointmentStatus = status;

                // Update active tab
                document.querySelectorAll('.appointment-tab').forEach(tab => {
                    tab.classList.toggle('active', tab.dataset.status === status);
                });
            }

            SmartClinic.hideLoading();
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Error: ' + error.message, 'error');
        }
    },

    renderAppointmentsList(appointments) {
        const container = document.getElementById('appointmentsContainer');
        if (!container) return;

        if (appointments.length === 0) {
            container.innerHTML = '<p class="empty-state">No appointments found</p>';
            return;
        }

        const html = appointments.map(apt => `
            <div class="appointment-item">
                <div class="appointment-details">
                    <h3>${apt.doctor_name}</h3>
                    <p class="specialization">${apt.specialization}</p>
                    <p class="datetime">📅 ${apt.appointment_date} at ${apt.appointment_time}</p>
                    <p class="reason"><strong>Reason:</strong> ${apt.reason || 'N/A'}</p>
                    <span class="status-badge ${apt.status}">${apt.status}</span>
                </div>
                <div class="appointment-actions">
                    ${apt.status === 'pending' || apt.status === 'confirmed' ?
                `<button class="btn btn-danger" onclick="PatientPortal.cancelAppointment(${apt.id})">Cancel</button>` : ''}
                    ${apt.status === 'completed' ?
                `<button class="btn btn-primary" onclick="window.location.href='view-prescription.html?appointment=${apt.id}'">View Prescription</button>` : ''}
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    },

    async cancelAppointment(appointmentId) {
        if (!confirm('Are you sure you want to cancel this appointment?')) {
            return;
        }

        const reason = prompt('Please provide a reason for cancellation (optional):');

        try {
            SmartClinic.showLoading('Cancelling...');

            const result = await API.appointments.update(appointmentId, {
                status: 'cancelled',
                cancellation_reason: reason || 'Patient requested cancellation'
            });

            if (result.success) {
                SmartClinic.showNotification('Appointment cancelled', 'success');
                this.loadAppointments(this.currentAppointmentStatus);
            }

            SmartClinic.hideLoading();
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Error: ' + error.message, 'error');
        }
    },

    /**
     * Initialize Prescriptions Page
     */
    async initPrescriptions() {
        await this.loadPrescriptions();
    },

    async loadPrescriptions() {
        try {
            SmartClinic.showLoading('Loading prescriptions...');

            const data = await API.prescriptions.getList();

            if (data.success) {
                this.renderPrescriptionsList(data.data.prescriptions);
            }

            SmartClinic.hideLoading();
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Error: ' + error.message, 'error');
        }
    },

    renderPrescriptionsList(prescriptions) {
        const container = document.getElementById('prescriptionsContainer');
        if (!container) return;

        if (prescriptions.length === 0) {
            container.innerHTML = '<p class="empty-state">No prescriptions found</p>';
            return;
        }

        const html = prescriptions.map(pres => `
            <div class="prescription-card" onclick="window.location.href='view-prescription.html?id=${pres.id}'">
                <div class="prescription-header">
                    <h3>${pres.doctor_name}</h3>
                    <span class="date">${new Date(pres.created_at).toLocaleDateString()}</span>
                </div>
                <p class="diagnosis"><strong>Diagnosis:</strong> ${pres.diagnosis}</p>
                <p class="medicines"><strong>Medicines:</strong> ${pres.medicine_count} prescribed</p>
                ${pres.follow_up_date ? `<p class="follow-up">Follow-up: ${pres.follow_up_date}</p>` : ''}
            </div>
        `).join('');

        container.innerHTML = html;
    },

    /**
     * Initialize View Prescription Page
     */
    async initViewPrescription() {
        const urlParams = new URLSearchParams(window.location.search);
        const prescriptionId = urlParams.get('id');

        if (!prescriptionId) {
            SmartClinic.showNotification('No prescription selected', 'error');
            window.location.href = 'prescription.html';
            return;
        }

        try {
            SmartClinic.showLoading('Loading prescription...');

            const data = await API.prescriptions.view(prescriptionId);

            if (data.success) {
                this.renderPrescriptionDetails(data.data);
            }

            SmartClinic.hideLoading();
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Error: ' + error.message, 'error');
        }
    },

    renderPrescriptionDetails(prescription) {
        // Update patient info
        document.getElementById('patientName').textContent = prescription.patient.name;
        document.getElementById('patientAge').textContent = this.calculateAge(prescription.patient.date_of_birth);
        document.getElementById('patientGender').textContent = prescription.patient.gender;

        // Update doctor info
        document.getElementById('doctorName').textContent = prescription.doctor.name;
        document.getElementById('doctorQualification').textContent = prescription.doctor.qualification;
        document.getElementById('doctorSpecialization').textContent = prescription.doctor.specialization;

        // Update prescription date
        document.getElementById('prescriptionDate').textContent = new Date(prescription.prescribed_on).toLocaleDateString();

        // Update diagnosis
        document.getElementById('diagnosis').textContent = prescription.diagnosis;

        // Render medicines table
        const medicinesTable = document.getElementById('medicinesTable');
        if (medicinesTable && prescription.medicines) {
            const rows = prescription.medicines.map(med => `
                <tr>
                    <td>${med.name}</td>
                    <td>${med.dosage}</td>
                    <td>${med.frequency}</td>
                    <td>${med.duration}</td>
                </tr>
            `).join('');

            medicinesTable.innerHTML = rows;
        }

        // Update instructions
        document.getElementById('instructions').textContent = prescription.instructions || 'No specific instructions';

        // Update follow-up
        if (prescription.follow_up_date) {
            document.getElementById('followUpDate').textContent = prescription.follow_up_date;
        }
    },

    calculateAge(dateOfBirth) {
        const today = new Date();
        const birthDate = new Date(dateOfBirth);
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age + ' years';
    },

    /**
     * Initialize Messages Page
     */
    async initMessages() {
        this.currentMessageType = 'inbox';
        await this.loadMessages('inbox');

        // Setup tab listeners
        document.querySelectorAll('.message-tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                const type = e.target.dataset.type;
                this.loadMessages(type);
            });
        });
    },

    async loadMessages(type) {
        try {
            SmartClinic.showLoading('Loading messages...');

            const data = await API.messages.getInbox(type === 'inbox' ? 1 : 2);

            if (data.success) {
                this.renderMessagesList(data.data.messages, type);
                this.currentMessageType = type;

                // Update active tab
                document.querySelectorAll('.message-tab').forEach(tab => {
                    tab.classList.toggle('active', tab.dataset.type === type);
                });
            }

            SmartClinic.hideLoading();
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Error: ' + error.message, 'error');
        }
    },

    renderMessagesList(messages, type) {
        const container = document.getElementById('messagesContainer');
        if (!container) return;

        if (messages.length === 0) {
            container.innerHTML = '<p class="empty-state">No messages</p>';
            return;
        }

        const html = messages.map(msg => `
            <div class="message-item ${msg.is_read ? 'read' : 'unread'}" onclick="PatientPortal.viewMessage(${msg.id})">
                <div class="message-header">
                    <strong>${type === 'inbox' ? msg.sender_name : msg.receiver_name}</strong>
                    <span class="date">${new Date(msg.created_at).toLocaleDateString()}</span>
                </div>
                <div class="message-subject">${msg.subject}</div>
                <div class="message-preview">${msg.message.substring(0, 100)}...</div>
            </div>
        `).join('');

        container.innerHTML = html;
    },

    async viewMessage(messageId) {
        try {
            // Mark as read
            await API.messages.markRead(messageId);

            // Reload messages
            this.loadMessages(this.currentMessageType);
        } catch (error) {
            console.error('Error marking message as read:', error);
        }
    },

    /**
     * Initialize Settings Page
     */
    async initSettings() {
        await this.loadProfileSettings();
    },

    async loadProfileSettings() {
        try {
            SmartClinic.showLoading('Loading profile...');

            const data = await API.patients.getProfile();

            if (data.success) {
                this.populateSettingsForm(data.data);
            }

            SmartClinic.hideLoading();
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Error: ' + error.message, 'error');
        }
    },

    populateSettingsForm(profile) {
        const form = document.getElementById('profileForm');
        if (!form) return;

        form.querySelector('#firstName').value = profile.first_name || '';
        form.querySelector('#lastName').value = profile.last_name || '';
        form.querySelector('#email').value = profile.email || '';
        form.querySelector('#phone').value = profile.phone || '';
        form.querySelector('#dateOfBirth').value = profile.date_of_birth || '';
        form.querySelector('#gender').value = profile.gender || '';
        form.querySelector('#address').value = profile.address || '';
        form.querySelector('#city').value = profile.city || '';
        form.querySelector('#bloodGroup').value = profile.blood_group || '';
        form.querySelector('#emergencyContact').value = profile.emergency_contact || '';
        form.querySelector('#allergies').value = profile.allergies || '';
        form.querySelector('#medicalHistory').value = profile.medical_history || '';
    },

    async updateProfile() {
        const form = document.getElementById('profileForm');
        if (!form) return;

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        try {
            SmartClinic.showLoading('Updating profile...');

            const result = await API.patients.updateProfile(data);

            if (result.success) {
                SmartClinic.showNotification('Profile updated successfully', 'success');
            }

            SmartClinic.hideLoading();
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Error: ' + error.message, 'error');
        }
    },

    async changePassword() {
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (!currentPassword || !newPassword || !confirmPassword) {
            SmartClinic.showNotification('All password fields are required', 'error');
            return;
        }

        if (newPassword !== confirmPassword) {
            SmartClinic.showNotification('New passwords do not match', 'error');
            return;
        }

        try {
            SmartClinic.showLoading('Changing password...');

            const result = await API.patients.changePassword({
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword
            });

            if (result.success) {
                SmartClinic.showNotification('Password changed successfully', 'success');
                document.getElementById('passwordForm').reset();
            }

            SmartClinic.hideLoading();
        } catch (error) {
            SmartClinic.hideLoading();
            SmartClinic.showNotification('Error: ' + error.message, 'error');
        }
    }
};

// Export to window
window.PatientPortal = PatientPortal;
