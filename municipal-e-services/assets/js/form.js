function showMessage(msg, type) {
    const el = document.getElementById('appMsg');
    el.innerText = msg;
    el.className = `msg ${type}`;
    el.style.display = 'block';
    el.classList.remove('fade-out');

    setTimeout(() => {
        el.classList.add('fade-out');
        setTimeout(() => { el.style.display = 'none'; }, 500);
    }, 3000);
}

function renderExtraFields(type) {
    const container = document.getElementById('extraFields');
    const uploadInstruction = document.getElementById('uploadInstruction');
    container.innerHTML = '';
    uploadInstruction.innerText = '';

    if (type === 'Barangay Clearance') {
        container.innerHTML += '<label>Address:</label><input type="text" name="extra_fields[address]" required>';
        uploadInstruction.innerHTML = '<p class="instruction-text">Please upload your valid ID below.</p>';
    } else if (type === 'Business Permit / Mayor’s Permit') {
        container.innerHTML += '<label>Business Name:</label><input type="text" name="extra_fields[business_name]" required>';
        container.innerHTML += '<label>Business Address:</label><input type="text" name="extra_fields[business_address]" required>';
        container.innerHTML += '<label>Nature of Business:</label><input type="text" name="extra_fields[nature_of_business]" required>';
        container.innerHTML += '<label>Number of Employees:</label><input type="number" name="extra_fields[number_of_employees]" required>';
        uploadInstruction.innerHTML = '<p class="instruction-text">Please upload your previous business permit and valid ID below.</p>';
    } else if (type === 'Building Permit') {
        container.innerHTML += '<label>Project Name:</label><input type="text" name="extra_fields[project_name]" required>';
        container.innerHTML += '<label>Construction Type:</label><input type="text" name="extra_fields[construction_type]" required>';
        container.innerHTML += '<label>Project Location:</label><input type="text" name="extra_fields[project_location]" required>';
        container.innerHTML += '<label>Estimated Project Cost:</label><input type="number" name="extra_fields[estimated_cost]" required>';
        container.innerHTML += '<label>Contractor Name:</label><input type="text" name="extra_fields[contractor_name]" required>';
        uploadInstruction.innerHMTL = '<p class="instruction-text">Please upload your building plans, lot documents, and valid ID below.</p>';
    } else if (type === 'Permit to Hold an Event / Parade / Rally') {
        container.innerHTML += '<label>Event Name:</label><input type="text" name="extra_fields[event_name]" required>';
        container.innerHTML += '<label>Event Date:</label><input type="date" name="extra_fields[event_date]" required>';
        container.innerHTML += '<label>Venue/Location:</label><input type="text" name="extra_fields[venue]" required>';
        container.innerHTML += '<label>Organizer Name:</label><input type="text" name="extra_fields[organizer_name]" required>';
        container.innerHTML += '<label>Contact Number:</label><input type="text" name="extra_fields[contact_number]" required>';
        uploadInstruction.innerHTML = '<p class="instruction-text">Please upload your event proposal, organizer ID, and all related documents below.</p>';
    } else if (type === 'Barangay Business Clearance') {
        container.innerHTML += '<label>Business Name:</label><input type="text" name="extra_fields[business_name]" required>';
        container.innerHTML += '<label>Business Type:</label><input type="text" name="extra_fields[business_type]" required>';
        container.innerHTML += '<label>Business Address:</label><input type="text" name="extra_fields[business_address]" required>';
        uploadInstruction.innerHTML = '<p class="instruction-text">Please upload your barangay clearance and valid ID below.</p>';
    } else if (type === 'Indigency Certificate') {
        container.innerHTML += '<label>Address:</label><input type="text" name="extra_fields[address]" required>';
        uploadInstruction.innerHTML = '<p class="instruction-text">Please upload your valid ID below.</p>';
    } else if (type === 'Residency Certificate') {
        container.innerHTML += '<label>Address:</label><input type="text" name="extra_fields[address]" required>';
        container.innerHTML += '<label>Years of Residency:</label><input type="number" name="extra_fields[years_of_residency]" required>';
        uploadInstruction.innerHTML = '<p class="instruction-text">Please upload your valid ID and proof of residency below.</p>';
    }
}

async function submitApplication(e) {
    e.preventDefault();
    const form = document.getElementById('applicationForm');
    const formData = new FormData(form);

    const extraFields = {};
    form.querySelectorAll('[name^="extra_fields["]').forEach(input => {
        const key = input.name.match(/extra_fields\[(.*)\]/)[1];
        extraFields[key] = input.value;
    });

    formData.append('extra_data', JSON.stringify(extraFields));

    const files = form.querySelectorAll('input[type="file"]');
    let validFiles = false;
    files.forEach(input => {
        if (input.files.length > 0) validFiles = true;
    });

    if (!validFiles) {
        showMessage('Please upload required documents.', 'error');
        return;
    }

    const res = await fetch('../controllers/ApplicationController.php', {
        method: 'POST',
        body: formData
    });

    const data = await res.json();
    showMessage(data.message, data.success ? 'success' : 'error');
    if (data.success) form.reset();
}