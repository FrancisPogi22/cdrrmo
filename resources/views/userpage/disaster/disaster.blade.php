<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <div class="main-content">
            <div class="grid grid-cols-1">
                <div class="grid col-end-1">
                    <div class="text-white text-2xl">
                        <i class="bi bi-tropical-storm p-2 bg-slate-600"></i>
                    </div>
                </div>
                <span class="text-xl font-bold">MANAGE DISASTER INFORMATION</span>
            </div>
            <hr class="mt-4 mb-3">
            @if (auth()->user()->is_disable == 0)
                <div class="create-section">
                    <button class="btn-submit createDisasterData">
                        <i class="bi bi-cloud-plus pr-2"></i>
                        Create Disaster
                    </button>
                </div>
            @endif
            <div class="table-container p-3 shadow-lg rounded-lg">
                <div class="block w-full overflow-auto pb-2">
                    <header class="text-2xl font-semibold mb-3">Disaster Information Table</header>
                    <table class="table disasterTable" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th colspan="2">Disaster Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            @if (auth()->user()->is_disable == 0)
                @include('userpage.disaster.disasterModal')
            @endif
            @include('userpage.changePasswordModal')
        </div>
    </div>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.toastr')
    <script>
        let disasterTable = $('.disasterTable').DataTable({
            language: {
                emptyTable: '<div class="no-data">There are no disaster data available.</div>',
            },
            ordering: false,
            responsive: true,
            processing: false,
            serverSide: true,
            ajax: "{{ route('disaster.display') }}",
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'status',
                    name: 'status',
                    width: '10%'
                },
                {
                    data: 'action',
                    name: 'action',
                    width: '10%',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        @if (auth()->user()->is_disable == 0)
            let disasterId, defaultFormData, status;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let validator = $("#disasterForm").validate({
                rules: {
                    name: 'required'
                },
                messages: {
                    name: 'Please Enter Disaster Name.'
                },
                errorElement: 'span',
                submitHandler: disasterFormHandler
            });

            $(document).on('click', '.createDisasterData', function() {
                $('.modal-header').removeClass('bg-yellow-500').addClass('bg-green-600');
                $('.modal-title').text('Create Disaster Form');
                $('#submitDisasterBtn').removeClass('btn-update').addClass('btn-submit').text('Create');
                $('#operation').val('create');
                $('#disasterModal').modal('show');
            });

            $(document).on('click', '.updateDisaster', function() {
                let {
                    id,
                    name
                } = getRowData(this, disasterTable);
                disasterId = id;
                $('#disasterName').val(name);
                $('.modal-header').removeClass('bg-green-600').addClass('bg-yellow-500');
                $('.modal-title').text('Update Disaster Form');
                $('#submitDisasterBtn').removeClass('btn-submit').addClass('btn-update').text('Update');
                $('#operation').val('update');
                $('#disasterModal').modal('show');
                defaultFormData = $('#disasterForm').serialize();
            });

            $(document).on('click', '.removeDisaster', function() {
                disasterId = getRowData(this, disasterTable).id;
                alterDisasterData('remove');
            });

            $(document).on('change', '.changeDisasterStatus', function() {
                disasterId = getRowData(this, disasterTable).id;
                status = $(this).val();
                alterDisasterData('change');
            });

            $('#disasterModal').on('hidden.bs.modal', function() {
                validator.resetForm();
                $('#disasterForm')[0].reset();
            });

            function alterDisasterData(operation) {
                confirmModal(`Do you want to ${operation} this disaster?`).then((result) => {
                    if (result.isConfirmed) {
                        let url = operation == 'remove' ? "{{ route('disaster.remove', ':disasterId') }}".replace(
                                ':disasterId', disasterId) : "{{ route('disaster.change.status', ':disasterId') }}"
                            .replace(':disasterId', disasterId);

                        $.ajax({
                            type: 'PATCH',
                            data: {
                                status: status
                            },
                            url: url,
                            success: function() {
                                showSuccessMessage(`Disaster successfully ${operation}d.`);
                                disasterTable.draw();
                            },
                            error: function() {
                                showErrorMessage();
                            }
                        });
                    } else {
                        $('.changeDisasterStatus').val('');
                    }
                });
            }

            function disasterFormHandler(form) {
                let operation = $('#operation').val(),
                    url = "",
                    type = "",
                    formData = $(form).serialize();

                url = operation == 'create' ? "{{ route('disaster.create') }}" :
                    "{{ route('disaster.update', 'disasterId') }}".replace('disasterId',
                        disasterId);

                type = operation == 'create' ? "POST" : "PATCH";

                confirmModal(`Do you want to ${operation} this disaster?`).then((result) => {
                    if (result.isConfirmed) {
                        if (operation == 'update' && defaultFormData == formData) {
                            showWarningMessage('No changes were made.');
                            return;
                        }
                        $.ajax({
                            data: formData,
                            url,
                            type,
                            success: function(response) {
                                response.status == 'warning' ? showWarningMessage(response.message) : (
                                    showSuccessMessage(`Disaster successfully ${operation}d.`), $(
                                        '#disasterModal').modal('hide'), disasterTable.draw());
                            },
                            error: function() {
                                showErrorMessage();
                            }
                        });
                    }
                });
            }
        @endif
    </script>
</body>

</html>
