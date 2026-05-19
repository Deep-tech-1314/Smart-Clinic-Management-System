// ===================================
// SUDAMA CLINIC - Appointments Manager
// Handle appointment viewing and management
// Connected to PHP Backend API
// ===================================

// Get all appointments (from API)
async function getAppointments(params = {}) {
    try {
        const response = await API.appointments.getList(params);
        if (response.success) {
            return response.data || [];
        }
        return [];
    } catch (error) {
        console.error('Error fetching appointments:', error);
        return [];
    }
}

// Get appointment by ID
async function getAppointmentById(id) {
    try {
        const response = await API.appointments.getList({ id });
        if (response.success && response.data.length > 0) {
            return response.data[0];
        }
        return null;
    } catch (error) {
        console.error('Error fetching appointment:', error);
        return null;
    }
}

// Get user's appointments (patient)
async function getUserAppointments(filters = {}) {
    try {
        const response = await API.appointments.getList(filters);
        if (response.success) {
            return response.data || [];
        }
        return [];
    } catch (error) {
        console.error('Error fetching appointments:', error);
        return [];
    }
}

// Book new appointment
async function bookAppointment(appointmentData) {
    try {
        const response = await API.appointments.create(appointmentData);
        if (response.success) {
            return response.data;
        }
        throw new Error(response.message || 'Booking failed');
    } catch (error) {
        throw error;
    }
}

// Update appointment
async function updateAppointment(id, updates) {
    try {
        const response = await API.appointments.update(id, updates);
        if (response.success) {
            return true;
        }
        throw new Error(response.message || 'Update failed');
    } catch (error) {
        console.error('Error updating appointment:', error);
        throw error;
    }
}

// Cancel appointment
async function cancelAppointment(id, reason = '') {
    try {
        const response = await API.appointments.cancel(id, reason);
        if (response.success) {
            SmartClinic.showNotification('Appointment cancelled successfully', 'success');
            return true;
        }
        throw new Error(response.message || 'Cancellation failed');
    } catch (error) {
        SmartClinic.showNotification(error.message || 'Failed to cancel appointment', 'error');
        throw error;
    }
}

// Get available time slots for a doctor on a date
async function getAvailableSlots(doctorId, date) {
    try {
        const response = await API.appointments.getSlots(doctorId, date);
        if (response.success) {
            return response.data || [];
        }
        return [];
    } catch (error) {
        console.error('Error fetching time slots:', error);
        return [];
    }
}

// Get doctors list
async function getDoctors(filters = {}) {
    try {
        const response = await API.doctors.getList(filters);
        if (response.success) {
            return response.data || [];
        }
        return [];
    } catch (error) {
        console.error('Error fetching doctors:', error);
        return [];
    }
}

// Get doctor by ID
async function getDoctorById(id) {
    try {
        const response = await API.doctors.getProfile(id);
        if (response.success) {
            return response.data;
        }
        return null;
    } catch (error) {
        console.error('Error fetching doctor:', error);
        return null;
    }
}

// Filter appointments by status (client-side helper)
function filterAppointmentsByStatus(appointments, status) {
    if (status === 'all') return appointments;
    return appointments.filter(apt => apt.status === status);
}

// Sort appointments by date (client-side helper)
function sortAppointmentsByDate(appointments, ascending = true) {
    return appointments.sort((a, b) => {
        const dateA = new Date(a.date + ' ' + a.time);
        const dateB = new Date(b.date + ' ' + b.time);
        return ascending ? dateA - dateB : dateB - dateA;
    });
}

// Get upcoming appointments
function getUpcomingFromList(appointments, limit = 5) {
    const now = new Date();
    const upcoming = appointments.filter(apt => {
        const aptDate = new Date(apt.date + ' ' + apt.time);
        return aptDate > now && apt.status !== 'cancelled';
    });
    return sortAppointmentsByDate(upcoming, true).slice(0, limit);
}

// Get past appointments
function getPastFromList(appointments) {
    const now = new Date();
    const past = appointments.filter(apt => {
        const aptDate = new Date(apt.date + ' ' + apt.time);
        return aptDate < now;
    });
    return sortAppointmentsByDate(past, false);
}

// Format appointment for display
function formatAppointment(apt) {
    return {
        id: apt.id,
        doctorName: apt.doctor_name,
        specialty: apt.specialization,
        date: apt.date,
        time: apt.time,
        status: apt.status,
        type: apt.type,
        reason: apt.reason,
        charge: apt.charge
    };
}

// Export functions
window.Appointments = {
    getAppointments,
    getAppointmentById,
    getUserAppointments,
    bookAppointment,
    updateAppointment,
    cancelAppointment,
    getAvailableSlots,
    getDoctors,
    getDoctorById,
    filterAppointmentsByStatus,
    sortAppointmentsByDate,
    getUpcomingFromList,
    getPastFromList,
    formatAppointment
};
