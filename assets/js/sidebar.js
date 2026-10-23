/*
|--------------------------------------------------------------------------
| SIMU Framework
|--------------------------------------------------------------------------
| Sidebar
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function () {

    const tombol = document.getElementById("toggleSidebar");

    const sidebar = document.querySelector(".sidebar");

    if (!tombol || !sidebar) return;

    tombol.addEventListener("click", function () {

        if (window.innerWidth <= 992) {

            sidebar.classList.toggle("show");

        } else {

            sidebar.classList.toggle("hide");

            document.querySelector(".main-wrapper").classList.toggle("full");

        }

    });

});