(() => {
    const search = document.querySelector('.expanding-search');
    if (!search) return;

    const input = search.querySelector('.search-input');
    const button = search.querySelector('.search-circle');
    const openSearch = () => {
        search.classList.add('is-open');
        button.setAttribute('aria-expanded', 'true');
        button.setAttribute('aria-label', 'Tìm kiếm');
        input.focus();
    };

    const closeSearch = () => {
        search.classList.remove('is-open');
        button.setAttribute('aria-expanded', 'false');
        button.setAttribute('aria-label', 'Mở tìm kiếm');
    };

    search.addEventListener('submit', (event) => {
        if (!search.classList.contains('is-open')) {
            event.preventDefault();
            openSearch();
            return;
        }
        input.value = input.value.trim();
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            input.value = '';
            closeSearch();
            button.focus();
        }
    });

    document.addEventListener('click', (event) => {
        if (!search.contains(event.target) && input.value.trim() === '') closeSearch();
    });
})();

(() => {
    const button = document.querySelector('.mobile-menu');
    const navigation = document.querySelector('#primary-navigation');
    if (!button || !navigation) return;

    const closeMenu = () => {
        navigation.classList.remove('is-open');
        button.classList.remove('is-open');
        button.setAttribute('aria-expanded', 'false');
        button.setAttribute('aria-label', 'Mở menu');
    };

    button.addEventListener('click', () => {
        const willOpen = !navigation.classList.contains('is-open');
        navigation.classList.toggle('is-open', willOpen);
        button.classList.toggle('is-open', willOpen);
        button.setAttribute('aria-expanded', String(willOpen));
        button.setAttribute('aria-label', willOpen ? 'Đóng menu' : 'Mở menu');
    });

    navigation.addEventListener('click', (event) => {
        if (event.target.closest('a')) closeMenu();
    });

    document.addEventListener('click', (event) => {
        if (!navigation.contains(event.target) && !button.contains(event.target)) closeMenu();
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1000) closeMenu();
    });
})();
