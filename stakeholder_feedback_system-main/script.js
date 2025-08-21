// DOM elements
const feedbackForm = document.getElementById('feedbackForm');
const submitBtn = document.getElementById('submitBtn');
const successMessage = document.getElementById('successMessage');
const errorMessage = document.getElementById('errorMessage');
const resetFormBtn = document.getElementById('resetForm');

// Form fields
const nameField = document.getElementById('name');
const emailField = document.getElementById('email');
const ratingField = document.querySelector('input[name="rating"]:checked');
const commentsField = document.getElementById('comments');

// Error message elements
const nameError = document.getElementById('nameError');
const emailError = document.getElementById('emailError');
const ratingError = document.getElementById('ratingError');
const commentsError = document.getElementById('commentsError');

// Star rating elements
const starInputs = document.querySelectorAll('input[name="rating"]');
const starLabels = document.querySelectorAll('.stars label');
const ratingText = document.querySelector('.rating-text');

// Validation patterns
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const namePattern = /^[a-zA-Z\s]{2,50}$/;

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeStarRating();
    setupFormValidation();
    setupFormSubmission();
    setupResetForm();
});

// Star rating functionality
function initializeStarRating() {
    starInputs.forEach((input, index) => {
        input.addEventListener('change', function() {
            updateRatingDisplay(this.value);
            clearError('rating');
        });
    });

    // Hover effects for stars
    starLabels.forEach((label, index) => {
        label.addEventListener('mouseenter', function() {
            const starValue = 5 - index;
            updateRatingText(starValue);
        });

        label.addEventListener('mouseleave', function() {
            const selectedRating = document.querySelector('input[name="rating"]:checked');
            if (selectedRating) {
                updateRatingText(selectedRating.value);
            } else {
                updateRatingText('Click to rate');
            }
        });
    });
}

function updateRatingDisplay(rating) {
    updateRatingText(rating);
}

function updateRatingText(rating) {
    const ratingDescriptions = {
        1: 'Poor',
        2: 'Fair',
        3: 'Good',
        4: 'Very Good',
        5: 'Excellent'
    };
    
    if (ratingDescriptions[rating]) {
        ratingText.textContent = ratingDescriptions[rating];
    } else {
        ratingText.textContent = rating;
    }
}

// Form validation
function setupFormValidation() {
    // Real-time validation for name field
    nameField.addEventListener('input', function() {
        validateName(this.value);
    });

    // Real-time validation for email field
    emailField.addEventListener('input', function() {
        validateEmail(this.value);
    });

    // Real-time validation for comments field
    commentsField.addEventListener('input', function() {
        validateComments(this.value);
    });
}

function validateName(name) {
    if (!name.trim()) {
        showError('name', 'Name is required');
        return false;
    }
    if (!namePattern.test(name.trim())) {
        showError('name', 'Name must be 2-50 characters and contain only letters and spaces');
        return false;
    }
    clearError('name');
    return true;
}

function validateEmail(email) {
    if (!email.trim()) {
        showError('email', 'Email is required');
        return false;
    }
    if (!emailPattern.test(email.trim())) {
        showError('email', 'Please enter a valid email address');
        return false;
    }
    clearError('email');
    return true;
}

function validateRating() {
    const selectedRating = document.querySelector('input[name="rating"]:checked');
    if (!selectedRating) {
        showError('rating', 'Please select a rating');
        return false;
    }
    clearError('rating');
    return true;
}

function validateComments(comments) {
    if (!comments.trim()) {
        showError('comments', 'Comments are required');
        return false;
    }
    if (comments.trim().length < 10) {
        showError('comments', 'Comments must be at least 10 characters long');
        return false;
    }
    if (comments.trim().length > 1000) {
        showError('comments', 'Comments must be less than 1000 characters');
        return false;
    }
    clearError('comments');
    return true;
}

function validateForm() {
    const nameValid = validateName(nameField.value);
    const emailValid = validateEmail(emailField.value);
    const ratingValid = validateRating();
    const commentsValid = validateComments(commentsField.value);

    return nameValid && emailValid && ratingValid && commentsValid;
}

function showError(field, message) {
    const errorElement = document.getElementById(field + 'Error');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

function clearError(field) {
    const errorElement = document.getElementById(field + 'Error');
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.style.display = 'none';
    }
}

// Form submission
function setupFormSubmission() {
    feedbackForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }

        submitForm();
    });
}

async function submitForm() {
    // Disable submit button and show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
    
    // Hide any existing messages
    hideMessages();

    // Prepare form data
    const formData = new FormData(feedbackForm);
    const data = {
        name: formData.get('name').trim(),
        email: formData.get('email').trim(),
        rating: formData.get('rating'),
        comments: formData.get('comments').trim()
    };

    try {
        const response = await fetch('submit_feedback.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            showSuccessMessage();
            feedbackForm.reset();
            resetStarRating();
        } else {
            showErrorMessage(result.message || 'Failed to submit feedback. Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorMessage('Network error. Please check your connection and try again.');
    } finally {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Feedback';
    }
}

function resetStarRating() {
    starInputs.forEach(input => input.checked = false);
    updateRatingText('Click to rate');
}

function showSuccessMessage() {
    successMessage.style.display = 'block';
    feedbackForm.style.display = 'none';
}

function showErrorMessage(message) {
    document.getElementById('errorText').textContent = message;
    errorMessage.style.display = 'block';
}

function hideMessages() {
    successMessage.style.display = 'none';
    errorMessage.style.display = 'none';
}

// Reset form functionality
function setupResetForm() {
    resetFormBtn.addEventListener('click', function() {
        feedbackForm.reset();
        resetStarRating();
        hideMessages();
        clearAllErrors();
        feedbackForm.style.display = 'block';
    });
}

function clearAllErrors() {
    clearError('name');
    clearError('email');
    clearError('rating');
    clearError('comments');
}
