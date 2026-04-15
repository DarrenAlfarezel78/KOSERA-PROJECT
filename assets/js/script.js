function applySearchFilter() {
    const filter = document.getElementById('searchFilter');
    const keyword = filter ? filter.value.trim().toLowerCase() : '';
    const rows = document.querySelectorAll('.service-row');

    rows.forEach((row) => {
        const haystack = row.textContent.toLowerCase();
        const visible = keyword === '' || haystack.includes(keyword);
        row.style.display = visible ? '' : 'none';
    });
}

function confirmDelete(event, serviceTitle) {
    const ok = window.confirm('Hapus jasa "' + serviceTitle + '"? Tindakan ini tidak bisa dibatalkan.');
    if (!ok) {
        event.preventDefault();
    }
    return ok;
}

function validateServiceForm(form) {
    const title = form.title.value.trim();
    const description = form.description.value.trim();
    const provider = form.provider_name.value.trim();
    const categoryId = form.category_id.value;
    const subCategoryId = form.sub_category_id.value;
    const priceRaw = form.price.value.trim();

    const errors = {};

    if (!title) errors.title = 'Judul jasa wajib diisi.';
    if (!description) errors.description = 'Deskripsi wajib diisi.';
    if (!provider) errors.provider_name = 'Nama mitra wajib diisi.';
    if (!categoryId) errors.category_id = 'Kategori wajib dipilih.';
    if (!subCategoryId) errors.sub_category_id = 'Sub-kategori wajib dipilih.';

    if (!priceRaw) {
        errors.price = 'Harga wajib diisi.';
    } else if (!/^\d+(\.\d{1,2})?$/.test(priceRaw)) {
        errors.price = 'Harga harus angka valid (contoh: 25000 atau 25000.50).';
    } else if (parseFloat(priceRaw) <= 0) {
        errors.price = 'Harga harus lebih besar dari 0.';
    }

    document.querySelectorAll('.field-error').forEach((el) => {
        el.textContent = '';
    });

    Object.keys(errors).forEach((field) => {
        const target = document.getElementById('err-' + field);
        if (target) {
            target.textContent = errors[field];
        }
    });

    return Object.keys(errors).length === 0;
}
