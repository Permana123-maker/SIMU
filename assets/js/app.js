/*
|--------------------------------------------------------------------------
| APP.JS
|--------------------------------------------------------------------------
| JavaScript Global SIMU
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function () {

    console.log("SIMU Loaded");

    /* ==========================================
       Bootstrap Tooltip
    ========================================== */

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');

    [...tooltipTriggerList].forEach(function (tooltipTriggerEl) {

        new bootstrap.Tooltip(tooltipTriggerEl);

    });

    /* ==========================================
       Bootstrap Popover
    ========================================== */

    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');

    [...popoverTriggerList].forEach(function (popoverTriggerEl) {

        new bootstrap.Popover(popoverTriggerEl);

    });

    /* ==========================================
       Auto Close Alert
    ========================================== */

    const alerts = document.querySelectorAll(".alert-auto");

    alerts.forEach(function(alert){

        setTimeout(function(){

            alert.classList.remove("show");

            alert.classList.add("fade");

            setTimeout(function(){

                alert.remove();

            },500);

        },3000);

    });

    /* ==========================================
       Konfirmasi Logout
    ========================================== */

    document.querySelectorAll(".btn-logout").forEach(function(btn){

        btn.addEventListener("click",function(e){

            if(!confirm("Apakah Anda yakin ingin logout?")){

                e.preventDefault();

            }

        });

    });

    /* ==========================================
       Disable Double Click Button
    ========================================== */

    document.querySelectorAll(".btn-submit").forEach(function(btn){

        btn.addEventListener("click",function(){

            btn.disabled = true;

            btn.innerHTML = `
                <span class="spinner-border spinner-border-sm"></span>
                Memproses...
            `;

            btn.closest("form").submit();

        });

    });

    /* ==========================================
       Scroll To Top
    ========================================== */

    const btnTop = document.getElementById("scrollTop");

    if(btnTop){

        window.addEventListener("scroll",function(){

            if(window.scrollY > 250){

                btnTop.style.display="block";

            }else{

                btnTop.style.display="none";

            }

        });

        btnTop.addEventListener("click",function(){

            window.scrollTo({

                top:0,

                behavior:"smooth"

            });

        });

    }

});