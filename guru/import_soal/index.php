<?php

/*
|--------------------------------------------------------------------------
| IMPORT SOAL
|--------------------------------------------------------------------------
| SIMU - Sistem Informasi Manajemen Ujian Matematika SMP
|--------------------------------------------------------------------------
*/

$page_title = "Import Soal";

require_once __DIR__ . '/../../app/konfigurasi/konfigurasi.php';
require_once __DIR__ . '/../../app/keamanan/session.php';
require_once __DIR__ . '/../../app/database/database.php';

require_once __DIR__ . '/../template/header.php';
require_once __DIR__ . '/../template/sidebar.php';
require_once __DIR__ . '/../template/topbar.php';

?>

<div class="container-fluid">

    <div class="row">

        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">

                        <i class="bi bi-upload"></i>

                        Import Soal

                    </h5>

                </div>

                <div class="card-body">

                    <form
                        action="upload.php"
                        method="POST"
                        enctype="multipart/form-data">

                        <!-- ====================================================== -->
                        <!-- JENJANG SOAL -->
                        <!-- ====================================================== -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">

                                Jenjang Soal

                            </label>

                            <select
                                name="jenjang"
                                class="form-select"
                                required>

                                <option value="">

                                    -- Pilih Jenjang --

                                </option>

                                <option value="VII">

                                    Kelas VII

                                </option>

                                <option value="VIII">

                                    Kelas VIII

                                </option>

                                <option value="IX">

                                    Kelas IX

                                </option>

                            </select>

                        </div>

                        <!-- ====================================================== -->
                        <!-- FILE EXCEL -->
                        <!-- ====================================================== -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">

                                File Excel

                            </label>

                            <input
                                type="file"
                                name="file_excel"
                                class="form-control"
                                accept=".xlsx,.xls"
                                required>

                            <small class="text-muted">

                                Gunakan template Excel resmi SIMU.

                            </small>

                        </div>

                        <!-- ====================================================== -->
                        <!-- TOMBOL -->
                        <!-- ====================================================== -->

                        <div class="d-flex gap-2">

                            <a
                                href="template.php"
                                class="btn btn-success">

                                <i class="bi bi-download"></i>

                                Download Template

                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary">

                                <i class="bi bi-upload"></i>

                                Upload Soal

                            </button>

                            <a
                                href="../dashboard/index.php"
                                class="btn btn-secondary ms-auto">

                                <i class="bi bi-arrow-left"></i>

                                Kembali

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once __DIR__ . '/../template/footer.php';

?>