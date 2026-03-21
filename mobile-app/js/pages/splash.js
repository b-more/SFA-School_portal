export function renderSplash(container, settings, onDone) {
    const logoHtml = settings?.logo
        ? `<img src="${settings.logo}" alt="Logo">`
        : `<span class="splash-logo-text">SF</span>`;

    container.innerHTML = `
        <div class="splash" id="splash">
            <div class="splash-logo">${logoHtml}</div>
            <div class="splash-name">${settings?.name || 'St. Francis of Assisi'}</div>
            <div class="splash-motto">${settings?.motto || 'Excellence in Education'}</div>
            <div class="splash-loader"></div>
        </div>
    `;

    setTimeout(() => {
        const el = document.getElementById('splash');
        if (el) el.classList.add('splash-fade');
        setTimeout(onDone, 400);
    }, 2000);
}
