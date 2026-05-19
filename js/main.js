// ===================================
// SUDAMA CLINIC - Main JavaScript
// Core utilities and shared functions
// ===================================

// Navbar scroll effect
window.addEventListener('scroll', function () {
  const navbar = document.getElementById('navbar');
  if (navbar) {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  }
});

// Mobile navbar toggle
const navbarToggle = document.getElementById('navbarToggle');
const navbarNav = document.getElementById('navbarNav');

if (navbarToggle && navbarNav) {
  navbarToggle.addEventListener('click', function () {
    navbarNav.classList.toggle('active');
  });
}

// Sidebaer off-canvas toggle
window.toggleSidebar = function() {
  const sidebar = document.querySelector('.dashboard-sidebar');
  const overlay = document.querySelector('.sidebar-overlay');
  if (sidebar && overlay) {
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    if (sidebar.classList.contains('active')) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
  }
};

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const href = this.getAttribute('href');
    if (href === '#') return;

    e.preventDefault();
    const target = document.querySelector(href);
    if (target) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
      // Close mobile menu if open
      if (navbarNav) {
        navbarNav.classList.remove('active');
      }
    }
  });
});

// ===================================
// Modal Management
// ===================================

function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
  }
}

// Close modal when clicking outside
document.addEventListener('click', function (e) {
  if (e.target.classList.contains('modal')) {
    closeModal(e.target.id);
  }
});

// Close modal with Escape key
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    const activeModal = document.querySelector('.modal.active');
    if (activeModal) {
      closeModal(activeModal.id);
    }
  }
});

// ===================================
// Notification System
// ===================================

function showNotification(message, type = 'info') {
  // Remove existing notification
  const existing = document.querySelector('.notification-toast');
  if (existing) {
    existing.remove();
  }

  const notification = document.createElement('div');
  notification.className = `notification-toast alert alert-${type}`;
  notification.style.cssText = `
    position: fixed;
    top: 80px;
    right: 20px;
    z-index: 3000;
    min-width: 300px;
    max-width: 500px;
    animation: slideDown 0.3s ease-out;
  `;

  const icon = {
    success: '✓',
    error: '✕',
    warning: '⚠',
    info: 'ℹ'
  }[type] || 'ℹ';

  notification.innerHTML = `
    <span style="font-size: 1.2rem; margin-right: 10px;">${icon}</span>
    <span>${message}</span>
  `;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.animation = 'slideUp 0.3s ease-out';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// ===================================
// Form Validation
// ===================================

function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function validatePhone(phone) {
  const re = /^[\d\s\-\+\(\)]{10,}$/;
  return re.test(phone);
}

function validatePassword(password) {
  // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
  return password.length >= 8 &&
    /[A-Z]/.test(password) &&
    /[a-z]/.test(password) &&
    /[0-9]/.test(password);
}

function showFieldError(fieldId, message) {
  const field = document.getElementById(fieldId);
  if (!field) return;

  // Remove existing error
  const existingError = field.parentElement.querySelector('.form-error');
  if (existingError) {
    existingError.remove();
  }

  // Add error styling
  field.style.borderColor = 'var(--accent-error)';

  // Add error message
  if (message) {
    const error = document.createElement('span');
    error.className = 'form-error';
    error.textContent = message;
    field.parentElement.appendChild(error);
  }
}

function clearFieldError(fieldId) {
  const field = document.getElementById(fieldId);
  if (!field) return;

  field.style.borderColor = '';
  const error = field.parentElement.querySelector('.form-error');
  if (error) {
    error.remove();
  }
}

// ===================================
// LocalStorage Helpers
// ===================================

const Storage = {
  get: function (key) {
    try {
      const item = localStorage.getItem(key);
      return item ? JSON.parse(item) : null;
    } catch (e) {
      console.error('Error reading from localStorage:', e);
      return null;
    }
  },

  set: function (key, value) {
    try {
      localStorage.setItem(key, JSON.stringify(value));
      return true;
    } catch (e) {
      console.error('Error writing to localStorage:', e);
      return false;
    }
  },

  remove: function (key) {
    try {
      localStorage.removeItem(key);
      return true;
    } catch (e) {
      console.error('Error removing from localStorage:', e);
      return false;
    }
  },

  clear: function () {
    try {
      localStorage.clear();
      return true;
    } catch (e) {
      console.error('Error clearing localStorage:', e);
      return false;
    }
  }
};

// ===================================
// Date & Time Utilities
// ===================================

function formatDate(date) {
  const d = new Date(date);
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return d.toLocaleDateString('en-US', options);
}

function formatTime(time) {
  const [hours, minutes] = time.split(':');
  const h = parseInt(hours);
  const ampm = h >= 12 ? 'PM' : 'AM';
  const displayHours = h % 12 || 12;
  return `${displayHours}:${minutes} ${ampm}`;
}

function formatDateTime(datetime) {
  const d = new Date(datetime);
  return `${formatDate(d)} at ${formatTime(d.toTimeString().slice(0, 5))}`;
}

// ===================================
// Loading Spinner
// ===================================

function showLoading(message = 'Loading...') {
  const existing = document.querySelector('.loading-overlay');
  if (existing) return;

  const overlay = document.createElement('div');
  overlay.className = 'loading-overlay';
  overlay.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 20px;
  `;

  overlay.innerHTML = `
    <div class="spinner"></div>
    <p style="color: var(--color-text-primary); font-size: var(--font-size-lg);">${message}</p>
  `;

  document.body.appendChild(overlay);
}

function hideLoading() {
  const overlay = document.querySelector('.loading-overlay');
  if (overlay) {
    overlay.remove();
  }
}

// ===================================
// Initialize on page load
// ===================================

document.addEventListener('DOMContentLoaded', function () {
  console.log('SUDAMA CLINIC initialized');

  // Add intersection observer for animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);

  // Observe elements with animation classes
  document.querySelectorAll('.fade-in, .slide-up, .scale-in').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
    observer.observe(el);
  });
});

// Export functions for use in other scripts
window.SmartClinic = {
  openModal,
  closeModal,
  showNotification,
  validateEmail,
  validatePhone,
  validatePassword,
  showFieldError,
  clearFieldError,
  Storage,
  formatDate,
  formatTime,
  formatDateTime,
  showLoading,
  hideLoading
};


// ===================================
// Dark Mode Global Toggle
// ===================================

document.addEventListener('DOMContentLoaded', function () {
  const themeToggle = document.createElement('button');
  themeToggle.id = 'headerThemeToggle';
  themeToggle.setAttribute('aria-label', 'Toggle Dark Mode');

  themeToggle.style.cssText = `
    background: transparent;
    border: 1px solid var(--primary-start);
    border-radius: 50%;
    width: 36px;
    height: 36px;
    min-width: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--primary-start);
    font-size: 1.1rem;
    margin-left: 10px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0;
  `;

  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  themeToggle.innerHTML = isDark ? '☀️' : '🌙';

  themeToggle.addEventListener('click', () => {
    themeToggle.style.transform = 'scale(0.8)';
    setTimeout(() => themeToggle.style.transform = 'scale(1)', 150);

    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    themeToggle.innerHTML = newTheme === 'dark' ? '☀️' : '🌙';
  });

  themeToggle.addEventListener('mouseover', () => {
    themeToggle.style.boxShadow = '0 0 10px rgba(6, 182, 212, 0.4)';
    themeToggle.style.transform = 'translateY(-2px)';
  });
  themeToggle.addEventListener('mouseout', () => {
    themeToggle.style.boxShadow = 'none';
    themeToggle.style.transform = 'translateY(0)';
  });

  // Inject into navbar dynamically
  const navbarNav = document.querySelector('.navbar-nav');
  const flexNav = document.querySelector('nav div[style*="gap: 8px"]');
  const adminProfile = document.querySelector('.header-right');

  if (navbarNav) {
    // Inject Theme Toggle
    const themeLi = document.createElement('li');
    themeLi.style.display = 'flex';
    themeLi.style.alignItems = 'center';
    themeLi.appendChild(themeToggle);
    navbarNav.appendChild(themeLi);

    // Inject Translator
    const langLi = document.createElement('li');
    langLi.style.display = 'flex';
    langLi.style.alignItems = 'center';
    langLi.id = 'google_translate_element';
    langLi.style.marginLeft = '10px';
    navbarNav.appendChild(langLi);
  } else if (flexNav) {
    flexNav.appendChild(themeToggle);

    const langContainer = document.createElement('div');
    langContainer.id = 'google_translate_element';
    langContainer.style.marginLeft = '10px';
    flexNav.appendChild(langContainer);
  } else if (adminProfile) {
    adminProfile.insertBefore(themeToggle, adminProfile.firstChild);

    const langContainer = document.createElement('div');
    langContainer.id = 'google_translate_element';
    langContainer.style.marginRight = '10px';
    adminProfile.insertBefore(langContainer, adminProfile.firstChild);
  } else {
    // Fallback Theme Toggle
    themeToggle.style.position = 'fixed';
    themeToggle.style.top = '15px';
    themeToggle.style.right = '20px';
    themeToggle.style.zIndex = '9999';
    themeToggle.style.background = 'var(--color-bg-secondary)';
    themeToggle.style.boxShadow = 'var(--shadow-md)';
    document.body.appendChild(themeToggle);

    // Fallback Translator
    const langContainer = document.createElement('div');
    langContainer.id = 'google_translate_element';
    langContainer.style.position = 'fixed';
    langContainer.style.bottom = '15px';
    langContainer.style.left = '20px';
    langContainer.style.zIndex = '9999';
    document.body.appendChild(langContainer);
  }

  // Initialize Google Translate
  window.googleTranslateElementInit = function () {
    new google.translate.TranslateElement({
      pageLanguage: 'en',
      layout: google.translate.TranslateElement.InlineLayout.SIMPLE
    }, 'google_translate_element');
  };

  // Append translation API script
  const gtScript = document.createElement('script');
  gtScript.type = 'text/javascript';
  gtScript.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
  document.body.appendChild(gtScript);

  // Inject styling to customize Google Translate widget for Light/Dark mode
  const gtStyles = document.createElement('style');
  gtStyles.innerHTML = `
        /* Strictly hide the Google Translate top banner */
        .goog-te-banner-frame.skiptranslate, .goog-te-banner-frame { 
            display: none !important; 
        }
        body { 
            top: 0px !important; 
            position: static !important;
        }
        
        /* Hide tooltips that Google creates over text */
        .goog-text-highlight {
            background-color: transparent !important;
            box-shadow: none !important;
        }

        /* Style the dropdown nicely */
        #google_translate_element select {
          background: var(--color-bg-secondary);
          color: var(--color-text-primary);
          border: 1px solid var(--primary-start);
          border-radius: 8px;
          padding: 6px 10px;
          font-size: 0.85rem;
          cursor: pointer;
          outline: none;
          font-weight: 500;
          box-shadow: var(--shadow-sm);
        }
        
        /* Hide the Google logo that clutters the nav */
        .goog-logo-link { display: none !important; }
        .goog-te-gadget { color: transparent !important; font-size: 0; }
      `;
  document.head.appendChild(gtStyles);
});
