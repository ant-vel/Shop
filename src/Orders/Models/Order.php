<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Orders\Models;

use Antvel\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Antvel\Comments\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Concerns\Management;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'address_id',
        'status',
        'type',
        'description',
        'end_date',
        'seller_id',
    ];

    protected $appends = ['translatedStatus'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get all of the comments of the order.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->orderBy('created_at', 'desc');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function inDetail()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function getDetailsAttribute()
    {
        return $this->hasMany(OrderDetail::class)->get();
    }

    public function getTranslatedStatusAttribute()
    {
        return trans('globals.order_status.' . $this->status);
    }

    public function path()
    {
        if ($notifiable->isAdmin()) {
            return route('orders.show_seller_order', $this->order);
        }

        return route('orders.show_order', $this->order);
    }

    public function scopeForSignedUser($query, $type)
    {
        if (Auth::guest()) {
            return [];
        }

        return $query->where([
            'user_id' => Auth::user()->id,
            ['description', '<>', ''],
            'type' => $type,
        ])->take(5)->get();
    }

    public function scopeOfType($query, $type)
    {
        return $query->whereType($type);
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('orders.status', $status);
    }

    public function scopeOfDates($query, $from, $to = '')
    {
        if (trim($from) == '' && trim($to) == '') {
            return;
        }

        if (trim($from) != '' && trim($to) != '') {

            return $query->whereBetween(DB::raw('DATE(orders.created_at)'), [$from, $to]);

        } elseif (trim($from) != '' && trim($to) == '') {

            return $query->where(DB::raw('DATE(orders.created_at)'), $from);

        } elseif (trim($from) == '' && trim($to) != '') {

            return $query->where(DB::raw('DATE(orders.created_at)'), $to);

        }
    }
}
