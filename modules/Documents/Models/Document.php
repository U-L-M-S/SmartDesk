<?php

namespace Modules\Documents\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'filename',
        'original_filename',
        'mime_type',
        'file_size',
        'storage_path',
        'uploaded_by',
        'current_version_id',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'file_size' => 'integer',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function currentVersion()
    {
        return $this->belongsTo(DocumentVersion::class, 'current_version_id');
    }

    public function tags()
    {
        return $this->belongsToMany(DocumentTag::class, 'document_tag');
    }

    public function shares()
    {
        return $this->hasMany(DocumentShare::class);
    }

    public function sharedWith()
    {
        return $this->belongsToMany(User::class, 'document_shares')
            ->withPivot(['permission', 'shared_by'])
            ->withTimestamps();
    }

    public function createVersion(string $filename, string $mimeType, int $fileSize, string $storagePath, User $user, ?string $changeNote = null): DocumentVersion
    {
        $versionNumber = $this->versions()->max('version_number') + 1;

        $version = $this->versions()->create([
            'version_number' => $versionNumber,
            'filename' => $filename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'storage_path' => $storagePath,
            'uploaded_by' => $user->id,
            'change_note' => $changeNote,
        ]);

        $this->current_version_id = $version->id;
        $this->save();

        return $version;
    }

    public function shareWith(User $user, string $permission, User $sharedBy): void
    {
        $this->shares()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'permission' => $permission,
                'shared_by' => $sharedBy->id,
            ]
        );
    }

    public function canAccess(User $user): bool
    {
        if ($this->uploaded_by === $user->id) {
            return true;
        }

        if ($this->is_public) {
            return true;
        }

        return $this->shares()->where('user_id', $user->id)->exists();
    }

    public function scopeAccessibleBy($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('uploaded_by', $user->id)
                ->orWhere('is_public', true)
                ->orWhereHas('shares', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
        });
    }
}
