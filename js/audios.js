const botones=document.querySelectorAll('.audio-btn');

botones.forEach(boton=>{

    boton.addEventListener('click',()=>{

        const audio=

        boton.nextElementSibling;

        audio.currentTime=0;

        audio.play();

    });

});