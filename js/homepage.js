const cursor = document.querySelector(".custom-cursor");

let mouseX = 0;
let mouseY = 0;

let posX = 0;
let posY = 0;

document.addEventListener("mousemove",(e)=>{

    mouseX = e.clientX;
    mouseY = e.clientY;

});

function animateCursor(){

    posX += (mouseX - posX) * 0.15;
    posY += (mouseY - posY) * 0.15;

    cursor.style.left = posX + "px";
    cursor.style.top = posY + "px";

    requestAnimationFrame(animateCursor);
}

animateCursor();