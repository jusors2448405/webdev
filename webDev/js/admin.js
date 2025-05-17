/**
 * Admin JavaScript file for the portfolio website
 */

document.addEventListener('DOMContentLoaded', function() {
    // Image upload preview
    const imageInput = document.getElementById('image');
    const filePreview = document.querySelector('.file-preview');
    
    if (imageInput && filePreview) {
        imageInput.addEventListener('change', function() {
            // Clear previous preview
            filePreview.innerHTML = '';
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Image Preview';
                    filePreview.appendChild(img);
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Confirm delete
    window.confirmDelete = function(id, title) {
        if (confirm(`Are you sure you want to delete "${title}"? This action cannot be undone.`)) {
            window.location.href = `dashboard.php?delete=${id}`;
        }
    };
    
    // Form validation
    const workForm = document.querySelector('form[enctype="multipart/form-data"]');
    
    if (workForm) {
        workForm.addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const imageInput = document.getElementById('image');
            let hasError = false;
            
            // Check if title is empty
            if (title === '') {
                showValidationError('title', 'Title is required');
                hasError = true;
            } else {
                clearValidationError('title');
            }
            
            // Check if description is empty
            if (description === '') {
                showValidationError('description', 'Description is required');
                hasError = true;
            } else {
                clearValidationError('description');
            }
            
            // Check if image is selected for new works
            if (imageInput && imageInput.required && imageInput.files.length === 0) {
                showValidationError('image', 'Image is required');
                hasError = true;
            } else {
                clearValidationError('image');
            }
            
            if (hasError) {
                e.preventDefault();
            }
        });
    }
    
    function showValidationError(fieldId, message) {
        const field = document.getElementById(fieldId);
        let errorElement = field.parentNode.querySelector('.validation-error');
        
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'validation-error';
            errorElement.style.color = 'var(--admin-danger)';
            errorElement.style.fontSize = '0.85rem';
            errorElement.style.marginTop = '5px';
            field.parentNode.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
        field.style.borderColor = 'var(--admin-danger)';
    }
    
    function clearValidationError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorElement = field.parentNode.querySelector('.validation-error');
        
        if (errorElement) {
            errorElement.remove();
        }
        
        field.style.borderColor = '';
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    }
});
