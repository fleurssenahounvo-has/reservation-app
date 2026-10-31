<?php
declare(strict_types=1);

namespace App\Service;

use RuntimeException;

/**
 * Levée quand le créneau demandé chevauche une réservation active ou un blocage.
 */
class ConflitReservationException extends RuntimeException
{
}