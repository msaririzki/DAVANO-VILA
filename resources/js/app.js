

import Alpine from 'alpinejs';
import './payment-proof-reader';

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

function parseCalendarDate(value) {
    if (!value) return null;
    const parts = value.split('-').map(Number);
    if (parts.length !== 3 || parts.some(Number.isNaN)) return null;
    return new Date(parts[0], parts[1] - 1, parts[2]);
}

function formatCalendarDate(date) {
    return [
        date.getFullYear(),
        String(date.getMonth() + 1).padStart(2, '0'),
        String(date.getDate()).padStart(2, '0'),
    ].join('-');
}

function calendarAddDays(date, amount) {
    return new Date(date.getFullYear(), date.getMonth(), date.getDate() + amount);
}

function closeDatePicker(calendar) {
    calendar.querySelector('[data-calendar-panel]')?.classList.add('hidden');
    calendar.querySelector('[data-calendar-backdrop]')?.classList.add('hidden');
}

function openDatePicker(calendar) {
    document.querySelectorAll('[data-date-picker]').forEach((other) => {
        if (other !== calendar) closeDatePicker(other);
    });
    calendar.querySelector('[data-calendar-panel]')?.classList.remove('hidden');
    calendar.querySelector('[data-calendar-backdrop]')?.classList.remove('hidden');
}

function initializeDatePickers(root = document) {
    root.querySelectorAll('[data-date-picker]:not([data-ready="1"])').forEach((calendar) => {
        calendar.dataset.ready = '1';

        const input = calendar.querySelector('[data-picker-input]');
        const display = calendar.querySelector('[data-picker-display]');
        const monthLabel = calendar.querySelector('[data-calendar-month]');
        const weekdays = calendar.querySelector('[data-calendar-weekdays]');
        const days = calendar.querySelector('[data-calendar-days]');
        const previous = calendar.querySelector('[data-calendar-prev]');
        const next = calendar.querySelector('[data-calendar-next]');
        const locale = document.documentElement.lang || 'id-ID';
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        let minDate = parseCalendarDate(calendar.dataset.minDate) || today;
        let viewDate = parseCalendarDate(input?.value) || minDate;
        viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth(), 1);

        const sameDay = (left, right) => left && right
            && left.getFullYear() === right.getFullYear()
            && left.getMonth() === right.getMonth()
            && left.getDate() === right.getDate();

        const render = () => {
            const selected = parseCalendarDate(input?.value);
            const form = calendar.closest('form');
            const otherName = input?.name === 'check_in_date' ? 'check_out_date' : 'check_in_date';
            const otherInput = form?.querySelector(`[data-picker-type="${otherName}"] [data-picker-input]`);
            const otherDate = parseCalendarDate(otherInput?.value);
            const checkIn = input?.name === 'check_in_date' ? selected : otherDate;
            const checkOut = input?.name === 'check_out_date' ? selected : otherDate;
            const monthStart = new Date(viewDate.getFullYear(), viewDate.getMonth(), 1);
            const monthEnd = new Date(viewDate.getFullYear(), viewDate.getMonth() + 1, 0);
            const leading = (monthStart.getDay() + 6) % 7;
            const firstCell = calendarAddDays(monthStart, -leading);
            const totalCells = Math.ceil((leading + monthEnd.getDate()) / 7) * 7;

            if (monthLabel) {
                monthLabel.textContent = monthStart.toLocaleDateString(locale, { month: 'long', year: 'numeric' });
            }
            if (previous) {
                previous.disabled = monthStart <= new Date(minDate.getFullYear(), minDate.getMonth(), 1);
            }
            if (display) {
                display.textContent = selected
                    ? selected.toLocaleDateString(locale, { weekday: 'short', day: 'numeric', month: 'short' })
                    : calendar.dataset.emptyLabel;
            }
            if (!days) return;

            days.innerHTML = '';
            for (let index = 0; index < totalCells; index += 1) {
                const date = calendarAddDays(firstCell, index);
                if (date.getMonth() !== monthStart.getMonth()) {
                    const empty = document.createElement('span');
                    empty.className = 'calendar-day is-empty';
                    days.appendChild(empty);
                    continue;
                }

                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = String(date.getDate());
                button.className = 'calendar-day';
                button.disabled = date < minDate;
                if (button.disabled) button.classList.add('is-disabled');
                if (sameDay(date, today)) button.classList.add('is-today');
                if (sameDay(date, checkIn)) button.classList.add('is-selected');
                if (sameDay(date, checkOut)) button.classList.add('is-checkout');
                if (checkIn && checkOut && date > checkIn && date < checkOut) button.classList.add('is-in-range');
                button.addEventListener('click', () => {
                    input.value = formatCalendarDate(date);
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    if (input.name === 'check_in_date') {
                        form?.querySelectorAll('[data-picker-type="check_out_date"]').forEach((checkout) => {
                            checkout.dispatchEvent(new CustomEvent('calendar:min-date', {
                                detail: { date: calendarAddDays(date, 1) },
                            }));
                        });
                    }
                    render();
                    closeDatePicker(calendar);
                });
                days.appendChild(button);
            }
        };

        if (weekdays) {
            weekdays.innerHTML = '';
            const monday = new Date(2026, 0, 5);
            for (let index = 0; index < 7; index += 1) {
                const label = document.createElement('span');
                label.textContent = calendarAddDays(monday, index).toLocaleDateString(locale, { weekday: 'short' });
                weekdays.appendChild(label);
            }
        }

        calendar.querySelector('[data-calendar-toggle]')?.addEventListener('click', () => {
            const panel = calendar.querySelector('[data-calendar-panel]');
            panel?.classList.contains('hidden') ? openDatePicker(calendar) : closeDatePicker(calendar);
        });
        calendar.querySelector('[data-calendar-close]')?.addEventListener('click', () => closeDatePicker(calendar));
        calendar.querySelector('[data-calendar-backdrop]')?.addEventListener('click', () => closeDatePicker(calendar));
        previous?.addEventListener('click', () => {
            viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth() - 1, 1);
            render();
        });
        next?.addEventListener('click', () => {
            viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth() + 1, 1);
            render();
        });
        calendar.addEventListener('calendar:min-date', (event) => {
            minDate = event.detail.date;
            const selected = parseCalendarDate(input.value);
            if (selected && selected < minDate) input.value = '';
            viewDate = new Date((selected || minDate).getFullYear(), (selected || minDate).getMonth(), 1);
            render();
        });

        render();
    });
}

document.addEventListener('DOMContentLoaded', () => initializeDatePickers());
