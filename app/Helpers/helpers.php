<?php

use App\Helpers\ActivityLogHelper;
use App\Helpers\ChapterHelper;
use App\Helpers\ChartHelper;
use App\Helpers\CommentHelper;
use App\Helpers\ExerciseHelper;
use App\Helpers\LocalizationHelper;
use App\Helpers\RatingCommentsHelper;
use App\Helpers\RatingHelper;
use App\Helpers\TemplateHelper;
use App\Helpers\UserHelper;
use App\Models\Activity;
use App\Models\User;
use App\Models\Chapter;
use App\Models\Comment;
use App\Models\Exercise;
use Illuminate\Support\Collection;

if (!function_exists('respondWithToken')) {
    function respondWithToken(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }
}
