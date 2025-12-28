const signupForm = document.getElementById('signup_form');

signupForm?.addEventListener('submit', function(e) {
    let isValid = true;

    const email = document.getElementById('Email_box');
    const eError = email.parentElement.nextElementSibling; 
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email.value)) {
        eError.textContent = "Valid email is required";
        eError.style.color = "red";
        email.style.border = "1px solid red";
        isValid = false;
    } else {
        eError.textContent = "";
        email.style.border = "1px solid green";
    }
    const phone = document.getElementById('Phone');
    const pError = phone.nextElementSibling;
    if (phone.value.length !== 10) {
        pError.textContent = "Enter 10 digit number";
        pError.style.color = "red";
        phone.style.border = "1px solid red";
        isValid = false;
    } else {
        pError.textContent = "";
        phone.style.border = "1px solid green";
    }

    const pass = document.getElementById('password_box');
    const cPass = document.getElementById('cPassword');
    const cError = cPass.parentElement.nextElementSibling;

    if (cPass.value !== pass.value || cPass.value === "") {
        cError.textContent = "Passwords do not match";
        cError.style.color = "red";
        cPass.style.border = "1px solid red";
        isValid = false;
    } else {
        cError.textContent = "";
        cPass.style.border = "1px solid green";
    }

    const otherFields = ['fName', 'lName', 'address'];
    otherFields.forEach(id => {
        const field = document.getElementById(id);
        if (field.value.trim() === "") {
            field.style.border = "1px solid red";
            isValid = false;
        } else {
            field.style.border = "1px solid green";
        }
    });

    if (!isValid) {
        e.preventDefault(); 
    }
});