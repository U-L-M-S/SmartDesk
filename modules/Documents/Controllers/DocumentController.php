<?php

namespace Modules\Documents\Controllers;

use App\Http\Controllers\Controller;
use Modules\Documents\Models\Document;
use Modules\Documents\Models\DocumentTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::accessibleBy(Auth::user())
            ->with(['uploader', 'tags'])
            ->latest()
            ->paginate(20);

        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        $tags = DocumentTag::all();
        return view('documents.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:51200', // 50MB max
            'is_public' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:document_tags,id',
        ]);

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $filename, 'local');

        $document = Document::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'storage_path' => $path,
            'uploaded_by' => Auth::id(),
            'is_public' => $validated['is_public'] ?? false,
        ]);

        // Create initial version
        $version = $document->createVersion(
            $filename,
            $file->getMimeType(),
            $file->getSize(),
            $path,
            Auth::user(),
            'Initial version'
        );

        // Attach tags
        if (!empty($validated['tags'])) {
            $document->tags()->attach($validated['tags']);
        }

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        if (!$document->canAccess(Auth::user())) {
            abort(403, 'You do not have permission to view this document.');
        }

        $document->load(['uploader', 'tags', 'versions.uploader', 'shares.user', 'shares.sharedBy']);

        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        if (!$document->canAccess(Auth::user())) {
            abort(403, 'You do not have permission to edit this document.');
        }

        $tags = DocumentTag::all();
        return view('documents.edit', compact('document', 'tags'));
    }

    public function update(Request $request, Document $document)
    {
        if (!$document->canAccess(Auth::user())) {
            abort(403, 'You do not have permission to edit this document.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:document_tags,id',
        ]);

        $document->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
        ]);

        // Sync tags
        if (isset($validated['tags'])) {
            $document->tags()->sync($validated['tags']);
        }

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        if ($document->uploaded_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'You do not have permission to delete this document.');
        }

        // Delete all versions from storage
        foreach ($document->versions as $version) {
            Storage::disk('local')->delete($version->storage_path);
        }

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    public function download(Document $document)
    {
        if (!$document->canAccess(Auth::user())) {
            abort(403, 'You do not have permission to download this document.');
        }

        return Storage::disk('local')->download($document->storage_path, $document->original_filename);
    }

    public function uploadVersion(Request $request, Document $document)
    {
        if (!$document->canAccess(Auth::user())) {
            abort(403, 'You do not have permission to upload a new version.');
        }

        $validated = $request->validate([
            'file' => 'required|file|max:51200', // 50MB max
            'change_note' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $filename, 'local');

        $document->createVersion(
            $filename,
            $file->getMimeType(),
            $file->getSize(),
            $path,
            Auth::user(),
            $validated['change_note'] ?? null
        );

        return redirect()->route('documents.show', $document)
            ->with('success', 'New version uploaded successfully.');
    }

    public function share(Request $request, Document $document)
    {
        if ($document->uploaded_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'You do not have permission to share this document.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission' => 'required|in:view,edit,manage',
        ]);

        $user = \App\Models\User::find($validated['user_id']);
        $document->shareWith($user, $validated['permission'], Auth::user());

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document shared successfully.');
    }
}
