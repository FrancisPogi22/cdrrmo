@if (auth()->user()->is_disable == 0)
    <div class="modal fade" id="disasterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <header class="modal-label-container">
                    <h1 class="modal-label"></h1>
                </header>
                <div class="modal-body">
                    <form id="disasterForm">
                        @csrf
                        <div class="form-content">
                            <div class="field-container">
                                <label for="name">Disaster Name</label>
                                <input type="text" name="name" class="form-control" autocomplete="off"
                                    placeholder="Enter Disaster Name" id="disasterName">
                            </div>
                            <div class="form-button-container">
                                <button id="submitDisasterBtn"></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
