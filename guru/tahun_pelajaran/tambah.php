<?php

/*
|--------------------------------------------------------------------------
| SIMU
|--------------------------------------------------------------------------
| Tambah Tahun Pelajaran
|--------------------------------------------------------------------------
*/

require_once "../../app/keamanan/session.php";
require_once "../../app/database/database.php";

$page_title = "Tambah Tahun Pelajaran";

require_once "../template/header.php";

?>

<div class="container-fluid">

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-primary text-white">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>

                    <h4 class="mb-1">

                        <i class="bi bi-calendar-plus"></i>

                        Tambah Tahun Pelajaran

                    </h4>

                    <small>

                        Tambahkan Tahun Pelajaran baru yang akan digunakan pada SIMU.

                    </small>

                </div>

                <a href="index.php"

                    class="btn btn-light mt-3 mt-md-0">

                    <i class="bi bi-arrow-left"></i>

                    Kembali

                </a>

            </div>

        </div>

        <div class="card-body">

            <form
                action="proses_tambah.php"
                method="POST"
                autocomplete="off">

               <div class="row">

    <!-- ====================================================== -->
    <!-- Nama Tahun Pelajaran -->
    <!-- ====================================================== -->

    <div class="col-lg-6 mb-4">

        <label for="nama_tahun" class="form-label">

            Nama Tahun Pelajaran

            <span class="text-danger">*</span>

        </label>

        <input
            type="text"
            id="nama_tahun"
            name="nama_tahun"
            class="form-control"
            placeholder="Contoh : 2026/2027"
            maxlength="9"
            pattern="^\d{4}/\d{4}$"
            required>

        <div class="form-text">

            Gunakan format:
            <strong>2026/2027</strong>

        </div>

    </div>

    <!-- ====================================================== -->
    <!-- Status Aktif -->
    <!-- ====================================================== -->

    <div class="col-lg-6 mb-4">

        <label for="aktif" class="form-label">

            Jadikan Tahun Pelajaran Aktif?

            <span class="text-danger">*</span>

        </label>

        <select
            id="aktif"
            name="aktif"
            class="form-select"
            required>

            <option value="Tidak" selected>

                Tidak

            </option>

            <option value="Ya">

                Ya

            </option>

        </select>

        <div class="form-text">

            Jika memilih <strong>Ya</strong>, maka Tahun Pelajaran yang sedang aktif akan dinonaktifkan secara otomatis.

        </div>

    </div>

</div>

<hr>

<!-- ====================================================== -->
<!-- TOMBOL AKSI -->
<!-- ====================================================== -->

<div class="d-flex justify-content-end gap-2 flex-wrap">

    <a href="index.php"
       class="btn btn-secondary">

        <i class="bi bi-arrow-left-circle"></i>

        Kembali

    </a>

    <button
        type="reset"
        class="btn btn-warning">

        <i class="bi bi-arrow-counterclockwise"></i>

        Reset

    </button>

    <button
        type="submit"
        class="btn btn-primary">

        <i class="bi bi-save"></i>

        Simpan

    </button>

</div>

</form>

        </div>

    </div>

</div>

<?php

require_once "../template/footer.php";

?>