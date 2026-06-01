const cards = document.querySelectorAll(".accordion-card");

cards.forEach(card => {

    const button = card.querySelector(".accordion-btn");

    button.addEventListener("click", () => {

        cards.forEach(otherCard => {

            if(otherCard !== card){
                otherCard.classList.remove("active");
            }

        });

        card.classList.toggle("active");

    });

});