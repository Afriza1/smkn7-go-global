<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentStudentLinkDecision extends Model
{
    protected $fillable = ['parent_student_link_id', 'actor_id', 'actor_name_snapshot', 'decision', 'note', 'decided_at'];
    protected function casts(): array { return ['decided_at' => 'datetime']; }
    public function link(): BelongsTo { return $this->belongsTo(ParentStudentLink::class, 'parent_student_link_id'); }
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }
}
