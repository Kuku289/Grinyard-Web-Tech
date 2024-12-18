document.addEventListener('DOMContentLoaded', () => {
    const farmerForm = document.getElementById('farmerForm');
    const farmersTableBody = document.getElementById('farmersTableBody');

    // Load farmers from localStorage on page load
    loadFarmers();

    farmerForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form values
        const firstName = document.getElementById('firstName').value;
        const lastName = document.getElementById('lastName').value;
        const email = document.getElementById('email').value;
        const farmLocation = document.getElementById('farmLocation').value;
        const farmType = document.getElementById('farmType').value;

        // Create farmer object
        const farmer = {
            id: Date.now(), // Unique ID
            firstName,
            lastName,
            email,
            farmLocation,
            farmType
        };

        // Add farmer to localStorage
        addFarmer(farmer);

        // Reset form
        farmerForm.reset();
    });

    function addFarmer(farmer) {
        // Get existing farmers or initialize empty array
        let farmers = JSON.parse(localStorage.getItem('farmers') || '[]');
        
        // Add new farmer
        farmers.push(farmer);
        
        // Save back to localStorage
        localStorage.setItem('farmers', JSON.stringify(farmers));

        // Refresh the table
        loadFarmers();
    }

    function loadFarmers() {
        // Clear existing table rows
        farmersTableBody.innerHTML = '';

        // Get farmers from localStorage
        const farmers = JSON.parse(localStorage.getItem('farmers') || '[]');

        // Populate table
        farmers.forEach(farmer => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${farmer.firstName} ${farmer.lastName}</td>
                <td>${farmer.email}</td>
                <td>${farmer.farmLocation}</td>
                <td>${farmer.farmType}</td>
                <td>
                    <button onclick="deleteFarmer(${farmer.id})" class="delete-btn">Delete</button>
                </td>
            `;
            farmersTableBody.appendChild(row);
        });
    }

    // Expose deleteFarmer to global scope for inline onclick
    window.deleteFarmer = function(id) {
        // Get existing farmers
        let farmers = JSON.parse(localStorage.getItem('farmers') || '[]');
        
        // Remove farmer with matching id
        farmers = farmers.filter(farmer => farmer.id !== id);
        
        // Save back to localStorage
        localStorage.setItem('farmers', JSON.stringify(farmers));

        // Refresh the table
        loadFarmers();
    };
});