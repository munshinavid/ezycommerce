const form = document.querySelector('form');
const genderInputs = document.querySelectorAll('input[name="gender"]');
const emailInput = document.getElementById('email');
const nidNumberInput = document.getElementById('nid-number');
const errorMessage = document.createElement('div');

form.prepend(errorMessage);

form.addEventListener('submit', (e) => {
  let errors = [];

  // Validate gender
  const genderSelected = Array.from(genderInputs).some(input => input.checked);
  if (!genderSelected) {
    errors.push('Gender is required');
  }

  // Validate email
  if (!emailInput.value || !validateEmail(emailInput.value)) {
    errors.push('A valid email is required');
    emailInput.parentElement.classList.add('incorrect');
  }

  // Validate NID
  if (!nidNumberInput.value || isNaN(nidNumberInput.value)) {
    errors.push('NID number is required and must be numeric');
    nidNumberInput.parentElement.classList.add('incorrect');
  }

  if (errors.length > 0) {
    e.preventDefault();
    errorMessage.innerText = errors.join('. ');
  }
});

// Helper function to validate email
function validateEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

// Real-time input correction
const allInputs = [emailInput, ...genderInputs, nidNumberInput];
allInputs.forEach(input => {
  input.addEventListener('input', () => {
    if (input.parentElement.classList.contains('incorrect')) {
      input.parentElement.classList.remove('incorrect');
      errorMessage.innerText = '';
    }
  });
});
