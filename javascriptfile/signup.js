const signupForm = document.getElementById('signup_form');
const firstNameInput = document.getElementById('fName');
const lastNameInput = document.getElementById('lName');
const emailInput = document.getElementById('Email_box');
const passwordInput = document.getElementById('password_box');
const confirmPasswordInput = document.getElementById('cPassword');
const errorMessages = document.querySelectorAll('.error');

const nameRegex = /^[A-Za-z]{2,}$/; 
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

signupForm?.addEventListener('submit', function(e) {
  e.preventDefault();
  const isValid=true;
  if(!nameRegex.test(firstNameInput.value)){
    alert("First name should contain only letters and at least 2 characters");
    firstNameInput.style.border = "1px solid red";
    isValid=false;
  } else {
    firstNameInput.style.border = "1px solid green";
  }
  if(!nameRegex.test(lastNameInput.value)){
    alert("Last name should contain only letters and at least 2 characters");
    lastNameInput.style.border = "1px solid red";
    isValid=false;
  } else {
    lastNameInput.style.border = "1px solid green";
  }
  if(emailRegex.test(emailInput.value)){
    errorMessages[2].textContent = "Valid email address";
    errorMessages[2].style.color = "green";
    emailInput.style.border = "1px solid green";
  } else {
    errorMessages[2].textContent = "Please enter a valid email address";
    errorMessages[2].style.color = "red";
    emailInput.style.border = "1px solid red";
    isValid=false;
  }
  if(passwordRegex.test(passwordInput.value)){
    errorMessages[3].textContent = "Strong password";
    errorMessages[3].style.color = "green";
    passwordInput.style.border = "1px solid green";
  } else {
    errorMessages[3].textContent = "Password must be 8+ chars, include uppercase, lowercase, number, and special character";
    errorMessages[3].style.color = "red";
    passwordInput.style.border = "1px solid red";
    isValid=false;
  }
  if(passwordInput.value === confirmPasswordInput.value){
    errorMessages[4].textContent = "Passwords match";
    errorMessages[4].style.color = "green";
    confirmPasswordInput.style.border = "1px solid green";
  } else {
    errorMessages[4].textContent = "Passwords do not match";
    errorMessages[4].style.color = "red";
    confirmPasswordInput.style.border = "1px solid red";
    isValid=false;
  }
  if(isValid){
  alert("Signup successful");
  signupForm.submit();
  } 
});