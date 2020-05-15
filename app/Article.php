<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use ScoutElastic\Highlight;
use ScoutElastic\Searchable;

/**
 * App\Article
 *
 * @property mixed bookmarks
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property string $published_at
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|User[]
 * @property-read int|null $bookmarks_count
 * @property-read Collection|Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read mixed $bookmarked
 * @property Highlight|null $highlight
 * @property-read mixed $liked
 * @property-read Collection|User[] $likes
 * @property-read int|null $likes_count
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read User|null $user
 * @method static Builder|Article newModelQuery()
 * @method static Builder|Article newQuery()
 * @method static Builder|Article query()
 * @method static Builder|Article whereBody($value)
 * @method static Builder|Article whereCreatedAt($value)
 * @method static Builder|Article whereId($value)
 * @method static Builder|Article whereImage($value)
 * @method static Builder|Article wherePublishedAt($value)
 * @method static Builder|Article whereSlug($value)
 * @method static Builder|Article whereTitle($value)
 * @method static Builder|Article whereUpdatedAt($value)
 * @method static Builder|Article whereUserId($value)
 * @mixin Eloquent
 */
class Article extends Model
{
//    use HasPersianSlug;
//
//    public function getSlugOptions(): SlugOptions
//    {
//        return SlugOptions::create()
//            ->generateSlugsFrom('title')
//            ->saveSlugsTo('slug');
//    }

    use Searchable;

    /**
     * @var string
     */
    protected $indexConfigurator = ArticleIndexConfigurator::class;

    // Here you can specify a mapping for model fields
    protected $mapping = [
        'properties' => [
            'title'=>[
                'type'=>'text'
            ]
        ]
    ];




    protected $appends = ['liked','bookmarked'];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }

    public function tags()
    {
        return $this->morphToMany('App\Tag', 'taggable');
    }

    public function getLikedAttribute()
    {

        if (auth()->check()) {
            $userId = auth()->user()->id;
            $liked = false;
            $likes = $this->likes;
            foreach ($likes as $like) if ($like->id === $userId) $liked = true;
            return $liked;

        }
        return false;
    }

    public function getBookmarkedAttribute()
    {
        if (auth()->check()) {
            $userId = auth()->user()->id;
            $bookmarked = false;
            $bookmarks = $this->bookmarks;
            foreach ($bookmarks as $bookmark) if ($bookmark->id === $userId) $bookmarked = true;
            return $bookmarked;
        }
        return false;
    }

    public function likes()
    {
        return $this->morphToMany('App\User', 'likeable', 'likes');

    }

    public function bookmarks()
    {
        return $this->morphToMany('App\User', 'bookmarkable', 'bookmarks');

    }

    public function getImageAttribute()
    {

        return asset($this->attributes['image']);
    }

}
