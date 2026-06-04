function toggleCard(element) {
    const content = element.nextElementSibling;
    const arrow = element.querySelector('.arrow');

    if (content.style.display === "block") {
        content.style.display = "none";
        arrow.style.transform = "rotate(0deg)";
    } else {
        content.style.display = "block";
        arrow.style.transform = "rotate(180deg)";
    }
}

function irProgreso() {
    window.location.href = "progreso.php";
}