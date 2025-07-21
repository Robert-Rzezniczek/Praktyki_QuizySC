<?php

/**
 * Education level.
 */

namespace App\Entity\Enum;

/**
 * Enum EducationLevel.
 */
enum EducationLevel: string
{
    case PRIMARY_SCHOOL = 'Szkoła podstawowa';
    case SECONDARY_SCHOOL = 'Szkoła ponadpodstawowa';
    case UNIVERSITY = 'Studia';

    /**
     * Get the label for the education level.
     *
     * @return string string
     */
    public function label(): string
    {
        return match ($this) {
            self::PRIMARY_SCHOOL => 'label.primary_school',
            self::SECONDARY_SCHOOL => 'label.secondary_school',
            self::UNIVERSITY => 'label.university',
        };
    }
}
