document.querySelectorAll('.password-toggle').forEach((button) => {
    button.addEventListener('click', () => {
        const field = button.closest('.password-field');
        const input = field?.querySelector('input');
        if (!input) return;

        const willShow = input.type === 'password';
        input.type = willShow ? 'text' : 'password';
        button.classList.toggle('is-visible', willShow);
        button.setAttribute('aria-pressed', String(willShow));
        button.setAttribute('aria-label', willShow ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
        input.focus({preventScroll:true});
    });
});
