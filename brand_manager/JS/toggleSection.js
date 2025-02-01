// Function to toggle sections
function toggleSection(sectionId) {
    // Hide all sections
    const sections = document.querySelectorAll('.toggle-section');
    sections.forEach(section => {
        section.style.display = 'none';
    });

    // Show the selected section
    const selectedSection = document.getElementById(sectionId);
    if (selectedSection) {
        selectedSection.style.display = 'block';
    }
}
