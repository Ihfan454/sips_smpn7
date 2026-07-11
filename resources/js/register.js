/**
 * Register Page JavaScript
 * Sistem Informasi Pelanggaran Siswa SMP Negeri 7 Jember
 * Dengan Animasi Splash Screen dan Slide ke Kiri
 */

// ========================================
// SPLASH SCREEN & ANIMATION CONTROLLER
// ========================================
class SplashScreenController {
    constructor() {
        this.splashScreen = document.getElementById('splashScreen');
        this.registerContainer = document.getElementById('registerContainer');
        this.registerLeft = document.getElementById('registerLeft');
        this.registerRight = document.getElementById('registerRight');
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

        // 2. Tampilkan register container
        this.registerContainer.classList.add('visible');

        // 3. Animasi slide untuk bagian kiri dari tengah ke kiri
        // Pertama set posisi awal di tengah (seperti splash)
        this.registerLeft.classList.add('initial-center');

        // Beri sedikit delay untuk memastikan container visible
        setTimeout(() => {
            // Hapus class initial-center dan tambah slide-left
            this.registerLeft.classList.remove('initial-center');
            this.registerLeft.classList.add('slide-left');

            // 4. Fade in form register di sebelah kanan
            this.registerRight.classList.add('visible');
        }, 100);
    }
}

// ========================================
// TOGGLE PASSWORD VISIBILITY
// ========================================
class PasswordToggle {
    constructor() {
        this.buttons = document.querySelectorAll('.toggle-password');
        this.init();
    }

    init() {
        this.buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const targetId = btn.getAttribute('data-target');
                const input = document.getElementById(targetId);
                
                if (input) {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                }
            });
        });
    }
}

// ========================================
// PASSWORD MATCH CHECKER (UNTUK REGISTER)
// ========================================
class PasswordMatch {
    constructor() {
        this.passwordInput = document.getElementById('password');
        this.confirmInput = document.getElementById('password_confirmation');
        this.matchDiv = document.getElementById('passwordMatch');
        this.registerBtn = document.getElementById('registerBtn');
        this.init();
    }

    init() {
        if (!this.confirmInput || !this.passwordInput) return;

        const checkMatch = () => {
            const password = this.passwordInput.value;
            const confirm = this.confirmInput.value;
            
            if (confirm === '') {
                if (this.matchDiv) {
                    this.matchDiv.textContent = '';
                    this.matchDiv.classList.remove('match', 'not-match');
                }
                if (this.registerBtn) this.registerBtn.disabled = false;
                return;
            }
            
            if (password === confirm) {
                if (this.matchDiv) {
                    this.matchDiv.textContent = '✓ Password cocok';
                    this.matchDiv.classList.add('match');
                    this.matchDiv.classList.remove('not-match');
                }
                if (this.registerBtn) this.registerBtn.disabled = false;
            } else {
                if (this.matchDiv) {
                    this.matchDiv.textContent = '✗ Password tidak cocok';
                    this.matchDiv.classList.add('not-match');
                    this.matchDiv.classList.remove('match');
                }
                if (this.registerBtn) this.registerBtn.disabled = true;
            }
        };

        this.passwordInput.addEventListener('input', checkMatch);
        this.confirmInput.addEventListener('input', checkMatch);
    }
}

// ========================================
// FORM VALIDATION UNTUK REGISTER
// ========================================
class FormValidation {
    constructor() {
        this.form = document.getElementById('registerForm');
        this.init();
    }

    init() {
        if (!this.form) return;

        this.form.addEventListener('submit', (e) => {
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const nip = document.getElementById('nip');
            const ni_pppk = document.getElementById('ni_pppk');
            const password = document.getElementById('password');
            const confirm = document.getElementById('password_confirmation');
            let isValid = true;

            // Clear previous errors
            this.clearErrors();

            // Validate name
            if (!name.value.trim()) {
                this.showError(name, 'Nama lengkap harus diisi');
                isValid = false;
            }

            // Validate email
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim()) {
                this.showError(email, 'Email harus diisi');
                isValid = false;
            } else if (!emailPattern.test(email.value)) {
                this.showError(email, 'Email tidak valid');
                isValid = false;
            }

            // Validate NIP (opsional, jika diisi harus 18 digit angka)
            if (nip && nip.value.trim()) {
                const nipPattern = /^[0-9]{18}$/;
                if (!nipPattern.test(nip.value)) {
                    this.showError(nip, 'NIP harus 18 digit angka');
                    isValid = false;
                }
            }

            // Validate NI PPPK (opsional, jika diisi harus 18 digit angka)
            if (ni_pppk && ni_pppk.value.trim()) {
                const pppkPattern = /^[0-9]{18}$/;
                if (!pppkPattern.test(ni_pppk.value)) {
                    this.showError(ni_pppk, 'NI PPPK harus 18 digit angka');
                    isValid = false;
                }
            }

            // Validate password
            if (!password.value) {
                this.showError(password, 'Password harus diisi');
                isValid = false;
            } else if (password.value.length < 8) {
                this.showError(password, 'Password minimal 8 karakter');
                isValid = false;
            }

            // Validate confirm password
            if (password.value !== confirm.value) {
                this.showError(confirm, 'Password tidak cocok');
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
    new PasswordMatch();
    new FormValidation();
    new ResetErrorOnInput();
    
    console.log('✨ Register page with opening animation ready - SMP Negeri 7 Jember');
});