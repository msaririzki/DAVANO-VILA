

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
    
    const searchInput = selectRoot.querySelector('[data-stable-select-search]');
    if (searchInput) {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input')); // Reset filter
        setTimeout(() => searchInput.focus(), 50); // Focus after a short delay so it renders
    }
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
        
        const searchInput = selectRoot.querySelector('[data-stable-select-search]');
        if (searchInput) {
            searchInput.addEventListener('input', (event) => {
                const query = event.target.value.toLowerCase().trim();
                selectRoot.querySelectorAll('[data-stable-select-option]').forEach((optionButton) => {
                    if (optionButton.dataset.value === '') {
                        // Always show placeholder if query is empty, else hide
                        optionButton.style.display = query === '' ? '' : 'none';
                        return;
                    }
                    const text = optionButton.textContent.toLowerCase();
                    if (text.includes(query)) {
                        optionButton.style.display = '';
                    } else {
                        optionButton.style.display = 'none';
                    }
                });
            });
            
            // Prevent search from closing menu
            searchInput.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeStableSelect(selectRoot);
                    trigger.focus();
                } else if (event.key === 'Enter') {
                    event.preventDefault(); // Don't submit form if inside one
                    // Focus the first visible option
                    const firstVisible = selectRoot.querySelector('[data-stable-select-option]:not([style*="display: none"])');
                    if (firstVisible) firstVisible.focus();
                }
            });
        }

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
                // If there's no search input, focus first option
                if (!searchInput) {
                    selectRoot.querySelector('[data-stable-select-option]')?.focus();
                }
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

function initializeMoneyInputs(root = document) {
    root.querySelectorAll('[data-money-input]:not([data-money-input-ready])').forEach((moneyInput) => {
        const display = moneyInput.querySelector('[data-money-display]');
        const value = moneyInput.querySelector('[data-money-value]');

        if (!display || !value) {
            return;
        }

        moneyInput.dataset.moneyInputReady = 'true';

        const syncValue = () => {
            const digits = display.value.replace(/\D/g, '');
            const numericValue = digits === '' ? 0 : Number.parseInt(digits, 10);
            value.value = String(numericValue);
            display.value = digits === '' ? '' : new Intl.NumberFormat('id-ID').format(numericValue);
        };

        display.addEventListener('input', syncValue);
        display.addEventListener('blur', () => {
            syncValue();
            if (display.value === '') {
                display.value = '0';
            }
        });
        moneyInput.closest('form')?.addEventListener('submit', syncValue);
        syncValue();
    });
}

document.addEventListener('DOMContentLoaded', () => initializeMoneyInputs());

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
