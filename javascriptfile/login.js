const emailInput = document.getElementById('email_box');
const passwordInput = document.getElementById('password_input');
const loginForm = document.getElementById('login_form');
const errorMessages = document.querySelectorAll('.error');

const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

loginForm?.addEventListener('submit', function(e) {
  e.preventDefault();
  if(emailRegex.test(emailInput.value)) {
    errorMessages[0].textContent = 'Valid email address';
    errorMessages[0].style.color = 'green';
    emailInput.style.border = '1px solid green';
  } else {
    errorMessages[0].textContent = 'Please enter a valid email address';
    errorMessages[0].style.color = 'red';
    emailInput.style.border = '1px solid red';
    return;
  }
  if(passwordRegex.test(passwordInput.value)) {
    errorMessages[1].textContent = 'Strong password';
    errorMessages[1].style.color = 'green';
    passwordInput.style.border = '1px solid green';
  } else {
    errorMessages[1].textContent = 'Password must be at least 8 characters, include uppercase, lowercase, number, and special character';
    errorMessages[1].style.color = 'red';
    passwordInput.style.border = '1px solid red';
    return;
  }
  alert("Login successful ");
  window.location.href = "index.html";
});