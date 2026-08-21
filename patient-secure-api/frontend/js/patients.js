let csrfToken = "";
let editingPatientId = null;

const patientForm = document.getElementById("patientForm");
const patientTableBody = document.getElementById("patientTableBody");
const message = document.getElementById("message");
const formTitle = document.getElementById("formTitle");
const submitButton = document.getElementById("submitButton");
const cancelButton = document.getElementById("cancelButton");

// Fetch CSRF token

async function fetchCsrfToken() {
    const response = await fetch("../api/csrf", {
        method: "GET",
        credentials: "include"
    });

    const data = await response.json();

    if (!response.ok) {
        throw new Error("Unable to obtain CSRF token");
    }

    csrfToken = data.csrf_token;
}

// GET

async function loadPatients() {
    const response = await fetch("../api/patients", {
        method: "GET",
        credentials: "include"
    });

    const data = await response.json();

    if (!response.ok) {
        throw new Error(data.message || "Unable to load patients");
    }

    patientTableBody.innerHTML = "";

    data.patients.forEach((patient) => {
        patientTableBody.innerHTML += `
            <tr>
                <td>${patient.id}</td>
                <td>${patient.name}</td>
                <td>${patient.email}</td>
                <td>${patient.phone}</td>
                <td>${patient.diagnosis}</td>
                <td>${patient.created_at}</td>
                <td>
                    <button
                        class="btn btn-sm btn-warning me-1"
                        onclick="editPatient(${patient.id})"
                    >
                        Edit
                    </button>

                    <button
                        class="btn btn-sm btn-danger"
                        onclick="deletePatient(${patient.id})"
                    >
                        Delete
                    </button>
                </td>
            </tr>
        `;
    });
}

// POST / PUT

patientForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const patientData = {
        name: document.getElementById("name").value,
        email: document.getElementById("email").value,
        phone: document.getElementById("phone").value,
        diagnosis: document.getElementById("diagnosis").value
    };

    const url = editingPatientId
        ? `../api/patients/${editingPatientId}`
        : "../api/patients";

    const method = editingPatientId ? "PUT" : "POST";

    const response = await fetch(url, {
        method: method,
        credentials: "include",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": csrfToken
        },
        body: JSON.stringify(patientData)
    });

    const data = await response.json();

    if (!response.ok) {
        showMessage(data.message, "danger");
        return;
    }

    showMessage(data.message, "success");

    patientForm.reset();

    editingPatientId = null;
    formTitle.textContent = "Add Patient";
    submitButton.textContent = "Add Patient";
    cancelButton.classList.add("d-none");

    await loadPatients();
});

// EDIT / PUT

async function editPatient(id) {
    const response = await fetch("../api/patients", {
        method: "GET",
        credentials: "include"
    });

    const data = await response.json();

    const patient = data.patients.find(
        (patient) => patient.id == id
    );

    if (!patient) {
        showMessage("Patient not found", "danger");
        return;
    }

    editingPatientId = id;

    document.getElementById("name").value = patient.name;
    document.getElementById("email").value = patient.email;
    document.getElementById("phone").value = patient.phone;
    document.getElementById("diagnosis").value = patient.diagnosis;

    formTitle.textContent = "Edit Patient";
    submitButton.textContent = "Update Patient";
    cancelButton.classList.remove("d-none");
};

// DELETE

async function deletePatient(id) {
    const response = await fetch(`../api/patients/${id}`, {
        method: "DELETE",
        credentials: "include",
        headers: {
            "X-CSRF-Token": csrfToken
        }
    });

    const data = await response.json();

    if (!response.ok) {
        showMessage(data.message, "danger");
        return;
    }

    showMessage(data.message, "success");

    await loadPatients();
}

cancelButton.addEventListener("click", () => {
    patientForm.reset();

    editingPatientId = null;

    formTitle.textContent = "Add Patient";
    submitButton.textContent = "Add Patient";
    cancelButton.classList.add("d-none");
});

function showMessage(text, type) {
    message.innerHTML = `
        <div class="alert alert-${type}">
            ${text}
        </div>
    `;
}

async function initialize() {
    try {
        await fetchCsrfToken();
        await loadPatients();
    } catch (error) {
        showMessage(error.message, "danger");
    }
}

initialize();