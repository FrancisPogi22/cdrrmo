<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <title>{{ config('app.name') }}</title>
</head>

<body>
    <div class="wrapper">
        @include('sweetalert::alert')
        @include('partials.header')
        @include('partials.sidebar')
        <x-messages />
        <div class="main-content">
            <div class="dashboard-logo pb-4">
                <div class="grid grid-cols-2">
                    <div class="grid col-end-1 mr-4">
                        <div>
                            <i class="bi bi-people text-2xl p-2 bg-slate-700 text-white rounded"></i>
                        </div>
                    </div>
                    <div>
                        <span class="text-xl font-bold tracking-wider">EVACUEE INFORMATION</span>
                    </div>
                </div>
                <hr class="mt-4 bg-black">
                <div class="flex flex-wrap justify-end">
                    <div class="text-white text-sm font-semibold">
                        <button id="returnEvacueeBtn"
                            class="bg-blue-600 hover:bg-blue-700 p-2 mt-3 mr-3 rounded drop-shadow-xl hover:scale-105 duration-100">
                            <i class="bi bi-person-up fs-6 pr-1"></i>
                            Returning Home
                        </button>
                        <button id="recordEvacueeBtn" data-toggle="modal" data-target="#evacueeInfoFormModal"
                            class="bg-green-600 hover:bg-green-700 p-2 mt-2 rounded drop-shadow-xl hover:scale-105 duration-100">
                            <i class="bi bi-person-down fs-6 pr-1"></i>
                            Record Evacuee Info
                        </button>
                    </div>
                    @include('userpage.evacuee.evacueeInfoFormModal')
                </div>
                <div class="table-container mt-3 mb-2 p-3 bg-slate-50 shadow-lg flex rounded-lg">
                    <div class="block w-full overflow-auto">
                        <table class="table data-table table-striped table-light align-middle" width="100%">
                            <thead class="thead-light text-justify">
                                <tr class="table-row">
                                    <th>Id</th>
                                    <th>House Hold #</th>
                                    <th><label for="selectAllCheckBox">Select All</label>
                                        <input type="checkbox" class="w-4 h-4 accent-blue-600" id="selectAllCheckBox">
                                    </th>
                                    <th>Full Name</th>
                                    <th>Sex</th>
                                    <th>Age</th>
                                    <th>Barangay</th>
                                    <th>Date Entry</th>
                                    <th>Date Out</th>
                                    <th>Disaster Type</th>
                                    <th>Disaster Id</th>
                                    <th>Disaster Info</th>
                                    <th>Evacuation Center</th>
                                    <th>4Ps</th>
                                    <th>PWD</th>
                                    <th>Pregnant</th>
                                    <th>Lactating</th>
                                    <th>Student</th>
                                    <th>Working</th>
                                    <th class="w-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            let evacueeTable = $('.data-table').DataTable({
                order: [[1, 'asc']],
                responsive: true,
                processing: false,
                serverSide: true,
                ajax: "{{ route('get.evacuee.info.cswd') }}",
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        visible: false
                    },
                    {
                        data: 'house_hold_number',
                        name: 'house_hold_number',
                        width: '8%'
                    },
                    {
                        data: 'select',
                        name: 'select',
                        orderable: false,
                        searchable: false,
                        selectRow: true
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'sex',
                        name: 'sex'
                    },
                    {
                        data: 'age',
                        name: 'age'
                    },
                    {
                        data: 'barangay',
                        name: 'barangay'
                    },
                    {
                        data: 'date_entry',
                        name: 'date_entry'
                    },
                    {
                        data: 'date_out',
                        name: 'date_out'
                    },
                    {
                        data: 'disaster_type',
                        name: 'disaster_type'
                    },
                    {
                        data: 'disaster_id',
                        name: 'disaster_id',
                        visible: false
                    },
                    {
                        data: 'disaster_info',
                        name: 'disaster_info'
                    },
                    {
                        data: 'evacuation_assigned',
                        name: 'evacuation_assigned'
                    },
                    {
                        data: '4Ps',
                        name: '4Ps',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'PWD',
                        name: 'PWD',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'pregnant',
                        name: 'pregnant',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'lactating',
                        name: 'lactating',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'student',
                        name: 'student',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'working',
                        name: 'working',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function() {
                    $('#selectAllCheckBox').prop('checked', false);

                    this.api().rows().every(function() {
                        let columnName = [
                            '4Ps',
                            'PWD',
                            'pregnant',
                            'lactating',
                            'student',
                            'working'
                        ];

                        for (let i = 0; i < columnName.length; i++) {
                            this.data()[columnName[i]] == 1 ?
                                this.data()[columnName[i]] = 'Yes' :
                                this.data()[columnName[i]] = 'No';
                            $(`td:eq(${i+11})`, this.node()).text(this.data()[columnName[i]]);
                        }

                        if (this.data()['date_out'] !== null) {
                            let checkbox = $(this.node()).find('td:eq(1) input[type="checkbox"]');
                            checkbox.prop('disabled', true);
                            checkbox.hide();
                        }
                    });
                }
            });

            $('#recordEvacueeBtn').click(function() {
                $('.modal-header').
                    removeClass('bg-yellow-500').
                    addClass('bg-green-600');
                $('.modal-title').text('Record Evacuee Information');
                $('#saveEvacueeInfoBtn').
                    removeClass('bg-yellow-500 hover:bg-yellow-600').
                    addClass('bg-green-600 hover:bg-green-700');
                $('#dateFormFieldsContainer').hide();
                $('#evacuationSelectContainer').removeClass('hidden');
                $('#operation').val('record');
                $('#evacueeInfoFormModal').modal('show');
            });

            let evacueeId, defaultFormData;

            $(document).on('click', '.editEvacueeBtn', function() {
                $('.modal-header').removeClass('bg-green-700').addClass('bg-yellow-500');
                $('.modal-title').text('Edit Evacuee Information');
                $('#saveEvacueeInfoBtn').
                    removeClass('bg-green-700 hover:bg-green-800').
                    addClass('bg-yellow-500 hover:bg-yellow-600');
                $('#dateFormFieldsContainer').show();

                let currentRow = $(this).closest('tr');

                if (evacueeTable.responsive.hasHidden()) {
                    currentRow = currentRow.prev('tr');
                }

                let data = evacueeTable.row(currentRow).data();

                evacueeId = data['id'];
                $('input[name="houseHoldNumber"]').val(data['house_hold_number']);
                $('input[name="fullName"]').val(data['full_name']);
                $(`input[name="sex"], option[value="${data['sex']}"]`).prop('selected', true);
                $('input[name="age"]').val(data['age']);
                dateEntryInput.setDate(data['date_entry']);

                if (data['date_out'] == null) {
                    $('#dateOutContainer').hide();
                    $('#dateEntryContainer').removeClass('lg:w-6/12');
                    dateOutInput.setDate('');
                } else {
                    $('#dateOutContainer').show();
                    $('#dateEntryContainer').addClass('lg:w-6/12');
                    dateOutInput.setDate(data['date_out']);
                }

                $(`option[value="${data['barangay']}"]`).prop('selected', true);
                $(`option[value="${data['disaster_type']}"]`).prop('selected', true);
                $(`option[value="${data['disaster_id']}"]`).prop('selected', true);

                if (data['disaster_type'] == 'Typhoon') {
                    $('#flashflood option:selected').prop('selected', false);
                    $('#flashfloodSelectContainer').hide();
                    $('#typhoonSelectContainer').show();
                } else {
                    $('#typhoon option:selected').prop('selected', false);
                    $('#typhoonSelectContainer').hide();
                    $('#flashfloodSelectContainer').show();
                }

                $('input[name="disasterInfo"]').val(data['disaster_info']);

                if ($(`option[value="${data['evacuation_assigned']}"]`).length) {
                    $('#evacuationSelectContainer').removeClass('hidden');
                    $(`option[value="${data['evacuation_assigned']}"]`).prop('selected', true);
                } else {
                    $('#evacuationSelectContainer').addClass('hidden');
                    $('input[name="defaultEvacuationAssigned"]').val(data['evacuation_assigned']);
                }

                dataName = ['4Ps', 'PWD', 'pregnant', 'lactating', 'student', 'working'];
                checkbox = ['fourps', 'pwd', 'pregnant', 'lactating', 'student', 'working'];

                for (let i = 0; i < dataName.length; i++) {
                    data[dataName[i]] == 'Yes' ?
                    $(`input[name="${checkbox[i]}"]`).prop('checked', true) :
                    $(`input[name="${checkbox[i]}"]`).prop('checked', false);
                }

                $('#operation').val('edit');
                $('#evacueeInfoFormModal').modal('show');
                defaultFormData = $('#evacueeInfoForm').serialize();
            });

            $('#disasterType').on('change', function(e) {
                let disasterType = e.target.value;

                if (disasterType == 'Typhoon') {
                    $('#typhoonSelectContainer').show();
                    $('#flashflood').val('');
                    $('#flashfloodSelectContainer').hide();
                } else if (disasterType == 'Flashflood') {
                    $('#typhoonSelectContainer').hide();
                    $('#typhoon').val('');
                    $('#flashfloodSelectContainer').show();
                } else {
                    $('#typhoon').val('');
                    $('#typhoonSelectContainer').hide();
                    $('#flashflood').val('');
                    $('#flashfloodSelectContainer').hide();
                }

                $('input[name="disasterInfo"]').val('');
            });

            $("#typhoon").on('change', function(e) {
                let selectedText = $('#typhoon option:selected').text();
                $('input[name="disasterInfo"]').val(selectedText.trim());
            });

            $("#flashflood").change(function() {
                let selectedText = $('#flashflood option:selected').text();
                $('input[name="disasterInfo"]').val(selectedText.trim());
            });

            function datePicker(id) {
                return flatpickr(id, {
                    enableTime: true,
                    allowInput: true,
                    timeFormat: "h:i K",
                    dateFormat: "D, M j, Y h:i K",
                    minuteIncrement: 1,
                    secondIncrement: 1,
                    position: "below center",
                });
            }

            let dateEntryInput = datePicker("#dateEntry"), dateOutInput = datePicker("#dateOut");

            let validator = $("#evacueeInfoForm").validate({
                rules: {
                    houseHoldNumber: {
                        required: true,
                        min: 1,
                    },
                    fullName: {
                        required: true,
                    },
                    sex: {
                        required: true,
                    },
                    age: {
                        required: true,
                        min: 1,
                    },
                    dateEntry: {
                        required: true,
                    },
                    dateOut: {
                        required: true,
                    },
                    barangay: {
                        required: true,
                    },
                    disasterType: {
                        required: true,
                    },
                    typhoon: {
                        required: true,
                    },
                    flashflood: {
                        required: true,
                    },
                    evacuationAssigned: {
                        required: true,
                    },
                },
                messages: {
                    houseHoldNumber: {
                        required: 'Please enter House Hold Number.',
                        min: 'Please enter a number greater than zero.',
                    },
                    fullName: {
                        required: 'Please enter Full Name.',
                    },
                    sex: {
                        required: 'Please select Sex.',
                    },
                    age: {
                        required: 'Please enter Age.',
                        min: 'Please enter a number greater than zero.',
                    },
                    dateEntry: {
                        required: 'Please select Date Entry.',
                    },
                    dateOut: {
                        required: 'Please select Date Out.',
                    },
                    barangay: {
                        required: 'Please select Barangay.',
                    },
                    disasterType: {
                        required: 'Please select Disaster.',
                    },
                    typhoon: {
                        required: 'Please select Typhoon.',
                    },
                    flashflood: {
                        required: 'Please select Flashflood.',
                    },
                    evacuationAssigned: {
                        required: 'Please select Evacuation Center.',
                    },
                },
                errorElement: 'span',
                submitHandler: formSubmitHandler,
            });

            function formSubmitHandler(form, e) {
                let operation = $('#operation').val(),
                    url = "",
                    type = "",
                    hideModal = false,
                    formData = $(form).serialize(),
                    modal = $('#evacueeInfoFormModal');

                if (operation == 'record') {
                    url = "{{ route('record.evacuee.cswd') }}";
                    type = "POST";
                } else {
                    url = "{{ route('update.evacuee.info.cswd', 'evacueeId') }}".replace('evacueeId', evacueeId);
                    type = "PUT";
                    hideModal = true;
                }

                confirmModal(`Do you want to ${operation} this evacuee info?`).then((result) => {
                    if (result.isConfirmed) {
                        if (operation == 'edit' && defaultFormData == formData) {
                            modal.modal('hide');
                            messageModal(
                                'Info',
                                'No changes were made.',
                                'info',
                                '#B91C1C',
                            );
                            return;
                        }
                        $.ajax({
                            data: formData,
                            url: url,
                            type: type,
                            dataType: 'json',
                            success: function(response) {
                                if (response.condition == 0) {
                                    messageModal(
                                        'Warning',
                                        'Please fill up the form correctly.',
                                        'warning',
                                        '#FFDF00',
                                    );
                                } else {
                                    if (hideModal) {
                                        modal.modal('hide');
                                    }

                                    evacueeTable.draw();

                                    operation == 'record' ?
                                        operation += "ed new" :
                                        operation += "ed the";

                                    messageModal(
                                        'Success',
                                        `Successfully ${operation} evacuee info.`,
                                        'success',
                                        '#3CB043',
                                    );
                                }
                            },
                            error: function(jqXHR, error, data) {
                                if (hideModal) {
                                        modal.modal('hide');
                                }

                                messageModal(
                                    jqXHR.status,
                                    data,
                                    'error',
                                    '#B91C1C',
                                );
                            }
                        });
                    }
                });
            }

            $('#evacueeInfoFormModal').on('hidden.bs.modal', function() {
                validator.resetForm();
                $('#evacueeInfoForm').trigger("reset");
            });

            $('#selectAllCheckBox').click(function() {
                let checkBox = $('.data-table tbody tr td input[type="checkbox"]');

                $(this).is(':checked') ?
                    checkBox.each(function() {
                        if (!$(this).is(':disabled'))
                            $(this).prop('checked', true);
                    }) :
                    checkBox.each(function() {
                        if (!$(this).is(':disabled'))
                            $(this).prop('checked', false);
                    });
            });

            $('#returnEvacueeBtn').on('click', function() {
                let id = [],
                    checked = $('.data-table tbody tr td input[type="checkbox"]:checked');

                if (checked.length > 0) {
                    $(checked).each(function() {
                        id.push($(this).val());
                    });
                }

                if (id.length > 0) {
                    message = "";

                    id.length == 1 ?
                        message = "Is this evacuee going back home?" :
                        message = "Are these evacuees going back to their homes?";

                    confirmModal(message).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                data: {
                                    evacueeIds: id,
                                },
                                url: "{{ route('update.evacuee.dateout.cswd') }}",
                                type: "PATCH",
                                dataType: 'json',
                                success: function(response) {
                                    evacueeTable.draw();

                                    messageModal(
                                        'Success',
                                        'Successfully update the evacuee/s date out.',
                                        'success',
                                        '#3CB043',
                                    );
                                },
                                error: function(jqXHR, error, data) {
                                    $('#selectAllCheckBox').prop('checked', false);

                                    messageModal(
                                        jqXHR.status,
                                        data,
                                        'error',
                                        '#B91C1C',
                                    );
                                }
                            });
                        }
                    });
                } else {
                    $('#selectAllCheckBox').prop('checked', false);

                    messageModal(
                        'Warning',
                        'Please select at least one evacuee.',
                        'warning',
                        '#FFDF00',
                    );
                }
            });
        });
    </script>
</body>

</html>