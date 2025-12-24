const bookingForm = document.getElementById('Booking');
const checkinInput = document.getElementById('checkin');
const nightsInput = document.getElementById('nights');
const guestInput = document.getElementById('guest');
const nameInput = document.getElementById('Fname');
const inputemail = document.getElementById('Email');
const phoneInput = document.getElementById('phone');

const displayNights = document.getElementById('Numofnight');
const displayGuests = document.getElementById('numofguest');
const subtotalText = document.getElementById('SubTotal');
const totalAmountText = document.getElementById('total_Amount');

const pricePerNight = 1000;

const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const phoneRegex = /^(98|97)\d{8}$/; 

const updateSummary = () => {
    const nights = parseInt(nightsInput.value) || 0;
    const guests = parseInt(guestInput.value) || 0;
    
    if(displayNights) displayNights.textContent = nights;
    if(displayGuests) displayGuests.textContent = guests;
    
    const total = nights * pricePerNight;
    if(subtotalText) subtotalText.textContent = 'Rs.' + total;
    if(totalAmountText) totalAmountText.textContent = 'Rs.' + total;
};

nightsInput?.addEventListener('input', updateSummary);
guestInput?.addEventListener('input', updateSummary);

bookingForm?.addEventListener('submit', function(e) {
    e.preventDefault(); 
    let isValid = true;

    if(nameInput.value.trim().length < 3) {
        alert("Please enter a valid full name (at least 3 characters)");
        nameInput.style.border = '1px solid red';
        isValid = false;
    } else {
        nameInput.style.border = '1px solid green';
    }

    if(emailRegex.test(inputemail.value)) {
        inputemail.style.border = '1px solid green';
    } else {
        alert("Please enter a valid email address");
        inputemail.style.border = '1px solid red';
        isValid = false;
    }
    if(phoneRegex.test(phoneInput.value)) {
        phoneInput.style.border = '1px solid green';
    } else {
        alert("Please enter a valid 10-digit phone number");
        phoneInput.style.border = '1px solid red';
        isValid = false;
    }
    if(checkinInput?.value === "") {
        alert("Please select a check-in date");
        checkinInput.style.border = '1px solid red';
        isValid = false;
    } else {
        checkinInput.style.border = '1px solid green';
    }
    if(isValid){
        alert("Booking details are valid!");
        setTimeout(() => {
            bookingForm.submit(); 
        }, 500);
    }
});