// Stores the ID of the appointment that was recently updated
let highlightedAppointmentId = null;


// SHOW MESSAGE


function showMessage(message, type) {
    const messageBox = document.getElementById("message");

    messageBox.innerHTML = `<div class="alert alert-${type}">
            ${message}
        </div>`;

    setTimeout(() => {
        messageBox.innerHTML = "";
    }, 3000);
}


// LOAD APPOINTMENTS - GET

async function loadAppointments() {
    try {
        const response = await fetch("api.php");

        const result = await response.json();

        console.log(result);

        const tableBody = document.getElementById("appointmentTableBody");

        tableBody.innerHTML = "";

        result.data.forEach((appointment) => {
            const row = `
                <tr class="${String(appointment.id) === String(highlightedAppointmentId)
                    ? "table-warning"
                    : ""
                }">

                    <td>${appointment.id}</td>

                    <td>${appointment.patient_name}</td>

                    <td>${appointment.email}</td>

                    <td>${appointment.mobile}</td>

                    <td>${appointment.appointment_date}</td>

                    <td>${appointment.appointment_time}</td>


                    <td>

                        <select
                            class="form-select form-select-sm"
                            onchange="
                                updateStatus(
                                    ${appointment.id},
                                    this.value
                                )
                            "
                        >

                            <option
                                value="Pending"
                                ${appointment.status === "Pending"
                    ? "selected"
                    : ""
                }
                            >
                                Pending
                            </option>


                            <option
                                value="Confirmed"
                                ${appointment.status === "Confirmed"
                    ? "selected"
                    : ""
                }
                            >
                                Confirmed
                            </option>


                            <option
                                value="Cancelled"
                                ${appointment.status === "Cancelled"
                    ? "selected"
                    : ""
                }
                            >
                                Cancelled
                            </option>

                        </select>

                    </td>


                    <td>

                        <button
                            class="btn btn-sm btn-outline-warning"
                            onclick="
                                editAppointment(
                                    ${appointment.id}
                                )
                            "
                        >
                            Edit
                        </button>


                        <button
                            class="btn btn-sm btn-outline-danger"
                            onclick="
                                deleteAppointment(
                                    ${appointment.id}
                                )
                            "
                        >
                            Delete
                        </button>

                    </td>

                </tr>
            `;

            tableBody.innerHTML += row;
        });

        // Remove row highlight after 2 seconds

        if (highlightedAppointmentId !== null) {
            setTimeout(() => {
                highlightedAppointmentId = null;

                loadAppointments();
            }, 2000);
        }
    } catch (error) {
        console.error("Error loading appointments:", error);

        showMessage("Failed to load appointments", "danger");
    }
}

// Load appointments when page opens

loadAppointments();


// SET MINIMUM APPOINTMENT DATE


const appointmentDateInput = document.getElementById("appointmentDate");

appointmentDateInput.min = new Date().toISOString().split("T")[0];


// CREATE / UPDATE APPOINTMENT
// POST / PUT


document
    .getElementById("appointmentForm")
    .addEventListener("submit",

        async function (event) {
            event.preventDefault();

            // Get form values

            const patientName = document.getElementById("patientName").value.trim();

            const email = document.getElementById("email").value.trim();

            const mobile = document.getElementById("mobile").value.trim();

            const appointmentDate = document.getElementById("appointmentDate").value;

            const appointmentTime = document.getElementById("appointmentTime").value;


            // Required field validation


            if (
                !patientName ||
                !email ||
                !mobile ||
                !appointmentDate ||
                !appointmentTime
            ) {
                showMessage("All fields are required", "danger");

                return;
            }

            // EMAIL VALIDATION


            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailPattern.test(email)) {
                showMessage("Invalid email format", "danger");

                return;
            }


            // MOBILE VALIDATION


            if (!/^\d{10}$/.test(mobile)) {
                showMessage("Mobile number must contain 10 digits", "danger");

                return;
            }


            // DATE VALIDATION


            const today = new Date().toISOString().split("T")[0];

            if (appointmentDate < today) {
                showMessage("Appointment date cannot be in the past", "danger");

                return;
            }


            // GET APPOINTMENT ID


            const appointmentId = document.getElementById("appointmentId").value;


            // CREATE APPOINTMENT OBJECT
            // that is javascript object

            const appointment = {
                patient_name: patientName,

                email: email,

                mobile: mobile,

                appointment_date: appointmentDate,

                appointment_time: appointmentTime,
            };


            // DETERMINE METHOD


            let method = "POST";

            // If ID exists → UPDATE

            if (appointmentId) {
                method = "PUT";

                appointment.id = appointmentId;
            }


            // SEND REQUEST


            try {
                const response = await fetch("api.php", {
                    method: method,

                    headers: {
                        "Content-Type": "application/json",
                    },

                    body: JSON.stringify(appointment),
                    // this converts javascript obj to json obj
                });

                const result = await response.json();

                console.log(result);

                // SUCCESS


                if (result.status === "success") {
                    showMessage(result.message, "success");

                    document.getElementById("appointmentForm").reset();

                    document.getElementById("appointmentId").value = "";

                    document.getElementById("submitButton").textContent =
                        "Book Appointment";

                    document.getElementById("cancelButton").style.display = "none";

                    // Highlight updated appointment

                    if (method === "PUT") {
                        highlightedAppointmentId = appointmentId;
                    }

                    await loadAppointments();
                }


                // API ERROR

                else {
                    showMessage(result.message, "danger");
                }
            } catch (error) {
                console.error("API request error:", error);

                showMessage("API request failed", "danger");
            }
        });


// EDIT APPOINTMENT


async function editAppointment(id) {
    try {
        const response = await fetch("api.php");

        const result = await response.json();

        if (result.status !== "success") {
            showMessage("Unable to load appointments", "danger");

            return;
        }

        const appointment = result.data.find((appointment) => appointment.id == id);

        if (!appointment) {
            showMessage("Appointment not found", "danger");

            return;
        }

        // Store ID

        document.getElementById("appointmentId").value = appointment.id;

        // Fill form

        document.getElementById("patientName").value = appointment.patient_name;

        document.getElementById("email").value = appointment.email;

        document.getElementById("mobile").value = appointment.mobile;

        document.getElementById("appointmentDate").value =
            appointment.appointment_date;

        document.getElementById("appointmentTime").value =
            appointment.appointment_time;

        // Change button

        document.getElementById("submitButton").textContent = "Update Appointment";

        // Show cancel button

        document.getElementById("cancelButton").style.display = "inline-block";

        // Scroll to form

        window.scrollTo({
            top: 0,

            behavior: "smooth",
        });
    } catch (error) {
        console.error("Error loading appointment:", error);

        showMessage("Failed to load appointment", "danger");
    }
}


// DELETE APPOINTMENT


async function deleteAppointment(id) {
    const confirmed = confirm(
        "Are you sure you want to delete this appointment?",
    );

    if (!confirmed) {
        return;
    }

    try {
        const response = await fetch("api.php", {
            method: "DELETE",

            headers: {
                "Content-Type": "application/json",
            },

            body: JSON.stringify({
                id: id,
            }),
        });

        const result = await response.json();

        console.log(result);

        if (result.status === "success") {
            showMessage(result.message, "success");

            await loadAppointments();
        } else {
            showMessage(result.message, "danger");
        }
    } catch (error) {
        console.error("Delete error:", error);

        showMessage("API request failed", "danger");
    }
}


// UPDATE STATUS


async function updateStatus(id, status) {
    try {
        const response = await fetch("api.php", {
            method: "PUT",

            headers: {
                "Content-Type": "application/json",
            },

            body: JSON.stringify({
                id: id,

                status: status,
            }),
        });

        const result = await response.json();

        console.log(result);

        if (result.status === "success") {
            showMessage(result.message, "success");

            await loadAppointments();
        } else {
            showMessage(result.message, "danger");
        }
    } catch (error) {
        console.error("Status update error:", error);

        showMessage("API request failed", "danger");
    }
}


// CANCEL EDIT


document.getElementById("cancelButton").addEventListener("click", function () {
    document.getElementById("appointmentForm").reset();

    document.getElementById("appointmentId").value = "";

    document.getElementById("submitButton").textContent = "Book Appointment";

    document.getElementById("cancelButton").style.display = "none";

    document.getElementById("message").innerHTML = "";
});
