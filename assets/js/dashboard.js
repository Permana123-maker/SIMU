/*
|--------------------------------------------------------------------------
| DASHBOARD.JS
|--------------------------------------------------------------------------
| Dashboard SIMU
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function () {

    /*
    |--------------------------------------------------------------------------
    | Animasi Card Statistik
    |--------------------------------------------------------------------------
    */

    const cards = document.querySelectorAll(".stat-card");

    cards.forEach(function(card, index){

        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";

        setTimeout(function(){

            card.style.transition = "all .4s ease";

            card.style.opacity = "1";
            card.style.transform = "translateY(0)";

        }, index * 150);

    });

    /*
    |--------------------------------------------------------------------------
    | Efek Hover Icon
    |--------------------------------------------------------------------------
    */

    cards.forEach(function(card){

        card.addEventListener("mouseenter", function(){

            const icon = card.querySelector("i");

            if(icon){

                icon.style.transform = "scale(1.15)";
                icon.style.transition = ".3s";

            }

        });

        card.addEventListener("mouseleave", function(){

            const icon = card.querySelector("i");

            if(icon){

                icon.style.transform = "scale(1)";

            }

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Greeting
    |--------------------------------------------------------------------------
    */

    const greeting = document.getElementById("greeting");

    if(greeting){

        const jam = new Date().getHours();

        let pesan = "";

        if(jam >= 4 && jam < 10){

            pesan = "Selamat Pagi";

        }else if(jam >= 10 && jam < 15){

            pesan = "Selamat Siang";

        }else if(jam >= 15 && jam < 18){

            pesan = "Selamat Sore";

        }else{

            pesan = "Selamat Malam";

        }

        greeting.innerHTML = pesan;

    }

});