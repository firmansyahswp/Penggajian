<div class="modal-header">
    <h5 class="modal-title" id="editModalLabel">Edit Pengajuan Ketidakhadiran</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form action="<?= base_url('pegawai/ketidakhadiran/update/' . $izin['id']) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="modal-body">
        <div class="form-group">
            <label for="tanggal_edit">Tanggal Izin:</label>
            <input type="date" name="tanggal" id="tanggal_edit" class="form-control" value="<?= old('tanggal', $izin['tanggal']) ?>" required>
        </div>
        <div class="form-group">
            <label for="keterangan_edit">Keterangan:</label>
            <select name="keterangan" id="keterangan_edit" class="form-control" required>
                <option value="Izin" <?= $izin['keterangan'] === 'Izin' ? 'selected' : '' ?>>Izin</option>
                <option value="Sakit" <?= $izin['keterangan'] === 'Sakit' ? 'selected' : '' ?>>Sakit</option>
            </select>
        </div>
        <div class="form-group">
            <label for="deskripsi_edit">Deskripsi:</label>
            <textarea name="deskripsi" id="deskripsi_edit" class="form-control" rows="3" required><?= old('deskripsi', $izin['deskripsi']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="file_bukti_edit">File Bukti (Kosongkan jika tidak diubah):</label>
            <input type="file" name="file_bukti" id="file_bukti_edit" class="form-control-file">
            <?php if ($izin['file']): ?>
                <small class="form-text text-muted">File saat ini: **<?= $izin['file'] ?>**. Akan diganti jika Anda upload file baru.</small>
            <?php endif; ?>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
    </div>
</form>