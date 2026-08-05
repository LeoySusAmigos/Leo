let audioLeo = null;

function reproducirLeo(audio){

    // Si Leo estaba hablando...
    if(audioLeo){

        audioLeo.pause();
        audioLeo.currentTime = 0;

    }

    audioLeo = new Audio("audios/LeoMotivacion/" + audio + ".mp3");

    audioLeo.play().catch(error=>{

        console.log("No fue posible reproducir el audio.", error);

    });

    return audioLeo;

}

function reproducirLeoAleatorio(prefijo,total){

    const numero = Math.floor(Math.random()*total)+1;

    return reproducirLeo(prefijo + numero);

}


async function dialogoLeo(audio,texto,idBurbuja){

    const burbuja =
    document.getElementById(idBurbuja);

    burbuja.innerHTML="";

    // Tres puntitos
    burbuja.innerHTML=`
        <span class="leo-thinking">
            <span></span>
            <span></span>
            <span></span>
        </span>
    `;

    await esperar(600);

    burbuja.innerHTML="";

    reproducirLeo(audio);

    const palabras=texto.split(" ");

    let html="";

    for(let i=0;i<palabras.length;i++){

        html+=`
        <span class="leo-word nueva">
            ${palabras[i]}
        </span> `;

        burbuja.innerHTML=html;

        const spans=
        burbuja.querySelectorAll(".leo-word");

        spans[i].classList.remove("nueva");
        spans[i].classList.add("activa");

        if(i>0){

            spans[i-1].classList.remove("activa");
            spans[i-1].classList.add("leida");

        }

        await esperar(320);

    }

    const spans=
    burbuja.querySelectorAll(".leo-word");

    spans.forEach(span=>{

        span.classList.remove("activa");
        span.classList.add("leida");

    });

}