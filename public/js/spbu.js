document.addEventListener('DOMContentLoaded', function () {
    const provinsiSelect = document.querySelector('#provinsi');
    const kabupatenSelect = document.querySelector('#kabupaten');

    if (provinsiSelect && kabupatenSelect) {
        provinsiSelect.addEventListener('change', function () {
            const provinsiId = this.value;

            fetch(`/ajax/kabupaten/${provinsiId}`)
                .then(res => res.json())
                .then(data => {
                    kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';
                    data.forEach(kab => {
                        kabupatenSelect.innerHTML += `<option value="${kab.id}">${kab.nama}</option>`;
                    });
                });
        });
    }
});
