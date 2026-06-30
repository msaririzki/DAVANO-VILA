const MAX_PROOF_WIDTH = 1600;
const MAX_PROOF_HEIGHT = 2400;
const TARGET_PROOF_BYTES = 800 * 1024;

function loadImage(file) {
    return new Promise((resolve, reject) => {
        const image = new Image();
        const url = URL.createObjectURL(file);

        image.onload = () => {
            URL.revokeObjectURL(url);
            resolve(image);
        };
        image.onerror = () => {
            URL.revokeObjectURL(url);
            reject(new Error('Gambar tidak dapat dibuka.'));
        };
        image.src = url;
    });
}

function canvasBlob(canvas, type, quality) {
    return new Promise((resolve, reject) => {
        canvas.toBlob((blob) => {
            if (blob) {
                resolve(blob);
            } else {
                reject(new Error('Gambar gagal dikompres.'));
            }
        }, type, quality);
    });
}

async function prepareProof(file) {
    const image = await loadImage(file);
    const scale = Math.min(
        1,
        MAX_PROOF_WIDTH / image.naturalWidth,
        MAX_PROOF_HEIGHT / image.naturalHeight,
    );
    const canvas = document.createElement('canvas');
    canvas.width = Math.max(1, Math.round(image.naturalWidth * scale));
    canvas.height = Math.max(1, Math.round(image.naturalHeight * scale));

    const context = canvas.getContext('2d', { alpha: false });
    context.fillStyle = '#ffffff';
    context.fillRect(0, 0, canvas.width, canvas.height);
    context.drawImage(image, 0, 0, canvas.width, canvas.height);

    const type = canvas.toDataURL('image/webp').startsWith('data:image/webp')
        ? 'image/webp'
        : 'image/jpeg';
    let quality = 0.82;
    let blob = await canvasBlob(canvas, type, quality);

    while (blob.size > TARGET_PROOF_BYTES && quality > 0.5) {
        quality -= 0.1;
        blob = await canvasBlob(canvas, type, quality);
    }

    return { blob, canvas, type };
}

function normalizeAmount(rawAmount) {
    let value = rawAmount.replace(/\s/g, '').replace(/[^\d.,]/g, '');
    const separators = [...value.matchAll(/[.,]/g)];

    if (separators.length > 0) {
        const lastSeparator = separators.at(-1).index;
        const decimalLength = value.length - lastSeparator - 1;

        if (decimalLength === 2) {
            value = value.slice(0, lastSeparator);
        }
    }

    const digits = value.replace(/\D/g, '');
    return digits === '' ? null : Number.parseInt(digits, 10);
}

function extractAmount(text, maximumAmount) {
    const normalized = text.toUpperCase().replace(/\n/g, ' ');
    const candidates = [];
    const patterns = [
        { regex: /(?:TOTAL\s+TRANSFER|NOMINAL|JUMLAH|AMOUNT|TOTAL)[^\d]{0,24}(?:RP\.?\s*)?([\d][\d.,\s]{3,})/g, score: 4 },
        { regex: /(?:RP\.?|IDR)\s*([\d][\d.,\s]{3,})/g, score: 3 },
    ];

    patterns.forEach(({ regex, score }) => {
        for (const match of normalized.matchAll(regex)) {
            const amount = normalizeAmount(match[1]);
            if (amount && amount >= 1000 && amount <= maximumAmount) {
                candidates.push({ amount, score });
            }
        }
    });

    candidates.sort((left, right) => right.score - left.score || right.amount - left.amount);
    return candidates[0]?.amount ?? null;
}

function extractReference(text) {
    const normalized = text.toUpperCase().replace(/[^A-Z0-9\n:/._-]/g, ' ');
    const patterns = [
        /(?:NO\.?\s*(?:REF(?:ERENSI)?|TRANSAKSI)|REFERENCE|TRANSACTION\s*ID|ID\s*TRANSAKSI|TRACE\s*NO)[\s:/._-]*([A-Z0-9][A-Z0-9._-]{5,})/,
        /(?:REF(?:ERENSI)?)[\s:/._-]+([A-Z0-9][A-Z0-9._-]{5,})/,
    ];

    for (const pattern of patterns) {
        const reference = normalized.match(pattern)?.[1]?.replace(/^[._-]+|[._-]+$/g, '');
        if (reference) return reference.slice(0, 120);
    }

    return null;
}

function selectDetectedBank(select, text) {
    if (!select) return false;

    const normalized = text.toUpperCase().replace(/\s/g, '');

    for (const option of select.options) {
        if (!option.value) continue;

        const optionText = option.textContent.toUpperCase();
        const bankName = optionText.split('—')[0].trim().replace(/\s/g, '');
        const accountDigits = optionText.replace(/\D/g, '');
        const accountSuffix = accountDigits.slice(-5);

        if (
            (bankName.length >= 3 && normalized.includes(bankName))
            || (accountSuffix.length === 5 && normalized.includes(accountSuffix))
        ) {
            select.value = option.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            return true;
        }
    }

    return false;
}

async function sha256(blob) {
    const digest = await crypto.subtle.digest('SHA-256', await blob.arrayBuffer());
    return [...new Uint8Array(digest)]
        .map((byte) => byte.toString(16).padStart(2, '0'))
        .join('');
}

function updateStatus(reader, message, tone = 'slate') {
    const status = reader.querySelector('[data-proof-status]');
    if (!status) return;

    const toneClasses = {
        slate: ['bg-slate-50', 'border-slate-200', 'text-slate-700'],
        amber: ['bg-amber-50', 'border-amber-200', 'text-amber-800'],
        emerald: ['bg-emerald-50', 'border-emerald-200', 'text-emerald-800'],
        rose: ['bg-rose-50', 'border-rose-200', 'text-rose-800'],
    };

    status.className = `mt-3 rounded-xl border px-3 py-2.5 text-xs font-bold ${toneClasses[tone].join(' ')}`;
    status.textContent = message;
}

function setMoneyValue(form, amount) {
    const display = form.querySelector('[data-money-display]');
    if (!display || !amount) return;

    display.value = String(amount);
    display.dispatchEvent(new Event('input', { bubbles: true }));
}

function replaceInputFile(input, blob, type) {
    if (typeof DataTransfer === 'undefined') return;

    const extension = type === 'image/webp' ? 'webp' : 'jpg';
    const compressedFile = new File([blob], `bukti-transfer-${Date.now()}.${extension}`, {
        type,
        lastModified: Date.now(),
    });
    const transfer = new DataTransfer();
    transfer.items.add(compressedFile);
    input.files = transfer.files;
}

function initializePaymentProofReaders(root = document) {
    root.querySelectorAll('[data-payment-proof-reader]:not([data-proof-ready])').forEach((reader) => {
        reader.dataset.proofReady = 'true';
        const form = reader.closest('form');
        const input = reader.querySelector('[data-proof-input]');
        const preview = reader.querySelector('[data-proof-preview]');
        const previewImage = reader.querySelector('[data-proof-preview-image]');
        const sizeLabel = reader.querySelector('[data-proof-size]');
        const progress = reader.querySelector('[data-proof-progress]');
        const submit = form?.querySelector('[data-payment-submit]');
        const referenceInput = form?.querySelector('[name="transfer_reference"]');
        const bankSelect = form?.querySelector('[name="bank_account_id"]');
        const confidenceInput = form?.querySelector('[name="ocr_confidence"]');
        const detectedAmountInput = form?.querySelector('[name="ocr_detected_amount"]');
        const detectedReferenceInput = form?.querySelector('[name="ocr_detected_reference"]');
        const maximumAmount = Number.parseInt(reader.dataset.maximumAmount || '0', 10);
        const bookingCode = reader.dataset.bookingCode || 'PEMBAYARAN';
        let previewUrl = null;

        input?.addEventListener('change', async () => {
            const file = input.files?.[0];
            if (!file || !form) return;

            submit?.setAttribute('disabled', 'disabled');
            submit?.classList.add('opacity-60', 'cursor-wait');
            progress?.classList.remove('hidden');
            updateStatus(reader, 'Menyiapkan dan mengompres gambar di perangkat…');

            try {
                const prepared = await prepareProof(file);
                replaceInputFile(input, prepared.blob, prepared.type);

                if (previewUrl) URL.revokeObjectURL(previewUrl);
                previewUrl = URL.createObjectURL(prepared.blob);
                previewImage.src = previewUrl;
                preview?.classList.remove('hidden');
                preview?.classList.add('flex');
                sizeLabel.textContent = `${Math.max(1, Math.round(prepared.blob.size / 1024))} KB`;

                const proofHash = await sha256(prepared.blob);
                const fallbackReference = `BUKTI-${bookingCode}-${proofHash.slice(0, 12)}`.toUpperCase();
                referenceInput.value = fallbackReference;

                updateStatus(reader, 'Membaca nominal, rekening, dan referensi secara otomatis…');
                const { createWorker } = await import('tesseract.js');
                const worker = await createWorker('eng', 1, {
                    logger(message) {
                        if (message.status === 'recognizing text' && Number.isFinite(message.progress)) {
                            const percentage = Math.round(message.progress * 100);
                            updateStatus(reader, `Membaca bukti transfer… ${percentage}%`);
                        }
                    },
                });

                let result;
                try {
                    result = await worker.recognize(prepared.canvas);
                } finally {
                    await worker.terminate();
                }

                const text = result.data.text || '';
                const confidence = Math.max(0, Math.min(100, Math.round(result.data.confidence || 0)));
                const amount = extractAmount(text, maximumAmount);
                const detectedReference = extractReference(text);

                confidenceInput.value = String(confidence);
                if (amount) {
                    setMoneyValue(form, amount);
                    detectedAmountInput.value = String(amount);
                }
                if (detectedReference) {
                    referenceInput.value = detectedReference;
                    detectedReferenceInput.value = detectedReference;
                }
                selectDetectedBank(bankSelect, text);

                if (amount) {
                    updateStatus(
                        reader,
                        `Bukti terbaca (${confidence}%). Data pembayaran sudah diisi—periksa lalu konfirmasi.`,
                        confidence >= 65 ? 'emerald' : 'amber',
                    );
                } else {
                    updateStatus(
                        reader,
                        'Bukti sudah siap, tetapi nominal belum terbaca. Pilih nominal DP/Lunasi atau isi manual.',
                        'amber',
                    );
                }
            } catch (error) {
                updateStatus(
                    reader,
                    'Pembacaan otomatis tidak berhasil. Gambar tetap dapat dikirim; isi nominal dan rekening secara manual.',
                    'rose',
                );
            } finally {
                progress?.classList.add('hidden');
                submit?.removeAttribute('disabled');
                submit?.classList.remove('opacity-60', 'cursor-wait');
            }
        });
    });
}

if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => initializePaymentProofReaders());
}

export { extractAmount, extractReference, normalizeAmount };
