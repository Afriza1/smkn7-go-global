const TABLE_FRAGMENT = 'data-alumni-tracer-study';

document.addEventListener('DOMContentLoaded', () => {
    const section = document.getElementById(TABLE_FRAGMENT);
    if (!section) return;

    const routePath = window.location.pathname;
    const startAtTop = document.querySelector('[data-tracer-statistics-start-top="true"]') !== null;
    const main = section.closest('main');
    const loadingIndicator = document.getElementById('tracer-study-ajax-loading');
    const errorMessage = document.getElementById('tracer-study-ajax-error');
    let activeRequest = null;
    let latestRequest = 0;
    let ajaxUnavailable = false;
    let lastRequestedUrl = new URL(window.location.href);

    if (startAtTop) {
        if (window.location.hash) {
            const initialUrl = new URL(window.location.href);
            initialUrl.hash = '';
            window.history.replaceState(window.history.state, '', initialUrl);
        }
        if (main) {
            main.scrollTop = 0;
            window.addEventListener('pageshow', () => { main.scrollTop = 0; });
        }
    } else if (window.location.hash !== `#${TABLE_FRAGMENT}`) {
        const initialUrl = new URL(window.location.href);
        initialUrl.hash = TABLE_FRAGMENT;
        window.history.replaceState(window.history.state, '', initialUrl);
    }

    async function updateTable(url, { pushHistory = true } = {}) {
        const targetUrl = new URL(url, window.location.href);
        targetUrl.hash = startAtTop ? '' : TABLE_FRAGMENT;
        if (targetUrl.origin !== window.location.origin || targetUrl.pathname !== routePath) return;
        if (ajaxUnavailable) {
            window.location.assign(targetUrl.href);
            return;
        }
        if (pushHistory && targetUrl.href === window.location.href) return;

        activeRequest?.abort();
        const requestController = new AbortController();
        activeRequest = requestController;
        const requestNumber = ++latestRequest;
        lastRequestedUrl = targetUrl;
        const currentMain = section.closest('main');
        const scrollTop = currentMain?.scrollTop ?? 0;
        const previousContent = document.getElementById('tracer-study-table-content');

        section.setAttribute('aria-busy', 'true');
        loadingIndicator?.classList.remove('hidden');
        errorMessage?.classList.add('hidden');

        try {
            const response = await fetch(targetUrl, {
                method: 'GET',
                credentials: 'same-origin',
                signal: requestController.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json, text/html',
                },
            });

            if (requestNumber !== latestRequest) return;
            if (response.redirected && new URL(response.url).pathname !== routePath) {
                window.location.assign(response.url);
                return;
            }
            if (!response.ok) throw new Error(`Request tabel gagal (${response.status}).`);

            const html = await response.text();
            if (requestNumber !== latestRequest) return;
            const documentFragment = new DOMParser().parseFromString(html, 'text/html');
            const nextContent = documentFragment.getElementById('tracer-study-table-content');
            if (!nextContent || !previousContent?.isConnected) {
                throw new Error('Respons tabel tidak lengkap.');
            }

            previousContent.replaceWith(nextContent);
            if (pushHistory) window.history.pushState({ tracerStudyTable: true }, '', targetUrl);
            if (currentMain) currentMain.scrollTop = scrollTop;
            errorMessage?.classList.add('hidden');
            errorMessage?.classList.remove('flex');
            ajaxUnavailable = false;
        } catch (error) {
            if (error.name !== 'AbortError' && requestNumber === latestRequest) {
                ajaxUnavailable = true;
                errorMessage?.classList.remove('hidden');
                errorMessage?.classList.add('flex');
            }
        } finally {
            if (requestNumber === latestRequest) {
                section.setAttribute('aria-busy', 'false');
                loadingIndicator?.classList.add('hidden');
                activeRequest = null;
            }
        }
    }

    function urlFromForm(form) {
        const url = new URL(form.action, window.location.href);
        const query = new URLSearchParams();
        for (const [name, value] of new FormData(form)) {
            if (typeof value === 'string' && value.trim() !== '') query.set(name, value);
        }
        url.search = query.toString();
        url.hash = startAtTop ? '' : TABLE_FRAGMENT;
        return url;
    }

    section.addEventListener('change', (event) => {
        const control = event.target.closest('[data-tracer-filter]');
        if (!control || !section.contains(control)) return;
        const form = control.closest('form[data-tracer-filter-form], form[data-tracer-per-page-form]');
        if (!form) return;
        if (ajaxUnavailable) form.requestSubmit();
        else updateTable(urlFromForm(form));
    });

    section.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-tracer-filter-form]');
        if (!form || ajaxUnavailable) return;
        event.preventDefault();
        updateTable(urlFromForm(form));
    });

    section.addEventListener('click', (event) => {
        const link = event.target.closest('[data-tracer-pagination] a[href]');
        if (ajaxUnavailable || !link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        updateTable(link.href);
    });

    section.addEventListener('click', (event) => {
        if (event.target.closest('[data-tracer-native-fallback]')) {
            window.location.assign(lastRequestedUrl.href);
        }
    });

    window.addEventListener('popstate', () => {
        if (window.location.pathname !== routePath) return;
        if (ajaxUnavailable) window.location.reload();
        else updateTable(window.location.href, { pushHistory: false });
    });
});
