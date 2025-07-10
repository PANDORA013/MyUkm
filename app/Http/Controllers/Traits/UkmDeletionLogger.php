<?php

namespace App\Http\Controllers\Traits;

use App\Models\UkmDeletion;
use Illuminate\Support\Facades\Auth;
use App\Models\UKM;

trait UkmDeletionLogger
{
    public function logUkmDeletion(UKM $ukm, $reason = null)
    {
        $user = Auth::user();
        UkmDeletion::create([
            'ukm_id' => $ukm->id,
            'ukm_name' => $ukm->name,
            'ukm_code' => $ukm->code,
            'deleted_by' => $user ? $user->id : null,
            'deletion_reason' => $reason,
        ]);
    }
}
