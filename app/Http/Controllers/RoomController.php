<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        return view('rooms.index', [
            'rooms' => Room::query()->with('units')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('rooms.create', [
            'room' => new Room([
                'status' => Room::STATUS_AVAILABLE,
                'capacity' => 2,
                'included_capacity' => 2,
                'max_capacity' => 2,
                'allow_unit_quantity' => false,
                'extra_guest_charge_mode' => 'manual',
                'extra_guest_adult_price' => 0,
                'extra_guest_child_price' => 0,
                'is_active' => true,
                'facilities' => [],
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['image_path'] = $this->storeImage($request) ?? $this->submittedImageUrl($request);

        $room = Room::query()->create($data);
        $this->syncUnits($room, $this->submittedUnitNames($request, $room));

        AuditLogger::record(
            $request,
            'room.created',
            'Menambahkan kamar '.$room->name,
            $room,
            null,
            $room->only(['name', 'price', 'capacity', 'included_capacity', 'max_capacity', 'allow_unit_quantity', 'extra_guest_charge_mode', 'status', 'is_active', 'image_path', 'facilities']),
        );

        return redirect()->route('rooms.index')->with('status', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Room $room): View
    {
        return view('rooms.edit', [
            'room' => $room->load('units'),
        ]);
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $data = $this->validatedData($request);
        $imagePath = $this->storeImage($request) ?? $this->submittedImageUrl($request);
        $oldValues = $room->only(['name', 'price', 'capacity', 'included_capacity', 'max_capacity', 'allow_unit_quantity', 'extra_guest_charge_mode', 'status', 'is_active', 'image_path', 'facilities']);

        if ($imagePath !== null) {
            if ($room->image_path && ! str_starts_with($room->image_path, 'http')) {
                Storage::disk('public')->delete($room->image_path);
            }

            $data['image_path'] = $imagePath;
        }

        $room->update($data);
        $this->syncUnits($room, $this->submittedUnitNames($request, $room));

        AuditLogger::record(
            $request,
            'room.updated',
            'Mengubah data kamar '.$room->name,
            $room,
            $oldValues,
            $room->only(['name', 'price', 'capacity', 'included_capacity', 'max_capacity', 'allow_unit_quantity', 'extra_guest_charge_mode', 'status', 'is_active', 'image_path', 'facilities']),
        );

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
            'included_capacity' => ['nullable', 'integer', 'min:1', 'max:50'],
            'max_capacity' => ['nullable', 'integer', 'min:1', 'max:50', 'gte:included_capacity'],
            'allow_unit_quantity' => ['nullable', 'boolean'],
            'extra_guest_charge_mode' => ['nullable', 'in:manual,none'],
            'extra_guest_adult_price' => ['nullable', 'numeric', 'min:0'],
            'extra_guest_child_price' => ['nullable', 'numeric', 'min:0'],
            'capacity_rule_note' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:Available,Cleaning,Maintenance'],
            'facilities_text' => ['nullable', 'string', 'max:2000'],
            'room_units_text' => ['nullable', 'string', 'max:4000'],
            'image' => ['nullable', 'image', 'max:4096'],
            'image_url' => ['nullable', 'url', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'capacity' => $validated['max_capacity'] ?? $validated['capacity'],
            'included_capacity' => $validated['included_capacity'] ?? $validated['capacity'],
            'max_capacity' => $validated['max_capacity'] ?? $validated['capacity'],
            'allow_unit_quantity' => $request->boolean('allow_unit_quantity'),
            'extra_guest_charge_mode' => $validated['extra_guest_charge_mode'] ?? 'manual',
            'extra_guest_adult_price' => $validated['extra_guest_adult_price'] ?? 0,
            'extra_guest_child_price' => $validated['extra_guest_child_price'] ?? 0,
            'capacity_rule_note' => $validated['capacity_rule_note'] ?? null,
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

    /**
     * @return array<int, string>
     */
    private function submittedUnitNames(Request $request, Room $room): array
    {
        $names = collect(preg_split('/\r\n|\r|\n/', $request->string('room_units_text')->toString()) ?: [])
            ->map(fn (string $name): string => trim($name))
            ->filter()
            ->unique()
            ->values();

        if ($names->isNotEmpty()) {
            return $names->all();
        }

        $defaultCount = str_contains(strtolower($room->name), 'commercial') ? 6 : 1;

        return collect(range(1, $defaultCount))
            ->map(fn (int $number): string => $defaultCount > 1 ? sprintf('%s %02d', $room->name, $number) : $room->name.' 01')
            ->all();
    }

    /**
     * @param  array<int, string>  $unitNames
     */
    private function syncUnits(Room $room, array $unitNames): void
    {
        $activeNames = collect($unitNames);

        foreach ($activeNames as $name) {
            $room->units()->updateOrCreate(
                ['name' => $name],
                [
                    'status' => Room::STATUS_AVAILABLE,
                    'is_active' => true,
                ],
            );
        }

        $room->units()
            ->whereNotIn('name', $activeNames->all())
            ->update(['is_active' => false]);
    }
}
