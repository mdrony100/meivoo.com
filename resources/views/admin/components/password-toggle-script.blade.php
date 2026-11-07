<script>
    (function () {
        function buildToggle(input) {
            if (!input || input.dataset.passwordEnhanced === '1' || input.type !== 'password') {
                return;
            }

            input.dataset.passwordEnhanced = '1';
            const wrapper = document.createElement('div');
            wrapper.className = 'input-group password-field';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-outline-secondary password-toggle';
            button.setAttribute('aria-label', 'Show password');
            button.innerHTML = '<i class="bi bi-eye"></i>';

            button.addEventListener('click', function () {
                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                button.classList.toggle('is-active', !visible);
                button.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
                button.innerHTML = visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
            });

            wrapper.appendChild(button);
        }

        function enhance(root) {
            (root || document).querySelectorAll('input[type="password"]').forEach(buildToggle);
        }

        document.addEventListener('DOMContentLoaded', function () {
            enhance(document);
        });

        window.MeivooPasswordToggle = { enhance: enhance };
    })();
</script>
