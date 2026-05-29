

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

function closeStableSelect(selectRoot) {
    selectRoot.dataset.open = 'false';
    selectRoot.querySelector('[data-stable-select-menu]')?.classList.add('hidden');
    selectRoot.querySelector('[data-stable-select-trigger]')?.setAttribute('aria-expanded', 'false');
}

function openStableSelect(selectRoot) {
    document.querySelectorAll('[data-stable-select][data-open="true"]').forEach((openRoot) => {
        if (openRoot !== selectRoot) {
            closeStableSelect(openRoot);
        }
    });

    selectRoot.dataset.open = 'true';
    selectRoot.querySelector('[data-stable-select-menu]')?.classList.remove('hidden');
    selectRoot.querySelector('[data-stable-select-trigger]')?.setAttribute('aria-expanded', 'true');
}

function syncStableSelect(selectRoot) {
    const nativeSelect = selectRoot.querySelector('[data-stable-select-native]');
    const label = selectRoot.querySelector('[data-stable-select-label]');
    const options = selectRoot.querySelectorAll('[data-stable-select-option]');

    if (!nativeSelect || !label) {
        return;
    }

    const selectedOption = nativeSelect.options[nativeSelect.selectedIndex];
    label.textContent = selectedOption?.textContent?.trim() || '';

    options.forEach((optionButton) => {
        const isSelected = optionButton.dataset.value === nativeSelect.value;
        optionButton.setAttribute('aria-selected', isSelected ? 'true' : 'false');
        optionButton.classList.toggle('bg-emerald-50', isSelected);
        optionButton.classList.toggle('font-black', isSelected);
        optionButton.classList.toggle('text-emerald-800', isSelected);
        optionButton.classList.toggle('font-semibold', !isSelected);
        optionButton.classList.toggle('text-neutral-700', !isSelected && optionButton.dataset.value !== '');
        optionButton.classList.toggle('text-neutral-600', !isSelected && optionButton.dataset.value === '');
        optionButton.querySelector('[data-stable-select-check]')?.classList.toggle('hidden', !isSelected);
    });
}

function initializeStableSelects(root = document) {
    root.querySelectorAll('[data-stable-select]:not([data-stable-select-ready])').forEach((selectRoot) => {
        const nativeSelect = selectRoot.querySelector('[data-stable-select-native]');
        const trigger = selectRoot.querySelector('[data-stable-select-trigger]');

        if (!nativeSelect || !trigger) {
            return;
        }

        selectRoot.dataset.stableSelectReady = 'true';
        selectRoot.dataset.open = 'false';
        syncStableSelect(selectRoot);

        trigger.addEventListener('click', () => {
            if (selectRoot.dataset.open === 'true') {
                closeStableSelect(selectRoot);
            } else {
                openStableSelect(selectRoot);
            }
        });

        trigger.addEventListener('keydown', (event) => {
            if (['ArrowDown', 'Enter', ' '].includes(event.key)) {
                event.preventDefault();
                openStableSelect(selectRoot);
                selectRoot.querySelector('[data-stable-select-option]')?.focus();
            }
        });

        selectRoot.querySelectorAll('[data-stable-select-option]').forEach((optionButton) => {
            optionButton.addEventListener('click', () => {
                nativeSelect.value = optionButton.dataset.value || '';
                nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
                nativeSelect.dispatchEvent(new Event('input', { bubbles: true }));
                syncStableSelect(selectRoot);
                closeStableSelect(selectRoot);
                trigger.focus();
            });

            optionButton.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeStableSelect(selectRoot);
                    trigger.focus();
                }
            });
        });

        nativeSelect.addEventListener('change', () => syncStableSelect(selectRoot));
    });
}

document.addEventListener('DOMContentLoaded', () => initializeStableSelects());

document.addEventListener('click', (event) => {
    document.querySelectorAll('[data-stable-select][data-open="true"]').forEach((selectRoot) => {
        if (!selectRoot.contains(event.target)) {
            closeStableSelect(selectRoot);
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        document.querySelectorAll('[data-stable-select][data-open="true"]').forEach(closeStableSelect);
    }
});
