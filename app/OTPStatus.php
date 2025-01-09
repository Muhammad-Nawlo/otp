<?php

namespace App;

enum OTPStatus: string
{
    case VALID = 'valid';
    case INVALID = 'invalid';
    case EXPIRED = 'expired';
}
