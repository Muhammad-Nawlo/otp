<?php

namespace App\Services;

use App\Models\Otp;
use App\OTPStatus;
use Carbon\Carbon;

class OtpService
{

    public function generate(string $identifier, int $length = 4, int $validity = 10)
    {
        Otp::where('identifier', $identifier)->where('valid', true)->delete();
        $token = $this->generateNumericToken($length);

        Otp::create([
            'identifier' => $identifier,
            'token' => $token,
            'validity' => $validity
        ]);

        return $token;
    }


    public function isValid(string $identifier, string $token): bool
    {
        $otp = Otp::where('identifier', $identifier)->where('token', $token)->first();

        if ($otp instanceof Otp) {
            $validity = $otp->created_at->addMinutes($otp->validity);

            return Carbon::now()->lt($validity) && $otp->valid;
        }

        return false;
    }

    public function validate(string $identifier, string $token)
    {
        $otp = Otp::where('identifier', $identifier)->where('token', $token)->first();
        if (!$otp) {
            return OTPStatus::INVALID;
        }
        if (!$otp->valid) {
            $otp->update(['valid' => false]);
            return OTPStatus::INVALID;
        }
        $now = Carbon::now();
        $validity = $otp->created_at->addMinutes($otp->validity);
        $otp->update(['valid' => false]);
        if (strtotime($validity) < strtotime($now)) {
            return OTPStatus::EXPIRED;
        }

        $otp->update(['valid' => false]);

        return OTPStatus::VALID;
    }


    private function generateNumericToken(int $length = 4): string
    {
        $i = 0;
        $token = "";

        while ($i < $length) {
            $token .= random_int(0, 9);
            $i++;
        }

        return $token;
    }
}
