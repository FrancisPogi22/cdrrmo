@include('partials.authHeader')

<body>
    <div class="wrapper">
        <header class="header-section"></header>
        <section class="auth-section">
            <div class="auth-container">
                <div class="auth-header-desc">
                    <h1>{{ config('app.name') }}</h1>
                    <p>"E-Ligtas, the Disaster Preparedness Web App that effortlessly guides you to safety. Empower
                        yourself and your community – because safety isn't just a decision; it's a shared journey we
                        undertake together."</p>
                </div>
                <div class="auth-form-section">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="auth-email-container">
                            <input type="email" name="email" class="form-control"
                                value="{{ !empty(old('email')) ? old('email') : null }}" placeholder="Email Address"
                                required>
                        </div>
                        <div class="auth-password-container">
                            <input type="password" name="password" id="authPassword" class="form-control"
                                autocomplete="off" placeholder="Password">
                            <i class="bi bi-eye-slash" id="showAuthPassword"></i>
                        </div>
                        <div class="auth-btn-container">
                            <button type="submit" class="btn-login" id="loginBtn">Login</button>
                            <a href="{{ route('resident.eligtas.guideline') }}" class="btn-resident">Continue as
                                resident</a>
                        </div>
                        @if (session('limit'))
                            <p id="error-attempt">The password you've enter is incorrect, please wait for <span
                                    id="time"></span> seconds.</p>
                        @endif
                    </form>
                    <div class="forgot-password-container">
                        <a href="{{ route('recoverAccount') }}">Forgotten password?</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    @include('partials.toastr')
    <script>
        $(document).ready(() => {
            $(document).on('click', '#showAuthPassword', function() {
                const authPassword = $("#authPassword");
                authPassword.attr('type', authPassword.attr('type') == 'password' ? 'text' : 'password');
                $(this).toggleClass("bi-eye-slash bi-eye");
            });

            @if (session('limit'))
                let timeRemaining = {{ session('seconds') }};

                function updateCountdown() {
                    if (timeRemaining <= 0) {
                        $('#error-attempt, #time').text("");
                        $('#loginBtn').prop('disabled', 0);
                    } else {
                        $('#loginBtn').prop('disabled', 1);
                        $('#time').text(timeRemaining);
                        timeRemaining--;
                        setTimeout(updateCountdown, 1000);
                    }
                }

                updateCountdown();
            @endif
        });
    </script>
</body>

</html>
