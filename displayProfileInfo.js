/**
 * Sets the values of the fields in the Profile page
 * when the "DOMContentLoaded" event is triggered.
 */
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('firstName').value = firstName;
    document.getElementById('lastName').value = lastName;
    document.getElementById('dob').value = dob;
    document.getElementById('email').value = email;
    document.getElementById('program').value = program;

    document.getElementById('streetNumber').value = streetNumber;
    document.getElementById('streetName').value = streetName;
    document.getElementById('city').value = city;
    document.getElementById('province').value = province;
    document.getElementById('postalCode').value = postalCode;

    // Avatar selection
    if (avatarIndex !== null && avatarIndex !== '') {
        document.querySelector(`input[name="avatar"][value="${avatarIndex}"]`).checked = true;
    }
});
