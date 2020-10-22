<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $table = 'account';

    protected $casts = ['id' => 'string'];

    const UPDATED_AT = 'modified_at';

    public $incrementing = false;

    protected $keyType = 'string';

    public function cases()
    {
        return $this->hasMany(Cases::class, 'account_id', 'id');
    }

    public function phoneNumbers()
    {
        return $this->belongsToMany(PhoneNumber::class, 'entity_phone_number', 'entity_id', 'phone_number_id');
    }

    public function assignedUser()
    {
        return $this->hasOne(User::class, 'id','assigned_user_id' );
    }

    public function emailAddresses()
    {
        return $this->belongsToMany(EmailAddress::class, 'entity_email_address', 'entity_id', 'email_address_id');
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class, 'account_id', 'id');
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class, 'account_id', 'id');
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'account_contact', 'account_id', 'contact_id')->withPivot('role', 'is_inactive');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'account_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'account_id', 'id');
    }

    public function task()
    {
        return $this->hasOne(Task::class, 'parent_id', 'id');
    }

    public function calls()
    {
        return $this->morphMany(Call::class, 'parent', 'parent_type', 'parent_id');
    }

    public function emails()
    {
        return $this->morphMany(Email::class, 'parent', 'parent_type', 'parent_id');
    }

    public function meetings()
    {
        return $this->morphMany(Meeting::class, 'parent', 'parent_type', 'parent_id');
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, 'parent', 'parent_type', 'parent_id');
    }
	
}
