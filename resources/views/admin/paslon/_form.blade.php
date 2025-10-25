    @csrf
    <div class="form-grid">
        <div class="form-field">
            <label for="order_number">Nomor Urut</label>
            <input type="number" id="order_number" name="order_number" min="1" max="99"
                   value="{{ old('order_number', optional($paslon ?? null)->order_number) }}" required>
            @error('order_number')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-field split">
            <div>
                <label for="leader_name">Ketua</label>
                <input type="text" id="leader_name" name="leader_name"
                       value="{{ old('leader_name', optional($paslon ?? null)->leader_name ?? optional($paslon ?? null)->name) }}" required>
                @error('leader_name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="deputy_name">Wakil Ketua</label>
                <input type="text" id="deputy_name" name="deputy_name"
                       value="{{ old('deputy_name', optional($paslon ?? null)->deputy_name) }}" required>
                @error('deputy_name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="form-field">
            <label for="tagline">Tagline (opsional)</label>
            <input type="text" id="tagline" name="tagline"
                   value="{{ old('tagline', optional($paslon ?? null)->tagline) }}">
            @error('tagline')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-field full-width">
            <label for="vision">Visi</label>
            <textarea id="vision" name="vision" rows="3" required>{{ old('vision', optional($paslon ?? null)->vision) }}</textarea>
            @error('vision')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-field full-width">
            <label for="mission">Misi</label>
            <textarea id="mission" name="mission" rows="4" required>{{ old('mission', optional($paslon ?? null)->mission) }}</textarea>
            @error('mission')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-field full-width">
            <label for="program">Program Kerja</label>
            <textarea id="program" name="program" rows="4">{{ old('program', optional($paslon ?? null)->program) }}</textarea>
            @error('program')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-field full-width">
            <label for="image">Foto Paslon</label>
            <input type="file" id="image" name="image" accept="image/*">
            <p class="field-hint">Unggah gambar dengan latar transparan atau gelap agar terlihat kontras.</p>
            @error('image')
                <p class="field-error">{{ $message }}</p>
            @enderror
            @php
                $paslonImage = optional($paslon)->image_path;
            @endphp
            @if (!empty($paslonImage))
                <div class="image-preview">
                    <img src="{{ asset($paslonImage) }}" alt="{{ optional($paslon)->display_name ?? optional($paslon)->name }}">
                </div>
            @endif
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="primary-button">{{ $submitLabel ?? 'Simpan' }}</button>
        <a href="{{ route('admin.paslon.index') }}" class="ghost-button">Batal</a>
    </div>
