<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(): View
    {
        $links = Link::query()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        return view('admin.links.index', [
            'links' => $links,
            'types' => Link::TYPES,
        ]);
    }

    public function create(): View
    {
        return view('admin.links.form', [
            'link' => new Link(['type' => 'teamspeak', 'sort_order' => 0]),
            'types' => Link::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateLink($request);

        Link::query()->create($validated);

        return redirect()
            ->route('admin.links.index')
            ->with('status', 'Verbindung wurde angelegt.');
    }

    public function edit(Link $link): View
    {
        return view('admin.links.form', [
            'link' => $link,
            'types' => Link::TYPES,
        ]);
    }

    public function update(Request $request, Link $link): RedirectResponse
    {
        $validated = $this->validateLink($request);

        $link->update($validated);

        return redirect()
            ->route('admin.links.index')
            ->with('status', 'Verbindung wurde aktualisiert.');
    }

    public function destroy(Link $link): RedirectResponse
    {
        $link->delete();

        return redirect()
            ->route('admin.links.index')
            ->with('status', 'Verbindung wurde gelöscht.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateLink(Request $request): array
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(Link::TYPES))],
            'label' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        return $validated;
    }
}
