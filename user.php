<?php
$rootPath = __DIR__;

include $rootPath . '/koneksi.php';
/** @var mysqli $koneksi */

require_once $rootPath . '/app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: /login.php?toast_type=warning&toast_title=Akses Ditolak&toast_message=Silakan login terlebih dahulu");
    exit();
}
?>

<?php include 'partials/dashboard/header.php'; ?>
<?php include 'partials/dashboard/sidebar.php'; ?>

<?php
$queryAdmin  = "SELECT * FROM tb_login";
$resultAdmin = mysqli_query($koneksi, $queryAdmin);
?>

<link rel="stylesheet" href="/style/table.css">

<div class="main-content">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <h2 class="fw-bold mb-0 fs-4">Kelola Akses</h2>
                                <p class="text-muted small mb-0">EduTrack CMS</p>
                            </div>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <button id="btnTambah" class="btn btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Admin
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-2 p-md-3">
                        <table class="table table-hover align-middle mb-0 w-100 dt-card-table" id="table_user">
                            <thead id="table-thead">
                                <tr>
                                    <th class="ps-3">No</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Password</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $noAdmin = 1;
                            if ($resultAdmin && mysqli_num_rows($resultAdmin) > 0):
                                while ($rowAdmin = mysqli_fetch_assoc($resultAdmin)):
                            ?>
                                <tr>
                                    <td class="ps-3"><?= $noAdmin++ ?></td>
                                    <td><?= htmlspecialchars($rowAdmin['username']) ?></td>
                                    <td><?= htmlspecialchars($rowAdmin['email'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($rowAdmin['password']) ?></td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button data-id="<?= $rowAdmin['id'] ?>" class="btn btn-sm btn-warning btn-edit">Edit</button>
                                            <button data-id="<?= $rowAdmin['id'] ?>" class="btn btn-sm btn-danger btn-hapus">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                                endwhile;
                            endif;
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <form id="userForm">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="user_id" name="user_id">
                
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Admin</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="text" class="form-control" id="password" name="password" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSimpan">Save</button>
                <button type="button" class="btn btn-primary" id="btnUpdate" style="display:none;">Update</button>
            </div>
        </form>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="deleteUserModalLabel">Konfirmasi Hapus</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            Apakah Anda yakin ingin menghapus administrator ini?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-danger" id="btnConfirmDelete">Hapus</button>
        </div>
    </div>
  </div>
</div>

<script>
function updateTableTheme() {
    const thead = document.getElementById('table-thead');
    const htmlTheme = document.documentElement.getAttribute('data-bs-theme');
    
    // Update thead class
    if (thead) {
        thead.classList.remove('table-dark', 'table-light');
        thead.classList.add(htmlTheme === 'dark' ? 'table-dark' : 'table-light');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateTableTheme();
});

const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.attributeName === 'data-bs-theme') {
            updateTableTheme();
        }
    });
});
observer.observe(document.documentElement, { attributes: true });

(function waitForJQuery() {
    if (typeof window.jQuery === 'undefined') {
        setTimeout(waitForJQuery, 50);
        return;
    }
    initUserPage(jQuery);
})();

function initUserPage($) {
    var table;
    var dtOptions = {
        responsive: true,
        autoWidth: false,
        dom: '<"dt-toolbar d-flex justify-content-between align-items-center flex-wrap"lf>rt<"d-flex justify-content-between align-items-center flex-wrap"ip>',
        columnDefs: [
            { orderable: false, targets: [4] },
            { responsivePriority: 1, targets: 1 },
            { responsivePriority: 1, targets: 4 }
        ],
        language: {
            search:      "Cari:",
            lengthMenu:  "Tampilkan _MENU_ data",
            info:        "Menampilkan _START_–_END_ dari _TOTAL_ admin",
            infoEmpty:   "Tidak ada data",
            zeroRecords: "Admin tidak ditemukan.",
            paginate: { previous: "‹ Prev", next: "Next ›" }
        },
        order: [],
        initComplete: function () {
            console.log('DataTable initialized successfully!');
            var $tableNode = $(this.api().table().node());
            if (!$tableNode.parent().hasClass('table-scroll-x')) {
                $tableNode.wrap('<div class="table-scroll-x"></div>');
            }
        }
    };

    function initTable() {
        if ($.fn.DataTable.isDataTable('#table_user')) {
            $('#table_user').DataTable().destroy();
        }
        table = $('#table_user').DataTable(dtOptions);
    }

    function tampildata() {
        $.ajax({
            url: "/app/proses/crud-akses/read.php",
            method: "GET",
            success: function(data) {
                if ($.fn.DataTable.isDataTable('#table_user')) {
                    $('#table_user').DataTable().destroy();
                }
                $('#table_user tbody').html(data);
                initTable();
            },
            error: function(xhr, status, error) {
                console.error('Tampildata error:', xhr, status, error);
                toastr.error('Terjadi kesalahan saat memuat data');
            }
        });
    }

    toastr.options = {
        "closeButton": true,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "timeOut": "3000",
        "iconClass": ""
    };

    $(document).ready(function() {
        console.log('Initializing user page...');
        initTable();

        $('#btnTambah').click(function() {
            $('#userModalLabel').text('Tambah Admin');
            $('#userForm')[0].reset();
            $('#user_id').val('');
            $('#userModal').modal('show');
            $('#btnSimpan').show();
            $('#btnUpdate').hide();
        });

        $('#btnSimpan').click(function() {
            var formData = $('#userForm').serialize();
            $.ajax({
                url: "/app/proses/crud-akses/create.php",
                method: "POST",
                data: formData,
                success: function(response) {
                    console.log('Create response:', response);
                    if(response.trim() === 'success') {
                        $('#userModal').modal('hide');
                        tampildata();
                        toastr.success('Admin berhasil disimpan');
                    } else {
                        toastr.error('Gagal menyimpan data: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Create error:', xhr, status, error);
                    toastr.error('Terjadi kesalahan server');
                }
            });
        });

        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "/app/proses/crud-akses/get.php",
                method: "GET",
                data: { id: id },
                success: function(data) {
                    console.log('Get user data:', data);
                    var user = JSON.parse(data);
                    $('#user_id').val(user.id);
                    $('#username').val(user.username);
                    $('#email').val(user.email ?? '');
                    $('#password').val(user.password);

                    $('#userModalLabel').text('Edit Admin');
                    $('#userModal').modal('show');
                    $('#btnSimpan').hide();
                    $('#btnUpdate').show();
                },
                error: function(xhr, status, error) {
                    console.error('Get user error:', xhr, status, error);
                    toastr.error('Gagal memuat data admin');
                }
            });
        });

        $('#btnUpdate').click(function() {
            var formData = $('#userForm').serialize();
            $.ajax({
                url: "/app/proses/crud-akses/update.php",
                method: "POST",
                data: formData,
                success: function(response) {
                    console.log('Update response:', response);
                    if(response.trim() === 'success') {
                        $('#userModal').modal('hide');
                        tampildata();
                        toastr.success('Admin berhasil diupdate');
                    } else {
                        toastr.error('Gagal mengupdate data: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Update error:', xhr, status, error);
                    toastr.error('Terjadi kesalahan server');
                }
            });
        });

        var idUserHapus = null;

        $(document).on('click', '.btn-hapus', function() {
            idUserHapus = $(this).data('id');
            $('#deleteUserModal').modal('show');
        });

        $('#btnConfirmDelete').click(function() {
            if(idUserHapus) {
                $.ajax({
                    url: "/app/proses/crud-akses/delete.php",
                    method: "POST",
                    data: { user_id: idUserHapus },
                    success: function(response) {
                        console.log('Delete response:', response);
                        $('#deleteUserModal').modal('hide');
                        if(response.trim() === 'success') {
                            tampildata();
                            toastr.success('Admin berhasil dihapus');
                        } else {
                            toastr.error('Gagal menghapus data: ' + response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete error:', xhr, status, error);
                        $('#deleteUserModal').modal('hide');
                        toastr.error('Terjadi kesalahan server');
                    }
                });
            }
        });
    });
} 
</script>
<?php include 'partials/dashboard/footer.php'; ?>
