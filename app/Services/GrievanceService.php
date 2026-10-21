<?php

namespace App\Services;

use App\Enums\GrievanceStatus;
use App\Models\Grievance;
use App\Models\GrievanceDocument;
use App\Models\GrievanceUpdate;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GrievanceService
{
    public function create(array $data, ?string $ip = null): Grievance
    {
        return DB::transaction(function () use ($data, $ip) {
            $documents = $data['documents'] ?? [];
            unset($data['documents'], $data['consent'], $data['website']);

            $grievance = new Grievance($data);
            $grievance->case_no = 'TMP-'.Str::uuid();
            $grievance->status = GrievanceStatus::Pending->value;
            $grievance->ip = $ip;
            $grievance->save();

            $grievance->case_no = sprintf(
                '%s-%s-%05d',
                config('maku.grievance.prefix'),
                now()->year,
                $grievance->getKey()
            );
            $grievance->save();

            foreach ($documents as $document) {
                if ($document instanceof UploadedFile) {
                    $this->storeDocument($grievance, $document);
                }
            }

            GrievanceUpdate::create([
                'grievance_id' => $grievance->id,
                'status' => $grievance->status,
                'note' => 'शिकायत दर्ज की गई।',
                'is_public' => true,
            ]);

            return $grievance;
        });
    }

    public function addUpdate(
        Grievance $grievance,
        string $status,
        ?string $note,
        ?User $actor = null,
        bool $isPublic = false
    ): GrievanceUpdate {
        return DB::transaction(function () use ($grievance, $status, $note, $actor, $isPublic) {
            $grievance->status = $status;

            if ($status === GrievanceStatus::Resolved->value) {
                $grievance->resolved_at = now();
                $grievance->resolution_note = $note;
            }

            $grievance->save();

            return GrievanceUpdate::create([
                'grievance_id' => $grievance->id,
                'status' => $status,
                'note' => $note,
                'user_id' => $actor?->id,
                'is_public' => $isPublic,
            ]);
        });
    }

    public function storeDocument(Grievance $grievance, UploadedFile $file, ?User $actor = null): GrievanceDocument
    {
        $path = $file->store('grievances/'.$grievance->id, config('maku.uploads.disk'));

        return GrievanceDocument::create([
            'grievance_id' => $grievance->id,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => $actor?->id,
        ]);
    }

    public function findByCredentials(string $caseNo, string $phone): ?Grievance
    {
        $digits = clean_digits($phone);

        if ($digits === '' || $caseNo === '') {
            return null;
        }

        return Grievance::query()
            ->where('case_no', trim($caseNo))
            ->whereRaw("REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+91', '') = ?", [
                ltrim($digits, '0'),
            ])
            ->first();
    }
}
