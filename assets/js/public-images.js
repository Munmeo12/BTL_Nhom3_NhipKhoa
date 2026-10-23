(() => {
    const fallback = new URL('../images/figma/home-card-1.png', document.currentScript.src).href;
    document.querySelectorAll('[data-public-image]').forEach((image) => {
        const recover = () => {
            if (image.src !== fallback) image.src = fallback;
        };
        image.addEventListener('error', recover, { once: true });
        if (image.complete && image.naturalWidth === 0) recover();
    });
})();
