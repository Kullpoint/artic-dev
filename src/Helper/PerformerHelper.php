<?php
declare(strict_types=1);

namespace App\Helper;

use App\Entity\Performer;

/**
 * Class PerformerHelper
 * @package App\Helper
 */
class PerformerHelper
{
    public static function performerRating(Performer $performer)
    {
        $reviews = $performer->getReviews();
        $marks = [];
        foreach ($reviews as $review) {
            $mark = $review->getMark();
            $marks[] = (int)$mark;
        }
        $rating = array_sum($marks) / count($marks);
        if (is_float($rating)) {
            $rating = bcdiv($rating, 1, 2);
        }

        return $rating;
    }
}