/**
 * Login Page JavaScript
 * Sistem Informasi Pelanggaran Siswa SMP Negeri 7 Jember
 * Dengan Animasi Splash Screen dan Slide ke Kiri
 */

// ========================================
// SPLASH SCREEN & ANIMATION CONTROLLER
// ========================================
class SplashScreenController {
    constructor() {
        this.splashScreen = document.getElementById('splashScreen');
        this.loginContainer = document.getElementById('loginContainer');
        this.loginLeft = document.getElementById('loginLeft');
        this.loginRight = document.getElementById('loginRight');
        this.splashDuration = 2800; // 2.8 detik untuk splash screen
        this.init();
    }

    init() {
        if (!this.splashScreen) return;

        // Mulai animasi splash screen
        setTimeout(() => {
            this.startTransition();
        }, this.splashDuration);
    }

    startTransition() {
        // 1. Sembunyikan splash screen dengan fade out
        this.splashScreen.classList.add('hide');

        // 2. Tampilkan login container
        this.loginContainer.classList.add('visible');

        // 3. Animasi slide untuk bagian kiri dari tengah ke kiri
        // Pertama set posisi awal di tengah (seperti splash)
        this.loginLeft.classList.add('initial-center');

        // Beri sedikit delay untuk memastikan container visible
        setTimeout(() => {
            // Hapus class initial-center dan tambah slide-left
            this.loginLeft.classList.remove('initial-center');
            this.loginLeft.classList.add('slide-left');

            // 4. Fade in form login di sebelah kanan
            this.loginRight.classList.add('visible');
        }, 100);
    }
}

// ========================================
// TOGGLE PASSWORD VISIBILITY
// ========================================
class PasswordToggle {
    constructor() {
        this.toggleBtn = document.getElementById('togglePassword');
        this.passwordInput = document.getElementById('password');
        this.init();
    }

    init() {
        if (!this.toggleBtn || !this.passwordInput) return;

        this.toggleBtn.addEventListener('click', () => {
            const type = this.passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            this.passwordInput.setAttribute('type', type);
            
            const icon = this.toggleBtn.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            }
        });
    }
}

// ========================================
// LOGIN TYPE SELECTOR (Email / NIP / NI PPPK)
// ========================================
class LoginTypeSelector {
    constructor() {
        this.buttons = document.querySelectorAll('.type-btn');
        this.identifierInput = document.getElementById('login_identifier');
        this.init();
    }

    init() {
        this.loginTypeInput = document.getElementById('login_type');
        if (!this.buttons.length || !this.identifierInput) return;

        this.buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all buttons
                this.buttons.forEach(b => b.classList.remove('active'));
                
                // Add active class to clicked button
                btn.classList.add('active');
                
                // Update placeholder based on selected type
                const type = btn.getAttribute('data-type');
            
                // Set hidden input value
                if (this.loginTypeInput) {
                    this.loginTypeInput.value = type;
                }

                this.updatePlaceholder(type);
            });
        });
    }

    updatePlaceholder(type) {
        switch(type) {
            case 'email':
                this.identifierInput.placeholder = 'Masukkan Email (contoh: nama@email.com)';
                this.identifierInput.setAttribute('type', 'email');
                break;
            case 'nip':
                this.identifierInput.placeholder = 'Masukkan 18 digit NIP';
                this.identifierInput.setAttribute('type', 'text');
                break;
            case 'ni_pppk':
                this.identifierInput.placeholder = 'Masukkan 18 digit NI PPPK';
                this.identifierInput.setAttribute('type', 'text');
                break;
            default:
                this.identifierInput.placeholder = 'Email / NIP / PPPK';
        }
    }
}

// ========================================
// FORM VALIDATION
// ========================================
class FormValidation {
    constructor() {
        this.form = document.getElementById('loginForm');
        this.init();
    }

    init() {
        if (!this.form) return;

        this.form.addEventListener('submit', (e) => {
            const identifier = document.getElementById('login_identifier');
            const password = document.getElementById('password');
            let isValid = true;

            // Clear previous errors
            this.clearErrors();

            // Validate identifier
            if (!identifier.value.trim()) {
                this.showError(identifier, 'Identitas masuk harus diisi');
                isValid = false;
            }

            // Validate password
            if (!password.value) {
                this.showError(password, 'Password harus diisi');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    showError(input, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'input-error';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        
        const inputGroup = input.closest('.input-group');
        if (inputGroup && !inputGroup.querySelector('.input-error')) {
            inputGroup.appendChild(errorDiv);
        }
        
        input.style.borderColor = '#ef4444';
        input.style.backgroundColor = '#fef2f2';
    }

    clearErrors() {
        document.querySelectorAll('.input-error').forEach(error => error.remove());
        document.querySelectorAll('.modern-input').forEach(input => {
            input.style.borderColor = '';
            input.style.backgroundColor = '';
        });
    }
}

// ========================================
// RESET ERROR ON INPUT
// ========================================
class ResetErrorOnInput {
    constructor() {
        this.inputs = document.querySelectorAll('.modern-input');
        this.init();
    }

    init() {
        this.inputs.forEach(input => {
            input.addEventListener('input', () => {
                const errorDiv = input.closest('.input-group')?.querySelector('.input-error');
                if (errorDiv) errorDiv.remove();
                input.style.borderColor = '';
                input.style.backgroundColor = '';
            });
        });
    }
}

// ========================================
// INITIALIZE ALL FEATURES
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    new SplashScreenController();
    new PasswordToggle();
    new LoginTypeSelector();
    new FormValidation();
    new ResetErrorOnInput();
    
    console.log('✨ Login page with opening animation ready - SMP Negeri 7 Jember');
});