@extends('layouts.app')
@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')

<div class="card">
    <div class="card-header">
        <span class="card-title">All Users</span>
        <button class="btn btn-primary btn-sm" id="btnAddUser">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add User
        </button>
    </div>

    <table id="usersTable" class="dataTable" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>

{{-- ── Add User Modal ── --}}
<div class="modal-backdrop" id="addModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Add User</span>
            <button class="modal-close" data-close="addModal">&times;</button>
        </div>

        <div id="addErrors" class="alert alert-danger" style="display:none;"></div>

        <form id="addUserForm" novalidate>
            @csrf
            <div class="form-group">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" id="add_first_name" class="form-control" placeholder="John">
            </div>
            <div class="form-group">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" id="add_last_name" class="form-control" placeholder="Doe">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" id="add_email" class="form-control" placeholder="john@example.com">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-close="addModal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add User</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Edit User Modal ── --}}
<div class="modal-backdrop" id="editModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Edit User</span>
            <button class="modal-close" data-close="editModal">&times;</button>
        </div>

        <div id="editErrors" class="alert alert-danger" style="display:none;"></div>

        <form id="editUserForm" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_user_id">
            <div class="form-group">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" id="edit_first_name" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" id="edit_last_name" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" id="edit_email" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-close="editModal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Delete Confirm Modal ── --}}
<div class="modal-backdrop" id="deleteModal">
    <div class="modal-box" style="max-width:380px;">
        <div class="modal-header">
            <span class="modal-title">Delete User</span>
            <button class="modal-close" data-close="deleteModal">&times;</button>
        </div>
        <p style="color:var(--muted);font-size:.9rem;">Are you sure you want to delete this user? This action cannot be undone.</p>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-close="deleteModal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF  = $('meta[name="csrf-token"]').attr('content');
let deleteId = null;

// ── DataTable ────────────────────────────────────────────────────────────────
const table = $('#usersTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: { url: '{{ route("users.data") }}', type: 'GET' },
    columns: [
        { data: 'sr',         orderable: false },
        { data: 'first_name' },
        { data: 'last_name'  },
        { data: 'email'      },
        {
            data: 'actions',
            orderable: false,
            render: function (id) {
                return `
                    <button class="btn btn-edit btn-sm edit-btn" data-id="${id}">Edit</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="${id}">Delete</button>
                `;
            }
        }
    ]
});

// ── Modal helpers ─────────────────────────────────────────────────────────────
function openModal(id)  { $('#' + id).addClass('show'); }
function closeModal(id) { $('#' + id).removeClass('show'); }

$('[data-close]').on('click', function () { closeModal($(this).data('close')); });
$('.modal-backdrop').on('click', function (e) {
    if ($(e.target).hasClass('modal-backdrop')) closeModal($(this).attr('id'));
});

// ── Add user ─────────────────────────────────────────────────────────────────
$('#btnAddUser').on('click', function () {
    $('#addUserForm')[0].reset();
    $('#addErrors').hide();
    openModal('addModal');
});

// jQuery Validation – Add form
$('#addUserForm').validate({
    rules: {
        first_name: { required: true },
        last_name:  { required: true },
        email:      { required: true, email: true }
    },
    messages: {
        first_name: { required: 'First name is required.' },
        last_name:  { required: 'Last name is required.'  },
        email: { required: 'Email is required.', email: 'Enter a valid email.' }
    },
    errorPlacement: function (err, el) { err.insertAfter(el); },
    highlight:   function (el) { $(el).addClass('error'); },
    unhighlight: function (el) { $(el).removeClass('error'); },
    submitHandler: function (form) {
        const data = {
            _token:     CSRF,
            first_name: $('#add_first_name').val(),
            last_name:  $('#add_last_name').val(),
            email:      $('#add_email').val()
        };
        $.post('{{ route("users.store") }}', data)
            .done(function (res) {
                if (res.success) {
                    closeModal('addModal');
                    table.ajax.reload(null, false);
                }
            })
            .fail(function (xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    const msgs = Object.values(errors).flat().join('<br>');
                    $('#addErrors').html(msgs).show();
                }
            });
    }
});

// ── Edit user ─────────────────────────────────────────────────────────────────
$(document).on('click', '.edit-btn', function () {
    const id = $(this).data('id');
    $.get(`/users/${id}/edit`)
        .done(function (user) {
            $('#edit_user_id').val(user.id);
            $('#edit_first_name').val(user.first_name);
            $('#edit_last_name').val(user.last_name);
            $('#edit_email').val(user.email);
            $('#editErrors').hide();
            openModal('editModal');
        });
});

// jQuery Validation – Edit form
$('#editUserForm').validate({
    rules: {
        first_name: { required: true },
        last_name:  { required: true },
        email:      { required: true, email: true }
    },
    messages: {
        first_name: { required: 'First name is required.' },
        last_name:  { required: 'Last name is required.'  },
        email: { required: 'Email is required.', email: 'Enter a valid email.' }
    },
    errorPlacement: function (err, el) { err.insertAfter(el); },
    highlight:   function (el) { $(el).addClass('error'); },
    unhighlight: function (el) { $(el).removeClass('error'); },
    submitHandler: function () {
        const id   = $('#edit_user_id').val();
        const data = {
            _token:     CSRF,
            _method:    'PUT',
            first_name: $('#edit_first_name').val(),
            last_name:  $('#edit_last_name').val(),
            email:      $('#edit_email').val()
        };
        $.post(`/users/${id}`, data)
            .done(function (res) {
                if (res.success) {
                    closeModal('editModal');
                    table.ajax.reload(null, false);
                }
            })
            .fail(function (xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    const msgs = Object.values(errors).flat().join('<br>');
                    $('#editErrors').html(msgs).show();
                }
            });
    }
});

// ── Delete user ───────────────────────────────────────────────────────────────
$(document).on('click', '.delete-btn', function () {
    deleteId = $(this).data('id');
    openModal('deleteModal');
});

$('#confirmDelete').on('click', function () {
    if (!deleteId) return;
    $.ajax({
        url:  `/users/${deleteId}`,
        type: 'POST',
        data: { _token: CSRF, _method: 'DELETE' }
    })
    .done(function (res) {
        if (res.success) {
            closeModal('deleteModal');
            table.ajax.reload(null, false);
            deleteId = null;
        }
    });
});
</script>
@endpush
