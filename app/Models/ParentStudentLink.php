<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentStudentLink extends Model
{
    protected $fillable = ['parent_id', 'student_id', 'parent_name_snapshot', 'student_name_snapshot', 'identity_type', 'status', 'submitted_at', 'reviewed_by', 'reviewer_name_snapshot', 'reviewed_at', 'review_note'];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function parent(): BelongsTo { return $this->belongsTo(User::class, 'parent_id'); }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function decisions(): HasMany { return $this->hasMany(ParentStudentLinkDecision::class)->latest('decided_at'); }
}
