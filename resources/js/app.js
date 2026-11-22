/**
 * SiteDesk UI behaviour: small, dependency-free progressive enhancements.
 * Every page works without JavaScript; this only makes it smoother.
 */
document.documentElement.classList.add('js');

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Mobile navigation (animated open/close, accessible).
document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
    const menu = document.getElementById(button.getAttribute('aria-controls'));
    if (!menu) return;
    button.addEventListener('click', () => {
        const open = button.getAttribute('aria-expanded') !== 'true';
        button.setAttribute('aria-expanded', String(open));
        menu.classList.toggle('is-open', open);
    });
});

// Header gets a shadow once the page is scrolled.
const header = document.querySelector('[data-sticky-header]');
if (header) {
    const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
}

// Fade sections in as they scroll into view.
const revealed = document.querySelectorAll('[data-reveal]');
if (reducedMotion || !('IntersectionObserver' in window)) {
    revealed.forEach((el) => el.classList.add('is-visible'));
} else {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const delay = Number(entry.target.dataset.revealDelay || 0);
            setTimeout(() => entry.target.classList.add('is-visible'), delay);
            observer.unobserve(entry.target);
        });
    }, { rootMargin: '0px 0px -40px 0px', threshold: 0.05 });
    revealed.forEach((el) => observer.observe(el));
}

// Toasts slide in, then dismiss themselves (or on click).
document.querySelectorAll('[data-toast]').forEach((toast) => {
    const dismiss = () => {
        if (toast.classList.contains('is-leaving')) return;
        toast.classList.add('is-leaving');
        toast.addEventListener('animationend', () => toast.remove(), { once: true });
    };
    toast.querySelector('[data-toast-close]')?.addEventListener('click', dismiss);
    setTimeout(dismiss, 5000);
});

// Loading state on submit: prevents double submissions and gives feedback.
document.querySelectorAll('form[data-loading]').forEach((form) => {
    form.addEventListener('submit', (e) => {
        if (e.defaultPrevented) return;
        const button = form.querySelector('[type="submit"]');
        if (!button || button.disabled) return;
        // Disable on the next tick so the button's own value is still submitted.
        setTimeout(() => {
            button.disabled = true;
            button.dataset.originalHtml = button.innerHTML;
            button.innerHTML = `<svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/>
                <path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                <span>${form.dataset.loading || 'Saving…'}</span>`;
        });
    });
});
// Restore buttons if the user comes back with the browser's back button.
window.addEventListener('pageshow', (e) => {
    if (!e.persisted) return;
    document.querySelectorAll('[data-original-html]').forEach((b) => {
        b.innerHTML = b.dataset.originalHtml;
        b.disabled = false;
    });
});

// Styled confirm dialog instead of the browser's confirm() popup.
const modal = document.getElementById('confirm-modal');
if (modal && typeof modal.showModal === 'function') {
    let pendingForm = null;
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (form.dataset.confirmed === 'yes') return;
            e.preventDefault();
            pendingForm = form;
            modal.querySelector('[data-confirm-title]').textContent = form.dataset.confirmTitle || 'Are you sure?';
            modal.querySelector('[data-confirm-message]').textContent = form.dataset.confirm;
            modal.querySelector('[data-confirm-accept]').textContent = form.dataset.confirmButton || 'Delete';
            modal.showModal();
        });
    });
    modal.querySelector('[data-confirm-accept]').addEventListener('click', () => {
        if (!pendingForm) return;
        pendingForm.dataset.confirmed = 'yes';
        modal.close();
        pendingForm.requestSubmit();
    });
    modal.querySelectorAll('[data-confirm-cancel]').forEach((b) => b.addEventListener('click', () => modal.close()));
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.close(); });
}

// Image upload preview + drag and drop.
document.querySelectorAll('[data-dropzone]').forEach((zone) => {
    const input = zone.querySelector('input[type="file"]');
    const preview = zone.querySelector('[data-preview]');
    const empty = zone.querySelector('[data-empty]');
    const name = zone.parentElement.querySelector('[data-filename]');
    const show = (file) => {
        if (!file || !file.type.startsWith('image/')) return;
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        empty?.classList.add('hidden');
        if (name) name.textContent = `${file.name} · ${(file.size / 1024 / 1024).toFixed(2)} MB`;
    };
    input.addEventListener('change', () => show(input.files[0]));
    ['dragenter', 'dragover'].forEach((ev) => zone.addEventListener(ev, (e) => {
        e.preventDefault();
        zone.classList.add('ring-brand-500', 'bg-brand-50');
    }));
    ['dragleave', 'drop'].forEach((ev) => zone.addEventListener(ev, (e) => {
        e.preventDefault();
        zone.classList.remove('ring-brand-500', 'bg-brand-50');
    }));
    zone.addEventListener('drop', (e) => {
        if (!e.dataTransfer.files.length) return;
        input.files = e.dataTransfer.files;
        show(input.files[0]);
    });
});

// Live character counters (e.g. post excerpt).
document.querySelectorAll('[data-counter]').forEach((field) => {
    const out = document.getElementById(field.dataset.counter);
    const max = Number(field.getAttribute('maxlength'));
    if (!out || !max) return;
    const update = () => {
        out.textContent = `${field.value.length} / ${max}`;
        out.classList.toggle('text-amber-600', field.value.length > max * 0.9);
    };
    field.addEventListener('input', update);
    update();
});

// Textareas grow with their content.
document.querySelectorAll('textarea[data-autosize]').forEach((ta) => {
    const fit = () => { ta.style.height = 'auto'; ta.style.height = `${ta.scrollHeight + 2}px`; };
    ta.addEventListener('input', fit);
    fit();
});

// Reading progress bar on blog posts.
const progress = document.getElementById('reading-progress');
const article = document.querySelector('[data-article]');
if (progress && article) {
    const update = () => {
        const rect = article.getBoundingClientRect();
        const total = article.offsetHeight - window.innerHeight;
        const ratio = total > 0 ? Math.min(Math.max(-rect.top / total, 0), 1) : 1;
        progress.style.transform = `scaleX(${ratio})`;
    };
    update();
    window.addEventListener('scroll', update, { passive: true });
}

// Copy link button.
document.querySelectorAll('[data-copy]').forEach((button) => {
    button.addEventListener('click', async () => {
        const label = button.querySelector('[data-copy-label]');
        try {
            await navigator.clipboard.writeText(button.dataset.copy);
            const old = label.textContent;
            label.textContent = 'Link copied';
            setTimeout(() => (label.textContent = old), 1600);
        } catch { /* clipboard not available */ }
    });
});
