// Dark mode toggle.
// The saved choice ("light" or "dark") lives in localStorage. With no saved
// choice, the page follows the operating system setting through CSS.
(function () {
    var KEY = 'theme';
    var root = document.documentElement;

    function savedTheme() {
        try {
            return localStorage.getItem(KEY);
        } catch (e) {
            return null;
        }
    }

    function currentTheme() {
        var saved = root.getAttribute('data-theme');
        if (saved === 'light' || saved === 'dark') {
            return saved;
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function updateButtons() {
        var isDark = currentTheme() === 'dark';
        document.querySelectorAll('.theme-toggle').forEach(function (button) {
            button.textContent = isDark ? 'Light Mode' : 'Dark Mode';
            button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        });
    }

    var initial = savedTheme();
    if (initial === 'light' || initial === 'dark') {
        root.setAttribute('data-theme', initial);
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateButtons();

        document.querySelectorAll('.theme-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                var next = currentTheme() === 'dark' ? 'light' : 'dark';
                root.setAttribute('data-theme', next);
                try {
                    localStorage.setItem(KEY, next);
                } catch (e) {
                    // Storage blocked; the change still applies for this page.
                }
                updateButtons();
            });
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateButtons);
    });
})();
