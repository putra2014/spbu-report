<!DOCTYPE html>
<html>
<head>
    <title>Profil SPBU</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px; vertical-align: top; }
    </style>
</head>
<body>
    <h3>Profil SPBU - <?= $spbu['kode_spbu'] ?></h3>
    <table>
        <tr><td>Nama SPBU</td><td>: <?= $spbu['nama_spbu'] ?></td></tr>
        <tr><td>Perusahaan</td><td>: <?= $spbu['nama_perusahaan'] ?></td></tr>
        <tr><td>Alamat</td><td>: <?= $spbu['alamat_lengkap'] ?></td></tr>
        <tr><td>Provinsi</td><td>: <?= model('ProvinsiModel')->find($spbu['provinsi_id'])['nama'] ?? '-' ?></td></tr>
        <tr><td>Kabupaten</td><td>: <?= model('KabupatenModel')->find($spbu['kabupaten_id'])['nama'] ?? '-' ?></td></tr>
        <!-- Tambahkan sesuai kebutuhan -->
    </table>

    <script>
        window.print();
    </script>
</body>
</html>
