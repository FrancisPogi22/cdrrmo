@if (Auth::user()->user_role == '1')
    <div class="modal fade" id="editDisaster" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red-900">
                    <h1 class="modal-title fs-5 text-center text-white" id="exampleModalLabel">{{ config('app.name') }}
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editDisasterForm">
                        <input type="text" name="disasterId" id="disasterId" hidden>
                        @csrf
                        <div class="mb-3">
                            <label for="disaster_type" class="flex items-center justify-center">Disaster Type</label>
                            <input type="text" name="disaster_type" id="disaster_type" class="form-control"
                                autocomplete="off">
                            <span class="text-danger error-text disaster_type_error"></span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="cancelUpdate"
                                class="bg-slate-700 text-white p-2 py-2 rounded shadow-lg hover:shadow-xl transition duration-200"
                                data-bs-dismiss="modal">Close</button>
                            <button type="button" id="updateDisasterBtn"
                                class="bg-red-700 text-white p-2 py-2 rounded shadow-lg hover:shadow-xl transition duration-200">Update
                                Disaster</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
