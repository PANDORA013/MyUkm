document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const currentPhoto = document.getElementById('currentPhoto');
    const photoPlaceholder = document.getElementById('photoPlaceholder');

    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.match('image.*')) {
                    alert('Please select an image file');
                    return;
                }

                // Validate file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size should not exceed 2MB');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    if (currentPhoto) currentPhoto.style.display = 'none';
                    if (photoPlaceholder) photoPlaceholder.style.display = 'none';
                    if (photoPreview) {
                        photoPreview.style.display = 'block';
                        photoPreview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Password validation
    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Password baru dan konfirmasi password tidak cocok');
                return;
            }

            if (newPassword.length < 8) {
                e.preventDefault();
                alert('Password harus minimal 8 karakter');
                return;
            }
        });
    }
});
