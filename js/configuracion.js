function toggleCard(element) {
    const content = element.nextElementSibling;

    if (content.style.display === "block") {
        content.style.display = "none";
    } else {
        content.style.display = "block";
    }
}

function irProgreso() {
    window.location.href = "progreso.php";
} 