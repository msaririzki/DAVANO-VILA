<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        return view('rooms.index', [
            'rooms' => Room::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('rooms.create', [
            'room' => new Room([
                'status' => Room::STATUS_AVAILABLE,
                'capacity' => 2,
                'is_active' => true,
                'facilities' => [],
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['image_path'] = $this->storeImage($request) ?? $this->submittedImageUrl($request);

        Room::query()->create($data);

        return redirect()->route('rooms.index')->with('status', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Room $room): View
    {
        return view('rooms.edit', [
            'room' => $room,
        ]);
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $data = $this->validatedData($request);
        $imagePath = $this->storeImage($request) ?? $this->submittedImageUrl($request);

        if ($imagePath !== null) {
            if ($room->image_path && ! str_starts_with($room->image_path, 'http')) {
                Storage::disk('public')->delete($room->image_path);
            }

            $data['image_path'] = $imagePath;
        }

        $room->update($data);

        return redirect()->route('rooms.index')->with('status', 'Kamar berhasil diperbarui.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'status' => ['required', 'in:Available,Cleaning,Maintenance'],
            'facilities_text' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'max:4096'],
            'image_url' => ['nullable', 'url', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'capacity' => $validated['capacity'],
            'status' => $validated['status'],
            'facilities' => $this->parseFacilities($validated['facilities_text'] ?? ''),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function parseFacilities(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n|,/', $value) ?: [])
            ->map(fn (string $facility): string => trim($facility))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('rooms', 'public');
    }

    private function submittedImageUrl(Request $request): ?string
    {
        $imageUrl = $request->string('image_url')->trim()->toString();

        return $imageUrl !== '' ? $imageUrl : null;
    }
}
