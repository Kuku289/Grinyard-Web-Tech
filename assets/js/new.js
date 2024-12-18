// Base API URL
const API_BASE_URL = '/backend.php'; // Adjust this to your backend file path

// Navigation and Modal Functions
function showSection(sectionId) {
    const sections = document.querySelectorAll('.dashboard-section');
    sections.forEach(section => section.classList.add('hidden'));
    document.getElementById(sectionId).classList.remove('hidden');
}

function openModal(modalId) {
    const modalContainer = document.getElementById('modal-container');
    const modalContent = document.getElementById('modal-content');

    let formHTML = '';
    if (modalId === 'add-officer-modal') {
        formHTML = `
            <h2 class="text-xl font-bold mb-4">Add Extension Officer</h2>
            <form id="add-officer-form" onsubmit="submitOfficer(event)">
                <label class="block mb-2">First Name</label>
                <input type="text" id="officer-first-name" class="border p-2 w-full mb-4" required />
                <label class="block mb-2">Last Name</label>
                <input type="text" id="officer-last-name" class="border p-2 w-full mb-4" required />
                <label class="block mb-2">Email</label>
                <input type="email" id="officer-email" class="border p-2 w-full mb-4" required />
                <label class="block mb-2">Specialization</label>
                <input type="text" id="officer-specialization" class="border p-2 w-full mb-4" />
                <label class="block mb-2">Password</label>
                <input type="password" id="officer-password" class="border p-2 w-full mb-4" required />
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit</button>
            </form>
        `;
    } else if (modalId === 'add-farmer-modal') {
        formHTML = `
            <h2 class="text-xl font-bold mb-4">Add Farmer</h2>
            <form id="add-farmer-form" onsubmit="submitFarmer(event)">
                <label class="block mb-2">First Name</label>
                <input type="text" id="farmer-first-name" class="border p-2 w-full mb-4" required />
                <label class="block mb-2">Last Name</label>
                <input type="text" id="farmer-last-name" class="border p-2 w-full mb-4" required />
                <label class="block mb-2">Email</label>
                <input type="email" id="farmer-email" class="border p-2 w-full mb-4" required />
                <label class="block mb-2">Farm Location</label>
                <input type="text" id="farmer-location" class="border p-2 w-full mb-4" />
                <label class="block mb-2">Farm Type</label>
                <input type="text" id="farmer-type" class="border p-2 w-full mb-4" />
                <label class="block mb-2">Password</label>
                <input type="password" id="farmer-password" class="border p-2 w-full mb-4" required />
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit</button>
            </form>
        `;
    } else if (modalId === 'add-appointment-modal') {
        formHTML = `
            <h2 class="text-xl font-bold mb-4">Add Appointment</h2>
            <form id="add-appointment-form" onsubmit="submitAppointment(event)">
                <label class="block mb-2">Farmer ID</label>
                <input type="number" id="appointment-farmer-id" class="border p-2 w-full mb-4" required />
                <label class="block mb-2">Officer ID</label>
                <input type="number" id="appointment-officer-id" class="border p-2 w-full mb-4" required />
                <label class="block mb-2">Date</label>
                <input type="date" id="appointment-date" class="border p-2 w-full mb-4" required />
                <label class="block mb-2">Description (Optional)</label>
                <textarea id="appointment-description" class="border p-2 w-full mb-4"></textarea>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit</button>
            </form>
        `;
    }

    modalContent.innerHTML = formHTML;
    modalContainer.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modal-container').classList.add('hidden');
}

// Submission Functions
async function submitOfficer(event) {
    event.preventDefault();
    const data = {
        first_name: document.getElementById('officer-first-name').value,
        last_name: document.getElementById('officer-last-name').value,
        email: document.getElementById('officer-email').value,
        specialization: document.getElementById('officer-specialization').value,
        password: document.getElementById('officer-password').value
    };

    await postData(${API_BASE_URL}/officers, data, loadOfficers);
    closeModal();
}

async function submitFarmer(event) {
    event.preventDefault();
    const data = {
        first_name: document.getElementById('farmer-first-name').value,
        last_name: document.getElementById('farmer-last-name').value,
        email: document.getElementById('farmer-email').value,
        location: document.getElementById('farmer-location').value,
        farm_type: document.getElementById('farmer-type').value,
        password: document.getElementById('farmer-password').value
    };

    await postData(${API_BASE_URL}/farmers, data, loadFarmers);
    closeModal();
}

async function submitAppointment(event) {
    event.preventDefault();
    const data = {
        farmer_id: document.getElementById('appointment-farmer-id').value,
        officer_id: document.getElementById('appointment-officer-id').value,
        date: document.getElementById('appointment-date').value,
        description: document.getElementById('appointment-description').value
    };

    await postData(${API_BASE_URL}/appointments, data, loadAppointments);
    closeModal();
}

// CRUD Functions
async function postData(url, data, callback) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            Swal.fire('Success', result.message || 'Operation successful', 'success');
            callback();
        } else {
            Swal.fire('Error', result.error || 'Something went wrong', 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'Unable to complete the operation', 'error');
    }
}

async function loadFarmers() {
    await fetchData(${API_BASE_URL}/farmers, 'farmers-list');
}

async function loadOfficers() {
    await fetchData(${API_BASE_URL}/officers, 'officers-list');
}

async function loadAppointments() {
    await fetchData(${API_BASE_URL}/appointments, 'appointments-list');
}

async function fetchData(url, tableId) {
    try {
        const response = await fetch(url);
        const result = await response.json();
        if (result.success) {
            const tableBody = document.getElementById(tableId);
            tableBody.innerHTML = result.data.map(item => `
                <tr>
                    <td class="px-4 py-2">${item.id}</td>
                    <td class="px-4 py-2">${item.name || item.farmer_name || item.officer_name}</td>
                    <td class="px-4 py-2">${item.email || ''}</td>
                    <td class="px-4 py-2">${item.date || item.specialization || ''}</td>
                    <td class="px-4 py-2">${item.status || ''}</td>
                    <td class="px-4 py-2">
                        <button class="text-blue-500" onclick="editItem(${item.id}, '${tableId}')">Edit</button>
                        <button class="text-red-500" onclick="deleteItem(${item.id}, '${tableId}')">Delete</button>
                    </td>
                </tr>
            `).join('');
        }
    } catch (error) {
        Swal.fire('Error', 'Failed to load data', 'error');
    }
}

async function deleteItem(id, tableId) {
    const url = ${API_BASE_URL}/${tableId.split('-')[0]}/${id};
    try {
        const response = await fetch(url, {
            method: 'DELETE'
        });
        const result = await response.json();
        if (result.success) {
            Swal.fire('Success', 'Item deleted successfully', 'success');
            if (tableId === 'farmers-list') loadFarmers();
            else if (tableId === 'officers-list') loadOfficers();
            else if (tableId === 'appointments-list') loadAppointments();
        } else {
            Swal.fire('Error', result.error || 'Failed to delete item', 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'Unable to delete the item', 'error');
    }
}