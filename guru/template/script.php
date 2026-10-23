<?php
/*
|--------------------------------------------------------------------------
| SCRIPT TEMPLATE SIMU
|--------------------------------------------------------------------------
| Seluruh JavaScript dipanggil dari sini
|--------------------------------------------------------------------------
*/
?>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript SIMU -->
<script src="<?= BASE_URL ?>assets/js/app.js"></script>
<script src="<?= BASE_URL ?>assets/js/sidebar.js"></script>
<script src="<?= BASE_URL ?>assets/js/dashboard.js"></script>
<script src="<?= BASE_URL ?>assets/js/form.js"></script>
<script src="<?= BASE_URL ?>assets/js/table.js"></script>
<script src="<?= BASE_URL ?>assets/js/alert.js"></script>
<script src="<?= BASE_URL ?>assets/js/login.js"></script>

<script>

/*
|--------------------------------------------------------------------------
| SIDEBAR TOGGLE
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function () {

    const tombol = document.getElementById("toggleSidebar");
    const sidebar = document.querySelector(".sidebar");

    if (tombol && sidebar) {

        tombol.addEventListener("click", function () {

            sidebar.classList.toggle("hide");

        });

    }

});


/*
|--------------------------------------------------------------------------
| JAM DIGITAL
|--------------------------------------------------------------------------
*/

function updateClock() {

    const sekarang = new Date();

    const jam =
        String(sekarang.getHours()).padStart(2, "0") + ":" +
        String(sekarang.getMinutes()).padStart(2, "0") + ":" +
        String(sekarang.getSeconds()).padStart(2, "0");

    const clock = document.getElementById("clock");
    const jamTopbar = document.getElementById("jam");

    if (clock) {
        clock.innerHTML = jam;
    }

    if (jamTopbar) {
        jamTopbar.innerHTML = jam;
    }

}

updateClock();

setInterval(updateClock, 1000);


/*
|--------------------------------------------------------------------------
| ACTIVE SIDEBAR
|--------------------------------------------------------------------------
*/

const menu = document.querySelectorAll(".sidebar-menu a");

menu.forEach(function(item){

    if(item.href === window.location.href){

        item.classList.add("active");

    }

});

</script>

<?php
/*
|--------------------------------------------------------------------------
| MATHJAX (OPSIONAL)
|--------------------------------------------------------------------------
| Aktif hanya jika halaman mengatur:
| $gunakanMathJax = true;
|--------------------------------------------------------------------------
*/
?>

<?php if (!empty($gunakanMathJax)) : ?>

<script>

window.MathJax = {

    tex: {

        inlineMath: [['\\(','\\)'], ['$', '$']],

        displayMath: [['\\[','\\]'], ['$$','$$']]

    },

    svg: {

        fontCache: 'global'

    }

};

</script>

<script
    id="MathJax-script"
    async
    src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js">
</script>

<?php endif; ?>

</body>

</html>