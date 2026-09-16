<?php

namespace App\Services;

use App\Models\School;
use Illuminate\Support\Facades\Session;

class UniformSchoolAccess
{
    private const SESSION_KEY = 'uniform_unlocked_schools';

    private const DEFAULT_PIN = '1111';

    public function expectedPin(School $school): string
    {
        $pin = trim((string) ($school->pin ?? ''));

        return $pin !== '' ? $pin : self::DEFAULT_PIN;
    }

    public function matches(School $school, string $pin): bool
    {
        $entered = preg_replace('/\D+/', '', $pin) ?? '';
        if (strlen($entered) !== 4) {
            return false;
        }

        return hash_equals($this->expectedPin($school), $entered);
    }

    public function unlock(School $school): void
    {
        $unlocked = Session::get(self::SESSION_KEY, []);
        $unlocked[(string) $school->id] = true;
        Session::put(self::SESSION_KEY, $unlocked);
    }

    public function isUnlocked(School $school): bool
    {
        $unlocked = Session::get(self::SESSION_KEY, []);

        return ! empty($unlocked[(string) $school->id]);
    }

    public function assertUnlocked(School $school): void
    {
        if (! $this->isUnlocked($school)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pin' => 'Enter the school PIN to view this catalogue.',
            ]);
        }
    }
}
