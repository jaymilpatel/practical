document.addEventListener('DOMContentLoaded', function () {

    const startInput = document.getElementById('event_start');
    const endInput = document.getElementById('event_end');

    function pad(num) {
        return num.toString().padStart(2, '0');
    }

    // Set min to current date & time
    const now = new Date();
    const minDateTime = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
    startInput.min = minDateTime;
    endInput.min = minDateTime;

    function validateNoWeekend(input) {
        input.addEventListener('change', function () {
            const date = new Date(this.value);
            const day = date.getDay();
            if (day === 0 || day === 6) {
                alert("Weekends (Saturday and Sunday) are not allowed. Please choose a weekday.");
                this.value = ''; // Clear invalid input
            } else if (date < new Date()) {
                alert("You cannot choose a past date.");
                this.value = '';
            }
            const startDate = new Date(this.value);
		    const minEndDate = `${startDate.getFullYear()}-${pad(startDate.getMonth() + 1)}-${pad(startDate.getDate())}T${pad(startDate.getHours())}:${pad(startDate.getMinutes())}`;
		   
		    console.log(startDate)
		    endInput.min = minEndDate;

        });
    }

    validateNoWeekend(startInput);
    validateNoWeekend(endInput);


    const phoneInput = document.getElementById('organizer_phone');
    const phoneError = document.getElementById('phone-error');

    phoneInput.addEventListener('input', function () {
        const phoneRegex = /^\+91-\d{3}-\d{3}-\d{4}$/;
        if (this.value && !phoneRegex.test(this.value)) {
            phoneError.textContent = "Invalid format. Use +91-XXX-XXX-XXXX.";
        } else {
            phoneError.textContent = "";
        }
    });


    const form = document.getElementById('post');
    const venueType = document.getElementById('venue_type');
    const onlineFields = document.getElementById('online_fields');
    const offlineFields = document.getElementById('offline_fields');
    const fields = [
        'event_start',
        'event_end',
        'organizer_name',
        'organizer_email',
        'organizer_phone',
        'venue_type',
        'event_price'
    ];
    function showError(id, message) {
        document.getElementById(id).textContent = message;
    }
    function clearErrors() {
        document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');
    }
     function toggleVenueFields() {
        if (venueType.value === 'online') {
            onlineFields.style.display = 'block';
            offlineFields.style.display = 'none';
        } else if (venueType.value === 'offline') {
            onlineFields.style.display = 'none';
            offlineFields.style.display = 'block';
        } else {
            onlineFields.style.display = 'none';
            offlineFields.style.display = 'none';
        }
    }

    venueType.addEventListener('change', toggleVenueFields);
    toggleVenueFields();


    form.addEventListener('submit', function (e) {
    	clearErrors();
        let valid = true;

        // Clear old errors
        document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');

        fields.forEach(id => {
            const input = document.getElementById(id);
            const errorDiv = document.getElementById(`error-${id}`);

            if (!input || input.value.trim() === '') {
                errorDiv.textContent = 'This field is required.';
                if (valid) input.focus();
                valid = false;
            }
            const phoneRegex = /^\+91-\d{3}-\d{3}-\d{4}$/;
            if( id == 'organizer_phone'){
		        if (input.value && !phoneRegex.test(input.value) ) {
		            errorDiv.textContent = "Invalid format. Use +91-XXX-XXX-XXXX.";
		            if (valid) input.focus();
	                valid = false;
		        } else {
		            errorDiv.textContent = "";

		        }
		    }

        });
        if (venueType.value === 'online') {
            const url = document.getElementById('online_url').value.trim();
            if (!url) {
                showError('error-online_url', 'Online URL is required.');
                valid = false;
            }
        } else if (venueType.value === 'offline') {
            const address = document.getElementById('offline_address').value.trim();
            if (!address) {
                showError('error-offline_address', 'Offline address is required.');
                valid = false;
            }
        }
        if (!valid) {
            e.preventDefault(); // Stop form submission
        }
    });
});
