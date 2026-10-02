const ready = (callback) => document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', callback)
    : callback();

ready(() => {
    const passwordToggle = document.querySelector('[data-password-toggle]');
    passwordToggle?.addEventListener('click', () => {
        const input = document.querySelector('#password');
        input.type = input.type === 'password' ? 'text' : 'password';
        passwordToggle.textContent = input.type === 'password' ? '◉' : '◌';
    });

    const finder = document.querySelector('[data-finder-app]');
    if (!finder) return;

    const cards = [...document.querySelectorAll('[data-file-card]')];
    const search = document.querySelector('[data-file-search]');
    const count = document.querySelector('[data-visible-count]');
    const noResults = document.querySelector('[data-no-results]');
    const grid = document.querySelector('[data-file-grid]');

    const filter = () => {
        const term = (search?.value || '').trim().toLowerCase();
        let visible = 0;
        cards.forEach((card) => {
            const matches = !term || card.dataset.name.includes(term);
            card.hidden = !matches;
            if (matches) visible += 1;
        });
        if (count) count.textContent = `${visible} item${visible === 1 ? '' : 's'}`;
        if (noResults) noResults.hidden = visible !== 0;
    };
    search?.addEventListener('input', filter);
    document.addEventListener('keydown', (event) => {
        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            search?.focus();
        }
        if (event.key === 'Escape') document.querySelector('[data-preview-modal]')?.classList.remove('open');
    });

    document.querySelectorAll('[data-view]').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('[data-view]').forEach((item) => item.classList.remove('active'));
            button.classList.add('active');
            grid?.classList.toggle('list-view', button.dataset.view === 'list');
        });
    });
    document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', () => document.querySelector('.sidebar')?.classList.toggle('open'));
    document.querySelector('[data-refresh]')?.addEventListener('click', () => window.location.reload());

    const modal = document.querySelector('[data-preview-modal]');
    const loading = modal?.querySelector('[data-preview-loading]');
    const content = modal?.querySelector('[data-preview-content]');
    const message = modal?.querySelector('[data-preview-message]');
    const title = modal?.querySelector('[data-preview-title]');
    const download = modal?.querySelector('[data-preview-download]');
    const closeModal = () => {
        modal?.classList.remove('open');
        modal?.setAttribute('aria-hidden', 'true');
    };
    document.querySelectorAll('[data-preview-close]').forEach((element) => element.addEventListener('click', closeModal));
    document.querySelectorAll('.preview-trigger').forEach((button) => {
        button.addEventListener('click', async () => {
            modal?.classList.add('open');
            modal?.setAttribute('aria-hidden', 'false');
            title.textContent = button.dataset.fileName;
            loading.hidden = false;
            content.hidden = true;
            message.hidden = true;
            download.href = button.dataset.downloadUrl;
            try {
                const response = await fetch(button.dataset.previewUrl, { headers: { Accept: 'application/json' } });
                const data = await response.json();
                loading.hidden = true;
                if (data.supported) {
                    content.textContent = data.content;
                    content.hidden = false;
                } else {
                    message.textContent = data.message;
                    message.hidden = false;
                }
            } catch {
                loading.hidden = true;
                message.textContent = 'Preview tidak dapat dimuat. Silakan download file ini.';
                message.hidden = false;
            }
        });
    });
});
