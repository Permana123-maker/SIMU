<?php

/*
|--------------------------------------------------------------------------
| Session & Database
|--------------------------------------------------------------------------
*/

require_once "../../../app/keamanan/session.php";
require_once "../../../app/database/database.php";


/*
|--------------------------------------------------------------------------
| Ambil Paket Soal
|--------------------------------------------------------------------------
*/


$stmt = $pdo->prepare("

    SELECT

        id_paket,
        nama_paket,
        jenjang


    FROM paket_soal


    WHERE deleted_at IS NULL


    ORDER BY jenjang, nama_paket


");


$stmt->execute();


$paketSoal = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>


<!DOCTYPE html>

<html lang="id">


<head>


<meta charset="UTF-8">


<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>
Import Soal | SIMU
</title>


<?php require_once "../../../template/head.php"; ?>


</head>


<body>


<div class="wrapper">


<?php require_once "../../../template/sidebar.php"; ?>


<div class="main">


<?php require_once "../../../template/topbar.php"; ?>


<div class="container-fluid py-4">


<div class="d-flex justify-content-between align-items-center mb-4">


<div>

<h3>
Import Soal Excel
</h3>


<small class="text-muted">

Masukkan soal melalui file Excel

</small>


</div>



<a href="index.php"

class="btn btn-secondary">


<i class="bi bi-arrow-left"></i>

Kembali


</a>


</div>



<div class="card shadow-sm">


<div class="card-header">

<strong>
Form Import Soal
</strong>

</div>



<div class="card-body">



<form

action="proses_import.php"

method="POST"

enctype="multipart/form-data">



<div class="mb-3">


<label class="form-label">

Paket Soal

</label>



<select

name="id_paket"

class="form-select"

required>



<option value="">

-- Pilih Paket Soal --

</option>



<?php foreach($paketSoal as $paket): ?>


<option value="<?= $paket['id_paket']; ?>">


<?= htmlspecialchars($paket['jenjang']); ?>

-

<?= htmlspecialchars($paket['nama_paket']); ?>


</option>


<?php endforeach; ?>


</select>


</div>

                <!-- File Excel -->

                <div class="mb-3">


                    <label class="form-label">

                        File Excel Soal

                    </label>


                    <input

                        type="file"

                        name="file_excel"

                        class="form-control"

                        accept=".xlsx,.xls"

                        required>


                    <small class="text-muted">

                        Gunakan format template Excel SIMU.

                    </small>


                </div>




                <!-- Download Template -->


                <div class="mb-3">


                    <a href="template_import.xlsx"

                       class="btn btn-outline-success">


                        <i class="bi bi-download"></i>

                        Download Template Excel


                    </a>


                </div>





                <!-- Tombol -->


                <div class="d-flex justify-content-end gap-2 mt-4">



                    <a href="index.php"

                       class="btn btn-secondary">


                        <i class="bi bi-x-circle"></i>

                        Batal


                    </a>




                    <button

                        type="submit"

                        class="btn btn-primary">


                        <i class="bi bi-upload"></i>

                        Import Soal


                    </button>



                </div>



            </form>



        </div>


    </div>


</div>



<?php require_once "../../../template/footer.php"; ?>


</div>


</div>



</body>


</html>