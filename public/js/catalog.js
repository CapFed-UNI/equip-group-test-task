(() => {
    const listing = document.getElementById('catalog-listing');

    if (!listing) {
        return;
    }

    const load = async (url, push = true) => {
        listing.classList.add('is-loading');

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'text/html',
                },
            });

            if (!response.ok) {
                window.location.href = url;
                return;
            }

            listing.innerHTML = await response.text();

            if (push) {
                history.pushState({ catalog: true }, '', url);
            }
        } catch (error) {
            window.location.href = url;
        } finally {
            listing.classList.remove('is-loading');
        }
    };

    document.addEventListener('click', (event) => {
        const link = event.target.closest('[data-catalog-ajax]');

        if (!link || event.metaKey || event.ctrlKey || event.shiftKey) {
            return;
        }

        event.preventDefault();
        load(link.href);
    });

    document.addEventListener('change', (event) => {
        const form = event.target.closest('[data-catalog-per-page]');

        if (!form) {
            return;
        }

        const params = new URLSearchParams(new FormData(form));
        const url = `${form.action}?${params.toString()}`;
        load(url);
    });

    window.addEventListener('popstate', () => {
        load(window.location.href, false);
    });
})();
