<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paslon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PaslonController extends Controller
{
    /**
     * Display a listing of the paslon.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $paslons = Paslon::orderBy('order_number')->get();

        return view('admin.paslon.index', compact('paslons'));
    }

    /**
     * Show the form for creating a new paslon.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        return view('admin.paslon.create');
    }

    /**
     * Store a newly created paslon in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'order_number' => ['required', 'integer', 'min:1', 'max:99', 'unique:paslons,order_number'],
            'leader_name' => ['required', 'string', 'max:255'],
            'deputy_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'vision' => ['required', 'string'],
            'mission' => ['required', 'string'],
            'program' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:3072'],
        ]);

        unset($data['image']);
        $data['leader_name'] = trim($data['leader_name']);
        $data['deputy_name'] = trim($data['deputy_name']);
        $data['name'] = $this->buildDisplayName($data['leader_name'], $data['deputy_name']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage(
                $request->file('image'),
                $data['leader_name'],
                $data['deputy_name']
            );
        }

        Paslon::create($data);

        return redirect()->route('admin.paslon.index')->with('success', 'Paslon berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified paslon.
     *
     * @param  \App\Models\Paslon  $paslon
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit(Paslon $paslon)
    {
        return view('admin.paslon.edit', compact('paslon'));
    }

    /**
     * Update the specified paslon in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Paslon  $paslon
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Paslon $paslon)
    {
        $data = $request->validate([
            'order_number' => ['required', 'integer', 'min:1', 'max:99', 'unique:paslons,order_number,' . $paslon->id],
            'leader_name' => ['required', 'string', 'max:255'],
            'deputy_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'vision' => ['required', 'string'],
            'mission' => ['required', 'string'],
            'program' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:3072'],
        ]);

        unset($data['image']);
        $data['leader_name'] = trim($data['leader_name']);
        $data['deputy_name'] = trim($data['deputy_name']);
        $data['name'] = $this->buildDisplayName($data['leader_name'], $data['deputy_name']);

        if ($request->hasFile('image')) {
            $this->deleteImage($paslon->image_path);
            $data['image_path'] = $this->storeImage(
                $request->file('image'),
                $data['leader_name'],
                $data['deputy_name']
            );
        }

        $paslon->update($data);

        return redirect()->route('admin.paslon.index')->with('success', 'Paslon berhasil diperbarui.');
    }

    /**
     * Remove the specified paslon from storage.
     *
     * @param  \App\Models\Paslon  $paslon
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Paslon $paslon)
    {
        if ($paslon->tokens()->whereNotNull('used_at')->exists()) {
            return redirect()
                ->route('admin.paslon.index')
                ->withErrors(['general' => 'Paslon tidak dapat dihapus karena sudah memiliki suara.']);
        }

        $this->deleteImage($paslon->image_path);
        $paslon->delete();

        return redirect()->route('admin.paslon.index')->with('success', 'Paslon berhasil dihapus.');
    }

    /**
     * Store uploaded image into the public assets directory.
     *
     * @param  \Illuminate\Http\UploadedFile  $image
     * @param  string  $leaderName
     * @param  string|null  $deputyName
     * @return string
     */
    protected function storeImage($image, string $leaderName, ?string $deputyName = null): string
    {
        $directory = public_path('assets/images');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $extension = strtolower($image->getClientOriginalExtension());
        $slugSource = trim($leaderName . ' ' . ($deputyName ?? ''));
        $filename = time() . '_' . Str::slug($slugSource) . '.' . $extension;
        $image->move($directory, $filename);

        return 'assets/images/' . $filename;
    }

    /**
     * Delete image from disk when it is replaced or paslon is removed.
     *
     * @param  string|null  $path
     * @return void
     */
    protected function deleteImage(?string $path): void
    {
        if (! $path) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    /**
     * Build a combined paslon name from leader and deputy.
     *
     * @param  string  $leader
     * @param  string|null  $deputy
     * @return string
     */
    protected function buildDisplayName(string $leader, ?string $deputy): string
    {
        return trim($leader) . ($deputy ? ' & ' . trim($deputy) : '');
    }
}
