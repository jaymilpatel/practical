/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
document.addEventListener("DOMContentLoaded", function () {
        const startInput = document.getElementById('start');
        const endInput = document.getElementById('end');

        function pad(num) {
            return num.toString().padStart(2, '0');
        }

        const now = new Date();
        const minDateTime = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
        startInput.min = minDateTime;
        endInput.min = minDateTime;

        function validateNoWeekend(input) {
            input.addEventListener('change', function () {
                const date = new Date(this.value);
                const day = date.getDay();
                const fieldId = this.id;
                const errorEl = document.getElementById("error-" + fieldId);
                errorEl.textContent = '';

                if (day === 0 || day === 6) {
                    errorEl.textContent = "Weekends (Saturday and Sunday) are not allowed.";
                    this.value = '';
                    return;
                } else if (date < new Date()) {
                    errorEl.textContent = "You cannot choose a past date.";
                    this.value = '';
                    return;
                }

                if (fieldId === 'start') {
                    const startDate = new Date(this.value);
                    const minEndDate = `${startDate.getFullYear()}-${pad(startDate.getMonth() + 1)}-${pad(startDate.getDate())}T${pad(startDate.getHours())}:${pad(startDate.getMinutes())}`;
                    endInput.min = minEndDate;
                }
            });
        }

        validateNoWeekend(startInput);
        validateNoWeekend(endInput);

        const form = document.getElementById("eventForm");
        const venueType = document.getElementById("venue_type");
        const onlineInput = document.getElementById("online_input");
        const offlineInput = document.getElementById("offline_input");

        venueType.addEventListener("change", function () {
            onlineInput.style.display = this.value === "online" ? "block" : "none";
            offlineInput.style.display = this.value === "offline" ? "block" : "none";
        });
        const requiredFields = [
		    { name: "title", label: "Event Title" },
		    { name: "start", label: "Start Date & Time" },
		    { name: "end", label: "End Date & Time" },
		    { name: "organizer_name", label: "Organizer Name" },
		    { name: "organizer_email", label: "Organizer Email" },
		    { name: "organizer_phone", label: "Organizer Phone" },
		    { name: "venue_type", label: "Venue Type" },
		    { name: "price", label: "Price" },
		    { name: "image", label: "Event Image" }
		];
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const data = new FormData(form);
            if (data.get("honeypot")) return;

            document.querySelectorAll(".error").forEach(el => el.textContent = "");
            let hasError = false;

            const setError = (id, message) => {
                const el = document.getElementById("error-" + id);
                if (el) el.textContent = message;
                hasError = true;
            };
            requiredFields.forEach(field => {
		    	const value = data.get(field.name);

			    // If it's a file input, check that a file was selected
			    if (field.name === "image") {
			        if (!value || !(value instanceof File) || value.size === 0) {
			            setError(field.name, field.label + " is required.");
			        }
			    } else {
			        // For all text-based fields
			        if (!value || typeof value !== "string" || value.trim() === "") {
			            setError(field.name, field.label + " is required.");
			        }
			    }
			});
            const start = new Date(data.get("start"));
            if (start < new Date()) {
                setError("start", "Start date must be in the future.");
            }
            if ([0, 6].includes(start.getDay())) {
                setError("start", "Weekends are not allowed.");
            }

            const phone = data.get("organizer_phone") || "";
            if (!/\\+91-\\d{3}-\\d{3}-\\d{4}/.test(phone)) {
                setError("organizer_phone", "Invalid phone format.");
            }

            const venue = data.get("venue_type");
            console.log(venue)
            if (!venue) {
                setError("venue_type", "Please select a venue type.");
            } else if (venue === "online" && !data.get("online_url")) {
                setError("online_url", "Online event URL is required.");
            } else if (venue === "offline" && !data.get("offline_address")) {
                setError("offline_address", "Physical address is required.");
            }

            const image = data.get("image");
            if (!image || !['image/png', 'image/jpeg'].includes(image.type) || image.size > 2 * 1024 * 1024) {
                setError("image", "Upload PNG/JPEG image under 2MB.");
            }

            if (hasError) return;

            const submitData = async (attempt = 1) => {
                try {
                    const response = await fetch('/practical/wp-json/event/v1/submit', {
                        method: 'POST',
                        body: data,
                    });

                    if (!response.ok) {
                        if (attempt < 3) return submitData(attempt + 1);
                        throw new Error("Server error after multiple attempts");
                    }

                    const result = await response.json();

                    if (result.success) {
                        alert("Event Submitted Successfully!");
                        form.reset();
                        onlineInput.style.display = "none";
                        offlineInput.style.display = "none";
                    } else {
                        alert("Submission failed: " + (result.message || "Unknown error"));
                    }

                } catch (err) {
                    if (attempt < 3) {
                        submitData(attempt + 1);
                    } else {
                        alert("Submission failed after 3 attempts. Please try again later.");
                    }
                }
            };

            submitData();
        });
    });
