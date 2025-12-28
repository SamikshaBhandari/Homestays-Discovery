const loginForm = document.getElementById('login_form');
const emailInput = document.getElementById('email_box');
const passwordInput = document.getElementById('password_input');
const errorMessages = document.querySelectorAll('.error');

loginForm?.addEventListener('submit', function(e) {
  e.preventDefault();   
  let isValid = true; 

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if(emailRegex.test(emailInput.value)) {
    errorMessages[0].textContent = '';
    emailInput.style.border = '1px solid green';
  } else {
    errorMessages[0].textContent = 'Please enter a valid email address';
    errorMessages[0].style.color = 'red';
    emailInput.style.border = '1px solid red';
    isValid = false;
  }

  if(passwordInput.value.trim() !== "") {
    errorMessages[1].textContent = '';
    passwordInput.style.border = '1px solid green';
  } else {
    errorMessages[1].textContent = 'Password is required';
    errorMessages[1].style.color = 'red';
    passwordInput.style.border = '1px solid red';
    isValid = false;
  }
  if(isValid){
    alert("Login successful");
    loginForm.submit();
  }
});