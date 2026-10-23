/*
|--------------------------------------------------------------------------
| ALERT.JS
|--------------------------------------------------------------------------
| Alert SIMU
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function () {

    /*
    |--------------------------------------------------------------------------
    | Auto Close Alert
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll(".alert-auto").forEach(function(alert){

        setTimeout(function(){

            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);

            bsAlert.close();

        },3000);

    });

    /*
    |--------------------------------------------------------------------------
    | Alert Konfirmasi
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll(".btn-confirm").forEach(function(button){

        button.addEventListener("click",function(e){

            const pesan = this.dataset.message || "Apakah Anda yakin?";

            if(!confirm(pesan)){

                e.preventDefault();

            }

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Success Toast
    |--------------------------------------------------------------------------
    */

    const toastSuccess = document.getElementById("toastSuccess");

    if(toastSuccess){

        const toast = new bootstrap.Toast(toastSuccess);

        toast.show();

    }

    /*
    |--------------------------------------------------------------------------
    | Error Toast
    |--------------------------------------------------------------------------
    */

    const toastError = document.getElementById("toastError");

    if(toastError){

        const toast = new bootstrap.Toast(toastError);

        toast.show();

    }

});