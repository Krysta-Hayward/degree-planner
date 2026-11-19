<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Degree Planner')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
</head>

<body>
    <header class="site-header">
        @include('partials.app-logo')
        <nav>
            <a href="/" class="button-links">Home</a>
            <a href="/programs" class="button-links">Programs</a>
            <a href="/courses" class="button-links">Courses</a>
            <a href="/terms" class="button-links">Terms</a>
            <a href="/prerequisites" class="button-links">Prerequisites</a>

            @auth
                <a href="{{ route('classes') }}" class="button-links">Classes</a>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="button-links">Admin Dashboard</a>
                    <a href="{{ route('admin.data') }}" class="button-links">Database Overview</a>
                @elseif (auth()->user()->role === 'advisor')
                    <a href="{{ route('advisor.dashboard') }}" class="button-links">Advisor Dashboard</a>
                @elseif (auth()->user()->role === 'student')
                    <a href="{{ route('student.dashboard') }}" class="button-links">My Degree Plan</a>
                @endif

                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button class="button-links" type="submit">Log Out</button>
                </form>

                <span class="site-header-logged-in">
                    {{ Auth::user()->name }}
                    <span class="user-role">
                        ({{ ucfirst(Auth::user()->role) }})
                    </span>
                </span>

            @else
                <a href="{{ route('login') }}" class="button-links">Log In</a>
            @endauth
        </nav>
        <div>
            <button class="dark-light-mode-toggle theme-toggle" type="button" aria-pressed="false">
                💡 Dark Mode
            </button>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
    <script>
        // Save scroll position before submitting forms
        document.addEventListener('submit', function (event) {
            sessionStorage.setItem('pageScroll', window.scrollY);
        });

        document.addEventListener('click', function (event) {
            const link = event.target.closest(
                '.pagination a, .button-links, .button-links-primary, .button-links-danger'
            );

            if (link && link.tagName === 'A') {
                sessionStorage.setItem('pageScroll', window.scrollY);
            }
        });

        window.addEventListener('load', function () {
            const savedPosition = sessionStorage.getItem('pageScroll');

            if (savedPosition !== null) {
                window.scrollTo(0, parseInt(savedPosition, 10));
                sessionStorage.removeItem('pageScroll');
            }
        });


        document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('confirm-modal');
                const title = document.getElementById('confirm-title');
                const message = document.getElementById('confirm-message');
                const cancel = document.getElementById('confirm-cancel');
                const accept = document.getElementById('confirm-accept');

                let pendingForm = null;
                let pendingSubmitter = null;

                // Display the confirmation modal.
                function showConfirmation(form, button = null) {
                    pendingForm = form;
                    pendingSubmitter = button;

                    const settings = button || form;

                    title.textContent =
                        settings.dataset.confirmTitle || 'Confirm Action';

                    message.textContent =
                        settings.dataset.confirm || form.dataset.confirm;

                    accept.textContent =
                        settings.dataset.confirmButton || 'Confirm';

                    accept.classList.toggle(
                        'confirm-delete',
                        settings.dataset.confirmDanger !== 'false'
                    );

                    modal.showModal();
                }

                // Handle form submissions.
                document.addEventListener('submit', function (event) {
                    const form = event.target;

                    if (!form.matches('[data-confirm]')) {
                        return;
                    }

                    if (form.dataset.confirmed === 'true') {
                        delete form.dataset.confirmed;
                        return;
                    }

                    event.preventDefault();

                    showConfirmation(form, event.submitter);
                });

                // Handle buttons with their own confirmation messages.
                document.addEventListener('click', function (event) {
                    const button = event.target.closest('button[data-confirm]');

                    if (!button || button.type === 'submit') {
                        return;
                    }

                    event.preventDefault();

                    showConfirmation(button.form, button);
                });

                // Cancel.
                cancel.addEventListener('click', function () {
                    modal.close();
                    pendingForm = null;
                    pendingSubmitter = null;
                });

                // Confirm.
                accept.addEventListener('click', function () {
                    if (!pendingForm) return;

                    const form = pendingForm;
                    const submitter = pendingSubmitter;

                    pendingForm = null;
                    pendingSubmitter = null;

                    form.dataset.confirmed = 'true';
                    modal.close();

                    form.requestSubmit(submitter || undefined);
                });

                // Escape key.
                modal.addEventListener('close', function () {
                    pendingForm = null;
                    pendingSubmitter = null;
                });
            });
    </script>

    <dialog id="confirm-modal" class="confirm-modal">
        <div class="confirm-modal-content">
            <h2 id="confirm-title">Confirm Action</h2>

            <p id="confirm-message">
                Are you sure you want to continue?
            </p>

            <div class="confirm-modal-buttons">
                <button type="button" id="confirm-cancel" class="confirm-cancel">
                    Cancel
                </button>

                <button type="button" id="confirm-accept" class="confirm-delete">
                    Delete
                </button>
            </div>
        </div>
    </dialog>


</body>

</html>