/*
|--------------------------------------------------------------------------
| FORM.JS
|--------------------------------------------------------------------------
| Form SIMU
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function () {

    /*
    |--------------------------------------------------------------------------
    | Preview Gambar
    |--------------------------------------------------------------------------
    */

    const imageInput = document.querySelector(".image-preview-input");

    if(imageInput){

        imageInput.addEventListener("change", function(){

            const file = this.files[0];

            if(!file){

                return;

            }

            const reader = new FileReader();

            reader.onload = function(e){

                const preview = document.querySelector(".image-preview");

                if(preview){

                    preview.src = e.target.result;

                    preview.style.display = "block";

                }

            };

            reader.readAsDataURL(file);

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Password
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll(".toggle-password").forEach(function(button){

        button.addEventListener("click", function(){

            const target = document.querySelector(this.dataset.target);

            if(!target){

                return;

            }

            if(target.type === "password"){

                target.type = "text";

                this.innerHTML = '<i class="bi bi-eye-slash"></i>';

            }else{

                target.type = "password";

                this.innerHTML = '<i class="bi bi-eye"></i>';

            }

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Trim Input
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll("input[type='text']").forEach(function(input){

        input.addEventListener("blur", function(){

            this.value = this.value.trim();

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Disable Submit Ganda
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll("form").forEach(function(form){

        form.addEventListener("submit", function(){

            const submit = form.querySelector("[type='submit']");

            if(submit){

                submit.disabled = true;

                submit.innerHTML = `
                    <span class="spinner-border spinner-border-sm"></span>
                    Menyimpan...
                `;

            }

        });

    });

});