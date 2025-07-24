    function showMessage(msg, type) {
        const el = document.getElementById('loginMsg');
        el.innerText = msg;
        el.className = `msg ${type}`;
        el.style.display = 'block';
        setTimeout(() => { el.style.display = 'none'; }, 3000);
    }

    async function loginUser(e) {
        e.preventDefault();
        const form = document.getElementById('loginForm');
        const formData = new FormData(form);

        const res = await fetch('controllers/UserController.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();
        if (data.success) {
            window.location.href = data.redirect; // Dynamic redirect based on role
        } else {
            showMessage(data.message, 'error');
        }
    }