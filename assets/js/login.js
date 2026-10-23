/*
|--------------------------------------------------------------------------
| LOGIN.JS
|--------------------------------------------------------------------------
| Login SIMU
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function () {

    /*
    |--------------------------------------------------------------------------
    | Focus Username
    |--------------------------------------------------------------------------
    */

    const username = document.getElementById("username");

    if(username){

        username.focus();

    }

    /*
    |--------------------------------------------------------------------------
    | Show Hide Password
    |--------------------------------------------------------------------------
    */

    const btnPassword = document.getElementById("togglePassword");

    if(btnPassword){

        btnPassword.addEventListener("click",function(){

            const password = document.getElementById("password");

            if(password.type === "password"){

                password.type = "text";

                this.innerHTML = '<i class="bi bi-eye-slash"></i>';

            }else{

                password.type = "password";

                this.innerHTML = '<i class="bi bi-eye"></i>';

            }

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Disable Double Login
    |--------------------------------------------------------------------------
    */

    const form = document.getElementById("formLogin");

    if(form){

        form.addEventListener("submit",function(){

            const btn = document.getElementById("btnLogin");

            if(btn){

                btn.disabled = true;

                btn.innerHTML = `
                    <span class="spinner-border spinner-border-sm"></span>
                    Login...
                `;

            }

        });

    }

});