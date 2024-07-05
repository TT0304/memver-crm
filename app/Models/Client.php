<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TenantScoped;

class Client extends Model
{
    use SoftDeletes, HasFactory;
    use TenantScoped;

    public $table = 'clients';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'created_at',
        'cr',
        'package_name',
        'referral_days',
        'region_id',
        'city_id',
        'phone',
        'logo',
        'booking_days',
        'headquarter_location',
        'number_of_branches',
        'monthly_branch_price',
        'bank_name',
        'iban',
        'account_number',
        'tax_number',
        'official_name',
        'plan_id',
        'updated_at',
        'deleted_at',
        'created_by_id',

        'tamara_token_test',
        'tamara_notification_token_test',
        'tamara_token_prod',
        'tamara_notification_token_prod',
        'tamara_url_test',
        'tamara_url_prod',
        'tamara_prod',
        'domain',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function clientBrands()
    {
        return $this->hasMany(Brand::class, 'client_id', 'id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function getImageAttribute()
    {
        $file = $this->getMedia('image')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }

        return $file;
    }

    public function getBaseUrl(): string
    {
        return env('SPACES_URL') . '/' . env('SPACES_BUCKET').'/clients';
    }

    public function getMediaBaseUrlAttribute()
    {
        return config('filesystems.disks.spaces.url') . '/' . 'gym/clients/';
    }

    public function getLogoImageAttribute()
    {
        return $this->banner;
    }

    public function smsProviders()
    {
        return $this->belongsToMany(SmsProvider::class, 'client_sms_provider')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }
}
