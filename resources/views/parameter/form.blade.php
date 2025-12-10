<div class="mb-3">
    <label class="form-label">Nama Parameter</label>
    <input type="text" class="form-control" name="nama_param"
           value="{{ old('nama_param', $param->nama_param ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Key Parameter</label>
    <input type="text" class="form-control" name="key"
           value="{{ old('key', $param->key ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Nilai</label>
    <input type="number" class="form-control" step="0.01"
           name="nilai" value="{{ old('nilai', $param->nilai ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Keterangan</label>
    <textarea class="form-control" name="keterangan" rows="3">{{ old('keterangan', $param->keterangan ?? '') }}</textarea>
</div>
