<?php

namespace App\Models;

class Company extends Model implements CompanyInterface
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'name',
        'editor_id',
        'default_payment_method_id',
        'default_payment_term_days',
        'default_invoice_gross_counted',
    ];

    public function getCompanyId()
    {
        return $this->id;
    }

    /**
     * There might be multiple invitations for users for given company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'company_id');
    }

    /**
     * Company has multiple projects.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'company_id');
    }

    /**
     * There are multiple user availabilities for company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function availabilities()
    {
        return $this->hasMany(UserAvailability::class, 'company_id');
    }

    // relationships
    /**
     * Company can have multiple user roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Registries for company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function registries()
    {
        return $this->hasMany(InvoiceRegistry::class, 'company_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
