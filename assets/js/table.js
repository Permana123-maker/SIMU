/*
|--------------------------------------------------------------------------
| TABLE.JS
|--------------------------------------------------------------------------
| Fitur Tabel SIMU
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function () {

    /*
    |--------------------------------------------------------------------------
    | Pencarian Tabel
    |--------------------------------------------------------------------------
    */

    const search = document.getElementById("tableSearch");

    if(search){

        search.addEventListener("keyup", function(){

            const keyword = this.value.toLowerCase();

            const rows = document.querySelectorAll("tbody tr");

            rows.forEach(function(row){

                const text = row.innerText.toLowerCase();

                row.style.display = text.includes(keyword) ? "" : "none";

            });

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Pilih Semua Checkbox
    |--------------------------------------------------------------------------
    */

    const checkAll = document.getElementById("checkAll");

    if(checkAll){

        checkAll.addEventListener("change", function(){

            document.querySelectorAll(".check-item").forEach(function(item){

                item.checked = checkAll.checked;

            });

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Konfirmasi Hapus
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll(".btn-delete").forEach(function(button){

        button.addEventListener("click", function(e){

            if(!confirm("Yakin ingin menghapus data ini?")){

                e.preventDefault();

            }

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Nomor Otomatis
    |--------------------------------------------------------------------------
    */

    const nomor = document.querySelectorAll(".nomor");

    nomor.forEach(function(item,index){

        item.textContent = index + 1;

    });

});