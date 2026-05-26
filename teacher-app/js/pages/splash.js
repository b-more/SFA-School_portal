export function renderSplash(container, settings, onDone) {
    const logo = settings?.logo
        ? `<img src="${settings.logo}" alt="Logo" style="width:80px;height:80px;border-radius:16px;object-fit:cover">`
        : '<div class="splash-logo-text">SF</div>';
    container.innerHTML = `
        <div class="splash" id="splash">
            <div class="splash-logo">${logo}</div>
            <div class="splash-name">${settings?.name || 'St. Francis of Assisi'}</div>
            <div class="splash-motto">Teacher Portal</div>
            <div class="splash-loader"></div>
        </div>
    `;
    setTimeout(() => {
        document.getElementById('splash')?.classList.add('splash-fade');
        setTimeout(onDone, 400);
    }, 1500);
}
